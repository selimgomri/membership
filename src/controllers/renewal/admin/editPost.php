<?

$id = mysqli_real_escape_string($link, $id);

use Respect\Validation\Validator as v;

$ok = true;
$response = "";

$name = mysqli_real_escape_string($link, trim($_POST['name']));
$start = mysqli_real_escape_string($link, trim($_POST['start']));
$end = mysqli_real_escape_string($link, trim($_POST['end']));

if ($name == null || $name == "") {
	$ok = false;
	$response .= '<li>You did not supply a name</li>';
}

if ($start == null || $start == "") {
	$ok = false;
	$response .= '<li>You did not supply a start date</li>';
}

if ($end == null || $end == "") {
	$ok = false;
	$response .= '<li>You did not supply an end date</li>';
}

if (!v::date()->validate($start)) {
	$ok = false;
	$response .= '<li>The start date was incorrectly formatted</li>';
} else {
	$start = date("Y-m-d", strtotime($start));
}

if (!v::date()->validate($end)) {
	$ok = false;
	$response .= '<li>The end date was incorrectly formatted</li>';
} else {
	$end = date("Y-m-d", strtotime($end));
}

if ($ok) {
	$sql = "UPDATE `renewals` SET `Name` = '$name', `StartDate` = '$start', `EndDate` = '$end' WHERE `ID` = '$id';";
	mysqli_query($link, $sql);

	$_SESSION['NewRenewalErrorInfo'] = '
	<div class="alert alert-success">
		<p class="mb-0">
			<strong>
				We\'ve successfully updated this renewal period.
			</strong>
		</p>
	</div>';

	header("Location: " . app('request')->curl);
} else {
	$_SESSION['NewRenewalErrorInfo'] = '
	<div class="alert alert-danger">
		<p class="mb-0">
			<strong>
				An error occurred as there was a problem with the information you
				supplied. We did not update the renewal period.
			</strong>
		</p>
		<ul class="mb-0">
			' . $response . '
		</ul>
	</div>';
	header("Location: " . app('request')->curl);
}
