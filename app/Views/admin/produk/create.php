<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>


<form method="post" action="/admin/kelola-produk/simpan" enctype="multipart/form-data" class="p-4 rounded shadow bg-white">
    <h2 class="mb-4 fw-bold">🛒 Tambah Produk</h2>
    <div class="mb-3">
        <label for="name" class="form-label">Nama Produk</label>
        <input name="name" id="name" placeholder="Contoh: Kaos Polos" class="form-control" required>
    </div>

    <div class="mb-3">
        <label for="stock" class="form-label">Stok</label>
        <input name="stock" id="stock" type="number" placeholder="Jumlah stok" class="form-control" required>
    </div>

    <div class="mb-3">
        <label for="price" class="form-label">Harga</label>
        <input name="price" id="price" type="number" placeholder="Harga produk (Rp)" class="form-control" required>
    </div>

    <div class="mb-3">
        <label for="diskon" class="form-label">Diskon (%)</label>
        <input name="diskon" id="diskon" type="number" placeholder="Diskon dalam persen" class="form-control" min="0" max="100">
    </div>

    <div class="mb-3">
        <label for="weight" class="form-label">Berat (gram)</label>
        <input name="weight" id="weight" type="number" placeholder="Misal: 250" class="form-control" min="1" required>
        <div class="form-text">Masukkan berat produk dalam gram. Contoh: 250 untuk 250 gram.</div>
    </div>

    <div class="mb-3">
        <label for="image" class="form-label">Gambar Produk</label>
        <input type="file" name="image" id="image" class="form-control" accept="image/*" required>
        <div class="form-text">Pilih gambar produk (.jpg,), maksimal 2MB</div>
        <div id="preview" class="mt-3">
            <!-- Preview gambar muncul di sini -->
        </div>
    </div>

    <button class="btn btn-primary px-4">💾 Simpan</button>
</form>

<!-- Preview Gambar -->
<script>
    document.getElementById('image').addEventListener('change', function (e) {
        const preview = document.getElementById('preview');
        preview.innerHTML = ''; // clear sebelumnya

        const file = e.target.files[0];
        if (file && file.type.startsWith('image/')) {
            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.className = 'img-thumbnail mt-2';
            img.style.maxWidth = '200px';
            img.onload = () => URL.revokeObjectURL(img.src); // free memory
            preview.appendChild(img);
        }
    });
</script>

<?= $this->endSection() ?>
