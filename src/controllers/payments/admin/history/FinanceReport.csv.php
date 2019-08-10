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
$getPayments = $db->prepare("SELECT * FROM (SELECT 'Payments' AS Type, paymentsPending.Amount, paymentsPending.Type AS DebitCredit, CONCAT(users.Forename, ' ', users.Surname) AS `User`, paymentsPending.Name, MetadataJSON AS Info, payments.Status AS `Status`, paymentsPending.Date AS `Date`, NULL AS `Fees` FROM (((`paymentsPending` INNER JOIN `payments` ON paymentsPending.PMkey = payments.PMkey) INNER JOIN `users` ON users.UserID = payments.UserID) LEFT JOIN paymentsPayouts ON paymentsPayouts.ID = payments.Payout) WHERE paymentsPayouts.ArrivalDate LIKE :searchDate UNION ALL SELECT 'Payouts' AS Type, Amount, NULL AS DebitCredit, NULL AS `User`, NULL AS `Name`, NULL AS Info, NULL AS `Status`, ArrivalDate AS `Date`, Fees FROM paymentsPayouts WHERE paymentsPayouts.ArrivalDate LIKE :searchDate) AS UnitedTable ORDER BY UnitedTable.Date ASC, UnitedTable.User ASC");
$getPayments->execute(['searchDate' => $searchDate]);

// pre($getPayments->fetchAll(PDO::FETCH_ASSOC)); /*

// output headers so that the file is downloaded rather than displayed
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=SCDSMembership-FinanceReport.csv');

// create a file pointer connected to the output stream
$output = fopen('php://output', 'w');

// output the column headings
fputcsv($output, [env('CLUB_NAME') . ' Finance Report']);
fputcsv($output, ['Date', 'Details', 'In', 'Out', 'Gross/Net', 'Status']);
while ($row = $getPayments->fetch(PDO::FETCH_ASSOC)) {
  if ($row['Type'] == 'Payments') {
    $date = new DateTime($row['Date']);
    $in = $out = 0;
    if ($row['DebitCredit'] == 'Payment') {
      $in = $row['Amount'];
    } else if ($row['DebitCredit'] == 'Refund') {
      $out = $row['Amount'];
    }
    $json = json_decode($row['Info']);
    $name = $row['Name'];
    $infoText = '';
    if (isset($json->PaymentType) && $json->PaymentType == 'SquadFees') {
      foreach ($json->Members as $member) {
        $infoText .= '£' . $member->Fee . ' (' . $member->FeeName . ', ' . $member->MemberName . ') ';
      }
    }
    $details = $row['User'] . ', ' . $name . ' ' . $infoText;
    $status = paymentStatusString($row['Status']);
    fputcsv($output, [
      $date->format("m/d/Y"),
      $details,
      number_format($in/100, 2, '.', ''),
      number_format($out/100, 2, '.', ''),
      'Gross',
      $status
    ]);
  } else if ($row['Type'] == 'Payouts') {
    $dateString = '';
    $status = 'Pending';
    if ($row['Date'] != null) {
      $date = new DateTime($row['Date']);
      $dateString = $date->format("m/d/Y");
      $status = 'Paid out';
    }
    $details = "GoCardless bank payout";
    $in = $row['Amount'];
    $out = $row['Fees'];
    fputcsv($output, [
      $dateString,
      $details . ' before fees',
      number_format($in/100, 2, '.', ''),
      '',
      'Gross',
      $status
    ]);
    fputcsv($output, [
      $dateString,
      $details . ' - Fees',
      '',
      number_format($out/100, 2, '.', ''),
      'Net',
      $status
    ]);
    fputcsv($output, [
      $dateString,
      $details . ' - Total banked',
      number_format(($in-$out)/100, 2, '.', ''),
      '',
      'Net',
      $status
    ]);
  }
  //fputcsv($output, $row);
}