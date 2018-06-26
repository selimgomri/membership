<?php
  $pagetitle = "Add a swimmer";
  include BASE_PATH . "views/header.php";

  $errorMessage = "";
  $errorState = false;

if ($_SESSION['AccessLevel'] == "Parent") { ?>
<div class="container">
  <h1>Add a swimmer</h1>
  <p>We need a few details to find a swimmer from our database.</p>
  <? if (isset($_SESSION['ErrorState'])) {
    echo $_SESSION['ErrorState'];
    unset($_SESSION['ErrorState']);
  } ?>
  <hr>
  <form method="post" action="<?php echo autoUrl("myaccount/addswimmer"); ?>" name="register" id="register">
    <h2>Details</h2>
    <div class="form-group">
      <label for="asa">Swimmer's ASA Number</label>
      <input class="form-control mb-0" type="text" name="asa" id="asa" placeholder="123456" required>
    </div>
    <div class="form-group">
      <label for="accessKey">Access Key</label>
      <input class="form-control mb-0" type="text" name="accessKey" id="accessKey" placeholder="1A3B5C" required>
    </div>
    <input type="submit" class="btn btn-outline-dark mb-4" value="Add Swimmer">
</div>
<?php }

include BASE_PATH . "views/footer.php"; ?>
