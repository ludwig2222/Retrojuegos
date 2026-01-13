<?php
$product_id = 0;
if (isset($_GET['id'])) {
    $product_id = ((int)($_GET['id']));
}

if($product_id < 1){
    die("ID not found.");
}

require_once('header.php');

$data = get_product($product_id);
$pro = $data['pro'];
$cat = $data['cat'];
if($pro == null){
    die("Product not found.");
}
if($cat == null){
    die("Category not found.");
}

$images = get_product_photos($pro['photos']);

// Obtener reseñas de cada producto de la db
$reviews = [];
$stmt = $conn->prepare("SELECT r.rating, r.comment, r.created_at, u.first_name, u.last_name 
                        FROM reviews r 
                        JOIN users u ON r.user_id = u.id 
                        WHERE r.product_id = ? 
                        ORDER BY r.created_at DESC");
$stmt->bind_param("i", $pro['id']);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $reviews[] = $row;
}
$stmt->close();

// Puntuación media del producto
$avg_rating = null;
$review_count = 0;

$stmt = $conn->prepare("SELECT AVG(rating) AS avg_rating, COUNT(*) AS total_reviews 
                        FROM reviews 
                        WHERE product_id = ?");
$stmt->bind_param("i", $pro['id']);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $avg_rating = $row['avg_rating'];
    $review_count = $row['total_reviews'];
}
$stmt->close();

?>
<!-- Page Title-->    
<div class="container d-lg-flex justify-content-between py-2 py-lg-3">
  <div class="order-lg-2 mb-3 mb-lg-0 pt-lg-2">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb breadcrumb-dark flex-lg-nowrap justify-content-center justify-content-lg-start">
          <li class="breadcrumb-item"><a class="text-nowrap" href="<?= BASE_URL ?>"><i class="ci-home"></i><?= __('breadcrumb_home') ?></a></li>
        <li class="breadcrumb-item text-nowrap"><a href="<?= BASE_URL ?>/shop.php"><?= __('breadcrumb_shop') ?></a>
        </li>
        <li class="breadcrumb-item text-nowrap active" aria-current="page"><?= $pro['name'] ?></li>
      </ol>
    </nav>
  </div>
  <div class="order-lg-1 pe-lg-4 text-center text-lg-start">
    <h1 class="h3 text-white mb-0"><?= $pro['name'] ?></h1>
    <?php if ($review_count > 0): ?>
  <div class="average-rating mb-3">
    <span class="text-warning">
      <?= str_repeat('★', round($avg_rating)) ?>
      <?= str_repeat('☆', 5 - round($avg_rating)) ?>
    </span>
    <span class="ms-2">
      <?= number_format($avg_rating, 1) ?> / 5 
      (<?= $review_count ?> <?= __('reviews_count_label') ?>)
    </span>
  </div>
<?php else: ?>
  <p class="text-muted"><?= __('no_reviews_yet') ?></p>
<?php endif; ?>
  </div>
</div>     
<div class="container">
  <!-- Gallery + details-->
  <div class="bg-black shadow-lg rounded-3 px-4 py-3 mb-5">
    <div class="px-lg-3">
      <div class="row">
        <!-- Product gallery-->
        <div class="col-lg-7 pe-lg-0 pt-lg-4">
          <div class="product-gallery">
            <div class="product-gallery-preview order-sm-2">
              <?php
              foreach ($images as $key => $img) { ?>    
                <div class="product-gallery-preview-item active" id="pro-<?= $key ?>"><img src="<?= $img['src'] ?>" data-zoom="<?= $img['src'] ?>" alt="Product image">
                
                </div>
              <?php } ?>

              <?php if (!empty($pro['video_path'])): ?>
                <div class="product-gallery-preview-item" id="pro-video">
                  <video width="100%" height="315" controls>
                    <source src="<?= $pro['video_path'] ?>" type="video/mp4">
                    <?= __('video_not_supported') ?>
                  </video>
                </div>
              <?php endif; ?>     
            </div>    
            <div class="product-gallery-thumblist order-sm-1">
              <?php
              $active_class = ' active ';
              foreach ($images as $key => $img) { ?>      
                <a class="product-gallery-thumblist-item <?= $active_class ?>" href="#pro-<?= $key ?>">
                    <img src="<?= $img['thumb'] ?>" alt="Product thumb">
                </a>                     

              <?php $active_class = ""; } ?> 

              <?php if (!empty($pro['video_path'])): ?>
                <a class="product-gallery-thumblist-item" href="#pro-video">
                <div class="product-gallery-thumblist-item-text">
                  <i class="ci-video"></i> <span class="label-video"><?= __('video') ?></span>
                </div>
                </a>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <!-- Product details-->
        <div class="col-lg-5 pt-4 pt-lg-0">
          <div class="product-details ms-auto pb-3">
            <div class="mb-3">
                <span class="h3 fw-normal text-accent me-1"><?= convert_price(htmlspecialchars($pro['price'])) ?></span>                   
            </div>
            <form action="cart-process-add.php" class="mb-grid-gutter" method="post">
              <input type="hidden" name="id" value="<?= $product_id ?>">  
              <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center pb-1">
                  <label class="form-label text-white" for="product-size"><?= __('quantity') ?></label>
                </div>
              </div>
              <div class="mb-3 d-flex align-items-center">
                <select name="quantity" class="form-select me-3" style="width: 5rem;">
                  <option value="1">1</option>
                  <option value="2">2</option>
                  <option value="3">3</option>
                  <option value="4">4</option>
                  <option value="5">5</option>
                </select>
                <button class="btn btn-primary btn-shadow d-block w-100" type="submit"><i class="ci-cart fs-lg me-2"></i><?= __('add_to_cart_button') ?></button>
              </div>
            </form>
              
            <!-- Product panels-->
            <div class="accordion mb-4" id="productPanels">
              <div class="accordion-item">
                <h3 class="accordion-header">
                    <a class="accordion-button text-white" href="#productInfo" role="button" data-bs-toggle="collapse" aria-expanded="true" aria-controls="productInfo">
                        <i class="ci-announcement text-warning fs-lg align-middle mt-n1 me-2"></i><?= __('game_info') ?>
                    </a>
                </h3>
                <div class="accordion-collapse collapse show" id="productInfo" data-bs-parent="#productPanels">
                  <div class="accordion-body">
                    <h6 class="fs-sm mb-2 text-white"><?= __('description') ?></h6>
                    <ul class="fs-sm ps-4 text-white">
                      <li><?= (__($pro['description'])) ?></li>
                    </ul>
                    <h6 class="fs-sm mb-2 text-white"><?= __('genre') ?></h6>
                    <ul class="fs-sm ps-4 mb-0 text-white">
                      <li><?= htmlspecialchars(__($cat['name'])) ?></li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Review Form -->
            <div class="review-form mt-4">
            <h3 class="h5 text-light mb-3"><?= __('leave_review') ?></h3>
            <form action="review-process.php" method="post">
              <!-- Hidden product ID -->
              <input type="hidden" name="product_id" value="<?= $pro['id'] ?>">

              <!-- Rating -->
              <div class="mb-3">
                <label for="rating" class="form-label"><?= __('rating_label') ?></label>
                <select class="form-select" id="rating" name="rating" required>
                  <option value="5">★★★★★</option>
                  <option value="4">★★★★☆</option>
                  <option value="3">★★★☆☆</option>
                  <option value="2">★★☆☆☆</option>
                  <option value="1">★☆☆☆☆</option>
                </select>
              </div>

              <!-- Comment -->
              <div class="mb-3">
                <label for="comment" class="form-label"><?= __('comment_label') ?></label>
                <textarea class="form-control" id="comment" name="comment" rows="4" required></textarea>
              </div>

              <!-- Submit -->
              <button type="submit" class="btn btn-outline-primary"><?= __('submit_review_button') ?></button>
            </form>
            </div>
            
            <div class="reviews mt-5">
                <h3 class="h5 text-light mb-3"><?= __('customer_reviews') ?></h3>

                <?php if (empty($reviews)): ?>
                  <p class="text-muted"><?= __('no_reviews_yet') ?></p>
                <?php else: ?>
                  <?php foreach ($reviews as $rev): ?>
                    <div class="review border-bottom py-3">
                      <div class="d-flex justify-content-between">
                        <strong><?= htmlspecialchars($rev['first_name'] . ' ' . $rev['last_name']) ?></strong>
                        <span class="text-warning">
                          <?= str_repeat('★', $rev['rating']) ?>
                          <?= str_repeat('☆', 5 - $rev['rating']) ?>
                        </span>
                      </div>
                      <p class="mb-1"><?= nl2br(htmlspecialchars($rev['comment'])) ?></p>
                      <small class="text-muted"><?= date('d M Y H:i', strtotime($rev['created_at'])) ?></small>
                    </div>
                  <?php endforeach; ?>
                <?php endif; ?>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>   
      
<?php
require_once('footer.php');
?>
