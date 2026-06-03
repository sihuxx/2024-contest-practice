<?php
$user = ss();
$isManager = db::fetch("select * from tours where admin_user = '$user->idx' and isAccept = 1 and isCompleted = 0");
$isMember = db::fetch("select a.* from applys a inner join tours t on a.tour_idx = t.idx where a.user_idx = '$user->idx' and a.status = 1 and t.isCompleted = 0");

?>

<main class="myPage-content">
  <div class="tour-list">
    <div class="title-text">
      <h1>탐방 목록</h1>
    </div>
    <p class="tour-msg">
      <?php if (db::fetch("select * from tours where admin_user = '$user->idx' and isAccept is null")): ?> 신청하신 축제 탐방 모집을 관리자가 검토 중입니다.
      <?php elseif (db::fetch("select * from applys where user_idx = '$user->idx' and status = 0")): ?> 신청하신 축제 탐방 가입을 운영자가 검토 중입니다.
      <?php elseif ($isManager || $isMember):
        $tour = $isManager ?: db::fetch("select * from tours where idx = '$isMember->tour_idx'");
        $tour_members = db::fetchAll("select a.*, u.name, u.id, u.profile from applys a inner join users u on u.idx = a.user_idx where a.status = 1 and a.tour_idx = '$tour->idx'");
        $festival = db::fetch("select * from festivals where idx = '$tour->festival'");
        $admin_user = db::fetch("select * from users where idx = '$tour->admin_user'");
      ?>
    <div class="tour" onclick="location.href = '/festival/<?= $tour->festival ?>'" style="cursor: pointer;">
      <div class="tour-info">
        <p class="bold"><?= $tour->title ?></p>
        <p>탐방할 축제: <?= $festival->name ?></p>
        <p>탐방 날짜: <?= $tour->date ?></p>
        <p>모집 인원: <?= count($tour_members) + 1 ?>/<?= $tour->max_people ?></p>
        <?php if ($isManager && date("Y-m-d") >= $tour->date) { ?>
          <button>탐방 완료</button>
        <?php } ?>
      </div>
    </div>
  </div>
  <div class="member-list">
    <div class="title-text">
      <h1>멤버 목록</h1>
    </div>
    <div class="member-profile">
      <img src="<?= $admin_user->profile ?>">
      <div class="member-info">
        <span>탐방 운영자</span>
        <h3><?= $admin_user->name ?></h3>
        <p><?= $admin_user->id ?></p>
      </div>
    </div>
    <?php foreach ($tour_members as $member) { ?>
      <div class="member-profile">
        <img src="<?= $member->profile ?>">
        <div class="member-info">
          <h3><?= $member->name ?></h3>
          <p><?= $member->id ?></p>
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
      <?php foreach ($apply_members as $member) { ?>
        <div class="member-profile">
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
      <?php } ?>
    </div>
  <?php endif ?>
<?php else: ?> 활동 중인 축제 탐방이 존재하지 않습니다.
<?php endif ?>
</p>
</main>

<!-- 
  $a ?: $b
  // 같은 의미
  $a ? $a : $b
-->