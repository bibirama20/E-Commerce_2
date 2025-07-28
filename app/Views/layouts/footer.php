<footer class="text-center py-3 shadow-sm mt-auto" style="background: linear-gradient(135deg, #00bfff 0%, #007bff 100%); color: white;">
    <p class="mb-0 small">&copy; <?= date('Y') ?> <strong>ZETANI</strong> - Solusi Petani Masa Kini</p>
</footer>

<!-- Bootstrap Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Optional smooth scrolling for anchor links -->
<script>
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });
</script>

</body>
</html>
