<?php
require_once('functions.php');

// Usuario no acreditado es mandado a 'login'
if (!isset($_SESSION['user'])) {
    alert('warning', __('order_auth_warning'));
    header('Location: login.php');
    exit;
}

// Si no hay artículos dentro del carrito, te manda a la tienda después de una alerta
if (empty($_SESSION['cart'])) {
    alert('warning', __('cart_empty_warning'));
    header('Location: shop.php'); 
    exit;
}

require_once('header.php');

$cart_items = $_SESSION['cart'];
$u = $_SESSION['user'];
?>

<!-- Page Title-->
     
        <div class="container d-lg-flex justify-content-between py-2 py-lg-3">
          <div class="order-lg-2 mb-3 mb-lg-0 pt-lg-2">
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb breadcrumb-light flex-lg-nowrap justify-content-center justify-content-lg-start">
                <li class="breadcrumb-item"><a class="text-nowrap" href="index.php"><i class="ci-home"></i></i><?= __('breadcrumb_home') ?></a></li>
                <li class="breadcrumb-item text-nowrap"><a href="shop.php"><?= __('breadcrumb_shop') ?></a>
                <li class="breadcrumb-item text-nowrap"><a href="cart.php"><?= __('breadcrumb_cart') ?></a>
                </li>
                <li class="breadcrumb-item text-nowrap active" aria-current="page"><?= __('breadcrumb_checkout') ?></li>
              </ol>
            </nav>
          </div>
          <div class="order-lg-1 pe-lg-4 text-center text-lg-start">
            <h1 class="h3 text-light mb-0"><?= __('page_title_checkout') ?></h1>
          </div>
        </div>
      
      <div class="container pb-5 mb-2 mb-md-4">
        <div class="row">
          <section class="col-lg-8">
             <form action="review.php" method="post">
            <!-- Steps-->
            <div class="steps steps-light pt-2 pb-3 mb-5">
                <a class="step-item active" href="cart.php">
                <div class="step-progress"><span class="step-count">1</span></div>
                <div class="step-label"><i class="ci-cart"></i><?= __('step_cart') ?></div>
                </a>
                <a class="step-item active current" href="checkout.php">
                <div class="step-progress"><span class="step-count">2</span></div>
                <div class="step-label"><i class="ci-user-circle"></i><?= __('step_checkout') ?></div>
                </a>
                <a class="step-item" href="review.php">
                <div class="step-progress"><span class="step-count">3</span></div>
                <div class="step-label"><i class="ci-check-circle"></i><?= __('step_review') ?></div>
                </a></div>
            <!-- Autor info-->
            <div class="d-sm-flex justify-content-between align-items-center bg-darker p-4 rounded-3 mb-grid-gutter">
              <div class="d-flex align-items-center">
                <div class="img-thumbnail rounded-circle position-relative flex-shrink-0"><span class="badge bg-warning position-absolute end-0 mt-n2" data-bs-toggle="tooltip" title="<?= __('tooltip_reward_points') ?>"><?= $u['id'] ?></span><img class="rounded-circle" src="img/photo-user.png" width="90" alt=""></div>
                <div class="ps-3">
                  <h3 class="fs-base mb-0 text-light"><?= $u['first_name']." ".$u['last_name'] ?></h3><span class="text-accent fs-sm"><?= $u['email'] ?></span>
                </div>
              </div>
            </div>
            <!-- Shipping address-->
            <h2 class="h6 pt-1 pb-3 mb-3 border-bottom text-light"><?= __('section_shipping_address') ?></h2>
            <div class="row">
                <div class="col-sm-6">
                  <div class="mb-3">       
                     <?= text_input([
                          'name' => 'first_name',
                          'label' => __('label_first_name'),
                          'value' => $u['first_name'],
                          'attributes' => 'required'        
                      ]) ?>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="mb-3">
                    <?= text_input([
                          'name' => 'last_name',
                          'label' => __('label_last_name'),
                          'value' => $u['last_name'],
                          'attributes' => 'required'        
                      ]) ?>
                  </div>
                </div>
                </div>
                <div class="row">
                <div class="col-sm-6">
                  <div class="mb-3">
                    <?= text_input([
                          'name' => 'phone_number',
                          'label' => __('label_phone_number'),
                          'attributes' => 'required',        
                      ]) ?>
                  </div>
                </div>
                </div>        
            <div class="row">
              <div class="col-sm-12">
                <div class="mb-3">
                  <?= text_input([
                        'name' => 'address',
                        'label' => __('label_address'),
                        'attributes' => 'required',        
                    ]) ?>
                </div>
              </div>
            </div>
            <h6 class="mb-3 py-3 border-bottom text-light"><?= __('section_billing_address') ?></h6>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" checked id="same-address">
              <label class="form-check-label" for="same-address"><?= __('checkbox_same_address') ?></label>
            </div>
            <!-- Navigation (desktop)-->
            <div class="d-none d-lg-flex pt-4 mt-3">
              <div class="w-50 pe-3"><a class="btn btn-outline-primary d-block w-100" href="cart.php"><i class="ci-arrow-left mt-sm-0 me-1"></i><span class="d-none d-sm-inline"><?= __('btn_back_to_cart_full') ?></span><span class="d-inline d-sm-none">Back</span></a></div>
              <div class="w-50 ps-2"><button type="submit" class="btn btn-primary d-block w-100" href="review.php"><span class="d-none d-sm-inline"><?= __('btn_proceed_to_shipping_full') ?></span><span class="d-inline d-sm-none">Next</span><i class="ci-arrow-right mt-sm-0 ms-1"></i></button></div>
            </div>
            </form>
          </section>
          
        </div>
        <!-- Navigation (mobile)-->
        <div class="row d-lg-none">
          <div class="col-lg-8">
            <div class="d-flex pt-4 mt-3">
              <div class="w-50 pe-3"><a class="btn btn-outline-primary d-block w-100" href="cart.php"><i class="ci-arrow-left mt-sm-0 me-1"></i><span class="d-none d-sm-inline"><?= __('btn_back_to_cart_short') ?></span><span class="d-inline d-sm-none">Back</span></a></div>
              <div class="w-50 ps-2"><a class="btn btn-primary d-block w-100" href="review.php"><span class="d-none d-sm-inline"><?= __('btn_proceed_to_shipping_short') ?></span><span class="d-inline d-sm-none">Next</span><i class="ci-arrow-right mt-sm-0 ms-1"></i></a></div>
            </div>
          </div>
        </div>
      </div>

<?php
require_once('footer.php');
?>