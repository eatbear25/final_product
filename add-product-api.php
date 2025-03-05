<?php
// require __DIR__ . "/parts/admin-required.php"; # 需要管理者權限
require __DIR__ . "/parts/db-connect.php";

header('Content-Type: application/json');

$output = [
  'success' => false,
  'postData' => $_POST,
  'error' => '',
  'errorFields' => []
];

// * 取得表單資料
$name = trim($_POST['name'] ?? '');
$content = trim($_POST['content'] ?? '');
$category_id = $_POST['category'] ?? null;
$stock = $_POST['stock'] ?? null;
$price = $_POST['price'] ?? null;
$status = isset($_POST['status']) ? (int)$_POST['status'] : 0;

// * 欄位的資料檢查
$isPass = true;

// 產品名稱驗證
if (empty($name)) {
  $isPass = false;
  $output['errorFields']['name'] = '商品名稱為必填欄位';
} elseif (mb_strlen($name) < 2) {
  $isPass = false;
  $output['errorFields']['name'] = '請填寫至少兩字元以上的商品名稱';
}

if (empty($category_id)) {
  $isPass = false;
  $output['errorFields']['category'] = '商品分類為必填欄位';
}

if (empty($status)) {
  $isPass = false;
  $output['errorFields']['status'] = '商品分類為必填欄位';
}

// 庫存驗證
if (!isset($_POST['stock']) || !is_numeric($_POST['stock']) || intval($_POST['stock']) < 0) {
  $isPass = false;
  $output['errorFields']['stock'] = '庫存必須是正整數';
}

// 價格驗證
if (!isset($_POST['price']) || !is_numeric($_POST['price']) || intval($_POST['price']) < 1) {
  $isPass = false;
  $output['errorFields']['price'] = '價格必須是正整數';
}

if (! $isPass) {
  echo json_encode($output, JSON_UNESCAPED_UNICODE);
  exit;
}


// $sql = "INSERT INTO `product` (
//     `name`, `content`, `product_category_id`, `stock`, `price`, `status`
//     ) VALUES (
//       ?,
//       ?,
//       ?,
//       ?,
//       ?,
//       -- `image` ?,
//       ?
//     )";

// * 這邊是沒有圖片的
$sql = "INSERT INTO `product` (
  `name`, `content`, `product_category_id`, `stock`, `price`, `status`
  ) VALUES (
    ?,
    ?,
    ?,
    ?,
    ?,
    ?
  )";

try {
  $stmt = $pdo->prepare($sql);
  $stmt->execute([
    $name,
    $content,
    $category_id,
    $stock,
    $price,
    $status
    // $image_name // ✅ 把圖片檔名存入資料庫
  ]);

  # $stmt->rowCount() 影響的列數, 新增的話就是新增幾筆
  $output['success'] = !! $stmt->rowCount();
  $output['id'] = $pdo->lastInsertId(); # 最近新增資料的 PK
} catch (PDOException $ex) {
  $output['error'] = $ex->getMessage();
}

echo json_encode($output, JSON_UNESCAPED_UNICODE);
