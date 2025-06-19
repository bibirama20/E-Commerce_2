<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-3 col-lg-2 d-md-block bg-dark text-white sidebar py-3">
            <div class="position-sticky">

                <ul class="nav flex-column">
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
        </nav>

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
