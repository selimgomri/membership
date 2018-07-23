<?

$id = mysqli_real_escape_string($link, $id);

$sql = "SELECT * FROM `members` INNER JOIN `squads` ON members.SquadID =
squads.SquadID WHERE `MemberID` = '$id';";
$result = mysqli_query($link, $sql);

if (mysqli_num_rows($result) == 0) {
	halt(404);
}

$row = mysqli_fetch_array($result, MYSQLI_ASSOC);

$pagetitle = $row['MForename'] . " " . $row['MSurname'];

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/swimmersMenu.php"; ?>
<style>
@media print {
	.bg-primary {
		color-adjust: exact;
		-webkit-print-color-adjust: exact;
		background: #bd0000 !important;
    color: #ffffff !important;
	}
	.text-white {
		color-adjust: exact;
		-webkit-print-color-adjust: exact;
		color: #ffffff !important;
	}
	html, body, .container {
		background: #ffffff !important;
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
			We successfully added <? echo $row['MForename'] . " " . $row['MSurname']; ?>.
		</p>
		<p>
			Please print out, email or copy the information below to give to the
			parent of this new swimmer. It contains the details they need to connect
			this swimmer to their account online.
		</p>
		<p>
			This message will not be shown on the print out.
		</p>
		<p>
			<a target="_self" class="btn btn-info" href="javascript:window.print()">
				<i class="fa fa-print" aria-hidden="true"></i> Print
			</a>
		</p>
		<p class="mb-0">
			<a href="<? echo autoUrl("swimmers"); ?>" class="btn btn-info">
				Return to Swimmers
			</a>
			<a href="<? echo autoUrl("swimmers/addmember"); ?>" class="btn btn-info">
				Add Another Swimmer
			</a>
		</p>
	</div>

	<div class="p-3 mb-3 text-right mono">
		<? echo $row['SquadName']; ?> Squad
	</div>

	<div class="mb-3 p-5 bg-primary text-white">
		<span class="h3 mb-0">Chester-le-Street ASC</span>
		<h1 class="h2 mb-4">Online Membership System</h1>
		<p class="mb-0">
			<strong>
				Your Access Key for <? echo $row['MForename'] . " " . $row['MSurname']; ?>
			</strong>
		</p>
	</div>

  <p>
    Here at Chester-le-Street ASC, we provide a number of online services to
    manage our members. Our services allow you to manage your swimmers, enter
    competitions, stay up to date by email, make payments by Direct Debit (from
    2019).
  </p>
	<p>
		Here’s what you will need to add <? echo $row['MForename'] . " " .
		$row['MSurname']; ?> to your account in our Online Membership System.
	</p>

	<ul>
		<li>
			<strong>
				ASA Number:
			</strong>
			 <span class="mono"><? echo $row['ASANumber']; ?></span>
		</li>
		<li>
			<strong>
				CLS ASC Access Key:
			</strong>
				<span class="mono"><? echo $row['AccessKey']; ?></span>
		</li>
	</ul>

	<p>
		If you haven’t already done so, you will need to create an account on our
		new system. This is easy to do - You only need to fill out one form.
	</p>

	<p>
		To add a swimmer, log into your account at
		https://account.chesterlestreetasc.co.uk/  and the select ‘My Account’ from
		the menu at the top.
	</p>

	<p>
		In My Account you will find an option at add a swimmer. Click this and enter
		their ASA Number and CLS ASC Access Key as above.
	</p>

	<div class="row mb-3">
    <div class="col-12">
      <div class="border border-dark p-2 bg-white">
				<div class="row d-flex align-items-center">
					<div class="col-8 pl-5">
						<h2>Already Registered for an Account?</h2>
						<p class="lead">
							Scan this QR Code to add <? echo $row['MForename']; ?> quickly.
						</p>
						<p class="mb-0">
							This QR Code cannot be used once you've added this swimmer.
							Contact our support team for a replacement.
						</p>
					</div>
					<div class="col-4 text-center">
		        <img class="img-fluid mx-auto d-block"
		        src="<? echo autoUrl("services/qr-generator?size=200&text=" . rawurlencode(autoUrl("myaccount/addswimmer/auto/" . $row['ASANumber'] . "/" . $row['AccessKey'])) . ""); ?>"
		        srcset="<? echo autoUrl("services/qr-generator?size=400&text=" . rawurlencode(autoUrl("myaccount/addswimmer/auto/" . $row['ASANumber'] . "/" . $row['AccessKey'])) . ""); ?> 2x, <? echo autoUrl("services/qr-generator?size=400&text=" . rawurlencode(autoUrl("myaccount/addswimmer/auto/" . $row['ASANumber'] . "/" . $row['AccessKey'])) . ""); ?> 3x"
		        alt="<? echo autoUrl("myaccount/addswimmer/auto/" . $row['ASANumber'] . "/" . $row['AccessKey']); ?>"></img>
					</div>
				</div>
      </div>
      <span class="d-block d-sm-none mb-3"></span>
    </div>
	</div>

  <?
  $col = "col-sm-6";
  if ($row['ThriveNumber'] != "") {
    $col = "col-sm-4";
  }
  ?>
  <div class="row mb-3 align-items-stretch">
    <div class="<? echo $col; ?>">
      <div class="text-center border border-dark h-100 p-2 bg-white">
        <span class="lead mb-2">ASA Number</span>
        <img class="img-fluid mx-auto d-block"
        src="<? echo autoUrl("services/barcode-generator?codetype=Code128&size=60&text=" . $row['ASANumber'] . "&print=false"); ?>"
        srcset="<? echo autoUrl("services/barcode-generator?codetype=Code128&size=120&text=" . $row['ASANumber'] . "&print=false"); ?> 2x, <? echo autoUrl("services/barcode-generator?codetype=Code128&size=180&text=" . $row['ASANumber'] . "&print=false"); ?> 3x"
        alt="<? echo $row['ASANumber']; ?>"></img>
        <span class="mono"><? echo $row['ASANumber']; ?></span>
      </div>
      <span class="d-block d-sm-none mb-3"></span>
    </div>
    <div class="<? echo $col; ?>">
      <div class="text-center border border-dark h-100 p-2 bg-white">
        <span class="lead mb-2">CLSASC Number</span>
        <img class="img-fluid mx-auto d-block"
        src="<? echo autoUrl("services/barcode-generator?codetype=Code128&size=60&text=CLSX" . $row['MemberID'] . "&print=false"); ?>"
        srcset="<? echo autoUrl("services/barcode-generator?codetype=Code128&size=120&text=CLSX" . $row['MemberID'] . "&print=false"); ?> 2x, <? echo autoUrl("services/barcode-generator?codetype=Code128&size=180&text=CLSX" . $row['MemberID'] . "&print=false"); ?> 3x"
        alt="CLSX<? echo $row['MemberID']; ?>"></img>
        <span class="mono">CLSX<? echo $row['MemberID']; ?></span>
      </div>
      <? if ($row['ThriveNumber'] != "") { ?><span class="d-block d-sm-none mb-3"></span><? } ?>
    </div>
    <? if ($row['ThriveNumber'] != "") { ?>
    <div class="<? echo $col; ?>">
      <div class="text-center border border-dark h-100 p-2 bg-white">
        <span class="lead mb-2">Thrive Card</span>
        <img class="img-fluid mx-auto d-block"
        src="<? echo autoUrl("services/barcode-generator?codetype=Code128&size=60&text=" . $row['ThriveNumber'] . "&print=false"); ?>"
        srcset="<? echo autoUrl("services/barcode-generator?codetype=Code128&size=120&text=" . $row['ThriveNumber'] . "&print=false"); ?> 2x, <? echo autoUrl("services/barcode-generator?codetype=Code128&size=180&text=" . $row['ThriveNumber'] . "&print=false"); ?> 3x"
        alt="<? echo $row['ThriveNumber']; ?>"></img>
        <span class="mono"><? echo $row['ThriveNumber']; ?></span>
      </div>
    </div>
    <? } ?>
  </div>

	<div class="small text-muted">
		<p>
			Access Keys are unique for each swimmer and ensure that the right people
			add a swimmer to their account. To increase data security, we will
			regenerate access keys when you add a swimmer or remove a swimmer from
			your account. If you remove a swimmer, or want to move them to a different
			account, ask a committee member for a new access key. The committee member
			may need to verify your identity.
		</p>

		<p>
			Don’t have an ASA Number? If so, and you need to be registered in our
			system as a member, we’ll give you a reference number starting with CLSX
			which you can use in place of an ASA Number in our systems only.
		</p>

		<p>
			If you’d like more information about how we use data, contact
			enquiries@chesterlestreetasc.co.uk.
		</p>
	</div>

</div>
<?php include BASE_PATH . "views/footer.php";
