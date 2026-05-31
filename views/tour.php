<?php
$today = "2024-09-01";
$festivals = db::fetchAll("select * from festivals where end_date >= '$today'");
?>

<main class="tour-content">
    <button onclick="controlModal()">탐방 모집</button>
    <div class="tour-list">

    </div>
    <div class="tour-modal">
        <div class="tour-modal-header">
            <button onclick="controlModal()">닫기</button>
        </div>
        <form action="" method="post">
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
        $(".tour-modal").classList.toggle("toggle");
    }

    function festivalChange() {
        const select = $("select");
        const festivals = <?= json_encode($festivals) ?>;
        const selected = festivals.find(f => f.idx == select.value);
        const dateInput = $("input[name='date']");

        dateInput.onclick = () => {
            if (!selected.value) {
                alert("축제를 선택해주세요");
                return;
            }
        }

        dateInput.min = selected.start_date;
        dateInput.max = selected.end_date;
    }
    festivalChange();
</script>