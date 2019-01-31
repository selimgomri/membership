<?php

ignore_user_abort(true);
set_time_limit(0);

require BASE_PATH . 'controllers/payments/GoCardlessSetup.php';

global $db;

$query = $db->prepare("SELECT PMKey, UserID FROM paymentRetries WHERE Day >= CURDATE() AND Tried = ? LIMIT 4");
$query->execute([false]);

$mark_tried = $db->prepare("UPDATE paymentRetries SET Tried = ? WHERE PMKey = ?");
$email = $db->prepare("INSERT INTO notify (UserID, Status, Subject, Message, ForceSend, EmailType) VALUES (?, ?, ?, ?, ?, ?)");

$retries = $query->fetchAll();

foreach ($retries as $retry) {
  try {
    $client->payments()->retry($retry['PMKey']);
    $mark_tried->execute([true, $retry['PMKey']]);

    $subject = 'Payment Retry (' . $retry['PMKey'] . ')';
    $message = '
    <p>We\'re now retrying payment ' . $retry['PMKey'] . '. The money should leave your account within the next few days, if enough money is available.</p>';
    $message .= '<p>Kind regards,<br>The ' . CLUB_NAME . ' Team</p>';
    $email->execute([$retry['UserID'], 'Queued', $subject, $message, 1, 'Payments']);

  } catch (Exception $e) {
    echo "ERRORS";
    $mark_tried->execute([true, $retry['PMKey']]);

    $subject = 'Payment Retry Failed';
    $message = '
    <p>We attempted to retry payment ' . $retry['PMKey'] . ' but an error occured before we were able to complete the retry request.</p>
    <p>Please contact the treasurer as soon as possible.</p>';
    $message .= '<p>Kind regards,<br>The ' . CLUB_NAME . ' Team</p>';
    $email->execute([$retry['UserID'], 'Queued', $subject, $message, 1, 'Payments']);
  }
}
