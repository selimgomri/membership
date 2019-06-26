<?php

// Inserts the standard letterhead on PDF pages
$club = json_decode(CLUB_JSON);

?>

<div class="row mb-3">
  <div class="split-50">
    <?php if ($club->Logo != null) { ?>
    <img src="<?=BASE_PATH . $club->Logo?>" class="logo">
    <?php } else { ?>
      <h1 class="primary"><?=htmlspecialchars(env('CLUB_NAME'))?></h1>
    <?php } ?>
  </div>
  <div class="split-50 text-right">
    <p>
      <strong><?=htmlspecialchars(env('CLUB_NAME'))?></strong><br>
      <?php
      for ($i = 0; $i < sizeof($club->ClubAddress); $i++) { ?>
        <?=htmlspecialchars($club->ClubAddress[$i])?><br>
      <?php } ?>
    </p>
  </div>
</div>
