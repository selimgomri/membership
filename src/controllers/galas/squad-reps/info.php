<?php

$noSquad = false;
$doNotHalt = true;
require 'info.json.php';
$data = json_decode($output);

$squads = null;
global $systemInfo;
$leavers = $systemInfo->getSystemOption('LeaversSquad');
if ($_SESSION['AccessLevel'] != 'Parent') {
  $squads = $db->prepare("SELECT SquadName `name`, SquadID `id` FROM squads WHERE `SquadID` != ? ORDER BY SquadFee DESC, `name` ASC");
  $squads->execute([
    $leavers
  ]);
} else {
  $squads = $db->prepare("SELECT SquadName `name`, SquadID `id` FROM squads INNER JOIN squadReps ON squads.SquadID = squadReps.Squad WHERE squadReps.User = ? AND SquadID != ? ORDER BY SquadFee DESC, `name` ASC");
  $squads->execute([
    $_SESSION['UserID'],
    $leavers
  ]);
}

$swimsArray = [
  '50Free' => '50&nbsp;Free',
  '100Free' => '100&nbsp;Free',
  '200Free' => '200&nbsp;Free',
  '400Free' => '400&nbsp;Free',
  '800Free' => '800&nbsp;Free',
  '1500Free' => '1500&nbsp;Free',
  '50Back' => '50&nbsp;Back',
  '100Back' => '100&nbsp;Back',
  '200Back' => '200&nbsp;Back',
  '50Breast' => '50&nbsp;Breast',
  '100Breast' => '100&nbsp;Breast',
  '200Breast' => '200&nbsp;Breast',
  '50Fly' => '50&nbsp;Fly',
  '100Fly' => '100&nbsp;Fly',
  '200Fly' => '200&nbsp;Fly',
  '100IM' => '100&nbsp;IM',
  '150IM' => '150&nbsp;IM',
  '200IM' => '200&nbsp;IM',
  '400IM' => '400&nbsp;IM'
];

$pagetitle = htmlspecialchars($data->squad->name) . " Squad Rep View for " . htmlspecialchars($data->gala->name);

include BASE_PATH . 'views/header.php';

?>

<div class="container">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?=autoUrl("squad-reps")?>">Squad Reps</a></li>
			<li class="breadcrumb-item active" aria-current="page">View gala entries</li>
		</ol>
	</nav>

	<h1><?=htmlspecialchars($data->squad->name)?> Squad entries for <?=htmlspecialchars($data->gala->name)?></h1>
	<?php if ($data->gala->fixed_fee) { ?>
	<p class="lead">
		This gala costs &pound;<?=number_format($data->gala->fee/100, 2, '.', '')?>/swim
	</p>
	<?php } else { ?>
	<p class="lead">
		There is no fixed fee for this gala
	</p>
  <?php } ?>

	<div class="row">

    <?php if (!$noSquad) { ?>
    <div class="col-md-8">

			<h2>Entries for this gala</h2>
      <p class="lead">
        This list shows all entries by swimmers in <?=htmlspecialchars($data->squad->name)?> Squad.
      </p>

      <p>
        This is currently a Minimum Viable Product (MVP) and does not allow you to mark entries as paid or approve them.
      </p>

      <p>
        Entries shown in green have been charged for.
      </p>

      <?php if (sizeof($data->entries) > 0) { ?>
      <ul class="list-group mb-3">
        <?php foreach ($data->entries AS $entry) { ?>
          <?php $hasNoDD = ($entry->MandateID == null) || (getUserOption($entry->user, 'GalaDirectDebitOptOut')); ?>
        <li class="list-group-item <?php if (bool($entry->charged)) {?>list-group-item-success<?php } ?>" id="refund-box-<?=htmlspecialchars($entry->id)?>">
          <div class="row">
            <div class="col-sm-5 col-md-4 col-lg-6">
              <h3><?=htmlspecialchars($entry->forename . ' ' . $entry->surname)?></h3>

              <p>
                <strong>Swim England Number:</strong> <?=htmlspecialchars($entry->asa_number)?><br>
                <strong>Age today:</strong> <?=htmlspecialchars($entry->age_today)?><br>
                <strong>Age on day:</strong> <?=htmlspecialchars($entry->age_on_last_day)?><br>
                <strong>Age at end of year:</strong> <?=htmlspecialchars($entry->age_at_end_of_year)?><br>
              </p>

              <p class="mb-0">
                <?=htmlspecialchars($entry->forename)?> was entered in;
              </p>
              <ul class="list-unstyled">
              <?php $count = 0; ?>
              <?php foreach($entry->events as $event) { ?>
                <?php if ($event->selected) { $count++; ?>
                <li><?=htmlspecialchars($event->name)?><?php if (isset($event->entry_time) && $event->entry_time != null) { ?> <em><?=htmlspecialchars($event->entry_time)?></em><?php } ?></li>
                <?php } ?>
              <?php } ?>
            </div>
            <div class="col">
              <div class="d-sm-none mb-3"></div>
              <?php if (isset($entry->payment_intent->id) && $entry->payment_intent->id != null) { ?>
              <p>
                <strong>
                  Paid with
                </strong><br>
                <i class="fa <?=htmlspecialchars(getCardFA($entry->payment_intent->brand))?>" aria-hidden="true"></i> <span class="sr-only"><?=htmlspecialchars(getCardBrand($entry->payment_intent->brand))?></span> &#0149;&#0149;&#0149;&#0149; <?=htmlspecialchars($entry->payment_intent->last4)?>
              </p>
              <?php } ?>

              <p>
                <?php if ($data->gala->fixed_fee) { ?>
                <?=$count?> &times; &pound;<?=htmlspecialchars(number_format($data->gala->fee/100, 2))?>
                <?php } else { ?>
                <?=$count?> entries at no fixed fee
                <?php } ?>
              </p>

              <p class="mb-0">
                <strong>
                <?php if (bool($entry->charged)) { ?>Amount charged<?php } else { ?>Fee to pay<?php } ?>
                </strong><br>
                &pound;<?=htmlspecialchars(number_format($entry->amount_charged/100, 2))?>
              </p>

              <?php if ($entry->refunded) { ?>
              <p class="mt-3 mb-0">
                <strong>
                  Amount refunded
                </strong><br>
                &pound;<?=number_format($entry->amount_refunded/100, 2)?> has been refunded<?php if ($entry->payment_intent->id != null) { ?> to <?=htmlspecialchars(getCardBrand($entry->payment_intent->brand))?> <?=htmlspecialchars($entry->payment_intent->funding)?> card ending <?=htmlspecialchars($entry->payment_intent->last4)?><?php } ?>
              </p>

                <?php if ($hasNoDD && (!isset($entry->payment_intent->id) || $entry->payment_intent->id == null)) { ?>
                <p class="mt-3 mb-0">
                  The parent does not have a Direct Debit set up or has requested to pay by other means. Refund should be by cash, cheque or bank transfer.
                </p>
                <?php } else if (!$hasNoDD && (!isset($entry->payment_intent->id) || $entry->payment_intent->id == null)) { ?>
                <p class="mt-3 mb-0">
                  This gala will be refunded as a discount on the parent's next direct debit payment.
                </p>
                <?php } ?>
              <?php } ?>
            </div>
          </div>
        </li>
        <?php } ?>
      </ul>

			<?php } else { ?>
			<div class="alert alert-warning">
				<strong>There are no entries for this gala</strong>
			</div>
      <?php } ?>
    </div>
    <?php } ?>
  
    <div class="col">
      <h2>Select a squad</h2>
      <p class="lead">Select a squad to view entries for</p>
      <div class="form-group">
        <label for="squad-select">
          Choose squad
        </label>
        <select class="custom-select" id="squad-select" name="squad-select">
          <?php if ($noSquad) { ?>
          <option selected>Select a squad</option>
          <?php } ?>
          <?php while ($s = $squads->fetch(PDO::FETCH_ASSOC)) { ?>
          <option value="<?=$s['id']?>" <?php if ((int) $s['id'] == $squad) { ?>selected<?php } ?>>
              <?=htmlspecialchars($s['name'])?>
            </option>
          <?php } ?>
        </select>
      </div>
    </div>
  </div>
</div>

<script src="<?=autoUrl("js/squad-reps/select.js")?>"></script>

<?php

include BASE_PATH . 'views/footer.php';