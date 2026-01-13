<?php
session_start();
require_once('functions.php');

// Habilita informes de errores temporalmente
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Valida entrada
if (!isset($_POST['id']) || !isset($_POST['quantity'])) {
    die("Missing product ID or quantity.");
}

$id = (int)$_POST['id'];
$quantityToAdd = (int)$_POST['quantity'];

if ($id < 1 || $quantityToAdd < 1) {
    die("Invalid product ID or quantity.");
}

// Obtiene producto
$data = get_product($id);
if (!$data || !$data['pro']) {
    die("Product not found.");
}

$pro = $data['pro'];
$pro['quantity'] = $quantityToAdd;

// Agrega o actualiza carrito
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_SESSION['cart'][$id])) {
    $_SESSION['cart'][$id]['quantity'] += $quantityToAdd;
    $_SESSION['cart_quantity_increased'] = true;
} else {
    $_SESSION['cart'][$id] = [
        'pro' => $pro,
        'cat' => $data['cat'],
        'quantity' => $quantityToAdd
    ];
}

alert('success', __('alert_product_added'));
header('Location: shop.php?id=' . $id);
exit;