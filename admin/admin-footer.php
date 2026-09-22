<?php
/**
 * Admin Panel Footer Template
 */
$flash = get_flash_message();
?>
        </main>

        <!-- Admin Footer Info -->
        <footer class="mt-auto border-t border-white/10 px-6 sm:px-8 py-4 text-[11px] text-zinc-500 font-mono flex flex-col sm:flex-row items-center justify-between gap-2">
            <p>&copy; <?= date('Y') ?> Portfolio Management &bull; Crafted with PHP &amp; MySQL</p>
            <p>PHP <?= PHP_VERSION ?> &bull; System Active</p>
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
                background: '#12141c',
                color: '#f4f4f6',
                confirmButtonColor: '#6366f1',
                customClass: {
                    popup: 'border border-white/10 rounded-2xl shadow-2xl'
                },
                timer: 4000,
                timerProgressBar: true
            });
        });
    </script>
    <?php endif; ?>
</body>
</html>
