<?php
// 確保變數存在
if (!isset($column)) {
  $column = 'id';
}
if (!isset($order)) {
  $order = 'ASC';
}
if (!isset($next_order)) {
  $next_order = 'desc';
}

$searchQuery = $search ? "&search=" . urlencode($search) : "";
$statusQuery = $filter_status !== '' ? "&status=" . intval($filter_status) : "";
$categoryQuery = $filter_category !== '' ? "&category=" . intval($filter_category) : "";
$extraQuery = $searchQuery . $statusQuery . $categoryQuery;
?>


<!-- 新增以及商品管理 -->
<div class="row mb-4 g-0">
  <!-- 刪除所選 -->
  <div class="col-2">
    <button type="button" class="btn btn-danger" id="batchDeleteBtn"><i class="fa-solid fa-trash pe-2"></i>刪除所選</button>
  </div>

  <!-- 篩選框 -->
  <div class="col-6">
    <form class="d-flex" method="GET">
      <!-- 商品狀態篩選 -->
      <select class="form-select me-2" name="status" style="width: 180px; max-width: 100%;">
        <option value="" <?= $filter_status === '' ? 'selected' : '' ?>>商品狀態</option>
        <option value="1" <?= $filter_status === '1' ? 'selected' : '' ?>>上架</option>
        <option value="0" <?= $filter_status === '0' ? 'selected' : '' ?>>下架</option>
      </select>

      <!-- 商品分類篩選 -->
      <select class="form-select me-2" name="category" style="width: 220px; max-width: 100%;">
        <option value="" <?= $filter_category === '' ? 'selected' : '' ?>>商品分類</option>
        <?php
        $cat_sql = "SELECT id, name FROM product_category";
        $categories = $pdo->query($cat_sql)->fetchAll();
        foreach ($categories as $cat) :
        ?>
          <option value="<?= $cat['id'] ?>" <?= $filter_category == $cat['id'] ? 'selected' : '' ?>>
            <?= $cat['name'] ?>
          </option>
        <?php endforeach; ?>
      </select>

      <button class="btn btn-outline-success me-2" type="submit">
        <i class="fa-solid fa-filter"></i> 篩選
      </button>

      <a href="?page=1" class="btn btn-outline-secondary">
        <i class="fa-solid fa-arrow-rotate-right"></i> 清除
      </a>
    </form>
  </div>

  <!-- 搜尋框 -->
  <div class="col-4">
    <form class="d-flex" role="search" method="GET">
      <input class="form-control me-2" type="search" name="search"
        value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" placeholder="請輸入商品名稱或介紹">

      <!-- 隱藏篩選條件，確保搜尋時不會清空篩選 -->
      <input type="hidden" name="status" value="<?= htmlspecialchars($filter_status) ?>">
      <input type="hidden" name="category" value="<?= htmlspecialchars($filter_category) ?>">

      <button class="btn btn-outline-success" type="submit">
        <i class="fa-solid fa-magnifying-glass"></i>
      </button>
    </form>
  </div>

</div>

<div class="row mb-3">
  <div class="col">
    <table class="table table-bordered table-hover">
      <thead class="table-light">
        <tr>
          <th style="width: 40px " class="text-center">
            <input class="form-check-input" type="checkbox" id="selectAll">
          </th>
          <th style="width: 50px">
            <a href="?page=<?= $page ?>&column=id&order=<?= $next_order ?><?= $extraQuery ?>">
              編號 <?= $column == 'id' ? $sort_icons[$order] : '' ?>
            </a>
          </th>
          <th style="width: 100px">商品名稱</th>
          <th style="width: 200px">介紹</th>
          <th style="width: 70px">分類</th>
          <th style="width: 60px">
            <a href="?page=<?= $page ?>&column=stock&order=<?= $next_order ?><?= $extraQuery ?>">
              庫存 <?= $column == 'stock' ? $sort_icons[$order] : '' ?>
            </a>
          </th>
          <th style="width: 60px">
            <a href="?page=<?= $page ?>&column=price&order=<?= $next_order ?><?= $extraQuery ?>">
              價格 <?= $column == 'price' ? $sort_icons[$order] : '' ?>
            </a>
          </th>
          <th style="width: 70px">商品狀態</th>
          <th style="width: 80px">照片</th>
          <th style="width: 80px">
            <a href="?page=<?= $page ?>&column=created_at&order=<?= $next_order ?><?= $extraQuery ?>">
              上架時間 <?= $column == 'created_at' ? $sort_icons[$order] : '' ?>
            </a>
          </th>
          <!-- <th><a href="?page=<?= $page ?>&column=updated_at&order=<?= $next_order ?>">更新時間 <?= $column == 'updated_at' ? $sort_icons[$order] : '' ?></a></th> -->
          <th style="width: 100px">操作</th>
        </tr>
      </thead>

      <tbody class="my-auto">
        <?php foreach ($rows as $r): ?>
          <tr>
            <td class="text-center">
              <input class="form-check-input delete-checkbox" type="checkbox" name="product_delete_id[]" value="<?= $r['id'] ?>">
            </td>
            <td class="text-center"><?= $r['id'] ?></td>
            <td><?= $r['name'] ?></td>
            <td><?= $r['content'] ?></td>
            <td><?= $r['category_name'] ?></td>
            <td><?= $r['stock'] ?></td>
            <td>$<?= number_format($r['price']) ?></td>
            <td class="text-center">
              <button type="button" class="btn <?= $r['status'] == 1 ? 'btn-outline-success' : 'btn-outline-danger' ?>">
                <?= $r['status'] == 1 ? '上架' : '下架' ?>
              </button>
            </td>
            <td class="text-center"><img src="./product_images/<?= $r['image'] ?>" width="80"></td>
            <td><?= date("Y-m-d", strtotime($r['created_at'])) ?></td>
            <!-- <td><?= date("Y-m-d", strtotime($r['updated_at'])) ?></td> -->
            <td class="text-center">
              <a href="edit-product.php?id=<?= $r['id'] ?>" class="btn btn-warning">編輯</a>
              <a href="javascript: deleteOne(<?= $r['id'] ?>)" class="btn btn-danger">刪除</a>
            </td>
          </tr>
        <?php endforeach ?>
      </tbody>
    </table>
  </div>
</div>

<!-- 頁碼 -->
<div class="row mb-2">
  <div class="col">
    <nav aria-label="Page navigation example">
      <ul class="pagination justify-content-center">
        <?php
        $searchQuery = $search ? "&search=" . urlencode($search) : "";
        $statusQuery = $filter_status !== '' ? "&status=" . intval($filter_status) : "";
        $categoryQuery = $filter_category !== '' ? "&category=" . intval($filter_category) : "";
        $extraQuery = $searchQuery . $statusQuery . $categoryQuery;
        ?>

        <!-- 第一頁 -->
        <li class="page-item">
          <a class="page-link <?= $page == 1 ? 'disabled' : '' ?>" href="?page=1<?= $extraQuery ?>&column=<?= $column ?>&order=<?= $order ?>">
            <i class="fa-solid fa-angles-left"></i>
          </a>
        </li>

        <!-- 上一頁 -->
        <li class="page-item">
          <a class="page-link <?= $page == 1 ? 'disabled' : '' ?>" href="?page=<?= $page - 1 ?><?= $extraQuery ?>&column=<?= $column ?>&order=<?= $order ?>">
            <i class="fa-solid fa-angle-left"></i>
          </a>
        </li>

        <!-- 中間頁碼 -->
        <?php for ($i = $page - 5; $i <= $page + 5; $i++):
          if ($i >= 1 && $i <= $totalPages):
            $qs = $_GET;
            $qs['page'] = $i;
            $qs['column'] = $column;
            $qs['order'] = $order;
        ?>
            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
              <a class="page-link" href="?<?= http_build_query($qs) ?>"><?= $i ?></a>
            </li>
        <?php endif;
        endfor; ?>

        <!-- 下一頁 -->
        <li class="page-item">
          <a class="page-link <?= $page == $totalPages ? 'disabled' : '' ?>" href="?page=<?= $page + 1 ?><?= $extraQuery ?>&column=<?= $column ?>&order=<?= $order ?>">
            <i class="fa-solid fa-angle-right"></i>
          </a>
        </li>

        <!-- 最後一頁 -->
        <li class="page-item">
          <a class="page-link <?= $page == $totalPages ? 'disabled' : '' ?>" href="?page=<?= $totalPages ?><?= $extraQuery ?>&column=<?= $column ?>&order=<?= $order ?>">
            <i class="fa-solid fa-angles-right"></i>
          </a>
        </li>
      </ul>
    </nav>
  </div>
</div>