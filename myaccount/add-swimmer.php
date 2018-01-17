<?php
  $pagetitle = "Add a swimmer";
  include "../header.php";

  $errorMessage = "";
  $errorState = false;

?>
<div class="container">
  <h1>Add a swimmer</h1>
  <p>We need a few details to find a swimmer from our database.</p>
  <hr>
  <form method="post" action="add-swimmer-action.php" name="register" id="register">
    <h2>Details</h2>
    <div class="form-group">
      <label for="asa">Swimmer's ASA Number</label>
      <input class="form-control mb-0" type="text" name="asa" id="asa" placeholder="123456" required>
    </div>
    <div class="form-group">
      <label for="accessKey">Access Key</label>
      <input class="form-control mb-0" type="text" name="accessKey" id="accessKey" placeholder="123456" required>
    </div>
    <input type="submit" class="btn btn-success mb-4" value="Add Swimmer">
</div>

<?php include "../footer.php" ?>
