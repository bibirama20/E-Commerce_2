<div class="sidebar">
    <ul class="nav flex-column mt-3">
        <li class="nav-item">
            <a class="nav-link text-white" href="<?= base_url(session()->get('role') . '/dashboard') ?>">
               🖥️ Dashboard
            </a>
        </li>

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

        <li class="nav-item mt-3">
            <a class="nav-link text-danger" href="<?= base_url('/logout') ?>">🔓 Logout</a>
        </li>
    </ul>
</div>
