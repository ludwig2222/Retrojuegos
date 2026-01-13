<?php
require_once('functions.php');
protected_area();

$id = intval($_GET['id'] ?? 0);
$product = db_select_one('products', "id = $id");

if ($product && file_exists($product['file_path'])) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($product['file_path']) . '"');
    readfile($product['file_path']);
    exit;
} else {
    echo "File not found.";
}
?>
