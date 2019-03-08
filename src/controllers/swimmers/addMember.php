<?php
$pagetitle = "Add a member";
$title = "Add a member";
$content = "<p class=\"lead\">Add a member to the club system.</p>";

$content .= "<div class=\"row\"><div class=\"col col-md-8\">";
if (isset($_SESSION['ErrorState'])) {
	$content .= $_SESSION['ErrorState'];
	unset($_SESSION['ErrorState']);
}
// Main Info Content
$content .= "<form method=\"post\">";
$content .= "
<div class=\"form-group\">
	<label for=\"forename\">Forename</label>
	<input type=\"text\" class=\"form-control\" id=\"forename\" name=\"forename\" placeholder=\"Enter a forename\" required>
</div>";
$content .= "
<div class=\"form-group\">
	<label for=\"middlenames\">Middle Names</label>
	<input type=\"text\" class=\"form-control\" id=\"middlenames\" name=\"middlenames\" placeholder=\"Enter a middlename\">
</div>";
$content .= "
<div class=\"form-group\">
	<label for=\"surname\">Surname</label>
	<input type=\"text\" class=\"form-control\" id=\"surname\" name=\"surname\" placeholder=\"Enter a surname\" required>
</div>";
$content .= "
<div class=\"form-group\">
	<label for=\"datebirth\">Date of Birth</label>
	<input type=\"date\" class=\"form-control\" id=\"datebirth\" name=\"datebirth\" pattern=\"[0-9]{4}-[0-9]{2}-[0-9]{2}\" placeholder=\"YYYY-MM-DD\" required>
</div>";
$content .= "
<div class=\"form-group\">
	<label for=\"asa\">ASA Registration Number</label>
	<input type=\"test\" class=\"form-control\" id=\"asa\" name=\"asa\"
	aria-describedby=\"asaHelp\" placeholder=\"ASA Registration Numer\">
	<small id=\"asaHelp\" class=\"form-text text-muted\">If a swimmer does not yet
	have an ASA Number, leave this blank and we'll generate a temporary internal
	membership number for this swimmer.</small>
</div>";
$content .= "
<div class=\"form-group\">
	<label for=\"squad\">ASA Membership Category</label>
	<select class=\"custom-select\" placeholder=\"Select a Category\" id=\"cat\" name=\"cat\">
		<option value=\"1\">Category 1</option>
		<option value=\"2\" selected>Category 2</option>
		<option value=\"3\">Category 3</option>
	</select>
</div>";
$content .= "
<div class=\"form-group\">
	<label for=\"sex\">Sex</label>
	<select class=\"custom-select\" id=\"sex\" name=\"sex\" placeholder=\"Select\">
		<option value=\"Male\">Male</option>
		<option value=\"Female\">Female</option>
	</select>
</div>";
$sql = "SELECT * FROM `squads` ORDER BY `squads`.`SquadFee` DESC;";
$result = mysqli_query($link, $sql);
$squadCount = mysqli_num_rows($result);
$content .= "
<div class=\"form-group\">
	<label for=\"squad\">Squad</label>
		<select class=\"custom-select\" placeholder=\"Select a Squad\" id=\"squad\" name=\"squad\">";
//$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
for ($i = 0; $i < $squadCount; $i++) {
	$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	$content .= "<option value=\"" . $row['SquadID'] . "\"";
	$content .= ">" . $row['SquadName'] . "</option>";
}
$content .= "</select></div>";
$content .= "
<div class=\"form-group\">
	<label for=\"clubpays\">Club Pays?</label>
	<select class=\"custom-select\" placeholder=\"Do we pay>\" id=\"clubpays\" name=\"clubpays\" aria-describedby=\"cphelp\">
		<option value=\"0\" selected>No</option>
		<option value=\"1\">Yes</option>
	</select>
	<small id=\"cphelp\" class=\"form-text text-muted\">If this swimmer will not
	pay any squad or membership fees, eg if they are at a university, select
	Yes. They will still pay gala fees.</small>
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
<?php include BASE_PATH . "views/footer.php";
