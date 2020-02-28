<?php

$userID = mysqli_real_escape_string($link, $_SESSION['UserID']);
$renewal = mysqli_real_escape_string($link, $renewal);
$sql = "UPDATE `renewalProgress` SET `Stage` = `Stage` + 1 WHERE
`RenewalID` = '$renewal' AND `UserID` = '$userID';";
mysqli_query($link, $sql);

$query = "SELECT * FROM users WHERE UserID = '$userID' ";
$result = mysqli_query($link, $query);
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
$email = $row['EmailAddress'];
$forename = $row['Forename'];
$surname = $row['Surname'];
$access = $row['AccessLevel'];
$userID = $row['UserID'];
$mobile = $row['Mobile'];
$emailComms = $row['EmailComms'];
$mobileComms = $row['MobileComms'];
if ($emailComms==1) {
	$emailChecked = " checked ";
}
if ($mobileComms==1) {
	$mobileChecked = " checked ";
}

$pagetitle = "Account Review";
include BASE_PATH . "views/header.php";
include BASE_PATH . "views/renewalTitleBar.php";
?>

<div class="container">
	<h1>Your Membership Renewal</h1>
	<p class="lead"><?php echo date("Y-m-d"); ?></p>

	<?php if (isset($_SESSION['ErrorState'])) {
		echo $_SESSION['ErrorState'];
		unset($_SESSION['ErrorState']);
	} ?>

  <h2>Your Details</h2>
  <dl>
		<dt>Name</dt>
    <dd><?php echo $forename ?> <?php echo $surname ?></dd>

		<dt>Email</dt>
    <dd><?php echo $email ?></dd>

		<dt>Allow CLS ASC to send emails?</dt>
    <dd><input type="checkbox" <?php echo $emailChecked ?>></dd>

		<dt>Mobile Phone Number</dt>
    <dd><?php echo $mobile ?></dd>

		<dt>Allow CLS ASC to send you SMS Messages?</dt>
    <dd><input type="checkbox" <?php echo $mobileChecked ?>></dd>
	</dl>

	<hr>

	<h2>Your members</h2>
	<?php echo mySwimmersTable($link, $userID); ?>

	<hr>

	<h2>Your Monthly Fees</h2>
	<?php echo myMonthlyFeeTable($link, $userID); ?>

	<hr>

	<h2>Swimmer Medical Information</h2>
	<?

	$sql = "SELECT * FROM `members` LEFT JOIN `memberMedical` ON members.MemberID =
	memberMedical.MemberID WHERE `UserID` = '$userID';";
	$result = mysqli_query($link, $sql);

	for ($i = 0; $i < mysqli_num_rows($result); $i++) {
		$row[$i] = mysqli_fetch_array($result, MYSQLI_ASSOC);
		?>
		<h3><?php echo $row[$i]['MForename'] . " " . $row[$i]['MSurname']; ?></h3>

		<dl>
			<dt>Medical Conditions and/or Disabilities</dt>
			<?php if ($row[$i]['Conditions'] != "") { ?>
	    <dd><?php echo $row[$i]['Conditions'] ?></dd>
			<?php } else { ?>
			<dd>None</dd>
			<?php } ?>

			<dt>Allergies</dt>
			<?php if ($row[$i]['Allergies'] != "") { ?>
	    <dd><?php echo $row[$i]['Allergies'] ?></dd>
			<?php } else { ?>
			<dd>None</dd>
			<?php } ?>

			<dt>Medication</dt>
			<?php if ($row[$i]['Medication'] != "") { ?>
	    <dd><?php echo $row[$i]['Medication'] ?></dd>
			<?php } else { ?>
			<dd>None</dd>
			<?php } ?>
		</dl>

	<?php } ?>

	<hr>

	<h2>Emergency Contacts</h2>
	<p>Will go here</p>

	<hr>

	<h2>Code of Conduct Agreements</h2>

	<h3><?php echo $forename . " " . $surname; ?> (Parent)</h3>

	<ul>
		<li>
			Parents are not allowed on poolside at any time during training
			sessions and should not engage the coach in conversation from the
			spectator area whilst they are teaching.
		</li>
		<li>
			All parents of children under the age of 11 must remain within the
			building at all times when your child attends for their session.
		</li>
		<li>
			Encourage your child to learn the rules and ensure that they abide by
			them.
		</li>
		<li>
			Discourage unfair practices and arguing with officials.
		</li>
		<li>
			Help your child to recognise good performance, not just results.
		</li>
		<li>
			Never force your child to take part in sport and do not offer
			incentives for participation, however positive support is encouraged.
		</li>
		<li>
			Set a good example by recognising fair play and applauding the good
			performances of all.
		</li>
		<li>
			Never punish or belittle a child for losing or making mistakes but
			provide positive feedback to encourage future participation.
		</li>
		<li>
			Publicly accept officials’ judgments.
		</li>
		<li>
			Support your child’s involvement and help them to enjoy their sport.
		</li>
		<li>
			Use correct and proper language at all times.
		</li>
	</ul>

	<div class="mono">
		<p>
			I, <?php echo $forename . " " . $surname; ?> agree to the above Code of
			Conduct
		</p>
		<p>
			Signature&nbsp;______________________________
		</p>
		<p>
			Date&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;______________________________
		</p>
	</div>
	<div class="alert alert-info">
		<p class="mb-0">
			You agreed to the Code of Conduct earlier in the renewal process. You only
			need to sign if you wish to reinforce the meaning of the agreement with
			your children, or if you have been asked to provide a signed copy of your
			membership renewal by the Membership Secretary.
		</p>
	</div>

	<?
	for ($i = 0; $i < mysqli_num_rows($result); $i++) {
		?>
		<h3><?php echo $row[$i]['MForename'] . " " . $row[$i]['MSurname']; ?> (Swimmer)</h3>

		<ul>
			<li>
				Parents are not allowed on poolside at any time during training
				sessions and should not engage the coach in conversation from the
				spectator area whilst they are teaching.
			</li>
			<li>
				All parents of children under the age of 11 must remain within the
				building at all times when your child attends for their session.
			</li>
			<li>
				Encourage your child to learn the rules and ensure that they abide by
				them.
			</li>
			<li>
				Discourage unfair practices and arguing with officials.
			</li>
			<li>
				Help your child to recognise good performance, not just results.
			</li>
			<li>
				Never force your child to take part in sport and do not offer
				incentives for participation, however positive support is encouraged.
			</li>
			<li>
				Set a good example by recognising fair play and applauding the good
				performances of all.
			</li>
			<li>
				Never punish or belittle a child for losing or making mistakes but
				provide positive feedback to encourage future participation.
			</li>
			<li>
				Publicly accept officials’ judgments.
			</li>
			<li>
				Support your child’s involvement and help them to enjoy their sport.
			</li>
			<li>
				Use correct and proper language at all times.
			</li>
		</ul>

		<div class="mono">
			<p>
				I, <?php echo $row[$i]['MForename'] . " " . $row[$i]['MSurname']; ?> agree
				to the above Code of Conduct
			</p>
			<p>
				Signature&nbsp;______________________________
			</p>
			<p>
				Date&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;______________________________
			</p>
		</div>

	<?php } ?>

	<hr>
	<?php

	$name = getUserName($userID);

	$sql = "SELECT members.MemberID, members.MForename, members.MSurname,
	members.DateOfBirth, memberPhotography.Website, memberPhotography.Social,
	memberPhotography.Noticeboard, memberPhotography.FilmTraining,
	memberPhotography.ProPhoto FROM (`members` LEFT JOIN `memberPhotography` ON
	members.MemberID = memberPhotography.MemberID) WHERE `UserID` = '$userID'
	ORDER BY `MForename` ASC, `MSurname` ASC;";
	$result = mysqli_query($link, $sql);
	?>

	<h2>Data Protection</h2>
	<p>
		I understand that, in compliance with the General Data Protection
		Regulation, all efforts will be made to ensure that information is accurate,
		kept up to date and secure, and that it is used only in connection with the
		purposes of Chester-le-Street ASC. Information will be disclosed only to
		those members of the club for whom it is appropriate, and relevant officers
		of the Amateur Swimming Association (Swim England) or British Swimming.
		Information will not be kept once a person has left the club.
	</p>

	<div class="form-group">
		<input type="checkbox" checked>
		<label>
			I (<?php echo $name; ?>) agree to the use of my data by Chester-le-Street
			ASC as outlined above
		</label>
	</div>

	<hr>

	<h2>Terms and Conditions of the Club</h2>
	<p>
		The Member, and the Parent or Guardian, in the case of a person under the
		age of 18 years, hereby acknowledges that they have read the Club Rules and
		the Policies and Procedures Documentation of Chester-le-Street ASC, copies
		of which can be obtained from https://www.chesterlestreetasc.co.uk/policies.
		I confirm my understanding and acceptance that such rules (as amended from
		time to time) shall govern my membership of the club. I further acknowledge
		and accept the responsibilities of membership as set out in these rules and
		understand that it is my duty to read and abide by them (including any
		amendments). By providing my agreement, I consent to be bound by the Code of
		Conduct, Constitution, Rules and Policy Documents of the club.
	</p>

	<?php for ($i = 0; $i < mysqli_num_rows($result); $i++) {
	$row[$i] = mysqli_fetch_array($result, MYSQLI_ASSOC);
	$id[$i] = $row[$i]['MemberID'];
	$age[$i] = date_diff(date_create($row[$i]['DateOfBirth']),
	date_create('today'))->y; ?>

	<h3><?php echo $row[$i]['MForename'] . " " . $row[$i]['MSurname']; ?></h3>

	<div class="mono">
		<p>
			I, <?php echo $row[$i]['MForename'] . " " . $row[$i]['MSurname']; ?>
			agree to the Terms and Conditions of Chester-le-Street ASC as outlined
			above
		</p>
		<p>
			Signature&nbsp;______________________________
		</p>
		<p>
			Date&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;______________________________
		</p>
	</div>

	<?
	if ($age[$i] < 12) { ?>

	<p>
		In the case of a member under the age of twelve years the Parent or
		Guardian undertakes to explain the content and implications of the Terms
		and Conditions of Membership of Chester-le-Street ASC.
	</p>

	<div class="mono">
		<p>
			I, <?php echo $name; ?> have explained the content and
			implications to <?php echo $row[$i]['MForename'] . " " . $row[$i]['MSurname']; ?>
			and can confirm that they understood.
		</p>
		<p>
			Signature&nbsp;______________________________
		</p>
		<p>
			Date&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;______________________________
		</p>
	</div>

	<?php } ?>

	<?php } ?>

	<hr>

	<h2>Photography Consent</h2>
	<p>
		Please read the ASA/Chester-le-Street ASC Photography Policy before you
		continue to give or withold consent for photography.
	</p>

	<p>
		Chester-le-Street ASC may wish to take photographs of individuals and groups
		of swimmers under the age of 18, which may include your child during their
		membership of Chester-le-Street ASC. Photographs will only be taken and
		published in accordance with Swim England policy which requires the club to
		obtain the consent of the Parent or Guardian to take and use photographs
		under the following circumstances.
	</p>

	<p>
		It is entirely up to you whether or not you choose to allow us to take
		photographs and/or video of your child. You can change your choices at any
		time by heading to Swimmers.
	</p>

	<?php for ($i = 0; $i < mysqli_num_rows($result); $i++) {
		if ($age[$i] < 18) {
			$photo = [];
      if ($row[$i]['Website'] == 1) {
        $photo[0] = " checked ";
      }
      if ($row[$i]['Social'] == 1) {
        $photo[1] = " checked ";
      }
      if ($row[$i]['Noticeboard'] == 1) {
        $photo[2] = " checked ";
      }
      if ($row[$i]['FilmTraining'] == 1) {
        $photo[3] = " checked ";
      }
      if ($row[$i]['ProPhoto'] == 1) {
        $photo[4] = " checked ";
      } ?>
		<h3><?php echo $row[$i]['MForename'] . " " . $row[$i]['MSurname']; ?></h3>
		<p>
			I, <?php echo $name; ?> agree to photography in the following
circumstances. (Ticked boxes confirm photography permission.)
		</p>
		<div class="form-group">
			<input type="checkbox" <?php echo $photo[0]; ?>>
			<label>
				Take photographs to use on the clubs website
			</label>
		</div>
		<div class="form-group">
			<input type="checkbox" <?php echo $photo[1]; ?>>
			<label>
				Take photographs to use on social media sites
			</label>
		</div>
		<div class="form-group">
			<input type="checkbox" <?php echo $photo[2]; ?>>
			<label>
				Take photographs to use on club noticeboards
			</label>
		</div>
		<div class="form-group">
			<input type="checkbox" <?php echo $photo[3]; ?>>
			<label>
				Filming for training purposes only
			</label>
		</div>
		<div class="form-group">
			<input type="checkbox" <?php echo $photo[4]; ?>>
			<label>
				Employ a professional photographer (approved by the club) who will take
				photographs in competitions and/or club events.
			</label>
		</div>
	<?php } } ?>

	<h2>Medical Consent</h2>
	<p>
		For Parents and Guardians of members under 18 years. Swimmers aged 18 or
		over are individually responsible for ensuring they only swim if not told by
		a Doctor to refrain from physical activity.
	</p>

	<?php for ($i = 0; $i < mysqli_num_rows($result); $i++) {
		if ($age[$i] < 18) { ?>

	<h3>
		Consent for <?php echo $row[$i]['MForename'] . " " . $row[$i]['MSurname']; ?>
	</h3>
	<p>
		I confirm that <?php echo $row[$i]['MForename'] . " " .
		$row[$i]['MSurname']; ?> has not been advised by a doctor to not take
		part in physical activities unless under medical supervision.
	</p>

	<p>
		I, <?php echo $name; ?> hereby give permission for the coach or
		other appropriate person to give the authority on my behalf for any medical
		or surgical treatment recommended by competent medical authorities, where it
		would be contrary to my child's interest, in the doctor's opinion, for any
		delay to be incurred by seeking my personal consent.
	</p>

	<div class="mono">
		<p>
			Signature&nbsp;______________________________
		</p>
		<p>
			Date&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;______________________________
		</p>
	</div>

	<?php }
	} ?>

	<div class="mb-3 d-print-none">
		<a class="btn btn-outline-success" href="<?php echo currentUrl(); ?>">
			Finish Renewal
		</a>
	</div>
</div>

<?php $footer = new \SCDS\Footer();
$footer->render();
