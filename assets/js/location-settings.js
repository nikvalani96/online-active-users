(() => {
    'use strict';
    const toggle = document.querySelector('[data-wpoau-location-toggle]');
    if (!toggle || toggle.disabled) return;
    const saved = toggle.checked;
    const pending = document.querySelector('[data-wpoau-location-pending]');
    function sync() {
        document.querySelectorAll('[data-wpoau-geo]').forEach(node => {
            node.hidden = !toggle.checked;
            if (node.tagName === 'SELECT') node.disabled = !toggle.checked;
        });
        if (pending) pending.hidden = toggle.checked === saved;
    }
    toggle.addEventListener('change', sync);
    sync();
})();
