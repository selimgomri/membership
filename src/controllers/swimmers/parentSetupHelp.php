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
		background: #bd0000;
	}
	html, body {
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
		new system.
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
