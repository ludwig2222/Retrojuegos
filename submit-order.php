<?php
require_once('functions.php');

$user = $_SESSION['user'];

$total = 0;
foreach ($_SESSION['cart'] as $key => $val) {
    $total += $val['quantity'] * $val['pro']['price'];
}

db_insert(
    'orders',
        [
            'customer_id' => $user['id'],
            'order_status' => 1,
            'shipping' => json_encode($_SESSION['shipping']),
            'cart' => json_encode($_SESSION['cart']),
            'user' => json_encode($_SESSION['user']),
            'order_date' => time(),
            'total_price' => $total,
        ]
);

$_SESSION['shipping'] = null;
unset($_SESSION['cart']);
unset($_SESSION['shipping']);
header('Location: checkout-complete.php')
?>
