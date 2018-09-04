<?php

$pagetitle = "Time Conversion System";
include BASE_PATH . "views/header.php";
?>

<div class="container">
	<div class="row">
		<div class="col">
			<div class="mb-3 p-3 bg-white rounded shadow">
				<h1>Swim Time Converter</h1>
				<? if (isset($_SESSION['Time'])) { ?>
				<? if ($_SESSION['Time']['Error']) { ?>
				    <div class="alert alert-warning">
				        An error occured during calculation
			        </div>
				<? } else { ?>
					<table class="table table-striped">
						<thead class="thead-light">
					    <tr>
					      <th scope="col">Pool Length</th>
					      <th scope="col">Time (<?=htmlentities($_SESSION['Time']['Event'])?>)</th>
					    </tr>
					  </thead>
						<tbody>
							<tr>
								<td>20m</td>
								<td class="mono"><?=$_SESSION['Time']['Conv-20m']?></td>
							</tr>
							<tr>
								<td>25m</td>
								<td class="mono"><?=$_SESSION['Time']['Conv-25m']?></td>
							</tr>
							<tr>
								<td>33 1/3m</td>
								<td class="mono"><?=$_SESSION['Time']['Conv-33m']?></td>
							</tr>
							<tr>
								<td>50m</td>
								<td class="mono"><?=$_SESSION['Time']['Conv-50m']?></td>
							</tr>
							<tr>
								<td>20y</td>
								<td class="mono"><?=$_SESSION['Time']['Conv-20y']?></td>
							</tr>
							<tr>
								<td>25y</td>
								<td class="mono"><?=$_SESSION['Time']['Conv-25y']?></td>
							</tr>
							<tr>
								<td>27 1/2y</td>
								<td class="mono"><?=$_SESSION['Time']['Conv-27y']?></td>
							</tr>
							<tr>
								<td>33 1/3y</td>
								<td class="mono"><?=$_SESSION['Time']['Conv-33y']?></td>
							</tr>
							<tr>
								<td>36 2/3y</td>
								<td class="mono"><?=$_SESSION['Time']['Conv-36y']?></td>
							</tr>
						</tbody>
					</table>
				<? } unset($_SESSION['Time']); } ?>
				<? include "Form.php"; ?>
				<p class="small text-muted mt-3 mb-0">This software is powered by the <a href="https://github.com/Chester-le-Street-ASC/EquivalentTime" target="_blank">Chester-le-Street-ASC/EquivalentTime</a> package.</p>
			</div>
		</div>
	</div>
</div>

<?

include BASE_PATH . "views/footer.php";
