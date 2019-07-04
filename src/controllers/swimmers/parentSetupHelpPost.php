<?php

global $db;

use Respect\Validation\Validator as v;

$sql = $db->prepare("SELECT * FROM `members` INNER JOIN `squads` ON members.SquadID = squads.SquadID WHERE `MemberID` = ?");
$sql->execute([$id]);

$row = $sql->fetch(PDO::FETCH_ASSOC);

$swimEnglandText = "Swim England Number";
if (mb_stripos($row['ASANumber'], env('ASA_CLUB_CODE')) > -1) {
	$swimEnglandText = "Temporary Membership Number";
}

if ($row == null) {
	halt(404);
}

if (v::email()->validate($_POST['emailAddr'])) {
  $pagetitle = $row['MForename'] . " " . $row['MSurname'];
  $text = '<h1>Online Membership System</h1><p class="mb-0"><strong>Your Access Key for ' . $row['MForename'] . " " . $row['MSurname'] . '</strong></p>';
  $text .= '<p>
    Here at ' . CLUB_NAME . ', we provide a number of online services to
    manage our members. Our services allow you to manage your swimmers, enter
    competitions, stay up to date by email and make payments by Direct Debit.
  </p>';

  if (!(defined('IS_CLS') && IS_CLS)) {
  $text .= '<p><strong>Please note:</strong> Some services may not be provided by your club.</p>';
  }

  $text .= '<p>
    If you haven\'t already done so, you will need to create an account on our
    membership system. This is easy to do - You only need to fill out one form
    and then verify your email address.
  </p>';
  $text .= '<p>
    Here\'s what you will need to do to add ' . htmlspecialchars($row['MForename'] . " " .
    $row['MSurname']) . ' to your account in our Online Membership System. There
    are two methods you can use to do this.
  </p>';
  $text .= '<h2>Add via Link</h2><p>
    You\'ll be taken to a page where
    you\'ll be asked to log in, if you aren\'t already, and we\'ll automatically
    add ' . htmlspecialchars($row['MForename']) . ' to your account.
  </p>';
  $text .= '<p>
    <a href="' . autoUrl("my-account/addswimmer/auto/" . $row['ASANumber'] . "/" .
    $row['AccessKey']) . '">Click Here to add ' . htmlspecialchars($row['MForename']) . '</a>
  </p>';
  $text .= '<h2>Add Manually</h2><p>
    Alternatively, to add a swimmer log into your account at
    ' . autoUrl("") . '  and the select \'My Account\' then
    \'Add Swimmers\' from the menu at the top.
  </p>';
  if (!(mb_stripos($row['ASANumber'], env('ASA_CLUB_CODE')) > -1)) {
  $text .= '<p>You\'ll be directed to a page and asked to enter your swimmer\'s Swim England Number and CLS ASC Access Key as below.</p>';
  } else {
  $text .= '<p>You will be asked to enter the temporary membership number and access key as below.</p>';
  }
  $text .= '
  <table class="table table-sm table-borderless d-inline mb-0">
    <tbody>
      <tr>
        <th scope="row" class="pl-0">' . $swimEnglandText . '</th>
        <td class="pr-0"><span class="mono">' . htmlspecialchars($row['ASANumber']) . '</span></td>
      </tr>
      <tr>
        <th scope="row" class="pl-0">CLS ASC Access Key</th>
        <td class="pr-0"><span class="mono">' . htmlspecialchars($row['AccessKey']) . '</span></td>
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
    </p>';

    if (mb_stripos($row['ASANumber'], env('ASA_CLUB_CODE')) > -1) {

      $text .= '
      <p>
        At the time you were given this form we did not yet have a Swim England registration number for ' . htmlspecialchars($row['MForename']) . '. We have given you a temporary number which starts with ' . htmlspecialchars(env('ASA_CLUB_CODE')) . ' which you can use to add your swimmer to your account.
      </p>
      ';

    }

    $text .= '

    <p>
      If you\'d like more information about how we use data, contact
      enquiries@chesterlestreetasc.co.uk.
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
header("Location: " . currentUrl());
