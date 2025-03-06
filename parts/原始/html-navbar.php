<?php
if (!isset($pageName)) $pageName = '';
?>

<style>
  nav.navbar .navbar-nav a.nav-link.active {
    background-color: blue;
    color: white;
    border-radius: 6px;
    font-weight: 900;
  }
</style>

<div class="container">
  <nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
      <a class="navbar-brand" href="index_.php">Navbar</a>
      <button
        class="navbar-toggler"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent"
        aria-expanded="false"
        aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link <?= $pageName == 'home' ? 'active' : '' ?>" href="index_.php">首頁</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= $pageName == 'product-list' ? 'active' : '' ?>" href="product-list.php">商品管理</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</div>