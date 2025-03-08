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
  $where .= " AND (product.name LIKE $search_esc OR product.content LIKE $search_esc ) ";
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


if ($totalRows) {
  # 計算總頁數
  $totalPages = ceil($totalRows / $perPage);

  if ($page > $totalPages) {
    header("Location: ?page={$totalPages}");
    exit;
  }

  // 取得該頁面的資料，並加入 JOIN
  $sql = sprintf(
    " SELECT product.*, product_category.name AS category_name
      FROM product
      INNER JOIN product_category ON product.product_category_id = product_category.id
      %s
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
    font-weight: bold;
  }

  tr .highlight {
    background-color: #f9fafb;
  }


  input[type="checkbox"] {
    transform: scale(1.3);
    border: 1px solid #999;
    cursor: pointer;
  }

  thead {
    height: 60px;
  }

  table td,
  table th {
    vertical-align: middle;
  }
</style>

<?php include __DIR__ . '/parts/html-sidebar.php' ?>

<div class="container">
  <!-- 麵包屑 -->
  <div class="row">
    <div class="col py-4">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="#">
              <i class="fa-solid fa-house"></i>
            </a>
          </li>
          <li class="breadcrumb-item" aria-current="page">商品管理</li>
        </ol>
      </nav>
    </div>
  </div>

  <div class="row">
    <div class="col-8">
      <span class="fw-bold">全部商品</span> (<?= $totalRows ?>)
    </div>

    <!-- 搜尋 -->
    <div class="col-4 mb-3">
      <form class="d-flex" role="search">
        <input class="form-control" type="search"
          name="search" value="<?= $_GET['search'] ?? '' ?>"
          placeholder="請輸入商品名稱或介紹" aria-label="Search">
        <button class="btn btn-outline-success me-3" type="submit">
          <i class="fa-solid fa-magnifying-glass"></i>
        </button>
        <a href="<?= $_SERVER['PHP_SELF'] ?>" class="btn btn-outline-secondary">
          <i class="fa-solid fa-arrow-rotate-right"></i>
        </a>
      </form>
    </div>
  </div>

  <!-- 表格內容 -->
  <?php
  if (empty($rows)) {
    include __DIR__ . '/product-list-no-data.php';
  } else {
    include __DIR__ . '/product-list-content.php';
  }
  ?>

</div>

<?php include __DIR__ . '/parts/html-scripts.php' ?>

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

  // 變更表格行的背景顏色
  function toggleRowHighlight(checkbox) {
    let row = checkbox.closest("tr");
    if (checkbox.checked) {
      row.classList.add("table-danger");
    } else {
      row.classList.remove("table-danger");
    }
  }

  // * 批次刪除
  const batchDeleteBtn = document.getElementById("batchDeleteBtn");
  const checkboxes = document.querySelectorAll(".delete-checkbox");
  const selectAllCheckbox = document.getElementById("selectAll");

  // 監聽「全選」按鈕
  selectAllCheckbox.addEventListener("change", function() {
    checkboxes.forEach(cb => {
      cb.checked = this.checked;
      toggleRowHighlight(cb);
    });
  });

  // 監聽所有單選 checkbox，如果有取消選取則「全選」要取消
  checkboxes.forEach(cb => {
    cb.addEventListener("change", function() {
      selectAllCheckbox.checked = Array.from(checkboxes).every(cb => cb.checked);
      toggleRowHighlight(cb);
    });
  });



  // 點擊「刪除多筆」按鈕
  batchDeleteBtn.addEventListener("click", function() {
    let selectedIds = Array.from(checkboxes)
      .filter(cb => cb.checked) // 只取被勾選的 checkbox
      .map(cb => cb.value); // 取出 value (商品 ID)

    if (selectedIds.length === 0) {
      alert("請選擇要刪除的商品！");
      return;
    }

    if (!confirm(`確定要刪除這 ${selectedIds.length} 個商品嗎？`)) {
      return;
    }

    // 送出 AJAX 請求
    fetch("del-products.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json"
        },
        body: JSON.stringify({
          ids: selectedIds
        })
      })
      .then(response => response.json())
      .then(result => {
        if (result.success) {
          alert("刪除成功！");
          location.reload(); // 重新整理頁面
        } else {
          alert("刪除失敗：" + result.error);
        }
      })
      .catch(error => console.error("批次刪除錯誤:", error));
  });
</script>

<?php include __DIR__ . '/parts/html-tail.php' ?>