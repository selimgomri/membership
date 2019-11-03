<?php

global $db;

$getSwimmer = $db->prepare("SELECT MForename, MSurname, Forename, Surname, members.UserID FROM members LEFT JOIN users on members.UserID = users.UserID WHERE MemberID = ?");
$getSwimmer->execute([$id]);
$s = $getSwimmer->fetch(PDO::FETCH_ASSOC);

if ($s == null) {
  halt(404);
}

$pagetitle = htmlspecialchars($s["MForename"]) . " " . htmlspecialchars($s["MSurname"]) . "'s Parent";

include BASE_PATH . 'views/header.php';

?>

<div class="container">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?=autoUrl("swimmers")?>">Swimmers</a></li>
      <li class="breadcrumb-item"><a href="<?=autoUrl("swimmers/" . $id)?>"><?=htmlspecialchars($s["MForename"])?> <?=htmlspecialchars(mb_substr($s["MSurname"], 0, 1, 'utf-8'))?></a></li>
      <li class="breadcrumb-item active" aria-current="page">Parent</li>
    </ol>
  </nav>

  <h1><?=htmlspecialchars($s["MForename"] . ' ' . $s["MSurname"])?>'s Parent</h1>
  <div class="row">
    <div class="col-lg-8">
      <p class="lead">Parent information for <?=htmlspecialchars($s["MForename"])?></p>

      <?php if ($s['UserID'] != null) { ?>
      <p><?=htmlspecialchars($s["MForename"])?>'s parent is <a href="<?=htmlspecialchars(autoUrl("users/" . $s['UserID']))?>"><?=htmlspecialchars($s["Forename"] . ' ' . $s['Surname'])?></a>.</p>
      <?php } else { ?>
      <p><?=htmlspecialchars($s["MForename"])?> does not have a parent assigned.</p>
      <?php } ?>

      <?php if ($s['UserID'] != null) { ?>
      <h2>Assign a parent</h2>
      <p class="lead">You can assign a parent by email address.</p>
      <p>This only works if a user already has an account.</p>

      <form method="post">
        <?=\SCDS\CSRF::write()?>
        <div class="form-group">
          <label for="email-address">Email address of new parent</label>
          <input type="email" class="form-control" id="email-address" name="email-address" placeholder="Enter email">
        </div>
        <p>
          <button type="submit" class="btn btn-primary">
            Assign to parent
          </button>
        </p>
      </form>
      <?php } ?>
    </div>
  </div>

</div>

<?php

include BASE_PATH . 'views/footer.php';