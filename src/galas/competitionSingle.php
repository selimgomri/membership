<?php

$sql = "SELECT * FROM `galas` WHERE `GalaID` = '$idLast';";
$result = mysqli_query($link, $sql);
$count = mysqli_num_rows($result);
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
$course = ['LONG', 'SHORT', 'IRREGULAR'];

if ($count == 1) {
  $pagetitle = "Edit " . $row['GalaName'];
  $title = "Edit " . $row['GalaName'];
  $content .= "<p class=\"lead\">Edit a Gala</p>";
  $content .= "<form method=\"post\" action=\"updategala-action\">
  <div class=\"form-group row\">
    <label for=\"galaname\" class=\"col-sm-2 col-form-label\">Gala Name</label>
    <div class=\"col-sm-10\">
      <input type=\"text\" class=\"form-control\" id=\"galaname\" name=\"galaname\" placeholder=\"eg Chester-le-Street Open\" value=\"" . $row['GalaName'] . "\" required>
    </div>
  </div>
  <div class=\"form-group row\">
    <label for=\"length\" class=\"col-sm-2 col-form-label\">Course Length</label>
    <div class=\"col-sm-10\">
      <select class=\"custom-select\" name=\"length\" id=\"length\" required>";
      for ($i=0; $i<sizeof($course); $i++) {
        if ($course[$i] == $row['CourseLength']) {
          $content .= "<option selected value=\"" . $course[$i] . "\">" . $course[$i] . "</option>";
        }
        else {
          $content .= "<option value=\"" . $course[$i] . "\">" . $course[$i] . "</option>";
        }
      }
      $content .= "</select>
    </div>
  </div>
  <div class=\"form-group row\">
    <label for=\"venue\" class=\"col-sm-2 col-form-label\">Gala Venue</label>
    <div class=\"col-sm-10\">
      <input type=\"text\" class=\"form-control\" id=\"venue\" name=\"venue\" value=\"" . $row['GalaVenue'] . "\" placeholder=\"eg Chester-le-Street\" required>
    </div>
  </div>
  <div class=\"form-group row\">
    <label for=\"closingDate\" class=\"col-sm-2 col-form-label\">Closing Date</label>
    <div class=\"col-sm-10\">
      <input type=\"date\" class=\"form-control\" id=\"closingDate\" name=\"closingDate\" placeholder=\"YYYY-MM-DD\" pattern=\"[0-9]{4}-[0-9]{2}-[0-9]{2}\" value=\"" . $row['ClosingDate'] . "\" required>
    </div>
  </div>
  <div class=\"form-group row\">
    <label for=\"lastDate\" class=\"col-sm-2 col-form-label\">Last Day of Gala</label>
    <div class=\"col-sm-10\">
      <input type=\"date\" class=\"form-control\" id=\"galaDate\" name=\"galaDate\" placeholder=\"YYYY-MM-DD\" pattern=\"[0-9]{4}-[0-9]{2}-[0-9]{2}\" value=\"" . $row['GalaDate'] . "\" required>
    </div>
  </div>";
  if ($row['GalaFeeConstant'] == 1) {
    $content .= "
    <div class=\"form-group row\">
      <label for=\"GalaFeeConstant\" class=\"col-sm-2 col-form-label\">Gala Fee Constant?</label>
      <div class=\"col-sm-10\">
        <div class=\"custom-control custom-checkbox mt-2\">
          <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" checked id=\"GalaFeeConstant\" name=\"GalaFeeConstant\">
          <label class=\"custom-control-label\" for=\"GalaFeeConstant\">Tick if all swims are the same price</label>
        </div>
      </div>
    </div>";
  }
  else {
    $content .= "
    <div class=\"form-group row\">
      <label for=\"GalaFeeConstant\" class=\"col-sm-2 col-form-label\">Gala Fee Constant?</label>
      <div class=\"col-sm-10\">
        <div class=\"custom-control custom-checkbox mt-2\">
          <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" checked id=\"GalaFeeConstant\" name=\"GalaFeeConstant\">
          <label class=\"custom-control-label\" for=\"GalaFeeConstant\">Tick if all swims are the same price</label>
        </div>
      </div>
    </div>";
  }
  $content .= "
  <div class=\"form-group row\">
    <label for=\"galaFee\" class=\"col-sm-2 col-form-label\">Gala Fee</label>
    <div class=\"col-sm-10\">
      <div class=\"input-group\">
        <div class=\"input-group-prepend\">
          <span class=\"input-group-text\">&pound;</span>
        </div>
        <input type=\"text\" class=\"form-control\" id=\"galaFee\" name=\"galaFee\" aria-describedby=\"galaFeeHelp\" placeholder=\"eg 5.00\" value=\"" . $row['GalaFee'] . "\">
      </div>
      <small id=\"galaFeeHelp\" class=\"form-text text-muted\">If all swims at the gala are the same price, enter it here.</small>
    </div>
  </div>";
  if ($row['HyTek'] == 1) {
    $content .= "
    <div class=\"form-group row\">
      <label for=\"HyTek\" class=\"col-sm-2 col-form-label\">Require times?</label>
      <div class=\"col-sm-10\">
        <div class=\"custom-control custom-checkbox mt-2\">
          <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" checked id=\"HyTek\" name=\"HyTek\">
          <label class=\"custom-control-label\" for=\"HyTek\">Tick if this is a HyTek gala or needs times from parents</label>
        </div>
      </div>
    </div>";
  }
  else {
    $content .= "
    <div class=\"form-group row\">
      <label for=\"HyTek\" class=\"col-sm-2 col-form-label\">Require times?</label>
      <div class=\"col-sm-10\">
        <div class=\"custom-control custom-checkbox mt-2\">
          <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"HyTek\" name=\"HyTek\">
          <label class=\"custom-control-label\" for=\"HyTek\">Tick if this is a HyTek gala or needs times from parents</label>
        </div>
      </div>
    </div>";
  }
  $content .= "<input type=\"hidden\" value=\"" . $idLast . "\" name=\"galaID\"><p><button class=\"btn btn-outline-dark\" type=\"submit\" id=\"submit\">Update Gala</button></p>
  ";
}
else {
  $pagetitle = "No galas found";
  $title = "No galas found";
  $content = "<p class=\"lead\">Try going <a href=\"" . autoUrl('galas/competitions') . "\">back to competitions</a>.</p>";
}
?>
