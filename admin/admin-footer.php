<?php
/**
 * Admin Panel Footer Template
 */
$flash = get_flash_message();
?>
        </main>

        <!-- Admin Footer Info -->
        <footer class="mt-auto border-t border-slate-800/80 px-8 py-4 text-xs text-slate-500 flex flex-col sm:flex-row items-center justify-between gap-2">
            <p>&copy; <?= date('Y') ?> Portfolio Management System &bull; Versi 1.0.0</p>
            <p>PHP <?= PHP_VERSION ?> &bull; MySQL Database</p>
        </footer>
    </div>

    <!-- Admin Mobile Menu Toggle Script -->
    <script>
        const adminMobileBtn = document.getElementById('adminMobileToggle');
        const adminSidebar = document.getElementById('adminSidebar');

        if (adminMobileBtn && adminSidebar) {
            adminMobileBtn.addEventListener('click', () => {
                adminSidebar.classList.toggle('hidden');
            });
        }
    </script>

    <!-- SweetAlert2 Flash Notification if exists -->
    <?php if ($flash): ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            Swal.fire({
                icon: '<?= $flash['type'] === 'success' ? 'success' : ($flash['type'] === 'warning' ? 'warning' : 'error') ?>',
                title: '<?= $flash['type'] === 'success' ? 'Berhasil!' : 'Perhatian!' ?>',
                text: '<?= addslashes($flash['text']) ?>',
                background: '#1e293b',
                color: '#f8fafc',
                confirmButtonColor: '#3b82f6',
                timer: 4000,
                timerProgressBar: true
            });
        });
    </script>
    <?php endif; ?>
</body>
</html>
