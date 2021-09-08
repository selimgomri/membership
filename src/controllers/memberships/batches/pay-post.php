<?php

$user = app()->user;
$db = app()->db;

if (!isset($_POST['pay-method'])) {
  halt(404);
}

try {
  $batchPay = \SCDS\Memberships\Batch::goToCheckout($id, $_POST['pay-method']);

  if ($batchPay->type == 'checkout') {
    $checkoutSession = $batchPay->checkoutSession;

    $checkoutSession->metadata['return']['url'] = autoUrl('memberships');
    $checkoutSession->metadata['return']['instant'] = false;
    $checkoutSession->metadata['return']['buttonString'] = 'Return to batch information page';

    $checkoutSession->metadata['cancel']['url'] = autoUrl('memberships/batches/' . $id);

    $checkoutSession->save();

    http_response_code(302);
    header("Location: " . $checkoutSession->getUrl());
  } else if ($batchPay->type == 'dd') {

    http_response_code(302);
    header("Location: " . autoUrl('memberships/batches/' . $id));
  }
} catch (Exception $e) {
  halt(404);
}