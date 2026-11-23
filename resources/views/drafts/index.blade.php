@extends('claims::layouts.app')

@section('title', $isAdmin ? 'مسودات المستخدمين' : 'مسوداتي')

@section('content')
<style>
    .drafts-card { background:#fff; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,.06); padding:20px; margin-bottom:16px; }
    .drafts-table { width:100%; border-collapse:collapse; }
    .drafts-table th,.drafts-table td { padding:10px 12px; border-bottom:1px solid #eee; text-align:right; vertical-align:middle; }
    .drafts-table th { background:#f8fafc; font-weight:700; }
    .badge-type { display:inline-block; padding:3px 10px; border-radius:999px; background:#eef2ff; color:#4338ca; font-size:12px; font-weight:600; }
    .draft-actions { display:flex; gap:6px; flex-wrap:wrap; }
    .btn-sm { padding:5px 10px; font-size:12px; border-radius:6px; border:none; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:4px; }
    .btn-continue { background:#2563eb; color:#fff; }
    .btn-discard { background:#fee2e2; color:#dc2626; }
    .btn-finalize { background:#dcfce7; color:#16a34a; }
    .btn-danger { background:#dc2626; color:#fff; }
    .drafts-empty { padding:30px; text-align:center; color:#64748b; }
    .user-card { background:#fff; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,.06); padding:16px; margin-bottom:16px; }
    .user-card-header { display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap:wrap; margin-bottom:12px; }
    .user-card-title { margin:0; font-size:16px; font-weight:700; }
    .user-meta { color:#64748b; font-size:12px; }
    .pager { display:flex; gap:6px; align-items:center; justify-content:center; margin-top:10px; flex-wrap:wrap; }
    .pager button { padding:4px 10px; border:1px solid #cbd5e1; background:#fff; border-radius:6px; cursor:pointer; font-size:12px; }
    .pager button.active { background:#2563eb; color:#fff; border-color:#2563eb; }
    .pager button:disabled { opacity:.5; cursor:not-allowed; }
    .loading-row { text-align:center; color:#64748b; padding:16px; }
</style>

@if($isAdmin)
    <div class="drafts-card">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;gap:10px;flex-wrap:wrap;">
            <h2 style="margin:0;font-size:20px;"><i class="fa-solid fa-file-pen"></i> مسودات المستخدمين</h2>
            <div style="color:#64748b;font-size:13px;">عرض مسودات جميع المستخدمين. يمكنك حذف أي مسودة.</div>
        </div>
    </div>

    @if($draftUsers->count() === 0)
        <div class="drafts-card"><div class="drafts-empty"><i class="fa-solid fa-folder-open" style="font-size:40px;color:#cbd5e1;display:block;margin-bottom:10px;"></i>لا توجد مسودات معلقة للمستخدمين حالياً.</div></div>
    @else
        @foreach($draftUsers as $u)
            <div class="user-card" id="user-card-{{ $u->id }}" data-user-id="{{ $u->id }}">
                <div class="user-card-header">
                    <div>
                        <h3 class="user-card-title"><i class="fa-solid fa-user"></i> {{ $u->name }}</h3>
                        <div class="user-meta">عدد المسودات المعلقة: <span class="user-draft-count">{{ $u->drafts_count }}</span></div>
                    </div>
                    <div>
                        <button type="button" class="btn-sm btn-danger js-discard-user" data-user-id="{{ $u->id }}" data-url="{{ route('drafts.admin.discardUser', $u) }}">
                            <i class="fa-solid fa-trash"></i> حذف كل مسودات المستخدم
                        </button>
                    </div>
                </div>
                <div style="overflow-x:auto;">
                    <table class="drafts-table">
                        <thead><tr><th>#</th><th>النوع</th><th>العنوان / الملخص</th><th>آخر تعديل</th><th>إجراءات</th></tr></thead>
                        <tbody id="drafts-body-{{ $u->id }}"><tr><td colspan="5" class="loading-row">جارٍ التحميل...</td></tr></tbody>
                    </table>
                </div>
                <div class="pager" id="pager-{{ $u->id }}"></div>
            </div>
        @endforeach
    @endif
@else
    <div class="drafts-card">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;gap:10px;flex-wrap:wrap;">
            <h2 style="margin:0;font-size:20px;"><i class="fa-solid fa-file-pen"></i> مسوداتي</h2>
            <div style="color:#64748b;font-size:13px;">المسودات يتم حفظها تلقائياً أثناء الكتابة، ويمكنك استكمالها لاحقاً.</div>
        </div>
        @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
        @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif
        <div style="overflow-x:auto;">
            <table class="drafts-table">
                <thead><tr><th>#</th><th>النوع</th><th>العنوان / الملخص</th><th>آخر تعديل</th><th>إجراءات</th></tr></thead>
                <tbody id="my-drafts-body"><tr><td colspan="5" class="loading-row">جارٍ التحميل...</td></tr></tbody>
            </table>
        </div>
        <div class="pager" id="my-pager"></div>
        <div id="drafts-local-diag"></div>
    </div>
@endif

<script>
(function() {
    var csrf = @json(csrf_token());
    var isAdmin = @json($isAdmin);

    function escapeHtml(s) {
        return String(s == null ? '' : s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
    }

    function renderRowsHtml(drafts, emptyColspan) {
        if (!drafts.length) return '<tr><td colspan="' + emptyColspan + '" class="loading-row">لا توجد مسودات حالياً.</td></tr>';
        return drafts.map(function(d) {
            var finalizeBtn = d.can_finalize
                ? '<form method="POST" action="' + escapeHtml(d.finalize_url) + '" style="display:inline">' +
                  '<input type="hidden" name="_token" value="' + csrf + '">' +
                  '<button type="submit" class="btn-sm btn-finalize" onclick="return confirm(\'سيتم حفظ المسودة كسجل فعلي. هل تريد المتابعة؟\')"><i class="fa-solid fa-check"></i> حفظ نهائي</button></form>'
                : '';
            return '<tr>' +
                '<td>' + d.id + '</td>' +
                '<td><span class="badge-type">' + escapeHtml(d.type_label) + '</span></td>' +
                '<td>' + escapeHtml(d.title) + '</td>' +
                '<td style="white-space:nowrap;font-size:12px;color:#475569;">' + escapeHtml(d.updated_at || '') + '</td>' +
                '<td><div class="draft-actions">' +
                    '<a class="btn-sm btn-continue" href="' + escapeHtml(d.continue_url) + '"><i class="fa-solid fa-pen"></i> استكمال</a>' +
                    finalizeBtn +
                    '<form method="POST" action="' + escapeHtml(d.discard_url) + '" style="display:inline">' +
                        '<input type="hidden" name="_token" value="' + csrf + '">' +
                        '<button type="submit" class="btn-sm btn-discard" onclick="return confirm(\'هل أنت متأكد من حذف هذه المسودة؟\')"><i class="fa-solid fa-trash"></i> حذف</button>' +
                    '</form>' +
                '</div></td></tr>';
        }).join('');
    }

    function renderPager(el, p, onPage) {
        if (!p || p.last_page <= 1) { el.innerHTML = ''; return; }
        var html = '';
        html += '<button ' + (p.current_page <= 1 ? 'disabled' : '') + ' data-page="' + (p.current_page - 1) + '">السابق</button>';
        for (var i = 1; i <= p.last_page; i++) {
            html += '<button class="' + (i === p.current_page ? 'active' : '') + '" data-page="' + i + '">' + i + '</button>';
        }
        html += '<button ' + (p.current_page >= p.last_page ? 'disabled' : '') + ' data-page="' + (p.current_page + 1) + '">التالي</button>';
        el.innerHTML = html;
        el.querySelectorAll('button[data-page]').forEach(function(btn){
            btn.addEventListener('click', function(){ if (!btn.disabled) onPage(parseInt(btn.dataset.page,10)); });
        });
    }

    function loadUserDrafts(userId, page) {
        page = page || 1;
        var body = document.getElementById('drafts-body-' + userId);
        var pager = document.getElementById('pager-' + userId);
        body.innerHTML = '<tr><td colspan="5" class="loading-row">جارٍ التحميل...</td></tr>';
        fetch('{{ route("drafts.admin.userDrafts", ["user" => "__UID__"], false) }}'.replace('__UID__', userId) + '?page=' + page + '&per_page=5', {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        }).then(r => r.json()).then(data => {
            if (!data.ok) { body.innerHTML = '<tr><td colspan="5" class="loading-row">تعذر تحميل المسودات</td></tr>'; return; }
            body.innerHTML = renderRowsHtml(data.drafts || [], 5);
            renderPager(pager, data.pagination || {}, function(p){ loadUserDrafts(userId,p); });
            var countEl = document.querySelector('#user-card-' + userId + ' .user-draft-count');
            if (countEl && data.pagination) countEl.textContent = data.pagination.total;
        }).catch(() => { body.innerHTML = '<tr><td colspan="5" class="loading-row">تعذر تحميل المسودات</td></tr>'; });
    }

    function showLocalDiag() {
        var el = document.getElementById('drafts-local-diag');
        if (!el || !window.DraftsConfig) return;
        try {
            var ns = 'claims_drafts:v1:u' + window.DraftsConfig.userId + ':';
            var idx = JSON.parse(localStorage.getItem(ns + '__index') || '{}');
            var dirty = [];
            Object.keys(idx).forEach(function(uuid){
                var rec = JSON.parse(localStorage.getItem(ns + uuid) || 'null');
                if (rec && rec.dirty && !rec.closed) dirty.push({uuid:uuid,type:rec.type,rev:rec.revision,at:rec.updatedAt});
            });
            var lastErr = null;
            try { lastErr = JSON.parse(localStorage.getItem(ns + '__last_sync_error') || 'null'); } catch(e){}
            if (dirty.length > 0) {
                var html = '<div style="margin-top:12px;padding:10px 12px;border-radius:8px;background:#fff7ed;color:#9a3412;font-size:12px;">' +
                    '<strong><i class="fa-solid fa-triangle-exclamation"></i> يوجد ' + dirty.length + ' مسودة محلية لم تُزامَن مع السيرفر بعد.</strong>' +
                    '<div style="margin-top:4px;color:#7c2d12;">سيتم مزامنتها تلقائيًا عند توفر الاتصال. إذا استمرت المشكلة افتح Console وابحث عن Draft sync.</div>';
                if (lastErr) html += '<div style="margin-top:4px;direction:ltr;text-align:left;color:#991b1b;">Last sync error: ' + escapeHtml(JSON.stringify(lastErr).slice(0,300)) + '</div>';
                html += '</div>';
                el.innerHTML = html;
            } else { el.innerHTML = ''; }
        } catch(e) {}
    }

    function loadMyDrafts(page) {
        page = page || 1;
        var body = document.getElementById('my-drafts-body');
        var pager = document.getElementById('my-pager');
        if (!body) return;
        body.innerHTML = '<tr><td colspan="5" class="loading-row">جارٍ التحميل...</td></tr>';
        fetch('{{ route("drafts.myData", [], false) }}?page=' + page + '&per_page=10', {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        }).then(r => r.json()).then(data => {
            if (!data.ok) { body.innerHTML = '<tr><td colspan="5" class="loading-row">تعذر تحميل المسودات</td></tr>'; showLocalDiag(); return; }
            body.innerHTML = renderRowsHtml(data.drafts || [], 5);
            renderPager(pager, data.pagination || {}, loadMyDrafts);
            showLocalDiag();
        }).catch(() => { body.innerHTML = '<tr><td colspan="5" class="loading-row">تعذر تحميل المسودات</td></tr>'; showLocalDiag(); });
    }
    var reloadTimer = null;
    window.addEventListener('drafts:synced', function() {
        clearTimeout(reloadTimer);
        reloadTimer = setTimeout(function() {
            if (isAdmin) {
                document.querySelectorAll('[id^="user-card-"]').forEach(function(card){ loadUserDrafts(card.dataset.userId, 1); });
            } else {
                loadMyDrafts(1);
            }
        }, 400);
    });

    if (isAdmin) {
        document.querySelectorAll('[id^="user-card-"]').forEach(function(card){ loadUserDrafts(card.dataset.userId, 1); });
        document.querySelectorAll('.js-discard-user').forEach(function(btn) {
            btn.addEventListener('click', function() {
                if (!confirm('سيتم حذف جميع مسودات هذا المستخدم. هل أنت متأكد؟')) return;
                var userId = btn.dataset.userId;
                fetch(btn.dataset.url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' },
                    body: JSON.stringify({})
                }).then(r => r.json()).then(data => {
                    if (data.ok) loadUserDrafts(userId, 1);
                    else alert('تعذر حذف المسودات');
                }).catch(() => alert('تعذر حذف المسودات'));
            });
        });
    } else {
        loadMyDrafts(1);
    }
})();
</script>
@endsection