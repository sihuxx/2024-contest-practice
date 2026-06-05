<?php
$today = date("Y-m-d");
$festivals = db::fetchAll("select * from festivals where end_date >= '$today'");
$tours = db::fetchAll("select * from tours where isAccept = 1 and isCompleted = 0");
$user = ss();
$reviewTours = db::fetchAll("select t.*, ROUND(AVG(r.rating), 1) as avg_rating from tours t inner join reviews r on t.idx = r.tour_idx where t.isCompleted = 1 group by t.idx order by t.date desc")
?>

<main class="tour-content">
    <div class="title-text">
        <h1>축제 탐방</h1>
    </div>
    <button onclick="controlModal()">탐방 모집</button>
    <div class="tour-list">
        <?php foreach ($tours as $tour) {
            $tour_user = db::fetch("select * from users where idx = '$tour->admin_user'");
            $tour_members = db::fetchAll("select * from applys where tour_idx = '$tour->idx' and status = 1");
            $festival = db::fetch("select * from festivals where idx = '$tour->festival'");
              $tour_user_rating = db::fetch("select ROUND(AVG(rating), 1) rating from member_ratings where target_user_idx = '$tour_user->idx'");
      $tour_user_complete = db::fetchAll("select t.idx, a.* from tours t inner join applys a on t.idx = a.tour_idx where a.user_idx = '$tour_user->idx' and a.status = 1 and t.isCompleted = 1");
      $tour_user_admin = db::fetchAll("select * from tours where admin_user = '$tour_user->idx' and isCompleted = 1");
        ?>
            <div class="tour" onclick="location.href = '/festival/<?= $tour->festival ?>'" style="cursor: pointer;">
                <div class="tour-info">
                    <p class="bold"><?= $tour->title ?></p>
                    <p>탐방할 축제: <?= $festival->name ?></p>
                    <p>탐방 날짜: <?= $tour->date ?></p>
                    <p>모집 인원: <?= count($tour_members) + 1 ?>/<?= $tour->max_people ?></p>
                    <form method="POST" class="btns">
                        <input type="hidden" name="tour_idx" value="<?= $tour->idx ?>">
                        <button formaction="/tourApply">가입 신청</button>
                    </form>
                </div>
                <div class="profile" onclick="event.stopPropagation(); document.querySelector('.profile-modal').style.display = 'flex'">
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
            <div class="default-modal profile-modal">
                <div class="profile-header">
                    <button onclick="document.querySelector('.profile-modal').style.display = 'none'">닫기</button>
                </div>
                <div class="profile-con">
                    <div class="profile-info">
                        <img class="profile-img" src="<?= $tour_user->profile ?>">
                        <h3><?= $tour_user->name ?></h3>
                        <p><?= $tour_user->id ?></p>
                    </div>
                    <div class="profile-info">
                        <p>생년월일: <?= $tour_user->birth ?></p>
                        <p>평점: <?= $tour_user_rating->rating ?></p>
                        <p>탐방 운영 횟수: <?= count($tour_user_complete) + count($tour_user_admin) ?>회</p>
                        <p>탐방 완료 횟수: <?= count($tour_user_admin) ?>회</p>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
    <div class="title-text">
        <h1>탐방 후기</h1>
    </div>
    <div class="review-list">
        <div class="tour-list">
            <?php foreach ($reviewTours as $tour) {
                $tour_member = db::fetchAll("select * from applys where tour_idx = '$tour->idx'");
                $festival = db::fetch("select * from festivals where idx = '$tour->festival'");
            ?>
                <div class="tour" style="cursor: pointer;" onclick="location.href = '/review/<?= $tour->idx ?>'">
                    <img src="<?= $festival->image ?>">
                    <div class="tour-info">
                        <p class="bold"><?= $tour->title ?></p>
                        <p>탐방 축제: <?= $festival->name ?></p>
                        <p>모집된 인원: <?= count($tour_member) + 1 ?>명</p>
                        <p>평균 별점: <?= $tour->avg_rating ?>점</p>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</main>

<div class="default-modal">
    <div class="default-modal-header">
        <button onclick="controlModal()">닫기</button>
    </div>
    <form action="/addTour" method="post">
        <label>탐방 축제:
            <select name="festival" onchange="festivalChange()">
                <?php foreach ($festivals as $festival) { ?>
                    <option value="<?= $festival->idx ?>"><?= $festival->name ?></option>
                <?php } ?>
            </select>
        </label>
        <label>탐방 제목: <input type="text" name="title" placeholder="탐방 제목을 입력해주세요" required></label>
        <label>탐방 날짜: <input type="date" name="date" placeholder="탐방 날짜를 입력해주세요" required></label>
        <label>최대 모집 인원: <input type="number" min="2" name="max_people" placeholder="최대 모집 인원을 입력해주세요" required></label>
        <button>모집하기</button>
    </form>
</div>

<script>
    const $ = e => document.querySelector(e)

    function controlModal() {
        $(".default-modal").classList.toggle("toggle");
    }

    function festivalChange() {
        const select = $("select");
        const festivals = <?= json_encode($festivals) ?>;
        const selected = festivals.find(f => f.idx == select.value);
        const dateInput = $("input[name='date']");

        dateInput.onclick = () => {
            if (!select.value) {
                alert("축제를 선택해주세요");
                return;
            }
        }

        dateInput.value = '';
        dateInput.min = selected.start_date;
        dateInput.max = selected.end_date;
    }
    festivalChange();
</script>