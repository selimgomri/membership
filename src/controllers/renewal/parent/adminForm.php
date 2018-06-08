<?php
$pagetitle = "Administration Form";
include BASE_PATH . "views/header.php";
?>

<div class="container">
	<h1>Club Administration Form</h1>
	<p class="lead">Agree to some stuff</p>

	<ul>
		<li>Data Protection
			<ul>
				<li>Parent signs and Agrees</li>
				<li>Checkbox, name and date automatic</li>
			</ul>
		</li>
		<li>Terms and Conditions of Club Membership
			<ul>
				<li><em>Member</em> agrees to terms</li>
				<li>If member is under 12, parent signs also</li>
			</ul>
		</li>
		<li>Photography Permission
			<ul>
				<li>Photos for website</li>
				<li>Photos for social media</li>
				<li>Photos for club noticeboard</li>
				<li>Filming for training purposes</li>
				<li>Pro photographer in;
					<ul>
						<li>Competitions</li>
						<li>Galas</li>
						<li>Meet</li>
						<li>Club Events</li>
					</ul>
				</li>
				<li><em>Check all the above are desired</em></li>
			</ul>
		</li>
		<li>Medical Consent
			<ul>
				<li>For members under 18</li>
				<li>Confirm a doctor hasn't said don't do sport</li>
			</ul>
		</li>
	</ul>

	<div class="mb-3">
		<a class="btn btn-outline-success" href="">Save</a>
		<a class="btn btn-success" href="">Save and Continue</a>
	</div>
</div>

<?php include BASE_PATH . "views/footer.php";
