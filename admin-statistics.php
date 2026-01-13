<?php
require_once('functions.php');
requireRole(['admin', 'webmaster']);

protected_area();

$conn = new mysqli('localhost', 'root', '', 'retrojuegos');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT user_type, COUNT(*) as count FROM users GROUP BY user_type";
$result = $conn->query($sql);

$userData = [];
while ($row = $result->fetch_assoc()) {
    $userData[] = $row;
}

$monthlyRevenue = [];
$sql = "SELECT DATE_FORMAT(FROM_UNIXTIME(order_date), '%Y-%m') AS month, SUM(total_price) AS revenue
        FROM orders
        GROUP BY month
        ORDER BY month ASC";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $monthlyRevenue[] = $row;
}

$categoryData = [];
$sql = "SELECT categories.name AS category, COUNT(products.id) AS count
        FROM products
        JOIN categories ON products.category_id = categories.id
        GROUP BY category";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $categoryData[] = $row;
}



$conn = new mysqli('localhost', 'root', '', 'retrojuegos');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$topProducts = [];
$productSales = [];

$sql = "SELECT cart FROM orders";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    $raw = $row['cart'];

    $decoded = json_decode($raw, true);

    if (is_string($decoded)) {
        $decoded = json_decode($decoded, true);
    }

    // Extrae nombre y cantidad del producto 
    if (is_array($decoded)) {
        foreach ($decoded as $item) {
            if (isset($item['pro']['name']) && isset($item['quantity'])) {
                $name = $item['pro']['name'];
                $qty = (int)$item['quantity'];
                if (!isset($productSales[$name])) {
                    $productSales[$name] = 0;
                }
                $productSales[$name] += $qty;
            }
        }
    }
}

// Construye el vector final para Chart.js
foreach ($productSales as $name => $sales) {
    $topProducts[] = [
        'name' => $name,
        'sales' => $sales
    ];
}

// Lista categorías por sus claves de traducción para js
$translatedLabels = array_map(function($item) {
  return __($item['category']);
}, $categoryData);

require_once('header.php')
?>


<div class="container d-lg-flex justify-content-between py-2 py-lg-3">
  <div class="order-lg-2 mb-3 mb-lg-0 pt-lg-2">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb breadcrumb-light flex-lg-nowrap justify-content-center justify-content-lg-start">
        <li class="breadcrumb-item"><a class="text-nowrap" href="index.php"><i class="ci-home"></i><?= __('breadcrumb_home') ?></a></li>
        <li class="breadcrumb-item text-nowrap"><a href="admin-dashboard.php"><?= __('breadcrumb_account') ?></a>
        </li>
        <li class="breadcrumb-item text-nowrap active" aria-current="page"><?= __('breadcrumb_statistics') ?></li>
      </ol>
    </nav>
  </div>
  <div class="order-lg-1 pe-lg-4 text-center text-lg-start">
    <h1 class="h3 text-light mb-0"><?= __('page_statistics') ?></h1>
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
          <div class="mt-4">
           <h5 class="mb-3 stats"><?= __('chart_user_type_distribution') ?></h5>
            <div style="width:300px; height:300px;">
             <canvas id="userChart" width="400" height="400"></canvas>
            </div>
          </div>

          <div class="mt-5">
           <h5 class="mb-3 stats"><?= __('chart_top_selling_products') ?></h5>
            <canvas id="topProductsChart" height="100"></canvas>
          </div>

          <div class="mt-5">
           <h5 class="mb-3 stats"><?= __('chart_monthly_revenue') ?></h5>
            <canvas id="revenueChart" height="100"></canvas>
          </div>

          <div class="mt-5">
           <h5 class="mb-3 stats"><?= __('chart_products_per_category') ?></h5>
            <canvas id="categoryChart" height="100"></canvas>
          </div>                        
    </section>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
Chart.defaults.color = '#ffffff';  
Chart.defaults.plugins.legend.labels.color = '#ffffff'; 
Chart.defaults.scales.x.ticks.color = '#ffffff';
Chart.defaults.scales.y.ticks.color = '#ffffff';
Chart.defaults.scales.x.grid.color = 'rgba(108,117,125,0.6)';
Chart.defaults.scales.y.grid.color = 'rgba(108,117,125,0.6)';
</script>

<script>
  const userChart = new Chart(document.getElementById('userChart'), {
  type: 'doughnut',
  data: {
    labels: <?= json_encode(array_column($userData, 'user_type')) ?>,
    datasets: [{
      label: 'Users',
      data: <?= json_encode(array_column($userData, 'count')) ?>,
      backgroundColor: ['#32ffec', '#F73A7C', '#FE870D'],
      borderWidth: 1
    }]
  },
  options: { responsive: true, maintainAspectRatio: false }
});
</script>
<script>
new Chart(document.getElementById('revenueChart'), {
  type: 'line',
  data: {
    labels: <?= json_encode(array_column($monthlyRevenue, 'month')) ?>,
    datasets: [{
      label: 'Revenue ($)',
      data: <?= json_encode(array_column($monthlyRevenue, 'revenue')) ?>,
      borderColor: '#0d6efd', // keep blue
      backgroundColor: 'rgba(13,110,253,0.3)', // lighter fill
      fill: true,
      tension: 0.3
    }]
  },
  options: {
    responsive: true,
    scales: {
      x: {
        ticks: { color: '#ffffff' },
        grid: { color: 'rgba(108,117,125,0.6)', lineWidth: 1.2 }
      },
      y: {
        ticks: { color: '#ffffff' },
        grid: { color: 'rgba(108,117,125,0.6)', lineWidth: 1.2 }
      }
    },
    plugins: {
      legend: { labels: { color: '#ffffff' } }
    }
  }
});
</script>
<script>
new Chart(document.getElementById('categoryChart'), {
  type: 'bar',
  data: {
    labels: <?= json_encode($translatedLabels) ?>,
    datasets: [{
      label: '<?= __('products') ?>',
      data: <?= json_encode(array_column($categoryData, 'count')) ?>,
      backgroundColor: '#FE870D',
      borderColor: '#FE870D',
      borderWidth: 1
    }]
  },
  options: {
    responsive: true,
    scales: {
      x: {
        ticks: { color: '#ffffff' },
        grid: { color: 'rgba(108,117,125,0.6)', lineWidth: 1.2 }
      },
      y: {
        ticks: { color: '#ffffff' },
        grid: { color: 'rgba(108,117,125,0.6)', lineWidth: 1.2 }
      }
    },
    plugins: {
      legend: { labels: { color: '#ffffff' } }
    }
  }
});
</script>
<script>
new Chart(document.getElementById('topProductsChart'), {
  type: 'bar',
  data: {
    labels: <?= json_encode(array_column($topProducts, 'name')) ?>,
    datasets: [{
      label: 'Units Sold',
      data: <?= json_encode(array_column($topProducts, 'sales')) ?>,
      backgroundColor: '#F73A7C',
      borderColor: '#F73A7C',
      borderWidth: 1
    }]
  },
  options: {
    responsive: true,
    scales: {
      x: {
        ticks: { color: '#ffffff' },
        grid: { color: 'rgba(108,117,125,0.6)', lineWidth: 1.2 }
      },
      y: {
        ticks: { color: '#ffffff' },
        grid: { color: 'rgba(108,117,125,0.6)', lineWidth: 1.2 }
      }
    },
    plugins: {
      legend: { labels: { color: '#ffffff' } }
    }
  }
});
</script>


<?php
require_once('footer.php')
?>

