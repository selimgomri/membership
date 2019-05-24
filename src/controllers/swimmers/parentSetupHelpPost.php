<?
use Respect\Validation\Validator as v;
$id = mysqli_real_escape_string($link, $id);

$sql = "SELECT * FROM `members` INNER JOIN `squads` ON members.SquadID =
squads.SquadID WHERE `MemberID` = '$id';";
$result = mysqli_query($link, $sql);

if (mysqli_num_rows($result) == 0) {
	halt(404);
}

if (v::email()->validate($_POST['emailAddr'])) {

  $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

  $pagetitle = $row['MForename'] . " " . $row['MSurname'];
  $text = '<h1>Online Membership System</h1><p class="mb-0"><strong>Your Access Key for ' . $row['MForename'] . " " . $row['MSurname'] . '</strong></p>';
  $text .= '<p>
    Here at ' . CLUB_NAME . ', we provide a number of online services to
    manage our members. Our services allow you to manage your swimmers, enter
    competitions, stay up to date by email and make payments by Direct Debit
    (from 2019).
  </p>';
  $text .= '<p>
    If you haven’t already done so, you will need to create an account on our
    membership system. This is easy to do - You only need to fill out one form
    and then verify your email address.
  </p>';
  $text .= '<p>
    Here’s what you will need to do to add ' . $row['MForename'] . " " .
    $row['MSurname'] . ' to your account in our Online Membership System. There
    are two methods you can use to do this.
  </p>';
  $text .= '<h2>Add via Link</h2><p>
    You\'ll be taken to a page where
    you\'ll be asked to log in, if you aren\'t already, and we\'ll automatically
    add ' . $row['MForename'] . ' to your account.
  </p>';
  $text .= '<p>
    <a href="' . autoUrl("myaccount/addswimmer/auto/" . $row['ASANumber'] . "/" .
    $row['AccessKey']) . '">Click Here to add ' . $row['MForename'] . '</a>
  </p>';
  $text .= '<h2>Add Manually</h2><p>
    Alternatively, to add a swimmer log into your account at
    ' . autoUrl("") . '  and the select \'My Account\' then
    \'Add Swimmers\' from the menu at the top.
  </p>';
  $text .= '<p>
    You\'ll be directed to a page and asked to enter your swimmer\'s Swim England Number
    and CLS ASC Access Key as below.
  </p>';
  $text .= '
  <table class="table table-sm table-borderless d-inline mb-0">
    <tbody>
      <tr>
        <th scope="row" class="pl-0">Swim England Number</th>
        <td class="pr-0"><span class="mono">' . $row['ASANumber'] . '</span></td>
      </tr>
      <tr>
        <th scope="row" class="pl-0">CLS ASC Access Key</th>
        <td class="pr-0"><span class="mono">' . $row['AccessKey'] . '</span></td>
      </tr>
    </tbody>
  </table>
  ';
  $text .= '
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
      Don’t have a Swim England Number? If so, and you need to be registered in our
      system as a member, we\'ll give you a reference number starting with ' .
      CLUB_CODE . 'X which you can use in place of a Swim England Number in our systems
      only.
    </p>

    <p>
      If you’d like more information about how we use data, contact
      enquiries@chesterlestreetasc.co.uk.
    </p>

    <p>
      The user account service is provided to ' . CLUB_NAME . ' by
      Chester-le-Street ASC Club Digital Services.
    </p>
  </div>
  ';

  if (notifySend(null, "Access Key for " . $row['MForename'] . " " . $row['MSurname'], $text, "Parent of " . $row['MForename'] . " " . $row['MSurname'], $_POST['emailAddr'], $from = ["Email" => "membership@" . EMAIL_DOMAIN, "Name" => CLUB_NAME])) {
    $_SESSION['EmailStatus'] = true;
  } else {
    $_SESSION['EmailStatus'] = false;
  }
} else {
  $_SESSION['EmailStatus'] = false;
}
header("Location: " . app('request')->curl);
