<?php
require_once('functions.php');
protected_area();

$user_id = $_SESSION['user']['id'];
$product_id = intval($_GET['id'] ?? 0);

if ($product_id > 0) {
  $exists = db_select('wishlist', "user_id = $user_id AND product_id = $product_id");
  if (empty($exists)) {
    db_insert('wishlist', [
      'user_id' => $user_id,
      'product_id' => $product_id
    ]);
  }
}

header('Location: shop.php');
exit;
?>