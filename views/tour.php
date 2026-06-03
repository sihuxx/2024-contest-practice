<?php
$today = date("Y-m-d");
$festivals = db::fetchAll("select * from festivals where end_date >= '$today'");
$tours = db::fetchAll("select * from tours where isAccept = 1");
$user = ss();
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
            $festival = db::fetch("select * from festivals where idx = '$tour->festival'")
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
    <div class="title-text">
        <h1>탐방 후기</h1>
    </div>
    <div class="review-list">
        <div class="review">
            <?php foreach ($tours as $tour) { ?>
                <img src="">
                <div class="review-info">
                    <h1></h1>
                    <p>탐방 축제:</p>
                    <p>모집된 인원:</p>
                    <p>평균 별점:</p>
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