<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://code.jquery.com/ui/1.14.1/jquery-ui.js"></script>
<script>
  $(function() {
    let currentPath = window.location.pathname.split("/").pop(); // 取得當前頁面的檔案名稱

    $(".nav-link,.list-group-item").each(function() {
      let linkPath = $(this).attr("href").split("/").pop(); // 取得 <a> 的 href 檔案名稱
      if (linkPath === currentPath) {
        $(this).addClass("active"); // 為符合當前頁面的 <a> 加上 active
        $(this).closest(".nav-item").addClass("active"); // 也加到父級 nav-item，避免樣式問題
        $(this).closest(".collapse").addClass("show");
        if ($(this).closest(".collapse").length) {
          $(this).closest(".nav-item").removeClass("active");
        }
      }
    });
  });
</script>