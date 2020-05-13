<?php

$db = app()->db;
$tenant = app()->tenant;

$pagetitle = "Add a member";
$title = "Add a member";
$content = "<p class=\"lead\">Add a member to the club system.</p>";

$content .= "<div class=\"row\"><div class=\"col col-md-8\">";
if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['ErrorState'])) {
	$content .= $_SESSION['TENANT-' . app()->tenant->getId()]['ErrorState'];
	unset($_SESSION['TENANT-' . app()->tenant->getId()]['ErrorState']);
}
// Main Info Content
$content .= "<form method=\"post\">";
$content .= "
<div class=\"form-row\">
<div class=\"col-sm-4\">
<div class=\"form-group\">
	<label for=\"forename\">Forename</label>
	<input type=\"text\" class=\"form-control\" id=\"forename\" name=\"forename\" placeholder=\"Enter a forename\" required>
</div>
</div>";
$content .= "
<div class=\"col-sm-4\">
<div class=\"form-group\">
	<label for=\"middlenames\">Middle Names</label>
	<input type=\"text\" class=\"form-control\" id=\"middlenames\" name=\"middlenames\" placeholder=\"Enter a middlename\">
</div>
</div>";
$content .= "
<div class=\"col-sm-4\">
<div class=\"form-group\">
	<label for=\"surname\">Surname</label>
	<input type=\"text\" class=\"form-control\" id=\"surname\" name=\"surname\" placeholder=\"Enter a surname\" required>
</div>
</div>
</div>";
$content .= "
<div class=\"form-group\">
	<label for=\"datebirth\">Date of Birth</label>
	<input type=\"date\" class=\"form-control\" id=\"datebirth\" name=\"datebirth\" pattern=\"[0-9]{4}-[0-9]{2}-[0-9]{2}\" placeholder=\"YYYY-MM-DD\" required>
</div>";
$content .= "
<div class=\"form-row\">
<div class=\"col-sm-6\">
<div class=\"form-group\">
	<label for=\"asa\">Swim England Registration Number</label>
	<input type=\"test\" class=\"form-control\" id=\"asa\" name=\"asa\"
	aria-describedby=\"asaHelp\" placeholder=\"Swim England Registration Numer\">
	<small id=\"asaHelp\" class=\"form-text text-muted\">If a swimmer does not yet
	have a Swim England Number, leave this blank and we'll generate a temporary internal
	membership number for this swimmer.</small>
</div>
</div>";
$content .= "
<div class=\"col-sm-6\">
<div class=\"form-group\">
	<label for=\"squad\">Swim England Membership Category</label>
	<select class=\"custom-select\" placeholder=\"Select a Category\" id=\"cat\" name=\"cat\">
		<option value=\"0\">Not a Swim England Member</option>
		<option value=\"1\">Category 1</option>
		<option value=\"2\" selected>Category 2</option>
		<option value=\"3\">Category 3</option>
	</select>
</div>
</div>
</div>";
$content .= "
<div class=\"form-group\">
	<label for=\"sex\">Sex</label>
	<select class=\"custom-select\" id=\"sex\" name=\"sex\" placeholder=\"Select\">
		<option value=\"Male\">Male</option>
		<option value=\"Female\">Female</option>
	</select>
</div>";
$sql = $db->prepare("SELECT * FROM `squads` WHERE Tenant = ? ORDER BY `squads`.`SquadFee` DESC;");
$sql->execute([
	$tenant->getId()
]);
$content .= "
<div class=\"form-group\">
	<label for=\"squad\">Squad</label>
		<select class=\"custom-select\" placeholder=\"Select a Squad\" id=\"squad\" name=\"squad\">";
//$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
	$content .= "<option value=\"" . $row['SquadID'] . "\"";
	$content .= ">" . htmlspecialchars($row['SquadName']) . "</option>";
}
$content .= "</select></div>";
$content .= "
<div class=\"form-group\">
	<div class=\"custom-control custom-checkbox\">
		<input type=\"checkbox\" class=\"custom-control-input\" id=\"clubpays\" name=\"clubpays\" value=\"1\" aria-describedby=\"cphelp\">
		<label class=\"custom-control-label\" for=\"clubpays\">Club Pays?</label>
	</div>
	<small id=\"cphelp\" class=\"form-text text-muted\">Tick the box if this swimmer will not pay any squad or membership fees, eg if they are at a university.</small>
</div>

<div class=\"form-group\">
	<div class=\"custom-control custom-checkbox\">
		<input type=\"checkbox\" class=\"custom-control-input\" id=\"transfer\" name=\"transfer\" value=\"1\" aria-describedby=\"transfer-help\">
		<label class=\"custom-control-label\" for=\"transfer\">Transferring from another club?</label>
	</div>
	<small id=\"transfer-help\" class=\"form-text text-muted\">Tick the box if this swimmer is transferring from another swimming club - They will not be charged for Swim England membership fees. If it is almost a new Swim England membership year and this swimmer will not be completing membership renewal then leave the box unticked so they pay Swim England membership fees when registering.</small>
</div>";
$content .= "<button type=\"submit\" class=\"btn btn-success\">Add Member</button>";
$content .= "</div><div class=\"col-md-4\">";
$content .= "</div></div>";

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/swimmersMenu.php"; ?>
<div class="container">
	<div class="">
	<?php echo "<h1>" . $title . "</h1>";
	echo $content; ?>
	</div>
</div>

<?=SCDS\CSRF::write()?>

<?php $footer = new \SCDS\Footer();
$footer->render();
