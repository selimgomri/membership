<?php

require 'info.json.php';

$data = json_decode($output);

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=SCDSMembership-GalaEntriesSquadRepReport.csv');

// create a file pointer connected to the output stream
$output = fopen('php://output', 'w');

fputcsv($output, [env('CLUB_NAME') . ' Squad Rep Gala Entry Report']);
fputcsv($output, ['Swimmer', 'Row Type', '50 Free', '100 Free', '200 Free', '400 Free', '800 Free', '1500 Free', '50 Back', '100 Back', '200 Back', '50 Breast', '100 Breast', '200 Breast', '50 Fly', '100 Fly', '200 Fly', '100 IM', '200 IM', '400 IM', '150IM']);

foreach ($data->entries as $entry) {
  $swimmerRow = $swimmerTimeRow = [];
  $swimmerRow[] = $swimmerTimeRow[] = $entry->forename . ' ' . $entry->surname;
  $swimmerRow[] = 'Selected swims';
  //$swimmerTimeRow[] = '';
  $swimmerTimeRow[] = 'Entry times';
  foreach ($entry->events as $event) {
    if ($event->selected) {
      $swimmerRow[] = '✓';
    } else {
      $swimmerRow[] = '';
    }
    $swimmerTimeRow[] = $event->entry_time;
  }
  fputcsv($output, $swimmerRow);
  fputcsv($output, $swimmerTimeRow);
}