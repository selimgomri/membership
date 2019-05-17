<?php

global $db;
$getExtra = $db->prepare("SELECT * FROM `extras` WHERE `ExtraID` = ?");
$getExtra->execute([$id]);
$row = $getExtra->fetch(PDO::FETCH_ASSOC);

if ($row == null) {
	halt(404);
}

$pagetitle = "Editing " . htmlspecialchars($row['ExtraName']);

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/paymentsMenu.php";

?>

<div class="container">
  <div class="">
    <h1 class="border-bottom border-gray pb-2 mb-2">
			Edit <?=htmlspecialchars($row['ExtraName'])?>
		</h1>
    <p class="lead">Edit this extra monthly fee.</p>

    <hr>

    <div class="row">
      <div class="col-lg-8">
        <?
        if (isset($_SESSION['ErrorState'])) {
          echo $_SESSION['ErrorState'];
          unset($_SESSION['ErrorState']);
        }
        ?>
        <form method="post">
          <div class="form-group">
            <label for="name">Extra Name</label>
            <input type="text" class="form-control" id="name" name="name"
						placeholder="Enter name" value="<?=htmlspecialchars($row['ExtraName'])?>">
          </div>

          <div class="form-group">
            <label for="price">Price</label>
            <div class="input-group mb-3">
              <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon3">&pound;</span>
              </div>
              <input type="text" class="form-control" id="price" name="price"
							placeholder="Enter price" value="<?=htmlspecialchars($row['ExtraFee'])?>">
            </div>
          </div>

          <p class="mb-0">
            <button type="submit" class="btn btn-dark">
              Save Changes
            </button>
          </p>
        </form>
      </div>
    </div>
  </div>
</div>

<?php

include BASE_PATH . "views/footer.php";

?>
