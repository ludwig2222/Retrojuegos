<?php
require_once ('header.php');
?>


<div class="container py-4 py-lg-5 my-4">
        <div class="row">
          <div class="col-md-6">
            <div class="card border-0 shadow">
             <div class="card-body bg-black">  
              <form action="login-logic.php" method="post">               
                <h2 class="h4 mb-1 text-white"><?= __('sign_in_title') ?></h2>
                <div class="py-3">
                  <h3 class="d-inline-block align-middle fs-base fw-medium mb-2 me-2 text-muted"><?= __('sign_in_social') ?></h3>
                  <div class="d-inline-block align-middle"><a class="btn-social bs-google me-2 mb-2" href="#" data-bs-toggle="tooltip" title="<?= __('sign_in_google') ?>"><i class="ci-google"></i></a><a class="btn-social bs-facebook me-2 mb-2" href="#" data-bs-toggle="tooltip" title="Sign in with Facebook"><i class="ci-facebook"></i></a><a class="btn-social bs-twitter me-2 mb-2" href="#" data-bs-toggle="tooltip" title="Sign in with Twitter"><i class="ci-twitter"></i></a></div>
                </div>
                <hr>
                <h3 class="fs-base pt-4 pb-2 text-muted"><?= __('sign_in_form_intro') ?></h3>
                  <div class="input-group mb-3"><i class="ci-mail position-absolute top-50 translate-middle-y text-muted fs-base ms-3"></i>
                    <input class="form-control rounded-start text-white" name="email" type="email" placeholder="<?= __('email_placeholder') ?>" required>
                  </div>
                  <div class="input-group mb-3"><i class="ci-locked position-absolute top-50 translate-middle-y text-muted fs-base ms-3"></i>
                    <div class="password-toggle w-100">
                      <input name="password" class="form-control text-white" type="password" placeholder="<?= __('password_placeholder') ?>" required>
                      <label class="password-toggle-btn" aria-label="Show/hide password">
                        <input class="password-toggle-check" type="checkbox"><span class="password-toggle-indicator"></span>
                      </label>
                    </div>
                  </div>
                  <div class="d-flex flex-wrap justify-content-between">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" checked id="remember_me">
                      <label class="form-check-label" for="remember_me"><?= __('remember_me') ?></label>
                    </div><a class="nav-link-inline fs-sm" href="account-password-recovery.php"><?= __('forgot_password') ?></a>
                  </div>
                  <hr class="mt-4">
                  <div class="text-end pt-4">
                    <button class="btn btn-primary" type="submit"><i class="ci-sign-in me-2 ms-n21"></i><?= __('sign_in_button') ?></button>
                  </div>
                </form>
              </div>
            </div>
          </div>
          <div class="col-md-6 pt-4 mt-3 mt-md-0">
            <h2 class="h4 mb-3 text-white"><?= __('sign_up_title') ?></h2>
            <p class="fs-sm text-muted mb-4"><?= __('sign_up_intro') ?></p>
            <form action="register-logic.php" method="post" class="needs-validation" novalidate>
              <div class="row gx-4 gy-3">  
                <div class="col-sm-6">
                  <label class="form-label" for="reg-fn"><?= __('first_name_label') ?></label>
                  <input class="form-control text-white" name="first_name" type="text" required id="reg-fn">
                  <div class="invalid-feedback"><?= __('first_name_error') ?></div>
                </div>
                <div class="col-sm-6">
                  <label class="form-label" for="reg-ln"><?= __('last_name_label') ?></label>
                  <input class="form-control text-white" name="last_name" type="text" required id="reg-ln">
                  <div class="invalid-feedback"><?= __('last_name_error') ?></div>
                </div>
                <div class="col-sm-6">
                  <label class="form-label" for="reg-email"><?= __('email_label') ?></label>
                  <input class="form-control text-white" name="email" type="email" required id="reg-email">
                  <div class="invalid-feedback"><?= __('email_error') ?></div>
                </div>
                <div class="col-sm-6">
                  <label class="form-label" for="reg-phone"><?= __('phone_label') ?></label>
                  <input class="form-control text-white" name="phone_number" type="text" required id="reg-phone">
                  <div class="invalid-feedback"><?= __('phone_error') ?></div>
                </div>
                <div class="col-sm-6">
                  <label class="form-label" for="reg-password"><?= __('password_label') ?></label>
                  <input class="form-control text-white" name="password" type="password" required id="reg-password">
                  <div class="invalid-feedback"><?= __('password_error') ?></div>
                </div>
                <div class="col-sm-6">
                  <label class="form-label" for="reg-password-confirm"><?= __('confirm_password_label') ?></label>
                  <input class="form-control text-white" name="password_1" type="password" required id="reg-password-confirm">
                  <div class="invalid-feedback"><?= __('confirm_password_error') ?></div>
                </div>
                <div class="col-12 text-end">
                  <button class="btn btn-primary" type="submit"><i class="ci-user me-2 ms-n1"></i><?= __('sign_up_button') ?></button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>


<?php
require_once ('footer.php');
?>   