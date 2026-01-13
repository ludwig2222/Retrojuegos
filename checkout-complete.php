<?php
require_once('header.php');
if(isset($_SESSION['cart'])){
    unset($_SESSION['cart']);
}
$cart_items = [];

?>


<div class="container pb-5 mb-sm-4">
    <div class="pt-5">
      <div class="card bg-black py-3 mt-sm-3">
        <div class="card-body text-center">
          <h2 class="h4 pb-3 text-white"><?= __('order_thank_you_title') ?></h2>
          <p class="fs-sm mb-2"><?= __('order_placed_message') ?></p>
          <p class="fs-sm mb-2"><?= __('order_download_prompt') ?></p>
          <p class="fs-sm"><?= __('order_email_notice') ?> <u><?= __('order_next_steps') ?></u></p><a class="btn btn-outline-primary mt-3 me-3" href="shop.php"><?= __('order_back_to_shop') ?></a><a class="btn btn-outline-primary mt-3" href="account-orders.php"><i class="ci-location"></i> <?= __('order_go_to_orders') ?></a>
        </div>
      </div>
    </div>
</div>


<?php
require_once('footer.php');
?>