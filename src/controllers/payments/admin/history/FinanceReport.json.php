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
//header('Content-Disposition: attachment; filename=SCDSMembership-FinanceReport.csv');

$now = new DateTime('now', new DateTimeZone('UTC'));

$array = [];

// output the column headings
//$array += ['about' => env('CLUB_NAME') . ' Finance Report'];
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
    $item = [
      'object' => 'Payment',
      'type' => $row['DebitCredit'],
      'date' => $date->format("Y-m-d"),
      'details' => $details,
      'credits' => (int) $in,
      'debits' => (int) $out,
      'income' => 'Gross',
      'status' => $status
    ];
    $array[] = $item;
  } else if ($row['Type'] == 'Payouts') {
    $dateString = null;
    $status = 'Pending';
    if ($row['Date'] != null) {
      $date = new DateTime($row['Date']);
      $dateString = $date->format("m/d/Y");
      $status = 'Paid out';
    }
    $details = "GoCardless bank payout";
    $in = $row['Amount'];
    $out = $row['Fees'];
    $item = [
      'object' => 'Payout',
      'date' => $dateString,
      'details' => $details . ' before fees',
      'credits' => (int) $in,
      'debits' => 0,
      'income' => 'Gross',
      'status' => $status
    ];
    $array[] = $item;
    $item = [
      'object' => 'Payout',
      'date' => $dateString,
      'details' => $details . ' - Fees',
      'credits' => 0,
      'debits' => (int) $out,
      'income' => 'Net',
      'status' => $status
    ];
    $array[] = $item;
    $item = [
      'object' => 'Payout',
      'date' => $dateString,
      'details' => $details . ' - Total banked',
      'credits' => ((int) $in) - ((int) $out),
      'debits' => 0,
      'income' => 'Gross',
      'status' => $status
    ];
    $array[] = $item;
  }
  //fputcsv($output, $row);
}

$output = [
  'about' => env('CLUB_NAME') . ' Finance Report',
  'producer' => 'Swimming Club Data Systems - Membership Software',
  'month' => $month,
  'year' => $year,
  'date_produced' => $now->format("Y-m-d"),
  'items' => $array,
];