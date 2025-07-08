<div class="sidebar">
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
                <a class="nav-link text-white" href="<?= base_url('/admin/kelola-produk') ?>">🛠️ Kelola Produk</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="<?= base_url('/admin/keranjang') ?>">🛒 Keranjang</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="<?= base_url('/admin/checkout') ?>">💵 Checkout</a>
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
        <?php endif; ?>

        <!-- SUPPORT (Dropdown) ––> tambahkan mulai di sini -->
        <li class="nav-item mt-2">
            <a class="nav-link text-white d-flex justify-content-between align-items-center"
               data-bs-toggle="collapse" href="#supportMenu" role="button"
               aria-expanded="false" aria-controls="supportMenu">
                <span><i class="fas fa-headset me"></i>👨🏻‍💻 FAQ</span>
                <i class="fas fa-chevron-down"></i>
            </a>

            <div class="collapse" id="supportMenu">
                <ul class="nav flex-column ms-3 mt-2">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="https://wa.me/+6285292301037"><i class="fas fa-layer-group me-2"></i>✆ WhatsApp</a>
                    </li>
                        <li class="nav-item">
                        <a class="nav-link text-white" href="http://t.me/samudra1804"><i class="fas fa-phone-alt me-2"></i>➣ Telegram</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="https://www.instagram.com/habibiramadhani02?igsh=MXV6b2RlanF5dGFtYg=="><i class="fas fa-layer-group me-2"></i>🅾 Instagram</a>
                    </li>

                </ul>
            </div>
        </li>
        <!-- END SUPPORT -->

        <!-- LOGOUT -->
        <li class="nav-item mt-3">
            <a class="nav-link text-white" href="<?= base_url('/logout') ?>">🔓 Logout</a>
        </li>
    </ul>
</div>
