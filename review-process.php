<?php
require_once('functions.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = (int)($_POST['product_id'] ?? 0);
    $user_id    = $_SESSION['user']['id'] ?? 0;
    $rating     = (int)($_POST['rating'] ?? 0);
    $comment    = trim($_POST['comment'] ?? '');

    if ($product_id > 0 && $user_id > 0 && $rating >= 1 && $rating <= 5) {
        $stmt = $conn->prepare("INSERT INTO reviews (product_id, user_id, rating, comment) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $product_id, $user_id, $rating, $comment);
        $stmt->execute();
        $stmt->close();

        alert('success', __('alert_review_added'));
    } else {
        alert('warning', __('alert_review_failed'));
    }

    header("Location: product.php?id=" . $product_id);
    exit;
}