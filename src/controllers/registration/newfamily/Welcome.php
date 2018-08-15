<?php

$pagetitle = "Welcome";

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/notifyMenu.php";

?>

<div class="container">
  <div class="my-3 p-3 bg-white rounded box-shadow">
    <h1 class="border-bottom border-gray pb-2 mb-2">
      Welcome to Chester-le-Street ASC
    </h1>
    <p class="lead">
      We just need to run through the registration process with you. We'll ask
      you to fill out medical forms for your swimmer(s), agree to the Club Code
      of Conduct, Terms and Conditions of Membership before paying your
      registration fees, which consist of Club and Swim England Fees.
    </p>

		<p>
			Failure to complete registration before the end of the calendar month in
			which you join the club may result in suspension of your swimmer(s).
		</p>

		<p class="mb-0">
			<a href="" class="btn btn-success">
				Start Registration
			</a>
		</p>

  </div>
</div>

<?php

include BASE_PATH . "views/footer.php";

?>
