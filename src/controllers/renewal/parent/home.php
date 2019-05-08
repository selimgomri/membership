<?php

global $db;

$renewals = $db->query("SELECT * FROM `renewals` WHERE `StartDate` <= CURDATE() AND CURDATE() <= `EndDate`");
$row = $renewals->fetch(PDO::FETCH_ASSOC);

$year = date("Y", strtotime('+1 year'));

$pagetitle = $year . " Membership Renewal";
include BASE_PATH . "views/header.php";
include BASE_PATH . "views/renewalTitleBar.php";
?>

<div class="container">
	<?php if ($row != null) { ?>
		<h1>
      Membership Renewal for <?=htmlspecialchars($row['Year'])?>
    </h1>
		<p class="lead">
      Welcome to the Membership Renewal System for
      <?=htmlspecialchars($row['Year'])?>
    </p>
		<p>
      Membership renewal ensures all our information about you is up to date,
      that you and your swimmers understand your rights and responsibilities at
      the club, and that you can pay your <abbr
      title="Including Swim England Membership Fees"> membership fee</abbr> for
      the year ahead.
		</p>
		<p>
			The Membership Renewal Period is open until <?=date("l j F Y",
			strtotime($row['EndDate']))?>.
    </p>
		<p>
      This year, we'll be charging you for your Membership Fees through your
      direct debit. This will be as an add-on to your usual fees.
		</p>
		<p>
      We'll save your progress as you fill out the required forms.
    </p>
		<p>
			<a class="btn btn-success" href="<?=autoUrl("renewal/go")?>">
        Get Started
      </a>
		</p>
	<?php } else { ?>
		<h1>
      Membership Renewal for <?=htmlspecialchars($year)?>
    </h1>
		<p class="lead">
      Welcome to the Membership Renewal System
    </p>
		<p>
      Membership renewal ensures all our information about you is up to date,
			that you and your swimmers understand your rights and responsibilities at
			the club, and that you can pay your <abbr
      title="Including Swim England Membership Fees"> membership fee</abbr> for
      the year ahead.
		</p>
		<div class="alert alert-danger">
      <p class="mb-0">
			  <strong>The membership renewal period for <?=htmlspecialchars($year)?>
			  has not yet started</strong>
      </p>
      <p class="mb-0">
        We'll let you know when this starts
      </p>
		</div>
	<?php } ?>
</div>

<?php include BASE_PATH . "views/footer.php";
