<?php
require_once('functions.php');
requireRole(['admin']);

protected_area();

$conn = new mysqli('localhost', 'root', '', 'retrojuegos');

// Borrado de cliente
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    $user_id = intval($_POST['delete_user']);
    db_delete('users', "id = $user_id");
    header("Location: admin-user-management.php?deleted=1");
    exit;
}

// Borrado de reseñas
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_review'])) {
    $review_id = intval($_POST['delete_review']);
    $user_id = intval($_POST['user_id']); // nos quedamos en el usuario de la reseña

    db_delete('reviews', "id = $review_id");
    alert('success', __('review_deleted_success'));

    header("Location: admin-user-management.php?edit=$user_id&review_deleted=1");
    exit;
}

$editing_user = isset($_GET['edit']) ? intval($_GET['edit']) : null;
$user_to_edit = $editing_user ? db_select_one('users', "id = $editing_user") : null;

$orders = [];
$reviews = [];

if ($user_to_edit) {
    // Cogemos sus pedidos
    $orders_result = $conn->query(
            "SELECT * FROM orders WHERE customer_id = " 
            . intval($user_to_edit['id']) 
            . " ORDER BY order_date DESC");
    if ($orders_result && $orders_result->num_rows > 0) {
        while ($row = $orders_result->fetch_assoc()) {
            $orders[] = $row;
        }
    }

    // Cogemos sus valoraciones/reseñas-reviews
    $reviews_result = $conn->query(
    "SELECT reviews.id,
            reviews.product_id,
            products.name AS product_name,
            reviews.rating,
            reviews.comment,
            reviews.created_at
     FROM reviews
     INNER JOIN products ON reviews.product_id = products.id
     WHERE reviews.user_id = " . intval($user_to_edit['id']) . "
     ORDER BY reviews.created_at DESC"
    );
    if ($reviews_result && $reviews_result->num_rows > 0) {
        while ($row = $reviews_result->fetch_assoc()) {
            $reviews[] = $row;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $user_id = intval($_POST['user_id']);
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone_number']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $user_type = mysqli_real_escape_string($conn, $_POST['user_type']);
    
    $check = db_select_one('users', "username = '$username' AND id != $user_id");

    if ($check) {
        $username_error = "Username '$username' is already taken. Please choose another.";
    } else {  
    db_update('users', [
        'first_name' => $first_name,
        'last_name' => $last_name,
        'username' => $username,
        'email' => $email,
        'phone_number' => $phone,
        'address' => $address,
        'user_type' => $user_type
    ], "id = $user_id");

    header("Location: admin-user-management.php?updated=1");
    exit;
    }
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
        <li class="breadcrumb-item text-nowrap active" aria-current="page"><?= __('breadcrumb_user_management') ?></li>
      </ol>
    </nav>
  </div>
  <div class="order-lg-1 pe-lg-4 text-center text-lg-start">
    <h1 class="h3 text-light mb-0"><?= __('page_user_management') ?></h1>
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
            <li class="border-bottom mb-0"><a class="nav-link-style d-flex align-items-center px-4 py-3" href="admin-user-management.php"><i class="ci-user opacity-60 me-2"></i><?= __('admin_user_management') ?></a></li>
            <li class="border-top mb-0"><a class="nav-link-style d-flex align-items-center px-4 py-3" href="logout.php"><i class="ci-sign-out opacity-60 me-2"></i><?= __('sign_out') ?></a></li>
          </ul>
        </div>
      </div>
    </aside>
  <!-- Content  -->
    <section class="col-lg-8 pt-lg-4 pb-4 mb-3">
        <div class="pt-2 px-4 ps-lg-0 pe-xl-5">
        
          <div class="d-sm-flex flex-wrap justify-content-between align-items-center border-bottom">
            <div class="py-2">
              <form method="GET" class="d-flex flex-nowrap align-items-center pb-3">
                <label class="form-label fw-normal text-nowrap mb-0 me-2" for="sorting"><?= __('sort_by') ?></label>
                <select class="form-select form-select-sm me-2" name="sort" id="sorting" onchange="this.form.submit()">
                  <option value="created" <?= ($_GET['sort'] ?? '') === 'created' ? 'selected' : '' ?>><?= __('sort_newest') ?></option>
                  <option value="id" <?= ($_GET['sort'] ?? '') === 'id' ? 'selected' : '' ?>><?= __('sort_user_id') ?></option>
                  <option value="first_name" <?= ($_GET['sort'] ?? '') === 'first_name' ? 'selected' : '' ?>><?= __('sort_first_name') ?></option>
                  <option value="last_name" <?= ($_GET['sort'] ?? '') === 'last_name' ? 'selected' : '' ?>><?= __('sort_last_name') ?></option>
                  <option value="email" <?= ($_GET['sort'] ?? '') === 'email' ? 'selected' : '' ?>><?= __('sort_email') ?></option>
                  <option value="user_type" <?= ($_GET['sort'] ?? '') === 'user_type' ? 'selected' : '' ?>><?= __('sort_user_type') ?></option>
                </select>
              </form>
            </div>
          </div>

          <?php if (isset($username_error)): ?>
            <div class="alert alert-danger"><?= $username_error ?></div>
          <?php endif; ?>

          <?php if (isset($_GET['updated'])): ?>
            <div class="alert alert-success"><?= __('user_updated_success') ?></div>
          <?php endif; ?>

          <?php if (isset($_GET['deleted'])): ?>
            <div class="alert alert-success"><?= __('user_deleted_success') ?></div>
          <?php endif; ?>

         <?php if ($user_to_edit): ?>
            <div class="bg-black rounded-3 p-4 mb-4">
              <!-- Nav tabs -->
              <ul class="nav nav-tabs text-light" id="userTabs" role="tablist">
                <li class="nav-item" role="presentation">
                  <button class="nav-link active text-light" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab">
                    👤 <?= __('profile_tab') ?>
                  </button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link text-light" id="orders-tab" data-bs-toggle="tab" data-bs-target="#orders" type="button" role="tab">
                    📦 <?= __('orders_tab') ?>
                  </button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link text-light" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab">
                    ⭐ <?= __('reviews_tab') ?>
                  </button>
                </li>
              </ul>

              <!-- Tab content -->
              <div class="tab-content mt-3" id="userTabsContent">
                <!-- Profile -->
                <div class="tab-pane fade show active" id="profile" role="tabpanel">
                  <form method="post">
                    <input type="hidden" name="update_user" value="<?= $user_to_edit['id'] ?>">
                    <div class="mb-3">
                      <label class="form-label text-white"><?= __('first_name_label') ?></label>
                      <input type="text" name="first_name" class="form-control" value="<?= htmlspecialchars($user_to_edit['first_name']) ?>">
                    </div>
                    <div class="mb-3">
                      <label class="form-label text-white"><?= __('last_name_label') ?></label>
                      <input type="text" name="last_name" class="form-control" value="<?= htmlspecialchars($user_to_edit['last_name']) ?>">
                    </div>
                    <div class="mb-3">
                      <label class="form-label text-white"><?= __('username_label') ?></label>
                      <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user_to_edit['username']) ?>">
                    </div>
                    <div class="mb-3">
                      <label class="form-label text-white"><?= __('email_label') ?></label>
                      <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user_to_edit['email']) ?>">
                    </div>
                    <div class="mb-3">
                      <label class="form-label text-white"><?= __('address_label') ?></label>
                      <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($user_to_edit['address']) ?>">
                    </div>
                    <button class="btn btn-primary" type="submit"><?= __('update_user_button') ?></button>
                    <a href="admin-user-management.php" class="btn btn-secondary ms-2"><?= __('cancel_button') ?></a>
                  </form>
                </div>

                <!-- Orders -->
                <div class="tab-pane fade" id="orders" role="tabpanel">
                  <?php if (!empty($orders)): ?>
                    <table class="table table-dark table-hover align-middle w-100">
                      <thead class="bg-darker">
                        <tr>
                          <th><?= __('order_id') ?></th>
                          <th><?= __('order_status') ?></th>
                          <th><?= __('order_total') ?></th>
                          <th><?= __('order_date2') ?></th>
                          <th><?= __('actions') ?></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($orders as $order): ?>
                          <tr>
                            <td><?= htmlspecialchars($order['id']) ?></td>
                            <td><?= htmlspecialchars($order['order_status']) ?></td>
                            <td><?= htmlspecialchars($order['total_price']) ?> €</td>
                            <td><?= date('d-m-Y H:i', strtotime($order['order_date'])) ?></td>
                            <td>
                              <form method="post" action="admin-user-management.php" style="display:inline;" 
                                    onsubmit="return confirm('<?= __('cancel_order_confirm') ?>');">
                                <input type="hidden" name="user_id" value="<?= $user_to_edit['id'] ?>">
                                <input type="hidden" name="cancel_order" value="<?= $order['id'] ?>">
                                <button class="btn btn-sm btn-outline-warning" type="submit"><?= __('cancel_button') ?></button>
                              </form>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  <?php else: ?>
                    <p class="mt-3"><?= __('no_orders_found') ?></p>
                  <?php endif; ?>
                </div>

                <!-- Reviews -->
                <div class="tab-pane fade" id="reviews" role="tabpanel">
                  <?php if (!empty($reviews)): ?>
                    <table class="table table-dark table-hover align-middle w-100">
                      <thead class="bg-darker">
                        <tr>
                          <th><?= __('review_id') ?></th>
                          <th><?= __('review_product') ?></th>
                          <th><?= __('review_rating') ?></th>
                          <th><?= __('review_comment') ?></th>
                          <th><?= __('review_created') ?></th>
                          <th><?= __('actions') ?></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($reviews as $review): ?>
                          <tr>
                            <td><?= htmlspecialchars($review['id']) ?></td>
                            <td><?= htmlspecialchars($review['product_name']) ?></td>
                            <td><span class="badge bg-info"><?= htmlspecialchars($review['rating']) ?>/5</span></td>
                            <td><?= htmlspecialchars($review['comment']) ?></td>
                            <td><?= date('d-m-Y H:i', strtotime($review['created_at'])) ?></td>
                            <td>
                              <form method="post" action="admin-user-management.php" style="display:inline;" 
                                    onsubmit="return confirm('<?= __('delete_review_confirm') ?>');">
                                <input type="hidden" name="user_id" value="<?= $user_to_edit['id'] ?>">
                                <input type="hidden" name="delete_review" value="<?= $review['id'] ?>">
                                <button class="btn btn-sm btn-outline-danger" type="submit"><?= __('delete_button') ?></button>
                              </form>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  <?php else: ?>
                    <p class="mt-3"><?= __('no_reviews_found') ?></p>
                  <?php endif; ?>
                </div>
              </div>
            </div>
            <?php endif; ?>
          <?php
          // Cogemos a nuestros 'users' de la db
          $allowedSorts = ['id','created', 'first_name', 'last_name', 'email', 'user_type'];
          $sort = in_array($_GET['sort'] ?? '', $allowedSorts) ? $_GET['sort'] : 'created';

          $result = $conn->query("SELECT * FROM users ORDER BY $sort ASC");

          if ($result->num_rows > 0): ?>

            <!-- Table layout for medium and larger screens -->
            <div class="d-none d-md-block mt-4">
              <table class="table table-hover align-middle w-100">
                <thead class="bg-darker">
                  <tr>
                    <th><?= __('table_id') ?></th>
                    <th><?= __('table_name') ?></th>
                    <th><?= __('table_username') ?></th>
                    <th><?= __('table_email') ?></th>
                    <th><?= __('table_phone') ?></th>
                    <th><?= __('table_user_type') ?></th>
                    <th><?= __('table_created') ?></th>
                    <th><?= __('table_actions') ?></th>
                  </tr>
                </thead>
                <tbody>
                  <?php while ($user = $result->fetch_assoc()): ?>
                    <tr>
                      <td><?= htmlspecialchars($user['id']) ?></td>
                      <td><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></td>
                      <td><?= htmlspecialchars($user['username']) ?></td>
                      <td><?= htmlspecialchars($user['email']) ?></td>
                      <td><?= htmlspecialchars($user['phone_number']) ?></td>
                      <td><span class="badge bg-<?= $user['user_type'] === 'admin' ? 'danger' : ($user['user_type'] === 'webmaster' ? 'warning' : 'secondary') ?>">
                        <?= htmlspecialchars(ucfirst($user['user_type'])) ?>
                      </span></td>
                      <td><?= date('d-m-Y H:i', strtotime($user['created'])) ?></td>
                      <td>
                        <a href="admin-user-management.php?edit=<?= $user['id'] ?>" class="btn btn-sm btn-outline-primary"><?= __('edit_button') ?></a>
                        <form method="post" action="admin-user-management.php" onsubmit="return confirm('<?= __('delete_confirm') ?>');" style="display:inline;">
                          <input type="hidden" name="delete_user" value="<?= $user['id'] ?>">
                          <button class="btn btn-sm btn-outline-danger" type="submit"><?= __('delete_button') ?></button>
                        </form>
                      </td>
                    </tr>
                  <?php endwhile; ?>
                </tbody>
              </table>
            </div>

          <!-- Card layout for small screens -->
          <div class="d-block d-md-none mt-4">
            <?php
            $result->data_seek(0); // Iteramos de nuevo el bucle
            while ($user = $result->fetch_assoc()): ?>
              <div class="card mb-3">
                <div class="card-body bg-black">
                  <h5 class="card-title"><?= $user['first_name'] . ' ' . $user['last_name'] ?></h5>
                  <p class="card-text mb-1"><strong><?= __('card_username') ?></strong> <?= $user['username'] ?></p>
                  <p class="card-text mb-1"><strong><?= __('card_email') ?></strong> <?= $user['email'] ?></p>
                  <p class="card-text mb-1"><strong><?= __('card_phone') ?></strong> <?= $user['phone_number'] ?></p>
                  <p class="card-text mb-1"><strong><?= __('card_user_type') ?></strong> <?= ucfirst($user['user_type']) ?></p>
                  <p class="card-text mb-2"><strong><?= __('card_created') ?></strong> <?= date('d-m-Y H:i', strtotime($user['created'])) ?></p>
                  <a href="admin-user-management.php?edit=<?= $user['id'] ?>" class="btn btn-sm btn-outline-primary"><?= __('edit_button') ?></a>
                  <form method="post" action="admin-user-management.php" onsubmit="return confirm('<?= __('delete_confirm') ?>');" style="display:inline;">
                    <input type="hidden" name="delete_user" value="<?= $user['id'] ?>">
                    <button class="btn btn-sm btn-outline-danger" type="submit"><?= __('delete_button') ?></button>
                  </form>
                </div>
              </div>
            <?php endwhile; ?>
          </div>

        <?php else: ?>
          <p class="mt-4"><?= __('no_users_found') ?></p>
        <?php endif; ?>

        </div>
      </section>
  </div>
</div>

<?php
require_once('footer.php')
?>    
            