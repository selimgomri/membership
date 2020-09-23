<?php

if (!\SCDS\FormIdempotency::verify()) {
  halt(404);
}
if (!\SCDS\CSRF::verify()) {
  halt(404);
}

$db = app()->db;
$tenant = app()->tenant;

$insertIntoSwimmers = $db->prepare("INSERT INTO members (MForename, MMiddleNames, Msurname, DateOfBirth, Gender, ASANumber, ASACategory, RR, AccessKey, ClubPays, OtherNotes, Tenant) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$setTempASA = $db->prepare("UPDATE members SET ASANumber = ? WHERE MemberID = ?");

$setUser = $db->prepare("UPDATE members SET UserID = ? WHERE MemberID = ?");

$getMember = $db->prepare("SELECT UserID, MForename, MSurname FROM members WHERE MForename = ? AND MSurname = ? AND DateOfBirth = ? AND Tenant = ? AND Active");

$getUser = $db->prepare("SELECT UserID, Forename, Surname FROM users WHERE EmailAddress = ? AND Tenant = ? AND Active");

$addAccessLevel = $db->prepare("INSERT INTO `permissions` (`Permission`, `User`) VALUES (?, ?)");

$hasAccessLevel = $db->prepare("SELECT COUNT(*) FROM `permissions` WHERE `Permission` = ? AND `User` = ?");

$sendEmail = $db->prepare("INSERT INTO `notify` (`UserID`, `Status`, `Subject`, `Message`, `ForceSend`, `EmailType`) VALUES (:user, 'Queued', :subject, :message, 1, 'RegistrationEmails')");

if (is_uploaded_file($_FILES['file-upload']['tmp_name'])) {

  if (bool($_FILES['file-upload']['error'])) {
    // Error
    $_SESSION['TENANT-' . app()->tenant->getId()]['UploadError'] = true;
  } else if ($_FILES['file-upload']['type'] != 'text/csv' && $_FILES['file-upload']['type'] != 'application/vnd.ms-excel') {
    // Probably not a CSV
    $_SESSION['TENANT-' . app()->tenant->getId()]['UploadError'] = true;
  } else if ($_FILES['file-upload']['size'] > 30000) {
    // Too large, stop
    $_SESSION['TENANT-' . app()->tenant->getId()]['TooLargeError'] = true;
  } else {
    $failedSwimmers = [];
    try {
      $db->beginTransaction();

      $filePointer = fopen($_FILES['file-upload']['tmp_name'], 'r');
      while ($row = fgetcsv($filePointer)) {

        if (isset($row[3]) && $row[3] == 'Total Members By Subs') {
          break;
        }

        if (isset($row[1]) && ((int) $row[1]) != 0) {

          $names = explode(' ', trim($row[3]));

          $firstName = $lastName = $middleNames = null;

          if (sizeof($names) == 1) {
            $firstName = trim($row[3]);
            $lastName = 'Unknown-Surname';
          } else if (sizeof($names) == 0) {
            $firstName = (string) mb_strtolower(trim($row[7]));
            $lastName = 'Unknown-Surname';
          } else {
            $firstName = $names[0];
            $lastName = $names[sizeof($names) - 1];
            if (sizeof($names) > 2) {
              for ($i = 1; $i < sizeof($names) - 1; $i++) {
                if ($i > 1) {
                  $middleNames .= ' ';
                }
                $middleNames .= $names[$i];
              }
            }
          }

          $sex = 'Male';
          if (trim($row[6]) == 'Female') {
            $sex = 'Female';
          }

          try {
            $dateOfBirthObject = DateTime::createFromFormat('d/m/Y', $row[4], new DateTimeZone('Europe/London'));
            $dateOfBirth = $dateOfBirthObject->format('Y-m-d');
          } catch (Exception $e) {
            // Catch error DOB
            $dateOfBirth = '2000-01-01';
          }

          $details = [
            'MemberID' => (string) trim($row[1]),
            'UCard' => (string) trim($row[2]),
            'FirstName' => (string) mb_convert_case($firstName, MB_CASE_TITLE_SIMPLE),
            'MiddleNames' => (string) mb_convert_case($middleNames, MB_CASE_TITLE_SIMPLE),
            'LastName' => (string) mb_convert_case($lastName, MB_CASE_TITLE_SIMPLE),
            'DOB' => (string) $dateOfBirth,
            'Sex' => (string) $sex,
            'Email' => (string) mb_strtolower(trim($row[7])),
          ];

          // Check if there is already a user
          $getUser->execute([
            $details['Email'],
            $tenant->getId(),
          ]);

          $user = $getUser->fetch(PDO::FETCH_ASSOC);

          // Check if there is already a member
          $getMember->execute([
            $details['FirstName'],
            $details['LastName'],
            $details['DOB'],
            $tenant->getId(),
          ]);

          $member = $getMember->fetch(PDO::FETCH_ASSOC);

          if (!$member) {

            // Does not exist so we will add the member
            $insertIntoSwimmers->execute([
              $details['FirstName'],
              $details['MiddleNames'],
              $details['LastName'],
              $details['DOB'],
              $details['Sex'],
              null,
              0,
              0,
              mb_substr(hash(random_bytes(64), 'sha256'), 0, 10),
              0,
              '',
              $tenant->getId(),
            ]);

            $memberId = $db->lastInsertId();

            // $setTempASA = $db->prepare("UPDATE members SET ASANumber = ? WHERE MemberID = ?");
            $setTempASA->execute([
              'TEMPORARY-' . mb_strtoupper($tenant->getKey('ASA_CLUB_CODE')) . '-' . $memberId,
              $memberId,
            ]);

            if ($user) {

              // Set user on member
              $setUser->execute([
                $user['UserID'],
                $memberId,
              ]);

              // Add access level if needed
              $addAccessLevel = $db->prepare("INSERT INTO `permissions` (`Permission`, `User`) VALUES (?, ?)");
              $hasAccessLevel->execute([
                'Parent',
                $user['UserID'],
              ]);

              if ($hasAccessLevel->fetchColumn() == 0) {
                try {
                  $addAccessLevel->execute([
                    'Parent',
                    $user['UserID'],
                  ]);
                } catch (PDOException $e) {
                  // Ignore
                }
              }

              // Send user an email
              $subject = 'We\'ve added a member to your ' . $tenant->getName() . ' account';
              $content = "<p>We've added " . htmlspecialchars($details['FirstName'] . ' ' . $details['LastName']) .  " to your club account.</p>";
              $content .= "<p><a href=\"" . htmlspecialchars(autoUrl("")) . "\">Log in</a> to view more information.</p>";
              $content .= '<p>Please note that ' . htmlspecialchars(app()->tenant->getKey('CLUB_NAME')) . ' may not provide all services included in the membership software.</p>';

              $sendEmail->execute([
                "user" => $userId,
                "subject" => $subject,
                "message" => $content
              ]);
            } else {
              // Create a new user account

              $uuid = Ramsey\Uuid\Uuid::uuid4();
              $password = $uuid->toString();

              $addUser = $db->prepare("INSERT INTO users (EmailAddress, `Password`, Forename, Surname, Mobile, EmailComms, MobileComms, RR, Tenant) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
              $addUser->execute([
                $details['Email'],
                password_hash($password, PASSWORD_BCRYPT),
                $details['FirstName'],
                $details['LastName'],
                '',
                0,
                0,
                1,
                $tenant->getId(),
              ]);

              $userId = $db->lastInsertId();

              $addAccessLevel->execute([
                'Parent',
                $userId,
              ]);

              // Set user on member
              $setUser->execute([
                $userId,
                $memberId,
              ]);

              // Send user an email
              $subject = 'Complete your registration at ' . $tenant->getName();
              $content = "<p>We've created an account for you in our membership system.</p>";
              $content .= "<p>To continue, <a href=\"" . htmlspecialchars(autoUrl("assisted-registration/" . $userId . "/" . $password)) . "\">please follow this link</a></p>";
              if ($tenant->getBooleanKey('REQUIRE_FULL_REGISTRATION')) {
                $content .= "<p>As part of the registration process, we'll ask you to set a password, let us know your communication preferences and fill in important information about you and/or your members. At the end, we'll set up a direct debit so that payments to " . htmlspecialchars(app()->tenant->getKey('CLUB_NAME')) . " are taken automatically.</p>";
                $content .= "<p>You'll also be given the opportunity to set up a direct debit.</p>";
              }
              $content .= '<p>Please note that ' . htmlspecialchars(app()->tenant->getKey('CLUB_NAME')) . ' may not provide all services included in the membership software.</p>';

              $sendEmail->execute([
                "user" => $userId,
                "subject" => $subject,
                "message" => $content
              ]);
            }
          }
        }
      }

      $db->commit();
      $_SESSION['TENANT-' . app()->tenant->getId()]['UploadSuccess'] = true;
    } catch (Exception $e) {
      $db->rollBack();
      pre($e);
      $_SESSION['TENANT-' . app()->tenant->getId()]['UploadError'] = true;
    }

    if (sizeof($failedSwimmers) > 0) {
      $_SESSION['TENANT-' . app()->tenant->getId()]['FailedSwimmers'] = $failedSwimmers;
    }
  }
} else {
  $_SESSION['TENANT-' . app()->tenant->getId()]['UploadError'] = true;
}

header("Location: " . autoUrl("admin/member-upload/sheffield"));
