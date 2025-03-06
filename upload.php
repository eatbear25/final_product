<?php
// 允許上傳的圖片類型
$allowed_types = array('image/jpeg', 'image/png', 'image/gif');

// 上傳資料夾
$upload_dir = 'uploads/';

if ($_FILES['image']) {
  // 檢查檔案類型是否允許
  if (in_array($_FILES['image']['type'], $allowed_types)) {
    $upload_file = $upload_dir . basename($_FILES['image']['name']);

    // 嘗試將檔案從臨時位置移動到上傳資料夾
    if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_file)) {
      echo "檔案上傳成功.";
    } else {
      echo "上傳過程中出現問題.";
    }
  } else {
    echo "只允許上傳 JPG, JPEG, PNG 和 GIF 類型的圖片.";
  }
}
