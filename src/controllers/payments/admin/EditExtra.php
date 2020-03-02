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

  <nav aria-label="breadcrumb">
    <ol class="breadcrumb bg-light">
      <li class="breadcrumb-item"><a href="<?=htmlspecialchars(autoUrl('payments'))?>">Payments</a></li>
      <li class="breadcrumb-item"><a href="<?=htmlspecialchars(autoUrl('payments/extrafees'))?>">Extras</a></li>
      <li class="breadcrumb-item"><a href="<?=htmlspecialchars(autoUrl('payments/extrafees/' . $id))?>"><?=htmlspecialchars($row['ExtraName'])?></a></li>
      <li class="breadcrumb-item active" aria-current="page">Edit</li>
      
    </ol>
  </nav>

  <div class="">
    <h1>
			Edit <?=htmlspecialchars($row['ExtraName'])?>
		</h1>
    <p class="lead">Edit this extra monthly fee.</p>

    <div class="row">
      <div class="col-lg-8">
        <?php
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
              <input type="number" min="0" step="0.01"  class="form-control" id="price" name="price" placeholder="Enter price" value="<?=htmlspecialchars($row['ExtraFee'])?>">
            </div>
          </div>

          <div class="form-group">
            <label>Monthly payment or refund</label>
            <div class="custom-control custom-radio">
              <input type="radio" id="type-pay" name="pay-credit-type" class="custom-control-input" <?php if ($row['Type'] == 'Payment') { ?>checked<?php } ?> value="Payment">
              <label class="custom-control-label" for="type-pay">Payment</label>
            </div>
            <div class="custom-control custom-radio">
              <input type="radio" id="type-credit" name="pay-credit-type" class="custom-control-input" <?php if ($row['Type'] == 'Refund') { ?>checked<?php } ?> value="Refund">
              <label class="custom-control-label" for="type-credit">Credit/refund</label>
            </div>
          </div>

          <p class="mb-0">
            <button type="submit" class="btn btn-dark">
              Save changes
            </button>
          </p>
        </form>
      </div>
    </div>
  </div>
</div>

<?php

$footer = new \SCDS\Footer();
$footer->render();

?>
