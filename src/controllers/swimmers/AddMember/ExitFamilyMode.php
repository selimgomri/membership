<?

$family_id = $_SESSION['Swimmers-FamilyMode']['FamilyId'];

unset($_SESSION['Swimmers-FamilyMode']);

$pagetitle = "Select an Option - Add Member";
include BASE_PATH . "views/header.php";
include BASE_PATH . "views/swimmersMenu.php"; ?>

<div class="container">
	<div class="mb-3 p-3 bg-white rounded shadow">
		<h1>Is a parent/guardian with you now?</h1>
		<p>If so, we'll handle their registration here, otherwise we'll produce a
		signup link for them.</p>

		<div class="card-deck">
		  <div class="card">
		    <div class="card-body">
		      <h2 class="card-title">Yes the parent/guardian is here</h2>
		      <p class="card-text">We'll take the parent through signup, medical
		      forms, emergency contacts and direct debit setup.</p>
					<p class="card-text">This will take about ten minutes.</p>
				</div>
		    <div class="card-footer">
		      <a href="<? echo autoUrl("family/register/now/" . $family_id); ?>">
						Continue with parent/guardian
					</a>
		    </div>
		  </div>
		  <div class="card">
		    <div class="card-body">
		      <h2 class="card-title">No the parent/guardian is not here</h2>
		      <p class="card-text">We'll produce a link and QR code for the parent/guardian to use.</p>
		    </div>
		    <div class="card-footer">
					<a disabled href="<? echo autoUrl("family/register/later/" . $family_id); ?>">
		      	Produce a Signup Code Sheet
					</a>
		    </div>
		  </div>
		</div>

	</div>
</div>

<?php include BASE_PATH . "views/footer.php";
