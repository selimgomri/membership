<?php

$db = app()->db;
$tenant = app()->tenant;

$noSquad = false;
$doNotHalt = true;
require 'info.json.php';
$data = json_decode($output);

$squads = null;
$leavers = app()->tenant->getKey('LeaversSquad');
if ($leavers == null) {
  $leavers = 0;
}

if ($_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] != 'Parent') {
  $squads = $db->prepare("SELECT SquadName `name`, SquadID `id` FROM squads WHERE Tenant = ? AND `SquadID` != ? ORDER BY SquadFee DESC, `name` ASC");
  $squads->execute([
    $tenant->getId(),
    $leavers
  ]);
} else {
  $squads = $db->prepare("SELECT SquadName `name`, SquadID `id` FROM squads INNER JOIN squadReps ON squads.SquadID = squadReps.Squad WHERE Tenant = ? AND squadReps.User = ? AND SquadID != ? ORDER BY SquadFee DESC, `name` ASC");
  $squads->execute([
    $tenant->getId(),
    $_SESSION['TENANT-' . app()->tenant->getId()]['UserID'],
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

$numFormatter = new NumberFormatter("en", NumberFormatter::SPELLOUT);

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

	<h1><?=htmlspecialchars($data->squad->name)?> entries for <?=htmlspecialchars($data->gala->name)?></h1>
	<p class="lead">
		<?=htmlspecialchars($data->gala->venue)?>
	</p>

	<div class="row">

    <div class="col order-lg-1">
      <div class="cell">
        <h2>Select a squad</h2>
        <p class="lead">Select a squad to view entries for</p>
        <div class="form-group mb-0">
          <label for="squad-select">
            Choose squad
          </label>
          <select class="custom-select" id="squad-select" name="squad-select" data-gala-id="<?=htmlspecialchars($id)?>" data-page="<?=htmlspecialchars(autoUrl(''))?>" data-ajax-url="<?=htmlspecialchars(autoUrl('galas/squad-reps/entry-states'))?>">
            <?php if ($noSquad) { ?>
            <option selected>Select a squad</option>
            <?php } ?>
            <?php if ($_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] != "Parent") { ?>
            <option value="all" <?php if ("all" == $squad) { ?>selected<?php } ?>>
              All squads
            </option>
            <?php } ?>
            <?php while ($s = $squads->fetch(PDO::FETCH_ASSOC)) { ?>
            <option value="<?=$s['id']?>" <?php if ((int) $s['id'] == $squad) { ?>selected<?php } ?>>
              <?=htmlspecialchars($s['name'])?>
            </option>
            <?php } ?>
          </select>
        </div>
      </div>

      <?php if (!$noSquad) { ?>
      <div class="cell">
        <h2>Download entries</h2>
        <p class="lead">Download entries for this squad</p>
        <p>
          <a href="<?=autoUrl("galas/" . $id . "/squad-rep-view.csv?squad=" . $squad)?>" target="_blank" class="btn btn-block btn-primary">
            CSV (for Microsoft Excel)
          </a>
        </p>
        <p>
          <a href="<?=autoUrl("galas/" . $id . "/squad-rep-view.json?squad=" . $squad)?>" target="_blank" class="btn btn-block btn-primary">
            JSON
          </a>
        </p>
        <p>
          <a href="<?=autoUrl("galas/" . $id . "/squad-rep-view.pdf?squad=" . $squad)?>" target="_blank" class="btn btn-block btn-primary">
            PDF
          </a>
        </p>
      </div>
      <?php } ?>
    </div>

    <?php if (!$noSquad) { ?>
    <div class="col-md-8 order-lg-0" id="entries-list">

			<h2>Entries for this gala</h2>
      <p class="lead">
        This list shows all entries by swimmers in <?=htmlspecialchars($data->squad->name)?>.
      </p>

      <p>
        This allows you to mark an entry as paid or approve it (if the gala requires that entries are approved by a squad rep). If a parent pays by card or has had the entry charged to their account, you will not be able to uncheck the paid box.
      </p>

      <p>
        Entries shown in green have been paid or charged for.
      </p>

      <?php if (sizeof($data->entries) > 0) { ?>
      <ul class="list-group mb-3">
        <?php foreach ($data->entries AS $entry) { ?>
          <?php $hasNoDD = (!isset($entry->mandate->id) || $entry->mandate->id == null) || (getUserOption($entry->user, 'GalaDirectDebitOptOut')); ?>
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
                <li class="row">
                  <div class="col">
                    <?=htmlspecialchars($event->name)?><?php if (isset($event->entry_time) && $event->entry_time != null) { ?> <em><?=htmlspecialchars($event->entry_time)?></em><?php } ?>
                  </div>
                  <?php if ($event->allowed) { ?>
                  <div class="col">
                    &pound;<?=htmlspecialchars($event->price_string)?>
                  </div>
                  <?php } ?>
                </li>
                <?php } ?>
              <?php } ?>
              </ul>
            </div>
            <div class="col">
              <div class="d-sm-none mb-3"></div>
              <?php if ($entry->charged && isset($entry->payment_intent->id) && $entry->payment_intent->id != null) { ?>
              <p>
                <strong>
                  Paid with
                </strong><br>
                <i class="fa <?=htmlspecialchars(getCardFA($entry->payment_intent->brand))?>" aria-hidden="true"></i> <span class="sr-only"><?=htmlspecialchars(getCardBrand($entry->payment_intent->brand))?></span> &#0149;&#0149;&#0149;&#0149; <?=htmlspecialchars($entry->payment_intent->last4)?>
              </p>
              <?php } ?>

              <p>
                <?=mb_convert_case($numFormatter->format($count), MB_CASE_TITLE_SIMPLE)?> event<?php if ($count != 1) { ?>s<?php } ?>
              </p>

              <p class="mb-0">
                <strong>
                <?php if (bool($entry->charged)) { ?>Amount charged<?php } else { ?>Fee to pay<?php } ?>
                </strong><br>
                &pound;<?=htmlspecialchars($entry->amount_charged_string)?>
              </p>

              <?php if ($entry->refunded) { ?>
              <p class="mt-3 mb-0">
                <strong>
                  Amount refunded
                </strong><br>
                &pound;<?=htmlspecialchars($entry->amount_refunded_string)?> has been refunded<?php if ($entry->payment_intent->id != null) { ?> to <?=htmlspecialchars(getCardBrand($entry->payment_intent->brand))?> <?=htmlspecialchars($entry->payment_intent->funding)?> card ending <?=htmlspecialchars($entry->payment_intent->last4)?><?php } ?>
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
          <hr>
          <div class="custom-control custom-checkbox custom-control-inline">
            <input type="checkbox" class="custom-control-input" id="paid-<?=htmlspecialchars($entry->id)?>" name="paid-<?=htmlspecialchars($entry->id)?>" <?php if (isset($entry->charged) && $entry->charged) { ?>checked<?php } ?> <?php if (isset($entry->charge_lock) && $entry->charge_lock) { ?>disabled<?php } ?> data-ajax-action="mark-paid" data-entry-id="<?=htmlspecialchars($entry->id)?>">
            <label class="custom-control-label" for="paid-<?=htmlspecialchars($entry->id)?>">Entry paid</label>
          </div>
          <div class="custom-control custom-checkbox custom-control-inline">
            <input type="checkbox" class="custom-control-input" id="approved-<?=htmlspecialchars($entry->id)?>" name="approved-<?=htmlspecialchars($entry->id)?>" <?php if (isset($entry->approved) && $entry->approved) { ?>checked<?php } ?> data-ajax-action="approve-entry" data-entry-id="<?=htmlspecialchars($entry->id)?>">
            <label class="custom-control-label" for="approved-<?=htmlspecialchars($entry->id)?>">Entry approved</label>
          </div>
          <div class="custom-control custom-checkbox custom-control-inline">
            <input type="checkbox" class="custom-control-input" id="processed-<?=htmlspecialchars($entry->id)?>" name="processed-<?=htmlspecialchars($entry->id)?>" <?php if (isset($entry->processed) && $entry->processed) { ?>checked<?php } ?> disabled data-ajax-action="approve-entry" data-entry-id="<?=htmlspecialchars($entry->id)?>">
            <label class="custom-control-label" for="processed-<?=htmlspecialchars($entry->id)?>"><abbr title="Only staff can mark an entry processed, meaning it has been entered into HyTek or SportSystems">Entry processed</abbr></label>
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
  </div>
</div>

<?php

$footer = new \SCDS\Footer();
$footer->addJs("public/js/galas/squad-reps/squad-rep-approval.js");
$footer->render();