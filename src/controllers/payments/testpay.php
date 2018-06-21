<?php

require 'GoCardlessSetup.php';

/*$payment = $client->payments()->create([
  "params" => [
      "amount" => 1000, // 10 GBP in pence
      "currency" => "GBP",
      "links" => [
          "mandate" => "MD0003HV2AECY2"
                       // The mandate ID from last section
      ],
      // Almost all resources in the API let you store custom metadata,
      // which you can retrieve later
  ],
  "headers" => [
      "Idempotency-Key" => "sandbox-random_payment_specific_string-0"
  ]
]);

// Keep hold of this payment ID - we'll use it in a minute
// It should look like "PM000260X9VKF4"
print("ID: " . $payment->id);

echo "success";

$bankAccount = $client->customerBankAccounts()->get("BA0003GA1VRAX6");
print_r($bankAccount); */

$bank = $client->customerBankAccounts()->get("BA0003HKK8YAA9");
print_r($bank);
