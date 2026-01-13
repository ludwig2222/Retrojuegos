<?php
require_once('functions.php');
requireRole(['admin', 'webmaster']);

protected_area();

$conn = new mysqli('localhost', 'root', '', 'retrojuegos');

// Eliminar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && !isset($_POST['name'])) {
    $id = intval($_POST['id']);
    $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: admin-categories.php");
    exit;
}

// Actualizar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
    $id = intval($_POST['id']);
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $parent_id = $_POST['parent_id'] !== '' ? intval($_POST['parent_id']) : null;
    $photo = trim($_POST['photo']);

    $stmt = $conn->prepare("UPDATE categories SET name = ?, description = ?, parent_id = ?, photo = ? WHERE id = ?");
    $stmt->bind_param("ssisi", $name, $description, $parent_id, $photo, $id);
    $stmt->execute();
    header("Location: admin-categories.php");
    exit;
}

// Extraer categorías
$categories = db_select('categories', '1 ORDER BY id DESC');
// Para ordenarlas alfabéticamente
usort($categories, function ($a, $b) {
    return strcmp(__($a['name']), __($b['name']));
});



// Obtener una sola categoría para editar
$edit_category = null;
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_category = $result->fetch_assoc();
}

//$categories = db_select('categories', ' 1 ORDER BY id DESC ');

require_once('header.php')
?>
<div class="container d-lg-flex justify-content-between py-2 py-lg-3">
  <div class="order-lg-2 mb-3 mb-lg-0 pt-lg-2">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb breadcrumb-light flex-lg-nowrap justify-content-center justify-content-lg-start">
        <li class="breadcrumb-item"><a class="text-nowrap" href="index.php"><i class="ci-home"></i><?= __('breadcrumb_home') ?></a></li>
        <li class="breadcrumb-item text-nowrap"><a href="admin-dashboard.php"><?= __('breadcrumb_account') ?></a>
        </li>
        <li class="breadcrumb-item text-nowrap active" aria-current="page"><?= __('breadcrumb_categories') ?></li>
      </ol>
    </nav>
  </div>
  <div class="order-lg-1 pe-lg-4 text-center text-lg-start">
    <h1 class="h3 text-light mb-0"><?= __('page_product_categories') ?></h1>
  </div>
</div>
<div class="container pb-5 mb-2 mb-md-4">
    <div class="row">
       <aside class="col-lg-4 pt-4 pt-lg-0 pe-xl-5">
         <div class="bg-black rounded-3 shadow-lg pt-1 mb-5 mb-lg-0">
           <div class="d-md-flex justify-content-between align-items-center text-center text-md-start p-4">
             <div class="d-md-flex align-items-center">
               <div class="img-thumbnail rounded-circle position-relative flex-shrink-0 mx-auto mb-2 mx-md-0 mb-md-0" style="width: 6.375rem;">
                   <img class="rounded-circle" src="img/shop/account/user.png" alt="Susan Gardner">
               </div>
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
             <?php if ($edit_category): ?>
                 <div class="card mb-4">
                   <div class="card-header bg-black"><?= sprintf(__('edit_category_title'), $edit_category['name']) ?></div>
                   <div class="card-body bg-black">
                     <form method="POST" action="admin-categories.php">
                       <input type="hidden" name="id" value="<?= $edit_category['id'] ?>">

                       <div class="mb-3">
                         <label class="form-label"><?= __('category_name_label') ?></label>
                         <input class="form-control" type="text" name="name" value="<?= htmlspecialchars($edit_category['name']) ?>" required>
                       </div>

                       <div class="mb-3">
                         <label class="form-label"><?= __('category_description_label') ?></label>
                         <textarea class="form-control" name="description"><?= htmlspecialchars($edit_category['description']) ?></textarea>
                       </div>

                       <div class="mb-3">
                         <label class="form-label"><?= __('category_parent_id_label') ?></label>
                         <input class="form-control" type="number" name="parent_id" value="<?= htmlspecialchars($edit_category['parent_id']) ?>">
                       </div>

                       <div class="mb-3">
                         <label class="form-label"><?= __('category_photo_label') ?></label>
                         <input class="form-control" type="text" name="photo" value="<?= htmlspecialchars($edit_category['photo']) ?>">
                       </div>

                       <button class="btn btn-primary" type="submit"><?= __('update_category_button') ?></button>
                       <a class="btn btn-secondary" href="admin-categories.php"><?= __('cancel_button') ?></a>
                     </form>
                   </div>
                 </div>
             <?php endif; ?>

             <?php foreach ($categories as $key => $value) { ?>
              <div class="d-block d-sm-flex align-items-center py-4 border-bottom">
                   <!-- Image on the left -->
                   <div class="me-sm-4 flex-shrink-0 image-container">
                     <img class="category-thumb" src="<?= htmlspecialchars(get_product_thumb($value['photo'])) ?>" alt="<?= htmlspecialchars($value['name']) ?>">
                   </div>

                   <!-- Text/content on the right -->
                   <div class="text-center text-sm-start">
                     <h3 class="h6 product-title mb-2 text-white">
                       <?= htmlspecialchars(__($value['name'])) ?>
                     </h3>
                     <p class="fs-sm text-white mb-0"><?= htmlspecialchars(__($value['description'])) ?></p>

                     <!-- Action buttons -->
                     <div class="d-flex justify-content-center justify-content-sm-start pt-3">
                       <a class="btn bg-faded-info btn-icon me-2" href="admin-categories.php?id=<?= $value['id'] ?>" type="button" title="<?= __('edit_button_title') ?>">
                         <i class="ci-edit text-info"></i>
                       </a>
                       <form method="post" action="admin-categories.php" onsubmit="return confirm('<?= __('cat_delete_confirm') ?>');" style="display:inline;">
                       <input type="hidden" name="id" value="<?= $value['id'] ?>">
                       <button class="btn bg-faded-danger btn-icon" type="submit" title="<?= __('delete_button_title') ?>">
                         <i class="ci-trash text-danger"></i>
                       </button>
                       </form>
                     </div>
                   </div>
               </div>
             <?php } ?>
         </section>
     </div>
</div>

<?php
require_once('footer.php')
?>