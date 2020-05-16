<?php

$db = app()->db;
$tenant = app()->tenant;

use Respect\Validation\Validator as v;

$ok = true;
$response = "";

$name = trim($_POST['name']);
$start = trim($_POST['start']);
$end = trim($_POST['end']);
$year = (int) date("Y", strtotime("+1 year"));

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
	$start = date("Y-m-d", strtotime($end));
}

if ($ok) {
	try {
		$insert = $db->prepare("INSERT INTO `renewals` (`Name`, `StartDate`, `EndDate`, `Year`, Tenant) VALUES (?, ?, ?, ?, ?);");
		$insert->execute([
			$name,
			$start,
			$end,
			$year,
			$tenant->getId()
		]);
		header("Location: " . autoUrl("renewal"));
	} catch (Exception $e) {
		$_SESSION['TENANT-' . app()->tenant->getId()]['NewRenewalErrorInfo'] = '
		<div class="alert alert-danger">
			<p class="mb-0">
				<strong>
					A database error occurred.
				</strong>
			</p>
			<ul class="mb-0">
				' . $response . '
			</ul>
		</div>';
		$_SESSION['TENANT-' . app()->tenant->getId()]['NewRenewalForm'] = [$name, $start, $end];
		header("Location: " . autoUrl("renewal/new"));
	}
} else {
	$_SESSION['TENANT-' . app()->tenant->getId()]['NewRenewalErrorInfo'] = '
	<div class="alert alert-danger">
		<p class="mb-0">
			<strong>
				An error occurred as there was a problem with the information you supplied.
			</strong>
		</p>
		<ul class="mb-0">
			' . $response . '
		</ul>
	</div>';
	$_SESSION['TENANT-' . app()->tenant->getId()]['NewRenewalForm'] = [$name, $start, $end];
	header("Location: " . autoUrl("renewal/new"));
}
