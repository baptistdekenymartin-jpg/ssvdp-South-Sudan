        </main>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.querySelector('[data-admin-menu]');
    const sidebar = document.querySelector('[data-admin-sidebar]');
    if (toggle && sidebar) {
        toggle.addEventListener('click', function () {
            sidebar.classList.toggle('is-open');
        });
    }

    document.querySelectorAll('[data-other-select]').forEach(function (select) {
        var field = document.querySelector('[data-other-field="' + select.getAttribute('data-other-select') + '"]');
        var input = field ? field.querySelector('input') : null;
        function updateOtherField() {
            var isOther = select.value === 'Others' || select.value === 'Other';
            if (field) { field.hidden = !isOther; }
            if (input) { input.required = isOther; }
        }
        select.addEventListener('change', updateOtherField);
        updateOtherField();
    });

    document.querySelectorAll('[data-programme-title-source]').forEach(function (select) {
        var target = document.querySelector('[data-programme-title-target="' + select.getAttribute('data-programme-title-source') + '"]');
        if (!target) { return; }
        var suggestions = {};
        try { suggestions = JSON.parse(select.getAttribute('data-programme-title-map') || '{}'); } catch (error) {}
        select.addEventListener('change', function () {
            var suggested = suggestions[select.value] || '';
            if (suggested && (!target.value || target.hasAttribute('data-auto-title'))) {
                target.value = suggested;
                target.setAttribute('data-auto-title', '1');
                target.dispatchEvent(new Event('change', { bubbles: true }));
            }
        });
        target.addEventListener('change', function () { target.removeAttribute('data-auto-title'); });
    });
});
</script>
</body>
</html>
