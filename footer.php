 </main>
    <!-- Footer-->
    <footer class="footer bg-dark pt-5">
      <div class="container">
        <div class="row pb-2">
          <div class="col-md-4 col-sm-6">
          </div>
        </div>
      </div>
      <div class="pt-5 bg-darker">
        <div class="container">
            <div class="col-md-6 text-center text-md-start mb-4"> 
              <div class="widget widget-links widget-light">
                <ul class="widget-list d-flex flex-wrap justify-content-center justify-content-md-start">
                  <li class="widget-list-item me-4"><a class="widget-list-link text-white" href="about-us.php"><?= __('about_retrojuegos') ?></a></li>
                </ul>
              </div>
            </div>
          <div class="pb-4 fs-xs text-light text-center text-md-start"><?= __('copyright_notice') ?></div>
        </div>
      </div>
    </footer>
    <!-- Toolbar for handheld devices (Default)-->
    <div class="handheld-toolbar">
      <div class="d-table table-layout-fixed w-100 bg-black">
          <a class="d-table-cell handheld-toolbar-item" href="account-wishlist.php">
              <span class="handheld-toolbar-icon"><i class="ci-heart"></i></span>
              <span class="handheld-toolbar-label"><?= __('wishlist') ?></span>
          </a>
          <a class="d-table-cell handheld-toolbar-item" href="javascript:void(0)" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" onclick="window.scrollTo(0, 0)">
              <span class="handheld-toolbar-icon"><i class="ci-menu"></i></span>
              <span class="handheld-toolbar-label"><?= __('menu') ?></span>
          </a>
          <a class="d-table-cell handheld-toolbar-item" href="cart.php">
              <span class="handheld-toolbar-icon"><i class="ci-cart"></i><span class="badge bg-primary rounded-pill ms-1"><?= $cart_count ?></span></span>
              <span class="handheld-toolbar-label"><?= __('cart_total_label') ?>: $<?= $cart_total ?>.<small>00</small></span>
          </a>
      </div>
    </div>
    <!-- Back To Top Button--><a class="btn-scroll-top" href="#top" data-scroll><span class="btn-scroll-top-tooltip text-muted fs-sm me-2">Top</span><i class="btn-scroll-top-icon ci-arrow-up">   </i></a>
    <!-- Vendor scrits: js libraries and plugins-->
    <script src="vendor/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/simplebar/dist/simplebar.min.js"></script>
    <script src="vendor/tiny-slider/dist/min/tiny-slider.js"></script>
    <script src="vendor/smooth-scroll/dist/smooth-scroll.polyfills.min.js"></script>
    <script src="vendor/drift-zoom/dist/Drift.min.js"></script>
    <!-- Main theme script-->
    <script src="js/theme.min.js"></script>
    
    <script>
        document.addEventListener("DOMContentLoaded", function () {
          const geoLink = document.getElementById("geo-link");

          if ("geolocation" in navigator) {
            navigator.geolocation.getCurrentPosition(
              function (position) {
                const lat = position.coords.latitude.toFixed(6);
                const lon = position.coords.longitude.toFixed(6);
                geoLink.href = `https://www.google.com/maps/@${lat},${lon},15z`;
                geoLink.textContent = "Open in Maps";
              },
              function (error) {
                geoLink.textContent = "Location unavailable";
                geoLink.removeAttribute("href");
              }
            );
          } else {
            geoLink.textContent = "Geolocation not supported";
            geoLink.removeAttribute("href");
          }
        });
    </script>
    <script>
        document.querySelectorAll('.navbar-tool.dropdown').forEach(function (dropdown) {
          dropdown.addEventListener('mouseenter', function () {
            this.classList.add('show');
            const menu = this.querySelector('.dropdown-menu');
            if (menu) menu.classList.add('show');
          });

          dropdown.addEventListener('mouseleave', function () {
            this.classList.remove('show');
            const menu = this.querySelector('.dropdown-menu');
            if (menu) menu.classList.remove('show');
          });
        });
    </script>
    <script>
        // Guarda la posición del 'scroll' antes de navegar
        document.querySelectorAll('a[href*="wishlist-add.php"], a[href*="wishlist-remove.php"]').forEach(link => {
          link.addEventListener('click', function () {
            localStorage.setItem('scrollY', window.scrollY);
          });
        });

        // Restaura la posición del 'scroll' al cargar la página
        window.addEventListener('load', function () {
          const scrollY = localStorage.getItem('scrollY');
          if (scrollY !== null) {
            window.scrollTo(0, parseInt(scrollY));
            localStorage.removeItem('scrollY');
          }
        });
    </script>
    <script>
        const gridBtn = document.getElementById('gridViewBtn');
        const listBtn = document.getElementById('listViewBtn');

        gridBtn.addEventListener('click', function (e) {
          e.preventDefault();
          document.querySelectorAll('.product-item').forEach(item => {
            item.classList.remove('w-100');
            item.classList.add('col-md-4', 'col-sm-6');
          });
          document.querySelectorAll('.product-card').forEach(card => {
            card.classList.remove('list-mode');
          });
          gridBtn.classList.add('active');
          listBtn.classList.remove('active');
        });

        listBtn.addEventListener('click', function (e) {
          e.preventDefault();
          document.querySelectorAll('.product-item').forEach(item => {
            item.classList.remove('col-md-4', 'col-sm-6');
            item.classList.add('w-100');
          });
          document.querySelectorAll('.product-card').forEach(card => {
            card.classList.add('list-mode');
          });
          listBtn.classList.add('active');
          gridBtn.classList.remove('active');
        });
    </script>
  </body>
</html>