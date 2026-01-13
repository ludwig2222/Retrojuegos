<?php
require_once('functions.php');
protected_area();

$order_id = intval($_GET['id'] ?? 0);
$user_id = $_SESSION['user']['id'] ?? 0;

// Coge el pedido por ID & confirma que es del usuario
$order = db_select_one('orders', "id = $order_id AND customer_id = $user_id");
if (!$order) {
    echo "<p class='text-danger text-center mt-5'>Order not found or unauthorized access.</p>";
    require_once('footer.php');
    exit;
}

$date = new DateTime($order['order_date']); // "@" le dice a DateTime que es una marca de tiempo
$date->setTimezone(new DateTimeZone('Europe/Madrid')); // O cualquier zona GMT+2

// Decodifica el JSON del carrito
$cart_data = json_decode($order['cart'], true);
$shipping = json_decode($order['shipping'], true);

require_once('header.php');
?>
<div class="container d-lg-flex justify-content-between py-2 py-lg-3">
<div class="order-lg-2 mb-3 mb-lg-0 pt-lg-2">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb breadcrumb-dark flex-lg-nowrap justify-content-center justify-content-lg-start">
        <li class="breadcrumb-item"><a class="text-nowrap" href="index.php"><i class="ci-home"></i><?= __('breadcrumb_home') ?></a></li>
        <li class="breadcrumb-item text-nowrap"><a href="customer-dashboard.php"><?= __('breadcrumb_account') ?></a></li>
        <li class="breadcrumb-item text-nowrap"><a href="account-orders.php"><?= __('breadcrumb_orders') ?></a></li>
        <li class="breadcrumb-item text-nowrap active" aria-current="page"><?= __('breadcrumb_order_details') ?></li>
      </ol>
    </nav>
</div>
<div class="order-lg-1 pe-lg-4 text-center text-lg-start">
  <h1 class="h3 text-white mb-0"><?= __('my_order_details') ?></h1>
</div>
</div>
<div class="container pb-5 mb-2 mb-md-4">
      <div class="row">
          <aside class="col-lg-4 pt-4 pt-lg-0 pe-xl-5">
            <div class="bg-black rounded-3 shadow-lg pt-1 mb-5 mb-lg-0">
              <div class="d-md-flex justify-content-between align-items-center text-center text-md-start p-4">
                <div class="d-md-flex align-items-center">
                  <div class="img-thumbnail rounded-circle position-relative flex-shrink-0 mx-auto mb-2 mx-md-0 mb-md-0" style="width: 6.375rem;"><img class="rounded-circle" src="img/shop/account/user.png" alt="Susan Gardner"></div>
                  <div class="ps-md-3">
                    <h3 class="fs-base mb-0 text-white"><?= $_SESSION['user']['first_name'] ?></h3><span class="text-accent fs-sm"><?= $_SESSION['user']['email'] ?></span>
                  </div>
                </div><a class="btn btn-primary d-lg-none mb-2 mt-3 mt-md-0" href="#account-menu" data-bs-toggle="collapse" aria-expanded="false"><i class="ci-menu me-2"></i><?= __('account_menu') ?></a>
              </div>
              <div class="d-lg-block collapse" id="account-menu">
                <div class="bg-darker px-4 py-3">
                  <h3 class="fs-sm mb-0 text-white"><?= __('customer_dashboard') ?></h3>
                </div>
                <ul class="list-unstyled mb-0">
                  <li class="border-bottom mb-0"><a class="nav-link-style d-flex align-items-center px-4 py-3" href="account-orders.php"><i class="ci-user opacity-60 me-2"></i><?= __('my_orders') ?></a></li>
                  <li class="border-bottom mb-0"><a class="nav-link-style d-flex align-items-center px-4 py-3" href="account-wishlist.php"><i class="ci-user opacity-60 me-2"></i><?= __('my_wishlist') ?></a></li>
                  <li class="border-bottom mb-0"><a class="nav-link-style d-flex align-items-center px-4 py-3" href="account-settings.php"><i class="ci-user opacity-60 me-2"></i><?= __('settings') ?></a></li>
                  <li class="border-top mb-0"><a class="nav-link-style d-flex align-items-center px-4 py-3" href="logout.php"><i class="ci-sign-out opacity-60 me-2"></i><?= __('sign_out') ?></a></li>
                </ul>
              </div>
            </div>
          </aside>
          
          <section class="col-lg-8">
            <h2 class="h4 mb-4 text-white">Order #<?= $order['id'] ?> — <?= sprintf(__('order_placed_on'), $date->format('F j, Y')) ?></h2>
              <?php if (!empty($cart_data) && is_array($cart_data)): ?>
                <div class="list-group mb-4">
                  <?php 
                  $order_total = 0;

                  foreach ($cart_data as $item):
                    $product_id = intval($item['pro']['id'] ?? 0);
                    $pro = db_select_one('products', "id = $product_id");
                    $quantity = intval($item['quantity'] ?? 1);

                    if (!$pro || !isset($pro['id'])) continue;
                    $product_link = "product.php?id={$product_id}";
                    $thumb = get_product_thumb($pro['photos']);
                    $name = htmlspecialchars($pro['name']);
                    $price = floatval($pro['price']);
                    $file_path = $pro['file_path'] ?? '';

                    $subtotal = $price * $quantity;
                    $order_total += $subtotal;
                  ?>
                  <div class="list-group-item bg-black d-flex align-items-start py-3">
                    <a href="<?= $product_link ?>" class="me-3" style="width: 120px; flex-shrink: 0;">
                      <img src="<?= $thumb ?>" alt="<?= $name ?>" class="img-fluid rounded shadow-sm">
                    </a>
                    <div class="flex-grow-1">
                      <h5 class="mb-1"><a href="<?= $product_link ?>"><?= $name ?></a></h5>
                      <p class="mb-1 price-accent"><?= __('price_label') ?> <?= convert_price(number_format($price, 2)) ?></p>
                      <p class="mb-1 text-white"><?= __('quantity_label') ?> <?= $quantity ?></p>

                      <div class="mt-2">
                        <a class="btn btn-outline-primary btn-sm" href="download.php?id=<?= $product_id ?>">
                          <i class="ci-download me-1"></i><?= __('download_button') ?>
                        </a>
                      </div>
                    </div>
                  </div>
                  <?php endforeach; ?>
                </div>

                <div class="card bg-black">
                  <div class="card-body">
                    <h5><?= __('billing_info') ?></h5>
                    <p>
                      <strong><?= __('billing_name') ?></strong>
                      <?= ucwords(htmlspecialchars($shipping['first_name'] ?? '')) ?>
                      <?= ucwords(htmlspecialchars($shipping['last_name'] ?? '')) ?>
                    </p>
                    <p><strong><?= __('billing_address') ?></strong><br>
                      <?= nl2br(htmlspecialchars(ucwords($shipping['address'] ?? ''))) ?>
                    </p>
                    <p><strong><?= __('billing_phone') ?></strong> <?= htmlspecialchars($shipping['phone_number'] ?? '') ?></p>

                    <p><strong><?= __('billing_total') ?></strong> <?= convert_price(number_format($order['total_price'], 2)) ?></p>
                  </div>
                </div>

              <?php else: ?>
                <p class="text-warning"><?= __('no_products_in_order') ?></p>
              <?php endif; ?>
          </section>
      </div>
</div>              
<?php 
require_once('footer.php'); 
?>
