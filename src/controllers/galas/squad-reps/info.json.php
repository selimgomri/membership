<?php

global $db;

$squad = null;

if (isset($_GET['squad'])) {
  // Verify this squad is allowed for the user

  $squad = (int) $_GET['squad'];
}

if ($squad == null || $squad == 0) {
  halt(404);
}

$getGala = $db->prepare("SELECT GalaName `name`, GalaFee fee, GalaVenue venue, GalaFeeConstant fixed FROM galas WHERE GalaID = ?");
$getGala->execute([$id]);
$gala = $getGala->fetch(PDO::FETCH_ASSOC);

$getSquad = $db->prepare("SELECT SquadID id, SquadName `name` FROM squads WHERE SquadID = ?");
$getSquad->execute([
  $squad
]);
$squadInfo = $getSquad->fetch(PDO::FETCH_ASSOC);

if ($gala == null || ($squadInfo == null && !$doNotHalt)) {
  halt(404);
  $noSquad = true;
}

$getEntries = $db->prepare("SELECT members.UserID `user`, DateOfBirth, GalaDate, 50Free, 100Free, 200Free, 400Free, 800Free, 1500Free, 50Back, 100Back, 200Back, 50Breast, 100Breast, 200Breast, 50Fly, 100Fly, 200Fly, 100IM, 150IM, 200IM, 400IM, 50FreeTime, 100FreeTime, 200FreeTime, 400FreeTime, 800FreeTime, 1500FreeTime, 50BackTime, 100BackTime, 200BackTime, 50BreastTime, 100BreastTime, 200BreastTime, 50FlyTime, 100FlyTime, 200FlyTime, 100IMTime, 150IMTime, 200IMTime, 400IMTime, MForename, MSurname, EntryID, Charged, FeeToPay, MandateID, EntryProcessed Processed, Refunded, galaEntries.AmountRefunded, Intent, stripePayMethods.Brand, stripePayMethods.Last4, Funding, ASANumber FROM ((((((galaEntries INNER JOIN members ON galaEntries.MemberID = members.MemberID) INNER JOIN galas ON galaEntries.GalaID = galas.GalaID) LEFT JOIN users ON members.UserID = users.UserID) LEFT JOIN paymentPreferredMandate ON users.UserID = paymentPreferredMandate.UserID) LEFT JOIN stripePayments ON galaEntries.StripePayment = stripePayments.ID) LEFT JOIN stripePayMethods ON stripePayMethods.ID = stripePayments.Method) WHERE galaEntries.GalaID = ? AND members.SquadID = ? ORDER BY MForename ASC, MSurname ASC");
$getEntries->execute([$id, $squad]);

$swimsArray = [
  '50Free' => '50 Free',
  '100Free' => '100 Free',
  '200Free' => '200 Free',
  '400Free' => '400 Free',
  '800Free' => '800 Free',
  '1500Free' => '1500 Free',
  '50Back' => '50 Back',
  '100Back' => '100 Back',
  '200Back' => '200 Back',
  '50Breast' => '50 Breast',
  '100Breast' => '100 Breast',
  '200Breast' => '200 Breast',
  '50Fly' => '50 Fly',
  '100Fly' => '100 Fly',
  '200Fly' => '200 Fly',
  '100IM' => '100 IM',
  '150IM' => '150 IM',
  '200IM' => '200 IM',
  '400IM' => '400 IM'
];

$entries = [];
while ($entry = $getEntries->fetch(PDO::FETCH_ASSOC)) {
  $birthday = new DateTime($entry['DateOfBirth'], new DateTimeZone('Europe/London'));
  $today = new DateTime('now', new DateTimeZone('Europe/London'));
  $lastDay = new DateTime($entry['GalaDate'], new DateTimeZone('Europe/London'));
  $endOfYear = new DateTime('now', new DateTimeZone('Europe/London'));

  $ageToday = $birthday->diff($today)->format('%y');
  $ageLastDay = $birthday->diff($lastDay)->format('%y');
  $ageEndOfYear = $birthday->diff($endOfYear)->format('%y');

  $events = [];
  foreach ($swimsArray as $key => $value) {
    $entryTime = null;
    if (bool($entry[$key])) {
      $entryTime = $entry[$key . 'Time'];
    }
    $events[] = [
      'event' => $key,
      'selected' => bool($entry[$key]),
      'name' => $value,
      'entry_time' => $entryTime
    ];
  }
  $entries[] = [
    'id' => (int) $entry['EntryID'],
    'user' => (int) $entry['user'],
    'forename' => $entry['MForename'],
    'surname' => $entry['MSurname'],
    'asa_number' => $entry['ASANumber'],
    'age_today' => $ageToday,
    'age_on_last_day' => $ageLastDay,
    'age_at_end_of_year' => $ageEndOfYear,
    'events' => $events,
    'charged' => bool($entry['Charged']),
    'amount_charged' => (int) ($entry['FeeToPay']*100),
    'refunded' => bool($entry['Refunded']),
    'amount_refunded' => (int) $entry['AmountRefunded'],
    'payment_intent' => [
      'id' => $entry['Intent'],
      'brand' => $entry['Brand'],
      'last4' => $entry['Last4'],
      'funding' => $entry['Funding']
    ]
  ];
}

$jsonArray = [
  'gala' => [
    'id' => (int) $id,
    'name' => $gala['name'],
    'venue' => $gala['venue'],
    'fixed_fee' => bool($gala['fixed']),
    'fee' => (int) ($gala['fee']*100)
  ],
  'squad' => [
    'id' => (int) $squadInfo['id'],
    'name' => $squadInfo['name']
  ],
  'entries' => $entries
];

$output = json_encode($jsonArray);