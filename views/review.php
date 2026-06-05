<?php
$tour = db::fetch("select * from tours where idx = '$idx'");
$festival = db::fetch("select * from festivals where idx = '$tour->festival'");
$reviews = db::fetchAll("select * from reviews where tour_idx = '$idx' order by is_admin_review desc");
$avg_rating = db::fetch("select ROUND(AVG(rating), 1) rating from reviews where tour_idx = '$idx'");
?>

<main class="review-content">
  <div class="title-text">
    <h1>후기 상세</h1>
  </div>
  <div class="tour-title">
    <h1><?= $tour->title ?></h1>
    <p>탐방 축제: <?= $festival->name ?></p>
    <p>평균 별점: <?= $avg_rating->rating ?>점 </p>
  </div>
  <div class="review-list">
    <?php foreach ($reviews as $review) {
      $user = db::fetch("select * from users where idx = '$review->user_idx'");
      $user_rating = db::fetch("select ROUND(AVG(rating), 1) rating from member_ratings where target_user_idx = '$user->idx'");
      $user_complete = db::fetchAll("select t.idx, a.* from tours t inner join applys a on t.idx = a.tour_idx where a.user_idx = '$user->idx' and a.status = 1 and t.isCompleted = 1");
      $user_admin = db::fetchAll("select * from tours where admin_user = '$user->idx' and isCompleted = 1");
    ?>
      <div class="review">
        <?php if ($review->is_admin_review) { ?>
          <span class="admin-tag">탐방 운영자</span>
        <?php } ?>
        <div class="review-profile" onclick="document.querySelector('.profile-modal').style.display = 'flex'">
          <img src="<?= $user->profile ?>">
          <div class="profile-info">
            <p class="bold"><?= $user->name ?></p>
            <p><?= $user->id ?></p>
          </div>
        </div>
        <p><?= $review->content ?></p>
        <p>탐방 별점: <?= $review->rating ?>점</p>
      </div>
      <div class="default-modal profile-modal">
        <div class="profile-header">
          <button onclick="document.querySelector('.profile-modal').style.display = 'none'">닫기</button>
        </div>
        <div class="profile-con">
          <div class="profile-info">
            <img class="profile-img" src="<?= $user->profile ?>">
            <h3><?= $user->name ?></h3>
            <p><?= $user->id ?></p>
          </div>
          <div class="profile-info">
            <p>생년월일: <?= $user->birth ?></p>
            <p>평점: <?= $user_rating->rating ?></p>
            <p>탐방 운영 횟수: <?= count($user_complete) + count($user_admin) ?>회</p>
            <p>탐방 완료 횟수: <?= count($user_admin) ?>회</p>
          </div>
        </div>
      </div>
    <?php } ?>
  </div>
</main>