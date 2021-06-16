<?php

$pagetitle = "Membership Centre";
include BASE_PATH . "views/header.php";

?>

<div class="bg-light mt-n3 py-3 mb-3">
	<div class="container">

		<!-- Page header -->
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item active" aria-current="page">Memberships</li>
			</ol>
		</nav>

		<div class="row align-items-center">
			<div class="col-lg-8">
				<h1>
					Membership Centre
				</h1>
				<p class="lead mb-0">
					Welcome to the membership centre
				</p>
			</div>
		</div>
	</div>
</div>

<div class="container">

  <div class="row">
    <div class="col-lg-8">
      <p>
        The Membership Centre lets clubs track which memberships their members hold in a given year.
      </p>
    </div>
  </div>

</div>

<?php

$footer = new \SCDS\Footer();
$footer->render();
