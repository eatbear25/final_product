<?php
// require __DIR__ . "/parts/admin-required.php"; # 需要管理者權限

require __DIR__ . "/parts/db-connect.php";

$data = json_decode(file_get_contents('php://input'), true);
$ids = $data['ids'] ?? [];

if (!empty($ids) && is_array($ids)) {
  $placeholders = implode(',', array_fill(0, count($ids), '?'));
  $sql = "DELETE FROM `product` WHERE id IN ($placeholders)";
  $stmt = $pdo->prepare($sql);
  $stmt->execute($ids);

  echo json_encode(['success' => $stmt->rowCount() > 0]);
} else {
  echo json_encode(['success' => false, 'error' => '無效的 ID']);
}

$come_from = 'product-list.php';
if (! empty($_SERVER['HTTP_REFERER'])) {
  $come_from = $_SERVER['HTTP_REFERER'];
}

header("Location: $come_from");
