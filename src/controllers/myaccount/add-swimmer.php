<?php

$fluidContainer = true;
$pagetitle = "Add a swimmer";
include BASE_PATH . "views/header.php";

$errorMessage = "";
$errorState = false;

$id = $acs = null;

if (isset($_GET['id'])) {
  $id = $_GET['id'];
}

if (isset($_GET['acs'])) {
  $acs = $_GET['acs'];
}

if ($_SESSION['AccessLevel'] == "Parent") { ?>
  <div class="container-fluid">

    <div class="row justify-content-between">
      <div class="col-md-3 d-none d-md-block">
        <?php
          $list = new \CLSASC\BootstrapComponents\ListGroup(file_get_contents(BASE_PATH . 'controllers/myaccount/ProfileEditorLinks.json'));
          echo $list->render('addswimmer');
        ?>
      </div>
      <div class="col-md-9">
      <?php if (isset($_SESSION['AddSwimmerSuccessState'])) {
        echo $_SESSION['AddSwimmerSuccessState'];
        unset($_SESSION['AddSwimmerSuccessState']);
      } else if (isset($_SESSION['ErrorState'])) {
        echo $_SESSION['ErrorState'];
        unset($_SESSION['ErrorState']);
      } else { ?>
      <div class="">
        <h1>Add a swimmer</h1>
        <p>We need a few details to find a swimmer from our database.</p>
        <?php if (isset($_SESSION['ErrorState'])) {
          echo $_SESSION['ErrorState'];
          unset($_SESSION['ErrorState']);
        } ?>
        <?php if ($id != null && $acs != null) { ?>
          <div class="alert alert-success">
            <p class="mb-0"><strong>Thanks for following that link</strong></p>
            <p class="mb-0">We've automatically filled in the required details for
            you. <strong>Press Add Swimmer</strong> to add the swimmer to your
            account.</p>
          </div>
        <?php } ?>
        <form method="post" class="cell" action="<?php echo autoUrl("myaccount/addswimmer"); ?>" name="register" id="register">
          <h2>Details</h2>
          <div class="form-group">
            <label for="asa">Swimmer's Swim England Number or Temporary Membership Number</label>
            <input class="form-control mb-0" type="text" name="asa" id="asa" placeholder="123456" required value="<?=htmlspecialchars($id)?>">
          </div>
          <div class="form-group">
            <label for="accessKey">Access Key</label>
            <input class="form-control mb-0 mono" type="text" name="accessKey" id="accessKey" placeholder="1A3B5C" required value="<?=htmlspecialchars($acs)?>">
          </div>

          <input type="submit" class="btn btn-success" value="Add Swimmer">
        </form>
      </div>
      <?php } ?>
    </div>
  </div>
</div>
<?php }

include BASE_PATH . "views/footer.php"; ?>
