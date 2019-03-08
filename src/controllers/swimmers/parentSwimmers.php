<?php

$pagetitle = "My Swimmers";
$userID = $_SESSION['UserID'];

include BASE_PATH . "views/header.php";

?>

<div class="container">
	<h1>My Swimmers</h1>
	<p class="lead">My Swimmers shows you all of your registered swimmers and allows you to easily change their details.</p>
	<p>Please remember that it is your responsibility to also keep the ASA Membership System up to date with personal details.</p>

	<?php echo mySwimmersTable($link, $userID); ?>

	<p><a href="<?php echo autoUrl('myaccount/addswimmer'); ?>" class="btn btn-success">Add a Swimmer</a></p>
</div>

<?php include BASE_PATH . "views/footer.php";
