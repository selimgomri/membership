<?

global $db;

$sql = "SELECT * FROM ((members INNER JOIN familyMembers ON
familyMembers.MemberID = members.MemberID) INNER JOIN familyIdentifiers ON
familyIdentifiers.ID = familyMembers.FamilyID) WHERE FamilyID = ?";

try {
	$query = $db->prepare($sql);
	$query->execute([$id]);
} catch (PDOException $e) {
	halt(500);
}

$row = $query->fetchAll(PDO::FETCH_ASSOC);

if (!$row) {
	halt(404);
}

$pagetitle = "Parent Registration Form";

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/swimmersMenu.php"; ?>
<style>
@media print {
	.bg-primary {
		color-adjust: exact;
		-webkit-print-color-adjust: exact;
		background: #bd0000 !important;
    color: #fffffe !important;
	}
	.text-white {
		color-adjust: exact;
		-webkit-print-color-adjust: exact;
		color: #fffffe !important;
	}
	html, body {
		background: #ffffff !important;
		font-size: 12pt;
		padding: 0px !important;
		margin-top: 0px !important;
	}
}
</style>
<div class="container">

	<div class="alert alert-info d-print-none">
		<p class="mb-0">
			<strong>
				Notice to Staff
			</strong>
		</p>
		<p>
			Here's the details needed to continue registration.
		</p>
		<p>
			Please print out, email or copy the information below to give to the
			parent. It contains the details they need to complete the registration
			process online. They must complete registration in order to pay fees.
		</p>
		<p>
			This message will not be shown on the print out.
		</p>
		<p class="mb-0">
			<a target="_self" class="btn btn-info" href="javascript:window.print()">
				<i class="fa fa-print" aria-hidden="true"></i> Print
			</a>
		</p>
	</div>

	<div class="py-3 mb-3 text-right mono">
		Family Registration: <?php echo $row[0]['ID']; ?>
	</div>

	<div class="mb-3 p-5 bg-primary text-white">
		<span class="h3 mb-0"><?=htmlspecialchars(env('CLUB_NAME'))?></span>
		<h1 class="h2 mb-4">Online Membership System</h1>
		<p class="mb-0">
			<strong>
				Family Registration Sheet
			</strong>
		</p>
	</div>

  <p>
    Here at <?=htmlspecialchars(env('CLUB_NAME'))?>, we provide a number of online services to
    manage our members. Our services allow you to manage your swimmers, enter
    competitions, stay up to date by email and make payments by Direct Debit
    (from 2019).
  </p>

	<p>
		Follow the instructions below to sign up for your <?=htmlspecialchars(env('CLUB_NAME'))?>
		Account.
	</p>

	<p>
		Here’s what you will need to do to set up your account. This account will
		include <?php echo $row[0]['MForename'] . " " . $row[0]['MSurname']; for ($i =
		1; $i < sizeof($row); $i++) { echo ", " . $row[$i]['MForename'] . " " .
		$row[$i]['MSurname']; } ?>. There are two methods you can use to get
		started.
	</p>

	<h2>Register via QR Code</h2>

	<p>
		If you have a device that can read QR Codes (A built in feature on iOS and
		Android Devices), scan the QR Code below. You'll be taken to a page where
		you'll be asked to register for an account.
	</p>

	<p>
		If you use this method, we will automatically retrieve your family of
		swimmers from our database.
	</p>

  <div class="border border-dark p-2 bg-white mb-3 px-5">
		<div class="row d-flex align-items-center">
			<div class="col-8">
				<span class="h2">Register for your account</span>
				<p class="lead mb-0">
					Scan this QR Code to get going quickly.
				</p>
			</div>
			<div class="col-4 text-center">
        <img class="img-fluid ml-auto d-block" src="<?php echo
        autoUrl("services/qr-generator?size=200&text=" .
        rawurlencode(autoUrl("register/family/" . $row[0]['FamilyID'] . "/" .
        $row[0]['UID']))); ?>" srcset="<?php echo
        autoUrl("services/qr-generator?size=400&text=" .
        rawurlencode(autoUrl("register/family/" . $row[0]['FamilyID'] . "/" .
        $row[0]['UID']))); ?> 2x, <?php echo
        autoUrl("services/qr-generator?size=400&text=" .
        rawurlencode(autoUrl("register/family/" . $row[0]['FamilyID'] . "/" .
        $row[0]['UID']))); ?> 3x" alt="<?php echo autoUrl("register/family/" .
        $row[0]['FamilyID'] . "/" . $row[0]['UID']); ?>"></img>
			</div>
		</div>
  </div>

	<h2>Register and Find Family Manually</h2>

	<p>
		If you do not have a device that can read QR Codes, head to
		https://account.chesterlestreetasc.co.uk/register and then select 'Family
		Registration'
	</p>

	<p>
		You'll be directed to a page and asked to enter your Family Registration
		Number and Security Key from below.
	</p>

	<div class="mb-3">
		<table class="table table-sm table-borderless d-inline mb-0">
		  <tbody>
		    <tr>
		      <th scope="row" class="pl-0">Family Registration Number</th>
		      <td class="pr-0"><span class="mono">FAM<?php echo $row[0]['FamilyID']; ?></span></td>
		    </tr>
		    <tr>
		      <th scope="row" class="pl-0">Security Key</th>
		      <td class="pr-0"><span class="mono"><?php echo $row[0]['ACS']; ?></span></td>
		    </tr>
		  </tbody>
		</table>
	</div>

	<div class="small text-muted">
		<p>
			You must complete registration before <?php echo date("j F Y",
			strtotime("first day of next month")); ?>. Failure to do so may mean that
			your swimmer(s) will be suspended.
		</p>

		<p>
			<?=htmlspecialchars(env('CLUB_NAME'))?> cannot be responsible for errors in data entered by
			parents.
		</p>

		<p>
			If you’d like more information about how we use data, contact
			enquiries@chesterlestreetasc.co.uk.
		</p>

    <p>
      The user account service is provided to <?=htmlspecialchars(env('CLUB_NAME'))?> by
      Chester-le-Street ASC Club Digital Services.
    </p>
	</div>

</div>
<?php $footer = new \SCDS\Footer();
$footer->render();
