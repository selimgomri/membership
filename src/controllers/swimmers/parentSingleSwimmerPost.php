<?

$id = mysqli_real_escape_string($link, $id);
$userID = $_SESSION['UserID'];
$forenameUpdate = false;
$middlenameUpdate = false;
$surnameUpdate = false;
$dateOfBirthUpdate = false;
$sexUpdate = false;
$otherNotesUpdate = false;
$photoUpdate = false;
$update = false;
$successInformation = "";

$query = "SELECT * FROM members WHERE MemberID = '$id' ";
$result = mysqli_query($link, $query);
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);

$forename = $row['MForename'];
$middlename = $row['MMiddleNames'];
$surname = $row['MSurname'];
$dateOfBirth = $row['DateOfBirth'];
$sex = $row['Gender'];
$otherNotes = $row['OtherNotes'];

// Get the swimmer name
$sqlSecurityCheck = "SELECT `MForename`, `MSurname`, `UserID` FROM `members`
WHERE MemberID = '$id';";
$resultSecurityCheck = mysqli_query($link, $sqlSecurityCheck);
$swimmersSecurityCheck = mysqli_fetch_array($resultSecurityCheck, MYSQLI_ASSOC);

if ($swimmersSecurityCheck['UserID'] != $userID) {
  halt(404);
}
else {
  if (!empty($_POST['forename'])) {
    $newForename = mysqli_real_escape_string($link,
    trim(htmlspecialchars(ucwords($_POST['forename']))));
    if ($newForename != $forename) {
      $sql = "UPDATE `members` SET `MForename` = '$newForename' WHERE `MemberID` =
      '$id'";
      mysqli_query($link, $sql);
      $forenameUpdate = true;
      $update = true;
    }
  }
  if (isset($_POST['middlenames'])) {
    $newMiddlenames = mysqli_real_escape_string($link,
    trim(htmlspecialchars(ucwords($_POST['middlenames']))));
    if ($newMiddlenames != $middlename) {
      $sql = "UPDATE `members` SET `MMiddleNames` = '$newMiddlenames' WHERE
      `MemberID` = '$id'";
      mysqli_query($link, $sql);
      $middlenameUpdate = true;
      $update = true;
    }
  }
  if (!empty($_POST['surname'])) {
    $newSurname = mysqli_real_escape_string($link,
    trim(htmlspecialchars(ucwords($_POST['surname']))));
    if ($newSurname != $surname) {
      $sql = "UPDATE `members` SET `MSurname` = '$newSurname' WHERE `MemberID` = '$id'";
      mysqli_query($link, $sql);
      $surnameUpdate = true;
      $update = true;
    }
  }
  if (!empty($_POST['datebirth'])) {
    $newDateOfBirth = mysqli_real_escape_string($link,
    trim(htmlspecialchars(ucwords($_POST['datebirth']))));
    // NEEDS WORK FOR DATE TO BE RIGHT
    if ($newDateOfBirth != $dateOfBirth) {
      $sql = "UPDATE `members` SET `DateOfBirth` = '$newDateOfBirth' WHERE
      `MemberID` = '$id'";
      mysqli_query($link, $sql);
      $dateOfBirthUpdate = true;
      $update = true;
    }
  }
  if (!empty($_POST['sex'])) {
    $newSex = mysqli_real_escape_string($link,
    trim(htmlspecialchars(ucwords($_POST['sex']))));
    if ($newSex != $sex) {
      $sql = "UPDATE `members` SET `Gender` = '$newSex' WHERE `MemberID` = '$id'";
      mysqli_query($link, $sql);
      $sexUpdate = true;
      $update = true;
    }
  }
  if (isset($_POST['otherNotes'])) {
    $newOtherNotes = mysqli_real_escape_string($link,
    trim(htmlspecialchars(ucfirst($_POST['otherNotes']))));
    if ($newOtherNotes != $otherNotes) {
      $sql = "UPDATE `members` SET `OtherNotes` = '$newOtherNotes' WHERE
      `MemberID` = '$id'";
      mysqli_query($link, $sql);
      $otherNotesUpdate = true;
      $update = true;
    }
  }
  if ((!empty($_POST['disconnect'])) && (!empty($_POST['disconnectKey']))) {
    $disconnect = mysqli_real_escape_string($link,
    trim(htmlspecialchars($_POST['disconnect'])));
    $disconnectKey = mysqli_real_escape_string($link,
    trim(htmlspecialchars($_POST['disconnectKey'])));
    if ($disconnect == $disconnectKey) {
      $newKey = generateRandomString(8);
      $sql = "UPDATE `members` SET `UserID` = NULL, `AccessKey` = '$newKey'
      WHERE `MemberID` = '$id'";
      mysqli_query($link, $sql);
      header("Location: " . autoUrl("swimmers"));
    }
  }
  if (!empty($_POST['swimmerDeleteDanger'])) {
    $deleteKey = mysqli_real_escape_string($link,
    trim(htmlspecialchars($_POST['swimmerDeleteDanger'])));
    if ($deleteKey == $dbAccessKey) {
      $sql = "DELETE FROM `members` WHERE `members`.`MemberID` = '$id'";
      mysqli_query($link, $sql);
      header("Location: " . autoUrl("swimmers"));
    }
  }
  if (isset($_POST['webPhoto']) || isset($_POST['socPhoto']) || isset($_POST['noticePhoto']) || isset($_POST['trainFilm']) || isset($_POST['webPhoto'])) {
    setupPhotoPermissions($id);
  }
  // Web Photo Permissions
  $photo[0] = 1;
  if (!isset($_POST['webPhoto']) || $_POST['webPhoto'] != 1) {
    $photo[0] = 0;
  }
  $sql = "UPDATE `memberPhotography` SET `Website` = '$photo[0]' WHERE `MemberID` =
  '$id';";
  mysqli_query($link, $sql);
  $photoUpdate = true;
  $update = true;

  // Social Media Photo Permissions
  $photo[1] = 1;
  if (!isset($_POST['socPhoto']) || $_POST['socPhoto'] != 1) {
    $photo[1] = 0;
  }
  $sql = "UPDATE `memberPhotography` SET `Social` = '$photo[1]' WHERE `MemberID` =
  '$id';";
  mysqli_query($link, $sql);
  $photoUpdate = true;
  $update = true;

  // Notice Board Photo Permissions
  $photo[2] = 1;
  if (!isset($_POST['noticePhoto']) || $_POST['noticePhoto'] != 1) {
    $photo[2] = 0;
  }
  $sql = "UPDATE `memberPhotography` SET `Noticeboard` = '$photo[2]' WHERE
  `MemberID` = '$id';";
  mysqli_query($link, $sql);
  $photoUpdate = true;
  $update = true;

  // Filming in Training Permissions
  $photo[3] = 1;
  if (!isset($_POST['trainFilm']) || $_POST['trainFilm'] != 1) {
    $photo[3] = 0;
  }
  $sql = "UPDATE `memberPhotography` SET `FilmTraining` = '$photo[3]' WHERE
  `MemberID` = '$id';";
  mysqli_query($link, $sql);
  $photoUpdate = true;
  $update = true;

  // Pro Photographer Photo Permissions
  $photo[4] = 1;
  if (!isset($_POST['proPhoto']) || $_POST['proPhoto'] != 1) {
    $photo[4] = 0;
  }
  $sql = "UPDATE `memberPhotography` SET `ProPhoto` = '$photo[4]' WHERE
  `MemberID` = '$id';";
  mysqli_query($link, $sql);
  $photoUpdate = true;
  $update = true;
}

header("Location: " . app('request')->curl);
