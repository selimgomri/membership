<?php

$db = app()->db;
$currentUser = app()->user;

use Brick\Postcode\PostcodeFormatter;

$PostcodeFormatter = new PostcodeFormatter();

$allowed = false;

try {
  if (isset($_POST['street-and-number']) && $_POST['street-and-number'] && isset($_POST['town-city']) && $_POST['town-city'] && isset($_POST['post-code']) && $_POST['post-code']) {
    $allowed = true;
    $addr = [
      'streetAndNumber' => mb_convert_case(trim($_POST['street-and-number']), MB_CASE_TITLE, "UTF-8"),
      'city' => mb_convert_case(trim($_POST['town-city']), MB_CASE_TITLE, "UTF-8"),
      'postCode' => (string) $PostcodeFormatter->format('GB', trim($_POST['post-code'])),
    ];
    if (isset($_POST['flat-building']) && $_POST['flat-building']) {
      $addr += ['flatOrBuilding' => mb_convert_case(trim($_POST['flat-building']), MB_CASE_TITLE, "UTF-8")];
    }
    if (isset($_POST['county-province']) && $_POST['county-province']) {
      $addr += ['county' => mb_convert_case(trim($_POST['county-province']), MB_CASE_TITLE, "UTF-8")];
    }
    $addr = json_encode($addr);
    $currentUser->setUserOption('MAIN_ADDRESS', $addr);
    $_SESSION['OptionsUpdate'] = true;
  }

  if ($allowed) {
    $member = null;
    if (isPartialRegistration()) {
      $member = getNextSwimmer($_SESSION['UserID'], 0, true);
    } else {
      $member = getNextSwimmer($_SESSION['UserID'], 0);
    }

    if ($member != null) {
      $sql = $db->prepare("UPDATE `renewalProgress` SET `Stage` = `Stage` + 1, `Substage` = '0', `Part` = ? WHERE `RenewalID` = ? AND `UserID` = ?");
      $sql->execute([
        $member,
        $renewal,
        $_SESSION['UserID']
      ]);
    }
    header("Location: " . autoUrl("renewal/go"));
  }
} catch (Exception $e) {
  $_SESSION['ErrorState'] = "
	<div class=\"alert alert-danger\">
	<p class=\"mb-0\"><strong>An error occured when we tried to update our records</strong></p>
	<p class=\"mb-0\">Please try again</p>
	</div>";
	header("Location: " . autoUrl("renewal/go"));
}