<?php

$db = app()->db;

$session = \SCDS\Onboarding\Session::retrieve($_SESSION['OnboardingSessionId']);

if ($session->status == 'not_ready') halt(404);

$user = $session->getUser();

$tenant = app()->tenant;

$logos = app()->tenant->getKey('LOGO_DIR');

$stages = $session->stages;

$tasks = \SCDS\Onboarding\Member::stagesOrder();

// Get member
$onboardingMember = \SCDS\Onboarding\Member::retrieveById($id);

$member = $onboardingMember->getMember();

$website = $noticeboard = $social = $proPhoto = $filmTraining = false;

if ($_POST['website'] && $_POST['website']) {
  $website = true;
}

if ($_POST['social'] && $_POST['social']) {
  $social = true;
}

if ($_POST['noticeboard'] && $_POST['noticeboard']) {
  $noticeboard = true;
}

if ($_POST['pro-photo'] && $_POST['pro-photo']) {
  $proPhoto = true;
}

if ($_POST['film-training'] && $_POST['film-training']) {
  $filmTraining = true;
}

// Does permissions row exist?
$get = $db->prepare("SELECT COUNT(*) FROM memberPhotography WHERE MemberID = ?");
$get->execute([
  $member->getId(),
]);

$exists = $get->fetchColumn();

if ($exists) {
  // UPDATE
  $update = $db->prepare("UPDATE memberPhotography SET Website = ?, Social = ?, NoticeBoard = ?, FilmTraining = ?, ProPhoto = ? WHERE MemberID = ?");
  $update->execute([
    (int) $website,
    (int) $social,
    (int) $noticeboard,
    (int) $proPhoto,
    (int) $filmTraining,
    $member->getId(),
  ]);
} else {
  // INSERT
  $insert = $db->prepare("INSERT INTO memberPhotography (Website, Social, NoticeBoard, FilmTraining, ProPhoto, MemberID) VALUES (?, ?, ?, ?, ?, ?)");
  $insert->execute([
    (int) $website,
    (int) $social,
    (int) $noticeboard,
    (int) $proPhoto,
    (int) $filmTraining,
    $member->getId(),
  ]);
}

$onboardingMember->completeTask('photography_consent');

http_response_code(302);
header("Location: " . autoUrl('onboarding/go/start-task'));