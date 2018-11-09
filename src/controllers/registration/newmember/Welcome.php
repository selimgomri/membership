<?php

if (!user_needs_registration($_SESSION['UserID'])) {
  halt(404);
}

$pagetitle = "Welcome to the Club";

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/renewalTitleBar.php";

?>

<div class="container">
  <div class="my-3 p-3 bg-white rounded shadow">
    <h1 class="border-bottom border-gray pb-2 mb-2">
      Welcome to <?=CLUB_NAME?>
    </h1>
    <p class="lead">
      We just need to run through the registration process with you for your new
      swimmer. We'll ask you to fill out medical form, agree to the Club Code of
      Conduct, and Terms and Conditions of Membership before paying any required
      registration fees, which consist of Club and Swim England Fees.
    </p>

		<p>
			Failure to complete registration before the end of the calendar month in
			which you join the club may result in suspension of your new swimmer. This
			is because your Swim England Membership Fee covers insurance for your
			swimmers while taking part in the sport.
		</p>

		<p class="mb-0">
			<a href="<? echo autoUrl("renewal/go"); ?>" class="btn btn-success">
				Start Registration
			</a>
		</p>

  </div>
</div>

<?php

include BASE_PATH . "views/footer.php";

?>
