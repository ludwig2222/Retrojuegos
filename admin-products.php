<?php
require_once('functions.php');
requireRole(['admin', 'webmaster']);
protected_area();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && empty($_POST['edit_mode'])) {
    $product_id = intval($_POST['id']);
    db_delete('products', "id = $product_id");
    header('Location: admin-products.php?deleted=1');
    exit;
}

$editing = isset($_GET['edit']) ? intval($_GET['edit']) : null;
$product = $editing ? db_select_one('products', "id = $editing") : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
    $product_id = intval($_POST['product_id']);

    if (!$product) {
        $product = db_select_one('products', "id = $product_id");
    }

    // Si name/price/description faltan, asume que sólo se pide eliminar una foto
    $name = $_POST['name'] ?? $product['name'];
    $price = isset($_POST['price']) ? floatval($_POST['price']) : $product['price'];
    $description = $_POST['description'] ?? $product['description'];

    // Carga y elimina opcionalmente fotos anteriores
    $existing_photos = get_product_photos($_POST['existing_photos']);
    $delete_indexes = $_POST['delete_photo'] ?? [];

    foreach ($delete_indexes as $i) {
        if (isset($existing_photos[$i])) {
            if (file_exists($existing_photos[$i]['src'])) {
                unlink($existing_photos[$i]['src']);
            }
            unset($existing_photos[$i]);
        }
    }

    $existing_photos = array_values($existing_photos);
    $new_photos = [];

    // Subida de nuevas fotos
    if (!empty($_FILES['photos']['name'][0])) {
        foreach ($_FILES['photos']['tmp_name'] as $index => $tmpName) {
            if ($_FILES['photos']['error'][$index] === UPLOAD_ERR_OK) {
                $filename = uniqid() . '_' . basename($_FILES['photos']['name'][$index]);
                $targetPath = 'uploads/' . $filename;
                move_uploaded_file($tmpName, $targetPath);

                $new_photos[] = [
                    'src' => $targetPath,
                    'thumb' => $targetPath
                ];
            }
        }
    }

    $all_photos = array_slice(array_merge($existing_photos, $new_photos), 0, 6);
    $photos_json = json_encode($all_photos);

    // Subida de vídeo
    $video_path = $product['video_path'] ?? null;
    if (isset($_FILES['video_file']) && $_FILES['video_file']['error'] === UPLOAD_ERR_OK) {
        $video_tmp = $_FILES['video_file']['tmp_name'];
        $video_name = basename($_FILES['video_file']['name']);
        $video_ext = pathinfo($video_name, PATHINFO_EXTENSION);

        $new_video_name = time() . '-' . rand(100000,999999) . '.' . $video_ext;
        $video_dest = 'uploads/videos/' . $new_video_name;

        if (move_uploaded_file($video_tmp, $video_dest)) {
            $video_path = $video_dest;
        }
    }

    db_update('products', [
        'name' => $name,
        'price' => $price,
        'description' => $description,
        'photos' => $photos_json,
        'video_path' => $video_path
    ], "id = $product_id");

    // Redireccionamiento
    if (isset($_POST['return_to_list'])) {
        header("Location: admin-products.php?updated=1");
    } else {
        header("Location: admin-products.php?edit=$product_id&updated=1");
    }
    exit;
}

$sort = $_GET['sort'] ?? 'newest';

    switch ($sort) {
      case 'price_asc':
        $order = 'price ASC';
        break;
      case 'price_desc':
        $order = 'price DESC';
        break;
      case 'name_asc':
        $order = 'name ASC';
        break;
      case 'name_desc':
        $order = 'name DESC';
        break;
      default:
        $order = 'id DESC'; // el más reciente
}

$products = db_select('products', "1 ORDER BY $order");

require_once('header.php');
?>

<div class="container d-lg-flex justify-content-between py-2 py-lg-3">
  <div class="order-lg-2 mb-3 mb-lg-0 pt-lg-2">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb breadcrumb-light flex-lg-nowrap justify-content-center justify-content-lg-start">
        <li class="breadcrumb-item"><a class="text-nowrap" href="index.php"><i class="ci-home"></i><?= __('breadcrumb_home') ?></a></li>
        <li class="breadcrumb-item text-nowrap"><a href="admin-dashboard.php"><?= __('breadcrumb_account') ?></a>
        </li>
        <li class="breadcrumb-item text-nowrap active" aria-current="page"><?= __('breadcrumb_products') ?></li>
      </ol>
    </nav>
  </div>
  <div class="order-lg-1 pe-lg-4 text-center text-lg-start">
    <h1 class="h3 text-light mb-0"><?= __('page_products') ?></h1>
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
         <!-- Sorting dropdown -->
          <form method="GET" class="d-flex align-items-center">
            <input type="hidden" name="q" value="<?= htmlspecialchars($search) ?>">
            <label for="sort" class="me-2 mb-0"><?= __('sort_by') ?>:</label>
            <select name="sort" id="sort" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
              <option value="newest" <?= ($_GET['sort'] ?? '') === 'newest' ? 'selected' : '' ?>><?= __('newest') ?></option>
              <option value="price_asc" <?= ($_GET['sort'] ?? '') === 'price_asc' ? 'selected' : '' ?>><?= __('price_asc') ?></option>
              <option value="price_desc" <?= ($_GET['sort'] ?? '') === 'price_desc' ? 'selected' : '' ?>><?= __('price_desc') ?></option>
              <option value="name_asc" <?= ($_GET['sort'] ?? '') === 'name_asc' ? 'selected' : '' ?>><?= __('name_asc') ?></option>
              <option value="name_desc" <?= ($_GET['sort'] ?? '') === 'name_desc' ? 'selected' : '' ?>><?= __('name_desc') ?></option>
            </select>
          </form>


          <?php if ($product): ?>
          <div class="bg-black rounded-3 p-4 mb-4">
            <h4 class="mb-3 text-white"><?= __('edit_product_title') ?></h4>

            <form method="post" enctype="multipart/form-data">
              <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
              <input type="hidden" name="existing_photos" value='<?= htmlspecialchars($product['photos']) ?>'>
              <input type="hidden" name="update_product" value="1">
              <input type="hidden" name="edit_mode" value="1">

              <div class="mb-3">
                <label class="form-label"><?= __('product_name_label') ?></label>
                <input class="form-control" type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
              </div>

              <div class="mb-3">
                <label class="form-label"><?= __('product_price_label') ?></label>
                <input class="form-control" type="number" step="0.01" name="price" value="<?= htmlspecialchars($product['price']) ?>" required>
              </div>

              <div class="mb-3">
                <label class="form-label"><?= __('product_description_label') ?></label>
                <textarea class="form-control" name="description" rows="4" required><?= htmlspecialchars($product['description']) ?></textarea>
              </div>

              <div class="mb-3">
                <label class="form-label"><?= __('upload_video_label') ?></label>
                <input class="form-control" type="file" name="video_file" accept="video/mp4,video/webm,video/ogg">
              </div>

              <?php $existing_photos = get_product_photos($product['photos']); ?>
              <div class="mb-3">
                <label class="form-label"><?= __('current_images_label') ?></label><br>
                <?php foreach ($existing_photos as $index => $img): ?>
                  <div style="display:inline-block; position:relative; margin:5px;">
                    <img src="<?= $img['thumb'] ?>" style="max-width:120px;" alt="Product Image">
                    <button type="submit" name="delete_photo[]" value="<?= $index ?>" class="btn btn-sm btn-danger" style="position:absolute; top:0; right:0;">×</button>
                  </div>
                <?php endforeach; ?>
              </div>

              <div class="mb-3">
                <label class="form-label"><?= __('upload_new_images_label') ?></label>
                <input class="form-control" type="file" name="photos[]" multiple accept="image/*">
              </div>

              <button class="btn btn-primary" type="submit" name="return_to_list" value="1"><?= __('update_product_button') ?></button>
              <a href="admin-products.php" class="btn btn-secondary ms-2"><?= __('cancel_button') ?></a>
            </form>
          </div>
          <?php endif; ?>

          <?php
          foreach ($products as $key => $pro) { ?>               
          <!-- Product-->
          <div class="d-block d-sm-flex align-items-center py-4 border-bottom"><a class="d-block mb-3 mb-sm-0 me-sm-4 ms-sm-0 mx-auto" href="product.php?id=<?= $pro['id'] ?>" style="width: 12.5rem;">
                  <img class="rounded-3" src="<?= get_product_thumb($pro['photos']); ?>" alt="Product"></a>
            <div class="text-center text-sm-start">
              <h3 class="h6 product-title mb-2"><a href="product.php?id=<?= $pro['id'] ?>"><?= $pro['name'] ?></a></h3>
              <div class="d-inline-block text-accent">$<?= $pro['price'] ?>.<small>00</small></div>
              <div class="d-flex justify-content-center justify-content-sm-start pt-3">

                  <!-- Download Button -->
                  <a class="btn bg-faded-accent btn-icon me-2" href="download.php?id=<?= $pro['id'] ?>" title="<?= __('download_button_title') ?>">
                    <i class="ci-download text-accent"></i>
                  </a>

                  <!-- Edit Button -->
                  <a class="btn bg-faded-info btn-icon me-2" href="admin-products.php?edit=<?= $pro['id'] ?>" title="<?= __('edit_button_title') ?>">
                    <i class="ci-edit text-info"></i>
                  </a>

                  <!-- Delete Button (with confirmation) -->
                  <form method="post" action="admin-products.php" onsubmit="return confirm('<?= __('pro_delete_confirm') ?>');" style="display:inline;">
                    <input type="hidden" name="id" value="<?= $pro['id'] ?>">
                    <button class="btn bg-faded-danger btn-icon" type="submit" title="<?= __('delete_button_title') ?>">
                      <i class="ci-trash text-danger"></i>
                    </button>
                  </form>


              </div>
            </div>
          </div>

          <?php } ?>

        </div>
      </section>
  </div>
</div>

<?php
require_once('footer.php');
?>
<!-- Bootstrap JS and Tooltip Script -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function (tooltipTriggerEl) {
      new bootstrap.Tooltip(tooltipTriggerEl);
    });
  });
</script>
</body>
</html>