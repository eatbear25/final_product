<!DOCTYPE html>
<html lang="zh-TW">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= empty($title) ? '海優斯健康管理平台' : "$title - 海優斯健康管理平台" ?></title>

  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
  <!--bootstrap-->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
  <!--bootstrap icon-->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css">
  <!--jquery-->
  <link rel="stylesheet" href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css">

  <style>
    body {
      display: flex;
      min-height: 100vh;
      margin: 0;
    }

    .sidebar {
      background-color: #f8f9fa;
      padding: 20px;
    }

    .sidebar-brand {
      text-align: center;
      margin-bottom: 20px;
    }

    .sidebar-nav .nav-link {
      padding: 10px;
      color: #333;
    }

    .list-group-item {
      border: none;
    }

    .nav-item.active,
    .list-group-item.active {
      background-color: #d0e4ff !important;
      border-radius: 10px;
      color: black !important;
      padding-left: 5px !important;
      margin: 5px !important;
    }

    .content {
      flex-grow: 1;
      padding: 20px;
    }

    .form-text {
      color: red;
      font-weight: bold;
    }
  </style>
</head>

<body class="bg-white">