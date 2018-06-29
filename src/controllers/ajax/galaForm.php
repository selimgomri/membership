<?php

$count = 0;
$rows = 0;
$sql = "";
$response = "";

if ((isset($_REQUEST["galaID"])) && (isset($_REQUEST["swimmer"]))) {
  // get the galaID parameter from request
  $galaID = mysqli_real_escape_string($link, htmlentities($_REQUEST["galaID"]));
  $memberID = mysqli_real_escape_string($link, htmlentities($_REQUEST["swimmer"]));

  $sql = "SELECT * FROM galaEntries WHERE GalaID = '$galaID' AND MemberID = '$memberID';";
  $result = mysqli_query($link, $sql);
  $rows = mysqli_num_rows($result);
  $row = mysqli_fetch_array($result);

  if ($rows>0) {
    $response = '<div class="alert alert-warning"><strong>Oops. You\'ve aleady entered this swimmer into this gala</strong> <br>
    You might want to check that. ';
    if ($row['EntryProcessed'] == 0) {
      $response .= 'We\'ve not processed your entry yet, so you <a class="alert-link" href="' . autoUrl("galas/entries/" . $row["EntryID"]) . '">can edit that entry</a> if you need to make changes.';
    }
    $response .= '</div>';
  }
  else {

  	$sql = "SELECT `HyTek`, `GalaName`, `GalaFeeConstant` FROM galas WHERE GalaID = '$galaID';";

  	$result = mysqli_query($link, $sql);
  	$row = mysqli_fetch_array($result, MYSQLI_ASSOC);

  	$swimsArray = ['50Free','100Free','200Free','400Free','800Free','1500Free','50Breast','100Breast','200Breast','50Fly','100Fly','200Fly','50Back','100Back','200Back','100IM','150IM','200IM','400IM',];
  	$swimsTimeArray = ['50FreeTime','100FreeTime','200FreeTime','400FreeTime','800FreeTime','1500FreeTime','50BreastTime','100BreastTime','200BreastTime','50FlyTime','100FlyTime','200FlyTime','50BackTime','100BackTime','200BackTime','100IMTime','150IMTime','200IMTime','400IMTime',];
  	$swimsTextArray = ['50 Free','100 Free','200 Free','400 Free','800 Free','1500 Free','50 Breast','100 Breast','200 Breast','50 Fly','100 Fly','200 Fly','50 Back','100 Back','200 Back','100 IM','150 IM','200 IM','400 IM',];
    $rowArray = [1, null, null, null, null, 2, 1,  null, 2, 1, null, 2, 1, null, 2, 1, null, null, 2];
    $rowArrayText = ["Freestyle", null, null, null, null, 2, "Breaststroke",  null, 2, "Butterfly", null, 2, "Freestyle", null, 2, "Individual Medley", null, null, 2];

  	$HyTek = 1;

  	if ($row['HyTek'] == 1) {
  		$response .= "
  		<p>All swims possible under ASA Rules are shown below. Not all of these events may be available for " . $row['GalaName'] . "</p>
      <p>We're sorry, but for this gala we can't get swimmer's times automatically from British Swimming. As a result, you need to provide best times below. Entering a time automatically marks you as entering that event.</p>
  		<form>
      <div class=\"\">
  		";
  		for ($i = 0; $i < sizeof($swimsTimeArray); $i++) {
        if ($rowArray[$i] == 1) {
          $response .= "<h3>" . $rowArrayText[$i] . "</h3>";
          $response .= "<div class=\"galaEntryTimes mb-3\">";
        }
  			$response .= "
  		  <div class=\"form-group mb-0\">
  				<label>" . $swimsTextArray[$i] . "</label>
  				<div class=\"row no-gutters\">
  			    <div class=\"col\">
  			      <input type=\"number\" class=\"form-control\" placeholder=\"Minutes\" name=\"" . $swimsTimeArray[$i] . "Mins\" id=\"" . $swimsTimeArray[$i] . "Mins\" autocomplete=\"off\" pattern=\"[0-9]*\" inputmode=\"numeric\" min=\"0\">
  			    </div>
  					<div class=\"col\">
  			      <input type=\"number\" class=\"form-control\" placeholder=\"Seconds\" name=\"" . $swimsTimeArray[$i] . "Secs\" id=\"" . $swimsTimeArray[$i] . "Secs\" autocomplete=\"off\" pattern=\"[0-9]*\" inputmode=\"numeric\" min=\"0\" max=\"59\">
  			    </div>
  					<div class=\"col\">
  			      <input type=\"number\" class=\"form-control\" placeholder=\"Hundreds\" name=\"" . $swimsTimeArray[$i] . "Hunds\" id=\"" . $swimsTimeArray[$i] . "Hunds\" autocomplete=\"off\" pattern=\"[0-9]*\" inputmode=\"numeric\" min=\"0\" max=\"99\">
  			    </div>
  				</div>
  		  </div>";
        if ($rowArray[$i] == 2) {
          $response .= "</div>";
        }
  		}
  		$response .= '</div><input type="hidden" value="1" name="TimesRequired">';
  		$response .= "
  		</form>
  		";
  	}
  	else {
  		$response .= "
  		<p>All swims possible under ASA Rules are shown below. Not all of these events may be available for " . $row['GalaName'] . "</p>
  	  <div class=\"row mb-3\">
  	    <div class=\"col-sm-4 col-md-2\">
  	    <div class=\"custom-control custom-checkbox\">
  	      <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"50Free\" name=\"50Free\">
  	      <label class=\"custom-control-label\" for=\"50Free\">50 Freestyle</label>
  	    </div>
  	    </div>
  	    <div class=\"col-sm-4 col-md-2\">
  	    <div class=\"custom-control custom-checkbox\">
  	      <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"100Free\" name=\"100Free\">
  	      <label class=\"custom-control-label\" for=\"100Free\">100 Freestyle</label>
  	    </div>
  	    </div>
  	    <div class=\"col-sm-4 col-md-2\">
  	    <div class=\"custom-control custom-checkbox\">
  	      <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"200Free\" name=\"200Free\">
  	      <label class=\"custom-control-label\" for=\"200Free\">200 Freestyle</label>
  	    </div>
  	    </div>
  	    <div class=\"col-sm-4 col-md-2\">
  	    <div class=\"custom-control custom-checkbox\">
  	      <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"400Free\" name=\"400Free\">
  	      <label class=\"custom-control-label\" for=\"400Free\">400 Freestyle</label>
  	    </div>
  	    </div>
  	    <div class=\"col-sm-4 col-md-2\">
  	    <div class=\"custom-control custom-checkbox\">
  	      <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"800Free\" name=\"800Free\">
  	      <label class=\"custom-control-label\" for=\"800Free\">800 Freestyle</label>
  	    </div>
  	    </div>
  	    <div class=\"col-sm-4 col-md-2\">
  	    <div class=\"custom-control custom-checkbox\">
  	      <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"1500Free\" name=\"1500Free\">
  	      <label class=\"custom-control-label\" for=\"1500Free\">1500 Freestyle</label>
  	    </div>
  	    </div>
  	  </div>
  	  <div class=\"row mb-3\">
  	    <div class=\"col-sm-4 col-md-2\">
  	    <div class=\"custom-control custom-checkbox\">
  	      <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"50Breast\" name=\"50Breast\">
  	      <label class=\"custom-control-label\" for=\"50Breast\">50 Breaststroke</label>
  	    </div>
  	    </div>
  	    <div class=\"col-sm-4 col-md-2\">
  	    <div class=\"custom-control custom-checkbox\">
  	      <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"100Breast\" name=\"100Breast\">
  	      <label class=\"custom-control-label\" for=\"100Breast\">100 Breaststroke</label>
  	    </div>
  	    </div>
  	    <div class=\"col-sm-4 col-md-2\">
  	    <div class=\"custom-control custom-checkbox\">
  	      <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"200Breast\" name=\"200Breast\">
  	      <label class=\"custom-control-label\" for=\"200Breast\">200 Breaststroke</label>
  	    </div>
  	    </div>
  	  </div>
  	  <div class=\"row mb-3\">
  	    <div class=\"col-sm-4 col-md-2\">
  	    <div class=\"custom-control custom-checkbox\">
  	      <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"50Fly\" name=\"50Fly\">
  	      <label class=\"custom-control-label\" for=\"50Fly\">50 Butterfly</label>
  	    </div>
  	    </div>
  	    <div class=\"col-sm-4 col-md-2\">
  	    <div class=\"custom-control custom-checkbox\">
  	      <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"100Fly\" name=\"100Fly\">
  	      <label class=\"custom-control-label\" for=\"100Fly\">100 Butterfly</label>
  	    </div>
  	    </div>
  	    <div class=\"col-sm-4 col-md-2\">
  	    <div class=\"custom-control custom-checkbox\">
  	      <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"200Fly\" name=\"200Fly\">
  	      <label class=\"custom-control-label\" for=\"200Fly\">200 Butterfly</label>
  	    </div>
  	    </div>
  	  </div>
  	  <div class=\"row mb-3\">
  	    <div class=\"col-sm-4 col-md-2\">
  	    <div class=\"custom-control custom-checkbox\">
  	      <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"50Back\" name=\"50Back\">
  	      <label class=\"custom-control-label\" for=\"50Back\">50 Backstroke</label>
  	    </div>
  	    </div>
  	    <div class=\"col-sm-4 col-md-2\">
  	    <div class=\"custom-control custom-checkbox\">
  	      <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"100Back\" name=\"100Back\">
  	      <label class=\"custom-control-label\" for=\"100Back\">100 Backstroke</label>
  	    </div>
  	    </div>
  	    <div class=\"col-sm-4 col-md-2\">
  	    <div class=\"custom-control custom-checkbox\">
  	      <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"200Back\" name=\"200Back\">
  	      <label class=\"custom-control-label\" for=\"200Back\">200 Backstroke</label>
  	    </div>
  	    </div>
  	  </div>
  	  <div class=\"row mb-3\">
  	    <div class=\"col-sm-4 col-md-2\">
  	    <div class=\"custom-control custom-checkbox\">
  	      <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"100IM\" name=\"100IM\">
  	      <label class=\"custom-control-label\" for=\"100IM\">100 IM</label>
  	    </div>
  	    </div>
  	    <div class=\"col-sm-4 col-md-2\">
  	    <div class=\"custom-control custom-checkbox\">
  	      <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"150IM\" name=\"150IM\">
  	      <label class=\"custom-control-label\" for=\"150IM\">150 IM</label>
  	    </div>
  	    </div>
  	    <div class=\"col-sm-4 col-md-2\">
  	    <div class=\"custom-control custom-checkbox\">
  	      <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"200IM\" name=\"200IM\">
  	      <label class=\"custom-control-label\" for=\"200IM\">200 IM</label>
  	    </div>
  	    </div>
  	    <div class=\"col-sm-4 col-md-2\">
  	    <div class=\"custom-control custom-checkbox\">
  	      <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"400IM\" name=\"400IM\">
  	      <label class=\"custom-control-label\" for=\"400IM\">400 IM</label>
  	    </div>
  	    </div>
  	  </div>";
  		$response .= '<input type="hidden" value="0" name="TimesRequired">';
  	}
    if ($row['GalaFeeConstant'] != 1) {
      $response .= '
      <div class="form-group">
        <label for="galaFee">Enter Total</label>
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text">&pound;</span>
          </div>
          <input aria-describedby="feeHelp" type="number" id="galaFee" name="galaFee" class="form-control" required min="0">
        </div>
        <small id="feeHelp" class="form-text text-muted">Sadly we can\'t automatically calculate the entry fee for this gala</small>
      </div>';
    }

  }

	echo $response;

}
else {
	halt(404);
}
?>
