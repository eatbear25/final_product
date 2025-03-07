<?php
require __DIR__ . "/parts/db-connect.php";
$title = '商品列表';
$pageName = 'product-list';

# 用戶要看的頁數
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
if ($page < 1) {
  # $page = 1; # 讓 $page 永遠是大於等於 1
  header('Location: ?page=1'); # 轉向, 跳頁
  exit; # 結束程式
}

$where = ' WHERE 1 '; # SQL 條件的開頭

$search = $_GET['search'] ?? '';

if ($search) {
  $search_esc = $pdo->quote("%{$search}%"); # 避免 SQL injection
  $where .= " AND (`name` LIKE $search_esc OR `content` LIKE $search_esc ) ";
}


$t_sql = " SELECT COUNT(1) FROM `product` $where ";

# 每頁有幾筆
$perPage = 8;
# 取得總筆數
$totalRows = $pdo->query($t_sql)->fetch(PDO::FETCH_NUM)[0];

$totalPages = 0; # 總頁數的預設值
$rows = []; # 頁面資料的預設值

// 允許排序的欄位，防止 SQL Injection
$allowed_columns = ['id', 'stock', 'price', 'created_at', 'updated_at'];
$column = isset($_GET['column']) && in_array($_GET['column'], $allowed_columns) ? $_GET['column'] : 'id';
$order = isset($_GET['order']) && strtolower($_GET['order']) == 'desc' ? 'DESC' : 'ASC';

// 計算下一次點擊時的排序方向
$next_order = ($order === 'ASC') ? 'desc' : 'asc';
$sort_icons = [
  'ASC' => '<i class="fas fa-sort-up"></i>',
  'DESC' => '<i class="fas fa-sort-down"></i>'
];

// 原本的
// if ($totalRows) {
//   # 計算總頁數
//   $totalPages = ceil($totalRows / $perPage);

//   if ($page > $totalPages) {
//     header("Location: ?page={$totalPages}"); # 跳到最後一頁
//     exit;
//   }
//   # 取得該頁面的資料
//   $sql = sprintf("SELECT * FROM `product`
//                 %s
//                 ORDER BY id DESC 
//                 LIMIT %s, %s ", $where, ($page - 1) * $perPage, $perPage);
//   try {
//     $rows = $pdo->query($sql)->fetchAll();
//   } catch (PDOException $ex) {
//     echo '<h1>' . $ex->getMessage() . '</h1>';
//     echo '<h2>' . $ex->getCode() . '</h2>';
//   }
// }

if ($totalRows) {
  # 計算總頁數
  $totalPages = ceil($totalRows / $perPage);

  if ($page > $totalPages) {
    header("Location: ?page={$totalPages}");
    exit;
  }

  // 取得該頁面的資料，加入排序
  $sql = sprintf(
    "SELECT * FROM `product` %s 
    ORDER BY %s %s 
    LIMIT %s, %s",
    $where,
    $column,
    $order,
    ($page - 1) * $perPage,
    $perPage
  );

  try {
    $rows = $pdo->query($sql)->fetchAll();
  } catch (PDOException $ex) {
    echo '<h1>' . $ex->getMessage() . '</h1>';
    echo '<h2>' . $ex->getCode() . '</h2>';
  }
}

?>
<?php include __DIR__ . '/parts/html-head.php' ?>

<style>
  .search-field b {
    color: red;
  }

  th a {
    text-decoration: none;
    color: #228be6;
    font-weight: bold;
  }

  tr .highlight {
    background-color: #f9fafb;
  }
</style>

<?php include __DIR__ . '/parts/html-sidebar.php' ?>
<div class="container">
  <div class="row">
    <div class="col-8"></div>

    <div class="col-4 mb-4">
      <form class="d-flex" role="search">
        <input class="form-control me-2" type="search"
          name="search" value="<?= $_GET['search'] ?? '' ?>"
          placeholder="請輸入商品名稱或介紹" aria-label="Search">
        <button class="btn btn-outline-success" type="submit">Search</button>
      </form>
      <a href="<?= $_SERVER['PHP_SELF'] ?>" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-rotate-right"></i></a>
    </div>
  </div>

  <?php
  if (empty($rows)) {
    include __DIR__ . '/product-list-no-data.php';
  } else {
    include __DIR__ . '/product-list-content.php';
  }
  ?>

</div>
<?php include __DIR__ . '/parts/html-scripts.php' ?>

<script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
<script>
  const $_GET = <?= json_encode($_GET) ?>; // 生頁面時, 直接把資料放到 JS
  const search = $_GET.search;
  if (search) {
    const searchFields = document.querySelectorAll('.search-field');
    for (let td of searchFields) {
      td.innerHTML = td.innerHTML.split(search).join(`<b>${search}</b>`)
    }
  }

  const deleteOne = id => {
    // question: 1. 若要在詢問時呈現名字? 2. 點選後在詢問時整列要呈現明顯的底色
    if (confirm(`確定要刪除編號為 ${id} 的資料嗎?`)) {
      location.href = `del-product.php?id=${id}`;
    }
  }
</script>

<?php include __DIR__ . '/parts/html-tail.php' ?>