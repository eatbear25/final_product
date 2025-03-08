<?php
// require __DIR__ . "/parts/admin-required.php"; # 需要管理者權限
require __DIR__ . "/parts/db-connect.php";
$title = '編輯商品';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
  # 讀取單筆資料
  $sql = "SELECT * FROM product WHERE id=$id ";
  $row = $pdo->query($sql)->fetch();
}

// echo "<pre>";
// print_r($row);

// exit;

if (empty($row)) {
  header("Location: product-list.php"); # 沒拿到該筆資料時, 回列表頁
}

// * 取得產品分類資料
$category_sql = " SELECT * FROM `product_category` ";

try {
  $category_rows = $pdo->query($category_sql)->fetchAll();
} catch (PDOException $ex) {
  echo '<h1>' . $ex->getMessage() . '</h1>';
  echo '<h2>' . $ex->getCode() . '</h2>';
}
?>



<?php include __DIR__ . '/parts/html-head.php' ?>

<style>
  form .label-required {
    color: red;
  }

  .breadcrumb-item a {
    text-decoration: none;
    /* color: #444; */
  }

  h5 {
    border-bottom: 3px solid #339af0;
    padding-bottom: 16px;
  }
</style>

<?php include __DIR__ . '/parts/html-sidebar.php' ?>
<div class="container mb-5">
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
          <li class="breadcrumb-item">
            <a href="product-list.php">
              商品管理
            </a>
          </li>
          <li class="breadcrumb-item" aria-current="page">編輯商品</li>
        </ol>
      </nav>
    </div>
  </div>

  <div class="row">
    <div class="col">
      <h5 class="mb-4 fs-2">編輯商品資訊</h5>

      <form class="needs-validation" name="editProductForm" novalidate onsubmit="sendData(event)">
        <input type="hidden" name="id" value="<?= $row['id'] ?>">
        <div class="mb-3 col-7">
          <label for="" class="form-label">編號</label>
          <input type="text" class="form-control"
            value="<?= $row['id'] ?>" disabled>
        </div>

        <div class="mb-3 col-7">
          <label for="avatar" class="form-label">商品照片</label>
          <input class="form-control" type="file" name="avatar" accept="image/png,image/jpeg" />
          <br />

          <p>目前照片</p>
          <img src="./product_images/<?= $row['image'] ?>" alt="" id="preview" width="300" />
          <br />
        </div>

        <div class="mb-3 col-7">
          <label for="name" class="form-label"><span class="label-required">*</span> 商品名稱</label>
          <input type="text" class="form-control" id="name" name="name" minlength=2 placeholder=" 請輸入商品名稱" value="<?= $row['name'] ?>" required>
          <div class="invalid-feedback">
            請輸入至少兩個字元
          </div>
        </div>

        <div class="mb-3 col-7">
          <label for="category" class="form-label"><span class="label-required">*</span> 商品分類</label>
          <select class="form-select" aria-label="Default select example" name="category" required>
            <option disabled value="">請選擇</option>
            <!-- 比對 $row['product_category_id'] == $r['id'] -->
            <?php foreach ($category_rows as $r): ?>
              <option value="<?= $r['id'] ?>" <?= $row['product_category_id'] == $r['id'] ? 'selected' : '' ?>><?= $r['name'] ?></option>
            <?php endforeach ?>
          </select>
          <div class="invalid-feedback">
            請選擇商品分類
          </div>
        </div>

        <div class="mb-3 col-7">
          <label for="stock" class="form-label"><span class="label-required">*</span> 庫存數量</label>
          <input type="number" class="form-control" id="stock" name="stock" min=0 value="<?= $row['stock'] ?>" required>
          <div class=" invalid-feedback">
            請輸入正整數
          </div>
        </div>

        <div class="mb-3 col-7">
          <label for="price" class="form-label"><span class="label-required">*</span> 價格</label>
          <input type="number" class="form-control" id="price" name="price" min=1 value="<?= $row['price'] ?>" required>
          <div class="invalid-feedback">
            請輸入正整數
          </div>
        </div>

        <div class="mb-3 col-7">
          <label for="status" class="form-label"><span class="label-required">*</span> 商品狀態</label>
          <select class="form-select" aria-label="Default select example" name="status" required>
            <option disabled value="">請選擇</option>
            <option value="1" <?= $row['status'] == 1 ? 'selected' : '' ?>>上架</option>
            <option value="0" <?= $row['status'] == 0 ? 'selected' : '' ?>>下架</option>
          </select>
          <div class="invalid-feedback">
            請選擇商品狀態
          </div>
        </div>

        <div class="mb-3 col-7">
          <label for="content" class="form-label"><span class="label-required">*</span> 商品介紹</label>
          <textarea class="form-control" id="content" name="content" required><?= $row['content'] ?></textarea>
          <div class="invalid-feedback">
            此欄位為必填欄位
          </div>
        </div>

        <button type="submit" class="btn btn-primary">修改</button>
      </form>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="addResultModal" tabindex="-1" aria-labelledby="addResultModalLabel">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="addResultModalLabel">編輯結果</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="alert alert-success" role="alert">
          資料編輯成功
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">繼續編輯</button>
        <a href="javascript: myBack() " class="btn btn-primary">回列表頁</a>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/parts/html-scripts.php' ?>
<script>
  const myBack = () => {
    console.log('document.referer:', document.referrer);

    if (document.referrer) {
      location.href = document.referrer
    } else {
      location.href = 'product-list.php'
    }
  }

  // 建立對應到 DOM 的 Modal 物件
  const addResultModal = new bootstrap.Modal('#addResultModal');
  const modalInfo = document.querySelector('#addResultModal .alert');

  // * 處理圖片
  const avatar = document.editProductForm.avatar;
  const preview = document.querySelector("#preview");

  avatar.addEventListener("change", (e) => {
    if (avatar.files.length) {
      // 同步的方式載入檔案的內容預覽
      preview.src = URL.createObjectURL(avatar.files[0]);
    } else {
      preview.src = "";
    }
  });

  // Bootstrap 驗證區
  (function() {
    'use strict'

    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    var forms = document.querySelectorAll('.needs-validation')

    // Loop over them and prevent submission
    Array.prototype.slice.call(forms)
      .forEach(function(form) {
        form.addEventListener('submit', function(event) {
          if (!form.checkValidity()) {
            event.preventDefault()
            event.stopPropagation()
          }

          form.classList.add('was-validated')
        }, false)
      })
  })()

  const sendData = e => {
    e.preventDefault();

    let isPass = true; // 有沒有通過檢查

    if (isPass) {
      // 如果全部要檢查的欄位都通過檢查
      const fd = new FormData(document.editProductForm);

      fetch('edit-product-api.php', {
          method: 'POST',
          body: fd
        })
        .then(r => r.json())
        .then(result => {
          console.log(result);

          if (result.no_changes) {
            modalInfo.classList.remove('alert-success');
            modalInfo.classList.add('alert-warning');
            modalInfo.innerHTML = "資料沒有修改";
            addResultModal.show(); // 顯示 Modal
            return;
          }

          if (result.success) {
            modalInfo.classList.add('alert-success');
            modalInfo.classList.remove('alert-warning');
            modalInfo.innerHTML = "修改成功";
            addResultModal.show(); // 顯示 Modal
            return;
          }

          if (result.error) {
            alert(result.error);
          }
        })
        .catch(ex => {
          console.warn('Fetch 出錯了!');
          console.warn(ex);
        })
    }
  }
</script>
<?php include __DIR__ . '/parts/html-tail.php' ?>