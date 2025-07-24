<div class="sidebar d-print-none">
    <ul class="nav flex-column mt-3">
        <!-- DASHBOARD -->
        <li class="nav-item">
            <a class="nav-link text-white" href="<?= base_url(session()->get('role') . '/dashboard') ?>">
               🖥️ Dashboard
            </a>
        </li>

        <!-- MENU ADMIN / USER -->
        <?php if (session()->get('role') === 'admin'): ?>
            <li class="nav-item">
                <a class="nav-link text-white" href="<?= base_url('/admin/produk') ?>">🛍️ Lihat Produk</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="<?= base_url('/admin/statistik') ?>">📈 Statistik Penjualan</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="<?= base_url('/admin/order') ?>">📋 Detail Pesanan</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="<?= base_url('/admin/kelola-produk') ?>">🛠️ Kelola Produk</a>
            </li>
        <?php else: ?>
            <li class="nav-item">
                <a class="nav-link text-white" href="<?= base_url('/user/produk') ?>">🛍️ Produk</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="<?= base_url('/user/keranjang') ?>">🛒 Keranjang</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="<?= base_url('/user/checkout') ?>">💵 Checkout</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="<?= base_url('/user/pesanan') ?>">📝 Pesanan</a>
            </li>
        <?php endif; ?>

        <!-- SUPPORT (Dropdown) -->
        <li class="nav-item mt-2">
            <a class="nav-link text-white d-flex justify-content-between align-items-center"
               data-bs-toggle="collapse" href="#supportMenu" role="button"
               aria-expanded="false" aria-controls="supportMenu">
                <span>👨🏻‍💻 FAQ</span>
                <i class="fas fa-chevron-down"></i>
            </a>

            <div class="collapse" id="supportMenu">
                <ul class="nav flex-column ms-3 mt-2">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="https://wa.me/+6285292301037">✆ WhatsApp</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="http://t.me/samudra1804">➣ Telegram</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="https://www.instagram.com/habibiramadhani02?igsh=MXV6b2RlanF5dGFtYg==">🅾 Instagram</a>
                    </li>
                </ul>
            </div>
        </li>

        <!-- LOGOUT -->
        <li class="nav-item mt-3">
            <a class="nav-link text-white" href="<?= base_url('/logout') ?>">🔓 Logout</a>
        </li>
    </ul>
</div>
