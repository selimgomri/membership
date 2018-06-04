<?php
$pagetitle = "Attendance";
$title = "Squad Attendance";
$content = "<p class=\"lead\">View attendance records and fill out registers for squads</p>";

$content .= '
<div class="my-3 p-3 bg-white rounded box-shadow">
	<h2 class="border-bottom border-gray pb-2 mb-0">Attendance Quick $links</h2>
	<div class="media text-muted pt-3">
		<p class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
			<a href="' . autoUrl("attendance/register") . '"><strong class="d-block text-gray-dark">Take a Register</strong></a>
			Quickly take a register for any squad
		</p>
	</div>
	<div class="media text-muted pt-3">
		<p class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
			<a href="' . autoUrl("attendance/history/swimmers/") . '"><strong class="d-block text-gray-dark">Swimmer Attendance</strong></a>
			View swimmer attendance records for up to the last twenty weeks
		</p>
	</div>
	<div class="media text-muted pt-3">
		<p class="media-body pb-3 mb-0 lh-125 border-bottom border-gray text-muted">
			<!--<a href="' . autoUrl("attendance/history/squads/") . '">--><strong class="d-block text-gray-dark">Squad Attendance</strong><!--</a>-->
			View squad attendance records for up to the last twenty weeks (Coming Soon)
		</p>
	</div>
	<span class="d-block text-right mt-3">
		We\'ll be introducing more functionality as soon as we can
	</span>
</div>
';
