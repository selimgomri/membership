<!DOCTYPE html>
<html>
<head>
<title>BCrypt Generator for Romita Hunt</title>
</head>
<body>
<?php
$password = "";
if (isset($_POST['password'])) {
	echo "<p>" . password_hash($_POST['password'], PASSWORD_BCRYPT) . "</p>";
	?>
	<p>Copy the Hash above and email it back</p>
	<?php
}
else {
	?>
	<form method="post">
		<label for="password">Enter Password Here</label><br>
		<input type="password" name="password" id="password">
		<button type="submit">Submit</button>
	</form>
	Pressing submit will return a unique BCrypt Hash. Copy the hash and email it back.
	<?php
}
?>
</body>
</html>
