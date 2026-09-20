<?php
/**
 * Footer Template - Modern Minimalist & Human Crafted
 */
$flash = get_flash_message();
$site_name = get_setting($pdo, 'full_name', 'Rizky Pratama');
$github_url = get_setting($pdo, 'github_url', 'https://github.com');
$linkedin_url = get_setting($pdo, 'linkedin_url', 'https://linkedin.com');
$instagram_url = get_setting($pdo, 'instagram_url', 'https://instagram.com');
$email = get_setting($pdo, 'email', 'rizky.pratama@example.com');
?>
    <!-- Footer -->
    <footer class="bg-[#090a0f] border-t border-white/10 pt-16 pb-12 mt-auto relative z-10">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10 pb-12 border-b border-white/10">
                <!-- Col 1: Brand & Summary -->
                <div class="md:col-span-2 space-y-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-zinc-800 border border-white/15 flex items-center justify-center text-white font-mono font-bold text-sm">
                            <?= strtoupper(substr($site_name, 0, 1)) ?>
                        </div>
                        <span class="font-bold text-base text-white tracking-tight">
                            <?= sanitize($site_name) ?>
                        </span>
                    </div>
                    <p class="text-zinc-400 text-xs leading-relaxed max-w-sm">
                        Full-Stack Web Developer yang mengutamakan kode bersih, keamanan data, dan skema database relasional yang terstruktur rapi.
                    </p>
                    <div class="flex items-center gap-2 pt-1">
                        <a href="<?= sanitize($github_url) ?>" target="_blank" rel="noopener" class="w-8 h-8 rounded-lg bg-zinc-900 border border-white/10 flex items-center justify-center text-zinc-400 hover:text-white hover:border-white/20 transition" aria-label="GitHub">
                            <i class="fa-brands fa-github text-xs"></i>
                        </a>
                        <a href="<?= sanitize($linkedin_url) ?>" target="_blank" rel="noopener" class="w-8 h-8 rounded-lg bg-zinc-900 border border-white/10 flex items-center justify-center text-zinc-400 hover:text-white hover:border-white/20 transition" aria-label="LinkedIn">
                            <i class="fa-brands fa-linkedin-in text-xs"></i>
                        </a>
                        <a href="<?= sanitize($instagram_url) ?>" target="_blank" rel="noopener" class="w-8 h-8 rounded-lg bg-zinc-900 border border-white/10 flex items-center justify-center text-zinc-400 hover:text-white hover:border-white/20 transition" aria-label="Instagram">
                            <i class="fa-brands fa-instagram text-xs"></i>
                        </a>
                        <a href="mailto:<?= sanitize($email) ?>" class="w-8 h-8 rounded-lg bg-zinc-900 border border-white/10 flex items-center justify-center text-zinc-400 hover:text-white hover:border-white/20 transition" aria-label="Email">
                            <i class="fa-solid fa-envelope text-xs"></i>
                        </a>
                    </div>
                </div>

                <!-- Col 2: Navigasi -->
                <div>
                    <h3 class="text-white text-xs font-mono font-semibold uppercase tracking-wider mb-3">Navigasi</h3>
                    <ul class="space-y-2 text-xs text-zinc-400">
                        <li><a href="#home" class="hover:text-white transition">Beranda</a></li>
                        <li><a href="#about" class="hover:text-white transition">Tentang</a></li>
                        <li><a href="#skills" class="hover:text-white transition">Tech Stack</a></li>
                        <li><a href="#projects" class="hover:text-white transition">Portofolio</a></li>
                        <li><a href="#experience" class="hover:text-white transition">Rekam Jejak</a></li>
                    </ul>
                </div>

                <!-- Col 3: Akses -->
                <div>
                    <h3 class="text-white text-xs font-mono font-semibold uppercase tracking-wider mb-3">Tautan Singkat</h3>
                    <ul class="space-y-2 text-xs text-zinc-400">
                        <li><a href="#contact" class="hover:text-white transition">Kirim Pesan</a></li>
                        <li><a href="admin/login.php" class="hover:text-white transition">Panel Administrator</a></li>
                        <li><a href="setup.php" class="hover:text-white transition">Status Database</a></li>
                        <li class="pt-2">
                            <span class="inline-flex items-center gap-1.5 text-zinc-400 text-[11px] font-mono bg-zinc-900 px-2.5 py-1 rounded border border-white/10">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> PHP <?= PHP_VERSION ?>
                            </span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="pt-8 flex flex-col sm:flex-row items-center justify-between text-xs text-zinc-500 gap-4">
                <p>&copy; <?= date('Y') ?> <?= sanitize($site_name) ?>. All rights reserved.</p>
                <p class="font-mono text-[11px] text-zinc-500">
                    Handcrafted with <span class="text-zinc-300">PHP &amp; MySQL</span>
                </p>
            </div>
        </div>
    </footer>

    <!-- Back To Top Button -->
    <button id="backToTopBtn" type="button" aria-label="Kembali ke atas" class="fixed bottom-6 right-6 z-40 w-10 h-10 rounded-lg bg-zinc-900 hover:bg-zinc-800 text-zinc-300 hover:text-white border border-white/15 flex items-center justify-center transition-all duration-200 opacity-0 pointer-events-none translate-y-3 cursor-pointer shadow-lg">
        <i class="fa-solid fa-arrow-up text-xs"></i>
    </button>

    <!-- Main JavaScript File -->
    <script src="assets/js/main.js"></script>

    <!-- SweetAlert2 Flash Notification if exists -->
    <?php if ($flash): ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            Swal.fire({
                icon: '<?= $flash['type'] === 'success' ? 'success' : ($flash['type'] === 'warning' ? 'warning' : 'error') ?>',
                title: '<?= $flash['type'] === 'success' ? 'Berhasil' : 'Perhatian' ?>',
                text: '<?= addslashes($flash['text']) ?>',
                background: '#11131a',
                color: '#f4f4f6',
                confirmButtonColor: '#4f46e5',
                timer: 4000,
                timerProgressBar: true
            });
        });
    </script>
    <?php endif; ?>
</body>
</html>
