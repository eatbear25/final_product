<?php
// require __DIR__ . "/parts/admin-required.php";
require __DIR__ . "/parts/db-connect.php";

header('Content-Type: application/json');

$output = [
  'success' => false,
  'postData' => $_POST,
  'error' => '',
  'file' => '',
  'errorFields' => [],
  'no_changes' => false
];

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

if ($id < 1) {
  $output['error'] = '無效的商品 ID';
  echo json_encode($output, JSON_UNESCAPED_UNICODE);
  exit;
}

// 取得舊的商品資料
$sql = "SELECT * FROM product WHERE id=?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$row = $stmt->fetch();

if (!$row) {
  $output['error'] = '找不到該商品資料';
  echo json_encode($output, JSON_UNESCAPED_UNICODE);
  exit;
}

// **處理表單數據**
$name = trim($_POST['name'] ?? '');
$content = trim($_POST['content'] ?? '');
$category_id = isset($_POST['category']) ? intval($_POST['category']) : null;
$stock = isset($_POST['stock']) ? intval($_POST['stock']) : null;
$price = isset($_POST['price']) ? intval($_POST['price']) : null;
$status = isset($_POST['status']) ? intval($_POST['status']) : 1;
$file = $row['image'];
$hasNewImage = !empty($_FILES['avatar']['name']);

// **表單驗證**
$isPass = true;

if (empty($name)) {
  $isPass = false;
  $output['errorFields']['name'] = '商品名稱為必填欄位';
}

if (empty($category_id) || $category_id <= 0) {
  $isPass = false;
  $output['errorFields']['category'] = '請選擇正確的商品分類';
}

if (!isset($stock) || !is_numeric($stock) || $stock < 0) {
  $isPass = false;
  $output['errorFields']['stock'] = '庫存必須是正整數';
}

if (!isset($price) || !is_numeric($price) || $price < 1) {
  $isPass = false;
  $output['errorFields']['price'] = '價格必須是正整數';
}

if (!$isPass) {
  echo json_encode($output, JSON_UNESCAPED_UNICODE);
  exit;
}

// **判斷是否完全沒有變更**
if (
  $name === $row['name'] &&
  $content === $row['content'] &&
  $category_id === $row['product_category_id'] &&
  $stock === $row['stock'] &&
  $price === $row['price'] &&
  $status === $row['status'] &&
  !$hasNewImage
) {
  $output['no_changes'] = true;
  echo json_encode($output, JSON_UNESCAPED_UNICODE);
  exit;
}

// **圖片處理**
$extMap = [
  'image/jpeg' => '.jpg',
  'image/png' => '.png',
  'image/webp' => '.webp',
];

if ($hasNewImage) {
  if (!isset($extMap[$_FILES['avatar']['type']])) {
    $output['error'] = '圖片格式錯誤，只能上傳 jpg/png/webp';
    echo json_encode($output, JSON_UNESCAPED_UNICODE);
    exit;
  }

  $dir = __DIR__ . '/product_images/';
  $ext = $extMap[$_FILES['avatar']['type']];
  $newFile = md5($_FILES['avatar']['name'] . uniqid()) . $ext;
  $output['file'] = $newFile;

  if (!move_uploaded_file($_FILES['avatar']['tmp_name'], $dir . $newFile)) {
    $output['error'] = '圖片上傳失敗';
    echo json_encode($output, JSON_UNESCAPED_UNICODE);
    exit;
  }

  if (!empty($file) && file_exists($dir . $file)) {
    unlink($dir . $file);
  }

  $file = $newFile;
}

// **執行 UPDATE**
$sql = "UPDATE `product` SET 
        `name`=?, 
        `content`=?, 
        `product_category_id`=?, 
        `stock`=?, 
        `price`=?, 
        `status`=?, 
        `image`=? 
        WHERE `id`=?";

$stmt = $pdo->prepare($sql);
$stmt->execute([
  $name,
  $content,
  $category_id,
  $stock,
  $price,
  $status,
  $file,
  $id
]);

$output['success'] = ($stmt->rowCount() > 0);

echo json_encode($output, JSON_UNESCAPED_UNICODE);
