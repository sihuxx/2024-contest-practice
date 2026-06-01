<?php
$user = ss();
if (!$user) {
  back("로그인한 회원만 접근 가능한 페이지입니다");
}
$festival = db::fetch("select * from festivals where idx = '$idx'");
$tours = db::fetchAll("select * from tours where festival = '$festival->idx'")
?>

<main class="festival-content">
  <div class="title-text">
    <h1>축제 상세 정보</h1>
  </div>
  <div class="detail-content">
    <img src="<?= $festival->image ?>">
    <p class="bold"><?= $festival->name ?></p>
    <p>축제 기간:<?= $festival->start_date ?> ~ <?= $festival->end_date ?></p>
    <p>축제 장소:<?= $festival->address ?></p>
  </div>
  <div class="title-text">
    <h1>축제 탐방 목록</h1>
  </div>
  <div class="tour-list">
    <?php foreach ($tours as $tour) {
      $tour_user = db::fetch("select * from users where idx = '$tour->user_idx'");
      $tour_recruits = db::fetchAll("select * from recruits where tour_idx = '$tour->idx'");
      $festival = db::fetch("select * from festivals where idx = '$tour->festival'")
    ?>
      <div class="tour" style="cursor: pointer;">
        <div class="tour-info">
          <p class="bold"><?= $tour->title ?></p>
          <p>탐방 날짜: <?= $tour->date ?></p>
          <p>모집 인원: <?= count($tour_recruits) + 1 ?>/<?= $tour->max_people ?></p>
          <form method="POST" class="btns">
            <input type="hidden" name="tour_idx" value="<?= $tour->idx ?>">
            <input type="hidden" name="user_idx" value="<?= $user->idx ?>">
            <button formaction="/tourApply">가입 신청</button>
          </form>
        </div>
        <div class="profile">
          탐방 운영자:
          <div class="profile-info">
            <img src="<?= $tour_user->profile ?>">
            <div class="info">
              <p><?= $tour_user->name ?></p>
              <p><?= $tour_user->id ?></p>
            </div>
          </div>
        </div>
      </div>
    <?php } ?>
  </div>
</main>