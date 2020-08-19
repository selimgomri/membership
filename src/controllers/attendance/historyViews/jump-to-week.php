<?php

try {
  $date = new DateTime($_POST['go-to-week-date'], new DateTimeZone('Europe/London'));

  http_response_code(302);
  header("location: " . autoUrl('attendance/history/squads/' . $id . '?year=' . $date->format('o') . '&week=' . $date->format('W')));
} catch (Exception $e) {
  http_response_code(302);
  header("location: " . autoUrl('attendance/history/squads/' . $id));
}