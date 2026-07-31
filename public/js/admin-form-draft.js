/**
 * AdminFormDraft - tự động lưu / khôi phục dữ liệu form admin (sản phẩm, danh mục,
 * thương hiệu) vào localStorage. Giúp giữ lại thông tin đang nhập khi trang lag
 * và phải load lại mà không bị mất dữ liệu.
 *
 * - autoSave(): lưu khi gõ/đổi (debounce 400ms) và khôi phục khi load lại trang.
 * - File ảnh không thể khôi phục (trình duyệt chặn) nên sẽ bị bỏ qua.
 */
(function () {
    'use strict';

    var PREFIX = 'admin_draft_';

    function getValue(el) {
        if (el.type === 'checkbox') {
            return el.checked;
        }
        return el.value;
    }

    function setValue(el, val) {
        if (val === undefined || val === null) {
            return;
        }
        if (el.type === 'checkbox') {
            el.checked = !!val;
        } else {
            el.value = val;
        }
    }

    window.AdminFormDraft = {
        save: function (form, key) {
            if (!form) return;
            var data = {};
            var els = form.querySelectorAll('input, select, textarea');
            for (var i = 0; i < els.length; i++) {
                var el = els[i];
                if (!el.name || el.type === 'file' || el.type === 'submit' || el.type === 'button') continue;
                data[el.name] = getValue(el);
            }
            try {
                localStorage.setItem(PREFIX + key, JSON.stringify(data));
            } catch (e) { /* storage đầy hoặc private mode */ }
        },

        get: function (key) {
            var raw = null;
            try {
                raw = localStorage.getItem(PREFIX + key);
            } catch (e) {
                return null;
            }
            if (!raw) return null;
            try {
                return JSON.parse(raw);
            } catch (e) {
                return null;
            }
        },

        restore: function (form, key) {
            var data = this.get(key);
            if (!data) return false;
            var els = form.querySelectorAll('input, select, textarea');
            for (var i = 0; i < els.length; i++) {
                var el = els[i];
                if (!el.name || el.type === 'file' || el.type === 'submit' || el.type === 'button') continue;
                if (Object.prototype.hasOwnProperty.call(data, el.name)) {
                    setValue(el, data[el.name]);
                }
            }
            return true;
        },

        /**
         * Tự lưu dữ liệu khi người dùng nhập, và khôi phục lại khi trang load.
         * @param {HTMLFormElement} form
         * @param {string} key - tên khóa lưu trong localStorage (không cần tiền tố admin_draft_)
         * @param {Function} [customRestore] - hàm tự khôi phục (dùng cho form có dòng động như biến thể)
         */
        autoSave: function (form, key, customRestore) {
            if (!form) return;
            if (typeof customRestore === 'function') {
                customRestore(form, key);
            } else {
                this.restore(form, key);
            }

            var that = this;
            var timer = null;
            var schedule = function () {
                clearTimeout(timer);
                timer = setTimeout(function () { that.save(form, key); }, 400);
            };
            form.addEventListener('input', schedule);
            form.addEventListener('change', schedule);
        },

        clear: function (key) {
            try {
                localStorage.removeItem(PREFIX + key);
            } catch (e) { /* ignore */ }
        },

        clearAll: function () {
            try {
                var keys = [];
                for (var i = 0; i < localStorage.length; i++) {
                    var k = localStorage.key(i);
                    if (k && k.indexOf(PREFIX) === 0) keys.push(k);
                }
                for (var j = 0; j < keys.length; j++) {
                    localStorage.removeItem(keys[j]);
                }
            } catch (e) { /* ignore */ }
        }
    };
})();
