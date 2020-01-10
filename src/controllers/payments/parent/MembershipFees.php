<?php

global $db;
global $systemInfo;

$month = (new DateTime('now', new DateTimeZone('Europe/London')))->format('m');

$discounts = json_decode($systemInfo->getSystemOption('MembershipDiscounts'), true);
$clubDiscount = $swimEnglandDiscount = 0;
if ($discounts != null && isset($discounts['CLUB'][$month])) {
	$clubDiscount = $discounts['CLUB'][$month];
}
if ($discounts != null && isset($discounts['ASA'][$month])) {
	$swimEnglandDiscount = $discounts['ASA'][$month];
}

$sql = $db->prepare("SELECT COUNT(*) FROM `members` WHERE `members`.`UserID` = ? AND `ClubPays` = '0'");
$sql->execute([$_SESSION['UserID']]);

$clubFee = $totalFeeDiscounted = $totalFee = 0;

$payingSwimmerCount = $sql->fetchColumn();

$clubFees = \SCDS\Membership\ClubMembership::create($db, $_SESSION['UserID'], false);

$clubFee = $clubFees->getFee();

$getMembers = $db->prepare("SELECT * FROM `members` INNER JOIN `squads` ON squads.SquadID = members.SquadID WHERE `members`.`UserID` = ?");
$getMembers->execute([$_SESSION['UserID']]);

$member = $getMembers->fetchAll(PDO::FETCH_ASSOC);
$count = sizeof($member);

$totalFee += $clubFee;
$totalFeeDiscounted += $clubFee;

$asaFees = [];

$asa1 = $systemInfo->getSystemOption('ASA-County-Fee-L1') + $systemInfo->getSystemOption('ASA-Regional-Fee-L1') + $systemInfo->getSystemOption('ASA-National-Fee-L1');
$asa2 = $systemInfo->getSystemOption('ASA-County-Fee-L2') + $systemInfo->getSystemOption('ASA-Regional-Fee-L2') + $systemInfo->getSystemOption('ASA-National-Fee-L2');
$asa3 = $systemInfo->getSystemOption('ASA-County-Fee-L3') + $systemInfo->getSystemOption('ASA-Regional-Fee-L3') + $systemInfo->getSystemOption('ASA-National-Fee-L3');

for ($i = 0; $i < $count; $i++) {
	if ($member[$i]['ASACategory'] == 1 && !$member[$i]['ClubPays']) {
		$asaFees[$i] = $asa1;
	} else if ($member[$i]['ASACategory'] == 2  && !$member[$i]['ClubPays']) {
		$asaFees[$i] = $asa2;
	} else if ($member[$i]['ASACategory'] == 3  && !$member[$i]['ClubPays']) {
		$asaFees[$i] = $asa3;
	}

  $totalFee += $asaFees[$i];
  $totalFeeDiscounted += $asaFees[$i];
}

$clubFeeString = (string) (\Brick\Math\BigDecimal::of((string) $clubFee))->withPointMovedLeft(2)->toScale(2);
$totalFeeString = (string) (\Brick\Math\BigDecimal::of((string) $totalFee))->withPointMovedLeft(2)->toScale(2);

$pagetitle = "Membership Fees";

include BASE_PATH . 'views/header.php';

?>

<div class="container">
  <div class="row">
    <div class="col-lg-8">
      <h1>Membership fees</h1>
      <p class="lead">Club and Swim England membership fees are paid yearly and are due on 1 January.</p>

      <p>The fees you're charged are dependent on the number of members linked to this account and the type of each member.</p>

      <h2>Your Membership Fees</h2>
      <div class="table-responsive-md">
        <table class="table">
          <thead class="">
            <tr class="bg-primary text-light">
              <th>
                Club Membership
              </th>
              <th>
              </th>
            </tr>
          </thead>
          <thead class="thead-light">
            <tr>
              <th>
                Type
              </th>
              <th>
                Fee
              </th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($clubFees->getFeeItems() as $item) { ?>
            <tr>
              <td>
                <?=htmlspecialchars($item['description'])?>
              </td>
              <td>
                &pound;<?=(string) (\Brick\Math\BigDecimal::of((string) $item['amount']))->withPointMovedLeft(2)->toScale(2)?>
              </td>
            </tr>
            <?php } ?>
          </tbody>
          <thead class="">
            <tr class="bg-primary text-light">
              <th>
                Swim England Membership
              </th>
              <th>
              </th>
            </tr>
          </thead>
          <thead class="thead-light">
            <tr>
              <th>
                Swimmer
              </th>
              <th>
                Fee
              </th>
            </tr>
          </thead>
          <tbody>
        <?php
        for ($i = 0; $i < $count; $i++) {
          $asaFeesString;
          if ($member[$i]['ClubPays']) {
            $asaFeesString = "0.00 (Paid by club)";
          } else {
            $asaFeesString = (string) (\Brick\Math\BigDecimal::of((string) $asaFees[$i]))->withPointMovedLeft(2)->toScale(2);
          }
          ?>
          <tr>
            <td>
              <?=htmlspecialchars($member[$i]['MForename'] . " " . $member[$i]['MSurname'])?>
            </td>
            <td>
              &pound;<?php echo $asaFeesString; ?>
            </td>
          </tr>
        <?php } ?>
          </tbody>
          <tbody>
            <tr class="table-active">
              <td>
                Total Membership Fee
              </td>
              <td>
                &pound;<?= $totalFeeString ?>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php

include BASE_PATH . 'views/footer.php';