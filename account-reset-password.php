<?php
require_once('header.php');

$message = '';
$showForm = false;

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $stmt = $conn->prepare("SELECT id, reset_expires FROM users WHERE reset_token=?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && strtotime($user['reset_expires']) > time()) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['password'])) {
            $newPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password=?, reset_token=NULL, reset_expires=NULL WHERE id=?");
            $stmt->bind_param("si", $newPassword, $user['id']);
            $stmt->execute();
            $message = "Password updated successfully.";
        } else {
            $showForm = true;
        }
    } else {
        $message = "Invalid or expired token.";
    }
}
?>

<div class="container py-4 my-4">
  <div class="row justify-content-center">
    <div class="col-lg-8 col-md-10">
      <h2 class="h3 mb-4 text-light"><?= __('password_reset_title') ?></h2>
      <?php if (!empty($message)): ?>
        <p class="text-info">
          <?php
          if ($message === "Password updated successfully.") {
              echo __('password_reset_success');
          } elseif ($message === "Invalid or expired token.") {
              echo __('password_reset_invalid');
          } else {
              echo htmlspecialchars($message);
          }
          ?>
        </p>
      <?php endif; ?>
      <?php if ($showForm): ?>
        <form method="post" class="card-body needs-validation bg-black" novalidate>
          <div class="mb-3">
            <label class="form-label" for="new-password"><?= __('password_reset_new_label') ?></label>
            <input class="form-control" type="password" id="new-password" name="password" required>
          </div>
          <button class="btn btn-primary" type="submit"><?= __('password_reset_button') ?></button>
        </form>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php
require_once('footer.php');
?>