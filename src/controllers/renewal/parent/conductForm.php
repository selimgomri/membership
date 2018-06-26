<?php
if (isset($id)) {
	$id = mysqli_real_escape_string($link, $id);
	$name = getSwimmerName($id);
	if (!$name) {
		halt(404);
	}

	$sql = "SELECT * FROM `squads` INNER JOIN `members` ON members.SquadID =
	squads.SquadID WHERE `MemberID` = '$id';";
	$result = mysqli_query($link, $sql);
	if (mysqli_num_rows($result) != 1) {
		halt(404);
	}

	$row = mysqli_fetch_array($result, MYSQLI_ASSOC);

	$linkExists = false;
	$link = "";

	if ($row['SquadCoC'] != "") {
		$linkExists = true;
		$link = $row['SquadCoC'];
	}

	$pagetitle = $name . " -  Code of Conduct";
	include BASE_PATH . "views/header.php";
	?>
	<div class="container">
		<div class="row">
			<div class="col-lg-8">
				<form method="post">
					<h1>Code of Conduct Acceptance</h1>
					<p class="lead">For <? echo $name; ?></p>

					<? if ($linkExists) { ?>
					<p>
						<a href="<? echo $link; ?>" target="_blank">
							View the Code of Conduct
						</a>
					</p>
					<? } else { ?>
					<p>
						A code of conduct does not exist for the allocated squad. A general
						code of conduct is available instead in the form of the Parental Code
						of Conduct (below) and the Terms and Conditions of Membership,
						available on our main site.
					</p>
					<ul>
						<li>
							Parents are not allowed on poolside at any time during training
							sessions and should not engage the coach in conversation from the
							spectator area whilst they are teaching.
						</li>
						<li>
							All parents of children under the age of 11 must remain within the
							building at all times when your child attends for their session.
						</li>
						<li>
							Encourage your child to learn the rules and ensure that they abide by
							them.
						</li>
						<li>
							Discourage unfair practices and arguing with officials.
						</li>
						<li>
							Help your child to recognise good performance, not just results.
						</li>
						<li>
							Never force your child to take part in sport and do not offer
							incentives for participation, however positive support is encouraged.
						</li>
						<li>
							Set a good example by recognising fair play and applauding the good
							performances of all.
						</li>
						<li>
							Never punish or belittle a child for losing or making mistakes but
							provide positive feedback to encourage future participation.
						</li>
						<li>
							Publicly accept officials’ judgments.
						</li>
						<li>
							Support your child’s involvement and help them to enjoy their sport.
						</li>
						<li>
							Use correct and proper language at all times.
						</li>
					</ul>
					<? } ?>

					<div class="mb-3">
						<a class="btn btn-outline-success" href="">Save</a>
						<button type="submit" class="btn btn-success">Save and
						Continue</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<?php include BASE_PATH . "views/footer.php"; } else { $pagetitle = "Code of
	Conduct Acceptance"; include BASE_PATH . "views/header.php"; ?>

<div class="container">
	<div class="row">
		<div class="col-lg-8">
			<form method="post">
				<h1>Code of Conduct Acceptance</h1>
				<p class="lead">For Parents (Thats you!)</p>

				<ul>
					<li>
						Parents are not allowed on poolside at any time during training
						sessions and should not engage the coach in conversation from the
						spectator area whilst they are teaching.
					</li>
					<li>
						All parents of children under the age of 11 must remain within the
						building at all times when your child attends for their session.
					</li>
					<li>
						Encourage your child to learn the rules and ensure that they abide by
						them.
					</li>
					<li>
						Discourage unfair practices and arguing with officials.
					</li>
					<li>
						Help your child to recognise good performance, not just results.
					</li>
					<li>
						Never force your child to take part in sport and do not offer
						incentives for participation, however positive support is encouraged.
					</li>
					<li>
						Set a good example by recognising fair play and applauding the good
						performances of all.
					</li>
					<li>
						Never punish or belittle a child for losing or making mistakes but
						provide positive feedback to encourage future participation.
					</li>
					<li>
						Publicly accept officials’ judgments.
					</li>
					<li>
						Support your child’s involvement and help them to enjoy their sport.
					</li>
					<li>
						Use correct and proper language at all times.
					</li>
				</ul>

				<p>
					By clicking continue, you acknowledge that you agree to the above code
					of conduct.
				</p>

				<div class="mb-3">
					<a class="btn btn-outline-success" href="">Save</a>
					<button type="submit" class="btn btn-success">Save and
					Continue</button>
				</div>
			</form>
		</div>
	</div>
</div>

<?php include BASE_PATH . "views/footer.php";
}
