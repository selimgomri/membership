<?php

$day = date("w");
$time = date("H:i:s");
$time30 = date("H:i:s", strtotime("-30 minutes"));

$sql = "SELECT SessionID, squads.SquadID, SessionName, SquadName, VenueName, StartTime, EndTime FROM ((`sessions` INNER JOIN squads ON squads.SquadID = sessions.SquadID) INNER JOIN sessionsVenues ON sessions.VenueID = sessionsVenues.VenueID) WHERE SessionDay = :day AND StartTime <= :timenow AND (EndTime > :timenow OR EndTime > :time30) AND DisplayFrom <= CURDATE() AND DisplayUntil >= CURDATE() ORDER BY SquadFee DESC, SquadName ASC";
global $db;

$query = $db->prepare($sql);
$query->execute(['day' => $day, 'timenow' => $time, 'time30' => $time30]);
$sessions = $query->fetchAll(PDO::FETCH_ASSOC);

$pagetitle = "Attendance";
include BASE_PATH . "views/header.php";
include "attendanceMenu.php"; ?>
<div class="front-page" style="margin-bottom: -1rem;">
  <div class="container">

		<h1>Squad Attendance</h1>
		<p class="lead mb-4">View attendance records and fill out registers for squads</p>

    <?php if (sizeof($sessions) > 0) { ?>
      <div class="mb-4">
        <h2 class="mb-4">Current Sessions</h2>
        <div class="mb-4">
          <div class="news-grid">
        <?php for ($i = 0; $i < sizeof($sessions); $i++) { ?>
          <a href="<?=htmlspecialchars(autoUrl("attendance/register?date=" . urlencode($date) . "&squad=" . urlencode($sessions[$i]['SquadID']) . "&session=" . urlencode($sessions[$i]['SessionID'])))?>" title="<?=htmlspecialchars($sessions[$i]['SquadName'])?> Squad Register, <?=htmlspecialchars($sessions[$i]['SessionName'])?>">
            <div>
              <span class="title mb-0">
                Take <?=htmlspecialchars($sessions[$i]['SquadName'])?> Squad Register
              </span>
              <span class="d-flex mb-3">
                <?=date("H:i", strtotime($sessions[$i]['StartTime']))?> - <?=date("H:i", strtotime($sessions[$i]['EndTime']))?>
              </span>
            </div>
            <span class="category">
              <?=htmlspecialchars($sessions[$i]['SessionName'])?>, <?=htmlspecialchars($sessions[$i]['VenueName'])?>
            </span>
          </a>
        <?php } ?>
        </div>
      </div>
    </div>
    <?php } ?>

    <div class="mb-4">
      <?php if (sizeof($sessions) > 0) { ?>
      <h2 class="mb-4">Further Attendance Options</h2>
    <?php } ?>
      <div class="news-grid">
        <a href="<?=autoUrl("attendance/register")?>">
          <div>
            <span class="title mb-0">
              Take a Register
            </span>
            <span class="d-flex mb-3">
              Quickly take a register for any squad
            </span>
          </div>
          <span class="category">
            Attendance
          </span>
        </a>
        <a href="<?=autoUrl("attendance/history/swimmers")?>">
          <div>
            <span class="title mb-0">
              Swimmer Attendance
            </span>
            <span class="d-flex mb-3">
              View swimmer attendance records for up to the last twenty weeks
            </span>
          </div>
          <span class="category">
            Attendance
          </span>
        </a>
        <a href="<?=autoUrl("attendance/history/squads")?>">
          <div>
            <span class="title mb-0">
              Squad Attendance
            </span>
            <span class="d-flex mb-3">
              View squad attendance for the current week
            </span>
          </div>
          <span class="category">
            Attendance
          </span>
        </a>
      </div>
    </div>

  </div>
</div>
<?php $footer = new \SCDS\Footer();
$footer->render();
