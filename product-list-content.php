<div class="row mb-2">
  <div class="col">
    <nav aria-label="Page navigation example">
      <ul class="pagination">
        <li class="page-item">
          <a class="page-link <?= $page == 1 ? 'disabled' : '' ?>" href="?page=1">
            <i class="fa-solid fa-angles-left"></i>
          </a>
        </li>
        <li class="page-item">
          <a class="page-link <?= $page == 1 ? 'disabled' : '' ?>" href="?page=<?= $page - 1 ?>">
            <i class="fa-solid fa-angle-left"></i>
          </a>
        </li>

        <?php for ($i = $page - 5; $i <= $page + 5; $i++):
          if ($i >= 1 and $i <= $totalPages):
            $qs = $_GET; # 複製 $_GET, 包含所有 query string parameters
            $qs['page'] = $i;
        ?>
            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
              <a class="page-link" href="?<?= http_build_query($qs) ?>"><?= $i ?></a>
            </li>
        <?php endif;
        endfor; ?>

        <li class="page-item">
          <a class="page-link <?= $page == $totalPages ? 'disabled' : '' ?>" href="?page=<?= $page + 1 ?>">
            <i class="fa-solid fa-angle-right"></i>
          </a>
        </li>
        <li class="page-item">
          <a class="page-link <?= $page == $totalPages ? 'disabled' : '' ?>" href="?page=<?= $totalPages ?>">
            <i class="fa-solid fa-angles-right"></i>
          </a>
        </li>

        <a class="btn btn-success ms-auto" href="add-product.php" role="button">
          + 新增商品
        </a>
      </ul>

    </nav>
  </div>
</div>

<div class="row">
  <div class="col">
    <table class="table table-striped table-bordered">
      <thead>
        <tr>
          <th scope="col">#</th>
          <th scope="col">商品名稱</th>
          <th scope="col">介紹</th>
          <th scope="col">分類</th>
          <th scope="col">庫存</th>
          <th scope="col">價格</th>
          <th scope="col">商品狀態</th>
          <th scope="col">照片</th>
          <th scope="col">上架時間</th>
          <th scope="col">更新時間</th>
          <th scope="col">操作</th>
        </tr>
      </thead>

      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?= $r['id'] ?></td>
            <td><?= $r['name'] ?></td>
            <td><?= $r['content']; ?></td>
            <td><?= $r['product_category_id'] ?></td>
            <td><?= $r['stock'] ?></td>
            <td>$<?= number_format($r['price']) ?>
            </td>
            <td>
              <button type="button" class="btn <?= $r['status'] == 1 ? 'btn-outline-success' : 'btn-outline-danger' ?>">
                <?= $r['status'] == 1 ? '上架' : '下架' ?>
              </button>
            </td>
            <td><?= $r['image'] ?></td>
            <!-- <td><img src="./images/<?= $r['image'] ?>" alt="" width="80"></td> -->
            <td><?= date("Y-m-d", strtotime($r['created_at'])) ?></td>
            <td><?= date("Y-m-d", strtotime($r['updated_at'])) ?></td>
            <td>
              <a href="edit-product.php?id=<?= $r['id'] ?>" class="btn btn-warning">編輯</a>
              <a href="javascript: deleteOne(<?= $r['id'] ?>)" class="btn btn-danger">刪除</a>
            </td>
          </tr>
        <?php endforeach ?>
      </tbody>
    </table>
  </div>
</div>