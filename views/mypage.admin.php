<?php
$user = ss();
$festivals = db::fetchAll("select * from festivals order by end_date desc");
$tours = db::fetchAll("select * from tours");
?>

<main class="myPage-content">
  <?php if ($user->isAdmin == 0) { ?>
    <div class="tour-scene"></div>
    <div class="review-scene"></div>
  <?php } else if ($user->isAdmin == 1) { ?>
    <div class="festival-admin">
      <div class="title-text">
        <h1>축제 관리</h1>
      </div>
      <button onclick="toggleModal()">축제 추가</button>
      <div class="festival-grid">
        <?php foreach ($festivals as $festival) { ?>
          <div class="festival">
            <img src="<?= $festival->image ?>">
            <p class="bold"><?= $festival->name ?></p>
            <p>축제 기간: <?= $festival->start_date ?> ~ <?= $festival->end_date ?></p>
            <p>축제 장소: <?= $festival->address ?></p>
          </div>
        <?php } ?>
      </div>
    </div>
    <div class="tour-admin">
      <div class="title-text">
        <h1>탐방모집 관리</h1>
      </div>
      <div class="tour-list">
        <?php foreach ($tours as $tour) {
          $tour_user = db::fetch("select * from users where idx = '$tour->admin_user'"); 
          $festival = db::fetch("select * from festivals where idx = '$tour->festival'");
          ?>
          <div class="tour">
            <div class="tour-info">
              <p class="bold"><?= $tour->title ?></p>
              <p>탐방할 축제: <?= $festival->name ?></p>
              <p>탐방 날짜: <?= $tour->date ?></p>
              <p>최대 모집 인원: <?= $tour->max_people ?>명</p>
              <?php if($tour->isAccept === null) { ?>
                <form method="POST" class="btns">
                <input type="hidden" name="idx" value="<?= $tour->idx ?>">
                <button formaction="/tourAccept">수락</button>
                <button formaction="/tourReject">거절</button>
              </form>
              <?php } else  { ?>
              <p class="<?= $tour->isAccept === 1 ? 'accept-msg' : 'reject-msg' ?>">
                    <?php if(date("Y-m-d") > $tour->date): ?> 탐방이 완료되었습니다.
                    <?php elseif($tour->isAccept === 1): ?> 수락되었습니다.
                    <?php elseif($tour->isAccept === 0): ?> 거절되었습니다. 
                    <?php endif ?>
                  </p>
              <?php } ?>
            </div>
            <div class="profile">
              신청자:
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
    </div>
  <?php } ?>
</main>

<div class="default-modal">
  <div class="default-modal-header">
    <button onclick="toggleModal()">닫기</button>
  </div>
  <form action="/addFestival" method="post" enctype="multipart/form-data">
    <label>축제 이미지:<input type="file" name="file" required></label>
    <label>축제명:<input type="name" name="name" placeholder="축제명을 입력해주세요" required></label>
    <label>축제 시작일:<input type="date" name="start_date" placeholder="축제 시작일을 입력해주세요" required></label>
    <label>축제 종료일:<input type="date" name="end_date" placeholder="축제 종료일을 입력해주세요" required></label>
    <label>축제 장소:<input type="text" name="address" placeholder="축제 장소 입력해주세요" required></label>
    <button>추가하기</button>
  </form>
</div>

<script>
  const $ = e => document.querySelector(e);

  function toggleModal() {
    $(".default-modal").classList.toggle("toggle");
  }
</script>