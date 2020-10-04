<?php

$db = app()->db;
$tenant = app()->tenant;

header('content-type: application/json');

$json = [
  'status' => 200,
  'html' => null,
  'alerts' => null,
];

// Verify member
$getMember = $db->prepare("SELECT MForename, MSurname FROM members WHERE MemberID = ? AND Tenant = ?");
$getMember->execute([
  $_POST['member'],
  $tenant->getId(),
]);
$member = $getMember->fetch(PDO::FETCH_ASSOC);

if (!$member) {
  halt(404);
}

try {

  $startDate = new DateTime($_POST['from-date'], new DateTimeZone('Europe/London'));
  $endDate = new DateTime($_POST['to-date'], new DateTimeZone('Europe/London'));

  $history = AttendanceHistory::getHistory($_POST['member'], $_POST['from-date'], $_POST['to-date']);
  $data = $history->getData();

  ob_start();
?>

  <div class="card mb-3">
    <div class="card-header">
      Statistics
    </div>
    <div class="card-body">
      <p class="lead mb-3 text-muted">
        Stats for the period <?= htmlspecialchars($startDate->format('j F Y')) ?> to <?= htmlspecialchars($endDate->format('j F Y')) ?>
      </p>

      <dl class="row">
        <dt class="col-sm-5">Sessions Attended</dt>
        <dd class="col-sm-7"><?= htmlspecialchars($history->getTotalAttended()) ?></dd>

        <dt class="col-sm-5">Mandatory Sessions Attended</dt>
        <dd class="col-sm-7"><?= htmlspecialchars($history->getTotalMandatoryAttended()) ?></dd>

        <dt class="col-sm-5">Sessions Excused</dt>
        <dd class="col-sm-7"><?= htmlspecialchars($history->getTotalExcused()) ?></dd>

        <dt class="col-sm-5">Mandatory Sessions Excused</dt>
        <dd class="col-sm-7"><?= htmlspecialchars($history->getTotalMandatoryExcused()) ?></dd>

        <dt class="col-sm-5">Total Sessions</dt>
        <dd class="col-sm-7"><?= htmlspecialchars($history->getTotalSessions()) ?></dd>

        <dt class="col-sm-5">Total Mandatory Sessions</dt>
        <dd class="col-sm-7"><?= htmlspecialchars($history->getTotalMandatorySessions()) ?></dd>

        <dt class="col-sm-5">Attendance Percentage (Mandatory Sessions)</dt>
        <dd class="col-sm-7"><?= htmlspecialchars(number_format($history->getPercentageTotal(), 1)) ?>%</dd>

        <dt class="col-sm-5">Attendance Percentage (Total Sessions)</dt>
        <dd class="col-sm-7"><?= htmlspecialchars(number_format($history->getPercentageMandatory(), 1)) ?>%</dd>
      </dl>

      <p class="mb-0">
        Excused sessions have no impact on percentages.
      </p>
    </div>
  </div>

  <div class="card">

    <div class="card-header">
      Full Details
    </div>

    <?php if (sizeof($data) > 0) { ?>

      <div class="card-body">
        <p class="lead mb-0 text-muted">
          Sessions where registers were taken in the period <?= htmlspecialchars($startDate->format('j F Y')) ?> to <?= htmlspecialchars($endDate->format('j F Y')) ?>
        </p>
      </div>

      <ul class="list-group list-group-flush">
        <?php foreach ($data as $row) { ?>
          <li class="list-group-item">
            <pre class="mb-0"><?= json_encode($row, JSON_PRETTY_PRINT) ?></pre>
          </li>
        <?php } ?>
      </ul>
    <?php } else { ?>

      <div class="card-body">
        <div class="alert alert-warning mb-0">
          <p class="mb-0">
            <strong>No data to display</strong>
          </p>
          <p class="mb-0">
            No attendance records could be returned for the period <?= htmlspecialchars($startDate->format('j F Y')) ?> to <?= htmlspecialchars($endDate->format('j F Y')) ?>. Please try a new search.
          </p>
        </div>
      </div>

    <?php } ?>

  </div>

<?php
  $html = ob_get_clean();

  $json = [
    'status' => 200,
    'html' => $html,
    'alerts' => null,
    'dates' => [
      'start_date' => $startDate,
      'end_date' => $endDate,
    ],
    'data' => $data,
  ];
} catch (Exception $e) {

  reportError($e);

  $json = [
    'status' => 200,
    'html' => null,
    'alerts' => null,
  ];
}

echo json_encode($json);
