<?php
$user = ss();
$isManager = db::fetch("select * from tours where admin_user = '$user->idx' and isAccept = 1 and isCompleted = 0");
$isMember = db::fetch("select a.* from applys a inner join tours t on a.tour_idx = t.idx where a.user_idx = '$user->idx' and a.status = 1 and t.isCompleted = 0");
$completeTours = db::fetchAll("select t.*, r.idx as review_idx from tours t left join reviews r on t.idx = r.tour_idx and r.user_idx = '$user->idx' where (t.admin_user = '$user->idx' or t.idx in (select tour_idx from applys where user_idx = '$user->idx' and status = 1)) and t.isCompleted = 1 group by t.idx order by t.date desc");
?>
<?php foreach ($completeTours as $tour) { ?>
  <?= $tour->idx ?> / <?= $tour->review_idx ?> <br>
<?php } ?>

<main class="myPage-content">
  <div class="tour-list">
    <div class="title-text">
      <h1>탐방 목록</h1>
    </div>
    <?php if (db::fetch("select * from tours where admin_user = '$user->idx' and isAccept is null")): ?> 신청하신 축제 탐방 모집을 관리자가 검토 중입니다.
    <?php elseif (db::fetch("select * from applys where user_idx = '$user->idx' and status = 0")): ?> 신청하신 축제 탐방 가입을 운영자가 검토 중입니다.
    <?php elseif ($isManager || $isMember):
      $tour = $isManager ?: db::fetch("select * from tours where idx = '$isMember->tour_idx'");
      $tour_members = db::fetchAll("select a.*, u.name, u.id, u.profile from applys a inner join users u on u.idx = a.user_idx where a.status = 1 and a.tour_idx = '$tour->idx'");
      $festival = db::fetch("select * from festivals where idx = '$tour->festival'");

      $admin_user = db::fetch("select * from users where idx = '$tour->admin_user'");
      $admin_rating = db::fetch("select ROUND(AVG(rating), 1) rating from member_ratings where target_user_idx = '$admin_user->idx'");
      $admin_complete = db::fetchAll("select t.idx, a.* from tours t inner join applys a on t.idx = a.tour_idx where a.user_idx = '$admin_user->idx' and a.status = 1 and t.isCompleted = 1");
      $admin_admin = db::fetchAll("select * from tours where admin_user = '$admin_user->idx' and isCompleted = 1");
    ?>
      <div class="tour" onclick="location.href = '/festival/<?= $tour->festival ?>'" style="cursor: pointer;">
        <div class="tour-info">
          <p class="bold"><?= $tour->title ?></p>
          <p>탐방할 축제: <?= $festival->name ?></p>
          <p>탐방 날짜: <?= $tour->date ?></p>
          <p>모집 인원: <?= count($tour_members) + 1 ?>/<?= $tour->max_people ?></p>
          <?php if ($isManager && date("Y-m-d") >= $tour->date) { ?>
            <button onclick="event.stopPropagation(); document.querySelector('.admin-review-modal').style.display = 'flex'">탐방 완료</button>
          <?php } ?>
        </div>
      </div>

      <div class="default-modal admin-review-modal">
        <button onclick="document.querySelector('.admin-review-modal').style.display = 'none'">닫기</button>
        <form action="/addAdminReview" method="post">
          <input type="hidden" name="tour_idx" value="<?= $tour->idx ?>">
          <label>탐방 별점: <input type="range" name="rating" min="1" max="5"></label>
          <label>후기: <input type="text" name="content" placeholder="탐방 후기를 입력해주세요"></label>
          <?php foreach ($tour_members as $member) { ?>
            <div class="review-profile">
              <div class="profile-info">
                <p class="bold"><?= $member->name ?></p>
                <p><?= $member->id ?></p>
              </div>
              <input type="range" name="member_rating[<?= $member->idx ?>]" min="1" max="5">
            </div>
          <?php } ?>
          <button>완료</button>
        </form>
      </div>

  </div>
  <div class="member-list">
    <div class="title-text">
      <h1>멤버 목록</h1>
    </div>
    <div class="member-profile" onclick="document.querySelector('.profile-modal').style.display = 'flex'">
      <img src="<?= $admin_user->profile ?>">
      <div class="member-info">
        <span>탐방 운영자</span>
        <h3><?= $admin_user->name ?></h3>
        <p><?= $admin_user->id ?></p>
      </div>
    </div>

    <div class="default-modal profile-modal">
      <div class="profile-header">
        <button onclick="document.querySelector('.profile-modal').style.display = 'none'">닫기</button>
      </div>
      <div class="profile-con">
        <div class="profile-info">
          <img class="profile-img" src="<?= $admin_user->profile ?>">
          <h3><?= $admin_user->name ?></h3>
          <p><?= $admin_user->id ?></p>
        </div>
        <div class="profile-info">
          <p>생년월일: <?= $admin_user->birth ?></p>
          <p>평점: <?= $admin_rating->rating ?></p>
          <p>탐방 운영 횟수: <?= count($admin_complete) + count($admin_admin) ?>회</p>
          <p>탐방 완료 횟수: <?= count($admin_admin) ?>회</p>
        </div>
      </div>
    </div>

    <?php foreach ($tour_members as $member) {
        $member_rating = db::fetch("select ROUND(AVG(rating), 1) rating from member_ratings where target_user_idx = '$member->idx'");
        $member_complete = db::fetchAll("select t.idx, a.* from tours t inner join applys a on t.idx = a.tour_idx where a.user_idx = '$member->idx' and a.status = 1 and t.isCompleted = 1");
        $member_admin = db::fetchAll("select * from tours where admin_user = '$member->idx' and isCompleted = 1");
    ?>
      <div class="member-profile" onclick="document.querySelector('.profile-modal').style.display = 'flex'">
        <img src="<?= $member->profile ?>">
        <div class="member-info">
          <h3><?= $member->name ?></h3>
          <p><?= $member->id ?></p>
        </div>
      </div>

      <div class="default-modal profile-modal">
        <div class="profile-header">
          <button onclick="document.querySelector('.profile-modal').style.display = 'none'">닫기</button>
        </div>
        <div class="profile-con">
          <div class="profile-info">
            <img class="profile-img" src="<?= $member->profile ?>">
            <h3><?= $member->name ?></h3>
            <p><?= $member->id ?></p>
          </div>
          <div class="profile-info">
            <p>생년월일: <?= $member->birth ?></p>
            <p>평점: <?= $member_rating->rating ?></p>
            <p>탐방 운영 횟수: <?= count($member_complete) + count($member_admin) ?>회</p>
            <p>탐방 완료 횟수: <?= count($member_admin) ?>회</p>
          </div>
        </div>
      </div>
    <?php } ?>
  </div>
  <?php if ($isManager && count($tour_members) + 1 < $tour->max_people && date("Y-m-d") < $tour->date):
        $apply_members = db::fetchAll("select a.*, u.profile, u.name, u.id from applys a inner join users u on a.user_idx = u.idx where a.status = 0 and a.tour_idx = '$isManager->idx'");
  ?>
    <div class="apply-list">
      <div class="title-text">
        <h1>신청 목록</h1>
      </div>
      <?php foreach ($apply_members as $member) {
          $member_rating = db::fetch("select ROUND(AVG(rating), 1) rating from member_ratings where target_user_idx = '$member->idx'");
          $member_complete = db::fetchAll("select t.idx, a.* from tours t inner join applys a on t.idx = a.tour_idx where a.user_idx = '$member->idx' and a.status = 1 and t.isCompleted = 1");
          $member_admin = db::fetchAll("select * from tours where admin_user = '$member->idx' and isCompleted = 1");
      ?>
        <div class="member-profile" onclick="document.querySelector('.profile-modal').style.display = 'flex'">
          <img src="<?= $member->profile ?>">
          <div class="member-info">
            <h3><?= $member->name ?></h3>
            <p><?= $member->id ?></p>
            <form method="post">
              <input type="hidden" name="user_idx" value="<?= $member->user_idx ?>">
              <input type="hidden" name="tour_idx" value="<?= $member->tour_idx ?>">
              <button formaction="/applyAccept">수락</button>
              <button formaction="/applyReject">거절</button>
            </form>
          </div>
        </div>

        <div class="default-modal profile-modal">
          <div class="profile-header">
            <button onclick="document.querySelector('.profile-modal').style.display = 'none'">닫기</button>
          </div>
          <div class="profile-con">
            <div class="profile-info">
              <img class="profile-img" src="<?= $member->profile ?>">
              <h3><?= $member->name ?></h3>
              <p><?= $member->id ?></p>
            </div>
            <div class="profile-info">
              <p>생년월일: <?= $member->birth ?></p>
              <p>평점: <?= $member_rating->rating ?></p>
              <p>탐방 운영 횟수: <?= count($member_complete) + count($member_admin) ?>회</p>
              <p>탐방 완료 횟수: <?= count($member_admin) ?>회</p>
            </div>
          </div>
        </div>
      <?php } ?>
    </div>
  <?php endif ?>
<?php else: ?> 활동 중인 축제 탐방이 존재하지 않습니다.
<?php endif ?>
<div class="review-list">
  <div class="title-text">
    <h1>탐방 후기</h1>
  </div>
  <div class="tour-list">
    <?php foreach ($completeTours as $tour) {
      $festival = db::fetch("select * from festivals where idx = '$tour->festival'");
      $review_members = db::fetchAll("select u.*, a.tour_idx from users u inner join applys a on u.idx = a.user_idx where a.status = 1 and a.tour_idx = '$tour->idx'");
      $admin_user = db::fetch("select * from users where idx = '$tour->admin_user'");
    ?>
      <div class="tour" style="cursor: pointer;">
        <img src="<?= $festival->image ?>">
        <div class="tour-info">
          <p class="bold"><?= $tour->title ?></p>
          <p>탐방 축제: <?= $festival->name ?></p>
          <p>탐방 날짜: <?= $tour->date ?></p>
          <?php if (!$tour->review_idx) { ?>
            <button onclick="document.querySelector('.tour-<?= $tour->idx ?>').style.display = 'flex'">후기 작성</button>
          <?php } else { ?>
            <button onclick="location.href = '/review/<?= $tour->idx ?>'">후기 보기</button>
          <?php } ?>
        </div>
      </div>
      <div class="default-modal tour-<?= $tour->idx ?>">
        <button onclick="document.querySelector('.tour-<?= $tour->idx ?>').style.display = 'none'">닫기</button>
        <form action="/addReview" method="post">
          <input type="hidden" name="tour_idx" value="<?= $tour->idx ?>">
          <label>탐방 별점: <input type="range" name="rating" min="1" max="5"></label>
          <label>후기: <input type="text" name="content" placeholder="탐방 후기를 입력해주세요"></label>
          <div class="review-profile">
            <div class="profile-info">
              <p class="bold"><?= $admin_user->name ?> <span class="admin-tag">탐방 운영자</span></p>
              <p><?= $admin_user->id ?></p>
            </div>
            <input type="range" name="member_rating[<?= $admin_user->idx ?>]" min="1" max="5">
          </div>
          <?php foreach ($review_members as $member) { ?>
            <div class="review-profile">
              <div class="profile-info">
                <p class="bold"><?= $member->name ?></p>
                <p><?= $member->id ?></p>
              </div>
              <input type="range" name="member_rating[<?= $member->idx ?>]" min="1" max="5">
            </div>
          <?php } ?>
          <button>등록하기</button>
        </form>
      </div>
    <?php } ?>
  </div>
</div>
</main>


<!-- 
  $a ?: $b
  // 같은 의미
  $a ? $a : $b

  event.stopPropagation(): 클릭 이벤트가 부모로 올라가는 걸 막음
-->