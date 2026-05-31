<?php
$user = ss();
if(!$user) {
  back("로그인한 회원만 접근 가능한 페이지입니다");
}
$festival = db::fetch("select * from festivals where idx = '$idx'");
?>

<main class="festival-content">
  <div class="detail-content">
    <img src="<?=$festival->image?>">
    <p class="bold"><?=$festival->name?></p>
    <p>축제 기간:<?=$festival->start_date?> ~ <?=$festival->end_date?></p>
    <p>축제 장소:<?=$festival->address?></p>
  </div>
  <div class="tour-content"></div>
</main>