<?php

global $db;

$sql = $db->prepare("SELECT * FROM `members` INNER JOIN `squads` ON members.SquadID = squads.SquadID WHERE `MemberID` = ?");
$sql->execute([$id]);

$row = $sql->fetch(PDO::FETCH_ASSOC);

if ($row == null) {
	halt(404);
}
$swimEnglandText = "Swim England Number";
if (mb_stripos($row['ASANumber'], env('ASA_CLUB_CODE')) > -1) {
	$swimEnglandText = "Temporary Membership Number";
}

$pagetitle = htmlspecialchars($row['MForename'] . " " . $row['MSurname']) . " Registration Information";

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/swimmersMenu.php";

?>
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

  <?php if (isset($_SESSION['EmailStatus']) && $_SESSION['EmailStatus']) { ?>
    <div class="alert alert-success d-print-none">
      <p class="mb-0">We've sent an email to that address.</p>
    </div>
  <?php } else if (isset($_SESSION['EmailStatus']) && !$_SESSION['EmailStatus']) { ?>
    <div class="alert alert-danger d-print-none">
      <p class="mb-0">We were unable to send an email to that address.</p>
    </div>
  <?php }
  if (isset($_SESSION['EmailStatus'])) {
    unset($_SESSION['EmailStatus']);
  } ?>

	<div class="alert alert-info d-print-none">
		<p class="mb-0">
			<strong>
				Notice to Staff
			</strong>
		</p>
		<p>
			We successfully added <?=htmlspecialchars($row['MForename'] . " " . $row['MSurname'])?>.
		</p>
		<p>
			Please print out, email or copy the information below to give to the
			parent of this new swimmer. It contains the details they need to connect
			this swimmer to their account online.
		</p>
		<p>
			This message will not be shown on the print out.
		</p>
    <form method="post">
      <div class="form-group">
        <label for="emailAddr">Send to email address</label>
        <input type="email" class="form-control" id="emailAddr" name="emailAddr" aria-describedby="emailAddrHelp" placeholder="Enter email">
        <small id="emailAddrHelp" class="form-text text-muted">Sends a one-off email.</small>
      </div>
      <p>
        <button type="submit" class="btn btn-info">Send as Email</button>
        <a target="_self" class="btn btn-info" href="javascript:window.print()">
				<i class="fa fa-print" aria-hidden="true"></i> Print
			</a></p>
    </form>
		<p class="mb-0">
			<a href="<?=autoUrl("swimmers")?>" class="btn btn-info">
				Return to Swimmers
			</a>
			<?php if ($_SESSION['AccessLevel'] != "Coach" && $_SESSION['AccessLevel'] != "Galas") { ?>
			<a href="<?=autoUrl("swimmers/addmember")?>" class="btn btn-info">
				Add Another Swimmer
			</a>
			<?php } ?>
		</p>
	</div>

	<div class="py-3 mb-3 text-right mono">
		<?=htmlspecialchars($row['SquadName'])?> Squad
	</div>

	<div class="mb-3 p-5 bg-primary text-white">
		<span class="h3 mb-0"><?=htmlspecialchars(env('CLUB_NAME'))?></span>
		<h1 class="h2 mb-4">Online Membership System</h1>
		<p class="mb-0">
			<strong>
				Your Access Key for <?=htmlspecialchars($row['MForename'] . " " . $row['MSurname'])?>
			</strong>
		</p>
	</div>

  <p>
    Here at <?=htmlspecialchars(env('CLUB_NAME'))?>, we provide a number of online services to
    manage our members. Our services allow you to manage your swimmers, enter
    competitions, stay up to date by email and make payments by Direct Debit.
  </p>

	<?php if (!(defined('IS_CLS') && IS_CLS)) { ?>
	<p>
		<strong>Please note:</strong> Some services may not be provided by your club.
	</p>
	<?php } ?>

	<p>
		If you haven’t already done so, you will need to create an account on our
		membership system. This is easy to do - You only need to fill out one form
		and then verify your email address.
	</p>

	<p>
		Here’s what you will need to do to add <?=htmlspecialchars($row['MForename'] . " " . $row['MSurname'])?> to your account in our Online Membership System. There
		are two methods you can use to do this.
	</p>

	<h2>Add via QR Code</h2>

	<p>
		If you have a device that can read QR Codes (A built in feature on iOS and
		Android Devices), scan the QR Code below. You'll be taken to a page where
		you'll be asked to log in, if you aren't already, and we'll automatically
		add <?=htmlspecialchars($row['MForename'])?> to your account.
	</p>

  <div class="border border-dark p-2 bg-white mb-3 px-5">
		<div class="row d-flex align-items-center">
			<div class="col-8">
				<span class="h2">Already registered for an account?</span>
				<p class="lead">
					Scan this QR Code to add <?=htmlspecialchars($row['MForename'])?> quickly.
				</p>
				<p class="mb-0">
					This QR Code cannot be used once you've added this swimmer.
					Contact our support team for a replacement.
				</p>
			</div>
			<div class="col-4 text-center">
        <img class="img-fluid ml-auto d-block" src="<?php echo
        autoUrl("services/qr-generator?size=200&text=" .
        rawurlencode(autoUrl("my-account/addswimmer/auto/" . $row['ASANumber'] . "/" .
        $row['AccessKey'])) . ""); ?>" srcset="<?php echo
        autoUrl("services/qr-generator?size=400&text=" .
        rawurlencode(autoUrl("my-account/addswimmer/auto/" . $row['ASANumber'] . "/" .
        $row['AccessKey'])) . ""); ?> 2x, <?php echo
        autoUrl("services/qr-generator?size=400&text=" .
        rawurlencode(autoUrl("my-account/addswimmer/auto/" . $row['ASANumber'] . "/" .
        $row['AccessKey'])) . ""); ?> 3x" alt="<?php echo
        autoUrl("my-account/addswimmer/auto/" . $row['ASANumber'] . "/" .
        $row['AccessKey']); ?>"></img>
			</div>
		</div>
  </div>

	<h2>Add Manually</h2>

	<p>
		If you do not have a device that can read QR Codes, to add a swimmer, log
		into your account at <?=autoUrl("")?>  and the
		select 'My Account' then 'Add Swimmers' from the menu at the top.
	</p>

	<p>
		You'll be directed to a page and asked to enter your swimmer's <?=$swimEnglandText?> and <?=htmlspecialchars(env('CLUB_SHORT_NAME'))?> Access Key as below.
	</p>

	<div class="mb-3">
		<table class="table table-sm table-borderless d-inline mb-0">
		  <tbody>
		    <tr>
		      <th scope="row" class="pl-0"><?=$swimEnglandText?></th>
		      <td class="pr-0"><span class="mono"><?=htmlspecialchars($row['ASANumber'])?></span></td>
		    </tr>
		    <tr>
		      <th scope="row" class="pl-0"><?=htmlspecialchars(env('CLUB_SHORT_NAME'))?> Access Key</th>
		      <td class="pr-0"><span class="mono"><?=htmlspecialchars($row['AccessKey'])?></span></td>
		    </tr>
		  </tbody>
		</table>
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

		<?php if (mb_stripos($row['ASANumber'], env('ASA_CLUB_CODE')) > -1) { ?>
		<p>
			At the time you were given this form we did not yet have a Swim England registration number for <?=htmlspecialchars($row['MForename'])?>. We have given you a temporary number which starts with <span class="mono"><?=htmlspecialchars(env('ASA_CLUB_CODE'))?></span> which you can use to add your swimmer to your account.
		</p>
		<?php } ?>

		<p>
			If you’d like more information about how we use data, contact
			<?=CLUB_EMAIL?>.
		</p>

		<?php if (!(defined('IS_CLS') && IS_CLS)) { ?>
    <p>
      The user account service is provided to <?=htmlspecialchars(env('CLUB_NAME'))?> by
      Chester-le-Street ASC Club Digital Services.
    </p>
		<?php } ?>
	</div>

  <?php
  $col = "col-sm-6";
  if ($row['ThriveNumber'] != "") {
    $col = "col-sm-4";
  }
  ?>
  <div class="row mb-3 align-items-stretch">
    <div class="<?php echo $col; ?>">
      <div class="text-center border border-dark h-100 p-2 bg-white">
        <span class="mb-2">Swim England Number</span>
        <img class="img-fluid mx-auto d-block"
        src="<?php echo autoUrl("services/barcode-generator?codetype=Code128&size=60&text=" . $row['ASANumber'] . "&print=false"); ?>"
        srcset="<?php echo autoUrl("services/barcode-generator?codetype=Code128&size=120&text=" . $row['ASANumber'] . "&print=false"); ?> 2x, <?php echo autoUrl("services/barcode-generator?codetype=Code128&size=180&text=" . $row['ASANumber'] . "&print=false"); ?> 3x"
        alt="<?php echo $row['ASANumber']; ?>"></img>
        <span class="mono"><?php echo $row['ASANumber']; ?></span>
      </div>
      <span class="d-block d-sm-none mb-3"></span>
    </div>
    <div class="<?php echo $col; ?>">
      <div class="text-center border border-dark h-100 p-2 bg-white">
        <span class="mb-2"><?=htmlspecialchars(env('CLUB_SHORT_NAME'))?> Number</span>
        <img class="img-fluid mx-auto d-block"
        src="<?=autoUrl("services/barcode-generator?codetype=Code128&size=60&text=" . htmlspecialchars(env('ASA_CLUB_CODE') . $row['MemberID']) . "&print=false")?>"
        srcset="<?=autoUrl("services/barcode-generator?codetype=Code128&size=120&text=" . htmlspecialchars(env('ASA_CLUB_CODE') . $row['MemberID']) . "&print=false")?> 2x, <?=autoUrl("services/barcode-generator?codetype=Code128&size=180&text=" . htmlspecialchars(env('ASA_CLUB_CODE') . $row['MemberID']) . "&print=false")?> 3x"
        alt="<?=htmlspecialchars(env('ASA_CLUB_CODE'))?>X<?php echo $row['MemberID']; ?>"></img>
        <span class="mono"><?=htmlspecialchars(env('ASA_CLUB_CODE') . $row['MemberID'])?></span>
      </div>
      <?php if ($row['ThriveNumber'] != "") { ?><span class="d-block d-sm-none mb-3"></span><?php } ?>
    </div>
    <?php if ($row['ThriveNumber'] != "") { ?>
    <div class="<?php echo $col; ?>">
      <div class="text-center border border-dark h-100 p-2 bg-white">
        <span class="mb-2">Thrive Card</span>
        <img class="img-fluid mx-auto d-block"
        src="<?php echo autoUrl("services/barcode-generator?codetype=Code128&size=60&text=" . $row['ThriveNumber'] . "&print=false"); ?>"
        srcset="<?php echo autoUrl("services/barcode-generator?codetype=Code128&size=120&text=" . $row['ThriveNumber'] . "&print=false"); ?> 2x, <?php echo autoUrl("services/barcode-generator?codetype=Code128&size=180&text=" . $row['ThriveNumber'] . "&print=false"); ?> 3x"
        alt="<?php echo $row['ThriveNumber']; ?>"></img>
        <span class="mono"><?php echo $row['ThriveNumber']; ?></span>
      </div>
    </div>
    <?php } ?>
  </div>

	<div class="small text-muted">
		<p>
			The above barcodes are provided because Swim England Membership Cards are no longer issued.
		</p>
	</div>

</div>
<?php include BASE_PATH . "views/footer.php";
