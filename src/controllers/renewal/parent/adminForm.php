<?php

$userID = mysqli_real_escape_string($link, $_SESSION['UserID']);

$row = [];

$mySwimmers = mySwimmersTable($link, $userID);
$name = getUserName($userID);

$sql = "SELECT members.MemberID, members.MForename, members.MSurname,
members.DateOfBirth, memberPhotography.Website, memberPhotography.Social,
memberPhotography.Noticeboard, memberPhotography.FilmTraining,
memberPhotography.ProPhoto FROM (`members` LEFT JOIN `memberPhotography` ON
members.MemberID = memberPhotography.MemberID) WHERE `UserID` = '$userID' ORDER
BY `MForename` ASC, `MSurname` ASC;";
$result = mysqli_query($link, $sql);

$pagetitle = "Administration Form";
include BASE_PATH . "views/header.php";
?>

<div class="container">
	<form method="post">
		<h1>Club Administration Form</h1>
		<? if (isset($_SESSION['ErrorState'])) {
			echo $_SESSION['ErrorState'];
			unset($_SESSION['ErrorState']);
		} ?>
		<p class="lead">
			In this next step you, and your swimmers will need to agree to the terms and
			conditions of the club.
		</p>

		<p>
			This form relates to yourself and the swimmers listed below.
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
			<div class="custom-control custom-checkbox">
				<input type="checkbox" value="1" class="custom-control-input"
				name="data-agree" id="data-agree">
				<label class="custom-control-label" for="data-agree">
					I (<? echo $name; ?>) agree to the use of my data by Chester-le-Street
					ASC as outlined above
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
		$id[$i] = $row[$i]['MemberID'];
		$age[$i] = date_diff(date_create($row[$i]['DateOfBirth']), date_create('today'))->y; ?>

		<div class="my-3 p-3 bg-white rounded box-shadow">

			<h3><? echo $row[$i]['MForename'] . " " . $row[$i]['MSurname']; ?></h3>

			<div class="form-group <? if ($age[$i] >= 12) { echo "mb-0"; } ?>">
				<div class="custom-control custom-checkbox">
					<input type="checkbox" value="1" class="custom-control-input" name="<?
					echo $id[$i]; ?>-tc-confirm" id="<? echo $id[$i]; ?>-tc-confirm">
					<label class="custom-control-label" for="<? echo $id[$i];
					?>-tc-confirm">
						I, <? echo $row[$i]['MForename'] . " " . $row[$i]['MSurname']; ?>
						agree to the Terms and Conditions of Chester-le-Street ASC as outlined
						above
					</label>
				</div>
			</div>

			<?
			if ($age[$i] < 12) { ?>

			<p>
				In the case of a member under the age of twelve years the Parent or
				Guardian undertakes to explain the content and implications of the Terms
				and Conditions of Membership of Chester-le-Street ASC.
			</p>

			<div class="form-group mb-0">
				<div class="custom-control custom-checkbox">
					<input type="checkbox" value="1" class="custom-control-input" name="<?
					echo $id[$i]; ?>-pg-understanding" id="<? echo $id[$i];
					?>-pg-understanding">
					<label class="custom-control-label" for="<? echo $id[$i];
					?>-pg-understanding">
						I, <? echo $name; ?> have explained the content and
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

		<p>
			It is entirely up to you whether or not you choose to allow us to take
			photographs and/or video of your child. You can change your choices at any
			time by heading to Swimmers.
		</p>

		<? for ($i = 0; $i < mysqli_num_rows($result); $i++) {
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
		<div class="my-3 p-3 bg-white rounded box-shadow">
			<h3><? echo $row[$i]['MForename'] . " " . $row[$i]['MSurname']; ?></h3>
			<p>
				I, <? echo $name; ?> agree to photography in the following
	circumstances. Tick boxes only if you wish to grant us photography permission.
			</p>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" value="1" <? echo $photo[0]; ?>
				class="custom-control-input" name="<? echo $id[$i]; ?>-photo-web" id="<?
				echo $id[$i]; ?>-photo-web">
				<label class="custom-control-label" for="<? echo $id[$i]; ?>-photo-web">
					Take photographs to use on the clubs website
				</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" value="1" <? echo $photo[1]; ?>
				class="custom-control-input" name="<? echo $id[$i]; ?>-photo-soc" id="<?
				echo $id[$i]; ?>-photo-soc">
				<label class="custom-control-label" for="<? echo $id[$i]; ?>-photo-soc">
					Take photographs to use on social media sites
				</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" value="1" <? echo $photo[2]; ?>
				class="custom-control-input" name="<? echo $id[$i]; ?>-photo-nb" id="<?
				echo $id[$i]; ?>-photo-nb">
				<label class="custom-control-label" for="<? echo $id[$i]; ?>-photo-nb">
					Take photographs to use on club noticeboards
				</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" value="1" <? echo $photo[3]; ?>
				class="custom-control-input" name="<? echo $id[$i]; ?>-photo-film" id="<?
				echo $id[$i]; ?>-photo-film">
				<label class="custom-control-label" for="<? echo $id[$i]; ?>-photo-film">
					Filming for training purposes only
				</label>
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" value="1" <? echo $photo[4]; ?>
				class="custom-control-input" name="<? echo $id[$i]; ?>-photo-pro" id="<?
				echo $id[$i]; ?>-photo-pro">
				<label class="custom-control-label" for="<? echo $id[$i]; ?>-photo-pro">
					Employ a professional photographer (approved by the club) who will take
					photographs in competitions and/or club events.
				</label>
			</div>
		</div>
		<? } } ?>

		<h2>Medical Consent</h2>
		<p>For Parents and Guardians of members under 18 years</p>

		<? for ($i = 0; $i < mysqli_num_rows($result); $i++) {
			if ($age[$i] < 18) { ?>
		<div class="my-3 p-3 bg-white rounded box-shadow">

			<h3>
				Consent for <? echo $row[$i]['MForename'] . " " . $row[$i]['MSurname']; ?>
			</h3>
			<p>
				I confirm that <? echo $row[$i]['MForename'] . " " .
				$row[$i]['MSurname']; ?> has not been advised by a doctor to not take
				part in physical activities unless under medical supervision.
			</p>

			<p>
				I, <? echo $name; ?> hereby give permission for the coach or
				other appropriate person to give the authority on my behalf for any medical
				or surgical treatment recommended by competent medical authorities, where it
				would be contrary to my child's interest, in the doctor's opinion, for any
				delay to be incurred by seeking my personal consent.
			</p>

			<div class="custom-control custom-checkbox">
				<input type="checkbox" value="1" class="custom-control-input" name="<?
				echo $id[$i]; ?>-med" id="<? echo $id[$i]; ?>-med">
				<label class="custom-control-label" for="<? echo $id[$i]; ?>-med">
					Confirm
				</label>
			</div>

		</div>
		<? }
		} ?>

		<div class="mb-3">
			<a class="btn btn-outline-success" href="">Save</a>
			<button type="submit" class="btn btn-success">Save and Continue</button>
		</div>
	</form>
</div>

<?php include BASE_PATH . "views/footer.php";
