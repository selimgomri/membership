<?php

$disabled = "";

$sql = "SELECT * FROM ((galaEntries INNER JOIN members ON galaEntries.MemberID = members.MemberID) INNER JOIN galas ON galaEntries.GalaID = galas.GalaID) WHERE galaEntries.GalaID = '$id' AND `Charged` = '0' LIMIT 1;";
$result = mysqli_query($link, $sql);
$count = mysqli_num_rows($result);
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);

if ($count == 1) {
  $swimsArray = ['50Free','100Free','200Free','400Free','800Free','1500Free','50Breast','100Breast','200Breast','50Fly','100Fly','200Fly','50Back','100Back','200Back','100IM','150IM','200IM','400IM',];
  $swimsTimeArray = ['50FreeTime','100FreeTime','200FreeTime','400FreeTime','800FreeTime','1500FreeTime','50BreastTime','100BreastTime','200BreastTime','50FlyTime','100FlyTime','200FlyTime','50BackTime','100BackTime','200BackTime','100IMTime','150IMTime','200IMTime','400IMTime',];
  $swimsTextArray = ['50 Free','100 Free','200 Free','400 Free','800 Free','1500 Free','50 Breast','100 Breast','200 Breast','50 Fly','100 Fly','200 Fly','50 Back','100 Back','200 Back','100 IM','150 IM','200 IM','400 IM',];
  $rowArray = [1, null, null, null, null, 2, 1,  null, 2, 1, null, 2, 1, null, 2, 1, null, null, 2];
  $rowArrayText = ["Freestyle", null, null, null, null, 2, "Breaststroke",  null, 2, "Butterfly", null, 2, "Freestyle", null, 2, "Individual Medley", null, null, 2];

  $pagetitle = $row['MForename'] . " " . $row['MSurname'] . " - " . $row['GalaName'] . "";
  include BASE_PATH . "views/header.php";
	include BASE_PATH . "views/paymentsMenu.php"; ?>
  <div class="container">
		<div class="p-3 my-3 text-white bg-primary rounded shadow">
	    <h1 class="h6 mb-0"><strong><?php echo $row['MForename'] . " " . $row['MSurname']; ?></strong></h1>
	    <p class="mb-0">For <?php echo $row['GalaName']; ?></p>
		</div>

    <?php
    $closingDate = new DateTime($row['ClosingDate']);
    $theDate = new DateTime('now');
    $closingDate = $closingDate->format('Y-m-d');
    $theDate = $theDate->format('Y-m-d');

		?>

    <form method="post">

			<div class="my-3 p-3 bg-white rounded shadow">
				<div class="form-row mb-0">
					<div class="form-group col-md-6">
					  <label for="pay">Price to Pay (For all entries)</label>
						<div class="input-group">
							<div class="input-group-prepend">
						    <span class="input-group-text">&pound;</span>
						  </div>
					  	<input type="text" class="form-control" id="pay" name="pay"
							<?php if (!is_null($row['FeeToPay'])) { echo ' value="' . number_format($row['FeeToPay'],2,'.','') . '" '; } ?> placeholder="Amount to pay" required>
						</div>
					</div>
					<div class="form-group col-md-6">
					  <label for="refund">Amount Refunded</label>
						<div class="input-group">
							<div class="input-group-prepend">
						    <span class="input-group-text">&pound;</span>
						  </div>
					  	<input type="text" class="form-control" id="refund" name="refund" placeholder="Refund for Rejections">
						</div>
					</div>
				</div>
        <input type="hidden" name="entryID" value="<?php echo $row['EntryID']; ?>">
				<p class="mb-0"><button class="btn btn-dark" type="submit">Charge Parent</button>
			</div>

			<div class="my-3 p-3 bg-white rounded shadow">

	    <?php if ($row['HyTek'] != 1) {
	      for ($i=0; $i<sizeof($swimsArray); $i++) {
	        if ($rowArray[$i] == 1) { ?>
	          <div class="row mb-2">
	        <?php }
	        if ($row[$swimsArray[$i]] == 1) { ?>
	          <div class="col-sm-4 col-md-2">
	            <div class="custom-control custom-checkbox">
	              <input type="checkbox" value="1" class="custom-control-input" id="<?php echo $swimsArray[$i]; ?>" checked disabled name="<?php echo $swimsArray[$i]; ?>">
	              <label class="custom-control-label" for="<?php echo $swimsArray[$i]; ?>"><?php echo $swimsTextArray[$i]; ?></label>
	            </div>
	          </div>
	        <?php }
	        else { ?>
	          <div class="col-sm-4 col-md-2">
	            <div class="custom-control custom-checkbox">
	              <input type="checkbox" value="1" class="custom-control-input" id="<?php echo $swimsArray[$i]; ?>" disabled name="<?php echo $swimsArray[$i]; ?>">
	              <label class="custom-control-label" for="<?php echo $swimsArray[$i]; ?>"><?php echo $swimsTextArray[$i]; ?></label>
	            </div>
	          </div>
	        <?php }
	        if ($rowArray[$i] == 2) { ?>
	          </div>
	        <?php }
	      } ?>
	      <input type="hidden" value="0" name="TimesRequired">
	    <?php }
	    else {
	      for ($i = 0; $i < sizeof($swimsTimeArray); $i++) {
	        $time = $row[$swimsTimeArray[$i]];
	        $colonPos = mb_strpos($time, ":");
	        $stopPos = mb_strpos($time, ".");
	        $mins = sprintf('%02d',substr($time, 0, $colonPos));
	        $secs = sprintf('%02d',substr($time, $colonPos+1, 2));
	        $hunds = sprintf('%02d',substr($time, $stopPos+1, 2));
	        if ($mins == 0) {
	          $mins = "";
	        }
	        if ($secs == 0) {
	          $secs = "";
	        }
	        if ($hunds == 0) {
	          $hunds = "";
	        }

	        if ($rowArray[$i] == 1) { ?>
	          <h3><?php echo $rowArrayText[$i]; ?></h3>
	          <div class="galaEntryTimes mb-3">
	        <?php } ?>
	  		  <div class="form-group mb-0">
	  				<label><?php echo $swimsTextArray[$i]; ?></label>
	  				<div class="row no-gutters">
	  			    <div class="col">
	  			      <input type="number" class="form-control" placeholder="Minutes" value="<?php echo $mins; ?>" name="<?php echo $swimsTimeArray[$i]; ?>Mins" id="<?php echo $swimsTimeArray[$i]; ?>Mins" autocomplete="off" pattern="[0-9]*" inputmode="numeric" min="0">
	  			    </div>
	  					<div class="col">
	  			      <input type="number" class="form-control" placeholder="Seconds" value="<?php echo $secs; ?>" name="<?php echo $swimsTimeArray[$i]; ?>Secs" id="<?php echo $swimsTimeArray[$i]; ?>Secs" autocomplete="off" pattern="[0-9]*" inputmode="numeric" min="0" max="59">
	  			    </div>
	  					<div class="col">
	  			      <input type="number" class="form-control" placeholder="Hundreds" value="<?php echo $hunds; ?>" name="<?php echo $swimsTimeArray[$i]; ?>Hunds" id="<?php echo $swimsTimeArray[$i]; ?>Hunds" autocomplete="off" pattern="[0-9]*" inputmode="numeric" min="0" max="99">
	  			    </div>
	  				</div>
	  		  </div>
	        <?php if ($rowArray[$i] == 2) { ?>
	          </div>
	        <?php }
	  		} ?>
	      <input type="hidden" value="1" name="TimesRequired">
	    <?php } ?>

			</div>
  </form>
</div>
<?php
include BASE_PATH . "views/footer.php";
} else {
  $sql = "SELECT * FROM ((galaEntries INNER JOIN members ON galaEntries.MemberID = members.MemberID) INNER JOIN galas ON galaEntries.GalaID = galas.GalaID) WHERE galaEntries.GalaID = '$id' LIMIT 1;";
  $result = mysqli_query($link, $sql);
  $count = mysqli_num_rows($result);
  if ($count > 0) {
    $pagetitle = $row['MForename'] . " " . $row['MSurname'] . " - " . $row['GalaName'] . "";
    include BASE_PATH . "views/header.php";
  	include BASE_PATH . "views/paymentsMenu.php"; ?>
    <div class="container">
      <div class="row">
        <div class="col-lg-8">
          <h1>You've reached the end</h1>
          <p class="mb-0">There are no more entries to charge for this gala.</p>
          <hr>
          <p>If you think there has been an error, please contact suppot. <a href="mailto:support@chesterlestreetasc.co.uk">support@chesterlestreetasc.co.uk</a>.</p>
        </div>
      </div>
    </div>
    <?php include BASE_PATH . "views/footer.php";
  } else {
    halt(404);
  }
}
