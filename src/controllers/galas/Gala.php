<?php

global $db;

$galas = $db->prepare("SELECT GalaName, ClosingDate, GalaDate, GalaVenue, CourseLength FROM galas WHERE GalaID = ?");
$galas->execute([$id]);
$gala = $galas->fetch(PDO::FETCH_ASSOC);

if ($gala == null) {
  halt(404);
}

$pagetitle = htmlspecialchars($gala['GalaName']) . " - Galas";
include BASE_PATH . "views/header.php";
include "galaMenu.php";
?>

<div class="container">
  <div class="col-md-8">
    <h1>
      <?=htmlspecialchars($gala['GalaName'])?>
    </h1>
    <p class="lead">
      <?=htmlspecialchars($gala['GalaVenue'])?>
    </p>

    <div class="alert alert-warning">
      We're still working on improving these pages.
    </div>

  </div>
</div>

<?php include BASE_PATH . "views/footer.php"; ?>
