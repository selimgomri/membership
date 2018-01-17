<?php

$pagetitle = "Galas";
$title = "Galas";
$content = "<p class=\"lead\">The gala homepage gives you information about your gala entries for upcoming galas and other galas which are available to enter.</p>";
$content .= "<h2>Galas you can enter</h2>";
$content .= upcomingGalas($link);
$content .= "<p><a class=\"btn btn-light\" href=\"entergala\">Enter a gala</a> <a class=\"btn btn-light\" disabled href=\"entergala-mt\">Enter a gala with Manual Times</a> </p>";
$content .= "<h2>Galas you've entered</h2>";
$content .= enteredGalas($link, $userID);
?>
