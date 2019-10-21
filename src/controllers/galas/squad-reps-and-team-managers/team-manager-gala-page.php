<?php

/**
 * TEAM MANAGER HOME PAGE FOR A GALA
 */

canView('TeamManager', $_SESSION['UserID'], $id);

global $db;
$galaInfo = $db->prepare("SELECT GalaName FROM galas WHERE GalaID = ?");
$galaInfo->execute([
  $id
]);
$gala = $galaInfo->fetch(PDO::FETCH_ASSOC);

$pagetitle = htmlspecialchars($gala['GalaName']) . " - Team Managers";

include BASE_PATH . 'views/header.php';

?>

<div class="container">
  <div class="row">
    <div class="col-md-8">
      <h1><?=htmlspecialchars($gala['GalaName'])?><br><small>Team Managers</small></h1>
      <p class="lead">Welcome to the team manager dashboard for <?=htmlspecialchars($gala['GalaName'])?>.</p>
      <p>Team manager features are slowly being introduced and you'll eventually be able to see gala entries, medical information, emergency contacts, photography permissions and take registers for each session at a gala.</p>
    </div>
  </div>
</div>

<?php

include BASE_PATH . 'views/footer.php';