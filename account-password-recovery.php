<?php
require_once ('header.php');

?>

<div class="container py-4 py-lg-5 my-4">
    <div class="row justify-content-center">
      <div class="col-lg-8 col-md-10">
        <h2 class="h3 mb-4 text-light"><?= __('password_recovery_title') ?></h2>
        <p class="fs-md"><?= __('password_recovery_intro') ?></p>
        <ol class="list-unstyled fs-md">
          <li><span class="text-primary me-2">1.</span><?= __('password_recovery_step1') ?></li>
          <li><span class="text-primary me-2">2.</span><?= __('password_recovery_step2') ?></li>
          <li><span class="text-primary me-2">3.</span><?= __('password_recovery_step3') ?></li>
        </ol>
        <div class="card py-2 mt-4 bg-black">
          <form class="card-body needs-validation" action="password-recovery-logic.php" method="post" novalidate>
            <div class="mb-3">
              <label class="form-label" for="recover-email"><?= __('password_recovery_email_label') ?></label>
              <input class="form-control" name="email" type="email" id="recover-email" required>
              <div class="invalid-feedback"><?= __('password_recovery_email_invalid') ?></div>
            </div>
            <button class="btn btn-primary" type="submit"><?= __('password_recovery_button') ?></button>
          </form>
        </div>
      </div>
    </div>
</div>
<?php
require_once ('footer.php');
?>   