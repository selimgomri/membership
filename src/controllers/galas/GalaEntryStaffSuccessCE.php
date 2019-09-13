<?php

global $db;

$getSwimmer = $db->prepare("SELECT MForename, MSurname FROM members WHERE MemberID = ?");
$getSwimmer->execute([
  $swimmer
]);
$row = $getSwimmer->fetch(PDO::FETCH_ASSOC);

if ($row == null) {
  halt(404);
}

$pagetitle = htmlspecialchars($row['MForename']) . "'s Selected Sessions";

include BASE_PATH . "views/header.php";

?>

<div class="container">
  <div class="row">
    <div class="col-md-8">
      <h1>
        <?=htmlspecialchars($row['MForename'])?>'s Selected Sessions
      </h1>

      <?php if ($_SESSION['SuccessStatus']) { ?>
      <p>
        Your selection has been saved. Use the coach entry system to choose swims.
      </p>
      <?php } else { ?>
      <p>
        An error occurred which meant your choices were not saved.
      </p>
      <?php } unset($_SESSION['SuccessStatus']); ?>

      <div class="cell">
        <h3>Make another entry for <?=htmlspecialchars($row['MForename'])?></h3>

        <p>Return to the entry form to make another entry for <?=htmlspecialchars($row['MForename'])?>.</p>

        <p class="mb-0">
          <a href="<?=autoUrl("swimmers/" . $swimmer . "/enter-gala")?>" class="btn btn-primary">
            Make another entry
          </a>
        </p>
      </div>

      <div class="cell">
        <h3>If you're finished here</h3>

        <p>If you've finished making entries, return to the gala homepage or return to the page for <?=htmlspecialchars($row['MForename'])?>.</p>

        <p class="mb-0">
          <a href="<?=autoUrl("galas")?>" class="btn btn-primary">
            Gala home
          </a>
          <a href="<?=autoUrl("swimmers/" . $swimmer)?>" class="btn btn-primary">
            <?=htmlspecialchars($row['MForename'])?>'s page
          </a>
        </p>
      </div>
    </div>
  </div>
</div>

<?php

include BASE_PATH . "views/footer.php";