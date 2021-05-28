<?php
$pagetitle = "Add a Gala";
$title = "Add a Gala for Entries";
$content = "<p class=\"lead\">Add a Gala for parents to enter</p>";
$content .= "<div class=\"row\"><div class=\"col-md-10 col-lg-8\"><form method=\"post\" action=\"" . autoUrl("galas/addgala") . "\">
<div class=\"mb-3 row\">
  <label for=\"galaname\" class=\"col-sm-2 col-form-label\">Gala Name</label>
  <div class=\"col-sm-10\">
    <input type=\"text\" class=\"form-control\" id=\"galaname\" name=\"galaname\" placeholder=\"eg Chester-le-Street Open\" required>
  </div>
</div>
<div class=\"mb-3 row\">
  <label for=\"description\" class=\"col-sm-2 col-form-label\">Description (optional)</label>
  <div class=\"col-sm-10\">
    <textarea class=\"form-control mono\" id=\"description\" name=\"description\" aria-describedby=\"descriptionHelp\"></textarea>
    <small id=\"descriptionHelp\" class=\"form-text text-muted\">
      A description is optional and will only be displayed if you enter something. Markdown is supported here.
    </small>
  </div>
</div>
<div class=\"mb-3 row\">
  <label for=\"length\" class=\"col-sm-2 col-form-label\">Course Length</label>
  <div class=\"col-sm-10\">
    <select class=\"form-select\" name=\"length\" id=\"length\" required>
      <option value=\"LONG\">Long Course</option>
      <option value=\"SHORT\">Short Course</option>
      <option value=\"IRREGULAR\">Other Pool Length or Open Water</option>
    </select>
  </div>
</div>
<div class=\"mb-3 row\">
  <label for=\"venue\" class=\"col-sm-2 col-form-label\">Gala Venue</label>
  <div class=\"col-sm-10\">
    <input type=\"text\" class=\"form-control\" id=\"venue\" name=\"venue\" placeholder=\"eg Chester-le-Street\" required>
  </div>
</div>
<div class=\"mb-3 row\">
  <label for=\"closingDate\" class=\"col-sm-2 col-form-label\">Closing Date</label>
  <div class=\"col-sm-10\">
    <input type=\"date\" class=\"form-control\" id=\"closingDate\" name=\"closingDate\" placeholder=\"YYYY-MM-DD\" value=\"" . date("Y-m-d") . "\" pattern=\"[0-9]{4}-[0-9]{2}-[0-9]{2}\" required>
  </div>
</div>
<div class=\"mb-3 row\">
  <label for=\"lastDate\" class=\"col-sm-2 col-form-label\">Last Day of Gala</label>
  <div class=\"col-sm-10\">
    <input type=\"date\" class=\"form-control\" id=\"lastDate\" name=\"lastDate\" placeholder=\"YYYY-MM-DD\" value=\"" . date("Y-m-d") . "\" pattern=\"[0-9]{4}-[0-9]{2}-[0-9]{2}\" required>
  </div>
</div>
<div class=\"mb-3 row\">
  <label for=\"galaFee\" class=\"col-sm-2 col-form-label\">Gala Fee</label>
  <div class=\"col-sm-10\">
    <div class=\"input-group\">
      <div class=\"input-group-prepend\">
        <span class=\"input-group-text\">&pound;</span>
      </div>
      <input type=\"text\" class=\"form-control\" id=\"galaFee\" name=\"galaFee\" aria-describedby=\"galaFeeHelp\" placeholder=\"eg 5.00\">
    </div>
    <small id=\"galaFeeHelp\" class=\"form-text text-muted\">Enter the <strong>most common</strong> price for swims at this gala.</small>
  </div>
</div>
<div class=\"mb-3 row\">
  <label for=\"HyTek\" class=\"col-sm-2 col-form-label\">Require times?</label>
  <div class=\"col-sm-10\">
    <div class=\"custom-control form-checkbox mt-2\">
      <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"HyTek\" name=\"HyTek\">
      <label class=\"custom-control-label\" for=\"HyTek\">Tick if this is a HyTek gala or needs times from parents</label>
    </div>
  </div>
</div>
<div class=\"mb-3 row\">
  <label for=\"coachDecides\" class=\"col-sm-2 col-form-label\">Coach decides entries?</label>
  <div class=\"col-sm-10\">
    <div class=\"custom-control form-checkbox mt-2\">
      <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"coachDecides\" name=\"coachDecides\">
      <label class=\"custom-control-label\" for=\"coachDecides\">Tick if a coach will make entries for this gala</label>
    </div>
  </div>
</div>
<div class=\"mb-3 row\">
  <label for=\"approvalNeeded\" class=\"col-sm-2 col-form-label\">Approval needed?</label>
  <div class=\"col-sm-10\">
    <div class=\"custom-control form-checkbox mt-2\">
      <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"approvalNeeded\" name=\"approvalNeeded\">
      <label class=\"custom-control-label\" for=\"approvalNeeded\">Tick if entries must first be approved by a squad rep. Entries are automatically approved if a squad does not have a squad rep.</label>
    </div>
  </div>
</div>
<p><button class=\"btn btn-success\" type=\"submit\" id=\"submit\">Add gala</button></p>
</div></div></form>
<p>This gala will immediately be available for parents to enter, unless coaches decide entries.</p>
";

$use_white_background = true;
include BASE_PATH . "views/header.php";
include "galaMenu.php"; ?>
<div class="container">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?=autoUrl("galas")?>">Galas</a></li>
      <li class="breadcrumb-item active" aria-current="page">Add gala</li>
    </ol>
  </nav>
  <?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['ErrorState'])) {
    echo $_SESSION['TENANT-' . app()->tenant->getId()]['ErrorState'];
    unset($_SESSION['TENANT-' . app()->tenant->getId()]['ErrorState']);
  } ?>
  <?php echo "<h1>" . $title . "</h1>";
  echo $content; ?>
</div>
<?php $footer = new \SCDS\Footer();
$footer->render();
?>
