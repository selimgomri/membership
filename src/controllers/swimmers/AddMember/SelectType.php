<?

if (isset($_SESSION['Swimmers-FamilyMode'])) {
	header("Location: " . autoUrl("swimmers/new"));
	die();
}

$pagetitle = "Select an Option - Add Member";
include BASE_PATH . "views/header.php";
include BASE_PATH . "views/swimmersMenu.php"; ?>

<div class="container">
	<div class="">
		<h1>What type of member are you adding?</h1>
		<p>There are two ways to add new members</p>

		<div class="card-deck">
		  <div class="card">
		    <div class="card-body">
		      <h2 class="card-title">New Individual Member</h2>
		      <p class="card-text">If you just need to add a swimmer and don't need
		      to go through the setup process with a parent, add a New Individual
		      Member.</p>
					<p class="card-text">This method will require the parent to add the
					swimmer using an ASA Number and Access Key</p>
				</div>
		    <div class="card-footer">
		      <a href="<?=autoUrl("swimmers/new")?>">
						Add a new member
					</a>
		    </div>
		  </div>

			<!--
      <div class="card">
		    <div class="card-body">
		      <h2 class="card-title">Invite Member from Trial</h2>
		      <p class="card-text">
            If a swimmer applied for a trial online, you can invite them to join
            by email.
          </p>
					<p class="card-text">
            This process allows the full registration to be completed
            automatically (except for ASA Membership).
          </p>
				</div>
		    <div class="card-footer">
		      <a href="<?=autoUrl("trials")?>">
						Invite from trial
					</a>
		    </div>
		  </div>
			-->
		  <!--<div class="card">
		    <div class="card-body">
		      <h2 class="card-title">New Family of Members</h2>
		      <p class="card-text">Select this option if you're adding several
		      members in a family, or need to go through registration forms with a
		      new family.</p>
		    </div>
		    <div class="card-footer">
					<a disabled href="#">
		      	Add a new family (FUNCTIONALITY REMOVED)
					</a>
		    </div>
		  </div>-->
		</div>

		<p>
			We're actively working on new ways to join new members to the club and
			hope to introduce them from the summer onwards.
		</p>

	</div>
</div>

<?php include BASE_PATH . "views/footer.php";
