<?php
// require __DIR__ . "/parts/admin-required.php"; # 需要管理者權限
require __DIR__ . "/parts/db-connect.php";

$title = '新增商品';
$pageName = 'add-product';

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

  .upload-box {
    width: 300px;
    height: 275px;
    border: 2px dashed #ccc;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    transition: 0.3s;
  }

  .upload-box:hover {
    background-color: #f8f9fa;
  }

  .upload-box img {
    max-width: 100%;
    max-height: 100%;
    border-radius: 10px;
  }

  .upload-text {
    text-align: center;
    color: #888;
    font-size: 24px;
  }

  .upload-text i {
    font-size: 52px;
    color: #339af0;
  }

  .upload-text p {
    margin: 0;
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
          <li class="breadcrumb-item" aria-current="page">新增商品</li>
        </ol>
      </nav>
    </div>
  </div>

  <div class="row">
    <div class="col">
      <h5 class="mb-4 fs-2">新增商品資訊</h5>
      <form class="needs-validation" name="addProductForm" novalidate onsubmit="sendData(event)">
        <div class="mb-3 col-7">
          <label for="avatar" class="form-label">商品照片</label>
          <input class="form-control" type="file" name="avatar" accept="image/png,image/jpeg" />
        </div>

        <div class="upload-box mb-3" id="uploadBox">
          <img src="" alt="" id="preview" width="300" />
          <div class="upload-text">
            <i class="fas fa-cloud-upload-alt"></i>
            <p>圖片預覽區塊</p>
          </div>
        </div>

        <div class="mb-3 col-7">
          <label for="name" class="form-label"><span class="label-required">*</span> 商品名稱</label>
          <input type="text" class="form-control" id="name" name="name" minlength=2 placeholder=" 請輸入商品名稱" required>
          <div class="invalid-feedback">
            請輸入至少兩個字元
          </div>
        </div>

        <div class="mb-3 col-7">
          <label for="category" class="form-label"><span class="label-required">*</span> 商品分類</label>
          <select class="form-select" aria-label="Default select example" name="category" required>
            <option selected disabled value="">請選擇</option>
            <?php foreach ($category_rows as $row): ?>
              <option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
            <?php endforeach ?>
          </select>
          <div class="invalid-feedback">
            請選擇商品分類
          </div>
        </div>

        <div class="mb-3 col-7">
          <label for="stock" class="form-label"><span class="label-required">*</span> 庫存數量</label>
          <input type="number" class="form-control" id="stock" name="stock" min=0 required>
          <div class="invalid-feedback">
            請輸入正整數
          </div>
        </div>

        <div class="mb-3 col-7">
          <label for="price" class="form-label"><span class="label-required">*</span> 價格</label>
          <input type="number" class="form-control" id="price" name="price" min=1 required>
          <div class="invalid-feedback">
            請輸入正整數
          </div>
        </div>

        <div class="mb-3 col-7">
          <label for="status" class="form-label"><span class="label-required">*</span> 商品狀態</label>
          <select class="form-select" aria-label="Default select example" name="status" required>
            <option selected disabled value="">請選擇</option>
            <option value="1">上架</option>
            <option value="0">下架</option>
          </select>
          <div class="invalid-feedback">
            請選擇商品狀態
          </div>
        </div>

        <div class="mb-3 col-7">
          <label for="content" class="form-label"><span class="label-required">*</span> 商品介紹</label>
          <textarea class="form-control" id="content" name="content" required></textarea>
          <div class="invalid-feedback">
            此欄位為必填欄位
          </div>
        </div>

        <button type="submit" class="btn btn-primary ms-auto">新增</button>
      </form>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="addResultModal" tabindex="-1" aria-labelledby="addResultModalLabel">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="addResultModalLabel">新增結果</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="alert alert-success" role="alert">
          資料新增成功
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">繼續新增資料</button>
        <a href="product-list.php" class="btn btn-primary">回列表頁</a>
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/parts/html-scripts.php' ?>
<script>
  // 建立對應到 DOM 的 Modal 物件
  const addResultModal = new bootstrap.Modal('#addResultModal');

  // * 處理圖片
  const avatar = document.addProductForm.avatar;
  const preview = document.querySelector("#preview");
  const uploadText = document.querySelector(".upload-text");

  avatar.addEventListener("change", (e) => {
    if (avatar.files.length) {
      // 同步的方式載入檔案的內容預覽
      preview.src = URL.createObjectURL(avatar.files[0]);
      uploadText.style.display = 'none';
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
      const fd = new FormData(document.addProductForm);

      fetch('add-product-api.php', {
          method: 'POST',
          body: fd
        })
        .then(r => r.json())
        .then(result => {
          console.log(result);
          if (result.success) {
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

    // * 新增成功後，modal 關閉會刷新表單內容
    document.querySelector('#addResultModal').addEventListener('hidden.bs.modal', function() {
      document.addProductForm.reset(); // 清空表單
      document.addProductForm.classList.remove('was-validated'); // 移除 Bootstrap 驗證標記
      preview.src = "";
    });
  }
</script>
<?php include __DIR__ . '/parts/html-tail.php' ?>