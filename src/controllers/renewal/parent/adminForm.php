<?php

$userID = mysqli_real_escape_string($link, $_SESSION['UserID']);

$row = [];

$mySwimmers = mySwimmersTable($link, $userID);

$sql = "SELECT * FROM `members` WHERE `UserID` = '$userID';";
$result = mysqli_query($link, $sql);

$pagetitle = "Administration Form";
include BASE_PATH . "views/header.php";
?>

<div class="container">
	<h1>Club Administration Form</h1>
	<p class="lead">
		In this next step you, and your swimmers will need to agree to the terms and
		conditions of the club.
	</p>

	<? echo $mySwimmers; ?>

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
		<label for="gdpr-confirm">
			I agree to the use of my data by Chester-le-Street ASC as outlined above
		</label>
		<div id="gdpr-confirm" class="custom-control custom-checkbox">
			<input type="checkbox" class="custom-control-input" id="customCheck1">
			<label class="custom-control-label" for="customCheck1">
				I agree
			</label>
		</div>
	</div>

	<h2>Terms and Conditions of the Club</h2>
	<p>
		The Member, and the Parent or Guardian, in the case of a person under the
		age of 18 years, hereby acknowledges that they have read the Club Rules and
		the Policies and Procedures Documentation of CHester-le-Street ASC, copies
		of which can be obtained from <a
		href="https://www.chesterlestreetasc.co.uk/policies" target="_blank">our
		website</a>. I confirm my understanding and acceptance that such rules (as
		amended from time to time) shall govern my membership of the club. I further
		acknowledge and accept the responsibilities of membership as set out in
		these rules and understand that it is my duty to read and abide by them
		(including any amendments). By providing my agreement, I consent to be bound
		by the Code of Conduct, Constitution, Rules and Policy Documents of the
		club.
	</p>

	<div class="alert alert-warning">
		<p class="mb-0">
			<strong>
				Each swimmer must agree to this section separately
			</strong>
		</p>
		<p class="mb-0">
			We've provided a box where each swimmer can tick for themselves. Ticking
			this checkbox is legally equivalent to signing an agreement on paper.
		</p>
	</div>


	<? for ($i = 0; $i < mysqli_num_rows($result); $i++) {
	$row[$i] = mysqli_fetch_array($result, MYSQLI_ASSOC);
	$age = date_diff(date_create($row[$i]['DateOfBirth']), date_create('today'))->y; ?>

	<div class="my-3 p-3 bg-white rounded box-shadow">

		<h3><? echo $row[$i]['MForename'] . " " . $row[$i]['MSurname']; ?></h3>

		<div class="form-group <? if ($age >= 12) { echo "mb-0"; } ?>">
			<div id="t-and-c-confirm" class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input" id="customCheck1">
				<label class="custom-control-label" for="customCheck1">
					I, <? echo $row[$i]['MForename'] . " " . $row[$i]['MSurname']; ?> agree to the
					use of my data by Chester-le-Street ASC as outlined above
				</label>
			</div>
		</div>

		<?
		if ($age < 12) { ?>

		<p>
			In the case of a member under the age of twelve years the Parent or
			Guardian undertakes to explain the content and implications of the Terms
			and Conditions of Membership of Chester-le-Street ASC.
		</p>

		<div class="form-group mb-0">
			<div id="t-and-c-confirm" class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input" id="customCheck1">
				<label class="custom-control-label" for="customCheck1">
					I, <? echo getUserName($userID); ?> have explained the content and
					implications to <? echo $row[$i]['MForename'] . " " . $row[$i]['MSurname']; ?>
					and can confirm that they understood.
				</label>
			</div>
		</div>

		<? } ?>

	</div>

	<? } ?>

	<h2>Photography Consent</h2>
	<p>
		Please read the ASA/Chester-le-Street ASC Photography Policy before you
		continue to give or withold consent for photography.
	</p>

	<p>
		Chester-le-Street ASC may wish to take photographs of individuals and groups
		of swimmers under the age of 18, which may include your child during their
		membership of Chester-le-Street ASC. Photographs will only be taken and
		published in accordance with the ASA policy which requires the club to
		obtain the consent of the Parent or Guardian to take and use photographs
		under the following circumstances.
	</p>

	<? for ($i = 0; $i < mysqli_num_rows($result); $i++) { ?>
	<div class="my-3 p-3 bg-white rounded box-shadow">
		<h3><? echo $row[$i]['MForename'] . " " . $row[$i]['MSurname']; ?></h3>
		<p>
			I, <? echo getUserName($userID); ?> agree to photography in the following
circumstances. Tick boxes only if you wish to grant us photography permission.
		</p>
		<div id="t-and-c-confirm" class="custom-control custom-checkbox">
			<input type="checkbox" class="custom-control-input" id="customCheck1">
			<label class="custom-control-label" for="customCheck1">
				Take photographs to use on the clubs website
			</label>
		</div>
		<div id="t-and-c-confirm" class="custom-control custom-checkbox">
			<input type="checkbox" class="custom-control-input" id="customCheck1">
			<label class="custom-control-label" for="customCheck1">
				Take photographs to use on social media sites
			</label>
		</div>
		<div id="t-and-c-confirm" class="custom-control custom-checkbox">
			<input type="checkbox" class="custom-control-input" id="customCheck1">
			<label class="custom-control-label" for="customCheck1">
				Take photographs to use on club noticeboards
			</label>
		</div>
		<div id="t-and-c-confirm" class="custom-control custom-checkbox">
			<input type="checkbox" class="custom-control-input" id="customCheck1">
			<label class="custom-control-label" for="customCheck1">
				Filming for training purposes only
			</label>
		</div>
		<div id="t-and-c-confirm" class="custom-control custom-checkbox">
			<input type="checkbox" class="custom-control-input" id="customCheck1">
			<label class="custom-control-label" for="customCheck1">
				Employ a professional photographer (approved by the club) who will take
				photographs in competitions and/or club events.
			</label>
		</div>
	</div>
	<? } ?>

	<h2>Medical Consent</h2>
	<p>For Parents and Guardians of members under 18 years</p>

	<? for ($i = 0; $i < mysqli_num_rows($result); $i++) {
		$age = date_diff(date_create($row[$i]['DateOfBirth']), date_create('today'))->y;
		if ($age < 18) { ?>
	<div class="my-3 p-3 bg-white rounded box-shadow">

		<h3>
			Consent for <? echo $row[$i]['MForename'] . " " . $row[$i]['MSurname']; ?>
		</h3>
		<p>
			I confirm that <? echo $row[$i]['MForename'] . " " . $row[$i]['MSurname']; ?>
			has not been advised by a doctor to take part in physical activities
			unless under medical supervision.
		</p>

		<p>
			I, <? echo getUserName($userID); ?> hereby give permission for the coach or
			other appropriate person to give the authority on my behalf for any medical
			or surgical treatment recommended by competent medical authorities, where it
			would be contrary to my child's interest, in the doctor's opinion, for any
			delay to be incurred by seeking my personal consent.
		</p>

		<div id="t-and-c-confirm" class="custom-control custom-checkbox">
			<input type="checkbox" class="custom-control-input" id="customCheck1">
			<label class="custom-control-label" for="customCheck1">
				Confirm
			</label>
		</div>

	</div>
	<? }
	} ?>

	<ul>
		<li>Data Protection
			<ul>
				<li>Parent signs and Agrees</li>
				<li>Checkbox, name and date automatic</li>
			</ul>
		</li>
		<li>Terms and Conditions of Club Membership
			<ul>
				<li><em>Member</em> agrees to terms</li>
				<li>If member is under 12, parent signs also</li>
			</ul>
		</li>
		<li>Photography Permission
			<ul>
				<li>Photos for website</li>
				<li>Photos for social media</li>
				<li>Photos for club noticeboard</li>
				<li>Filming for training purposes</li>
				<li>Pro photographer in;
					<ul>
						<li>Competitions</li>
						<li>Galas</li>
						<li>Meet</li>
						<li>Club Events</li>
					</ul>
				</li>
				<li><em>Check all the above are desired</em></li>
			</ul>
		</li>
		<li>Medical Consent
			<ul>
				<li>For members under 18</li>
				<li>Confirm a doctor hasn't said don't do sport</li>
			</ul>
		</li>
	</ul>

	<div class="mb-3">
		<a class="btn btn-outline-success" href="">Save</a>
		<a class="btn btn-success" href="">Save and Continue</a>
	</div>
</div>

<?php include BASE_PATH . "views/footer.php";
