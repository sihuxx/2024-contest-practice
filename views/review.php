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
    ?>
      <div class="review">
        <?php if ($review->is_admin_review) { ?>
          <span class="admin-tag">탐방 운영자</span>
        <?php } ?>
        <div class="review-profile">
          <img src="<?= $user->profile ?>">
          <div class="profile-info">
            <p class="bold"><?= $user->name ?></p>
            <p><?= $user->id ?></p>
          </div>
        </div>
        <p><?= $review->content ?></p>
        <p>탐방 별점: <?= $review->rating ?>점</p>
      </div>
    <?php } ?>
  </div>
</main>