<div class="sidebar d-print-none">
    <ul class="nav flex-column mt-3">
        <!-- DASHBOARD -->
        <li class="nav-item">
            <a class="nav-link text-white fw-semibold" href="<?= base_url(session()->get('role') . '/dashboard') ?>">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
        </li>

        <!-- MENU ADMIN / USER -->
        <?php if (session()->get('role') === 'admin'): ?>
            <li class="nav-item">
                <a class="nav-link text-white" href="<?= base_url('/admin/produk') ?>">
                    <i class="bi bi-box-seam me-2"></i> Lihat Produk
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="<?= base_url('/admin/statistik') ?>">
                    <i class="bi bi-bar-chart-line me-2"></i> Statistik Penjualan
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="<?= base_url('/admin/order') ?>">
                    <i class="bi bi-clipboard-data me-2"></i> Kelola Pesanan
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="<?= base_url('/admin/kelola-produk') ?>">
                    <i class="bi bi-tools me-2"></i> Kelola Produk
                </a>
            </li>
            <li class="nav-item">
               <a class="nav-link text-white" href="<?= base_url('admin/users') ?>">
                    <i class="bi bi-people-fill me-2"></i> Kelola Pengguna
                </a>

            </li>

        <?php else: ?>
            <li class="nav-item">
                <a class="nav-link text-white" href="<?= base_url('/user/produk') ?>">
                    <i class="bi bi-box me-2"></i> Produk
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="<?= base_url('/user/keranjang') ?>">
                    <i class="bi bi-cart3 me-2"></i> Keranjang
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="<?= base_url('/user/pesanan') ?>">
                    <i class="bi bi-journal-check me-2"></i> Pesanan
                </a>
            </li>
        <?php endif; ?>

        <!-- SUPPORT -->
        <li class="nav-item mt-3">
            <a class="nav-link text-white d-flex justify-content-between align-items-center" 
               data-bs-toggle="collapse" href="#supportMenu" role="button"
               aria-expanded="false" aria-controls="supportMenu">
                <span><i class="bi bi-question-circle me-2"></i> FAQ & Support</span>
                <i class="bi bi-chevron-down small"></i>
            </a>

            <div class="collapse" id="supportMenu">
                <ul class="nav flex-column ms-3 mt-2">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="https://wa.me/+6285292301037" target="_blank">
                            <i class="bi bi-whatsapp me-2"></i> WhatsApp
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="http://t.me/samudra1804" target="_blank">
                            <i class="bi bi-telegram me-2"></i> Telegram
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="https://www.instagram.com/habibiramadhani02?igsh=MXV6b2RlanF5dGFtYg==" target="_blank">
                            <i class="bi bi-instagram me-2"></i> Instagram
                        </a>
                    </li>
                </ul>
            </div>
        </li>

        <!-- LOGOUT -->
        <li class="nav-item mt-3">
            <a class="nav-link text-white" href="<?= base_url('/logout') ?>">
                <i class="bi bi-box-arrow-right me-2"></i> Logout
            </a>
        </li>
    </ul>
</div>
