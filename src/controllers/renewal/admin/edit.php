<?php

$id = mysqli_real_escape_string($link, $id);

$sql = "SELECT * FROM `renewals` WHERE `ID` = '$id';";
$renewals = mysqli_query($link, $sql);

$row = mysqli_fetch_array($renewals, MYSQLI_ASSOC);

$pagetitle = "Create New Renewal";
include BASE_PATH . "views/header.php";
include BASE_PATH . "views/swimmersMenu.php";
?>

<div class="container">
	<div class="my-3 p-3 bg-white rounded shadow">
		<form method="post">
			<h1>Editing <?php echo $row['Name']; ?></h1>
			<?php if (isset($_SESSION['NewRenewalErrorInfo'])) {
				echo $_SESSION['NewRenewalErrorInfo'];
				unset($_SESSION['NewRenewalErrorInfo']);
			} ?>

			<div class="form-group">
		    <label for="name">Renewal Name</label>
		    <input type="text" class="form-control" id="name" name="name" value="<?
		    echo $row['Name']; ?>">
		  </div>

			<div class="form-row">
				<div class="form-group col-md-6">
			    <label for="start">Start Date</label>
			    <input type="date" class="form-control" id="start" name="start"
			    value="<?php echo date("Y-m-d", strtotime($row['StartDate'])); ?>">
			  </div>

				<div class="form-group col-md-6">
			    <label for="end">End Date</label>
			    <input type="date" class="form-control" id="end" name="end" value="<?
			    echo date("Y-m-d", strtotime($row['EndDate'])); ?>">
			  </div>
			</div>

			<p class="mb-0">
				<button class="btn btn-success" type="submit">
					Save Changes
				</button>
				<a href="<?php echo autoUrl("renewal/" . $id); ?>" class="btn
				btn-danger">
					Return to Status List
				</a>
			</p>

		</form>
	</div>
</div>

<?php include BASE_PATH . "views/footer.php";
