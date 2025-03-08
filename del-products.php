<?php
require __DIR__ . "/parts/db-connect.php";

header('Content-Type: application/json'); // 確保回傳 JSON 格式

$data = json_decode(file_get_contents('php://input'), true);

// 檢查 JSON 是否有效
if (json_last_error() !== JSON_ERROR_NONE) {
  echo json_encode(['success' => false, 'error' => 'JSON 解析錯誤']);
  exit;
}

$ids = $data['ids'] ?? [];

if (!empty($ids) && is_array($ids)) {
  // 確保 ID 陣列都是數字
  $ids = array_map('intval', $ids);

  // SQL: 使用占位符 (防止 SQL Injection)
  $placeholders = implode(',', array_fill(0, count($ids), '?'));
  $sql = "DELETE FROM `product` WHERE id IN ($placeholders)";
  $stmt = $pdo->prepare($sql);
  $stmt->execute($ids);

  echo json_encode(['success' => $stmt->rowCount() > 0]);
} else {
  echo json_encode(['success' => false, 'error' => '無效的 ID']);
}
