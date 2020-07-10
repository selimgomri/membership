<?php

$search = "";
if (isset($_GET['search'])) {
  $search = $_GET['search'];
}

$pagetitle = "Users";
include BASE_PATH . "views/header.php";

?>

<div class="container">
  <h1>User Directory</h1>
  <p class="lead">A list of users. Useful for changing account settings.</p>
  <div class="form-group row">
    <label class="col-sm-4 col-md-3 col-lg-2" for="search">Search by Name</label>
    <div class="col-sm-8 col-md-9 col-lg-10">
      <input class="form-control" id="search" name="search" value="<?= htmlspecialchars($search) ?>">
    </div>
  </div>

  <div id="output" data-ajax-url="<?= htmlspecialchars(autoUrl('users/ajax/userList')) ?>" data-page="<?= htmlspecialchars(autoUrl("users")) ?>">
    <div class="ajaxPlaceholder">
      <span class="h1 d-block">
        <i class="fa fa-spin fa-circle-o-notch" aria-hidden="true"></i><br>
        Loading Content
      </span>
      If content does not display, please turn on JavaScript
    </div>
  </div>

</div>

<?php $footer = new \SCDS\Footer();
$footer->addJs("public/js/users/list.js");
$footer->render();
