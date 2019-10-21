<?php

global $db;

$userInfo = $db->prepare("SELECT Forename, Surname, EmailAddress, Mobile, AccessLevel FROM users WHERE UserID = ?");
$userInfo->execute([$id]);
$info = $userInfo->fetch(PDO::FETCH_ASSOC);

$date = new DateTime('-1 day', new DateTimeZone('Europe/London'));
$getGalas = $db->prepare("SELECT GalaName, GalaID FROM galas WHERE GalaDate >= ? ORDER BY GalaDate ASC, GalaName ASC");
$getGalas->execute([
  $date->format("Y-m-d")
]);
$gala = $getGalas->fetch(PDO::FETCH_ASSOC);

if ($info == null) {
  halt(404);
}

$pagetitle = htmlspecialchars($info['Forename'] . ' ' . $info['Surname']) . ' Team Manager Options';

include BASE_PATH . "views/header.php";

?>

<div class="container">

  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?=autoUrl("users")?>">Users</a></li>
      <li class="breadcrumb-item"><a href="<?=autoUrl("users/" . $id)?>"><?=htmlspecialchars($info['Forename'] . ' ' . $info['Surname'])?></a></li>
      <li class="breadcrumb-item"><a href="<?=autoUrl("users/" . $id . "/team-manager")?>">TM Settings</a></li>
      <li class="breadcrumb-item active" aria-current="page">Assign</li>
    </ol>
  </nav>

  <div class="row">
    <div class="col-lg-8">
      <h1>
        Assign a gala to <?=htmlspecialchars($info['Forename'] . ' ' . $info['Surname'])?>
      </h1>

      <?php if (isset($_SESSION['AssignGalaError']) && $_SESSION['AssignGalaError']) { ?>
      <div class="alert alert-danger">
        <p class="mb-0">
          <strong>
            We were unable to assign that gala to <?=htmlspecialchars($info['Forename'])?>
          </strong>
        </p>
      </div>
      <?php
        unset($_SESSION['AssignGalaError']);
      } ?>

      <?php if ($gala != null) { ?>
      <form method="post">
        <div class="form-group">
          <label for="gala-select">
            Choose a gala
          </label>
          <select class="custom-select" id="gala-select" name="gala-select">
           <option selected>Select a gala</option>
            <?php do { ?>
              <option value="<?=$gala['GalaID']?>">
                <?=htmlspecialchars($gala['GalaName'])?>
              </option>
            <?php } while ($gala = $getGalas->fetch(PDO::FETCH_ASSOC)); ?>
          </select>
        </div>

        <p>
          <button type="submit" class="btn btn-primary">
            Assign gala
          </button>
        </p>
      </form>
      <?php } else { ?>
      <div class="alert alert-warning">
        <p class="mb-0">
          <strong>
            There are no galas to choose from
          </strong>
        </p>
      </div>
      <?php } ?>
    </div>
  </div>

</div>

<?php

include BASE_PATH . "views/footer.php";