<?php

$pagetitle = "SMS Lists";

$squads = $db->query("SELECT SquadName `name`, SquadID id FROM `squads` ORDER BY SquadFee DESC, SquadName ASC");

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/notifyMenu.php";

?>

<div class="container">

  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?=htmlspecialchars(autoUrl("notify"))?>">Notify</a></li>
      <li class="breadcrumb-item active" aria-current="page">SMS List</li>
    </ol>
  </nav>

  <div class="row">
    <div class="col-md-8">
    <h1 class="">
      SMS Contact Lists
    </h1>
    <p class="lead">
      Select a squad to retrive a phone number list. Copy the list into the "To"
      field of your SMS App.
    </p>

		<div class="form-group">
		  <label class="sr-only" for="squad">Select a Squad</label>
		  <select class="custom-select" placeholder="Select a Squad" id="squad" name="squad">
			  <option value="allSquads">Show All Squads</option>;
			  <?php while ($squad = $squads->fetch(PDO::FETCH_ASSOC)) { ?>
				<option value="<?=$squad['id']?>"><?=htmlspecialchars($squad['name'])?></option>
				<?php } ?>
	    </select>
		</div>

		<div class="form-group">
			<input id="output" class="form-control">
		  </input>
		</div>

		<p class="mb-0">
			<button class="btn btn-primary" id="copyButton" data-ajax-url="<?=htmlspecialchars(autoUrl("notify/sms/ajax"))?>">
				Copy to Clipboard
			</button>
		</p>
    </div>
  </div>
</div>

<?php

$footer = new \SCDS\Footer();
$footer->addJs("public/js/notify/GetSMS.js");
$footer->render();

?>
