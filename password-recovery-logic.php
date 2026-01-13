<?php
require_once('header.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['email'])) {
    $email = trim($_POST['email']);

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        // Genera token
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+30 minutes'));

        // Guarda token temporal
        $stmt = $conn->prepare("UPDATE users SET reset_token=?, reset_expires=? WHERE id=?");
        $stmt->bind_param("ssi", $token, $expires, $user['id']);
        $stmt->execute();

        // Envía correo con el 'reset link'
        $resetLink = "http://localhost/Retrojuegos/account-reset-password.php?token=$token";
        mail($email, "Password Reset", "Click here to reset your password: $resetLink");
    }

    echo "<div class='container py-4'><p class='text-info text-light'>" . __('password_reset_info') . "</p></div>";
}

require_once('footer.php');
?>