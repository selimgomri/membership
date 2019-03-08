<?

$userID = mysqli_real_escape_string($link, $_SESSION['UserID']);
$renewal = mysqli_real_escape_string($link, $renewal);
$sql = "UPDATE `renewalProgress` SET `Stage` = `Stage` + 1 WHERE
`RenewalID` = '$renewal' AND `UserID` = '$userID';";
mysqli_query($link, $sql);

$query = "SELECT * FROM users WHERE UserID = '$userID';";
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

$mailtext = '
  <p>Hello ' . $forename . ' ' . $surname . '. This is a copy of your membership renewal. Please retain it for your records.</p>
  <p class="lead">Completed at ' . date("H:i, j F Y") . '</p>

  <h2>Your Details</h2>
  <dl>
		<dt>Name</dt>
    <dd>' . $forename . ' ' . $surname . '</dd>';

if ($emailComms) { $mailtext .= '
	<dt>Allow ' . CLUB_SHORT_NAME . ' to send emails?</dt>
  <dd>You\'ve allowed us to send you emails. There are more email options available in My Account</dd>';
} else {
  $mailtext .= '
	<dt>Allow ' . CLUB_SHORT_NAME . ' to send emails?</dt>
  <dd>You\'ve not allowed us to send you emails. There are fine grained email options available in My Account</dd>';
}

$mailtext .= '
	<dt>Mobile Phone Number</dt>
  <dd>' . $mobile . '</dd>';

if ($mobileComms) { $mailtext .= '
	<dt>Allow ' . CLUB_SHORT_NAME . ' to send text messages?</dt>
  <dd>You\'ve allowed us to send you sms messages</dd>';
} else {
  $mailtext .= '
	<dt>Allow ' . CLUB_SHORT_NAME . ' to send emails?</dt>
  <dd>You\'ve not allowed us to send you sms messages</dd>';
}

$mailtext .= '
	</dl>

	<hr>

	<h2>Your Swimmers</h2>
	' . mySwimmersTable($link, $userID) . '

	<hr>

	<h2>Your Monthly Fees</h2>
	' . myMonthlyFeeTable($link, $userID) . '

	<hr>

	<h2>Swimmer Medical Information</h2>';

	$sql = "SELECT * FROM `members` LEFT JOIN `memberMedical` ON members.MemberID =
	memberMedical.MemberID WHERE `UserID` = '$userID';";
	$result = mysqli_query($link, $sql);

	for ($i = 0; $i < mysqli_num_rows($result); $i++) {
		$row[$i] = mysqli_fetch_array($result, MYSQLI_ASSOC);
    $mailtext .= '
		<h3>' . $row[$i]['MForename'] . " " . $row[$i]['MSurname'] . '</h3>

		<dl>
			<dt>Medical Conditions and/or Disabilities</dt>';
			if ($row[$i]['Conditions'] != "") {
	    $mailtext .= '<dd>' . $row[$i]['Conditions'] . '</dd>';
			} else {
			$mailtext .= '<dd>None</dd>';
      }

      $mailtext .= '<dt>Allergies</dt>';
      if ($row[$i]['Allergies'] != "") {
	    $mailtext .= '<dd>' . $row[$i]['Allergies'] . '</dd>';
			} else {
			$mailtext .= '<dd>None</dd>';
      }

      $mailtext .= '<dt>Medication</dt>';
      if ($row[$i]['Medication'] != "") {
	    $mailtext .= '<dd>' . $row[$i]['Medication'] . '</dd>';
			} else {
			$mailtext .= '<dd>None</dd>';
      }

      $mailtext .= '

		</dl>';

	}

	$mailtext .= '<hr>

	<h2>Code of Conduct Agreements</h2>

	<h3>' . $forename . " " . $surname . ' (Parent)</h3>

	' . getPostContent(8) . '

	<p>You have agreed to the above code of conduct.</p>

	<hr>';

	$name = getUserName($userID);

	$sql = "SELECT members.MemberID, members.MForename, members.MSurname,
	members.DateOfBirth, memberPhotography.Website, memberPhotography.Social,
	memberPhotography.Noticeboard, memberPhotography.FilmTraining,
	memberPhotography.ProPhoto, squads.SquadCoC FROM ((`members` LEFT JOIN
	`memberPhotography` ON members.MemberID = memberPhotography.MemberID) INNER
	JOIN `squads` ON `squads`.`SquadID` = `members`.`SquadID`) WHERE members.UserID =
	'$userID' ORDER BY `MForename` ASC, `MSurname` ASC;";
	$result = mysqli_query($link, $sql);

$mailtext .= '
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

  ' . getPostContent(4);

	for ($i = 0; $i < mysqli_num_rows($result); $i++) {
	$row[$i] = mysqli_fetch_array($result, MYSQLI_ASSOC);
	$id[$i] = $row[$i]['MemberID'];
	$age[$i] = date_diff(date_create($row[$i]['DateOfBirth']),
	date_create('today'))->y;

	$mailtext .= '
  <h2>Swimmer Agreements</h2>
  <p class="lead">Code of Conduct and Terms and Conditions Agreements</p>
  <hr><h3>' . $row[$i]['MForename'] . " " . $row[$i]['MSurname'] . '</h3>

	<p>
		' . $row[$i]['MForename'] . " " . $row[$i]['MSurname'] . ' has agreed to the
		Terms and Conditions of Chester-le-Street ASC as outlined above and the code
		of conduct for their squad detailed below.
	</p>';

  $mailtext .= getPostContent($row[$i]['SquadCoC']);

	if ($age[$i] < 12) {

  	$mailtext .= '<p>
  		In the case of a member under the age of twelve years the Parent or
  		Guardian undertakes to explain the content and implications of the Terms
  		and Conditions of Membership of Chester-le-Street ASC.
  	</p>

  	<p>
  		You, ' . $name . ' have explained the content and implications to ' .
  		$row[$i]['MForename'] . " " . $row[$i]['MSurname'] . ' and have confirmed
  		that they understood.
  	</p>';

	}

	}

	$mailtext .= '<hr>

	<h2>Photography Consent</h2>
	<p>
		You have read the ASA/Chester-le-Street ASC Photography Policy before you
		continued to give or withold consent for photography.
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
		time by heading to Swimmers in your account.
	</p>';

	for ($i = 0; $i < mysqli_num_rows($result); $i++) {
		if ($age[$i] < 18) {
			$photo = [];
      if ($row[$i]['Website'] == 1) {
        $photo[0] = "Allowed";
      }
      if ($row[$i]['Social'] == 1) {
        $photo[1] = "Allowed";
      }
      if ($row[$i]['Noticeboard'] == 1) {
        $photo[2] = "Allowed";
      }
      if ($row[$i]['FilmTraining'] == 1) {
        $photo[3] = "Allowed";
      }
      if ($row[$i]['ProPhoto'] == 1) {
        $photo[4] = "Allowed";
      }
		$mailtext .= '<h3>' . $row[$i]['MForename'] . " " . $row[$i]['MSurname'] . '</h3>
		<p>
			I, ' . $name . ' agree to photography in the following
circumstances.
		</p>
		<ul>
      <li>Take photographs to use on the clubs website<br>' . $photo[0] . '</li>
      <li>Take photographs to use on social media sites<br>' . $photo[1] . '</li>
      <li>Take photographs to use on club noticeboards<br>' . $photo[2] . '</li>
      <li>Filming for training purposes only<br>' . $photo[3] . '</li>
      <li>Employ a professional photographer (approved by the club) who will take
      photographs in competitions and/or club events.<br>' . $photo[4] . '</li>
		</ul>';
	} }

	$mailtext .= '<h2>Medical Consent</h2>
	<p>
		For Parents and Guardians of members under 18 years. Swimmers aged 18 or
		over are individually responsible for ensuring they only swim if not told by
		a Doctor to refrain from physical activity.
	</p>';

	for ($i = 0; $i < mysqli_num_rows($result); $i++) {
		if ($age[$i] < 18) {

	$mailtext .= '<h3>
		Consent for ' . $row[$i]['MForename'] . " " . $row[$i]['MSurname'] . '
	</h3>
	<p>
		I confirmed that ' . $row[$i]['MForename'] . " " . $row[$i]['MSurname'] . '
		has not been advised by a doctor to not take part in physical activities
		unless under medical supervision.
	</p>

	<p>
		I, ' . $name . ' hereby give permission for the coach or
		other appropriate person to give the authority on my behalf for any medical
		or surgical treatment recommended by competent medical authorities, where it
		would be contrary to my child\'s interest, in the doctor\'s opinion, for any
		delay to be incurred by seeking my personal consent.
	</p>';
  } }

  $mailtext .= '<p>Contact the Membership Secretary if there are any issues.</p>';

  notifySend($email, "Your Membership Renewal", $mailtext, $forename . ' ' . $surname, $email, $from = ["Email" => "noreply@membership-renewal.service.chesterlestreetasc.co.uk", "Name" => "Chester-le-Street ASC"]);
  header("Location: " . app('request')->curl);
