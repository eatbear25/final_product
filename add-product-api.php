<?php
// require __DIR__ . "/parts/admin-required.php"; # 需要管理者權限
require __DIR__ . "/parts/db-connect.php";

header('Content-Type: application/json');

$output = [
  'success' => false,
  'postData' => $_POST,
  'error' => '',
  'file' => '', # 儲存的檔名
  'errorFields' => []
];

// * 取得表單資料
$name = trim($_POST['name'] ?? '');
$content = trim($_POST['content'] ?? '');
$category_id = $_POST['category'] ?? null;
$stock = $_POST['stock'] ?? null;
$price = $_POST['price'] ?? null;
$status = isset($_POST['status']) ? (int)$_POST['status'] : 1;

$dir = __DIR__ . '/product_images/';

// * 欄位的資料檢查
$isPass = true;

// * 圖片驗證
# 1.篩選可上傳的檔案類型, 2.決定副檔名
$extMap = [
  'image/jpeg' => '.jpg',
  'image/png' => '.png',
  'image/webp' => '.webp',
];

if (empty($_FILES['avatar'])) {
  # avatar 欄位沒有上傳檔案
  echo json_encode($output);
  exit;
}
if (! is_string($_FILES['avatar']['name'])) {
  # 必須只上傳一個檔案
  $output['code'] = 401;
  echo json_encode($output);
  exit;
}

if ($_FILES['avatar']['error'] != 0) {
  # 上傳過程發生錯誤
  $output['code'] = 405;
  echo json_encode($output);
  exit;
}

if (empty($extMap[$_FILES['avatar']['type']])) {
  # 上傳的檔案不符合要求的類型
  $output['code'] = 407;
  echo json_encode($output);
  exit;
}

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

if (!isset($status)) {
  $isPass = false;
  $output['errorFields']['status'] = '商品狀態為必填欄位';
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

// * 確認圖片沒問題，就搬運檔案到指定資料夾
$ext = $extMap[$_FILES['avatar']['type']]; # 對應到的副檔
$file = md5($_FILES['avatar']['name'] . uniqid()) . $ext;
$output['file'] = $file;

try {
  $output['success'] = move_uploaded_file(
    $_FILES['avatar']['tmp_name'],
    $dir .  $file
  );
} catch (Exception $ex) {
  $output['error'] = $ex->getMessage();
}


$sql = "INSERT INTO `product` (
    `name`, `content`, `product_category_id`, `stock`, `price`, `status`, `image`
    ) VALUES (
      ?,
      ?,
      ?,
      ?,
      ?,
      ?,
      ?
    )";

// * 這邊是沒有圖片的
// $sql = "INSERT INTO `product` (
//   `name`, `content`, `product_category_id`, `stock`, `price`, `status`
//   ) VALUES (
//     ?,
//     ?,
//     ?,
//     ?,
//     ?,
//     ?
//   )";

try {
  $stmt = $pdo->prepare($sql);
  $stmt->execute([
    $name,
    $content,
    $category_id,
    $stock,
    $price,
    $status,
    $file
  ]);

  # $stmt->rowCount() 影響的列數, 新增的話就是新增幾筆
  $output['success'] = !! $stmt->rowCount();
  $output['id'] = $pdo->lastInsertId(); # 最近新增資料的 PK
} catch (PDOException $ex) {
  $output['error'] = $ex->getMessage();
}

echo json_encode($output, JSON_UNESCAPED_UNICODE);
