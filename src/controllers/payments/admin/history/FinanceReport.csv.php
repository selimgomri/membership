<?php

require BASE_PATH . 'controllers/payments/GoCardlessSetup.php';

/**
 * Exports of monthly financial reports
 * Shows payments by group and outgoings (GoCardless fees on payouts)
 * 
 * This means the system covers gross and net
 * 
 * TODO: Extend to cover Stripe card payments
 */

global $db;
$searchDate = $year . "-" . $month . "-" . "%";
$getPayments = $db->prepare("SELECT * FROM `payments` INNER JOIN `users` ON users.UserID = payments.UserID WHERE `Date` LIKE ?");
$getPayments->execute([$searchDate]);

// output headers so that the file is downloaded rather than displayed
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=SCDSMembership-FinanceReport.csv');

// create a file pointer connected to the output stream
$output = fopen('php://output', 'w');

// output the column headings
fputcsv($output, [env('CLUB_NAME') . ' Finance Report']);
while ($row = $getPayments->fetch(PDO::FETCH_ASSOC)) {
  fputcsv($output, $row);
}