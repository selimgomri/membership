<?php

require 'FinanceReport.json.php';

$data = json_encode($output);
$data = json_decode($data);
$items = $data->items;

// output headers so that the file is downloaded rather than displayed
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=SCDSMembership-FinanceReport.csv');

// create a file pointer connected to the output stream
$output = fopen('php://output', 'w');

// output the column headings
fputcsv($output, [env('CLUB_NAME') . ' Finance Report']);
fputcsv($output, ['Date', 'Details', 'In', 'Out', 'Gross/Net', 'Status']);
foreach ($items as $item) {
  fputcsv($output, [
    $item->date,
    $item->details,
    number_format($item->credits/100, 2, '.', ''),
    number_format($item->debits/100, 2, '.', ''),
    $item->income,
    $item->status
  ]);
}