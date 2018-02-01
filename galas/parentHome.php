<?php

$pagetitle = "Galas";
$title = "Galas";
$content = "<p class=\"lead\">The gala homepage gives you information about your gala entries for upcoming galas and other galas which are available to enter.</p>";
$content .= "<p>Please remember that this new system is in development, so you must hand in a <a href=\"https://www.chesterlestreetasc.co.uk/wp-content/uploads/2016/06/GalaEntryForm.pdf\" target=\"_blank\">paper Gala Entry Form</a> as well.</p>";
$content .= "<h2>Galas you can enter</h2>";
$content .= upcomingGalas($link);
$content .= "<p><a class=\"btn btn-success\" href=\"entergala\">Enter a gala</a></p>";
$content .= "<h2>Galas you've entered</h2>";
$content .= enteredGalas($link, $userID);
?>
