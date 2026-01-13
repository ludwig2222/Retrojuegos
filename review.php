<?php
require_once('functions.php');

if(isset($_POST['last_name'])){
    $_SESSION['shipping']['first_name'] = $_POST['first_name'];
    $_SESSION['shipping']['last_name'] = $_POST['last_name'];
    $_SESSION['shipping']['address'] = $_POST['address'];
    $_SESSION['shipping']['phone_number'] = $_POST['phone_number'];
}

if(!isset($_SESSION['user'])){
   alert('warning', 'Register or login before you submit your order.');
   header('Location: login.php');
   die();    
}

require_once('header.php');

$cart_items = $_SESSION['cart'] ?? [];

if (empty($cart_items)) {
    alert('warning', 'Your cart is empty.');
}

if(isset($_SESSION['cart'])){
    $shipping = 6;
    $tax = 8;
}else{
    return 0;
}


$total = $cart_total + $shipping + $tax;

$u = $_SESSION['user'];
?>


<!-- Page Title-->
      
<div class="container d-lg-flex justify-content-between py-2 py-lg-3">
  <div class="order-lg-2 mb-3 mb-lg-0 pt-lg-2">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb breadcrumb-light flex-lg-nowrap justify-content-center justify-content-lg-start">
        <li class="breadcrumb-item"><a class="text-nowrap" href="index.php"><i class="ci-home"></i><?= __('breadcrumb_home') ?></a></li>
        <li class="breadcrumb-item text-nowrap"><a href="shop.php"><?= __('breadcrumb_shop') ?></a></li>
        <li class="breadcrumb-item text-nowrap"><a href="cart.php"><?= __('breadcrumb_cart') ?></a></li>
        <li class="breadcrumb-item text-nowrap"><a href="checkout.php"><?= __('breadcrumb_checkout') ?></a></li>
        <li class="breadcrumb-item text-nowrap active" aria-current="page"><?= __('breadcrumb_review') ?></li>
      </ol>
    </nav>
  </div>
  <div class="order-lg-1 pe-lg-4 text-center text-lg-start">
    <h1 class="h3 text-light mb-0"><?= __('page_title_review') ?></h1>
  </div>
</div>
      
<div class="container pb-5 mb-2 mb-md-4">
  <div class="row">
    <section class="col-lg-8">
        <form action="submit-order.php" method="post">
      <!-- Steps-->
      <div class="steps steps-light pt-2 pb-3 mb-5">
          <a class="step-item active" href="cart.php">
          <div class="step-progress"><span class="step-count">1</span></div>
          <div class="step-label"><i class="ci-cart"></i><?= __('step_cart') ?></div>
          </a>
          <a class="step-item active" href="checkout.php">
          <div class="step-progress"><span class="step-count">2</span></div>
          <div class="step-label"><i class="ci-user-circle"></i><?= __('step_checkout') ?></div>
          </a>
          <a class="step-item active current" href="review.php">
          <div class="step-progress"><span class="step-count">3</span></div>
          <div class="step-label"><i class="ci-check-circle"></i><?= __('step_review') ?></div>
          </a></div>

      <!-- Shipping address-->
      <h2 class="h6 pt-1 pb-3 mb-3 border-bottom text-light"><?= __('section_review_order') ?></h2>

      <?php
      foreach ($cart_items as $key => $item) {

      ?>
      <!-- Item-->
      <div class="d-sm-flex justify-content-between align-items-center my-2 pb-3 border-bottom">
        <div class="d-block d-sm-flex align-items-center text-center text-sm-start"><a class="d-inline-block flex-shrink-0 mx-auto me-sm-4" href="product.php?id=<?= $item['pro']['id'] ?>">

        <img src="<?= get_product_thumb($item['pro']['photos']) ?>" width="160" alt="Product"></a>

          <div class="pt-2">
            <h3 class="product-title fs-base mb-2"><a href="product.php?id=<?= $item['pro']['id'] ?>"><?= substr($item['pro']['name'],0,1000) ?></a></h3>
            <div class="fs-sm"><span class="text-muted me-2"><?= __('label_unit_price') ?> <?= convert_price($item['pro']['price']) ?></span></div>
<!--                  <div class="fs-sm"><span class="text-muted me-2">Color:</span>White &amp; Blue</div>-->
            <div class="fs-lg text-accent pt-2"><?= convert_price($item['pro']['price'] * $item['quantity']) ?></div>
          </div>
        </div>

      </div>
      <!-- Item-->
      <?php } ?>
      <!-- Navigation (desktop)-->
      <div class="d-none d-lg-flex pt-4 mt-3">
        <div class="w-50 pe-3"><a class="btn btn-outline-primary d-block w-100" href="cart.php"><i class="ci-arrow-left mt-sm-0 me-1"></i><span class="d-none d-sm-inline"><?= __('btn_back_to_cart_full') ?></span><span class="d-inline d-sm-none">Back</span></a></div>
        <div class="w-50 ps-2"><a class="btn btn-primary d-block w-100" href="submit-order.php"><span class="d-none d-sm-inline"><?= __('btn_submit_order_full') ?></span><span class="d-inline d-sm-none">Next</span><i class="ci-arrow-right mt-sm-0 ms-1"></i></a></div>
      </div>
      </form>
    </section>
    <!-- Sidebar-->
    <aside class="col-lg-4 pt-4 pt-lg-0 ps-xl-5">
      <div class="bg-black rounded-3 shadow-lg p-4 ms-lg-auto">
        <div class="py-2 px-xl-2">

          <ul class="list-unstyled fs-sm pb-2 border-bottom">
            <li class="d-flex justify-content-between align-items-center"><span class="me-2"><?= __('label_subtotal') ?></span><span class="text-end"><?= convert_price($cart_total) ?></span></li>
            <li class="d-flex justify-content-between align-items-center"><span class="me-2"><?= __('label_shipping') ?></span><span class="text-end"><?= convert_price(6) ?></span></li>
            <li class="d-flex justify-content-between align-items-center"><span class="me-2"><?= __('label_taxes') ?></span><span class="text-end"><?= convert_price(8) ?></span></li>
            <li class="d-flex justify-content-between align-items-center"><span class="me-2"><?= __('label_discount') ?></span><span class="text-end">—</span></li>
          </ul>
          <h3 class="fw-normal text-center my-4 price-accent"><?= convert_price($total) ?></h3>
          <form class="needs-validation" method="post" novalidate>
            <div class="mb-3">
              <input class="form-control" type="text" placeholder="<?= __('placeholder_promo_code') ?>" required>
              <div class="invalid-feedback"><?= __('feedback_promo_code') ?></div>
            </div>
            <button class="btn btn-outline-primary d-block w-100" type="submit"><?= __('btn_apply_promo') ?></button>
          </form>
        </div>
      </div>
    </aside>
  </div>
  <!-- Navigation (mobile)-->
  <div class="row d-lg-none">
    <div class="col-lg-8">
      <div class="d-flex pt-4 mt-3">
        <div class="w-50 pe-3"><a class="btn btn-outline-primary d-block w-100" href="cart.php"><i class="ci-arrow-left mt-sm-0 me-1"></i><span class="d-none d-sm-inline"><?= __('btn_back_to_cart_short') ?></span><span class="d-inline d-sm-none">Back</span></a></div>
        <div class="w-50 ps-2"><a class="btn btn-primary d-block w-100" href="submit-order.php"><span class="d-none d-sm-inline"><?= __('btn_submit_order_short') ?></span><span class="d-inline d-sm-none">Next</span><i class="ci-arrow-right mt-sm-0 ms-1"></i></a></div>
      </div>
    </div>
  </div>
</div>

<?php
require_once('footer.php');
?>