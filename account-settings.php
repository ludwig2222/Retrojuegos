<?php
require_once('functions.php');
protected_area();

$can_update_profile = true;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = new mysqli('localhost', 'root', '', 'retrojuegos');
    $user_id = $_SESSION['user']['id'];

    // Maneja la actualización de contraseña primero
    if (!empty($_POST['new_password']) || !empty($_POST['confirm_password'])) {
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if ($new_password === $confirm_password) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $query = "UPDATE users SET password = ? WHERE id = ?";
            $stmt2 = $conn->prepare($query);
            $stmt2->bind_param("si", $hashed_password, $user_id);
            if ($stmt2->execute()) {
                $success_message = "Password updated successfully!";
            } else {
                $error_message = "Failed to update password.";
                $can_update_profile = false;
            }
            $stmt2->close();
        } else {
            $error_message = "Passwords do not match.";
            $can_update_profile = false;
        }
    }

    // Ahora maneja la actualización del perfil
    if ($can_update_profile) {
        $first_name = trim($_POST['first_name']);
        $last_name = trim($_POST['last_name']);
        $phone_number = trim($_POST['phone_number']);
        $address = trim($_POST['address']);

        $query = "UPDATE users SET first_name = ?, last_name = ?, phone_number = ?, address = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssi", $first_name, $last_name, $phone_number, $address, $user_id);

        if ($stmt->execute()) {
            $_SESSION['user']['first_name'] = $first_name;
            $_SESSION['user']['last_name'] = $last_name;
            $_SESSION['user']['phone_number'] = $phone_number;
            $_SESSION['user']['address'] = $address;

            if (!isset($success_message)) {
                $success_message = "Profile updated successfully!";
            }
        } else {
            if (!isset($error_message)) {
                $error_message = "Something went wrong. Please try again.";
            }
        }

        $stmt->close();
    }

    
    $conn->close();
}

//$user_id = $_SESSION['user']['id'];


require_once('header.php');
?>

<div class="container d-lg-flex justify-content-between py-2 py-lg-3">
<div class="order-lg-2 mb-3 mb-lg-0 pt-lg-2">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb breadcrumb-dark flex-lg-nowrap justify-content-center justify-content-lg-start">
        <li class="breadcrumb-item"><a class="text-nowrap" href="index.php"><i class="ci-home"></i><?= __('breadcrumb_home') ?></a></li>
        <li class="breadcrumb-item text-nowrap"><a href="customer-dashboard.php"><?= __('breadcrumb_account') ?></a></li>
        <li class="breadcrumb-item text-nowrap active" aria-current="page"><?= __('settings') ?></li>
      </ol>
    </nav>
</div>
    <div class="order-lg-1 pe-lg-4 text-center text-lg-start">
      <h1 class="h3 text-white mb-0"><?= __('settings') ?></h1>
    </div>

</div>
    <div class="container pb-5 mb-2 mb-md-4">
      <div class="row">
        <aside class="col-lg-4 pt-4 pt-lg-0 pe-xl-5">
            <div class="bg-black rounded-3 shadow-lg pt-1 mb-5 mb-lg-0">
              <div class="d-md-flex justify-content-between align-items-center text-center text-md-start p-4">
                <div class="d-md-flex align-items-center">
                  <div class="img-thumbnail rounded-circle position-relative flex-shrink-0 mx-auto mb-2 mx-md-0 mb-md-0" style="width: 6.375rem;"><img class="rounded-circle" src="img/shop/account/user.png" alt="Susan Gardner"></div>
                  <div class="ps-md-3">
                    <h3 class="fs-base mb-0 text-white"><?= $_SESSION['user']['first_name'] ?></h3><span class="text-accent fs-sm"><?= $_SESSION['user']['email'] ?></span>
                  </div>
                </div><a class="btn btn-primary d-lg-none mb-2 mt-3 mt-md-0" href="#account-menu" data-bs-toggle="collapse" aria-expanded="false"><i class="ci-menu me-2"></i><?= __('account_menu') ?></a>
              </div>
              <div class="d-lg-block collapse" id="account-menu">
                <div class="bg-darker px-4 py-3">
                  <h3 class="fs-sm mb-0 text-white"><?= __('customer_dashboard') ?></h3>
                </div>
                <ul class="list-unstyled mb-0">
                  <li class="border-bottom mb-0"><a class="nav-link-style d-flex align-items-center px-4 py-3" href="account-orders.php"><i class="ci-user opacity-60 me-2"></i><?= __('my_orders') ?></a></li>
                  <li class="border-bottom mb-0"><a class="nav-link-style d-flex align-items-center px-4 py-3" href="account-wishlist.php"><i class="ci-user opacity-60 me-2"></i><?= __('my_wishlist') ?></a></li>
                  <li class="border-bottom mb-0"><a class="nav-link-style d-flex align-items-center px-4 py-3" href="account-settings.php"><i class="ci-user opacity-60 me-2"></i><?= __('settings') ?></a></li>
                  <li class="border-top mb-0"><a class="nav-link-style d-flex align-items-center px-4 py-3" href="logout.php"><i class="ci-sign-out opacity-60 me-2"></i><?= __('sign_out') ?></a></li>
                </ul>
              </div>
            </div>
          </aside>
        <!-- Content  -->
          <section class="col-lg-8">
              <?php if (isset($success_message)): ?>
                <div class="alert alert-success"><?= $success_message ?></div>
              <?php elseif (isset($error_message)): ?>
                <div class="alert alert-danger"><?= $error_message ?></div>
              <?php endif; ?>
            <!-- Profile form-->
            <form method="POST" action="">
              <div class="row gx-4 gy-3">
                <div class="col-sm-6">
                  <label class="form-label" for="account-fn"><?= __('first_name_label') ?></label>
                  <input class="form-control" type="text" id="account-fn" name="first_name" value="<?= htmlspecialchars($_SESSION['user']['first_name']) ?>">
                </div>
                <div class="col-sm-6">
                  <label class="form-label" for="account-ln"><?= __('last_name_label') ?></label>
                  <input class="form-control" type="text" id="account-ln" name="last_name" value="<?= htmlspecialchars($_SESSION['user']['last_name']) ?>">
                </div>
                <div class="col-sm-6">
                  <label class="form-label" for="account-email"><?= __('email_label') ?></label>
                  <input class="form-control" type="email" id="account-email" value="<?= htmlspecialchars($_SESSION['user']['email']) ?>" disabled>
                </div>
                <div class="col-sm-6">
                  <label class="form-label" for="account-phone"><?= __('phone_label') ?></label>
                  <input class="form-control" type="text" id="account-phone" name="phone_number" value="<?= htmlspecialchars($_SESSION['user']['phone_number']) ?>" required>
                </div>
                <div class="col-sm-6">
                  <label class="form-label" for="account-address"><?= __('address_label') ?></label>
                  <input class="form-control" type="text" id="account-adress" name="address" value="<?= htmlspecialchars($_SESSION['user']['address']) ?>" required>
                </div>
                <div class="col-sm-6">
                  <label class="form-label" for="account-address"><?= __('payment_method_label') ?></label>
                  <div class="d-flex align-items-center"><img src="img/card-visa.png" width="39" alt="Visa">
                        <div class="ps-2"><span class="fw-medium text-heading me-1">Visa</span><?= sprintf(__('card_ending_in'), '4999') ?><span class="align-middle badge bg-info ms-2"><?= __('primary_badge') ?></span></div>
                      </div>
                </div>
                <div class="col-sm-6">
                  <label class="form-label" for="account-pass"><?= __('new_password_label') ?></label>
                  <div class="password-toggle">
                    <input class="form-control" type="password" id="account-pass" name="new_password">
                    <label class="password-toggle-btn" aria-label="Show/hide password">
                      <input class="password-toggle-check" type="checkbox"><span class="password-toggle-indicator"></span>
                    </label>
                  </div>
                </div>
                <div class="col-sm-6">
                  <label class="form-label" for="account-confirm-pass"><?= __('confirm_password_label') ?></label>
                  <div class="password-toggle">
                    <input class="form-control" type="password" id="account-confirm-pass" name="confirm_password">
                    <label class="password-toggle-btn" aria-label="Show/hide password">
                      <input class="password-toggle-check" type="checkbox"><span class="password-toggle-indicator"></span>
                    </label>
                  </div>
                </div>
                <div class="col-12">
                  <hr class="mt-2 mb-3">
                  <div class="d-flex flex-wrap justify-content-between align-items-center">
                    <button class="btn btn-primary mt-3 mt-sm-0" type="submit"><?= __('update_profile_button') ?></button>
                  </div>
                </div>
              </div>
            </form>
          </section>
      </div>
    </div>
    
<?php 
require_once('footer.php'); 
?>