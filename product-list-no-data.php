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

<div class="alert alert-warning" role="alert">
  沒有查詢到資料
</div>