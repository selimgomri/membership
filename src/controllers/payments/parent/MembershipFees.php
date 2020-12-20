<?php

$db = app()->db;
$tenant = app()->tenant;
// $user = $id;

// $object = MembershipFees::getByUser($user);

// pre($object);

// pre($object->getFormattedTotal());

$user = $_SESSION['TENANT-' . app()->tenant->getId()]['UserID'];
$info = null;
if ($_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] != 'Parent' && isset($id) && $id != null) {
  $user = $id;
  $userInfo = $db->prepare("SELECT Forename, Surname, EmailAddress, Mobile FROM users WHERE UserID = ? AND Tenant = ?");
  $userInfo->execute([
    $id,
    $tenant->getId()
  ]);
  $info = $userInfo->fetch(PDO::FETCH_ASSOC);

  $isParent = $db->prepare("SELECT COUNT(*) FROM permissions WHERE User = ? AND Permission = 'Parent'");
  $isParent->execute([$id]);

  if ($info == null || $isParent->fetchColumn() == 0) {
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

$sql = $db->prepare("SELECT COUNT(*) FROM `members` WHERE `members`.`UserID` = ?");
$sql->execute([$user]);

$clubFee = $totalFeeDiscounted = $totalFee = 0;

$payingSwimmerCount = $sql->fetchColumn();

$clubFees = MembershipFees::getByUser($user);

$clubFee = $clubFees->getTotal();

$getMembers = $db->prepare("SELECT * FROM `members` WHERE `members`.`UserID` = ?");
$getMembers->execute([
  $user
]);

$member = $getMembers->fetchAll(PDO::FETCH_ASSOC);
$count = sizeof($member);

$totalFee += $clubFee;
$totalFeeDiscounted += $clubFee;

$asaFees = [];

$asa1 = app()->tenant->getKey('ASA-County-Fee-L1') + app()->tenant->getKey('ASA-Regional-Fee-L1') + app()->tenant->getKey('ASA-National-Fee-L1');
$asa2 = app()->tenant->getKey('ASA-County-Fee-L2') + app()->tenant->getKey('ASA-Regional-Fee-L2') + app()->tenant->getKey('ASA-National-Fee-L2');
$asa3 = app()->tenant->getKey('ASA-County-Fee-L3') + app()->tenant->getKey('ASA-Regional-Fee-L3') + app()->tenant->getKey('ASA-National-Fee-L3');

for ($i = 0; $i < $count; $i++) {
  if ($member[$i]['ASACategory'] == 1 && !$member[$i]['ASAPaid']) {
    $asaFees[$i] = $asa1;
  } else if ($member[$i]['ASACategory'] == 2  && !$member[$i]['ASAPaid']) {
    $asaFees[$i] = $asa2;
  } else if ($member[$i]['ASACategory'] == 3  && !$member[$i]['ASAPaid']) {
    $asaFees[$i] = $asa3;
  }

  if (isset($asaFees[$i])) {
    $totalFee += $asaFees[$i];
    $totalFeeDiscounted += $asaFees[$i];
  }
}

$clubFeeString = $clubFees->getFormattedTotal();
$totalFeeString = (string) (\Brick\Math\BigDecimal::of((string) $totalFee))->withPointMovedLeft(2)->toScale(2);

$pagetitle = "Membership Fees";

include BASE_PATH . 'views/header.php';

?>

<div class="container">

  <?php if ($_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] != 'Parent') { ?>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= autoUrl("users") ?>">Users</a></li>
        <li class="breadcrumb-item"><a href="<?= autoUrl("users/" . $id) ?>"><?= htmlspecialchars(mb_substr($info["Forename"], 0, 1, 'utf-8') . mb_substr($info["Surname"], 0, 1, 'utf-8')) ?></a></li>
        <li class="breadcrumb-item active" aria-current="page">Annual membership</li>
      </ol>
    </nav>
  <?php } else { ?>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= autoUrl("payments") ?>">Payments</a></li>
        <li class="breadcrumb-item active" aria-current="page">Annual membership</li>
      </ol>
    </nav>
  <?php } ?>

  <div class="row">
    <div class="col-lg-8">
      <h1>Membership fees<?php if ($_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] != 'Parent') { ?> for <?= htmlspecialchars($info['Forename']) ?><?php } ?></h1>
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
          <?php foreach ($clubFees->getClasses() as $class) { ?>
            <thead class="">
              <tr class="bg-dark text-light">
                <th>
                  <?= htmlspecialchars($class->getName()) ?>
                </th>
                <th>
                </th>
              </tr>
            </thead>
            <thead class="thead-light">
              <tr>
                <th>
                  Member
                </th>
                <th>
                  Fee
                </th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($class->getFeeItems() as $item) { ?>
                <tr>
                  <td>
                    <?= htmlspecialchars($item->getDescription()) ?>
                  </td>
                  <td>
                    &pound;<?= htmlspecialchars($item->getFormattedAmount()) ?>
                  </td>
                </tr>
              <?php } ?>
            </tbody>
          <?php } ?>
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
                Member
              </th>
              <th>
                Fee
              </th>
            </tr>
          </thead>
          <tbody>
            <?php
            for ($i = 0; $i < $count; $i++) {
              $asaFeesString = "";
              if ($member[$i]['ASAPaid']) {
                $asaFeesString = "0.00 (Paid by club)";
              } else if (isset($asaFees[$i])) {
                if ((string) $asaFees[$i] != "") {
                  $asaFeesString = (string) (\Brick\Math\BigDecimal::of((string) $asaFees[$i]))->withPointMovedLeft(2)->toScale(2);
                }
              } else {
                $asaFeesString = '0.00';
              }
            ?>
              <tr>
                <td>
                  <?= htmlspecialchars($member[$i]['MForename'] . " " . $member[$i]['MSurname']) ?><?php if ($member[$i]['ASACategory'] > 0) { ?> (Category <?= htmlspecialchars($member[$i]['ASACategory']) ?>)<?php } ?>
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

$footer = new \SCDS\Footer();
$footer->render();
