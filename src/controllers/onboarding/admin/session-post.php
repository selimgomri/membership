<?php

// Update details first

$db = app()->db;
$tenant = app()->tenant;

$session = \SCDS\Onboarding\Session::retrieve($id);

if (!\SCDS\CSRF::verify()) {
  halt(403);
}

$states = [
  'not_ready' => 'Not ready',
  'pending' => 'Pending',
  'in_progress' => 'In progress',
  'complete' => 'Complete',
];

http_response_code(302);

try {

  $start = $session->start->format('Y-m-d');
  if (isset($_POST['start-date'])) {
    $postStart = new DateTime($_POST['start-date'], new DateTimeZone('UTC'));
    $start = $postStart->format('Y-m-d');
  }

  $chargeOutstanding = $session->chargeOutstanding;
  if (isset($_POST['charge-fees']) && $_POST['charge-fees'] == '1') {
    $chargeOutstanding = true;
  } else {
    $chargeOutstanding = false;
  }

  $chargeProRata = $session->chargeProRata;
  if ($chargeOutstanding && isset($_POST['charge-pro-rata']) && $_POST['charge-pro-rata'] == '1') {
    $chargeProRata = true;
  } else {
    $chargeProRata = false;
  }

  $welcomeText = null;
  if (isset($_POST['welcome-text']) && mb_strlen(trim($_POST['welcome-text'])) > 0) {
    $welcomeText = trim($_POST['welcome-text']);
  } else {
    $welcomeText = null;
  }

  $status = $session->status;
  $completedAt = $session->completedAt->format('Y-m-d H:i:s');
  if ($status != 'not_ready' && isset($_POST['status']) && in_array($_POST['status'], $states)) {
    $status = $_POST['status'];

    if ($status == 'complete') {
      $completedAt = (new DateTime('now', new DateTimeZone('UTC')))->format('Y-m-d H:i:s');
    }
  }

  $dueDate = $session->dueDate->format('Y-m-d');
  if (isset($_POST['start-date'])) {
    $postDueDate = new DateTime($_POST['due-date'], new DateTimeZone('UTC'));
    $dueDate = $postDueDate->format('Y-m-d');
  }

  $stages = [
    'task_account_details' => [
      'required' => isset($_POST['task-account-details']),
      'completed' => false,
    ],
    'task_model_forms' => [
      'required' => isset($_POST['task-model-forms']),
      'completed' => false,
    ],
    'task_fees' => [
      'required' => isset($_POST['task-fees']),
      'completed' => false,
    ],
  ];

  $updateSession = $db->prepare("UPDATE `onboardingSessions` SET `start` = ?, `charge_outstanding` = ?, `charge_pro_rata` = ?, `welcome_text` = ?, `status` = ?, `due_date` = ?, `completed_at` = ?, `stages` = ? WHERE `id` = ?");
  $updateSession->execute([
    $start,
    (int) $chargeOutstanding,
    (int) $chargeProRata,
    $welcomeText,
    $status,
    $dueDate,
    $completedAt,
    json_encode($stages),
    $id,
  ]);

  // Choose where to go next

  if (isset($_POST['action'])) {
    if ($_POST['action'] == 'fees') {
      // Redirect to batch page
      header('location: ' . autoUrl("onboarding/sessions/a/$id/batch?year=" . $_POST['year']));
    } else if ($_POST['action'] == 'send') {
      // Send email
      $session->sendEmail();
      header('location: ' . autoUrl("onboarding/sessions/a/$id"));
    }
  } else {
    // Just save
  }
} catch (Exception $e) {

  header('location: ' . autoUrl("onboarding/sessions/a/$id"));
}
