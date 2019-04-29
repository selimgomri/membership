<?php
$pagetitle = "Add a Gala";
$title = "Add a Gala for Entries";
$content = "<p class=\"lead\">Add a Gala for parents to enter</p>";
$content .= "<div class=\"row\"><div class=\"col-md-10 col-lg-8\"><form method=\"post\" action=\"" . autoUrl("galas/addgala") . "\">
<div class=\"form-group row\">
  <label for=\"galaname\" class=\"col-sm-2 col-form-label\">Gala Name</label>
  <div class=\"col-sm-10\">
    <input type=\"text\" class=\"form-control\" id=\"galaname\" name=\"galaname\" placeholder=\"eg Chester-le-Street Open\" required>
  </div>
</div>
<div class=\"form-group row\">
  <label for=\"length\" class=\"col-sm-2 col-form-label\">Course Length</label>
  <div class=\"col-sm-10\">
    <select class=\"custom-select\" name=\"length\" id=\"length\" required>
      <option value=\"LONG\">Long Course</option>
      <option value=\"SHORT\">Short Course</option>
      <option value=\"IRREGULAR\">Other Pool Length or Open Water</option>
    </select>
  </div>
</div>
<div class=\"form-group row\">
  <label for=\"venue\" class=\"col-sm-2 col-form-label\">Gala Venue</label>
  <div class=\"col-sm-10\">
    <input type=\"text\" class=\"form-control\" id=\"venue\" name=\"venue\" placeholder=\"eg Chester-le-Street\" required>
  </div>
</div>
<div class=\"form-group row\">
  <label for=\"closingDate\" class=\"col-sm-2 col-form-label\">Closing Date</label>
  <div class=\"col-sm-10\">
    <input type=\"date\" class=\"form-control\" id=\"closingDate\" name=\"closingDate\" placeholder=\"YYYY-MM-DD\" value=\"" . date("Y-m-d") . "\" pattern=\"[0-9]{4}-[0-9]{2}-[0-9]{2}\" required>
  </div>
</div>
<div class=\"form-group row\">
  <label for=\"lastDate\" class=\"col-sm-2 col-form-label\">Last Day of Gala</label>
  <div class=\"col-sm-10\">
    <input type=\"date\" class=\"form-control\" id=\"lastDate\" name=\"lastDate\" placeholder=\"YYYY-MM-DD\" value=\"" . date("Y-m-d") . "\" pattern=\"[0-9]{4}-[0-9]{2}-[0-9]{2}\" required>
  </div>
</div>
<div class=\"form-group row\">
  <label for=\"GalaFeeConstant\" class=\"col-sm-2 col-form-label\">Gala Fee Constant?</label>
  <div class=\"col-sm-10\">
    <div class=\"custom-control custom-checkbox mt-2\">
      <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"GalaFeeConstant\" name=\"galaFeeConstant\">
      <label class=\"custom-control-label\" for=\"GalaFeeConstant\">Tick if all swims are the same price</label>
    </div>
  </div>
</div>
<div class=\"form-group row\">
  <label for=\"galaFee\" class=\"col-sm-2 col-form-label\">Gala Fee</label>
  <div class=\"col-sm-10\">
    <div class=\"input-group\">
      <div class=\"input-group-prepend\">
        <span class=\"input-group-text\">&pound;</span>
      </div>
      <input type=\"text\" class=\"form-control\" id=\"galaFee\" name=\"galaFee\" aria-describedby=\"galaFeeHelp\" placeholder=\"eg 5.00\">
    </div>
    <small id=\"galaFeeHelp\" class=\"form-text text-muted\">If all swims at the gala are the same price, enter it here.</small>
  </div>
</div>
<div class=\"form-group row\">
  <label for=\"HyTek\" class=\"col-sm-2 col-form-label\">Require times?</label>
  <div class=\"col-sm-10\">
    <div class=\"custom-control custom-checkbox mt-2\">
      <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"HyTek\" name=\"HyTek\">
      <label class=\"custom-control-label\" for=\"HyTek\">Tick if this is a HyTek gala or needs times from parents</label>
    </div>
  </div>
</div>
<p><button class=\"btn btn-success\" type=\"submit\" id=\"submit\">Add Gala to Database</button></p>
</div></div></form>
<p class=\"mb-0\">This gala will immediately be available for parents to enter</p>
";

$use_white_background = true;
include BASE_PATH . "views/header.php";
include "galaMenu.php"; ?>
<div class="container">
  <?php if (isset($_SESSION['ErrorState'])) {
    echo $_SESSION['ErrorState'];
    unset($_SESSION['ErrorState']);
  } ?>
  <?php echo "<h1>" . $title . "</h1>";
  echo $content; ?>
</div>
<?php include BASE_PATH . "views/footer.php";
?>
