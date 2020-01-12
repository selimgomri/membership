<?php

require BASE_PATH . 'controllers/payments/GoCardlessSetup.php';

$user = $id;

global $db;

$getUser = $db->prepare("SELECT Forename, Surname FROM users WHERE UserID = ?");
$getUser->execute([$user]);
$info = $getUser->fetch(PDO::FETCH_ASSOC);

if ($info == null) {
  halt(404);
}

$pagetitle = "Current Fees";
$use_white_background = true;

include BASE_PATH . "views/header.php";

?>

<div class="container">

  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?=autoUrl("users")?>">Users</a></li>
      <li class="breadcrumb-item"><a href="<?=autoUrl("users/" . $id)?>"><?=htmlspecialchars(mb_substr($info['Forename'], 0, 1, 'utf-8') . mb_substr($info['Surname'], 0, 1, 'utf-8'))?></a></li>
      <li class="breadcrumb-item active" aria-current="page">Pending fees</li>
    </ol>
  </nav>

	<div class="row">
    <div class="col-lg-8">
  		<h1 class="">Charges since last bill</h1>
  		<p class="lead">Fees and Charges created since <?=htmlspecialchars($info['Forename'])?>'s last bill</p>
  		<p>They'll be billed for these on the first working day of the next month.</p>
    </div>
  </div>
	<?=feesToPay(null, $user)?>
</div>

<?php

include BASE_PATH . "views/footer.php";