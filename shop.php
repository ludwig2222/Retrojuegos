<?php
require_once('header.php');
// Variable para la barra de búsqueda:
$search = trim($_GET['q'] ?? '');

if ($search !== '') {
  $escaped = mysqli_real_escape_string($conn, $search);
  $products = db_select('products', "name LIKE '%$escaped%' OR description LIKE '%$escaped%' ORDER BY id DESC");
} else {
  $sort = $_GET['sort'] ?? 'newest';

    switch ($sort) {
      case 'price_asc':
        $order = 'price ASC';
        break;
      case 'price_desc':
        $order = 'price DESC';
        break;
      case 'name_asc':
        $order = 'name ASC';
        break;
      case 'name_desc':
        $order = 'name DESC';
        break;
      default:
        $order = 'id DESC';
}

$products = db_select('products', "1 ORDER BY $order");
}

$categories = db_select('categories', '1');
$category_map = [];

$products_by_category = [];
foreach ($products as $pro) {
    $products_by_category[$pro['category_id']][] = $pro;
}

foreach ($categories as $cat) {
    $category_map[$cat['id']] = $cat['name'];
}
?>


<div class="container d-lg-flex justify-content-between py-2 py-lg-3">
  <div class="order-lg-2 mb-3 mb-lg-0 pt-lg-2">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb breadcrumb-dark flex-lg-nowrap justify-content-center justify-content-lg-start">
        <li class="breadcrumb-item"><a class="text-nowrap" href="index.php"><i class="ci-home"></i><?= __('breadcrumb_home') ?></a></li>
        <li class="breadcrumb-item text-nowrap active" aria-current="page""><?= __('breadcrumb_shop') ?></li>
      </ol>
    </nav>
  </div>
  <div class="order-lg-1 pe-lg-4 text-center text-lg-start">
    <h1 class="h3 text-white mb-0"><?= __('sidebar_title') ?></h1>
  </div>
</div>

<div class="container pb-5 mb-2 mb-md-4">
  <div class="row">
    <!-- Sidebar-->
    <aside class="col-lg-4">
      <!-- Sidebar-->
      <div class="offcanvas offcanvas-collapse bg-black w-100 rounded-3 shadow-lg py-1" id="shop-sidebar" style="max-width: 22rem;">
        <div class="offcanvas-body py-grid-gutter px-lg-grid-gutter">
          <!-- Categories-->
          <div class="widget widget-categories mb-4 pb-4 border-bottom">
            <h2 class="widget-title text-white"><?= __('categories') ?></h2>
            <div class="accordion mt-n1" id="shop-categories">
              <!-- Games-->
              <?php foreach ($categories as $index => $cat): ?>
                  <?php
                    $cat_id = $cat['id'];
                    $cat_slug = strtolower(preg_replace('/[^a-z0-9]+/', '-', $cat['name']));
                    $cat_products = isset($products_by_category[$cat_id]) ? $products_by_category[$cat_id] : [];
                  ?>
                  <div class="accordion-item">
                    <h3 class="accordion-header">
                      <a class="accordion-button collapsed" href="#cat-<?= $cat_slug ?>" role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="cat-<?= $cat_slug ?>">
                        <?= htmlspecialchars(__($cat['name'])) ?>
                      </a>
                    </h3>
                    <div class="accordion-collapse collapse" id="cat-<?= $cat_slug ?>" data-bs-parent="#shop-categories">
                      <div class="accordion-body">
                        <div class="widget widget-links widget-filter">
                          <ul class="widget-list widget-filter-list pt-1" style="height: 12rem;" data-simplebar data-simplebar-auto-hide="false">
                            <?php if (!empty($cat_products)): ?>
                              <?php foreach ($cat_products as $pro): ?>
                                <li class="widget-list-item widget-filter-item">
                                  <a class="widget-list-link d-flex justify-content-between align-items-center" href="product.php?id=<?= $pro['id'] ?>">
                                    <span class="widget-filter-item-text"><?= htmlspecialchars($pro['name']) ?></span>
                                    <span class="fs-xs ms-3 price-accent"><?= convert_price(number_format($pro['price'], 2)) ?></span>
                                  </a>
                                </li>
                              <?php endforeach; ?>
                            <?php else: ?>
                              <li class="widget-list-item widget-filter-item text-muted"><?= __('no_products') ?></li>
                            <?php endif; ?>
                          </ul>
                        </div>
                      </div>
                    </div>
                  </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>
    </aside>
    <!-- Content  -->
    <section class="col-lg-8">
      <!-- Filter Toggle Button for Mobile -->
      <div class="d-lg-none mb-3 text-center">
        <button class="btn btn-outline-secondary" type="button" data-bs-toggle="offcanvas" data-bs-target="#shop-sidebar">
          <i class="ci-filter me-2"></i><?= __('browse_categories') ?>
        </button>
      </div>
      <!-- Toolbar-->
      <div class="d-flex justify-content-between align-items-center flex-wrap mb-4 gap-2">
          <!-- Sorting dropdown -->
          <form method="GET" class="d-flex align-items-center">
            <input type="hidden" name="q" value="<?= htmlspecialchars($search) ?>">
            <label for="sort" class="me-2 mb-0"><?= __('sort_by') ?>:</label>
            <select name="sort" id="sort" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
              <option value="newest" <?= ($_GET['sort'] ?? '') === 'newest' ? 'selected' : '' ?>><?= __('newest') ?></option>
              <option value="price_asc" <?= ($_GET['sort'] ?? '') === 'price_asc' ? 'selected' : '' ?>><?= __('price_asc') ?></option>
              <option value="price_desc" <?= ($_GET['sort'] ?? '') === 'price_desc' ? 'selected' : '' ?>><?= __('price_desc') ?></option>
              <option value="name_asc" <?= ($_GET['sort'] ?? '') === 'name_asc' ? 'selected' : '' ?>><?= __('name_asc') ?></option>
              <option value="name_desc" <?= ($_GET['sort'] ?? '') === 'name_desc' ? 'selected' : '' ?>><?= __('name_desc') ?></option>
            </select>
          </form>
          <!-- Grid/List toggle -->
          <div class="d-flex align-items-center">
            <a id="gridViewBtn" class="btn btn-sm btn-outline-primary me-2 active" href="#" aria-label="Vista en cuadrícula"><i class="ci-view-grid"></i></a>
            <a id="listViewBtn" class="btn btn-sm btn-outline-primary" href="#" aria-label="Vista en lista"><i class="ci-view-list"></i></a>
          </div>
      </div>
     <!-- Products grid -->
      <div id="productContainer" class="row">
      <?php foreach ($products as $key => $product): 
       $cat_name = isset($category_map[$product['category_id']]) ? $category_map[$product['category_id']] : 'Uncategorized';
      ?>
          <!-- Product -->
          <div class="product-item col-md-4 col-sm-6 px-2 mb-4">
            <div class="card product-card">
              <a class="card-img-top d-block overflow-hidden text-center bg-black" href="product.php?id=<?= $product['id'] ?>">
                <img class="img-fluid product-thumb" src="<?= get_product_thumb($product['photos']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
              </a>
              <div class="card-body py-2 bg-black">
                <a class="product-meta d-block fs-xs pb-1 text-muted" href="#"><?= htmlspecialchars(__($cat_name)) ?></a>
                <h3 class="product-title fs-sm">
                  <a href="product.php?id=<?= $product['id'] ?>"><?= $product['name'] ?></a>
                </h3>
                <div class="d-flex justify-content-between">
                  <div class="product-price">
                    <span class="text-accent"><?= convert_price($product['price']) ?></span>
                  </div>
                  <!-- Wishlist Button -->
                  <a href="wishlist-add.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-outline-danger position-absolute top-0 end-0 m-2" title="<?= __('add_to_wishlist') ?>">
                    <i class="ci-heart"></i>
                  </a>
                  <!-- Add to Cart Button -->
                  <form action="cart-process-add.php" method="POST" class="position-absolute bottom-0 end-0 m-2">
                    <input type="hidden" name="id" value="<?= $product['id'] ?>">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="btn btn-sm btn-outline-primary" title="<?= __('add_to_cart') ?>">
                      <i class="ci-cart"></i>
                    </button>
                  </form>
                </div>
              </div>
            </div>
            <hr class="d-sm-none">
          </div>
          <!-- Product -->
          <?php endforeach; ?>

          <?php if (empty($products)): ?>
          <div class="alert alert-warning text-center">
            <?= sprintf(__('no_products_found'), htmlspecialchars($search)) ?>
          </div>
      <?php endif; ?>
      </div>
    </section>
  </div>
</div>

<?php
require_once('footer.php');
?>