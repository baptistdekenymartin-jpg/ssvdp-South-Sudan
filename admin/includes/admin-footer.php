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
});
</script>
</body>
</html>
