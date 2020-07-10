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

$db = app()->db;
$tenant = app()->tenant;

$searchDate = $year . "-" . $month . "-" . "%";
$getPayments = $db->prepare("SELECT * FROM (SELECT 'GoCardless' AS Provider, 'Payments' AS Type, paymentsPending.Amount, paymentsPending.Type AS DebitCredit, CONCAT(users.Forename, ' ', users.Surname) AS `User`, paymentsPending.Name, MetadataJSON AS Info, payments.Status AS `Status`, paymentsPending.Date AS `Date`, NULL AS `Fees` FROM (((`paymentsPending` INNER JOIN `payments` ON paymentsPending.PMkey = payments.PMkey) INNER JOIN `users` ON users.UserID = payments.UserID) LEFT JOIN paymentsPayouts ON paymentsPayouts.ID = payments.Payout) WHERE paymentsPayouts.ArrivalDate LIKE :searchDate AND users.Tenant = :tenant UNION ALL SELECT 'GoCardless' AS Provider, 'Payouts' AS Type, Amount, NULL AS DebitCredit, NULL AS `User`, NULL AS `Name`, NULL AS Info, NULL AS `Status`, ArrivalDate AS `Date`, Fees FROM paymentsPayouts WHERE paymentsPayouts.ArrivalDate LIKE :searchDate AND paymentsPayouts.Tenant = :tenant UNION ALL SELECT 'Stripe' AS Provider, 'Payments' AS Type, stripePaymentItems.Amount, 'Payment' AS DebitCredit, CONCAT(users.Forename, ' ', users.Surname) AS `User`, CONCAT(stripePaymentItems.Name, ' ', stripePaymentItems.Description) AS `Name`, NULL AS Info, NULL AS `Status`, stripePayments.DateTime AS `Date`, stripePaymentItems.AmountRefunded AS `Fees` FROM ((`stripePaymentItems` INNER JOIN `stripePayments` ON stripePaymentItems.Payment = stripePayments.ID) INNER JOIN `users` ON users.UserID = stripePayments.User) WHERE stripePayments.DateTime LIKE :searchDate AND users.Tenant = :tenant UNION ALL SELECT 'Stripe' AS Provider, 'Payouts' AS Type, Amount, NULL AS DebitCredit, NULL AS `User`, ID AS `Name`, NULL AS Info, NULL AS `Status`, ArrivalDate AS `Date`, NULL AS `Fees` FROM stripePayouts WHERE ArrivalDate LIKE :searchDate AND stripePayouts.Tenant = :tenant) AS UnitedTable ORDER BY UnitedTable.Date ASC, UnitedTable.User ASC");
$getPayments->execute([
  'searchDate' => $searchDate,
  'tenant' => $tenant->getId()
]);

// pre($getPayments->fetchAll(PDO::FETCH_ASSOC)); /*

// output headers so that the file is downloaded rather than displayed
//header('Content-Disposition: attachment; filename=SCDSMembership-FinanceReport.csv');

$now = new DateTime('now', new DateTimeZone('UTC'));

$array = [];
$types = [];

// output the column headings
//$array += ['about' => app()->tenant->getKey('CLUB_NAME') . ' Finance Report'];
while ($row = $getPayments->fetch(PDO::FETCH_ASSOC)) {
  if ($row['Type'] == 'Payments') {
    $date = new DateTime($row['Date']);
    $in = $out = 0;
    if ($row['Provider'] == 'GoCardless') {
      if ($row['DebitCredit'] == 'Payment') {
        $in = $row['Amount'];
      } else if ($row['DebitCredit'] == 'Refund') {
        $out = $row['Amount'];
      }
    } else if ($row['Provider'] == 'Stripe' && $row['DebitCredit'] == 'Payment') {
      $in = $row['Amount'];
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
    $status = 'Unknown';
    if ($row['Provider'] == 'GoCardless') {
      $status = paymentStatusString($row['Status']);
    } else if ($row['Provider'] == 'Stripe') {
      $status = 'Check with stripe';
    }
    $grouping = null;
    if (isset($json->type)) {
      $grouping = [
        'object' => $json->type->object,
        'id' => $json->type->id,
        'name' => $json->type->name
      ];
      if (!isset($types[$json->type->object])) {
        $types += [$json->type->object => []];
      }
      if (!isset($types[$json->type->object][$json->type->id])) {
        $types[$json->type->object] += [$json->type->id => $json->type->name];
      }
    }
    $item = [
      'object' => 'Payment',
      'type' => $row['DebitCredit'],
      'date' => $date->format("Y-m-d"),
      'details' => $details,
      'credits' => (int) $in,
      'debits' => (int) $out,
      'income' => 'Gross',
      'status' => $status,
      'grouping' => $grouping
    ];
    $array[] = $item;
  } else if ($row['Type'] == 'Payouts') {
    $dateString = null;
    $status = 'Pending';
    if ($row['Date'] != null) {
      $date = new DateTime($row['Date']);
      $dateString = $date->format("Y-m-d");
      $status = 'Paid out';
    }
    if ($row['Provider'] == 'GoCardless') {
      $details = "GoCardless bank payout";
      $in = $row['Amount'];
      $out = $row['Fees'];
      $item = [
        'object' => 'Payout',
        'date' => $dateString,
        'details' => $details . ' before fees',
        'credits' => ((int) $in) + ((int) $out),
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
        'credits' => (int) $in,
        'debits' => 0,
        'income' => 'Net',
        'status' => $status
      ];
      $array[] = $item;
    } else if ($row['Provider'] == 'Stripe') {
      $item = [
        'object' => 'Payout',
        'date' => $dateString,
        'details' => $row['Name'] . ' - Total banked',
        'credits' => (int) $row['Amount'],
        'debits' => 0,
        'income' => 'Net',
        'status' => 'Check with stripe'
      ];
      $array[] = $item;
    }
  }
  //fputcsv($output, $row);
}

$output = [
  'about' => app()->tenant->getKey('CLUB_NAME') . ' Finance Report',
  'producer' => 'Swimming Club Data Systems - Membership Software',
  'month' => $month,
  'year' => $year,
  'date_produced' => $now->format("Y-m-d"),
  'types' => $types,
  'items' => $array,
];
