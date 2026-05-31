<?php
  $festivals = db::fetchAll('select * from festivals order by end_date desc');
?>

<main class="guide-content">
  <div class="festival-grid">
    <?php foreach($festivals as $festival) { ?>
      <div onclick="location.href = '/festival/<?=$festival->idx ?>'" class="festival">
      <img src="<?=$festival->image?>">
      <p class="bold"><?=$festival->name?></p>
      <p>축제 기간: <?=$festival->start_date?> ~ <?=$festival->end_date?></p>
      <p>축제 장소: <?= $festival->address ?></p>
    </div>
    <?php } ?>
  </div>
</main>