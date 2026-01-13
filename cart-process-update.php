<?php
require_once('functions.php');

if (isset($_POST['quantities']) && is_array($_POST['quantities'])) {
    foreach ($_POST['quantities'] as $id => $qty) {
        $id = (int)$id;
        $qty = max(1, (int)$qty);
        foreach ($_SESSION['cart'] as $key => $v) {
            if ($v['pro']['id'] == $id) {
                $_SESSION['cart'][$key]['quantity'] = $qty;
            }
        }
    }
    alert('success', __('alert_cart_updated'));
}

header('Location: cart.php');
exit;