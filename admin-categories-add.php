<?php
require_once('functions.php');
requireRole(['admin', 'webmaster']);

protected_area();

$rows = db_select('categories','parent_id = 0');
$categories = [];
$categories[0] = 'No parent';
foreach ($rows as $val) {
    $categories[$val['id']] = $val['name'];
}


if($_SERVER['REQUEST_METHOD'] == 'POST'){
   $_SESSION['form']['value'] = $_POST;
    
    $imgs = upload_images($_FILES['photo']);

    if ($imgs === false) {
    alert('danger', $_SESSION['upload_error'] ?? 'Image upload failed.');
    header('Location: admin-categories-add.php');
    exit;
    }

    $data['name'] = $_POST['name'];
    $data['parent_id'] = (int)($_POST['parent_id']);  
    $data['photo'] = json_encode($imgs);
    $data['description'] = $_POST['description'];

    if (db_insert('categories', $data)) {
        alert('success', 'Created category successfully.');
        header('Location: admin-categories.php');
        unset($_SESSION['form']);
    } else {
        alert('danger', 'Failed to create category, please try again.');
        header('Location: admin-categories-add.php');
    }
    die();
}

require_once('header.php')
?>

<div class="container d-lg-flex justify-content-between py-2 py-lg-3">
  <div class="order-lg-2 mb-3 mb-lg-0 pt-lg-2">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb breadcrumb-light flex-lg-nowrap justify-content-center justify-content-lg-start">
        <li class="breadcrumb-item"><a class="text-nowrap" href="index.php"><i class="ci-home"></i><?= __('breadcrumb_home') ?></a></li>
        <li class="breadcrumb-item text-nowrap"><a href="admin-dashboard.php"><?= __('breadcrumb_account') ?></a>
        </li>
        <li class="breadcrumb-item text-nowrap active" aria-current="page"><?= __('breadcrumb_add_category') ?></li>
      </ol>
    </nav>
  </div>
  <div class="order-lg-1 pe-lg-4 text-center text-lg-start">
    <h1 class="h3 text-light mb-0"><?= __('page_add_category') ?></h1>
  </div>
</div>

<div class="container pb-5 mb-2 mb-md-4">
  <div class="row">
    <aside class="col-lg-4 pt-4 pt-lg-0 pe-xl-5">
      <div class="bg-black rounded-3 shadow-lg pt-1 mb-5 mb-lg-0">
        <div class="d-md-flex justify-content-between align-items-center text-center text-md-start p-4">
          <div class="d-md-flex align-items-center">
            <div class="img-thumbnail rounded-circle position-relative flex-shrink-0 mx-auto mb-2 mx-md-0 mb-md-0" style="width: 6.375rem;"><img class="rounded-circle" src="img/shop/account/user.png" alt=""></div>
            <div class="ps-md-3">
              <h3 class="fs-base mb-0 text-white"><?= $_SESSION['user']['first_name'] ?></h3><span class="text-accent fs-sm"><?= $_SESSION['user']['email'] ?></span>
            </div>
          </div><a class="btn btn-primary d-lg-none mb-2 mt-3 mt-md-0" href="#account-menu" data-bs-toggle="collapse" aria-expanded="false"><i class="ci-menu me-2"></i><?= __('account_menu') ?></a>
        </div>
        <div class="d-lg-block collapse" id="account-menu">
          <div class="bg-darker px-4 py-3">
            <h3 class="fs-sm mb-0 text-white"><?= __('admin_dashboard') ?></h3>
          </div>
          <ul class="list-unstyled mb-0">
            <li class="border-bottom mb-0"><a class="nav-link-style d-flex align-items-center px-4 py-3" href="admin-products.php"><i class="ci-user opacity-60 me-2"></i><?= __('admin_products') ?></a></li>
            <li class="border-bottom mb-0"><a class="nav-link-style d-flex align-items-center px-4 py-3" href="admin-products-add.php"><i class="ci-user opacity-60 me-2"></i><?= __('admin_create_product') ?></a></li>
            <li class="border-bottom mb-0"><a class="nav-link-style d-flex align-items-center px-4 py-3" href="admin-categories.php"><i class="ci-user opacity-60 me-2"></i><?= __('admin_categories') ?></a></li>
            <li class="border-bottom mb-0"><a class="nav-link-style d-flex align-items-center px-4 py-3" href="admin-categories-add.php"><i class="ci-user opacity-60 me-2"></i><?= __('admin_create_category') ?></a></li>
            <li class="border-bottom mb-0"><a class="nav-link-style d-flex align-items-center px-4 py-3" href="admin-statistics.php"><i class="ci-user opacity-60 me-2"></i><?= __('admin_statistics') ?></a></li>
            <?php
            $userType = $_SESSION['user']['user_type'] ?? 'customer';
            if ($userType === 'admin') {
            ?>
            <li class="border-bottom mb-0"><a class="nav-link-style d-flex align-items-center px-4 py-3" href="admin-user-management.php"><i class="ci-user opacity-60 me-2"></i><?= __('admin_user_management') ?></a>
            </li>
            <?php } ?>
            <li class="border-top mb-0"><a class="nav-link-style d-flex align-items-center px-4 py-3" href="logout.php"><i class="ci-sign-out opacity-60 me-2"></i><?= __('sign_out') ?></a></li>
          </ul>
        </div>
      </div>
    </aside>
    <!-- Content  -->
    <section class="col-lg-8 pt-lg-4 pb-4 mb-3">
        <div class="pt-2 px-4 ps-lg-0 pe-xl-5">
          <form action="admin-categories-add.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3 pb-2">

              <?= text_input([
                  'name' => 'name',
                  'label' => __('category_name_label')
              ], []) ?>
              <div class="row mt-4">
                  <div class="col-6">
                      <div class="form-group">
                          <?= select_input([
                              'name' => 'parent_id',
                              'label' => __('parent_category_label')       
                          ], $categories) ?>
                      </div> 
                  </div>
                  <div class="col-md-6">
                      <div class="form-group">
                          <label for="photo"><?= __('category_image_label') ?></label>
                          <input class="form-control" name="photo" id="photo" type="file" accept=".jpg,.jpeg,.png,.gif">      
                      </div> 
                  </div>  
              </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="form-group">
                          <label for="description"><?= __('category_description_label') ?></label>
                          <textarea name="description" id="description" class="form-control"></textarea>
                        </div>
                    </div>                              
                </div>  


              <!-- description -->  

            </div>

            <button class="btn btn-primary d-block w-100" type="submit" name="submit"><i class="ci-cloud-upload fs-lg me-2"></i><?= __('submit_button') ?></button>
          </form>
        </div>
      </section>
  </div>
</div>


<?php
require_once('footer.php')
?>