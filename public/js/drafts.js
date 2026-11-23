/**
 * Drafts autosave - lightweight localStorage + server sync.
 * Config: window.DraftsConfig = { userId, routes: { sync, mine, discardBase, continueBase }, csrf }
 */
(function () {
  'use strict';
  if (!window.DraftsConfig || !window.DraftsConfig.userId) return;

  var CFG = window.DraftsConfig;
  var NS = 'claims_drafts:v1:u' + CFG.userId + ':';
  var INDEX_KEY = NS + '__index';
  var SYNC_DELAY = 1200;
  var MAX_BACKOFF = 30000;
  var timers = {};
  var backoff = 1000;
  var syncing = false;
  var online = navigator.onLine;

  function uuidv4() {
    if (window.crypto && crypto.randomUUID) return crypto.randomUUID();
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
      var r = Math.random() * 16 | 0, v = c === 'x' ? r : (r & 0x3 | 0x8);
      return v.toString(16);
    });
  }

  function now() { return Date.now(); }
  function lsGet(k) { try { return JSON.parse(localStorage.getItem(k)); } catch (e) { return null; } }
  function lsSet(k, v) { try { localStorage.setItem(k, JSON.stringify(v)); } catch (e) {} }
  function lsDel(k) { try { localStorage.removeItem(k); } catch (e) {} }

  function getIndex() { return lsGet(INDEX_KEY) || {}; }
  function setIndex(idx) { lsSet(INDEX_KEY, idx); }
  function draftKey(uuid) { return NS + uuid; }

  function findRecentLocalDraft(type, entityId) {
    var idx = getIndex();
    var best = null;
    Object.keys(idx).forEach(function(uuid){
      var rec = lsGet(draftKey(uuid));
      if (!rec || rec.closed || rec.type !== type) return;
      if (Date.now() - (rec.updatedAt || 0) > 7 * 24 * 3600 * 1000) return;
      if (entityId) {
        var pid = rec.payload && (rec.payload._claim_id || rec.payload._payment_id || rec.payload._entity_id);
        if (String(pid) !== String(entityId)) return;
      } else {
        // create form - only restore if no entity id in payload
        var pid2 = rec.payload && (rec.payload._claim_id || rec.payload._payment_id);
        if (pid2) return;
      }
      if (!best || (rec.updatedAt||0) > (best.updatedAt||0)) best = rec;
    });
    return best;
  }

  function readForm(form) {
    var data = {};
    var fd = new FormData(form);
    fd.forEach(function (value, key) {
      if (value instanceof File) return; // skip files
      if (key === '_token' || key === '_method') return;
      // handle array fields like name[]
      if (key.endsWith('[]')) {
        var k = key.slice(0, -2);
        if (!Array.isArray(data[k])) data[k] = [];
        data[k].push(value);
      } else if (data.hasOwnProperty(key)) {
        // convert to array on duplicate
        if (!Array.isArray(data[key])) data[key] = [data[key]];
        data[key].push(value);
      } else {
        data[key] = value;
      }
    });
    // also capture unchecked checkboxes? no, FormData skips them - acceptable
    // capture selects/textareas already covered
    return data;
  }

  function formFingerprint(data) {
    try { return JSON.stringify(data); } catch (e) { return ''; }
  }

  function getOrCreateDraft(form) {
    var uuid = form.querySelector('[name="draft_uuid"]');
    var draftUuid = uuid ? uuid.value : null;
    if (!draftUuid) {
      draftUuid = uuidv4();
      if (uuid) uuid.value = draftUuid;
    }
    return draftUuid;
  }

  function saveLocal(form) {
    var type = form.dataset.draftType;
    if (!type) return null;
    var uuid = getOrCreateDraft(form);
    var payload = readForm(form);
    payload.draft_uuid = uuid;
    // include entity id for edit forms
    if (form.dataset.draftEntityId) payload[form.dataset.draftEntityKey || '_entity_id'] = form.dataset.draftEntityId;

    var fp = formFingerprint(payload);
    var existing = lsGet(draftKey(uuid));
    if (existing && existing.fingerprint === fp) return existing;

    var revision = (existing && existing.revision ? existing.revision : 0) + 1;
    var record = {
      uuid: uuid,
      type: type,
      payload: payload,
      revision: revision,
      updatedAt: now(),
      fingerprint: fp,
      dirty: true,
      closed: false
    };
    lsSet(draftKey(uuid), record);
    var idx = getIndex();
    idx[uuid] = { type: type, updatedAt: record.updatedAt, title: payload.claim_number || payload.electronic_invoice_no || '' };
    setIndex(idx);
    return record;
  }

  function scheduleSync(form) {
    var uuid = getOrCreateDraft(form);
    clearTimeout(timers[uuid]);
    timers[uuid] = setTimeout(function () { syncDraft(uuid); }, SYNC_DELAY);
  }

  function markDirtyAndSchedule(form) {
    var rec = saveLocal(form);
    if (rec) scheduleSync(form);
    updateIndicator(form, 'saving');
  }

  function syncDraft(uuid) {
    var rec = lsGet(draftKey(uuid));
    if (!rec || !rec.dirty || rec.closed) return;
    if (!online) return;
    if (syncing) { setTimeout(function(){ syncDraft(uuid); }, 1000); return; }
    syncing = true;

    var ctrl = (typeof AbortController !== 'undefined') ? new AbortController() : null;
    var timeoutId = setTimeout(function(){ try { if (ctrl) ctrl.abort(); } catch(e){} }, 15000);
    fetch(CFG.routes.sync, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': CFG.csrf,
        'X-Requested-With': 'XMLHttpRequest'
      },
      credentials: 'same-origin',
      body: JSON.stringify({ uuid: rec.uuid, type: rec.type, payload: rec.payload, revision: rec.revision }),
      signal: ctrl ? ctrl.signal : undefined
    }).then(function (r) { return r.json().catch(function(){ return {}; }).then(function(j){ return {ok:r.ok, status:r.status, j:j}; }); })
      .then(function (res) {
        clearTimeout(timeoutId);
        syncing = false;
        if (res.ok && res.j && res.j.ok) {
          var latest = lsGet(draftKey(uuid)) || rec;
          var sentRev = rec.revision;
          if ((latest.revision || 0) <= sentRev) {
            latest.dirty = false;
            latest.serverRevision = res.j.draft ? res.j.draft.revision : sentRev;
            lsSet(draftKey(uuid), latest);
            updateIndicatorByUuid(uuid, 'saved');
          } else {
            // user edited while syncing - keep dirty and re-sync
            lsSet(draftKey(uuid), latest);
            setTimeout(function(){ syncDraft(uuid); }, 200);
          }
          backoff = 1000;
          try { window.dispatchEvent(new CustomEvent('drafts:synced', { detail: { uuid: uuid } })); } catch(e){}
        } else if (res.j && res.j.closed) {
          rec.closed = true; rec.dirty = false;
          lsSet(draftKey(uuid), rec);
          updateIndicatorByUuid(uuid, 'closed');
        } else {
          backoff = Math.min(backoff * 1.5, MAX_BACKOFF);
          try { console.warn('Draft sync failed', res.status, res.j); } catch(e){}
          try { localStorage.setItem(NS+'__last_sync_error', JSON.stringify({at:Date.now(),status:res.status,body:res.j,uuid:uuid})); } catch(e){}
          updateIndicatorByUuid(uuid, 'error');
          setTimeout(function(){ syncDraft(uuid); }, backoff);
        }
      }).catch(function (err) {
        clearTimeout(timeoutId);
        syncing = false;
        backoff = Math.min(backoff * 1.5, MAX_BACKOFF);
        try { console.warn('Draft sync network error', err); } catch(e){}
        try { localStorage.setItem(NS+'__last_sync_error', JSON.stringify({at:Date.now(),error:String(err),uuid:uuid})); } catch(e){}
        updateIndicatorByUuid(uuid, 'offline');
        setTimeout(function(){ syncDraft(uuid); }, backoff);
      });
  }

  function updateIndicator(form, state) {
    var el = form.querySelector('[data-draft-indicator]');
    if (!el) return;
    var map = {
      saving: '<i class="fa-solid fa-cloud-arrow-up"></i> جارٍ حفظ المسودة...',
      saved: '<i class="fa-solid fa-cloud-check" style="color:#16a34a"></i> تم حفظ المسودة',
      error: '<i class="fa-solid fa-triangle-exclamation" style="color:#dc2626"></i> تعذر مزامنة المسودة',
      offline: '<i class="fa-solid fa-wifi" style="color:#d97706"></i> أوفلاين - المسودة محفوظة محلياً',
      closed: '<i class="fa-solid fa-check"></i> تم إغلاق المسودة'
    };
    el.innerHTML = map[state] || '';
  }
  function updateIndicatorByUuid(uuid, state) {
    document.querySelectorAll('form[data-draft-type]').forEach(function(f){
      var u = f.querySelector('[name="draft_uuid"]');
      if (u && u.value === uuid) updateIndicator(f, state);
    });
  }

  function attachToForm(form) {
    if (form.__draftAttached) return;
    form.__draftAttached = true;
    var type = form.dataset.draftType;
    if (!type) return;

    // ensure hidden input
    var input = form.querySelector('[name="draft_uuid"]');
    if (!input) {
      input = document.createElement('input');
      input.type = 'hidden';
      input.name = 'draft_uuid';
      form.appendChild(input);
    }

    // restore from server-provided draft payload if present
    var restorePayload = window.DraftsRestorePayload;
    var restoreUuid = window.DraftsRestoreUuid;
    if (restorePayload && restoreUuid && !input.value) {
      input.value = restoreUuid;
      applyPayload(form, restorePayload);
      // save local snapshot
      var rec = saveLocal(form);
      if (rec) { rec.dirty = false; lsSet(draftKey(rec.uuid), rec); }
      updateIndicator(form, 'saved');
    } else if (restorePayload && restoreUuid && input.value === restoreUuid) {
      applyPayload(form, restorePayload);
      var rec2 = saveLocal(form);
      if (rec2) { rec2.dirty = false; lsSet(draftKey(rec2.uuid), rec2); }
      updateIndicator(form, 'saved');
    } else {
      var existingUuid = input.value;
      if (existingUuid) {
        var local = lsGet(draftKey(existingUuid));
        if (local && local.payload && !local.closed) {
          applyPayload(form, local.payload);
          updateIndicator(form, local.dirty ? 'saving' : 'saved');
        }
      } else {
        // try to find recent local draft for same type+entity
        var found = findRecentLocalDraft(type, form.dataset.draftEntityId || null);
        if (found && found.payload && !found.closed) {
          input.value = found.uuid;
          applyPayload(form, found.payload);
          updateIndicator(form, 'saved');
        }
      }
    }

    // Listen for changes
    var handler = function () { markDirtyAndSchedule(form); };
    form.addEventListener('input', handler);
    form.addEventListener('change', handler);

    // On submit: save final snapshot, but don't close (server closes on success).
    form.addEventListener('submit', function () {
      var rec = saveLocal(form);
      if (rec) { rec.dirty = true; lsSet(draftKey(rec.uuid), rec); syncDraft(rec.uuid); }
    }, true);

    // initial snapshot
    setTimeout(function(){ saveLocal(form); }, 500);
  }

  function applyPayload(form, payload) {
    if (!payload || typeof payload !== 'object') return;
    var keys = Object.keys(payload);
    var priority = ['hospital_id','department_id','entity_id','branch','location','beneficiary','law'];
    keys.sort(function(a,b){
      var ia = priority.indexOf(a), ib = priority.indexOf(b);
      if (ia === -1 && ib === -1) return 0;
      if (ia === -1) return 1;
      if (ib === -1) return -1;
      return ia - ib;
    });
    keys.forEach(function (key) { applyField(form, key, payload[key]); });
    setTimeout(function(){ keys.forEach(function(k){ applyField(form,k,payload[k]); }); }, 400);
    setTimeout(function(){ keys.forEach(function(k){ applyField(form,k,payload[k]); }); }, 1000);
  }

  function applyField(form, key, val) {
    if (key === '_token' || key === '_method' || key === 'draft_uuid') return;
    if (key.endsWith('[]')) return;
    var els = form.querySelectorAll('[name="' + CSS.escape(key) + '"], [name="' + CSS.escape(key) + '[]"]');
    if (!els.length) return;
    els.forEach(function (el) {
      if (el.type === 'file') return;
      if (el.type === 'checkbox' || el.type === 'radio') {
        if (Array.isArray(val)) el.checked = val.indexOf(el.value) !== -1;
        else el.checked = String(val) === String(el.value) || val === true;
      } else if (el.tagName === 'SELECT') {
        if (val == null) return;
        var strVal = String(val);
        var exists = false;
        for (var i=0;i<el.options.length;i++){ if(String(el.options[i].value)===strVal){exists=true;break;} }
        if (!exists && strVal !== '') {
          var opt = document.createElement('option');
          opt.value = strVal; opt.textContent = strVal; opt.selected = true;
          el.appendChild(opt);
        }
        el.value = strVal;
        try { if (window.jQuery && jQuery(el).data('select2')) jQuery(el).trigger('change.select2'); jQuery(el).trigger('change'); }
        catch(e) { el.dispatchEvent(new Event('change', {bubbles:true})); }
      } else {
        if (el.value === (val == null ? '' : String(val))) return;
        el.value = val == null ? '' : val;
        try { el.dispatchEvent(new Event('input', {bubbles:true})); } catch(e){}
        try { el.dispatchEvent(new Event('change', {bubbles:true})); } catch(e){}
      }
    });
  }

  function activeDraftUuids() {
    var uuids = [];
    document.querySelectorAll('form[data-draft-type] [name="draft_uuid"]').forEach(function(inp){
      if (inp.value && uuids.indexOf(inp.value) === -1) uuids.push(inp.value);
    });
    return uuids;
  }

  function closeLocalDraft(uuid) {
    var rec = lsGet(draftKey(uuid));
    if (!rec) return;
    rec.closed = true;
    rec.dirty = false;
    lsSet(draftKey(uuid), rec);
    var idx = getIndex();
    if (idx[uuid]) { delete idx[uuid]; setIndex(idx); }
    updateIndicatorByUuid(uuid, 'closed');
  }

  function patchFetch() {
    if (!window.fetch || window.fetch.__draftsPatched) return;
    var nativeFetch = window.fetch.bind(window);
    var patched = function(input, init) {
      init = init || {};
      var url = typeof input === 'string' ? input : (input && input.url) || '';
      var method = String((init.method || (input && input.method) || 'GET')).toUpperCase();
      var mutating = ['POST','PUT','PATCH','DELETE'].indexOf(method) !== -1;
      var uuids = mutating ? activeDraftUuids() : [];
      var draftUuid = uuids.length === 1 ? uuids[0] : null;
      if (draftUuid && !/\/drafts(?:\/|$)/.test(url)) {
        if (init.headers instanceof Headers) {
          if (!init.headers.has('X-Draft-UUID')) init.headers.set('X-Draft-UUID', draftUuid);
        } else {
          init.headers = Object.assign({}, init.headers || {});
          if (!init.headers['X-Draft-UUID'] && !init.headers['x-draft-uuid']) init.headers['X-Draft-UUID'] = draftUuid;
        }
      }
      return nativeFetch(input, init).then(function(res) {
        if (mutating && draftUuid && res && res.ok && !/\/drafts(?:\/|$)/.test(url)) {
          closeLocalDraft(draftUuid);
        }
        return res;
      });
    };
    patched.__draftsPatched = true;
    window.fetch = patched;
  }

  function boot() {
    document.querySelectorAll('form[data-draft-type]').forEach(attachToForm);
    patchFetch();

    window.addEventListener('online', function(){ online = true; flushDirty(); });
    window.addEventListener('offline', function(){ online = false; updateAllIndicators('offline'); });

    // flush dirty drafts on boot
    flushDirty();
  }

  function flushDirty() {
    var idx = getIndex();
    Object.keys(idx).forEach(function(uuid){
      var rec = lsGet(draftKey(uuid));
      if (rec && rec.dirty && !rec.closed) syncDraft(uuid);
    });
  }

  function updateAllIndicators(state) {
    document.querySelectorAll('form[data-draft-type]').forEach(function(f){ updateIndicator(f, state); });
  }

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', boot);
  else boot();

  // expose for debugging
  window.ClaimsDrafts = { saveLocal: saveLocal, syncDraft: syncDraft, getIndex: getIndex };
})();
