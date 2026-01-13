<?php
require_once('functions.php');

$id = (int)($_GET['id']);

if(isset($_SESSION['cart'])){
    foreach ($_SESSION['cart'] as $key => $v){
        if($v['pro']['id'] == $id){
            unset($_SESSION['cart'][$key]);
        }
    }
}

alert('success', __('alert_product_removed'));
header('Location: shop.php');