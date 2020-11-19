<?php

$db = app()->db;
$tenant = app()->tenant;

$avoidDirectDebit = app()->tenant->getBooleanKey('ALLOW_DIRECT_DEBIT_OPT_OUT') && isset($_POST['avoid-dd']) && bool($_POST['avoid-dd']);

$location = autoUrl("");

if ($avoidDirectDebit) {
	try {

    $db->beginTransaction();
    
    $nextStage = $db->prepare("UPDATE `renewalProgress` SET `Stage` = `Stage` + 1, `Substage` = 0 WHERE `RenewalID` = ? AND `UserID` = ?");
    $nextStage->execute([
      $renewal,
      $_SESSION['TENANT-' . app()->tenant->getId()]['UserID']
    ]);

		$db->commit();
		$location = autoUrl("renewal/go");

	} catch (Exception $e) {

		$location = autoUrl("renewal/go");

		$db->rollBack();

	}
} else {
	$location = autoUrl("renewal/payments/setup");
}

http_response_code(302);
header("Location: " . $location);