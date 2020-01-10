<?php

/**
 * getAccountBalance gets the account balance of a user.
 * An account balance consists of unpaid fees
 *
 * @param int $user the id of the user
 * @return int account balance
 */
function getAccountBalance($user) {
  global $db;

  $getBalance = $db->prepare("SELECT SUM(Amount) FROM paymentsPending WHERE UserID = ? AND (`Status` = 'Pending' OR `Status` = 'Queued' OR `Status` = 'Requested') AND `Type` = ?");
  $getBalance->execute([
    $_SESSION['UserID'],
    'Payment'
  ]);
  $balance = $getBalance->fetchColumn();

  $getBalance->execute([
    $_SESSION['UserID'],
    'Refund'
  ]);
  $credits = $getBalance->fetchColumn();
  
  return ($balance - $credits);
}