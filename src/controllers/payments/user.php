<?php

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/paymentsMenu.php";

require 'GoCardlessSetup.php';

//$customers = $client->customers()->list()->records;
//print_r($customers);

/*$user = $_SESSION['UserID'];

$sql = "SELECT `Forename`, `Surname`, `EmailAddress` FROM `users` WHERE `UserID` = $user ;";
$result = mysqli_query($link, $sql);
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);

$redirectFlow = $client->redirectFlows()->create([
    "params" => [
        // This will be shown on the payment pages
        "description" => "Club fees",
        // Not the access token
        "session_token" => "dummy_session_token",
        "success_redirect_url" => "https://developer.gocardless.com/example-redirect-uri/",
        // Optionally, prefill customer details on the payment page
        "prefilled_customer" => [
          "given_name" => $row['Forename'],
          "family_name" => $row['Surname'],
          "email" => $row['EmailAddress']
        ]
    ]
]);

// Hold on to this ID - you'll need it when you
// "confirm" the redirect flow later
print("ID: " . $redirectFlow->id . "<br />");

print("URL: " . $redirectFlow->redirect_url);
*/

 ?>

<div class="container">
	<h1>Payments</h1>
	<p class="lead">Here you can control your Direct Debit details and see your payment history</p>
	<h2>Billing Account Options</h2>
	<a href="<? echo autoUrl("payments/setup/0"); ?>" class="btn btn-dark">Add Bank Account</a>
	<a href="<? echo autoUrl("payments/banks"); ?>" class="btn btn-dark">Switch Bank Account</a>
	<h2>Billing History</h2>
	<div class="table-responsive">
		<table class="table table-striped">
			<thead>
				<tr>
					<th>ID</th>
					<th>Date</th>
					<th>Amount</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>1</td>
					<td>1 June 2018</td>
					<td>&pound;175.00</td>
				</tr>
				<tr>
					<td>1</td>
					<td>1 June 2018</td>
					<td>&pound;175.00</td>
				</tr>
				<tr>
					<td>1</td>
					<td>1 June 2018</td>
					<td>&pound;175.00</td>
				</tr>
				<tr>
					<td>1</td>
					<td>1 June 2018</td>
					<td>&pound;175.00</td>
				</tr>
				<tr>
					<td>1</td>
					<td>1 June 2018</td>
					<td>&pound;175.00</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>

<?php include BASE_PATH . "views/footer.php";
