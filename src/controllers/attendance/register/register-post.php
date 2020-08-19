<?php

use GuzzleHttp\Client;

$db = app()->db;
$tenant = app()->tenant;

try {
	
	// Validate user has authority
	$getMemberTenant = $db->prepare("SELECT Tenant FROM members WHERE MemberID = ?");
	$getMemberTenant->execute([
		(int) $_POST['memberId'],
	]);
	$memberTenant = $getMemberTenant->fetchColumn();

	if (!$memberTenant || $memberTenant != $tenant->getId()) {
		// No member
		throw new Exception('No such member');
	}

	// Update record (if exists)
	$update = $db->prepare("UPDATE sessionsAttendance SET AttendanceBoolean = ? WHERE WeekID = ? AND SessionID = ? AND MemberID = ?");
	$update->execute([
		(int) ($_POST['state'] == 'true'),
		(int) $_POST['weekId'],
		(int) $_POST['sessionId'],
		(int) $_POST['memberId'],
	]);

  // // Update data in db
  // $setSignedOut = $db->prepare("UPDATE covidVisitors SET SignedOut = ? WHERE ID = ?");
  // $setSignedOut->execute([
  //   (int) bool($_POST['state']),
  //   $_POST['id'],
  // ]);

  // // Prep JSON body for socket service
  $data = [
    'room' => 'register_room:' . 'week-' . $_POST['weekId'] . '-session-' . $_POST['sessionId'],
    'field' => $_POST['memberId'],
    'state' => (int) ($_POST['state'] == 'true'),
  ];

  $url = 'https://production-apis.tenant-services.membership.myswimmingclub.uk/attendance/send-register-change-message';
  if (bool(getenv("IS_DEV"))) {
    $url = 'https://apis.tenant-services.membership.myswimmingclub.uk/attendance/send-register-change-message';
  }

  http_response_code(200);

  try {

    $client = new Client();

    $r = $client->request('POST', $url, [
      'json' => $data
    ]);

  } catch (Exception $e) {
    // Ignore
  }
} catch (Exception $e) {

  http_response_code(500);

}
