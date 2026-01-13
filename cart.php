<?php
require_once('functions.php');
require_once('header.php');

$cart_items = $_SESSION['cart'] ?? [];

if (empty($cart_items)) {
    alert('warning', __('cart_empty_warning'));
}

?>

<div class="container d-lg-flex justify-content-between py-2 py-lg-3">
 <div class="order-lg-2 mb-3 mb-lg-0 pt-lg-2">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb breadcrumb-light flex-lg-nowrap justify-content-center justify-content-lg-start">
      <li class="breadcrumb-item"><a class="text-nowrap" href="index.php"><i class="ci-home"></i><?= __('breadcrumb_home') ?></a></li>
      <li class="breadcrumb-item text-nowrap"><a href="shop.php"><?= __('breadcrumb_shop') ?></a>
      </li>
      <li class="breadcrumb-item text-nowrap active" aria-current="page"><?= __('breadcrumb_cart') ?></li>
    </ol>
  </nav>
 </div>
 <div class="order-lg-1 pe-lg-4 text-center text-lg-start">
  <h1 class="h3 text-light mb-0"><?= __('page_cart_title') ?></h1>
 </div>
</div>
     
<div class="container pb-5 mb-2 mb-md-4"> 
  <div class="row">
    <!-- List of items-->
    <section class="col-lg-8">
      <div class="d-flex justify-content-between align-items-center pt-3 pb-4 pb-sm-5 mt-1">
        <h2 class="h6 text-light mb-0"><?= __('cart_products_heading') ?></h2><a class="btn btn-outline-primary btn-sm ps-2" href="shop.php"><i class="ci-arrow-left me-2"></i><?= __('cart_continue_shopping') ?></a>
      </div>
        <form action="cart-process-update.php" method="post">
            <?php foreach ($cart_items as $key => $item): ?>
              <div class="d-sm-flex justify-content-between align-items-center my-2 pb-3 border-bottom">
                <div class="d-block d-sm-flex align-items-center text-center text-sm-start">
                  <a class="d-inline-block flex-shrink-0 mx-auto me-sm-4" href="product.php?id=<?= $item['pro']['id'] ?>">
                    <img src="<?= get_product_thumb($item['pro']['photos']) ?>" width="160" alt="Product">
                  </a>
                  <div class="pt-2">
                    <h3 class="product-title fs-base mb-2">
                      <a href="product.php?id=<?= $item['pro']['id'] ?>"><?= substr($item['pro']['name'],0,1000) ?></a>
                    </h3>
                    <div class="fs-sm">
                      <span class="text-muted me-2"><?= __('cart_unit_price') ?> <?= convert_price($item['pro']['price']) ?></span>
                    </div>
                    <div class="fs-lg text-accent pt-2">
                      <?= convert_price($item['pro']['price'] * $item['quantity']) ?>
                    </div>
                  </div>
                </div>
                <div class="pt-2 pt-sm-0 ps-sm-3 mx-auto mx-sm-0 text-center text-sm-start" style="max-width: 9rem;">
                  <label class="form-label"><?= __('cart_quantity_label') ?></label>
                  <!-- name="quantities[PRODUCT_ID]" -->
                  <input class="form-control" type="number" min="1"
                         name="quantities[<?= $item['pro']['id'] ?>]"
                         value="<?= $item['quantity'] ?>">
                  <a href="cart-process-remove.php?id=<?= $item['pro']['id'] ?>"
                     class="btn btn-link px-0 text-danger">
                     <i class="ci-close-circle me-2"></i><span class="fs-sm"><?= __('cart_remove_button') ?></span>
                  </a>
                </div>
              </div>
            <?php endforeach; ?>
          <button class="btn btn-outline-primary d-block w-100 mt-4" type="submit">
            <i class="ci-loading fs-base me-2"></i><?= __('cart_update_button') ?>
          </button>
        </form>
    </section>
    <!-- Sidebar-->
    <aside class="col-lg-4 pt-4 pt-lg-0 ps-xl-5">
      <div class="bg-black rounded-3 shadow-lg p-4">
        <div class="py-2 px-xl-2">
          <div class="text-center mb-4 pb-3 border-bottom">
            <h2 class="h6 mb-3 pb-1 text-light"><?= __('cart_subtotal') ?></h2>
            <h3 class="fw-normal price-accent"><?= convert_price($cart_total) ?></h3>
          </div>
          <div class="mb-3 mb-4">
            <label class="form-label mb-3" for="order-comments"><span class="badge bg-info fs-xs me-2"><?= __('cart_note_badge') ?></span><span class="fw-medium"><?= __('cart_additional_comments_label') ?></span></label>
            <textarea class="form-control" rows="6" id="order-comments"></textarea>
          </div>
          <div class="accordion" id="order-options">

          </div><a class="btn btn-primary btn-shadow d-block w-100 mt-4" href="checkout.php"><i class="ci-card fs-lg me-2"></i><?= __('cart_checkout_button') ?></a>
        </div>
      </div>
    </aside>
  </div>
</div>

<?php
require_once('footer.php');
?>