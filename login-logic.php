<?php
require_once('functions.php');
session_start();

$email = trim($_POST['email']);
$password = trim($_POST['password']);

if (login_user($email, $password)) {
    alert('success', __('login_success'));

    // Se coge el "user_type" de la "session"
    $userType = $_SESSION['user']['user_type'] ?? 'customer';

    // Redirecciona en base a su rol
    switch ($userType) {
        case 'admin':
            header('Location: admin-dashboard.php');
            break;
        case 'webmaster':
            header('Location: webmaster-dashboard.php');
            break;
        case 'customer':
        default:
            header('Location: customer-dashboard.php');
            break;
    }
    die();
} else {
    alert('danger', __('login_error'));
    header('Location: login.php');
    die();
}
?>