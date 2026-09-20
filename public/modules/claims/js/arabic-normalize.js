/**
 * Arabic letters-insensitive search helpers.
 * Loaded on every page via claims::layouts.app
 *
 * - Exposes window.normalizeArabic()
 * - Makes all DataTables searches insensitive to Arabic letter variants (أ/ا, ى/ي, ة/ه, ...) and tashkeel
 * - Applies an Arabic-insensitive matcher to all Select2 instances as soon as Select2 loads
 */
(function (window, $) {
    'use strict';

    /**
     * Normalize Arabic text so searches don't depend on exact letter forms:
     * strips tashkeel/diacritics, unifies alef forms, ى->ي, ة->ه, ؤ->و, ئ->ي, drops ء.
     */
    function normalizeArabic(text) {
        if (typeof text !== 'string') return text;
        var t = text
            .replace(/[\u064B-\u0652\u0670\u0640]/g, '')   // tashkeel + tatweel
            .replace(/[أإآٱ]/g, 'ا')
            .replace(/ى/g, 'ي')
            .replace(/ة/g, 'ه')
            .replace(/ؤ/g, 'و')
            .replace(/ئ/g, 'ي')
            .replace(/ء/g, '')
            .replace(/[\u0660-\u0669]/g, function (c) { return String.fromCharCode(c.charCodeAt(0) - 0x0660 + 48); })
            .replace(/[\u06F0-\u06F9]/g, function (c) { return String.fromCharCode(c.charCodeAt(0) - 0x06F0 + 48); });
        try { t = t.normalize('NFC'); } catch (e) {}
        return t.toLowerCase();
    }
    window.normalizeArabic = normalizeArabic;

    // Make every DataTables search Arabic-insensitive (string + html columns)
    if ($ && $.fn.dataTable && $.fn.dataTable.ext && $.fn.dataTable.ext.type && $.fn.dataTable.ext.type.search) {
        var stripHtml = function (d) {
            return typeof d === 'string' ? $('<div>').html(d).text() : d;
        };
        $.fn.dataTable.ext.type.search.string = function (d) { return normalizeArabic(d); };
        $.fn.dataTable.ext.type.search.html = function (d) { return normalizeArabic(stripHtml(d)); };
    }

    // Arabic-insensitive Select2 matcher, applied as soon as Select2 is available
    function setupSelect2Matcher() {
        if (window.jQuery && jQuery.fn && jQuery.fn.select2 && !jQuery.fn.select2.__arabicPatched) {
            var matcher = function (params, data) {
                if ($.trim(params.term) === '') return data;
                var term = normalizeArabic(params.term);
                var text = typeof (data && data.text) === 'string' ? normalizeArabic(data.text) : '';
                if (text.indexOf(term) > -1) return data;
                return null;
            };
            try {
                if ($.fn.select2.defaults && typeof $.fn.select2.defaults.set === 'function') {
                    $.fn.select2.defaults.set('matcher', matcher);
                } else if ($.fn.select2.defaults) {
                    $.fn.select2.defaults.matcher = matcher;
                }
            } catch (e) {}
            jQuery.fn.select2.__arabicPatched = true;
            return true;
        }
        return false;
    }
    if (!setupSelect2Matcher()) {
        var timer = setInterval(function () {
            if (setupSelect2Matcher()) clearInterval(timer);
        }, 200);
        setTimeout(function () { clearInterval(timer); }, 15000);
    }
})(window, window.jQuery);