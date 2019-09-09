<?php

$pagetitle = "Squad Rep Home";

global $db;

$today = (new DateTime('now', new DateTimeZone('Europe/London')))->format("y-m-d");
$getGalas = $db->prepare("SELECT GalaName, GalaID, GalaVenue FROM galas WHERE GalaDate <= ? ORDER BY GalaDate ASC");
$getGalas->execute([
  $today
]);
$gala = $getGalas->fetch(PDO::FETCH_ASSOC);

include BASE_PATH . 'views/header.php';

?>

<div class="container">
  <div class="row">
    <div class="col-lg-8">
      <h1>Welcome to Squad Rep Services</h1>
      <p class="lead">This service allows you to view gala entries and their payment status for your squads.</p>

      <h2>
        Upcoming galas
      </h2>
      <?php if ($gala != null) { ?>
        <ul>
        <?php do { ?>
          <li>
            <a href="<?=autoUrl("galas/" . $gala['GalaID'] . "/squad-rep-view")?>">
              <?=htmlspecialchars($gala['GalaName'])?> at <?=htmlspecialchars($gala['GalaVenue'])?>
            </a>
          </li>
        <?php } while ($gala = $getGalas->fetch(PDO::FETCH_ASSOC)); ?>
        </ul>
      <?php } else { ?>
        <div class="alert alert-warning">
          <p class="mb-0">
            <strong>
              There are no upcoming galas
            </strong>
          </p>
          <p class="mb-0">
            Please check back later
          </p>
        </div>
      <?php } ?>

      <h2>
        Other services
      </h2>
    </div>
  </div>
</div>

<?php

include BASE_PATH . 'views/footer.php';