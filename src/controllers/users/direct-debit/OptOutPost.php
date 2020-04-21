<?php

try {

  if (!\SCDS\FormIdempotency::verify() || !\SCDS\CSRF::verify()) {
    $_SESSION['ErrorInvalidRequest'] = true;
  } else {
    try {
      // Get renewal
      $db = app()->db;
      
      include 'GetRenewal.php';

      $progress = $db->prepare("UPDATE `renewalProgress` SET `Stage` = `Stage` + 1 WHERE `RenewalID` = ? AND `UserID` = ?");
      $progress->execute([
        $renewal,
        $person
      ]);

      if ($reg) {
        $sql = "UPDATE `users` SET `RR` = 0 WHERE `UserID` = ?";
        $query = $db->prepare($sql);
        $query->execute([$person]);

        $query = $db->prepare("UPDATE `members` SET `RR` = 0, `RRTransfer` = 0 WHERE `UserID` = ?");
        $query->execute([$person]);

        // Remove from status tracker
        $delete = $db->prepare("DELETE FROM renewalProgress WHERE UserID = ? AND RenewalID = ?");
        $delete->execute([
          $person,
          $renewal
        ]);
      }
      $_SESSION['Successful'] = true;
    } catch (Exception $e) {
      // Catches halt
      $_SESSION['ErrorNoReg'] = true;
    }
  }
} catch (Excption $e) {

} finally {
  header("Location: " . autoUrl("users/" . $person . "/authorise-direct-debit-opt-out"));
}