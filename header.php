<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Gestión del envío del menú desplegable de moneda - 'currency'
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['currency'])) {
    $_SESSION['currency'] = strtoupper($_POST['currency']);
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}

require_once('functions.php');

$cart_items = [];
$cart_total = 0;
$cart_count = 0;

if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $id => $item) {
        $cart_total += $item['pro']['price'] * $item['quantity'];
        $cart_count += $item['quantity'];
        $cart_items[] = $item;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
  

<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<head>
    <meta charset="utf-8">
    <title>Retrojuegos video game page e-commerce</title>
    <meta name="description" content="Retrojuegos - Bootstrap">
    <meta name="author" content="Luis Miguel">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="img/favicon.png">  
    <meta name="theme-color" content="#ffffff">
    <!-- Slider Style -->
    <link rel="stylesheet" media="screen" href="vendor/tiny-slider/dist/tiny-slider.css"/>
    <!-- Main Theme Styles -->
    <link rel="stylesheet" media="screen" href="css/theme.min.css">
    <link rel="stylesheet" href="css/custom.css">
  </head>
  <!-- Body-->
  <body class="handheld-toolbar-enabled">   
      
      <!-- Navbar 3 Level (Light)-->
      <header class="shadow-sm">
        <!-- Topbar-->
        <div class="topbar topbar-dark bg-dark">
        <div class="container">
         
        <div class="topbar-logo me-3">
          <a class="navbar-brand" href="<?= url('') ?>">
            <img src="img/logo.png" class="logo-img" alt="Retrojuegos">
          </a>
        </div>
                             
        <div class="topbar-mobile-search d-flex ms-4 flex-grow-1">
         <form action="shop.php" method="get" class="w-100 position-relative">
          <input class="form-control rounded-end pe-5" type="text" name="q" placeholder="<?= __('search_placeholder') ?>" aria-label="Buscar productos">
          <!-- Clickable magnifying glass button -->
          <button type="submit"
            class="position-absolute top-50 end-0 translate-middle-y text-muted fs-base me-3 border-0 bg-transparent p-0"
            aria-label="Buscar">
            <i class="ci-search"></i>
          </button>
         </form>
        </div>

        <div class="navbar navbar-expand-lg navbar-dark">
           <div class="navbar-toolbar d-flex flex-shrink-0 align-items-center">
             <a class="navbar-tool d-none d-lg-flex ms-3" href="account-wishlist.php">
               <span class="navbar-tool-tooltip"><?= __('wishlist') ?></span>
               <div class="navbar-tool-icon-box"><i class="navbar-tool-icon ci-heart"></i></div>
             </a>

             <?php
             if (is_logged_in()) {
               $userType = $_SESSION['user']['user_type'] ?? 'customer';

               // Determina el tipo de panel basado en el rol
               switch ($userType) {
                 case 'admin':
                   $dashboardUrl = 'admin-dashboard.php';
                   break;
                 case 'webmaster':
                   $dashboardUrl = 'webmaster-dashboard.php';
                   break;
                 case 'customer':
                 default:
                   $dashboardUrl = 'customer-dashboard.php';
                   break;
               }
             ?>
               <div class="dropdown">
                   <a class="navbar-tool ms-1 ms-lg-0 me-n1 me-lg-2 dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                     <div class="navbar-tool-icon-box">
                         <i class="navbar-tool-icon ci-user"></i>
                     </div>
                     <div class="navbar-tool-text ms-n3">
                       <small class="hello-user"><?= sprintf(__('hello_user'), htmlspecialchars($_SESSION['user']['first_name'])) ?></small>
                       <?= __('my_account') ?>
                     </div>
                   </a>
                   <ul class="dropdown-menu dropdown-menu-end login-dropdown">
                     <li><a class="dropdown-item" href="<?= $dashboardUrl ?>"><?= __('dashboard') ?></a></li>
                     <li><hr class="dropdown-divider"></li>
                     <li><a class="dropdown-item text-danger" href="logout.php"><?= __('logout') ?></a></li>
                   </ul>
               </div>
             <?php } else { ?>
               <a class="navbar-tool ms-1 ms-lg-0 me-n1 me-lg-2" href="login.php">
                 <div class="navbar-tool-icon-box"><i class="navbar-tool-icon ci-user"></i></div>
                 <div class="navbar-tool-text ms-n3">
                   <small class="hello-user"><?= __('hello_guest') ?></small>
                   <?= __('my_account') ?>
                 </div>
               </a>
             <?php } ?>
           </div>
        </div>   
              
         <!-- Idioma & Moneda -->
        <div class="ms-4 text-wrap">
            <div class="dropdown me-4 w-100 w-md-auto">
                <a class="topbar-link dropdown-toggle"
                   href="#"
                   role="button"
                   data-bs-toggle="dropdown"
                   aria-expanded="false">

                    <img src="img/flags/<?= ($_SESSION['lang'] ?? 'en') === 'es' ? 'es' : 'en' ?>.png"
                         width="20"
                         alt=""
                         aria-hidden="true">

                    <?= ($_SESSION['lang'] ?? 'en') === 'es' ? 'Español' : 'English' ?>
                </a>

                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="?lang=es">
                            <img src="img/flags/es.png"
                                 width="20"
                                 alt=""
                                 aria-hidden="true">
                            Español
                        </a>
                    </li>

                    <li>
                        <a class="dropdown-item" href="?lang=en">
                            <img src="img/flags/en.png"
                                 width="20"
                                 alt=""
                                 aria-hidden="true">
                            English
                        </a>
                    </li>
                </ul>
            </div> 

             <div class="topbar-text dropdown disable-autohide me-3">
               <form method="post" id="currency-form" class="d-inline">
                   <select name="currency" class="form-select form-select-sm" aria-label="Seleccionar moneda" onchange="document.getElementById('currency-form').submit();">
                     <option value="USD" <?= ($_SESSION['currency'] ?? 'USD') === 'USD' ? 'selected' : '' ?>><?= __('currency_usd') ?></option>
                     <option value="EUR" <?= ($_SESSION['currency'] ?? 'USD') === 'EUR' ? 'selected' : '' ?>><?= __('currency_eur') ?></option>
                   </select>
               </form>   
             </div>
        </div>
           
        <div class="navbar-tool dropdown ms-2">
            <a class="navbar-tool-icon-box cart-icon-box dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="navbar-tool-label "><?= $cart_count ?></span><i class="navbar-tool-icon ci-cart"></i></a>
            <a class="navbar-tool-text" href="cart.php"><small><?= __('my_cart') ?></small>$<?= $cart_total ?>.00</a>
          <!-- Carrito Desplegable -->
         <div class="dropdown-menu dropdown-menu-end">
          <div class="widget widget-cart px-3 pt-2 pb-3" style="width: 20rem;">
            <div style="height: 15rem;" data-simplebar data-simplebar-auto-hide="false">
              <?php if (empty($cart_items)) { ?>
                <div class="text-center py-5">
                  <i class="ci-cart fs-3 text-muted"></i>
                  <p class="mb-0"><?= __('cart_empty') ?></p>
                  <small class="text-muted"><?= __('start_shopping') ?></small>
                  <a class="btn btn-outline-primary btn-sm d-block mt-3" href="shop.php"><?= __('shop_now') ?></a>
                </div>
              <?php } else { ?>
                <?php foreach ($cart_items as $item): ?>
                  <div class="widget-cart-item pb-2 border-bottom">
                    <a class="btn-close text-danger" href="cart-process-remove.php?id=<?= $item['pro']['id'] ?>" aria-label="<?= __('remove') ?>">&times;</a>
                    <div class="d-flex align-items-center">
                      <a class="flex-shrink-0" href="product.php?id=<?= $item['pro']['id'] ?>">
                        <img src="<?= get_product_thumb($item['pro']['photos']) ?>" width="64" alt="Product">
                      </a>
                      <div class="ps-2">
                        <h6 class="widget-product-title"><?= $item['pro']['name'] ?></h6>
                        <div class="widget-product-meta">
                          <span class="text-accent me-2"><?= convert_price($item['pro']['price']) ?></span>
                          <span class="text-muted">x <?= $item['quantity'] ?></span>
                        </div>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php } ?>
            </div>
            <div class="d-flex flex-wrap justify-content-between align-items-center py-3">
              <div class="fs-sm me-2 py-2">
                <span class="text-muted"><?= __('subtotal') ?></span>
                <span class="text-accent fs-base ms-1"><?= convert_price($cart_total) ?></span>
              </div>
              <a class="btn btn-outline-secondary btn-sm" href="cart.php"><?= __('expand_cart') ?><i class="ci-arrow-right ms-1 me-n1"></i></a>
            </div>
            <a class="btn btn-primary btn-sm d-block w-100" href="checkout.php">
              <i class="ci-card me-2 fs-base align-middle"></i><?= __('checkout') ?>
            </a>
           </div>
          </div>
        </div>
        </div>
        </div>
                
        <div class="navbar navbar-expand-lg navbar-dark bg-black navbar-stuck-menu mt-n2 pt-0 pb-2">
          <div class="container">
            <!-- Menú Principal -->
            <div class="collapse d-lg-block" id="navbarCollapse">
              <ul class="navbar-nav">
                  <li class="nav-item dropdown"><a class="nav-link" href="index.php"><?= __('home') ?></a>
                  </li>
                  <li class="nav-item dropdown"><a class="nav-link" href="shop.php"><?= __('shop') ?></a>
                  </li>
                  <li class="nav-item dropdown"><a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside"><?= __('account') ?></a>
                    <ul class="dropdown-menu">
                      <li class="dropdown"><a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown"><?= __('shop_user_account') ?></a>
                        <ul class="dropdown-menu">
                          <li><a class="dropdown-item" href="account-orders.php"><?= __('orders_history') ?></a></li>
                          <li><a class="dropdown-item" href="account-settings.php"><?= __('account_settings') ?></a></li>
                        </ul>
                      </li>
                      <li><a class="dropdown-item" href="login.php"><?= __('sign_in_up') ?></a>
                      </li>
                    </ul>
                  </li>
              </ul>
            </div>              
            <div class="topbar-text text-nowrap d-none d-md-inline-block ms-3">
                  <i class="ci-location me-1"></i>
                  <a id="geo-link" class="topbar-link" href="#" target="_blank"></a>
            </div> 
          </div>
        </div>
      </header>

    <?php
    if(isset($_SESSION['alert'])){
    ?>
    
    <div class="container pt-5">
        <div class="alert alert-<?= $_SESSION['alert']['type'] ?>">
            <?= $_SESSION['alert']['message'] ?>
        </div>
    </div>

    <?php 
    unset($_SESSION['alert']);
    }
    ?>