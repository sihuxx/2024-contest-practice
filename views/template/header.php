<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="view-transition" content="same-origin" />
  <title>전남</title>
  <link rel="stylesheet" href="/vendor/bootstrap-5.3.3-dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="/vendor/fontawesome-free-6.5.2-web/css/all.min.css">
  <link rel="stylesheet" href="/css/styles.css">
</head>

<body>
  <?php
  $user = ss();
  ?>

  <!-- 최상위 요소 -->
  <div id="app">
    <!-- 해더 영역 -->
    <header class="py-3 text-center shadow-sm">
      <div class="cont flex i-center gap-5">
        <a href="/" class="fs-3">Universal Festival</a>
        <ul class="flex me-auto">
          <li class="rel"><a href="/sub">문화관광축제</a></li>
          <li class="rel"><a href="/map">전국 축제</a></li>
          <li class="rel"><a href="/festivals">축제 안내</a></li>
          <li class="rel"><a href="/tour">축제 탐방</a></li>
        </ul>

        <div class="flex c gap-3">
          <?php if($user) { ?>
            <a href="/myPage" class="b bg-gradient"><span>마이페이지</span></a>
            <a href="/logout" class="b bg-gradient"><span>로그아웃</span></a>
          <?php } else { ?>
          <a href="/login" class="b bg-gradient"><span>로그인</span></a>
          <a href="/register" class="b bg-gradient"><span>회원가입</span></a>
          <?php } ?>
        </div>

      </div>
    </header>