<?php

global $db;

$swimsArray = ['50Free','100Free','200Free','400Free','800Free','1500Free','50Breast','100Breast','200Breast','50Fly','100Fly','200Fly','50Back','100Back','200Back','100IM','150IM','200IM','400IM',];
$swimsTextArray = ['50 Free','100 Free','200 Free','400 Free','800 Free','1500 Free','50 Breast','100 Breast','200 Breast','50 Fly','100 Fly','200 Fly','50 Back','100 Back','200 Back','100 IM','150 IM','200 IM','400 IM',];
$swimsTimeArray = ['50FreeTime','100FreeTime','200FreeTime','400FreeTime','800FreeTime','1500FreeTime','50BreastTime','100BreastTime','200BreastTime','50FlyTime','100FlyTime','200FlyTime','50BackTime','100BackTime','200BackTime','100IMTime','150IMTime','200IMTime','400IMTime',];

$entryList = "";
$get = $db->prepare("SELECT * FROM (galaEntries INNER JOIN galas ON galaEntries.GalaID = galas.GalaID) WHERE galaEntries.MemberID = ? AND galaEntries.GalaID = ?");
$get->execute([
  $_SESSION['SuccessfulGalaEntry']['Swimmer'],
  $_SESSION['SuccessfulGalaEntry']['Gala']
]);
$row = $get->fetch(PDO::FETCH_ASSOC);
// Print <li>Swim Name</li> for each entry
for ($y=0; $y<sizeof($swimsArray); $y++) {
  if ($row[$swimsArray[$y]] == 1) {
    $entryList .= "<li>" . $swimsTextArray[$y] . "</li>";
  }
}

$get = $db->prepare("SELECT members.MForename, members.MSurname, galas.GalaName, galas.GalaFee, galas.GalaFeeConstant, users.EmailAddress, users.Forename, users.Surname, FeeToPay FROM (((galaEntries INNER JOIN members ON galaEntries.MemberID = members.MemberID) INNER JOIN galas ON galaEntries.GalaID = galas.GalaID) INNER JOIN users ON members.UserID = users.UserID) WHERE galaEntries.MemberID = ? AND galaEntries.GalaID = ?");
$get->execute([
  $_SESSION['SuccessfulGalaEntry']['Swimmer'],
  $_SESSION['SuccessfulGalaEntry']['Gala']
]);
$row = $get->fetch(PDO::FETCH_ASSOC);

$pagetitle = htmlspecialchars($row['MForename']) . "'s Gala Entry to " . htmlspecialchars($row['GalaName']);

include BASE_PATH . "views/header.php";

?>

<div class="container">
  <div class="row">
    <div class="col-md-8">
      <h1>
        <?=htmlspecialchars($row['MForename'])?>'s Gala Entry to <?=htmlspecialchars($row['GalaName'])?>
      </h1>
      <p class="lead">
        Here are the details...
      </p>
      <div class="alert alert-success">
        <p class="mb-0">
          An email confirmation is on it's way to
        </p>
        <p class="text-truncate mb-0 mono">
          <?=htmlspecialchars($row['EmailAddress'])?>
        </p>
      </div>

      <p>
        You have entered <?=htmlspecialchars($row['MForename'])?> into;
      </p>

      <ul>
        <?=$entryList?>
      </ul>

      <?php if ($row['GalaFeeConstant'] == 1) { ?>
      <p>
        The fee for each swim is &pound;<?=number_format($row['GalaFee'],2,'.','')?>, the <strong>total fee payable is &pound;<?=number_format(($row['FeeToPay']),2,'.','')?></strong>
      </p>
      <?php } else { ?>
      <p>
        The <strong>total fee payable is &pound;<?=number_format(($row['FeeToPay']),2,'.','')?></strong>. If you have entered this amount incorrectly, you may incur extra charges from the club or gala host.
      </p>
      <?php } ?>

      <p>
        <a href="<?=autoUrl("galas")?>" class="btn btn-success">
          Return to galas
        </a>
      </p>

    </div>
  </div>
</div>

<?php

if (isset($_SESSION['SuccessfulGalaEntry'])) {
  unset($_SESSION['SuccessfulGalaEntry']);
}

include BASE_PATH . "views/footer.php";