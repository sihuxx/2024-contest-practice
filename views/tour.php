<?php
$today = "2024-09-01";
$festivals = db::fetchAll("select * from festivals where end_date >= '$today'");
$tours = db::fetchAll("select * from tours where status = 1");
$user = ss()
?>

<main class="tour-content">
    <div class="title-text">
        <h1>축제 탐방</h1>
    </div>
    <button onclick="controlModal()">탐방 모집</button>
    <div class="tour-list">
        <?php foreach ($tours as $tour) {
            $tour_user = db::fetch("select * from users where idx = '$tour->user_idx'"); ?>
            <div class="tour" onclick="location.href = '/festival/<?= $tour->festival ?>'" style="cursor: pointer;">
                <div class="tour-info">
                    <p class="bold"><?= $tour->title ?></p>
                    <p>탐방할 축제: <?= $tour->festival ?></p>
                    <p>탐방 날짜: <?= $tour->date ?></p>
                    <p>모집 인원: <?= $tour->people ?>/<?= $tour->max_people ?></p>
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
    <div class="default-modal">
        <div class="default-modal-header">
            <button onclick="controlModal()">닫기</button>
        </div>
        <form action="/addTour" method="post">
            <input type="hidden" name="user_idx" value="<?= $user->idx ?>">
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
</main>

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