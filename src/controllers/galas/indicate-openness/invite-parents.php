<?php

global $db;
$galaDetails = $db->prepare("SELECT GalaName `name`, GalaDate `ends`, CoachEnters FROM galas WHERE GalaID = ?");
$galaDetails->execute([$id]);
$gala = $galaDetails->fetch(PDO::FETCH_ASSOC);

if ($gala == null) {
  halt(404);
}

$squads = $db->query("SELECT SquadName `name`, SquadID `id` FROM squads ORDER BY SquadFee DESC, SquadName ASC;");

$galaDate = new DateTime($gala['ends'], new DateTimeZone('Europe/London'));
$nowDate = new DateTime('now', new DateTimeZone('Europe/London'));

$getSessions = $db->prepare("SELECT `Name`, `ID` FROM galaSessions WHERE Gala = ? ORDER BY `ID` ASC");
$getSessions->execute([$id]);
$session = $getSessions->fetch(PDO::FETCH_ASSOC);

$pagetitle = 'Invite parents to enter ' . htmlspecialchars($gala['name']);

include BASE_PATH . 'views/header.php';

?>

<div class="container">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?=autoUrl("galas")?>">Galas</a></li>
      <li class="breadcrumb-item"><a href="<?=autoUrl("galas/" . $id)?>"><?=htmlspecialchars($gala['name'])?></a></li>
      <li class="breadcrumb-item active" aria-current="page">Invite Parents</li>
    </ol>
  </nav>
  <div class="row">
    <div class="col-lg-8">
      <h1>Invite parents to enter <?=htmlspecialchars($gala['name'])?></h1>
      <p class="lead">Send an email to parents letting them know their swimmers can enter this gala.</p>

      <?php if (isset($_SESSION['SuccessStatus']) && $_SESSION['SuccessStatus']) { ?>
      <div class="alert alert-success">Saved</div>
      <?php unset($_SESSION['SuccessStatus']);
      } ?>

      <?php if (isset($_SESSION['ErrorStatus']) && $_SESSION['ErrorStatus']) { ?>
      <div class="alert alert-danger">Email not sent</div>
      <?php unset($_SESSION['ErrorStatus']);
      } ?>

      <?php if ($nowDate > $galaDate) { ?>
      <div class="alert alert-warning">
        This gala has finished. You cannot send another invitation email.
      </div>
      <?php } ?>

      <form method="post">

        <!-- Invite parents from following squads -->
        <p>Invite parents of swimmers in;</p>

        <div class="row">
        <?php while ($squad = $squads->fetch(PDO::FETCH_ASSOC)) { ?>
          <div class="col-6 col-sm-6 col-md-4 col-lg-3">
            <div class="form-group">
              <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="squad-<?=$squad['id']?>" name="squad-<?=$squad['id']?>">
                <label class="custom-control-label" for="squad-<?=$squad['id']?>"><?=htmlspecialchars($squad['name'])?></label>
              </div>
            </div>
          </div>
        <?php } ?>
        </div>

        <p>Where swimmers are at least</p>

        <div class="row">
          <div class="col-6 col-md-4 col-lg-3">
            <div class="form-group">
              <div class="input-group mb-3">
                <input type="num" min="0" max="150" value="9" class="form-control" placeholder="Age" id="min-age" name="min-age" aria-label="Minimum age of swimmers">
                <div class="input-group-append">
                  <span class="input-group-text">years old</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <p>
          <button class="btn btn-success" type="submit">
            Go
          </button>
        </p>
      </form>
    </div>
  </div>
</div>

<?php

include BASE_PATH . 'views/footer.php';