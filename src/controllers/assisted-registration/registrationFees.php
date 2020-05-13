<?php

$db = app()->db;


$user = $_SESSION['TENANT-' . app()->tenant->getId()]['UserID'];
$info = null;
if ($_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] != 'Parent' && isset($id) && $id != null) {
  $user = $id;
  $userInfo = $db->prepare("SELECT Forename, Surname, EmailAddress, Mobile, AccessLevel FROM users WHERE UserID = ?");
  $userInfo->execute([$id]);
  $info = $userInfo->fetch(PDO::FETCH_ASSOC);

  if ($info == null || $info['AccessLevel'] != 'Parent') {
    halt(404);
  }
}

$month = (new DateTime('now', new DateTimeZone('Europe/London')))->format('m');

$discounts = json_decode(app()->tenant->getKey('MembershipDiscounts'), true);
$clubDiscount = $swimEnglandDiscount = 0;
if ($discounts != null && isset($discounts['CLUB'][$month])) {
	$clubDiscount = $discounts['CLUB'][$month];
}
if ($discounts != null && isset($discounts['ASA'][$month])) {
	$swimEnglandDiscount = $discounts['ASA'][$month];
}

$clubFee = $totalFeeDiscounted = $totalFee = 0;

$sql = $db->prepare("SELECT COUNT(*) FROM `members` WHERE `members`.`UserID` = ? AND `ClubPays` = '0' AND `members`.`RR` = 1");
$sql->execute([$user]);
$registeringCount = $sql->fetchColumn();

$sql = $db->prepare("SELECT COUNT(*) FROM `members` WHERE `members`.`UserID` = ? AND `ClubPays` = '0'");
$sql->execute([$user]);
$totalCount = $sql->fetchColumn();

$existingCount = $totalCount - $registeringCount;

$clubFees = \SCDS\Membership\ClubMembership::create($db, $user, true);

$clubFee = $clubFees->getFee();

$getMembers = $db->prepare("SELECT * FROM `members` INNER JOIN `squads` ON squads.SquadID = members.SquadID WHERE `members`.`UserID` = ?");
$getMembers->execute([$user]);

$member = $getMembers->fetchAll(PDO::FETCH_ASSOC);
$count = sizeof($member);

$totalFee += $clubFee;
$totalFeeDiscounted += $clubFee;

$asaFees = [];

$asa1 = app()->tenant->getKey('ASA-County-Fee-L1') + app()->tenant->getKey('ASA-Regional-Fee-L1') + app()->tenant->getKey('ASA-National-Fee-L1');
$asa2 = app()->tenant->getKey('ASA-County-Fee-L2') + app()->tenant->getKey('ASA-Regional-Fee-L2') + app()->tenant->getKey('ASA-National-Fee-L2');
$asa3 = app()->tenant->getKey('ASA-County-Fee-L3') + app()->tenant->getKey('ASA-Regional-Fee-L3') + app()->tenant->getKey('ASA-National-Fee-L3');

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

$pagetitle = "Registration Fees";

$numFormatter = new NumberFormatter("en", NumberFormatter::SPELLOUT);

include BASE_PATH . 'views/header.php';

?>

<div class="container">

  <div class="row">
    <div class="col-lg-8">
      <h1>Membership fees for <?=htmlspecialchars($info['Forename'])?></h1>
      <p class="lead">Club and Swim England membership fees are paid on registration and then yearly, due on 1 January.</p>

      <p>We've tried our best to calculate the membership fees for <?=htmlspecialchars($info['Forename'])?> but you can modify them. Details about <?=htmlspecialchars($info['Forename'])?>'s registration fees will be included on the registration email we send them.</p>

      <form method="post">

        <h2>Club membership fee</h2>
        <p>Club membership fees are paid for a family, not individual members.</p>

        <p>We've calculated the club membership fee to pay based on <?=$numFormatter->format($registeringCount)?> member<?php if ($registeringCount != 1) { ?>s<?php } ?>.<?php if ($existingCount > 0) { ?> <?=mb_convert_case($numFormatter->format($existingCount), MB_CASE_TITLE_SIMPLE)?> member<?php if ($existingCount != 1) { ?>s<?php } ?> are already registered on this account.<?php } ?></p>

        <?php if ($existingCount > 0) { ?>
        <p>
          There will be <?=$numFormatter->format($totalCount)?> member<?php if ($totalCount != 1) { ?>s<?php } ?> linked to this account after registration.
        </p>
        <?php } ?>

        <div class="form-group">
          <label for="membership-fee">Club membership fee</label>
          <div class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text" id="membership-fee-prepend">&pound;</span>
            </div>
            <input type="number" min="0" step="0.01" class="form-control" id="membership-fee" value="<?=(string) (\Brick\Math\BigDecimal::of((string) $item['amount']))->withPointMovedLeft(2)->toScale(2)?>">
          </div>
        </div>

        <h2>Swim England fees</h2>

        <?php for ($i = 0; $i < $count; $i++) {
          $asaFeesString;
          if ($member[$i]['ClubPays']) {
            $asaFeesString = "0.00 (Paid by club)";
          } else {
            $asaFeesString = (string) (\Brick\Math\BigDecimal::of((string) $asaFees[$i]))->withPointMovedLeft(2)->toScale(2);
          } ?>
          <div class="form-group">
          <label for="<?=htmlspecialchar($member[$id]['MemberID'])?>-se-fee"><?=htmlspecialchars($member[$i]['MForename'] . " " . $member[$i]['MSurname'])?> Swim England Fee</label>
          <div class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text" id="<?=htmlspecialchar($member[$id]['MemberID'])?>-se-fee-prepend">&pound;</span>
            </div>
            <input type="number" min="0" step="0.01" class="form-control" id="<?=htmlspecialchar($member[$id]['MemberID'])?>-se-fee" name="<?=htmlspecialchar($member[$id]['MemberID'])?>-se-fee" value="<?=htmlspecialchars((string) (\Brick\Math\BigDecimal::of((string) $asaFees[$i]))->withPointMovedLeft(2)->toScale(2))?>">
          </div>
        </div>
        <?php } ?>

        <?=\SCDS\CSRF::write()?>

        <p>
          <button type="submit" class="btn btn-success">
            Continue
          </button>
        </p>

      </form>
    </div>
  </div>
</div>

<?php

$footer = new \SCDS\Footer();
$footer->render();