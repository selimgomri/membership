<?php

global $db;

// Get users for list
$getUsers = $db->query("SELECT Forename, Surname, UserID FROM users ORDER BY Forename ASC, Surname ASC");
$userDetails = $getUsers->fetch(PDO::FETCH_ASSOC);

$user = $_SESSION['UserID'];

$list = $db->prepare("SELECT * FROM `targetedLists` WHERE `ID` = ?");
$list->execute([$id]);
$row = $list->fetch(PDO::FETCH_ASSOC);

if ($row == null) {
  halt(404);
}

$squads = $db->query("SELECT * FROM `squads` ORDER BY `SquadFee` DESC, `SquadName` ASC");

$pagetitle = htmlspecialchars($row['Name']) . " - Lists";

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/notifyMenu.php";

 ?>

<div class="container">

  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?=htmlspecialchars(autoUrl("notify"))?>">Notify</a></li>
      <li class="breadcrumb-item"><a href="<?=htmlspecialchars(autoUrl("notify/lists"))?>">Lists</a></li>
      <li class="breadcrumb-item active" aria-current="page"><?=htmlspecialchars($row['Name'])?></li>
    </ol>
  </nav>

  <div class="row align-items-center mb-3">
    <div class="col-md-6">
	    <h1><?=htmlspecialchars($row['Name'])?></h1>
      <p class="lead"><?=htmlspecialchars($row['Description'])?></p>
    </div>
    <div class="col text-sm-right">
      <a href="<?=autoUrl("notify/lists/" . $id . "/edit")?>"
        class="btn btn-dark">Edit</a>
      <a href="<?=autoUrl("notify/lists/" . $id . "/delete")?>"
        class="btn btn-danger">Delete</a>
    </div>
  </div>
  <div class="row">
    <div class="col order-md-1 mb-3">
      <div class="card mb-3">
        <div class="card-header">
          Add member to list
        </div>
        <form class="card-body">
          <div class="form-group">
            <label for="squadSelect">Select Squad (Optional)</label>
            <select class="custom-select" id="squadSelect" name="squadSelect">
              <option value="all" selected>Choose...</option>
              <?php while ($squadsRow = $squads->fetch(PDO::FETCH_ASSOC)) { ?>
              <option value="<?=$squadsRow['SquadID']?>">
                <?=htmlspecialchars($squadsRow['SquadName'])?>
              </option>
              <?php } ?>
            </select>
          </div>
          <div class="form-group">
            <label for="swimmerSelect">Select member</label>
            <select class="custom-select" id="swimmerSelect" name="swimmerSelect" disabled>
              <option value="null" selected>Select squad first</option>
            </select>
          </div>
            <button type="button" class="btn btn-success" id="addSwimmer" data-ajax-url="<?=htmlspecialchars(autoUrl("notify/lists/ajax/" . $id))?>" disabled>
              Add member to list
            </button>
            <div id="status">
            </div>
        </form>
      </div>

      <div class="card">
        <div class="card-header">
          Add user to list
        </div>
        <form class="card-body">
          <div class="form-group">
            <label for="user-select">Search by name for user</label>
            <input type="text" name="user-name-search" id="user-name-search" class="form-control">
            </select>
          </div>
          <div class="form-group">
            <label for="user-select">Select user</label>
            <select class="custom-select" id="user-select" name="user-select" disabled>
              <option value="null" selected>Search for a user</option>
            </select>
          </div>
            <button type="button" class="btn btn-success" id="user-add" data-ajax-url="<?=htmlspecialchars(autoUrl("notify/lists/ajax/" . $id))?>" disabled>
              Add user to list
            </button>
            <div id="user-status">
            </div>
        </form>
      </div>
    </div>
    <div class="col-md-6 order-md-0">
      <div id="output">
        <div class="ajaxPlaceholder">
          <span class="h1 d-block">
            <i class="fa fa-spin fa-circle-o-notch" aria-hidden="true"></i>
            <br>Loading Content
          </span>If content does not display, please turn on JavaScript
        </div>
      </div>
    </div>
  </div>
</div>

<?php $footer = new \SCDS\Footer();
$footer->addJs("public/js/notify/TargetedListEditor.js");
$footer->render();
