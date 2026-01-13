<?php
require_once('functions.php');
requireRole(['customer','admin','webmaster']);

protected_area();

require_once('header.php');

//echo password_hash("", PASSWORD_DEFAULT);

?>

<div class="container d-lg-flex justify-content-between py-2 py-lg-3">
<div class="order-lg-2 mb-3 mb-lg-0 pt-lg-2">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb breadcrumb-dark flex-lg-nowrap justify-content-center justify-content-lg-start">
        <li class="breadcrumb-item"><a class="text-nowrap" href="index.php"><i class="ci-home"></i><?= __('breadcrumb_home') ?></a></li>
        <li class="breadcrumb-item text-nowrap active" aria-current="page"><?= __('breadcrumb_account') ?></li>
      </ol>
    </nav>
</div>    
    <div class="order-lg-1 pe-lg-4 text-center text-lg-start">
      <h1 class="h3 text-white mb-0"><?= __('page_title_account') ?></h1>
    </div>
</div>
    <div class="container pb-5 mb-2 mb-md-4">
      <div class="row">
        <!-- Sidebar-->
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
        <!-- Content  -->
      </div>
    </div>
    
<?php 
require_once('footer.php'); 
?>