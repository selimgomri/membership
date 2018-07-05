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

	$pagetitle = $name . " - Code of Conduct";
	include BASE_PATH . "views/header.php";
	?>
	<div class="container">
		<div class="row">
			<div class="col-lg-8">
				<div class="mb-3 p-3 bg-white rounded box-shadow">
					<form method="post">
						<h1>Code of Conduct Acceptance</h1>
						<p class="lead">For <? echo $name; ?></p>

						<? if (isset($_SESSION['RenewalErrorInfo'])) {
							echo $_SESSION['RenewalErrorInfo'];
							unset($_SESSION['RenewalErrorInfo']);
						} ?>

						<div class="alert alert-warning">
							<p class="mb-0">
								<strong>
									You must ensure that <? echo $name; ?> is present to agree to
									this code of conduct before you continue
								</strong>
							</p>
							<p class="mb-0">
								Membership renewal agreements are legally binding once the
								process is completed
							</p>
						</div>

						<h2>Squad Code of Conduct</h2>
						<? if ($linkExists) { ?>
						<p>
							<a href="<? echo $link; ?>" target="_blank">
								View the Squad Code of Conduct
							</a>
						</p>
						<? } else { ?>
						<p>
							A code of conduct does not exist for the allocated squad. A
							general code of conduct is available instead in the form of the
							Parental Code of Conduct (below) and the Terms and Conditions of
							Membership, available on our main site.
						</p>
						<ul>
							<li>
								Parents are not allowed on poolside at any time during training
								sessions and should not engage the coach in conversation from
								the spectator area whilst they are teaching.
							</li>
							<li>
								All parents of children under the age of 11 must remain within
								the building at all times when your child attends for their
								session.
							</li>
							<li>
								Encourage your child to learn the rules and ensure that they
								abide by them.
							</li>
							<li>
								Discourage unfair practices and arguing with officials.
							</li>
							<li>
								Help your child to recognise good performance, not just results.
							</li>
							<li>
								Never force your child to take part in sport and do not offer
								incentives for participation, however positive support is
								encouraged.
							</li>
							<li>
								Set a good example by recognising fair play and applauding the
								good performances of all.
							</li>
							<li>
								Never punish or belittle a child for losing or making mistakes
								but provide positive feedback to encourage future participation.
							</li>
							<li>
								Publicly accept officials’ judgments.
							</li>
							<li>
								Support your child’s involvement and help them to enjoy their
								sport.
							</li>
							<li>
								Use correct and proper language at all times.
							</li>
						</ul>
						<? } ?>

						<h2>Swim England Code of Conduct for Swimmers</h2>
						<h3>General behaviour</h3>
						<ol>
							<li>
								I will treat all members of, and persons associated with, the
								ASA with due dignity and respect.
							</li>
							<li>
								I will treat everyone equally and never discriminate against
								another person associated with the ASA on any grounds including
								that of age, sexual orientation, gender, faith, ethnic origin or
								nationality.
							</li>
							<li>
								I understand that the use of inappropriate or abusive language,
								bullying, harassment, or physical violence will not be tolerated
								and could result in action being taken through the disciplinary
								or child welfare policies.
							</li>
							<li>
								I will display a high standard of behaviour at all times.
							</li>
							<li>
								I will always report any poor behaviour by others to an
								appropriate officer or member of staff.
							</li>
							<li>
								I will recognise and celebrate the good performance and success
								of fellow club and team members.
							</li>
							<li>
								I will respect the privacy of others, especially in the use of
								changing facilities.
							</li>
						</ol>
						<h3>Training</h3>

						<ol>
							<li>
								I will treat my coach and fellow members with respect.
							</li>
							<li>
								I will make my coach aware if I have difficulties in attending
								training sessions as per the rules laid down for my squad.
							</li>
							<li>
								I will arrive in good time on poolside before the training
								session starts to complete poolside warm up as directed by my
								coach.
							</li>
							<li>
								I understand that if I arrive late, I must report to my coach
								before entering the pool.
							</li>
							<li>
								I will ensure that I have all of my equipment with me, e.g.
								paddles, kick boards, hats, goggles, etc.
							</li>
							<li>
								If I need to leave the pool for any reason during training, I
								will inform my coach before doing so.
							</li>
							<li>
								I will listen to what my coach is telling me at all times and
								obey any instructions given.
							</li>
							<li>
								I will always swim to the wall as I would do in a race, and I
								will practice turns as instructed.
							</li>
							<li>
								I will not stop and stand in the lane, or obstruct others from
								completing their training.
							</li>
							<li>
								I will not pull on the ropes as this may injure other members.
							</li>
							<li>
								I will not skip lengths or sets – to do so means I would only be
								cheating myself.
							</li>
							<li>
								I will think about what I am doing during training, and if I
								have any problems, I will discuss them with my coach at an
								appropriate time.
							</li>
							<li>
								If I have any problems with the behaviour of fellow members, I
								will report them at the time to an appropriate adult.
							</li>
						</ol>

						<h3>Competitions</h3>
						<ol>
							<li>
								At competitions, whether they be open meets, national events or
								club galas, I will always behave in a manner that shows respect
								to my coach, the officers, my team mates and the members of all
								competing organisations.
							</li>
							<li>
								I understand that I will be required to attend events and galas
								that the Chief Coach has entered/selected me for, unless agreed
								otherwise by prior arrangement with the relevant official and
								coach.
							</li>
							<li>
								I understand that I must wear appropriate swimwear, tracksuits,
								T-shirts/shorts and hats as per the rules laid down by the
								organisation.
							</li>
							<li>
								I will report to my coach and/or team manager on arrival on
								poolside.
							</li>
							<li>
								I will warm up before the event as directed by the coach in
								charge on that day and ensure I fully prepare myself for the
								race.
							</li>
							<li>
								I will be part of the team. This means I will stay with the team
								on poolside.
							</li>
							<li>
								If I have to leave poolside for any reason, I will inform, and
								in some cases, get the consent of the team manager/coach before
								doing so.
							</li>
							<li>
								After my race, I will report to my coach for feedback.
							</li>
							<li>
								I will support my team mates. Everyone likes to be supported and
								they will be supporting me in return.
							</li>
							<li>
								I will swim down after the race if possible, as advised by my
								coach.
							</li>
							<li>
								My behaviour in the swim down facility must be appropriate and
								respectful to other users at all times.
							</li>
							<li>
								I will never leave an event until either the gala is complete or
								I have the explicit agreement of the coach or team manager.
							</li>
						</ol>

						<div class="form-group">
							<div class="custom-control custom-checkbox">
							  <input type="checkbox" class="custom-control-input" id="agree"
							  name="agree" value="1">
							  <label class="custom-control-label" for="agree">
									I, <? echo $name; ?> agree to this code of conduct
								</label>
							</div>
						</div>

						<div>
							<button type="submit" class="btn btn-success">Continue</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

	<?php include BASE_PATH . "views/footer.php"; } else { $pagetitle = "Code of
	Conduct Acceptance"; include BASE_PATH . "views/header.php"; ?>

<div class="container">
	<div class="row">
		<div class="col-lg-8">
			<div class="mb-3 p-3 bg-white rounded box-shadow">
				<form method="post">
					<h1>Parent Code of Conduct</h1>
					<p class="lead">
						You must accept the terms of this code of conduct to continue
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

					<p>
						By clicking continue, you acknowledge that you agree to the above code
						of conduct.
					</p>

					<div>
						<a class="btn btn-outline-success" href="">Save</a>
						<button type="submit" class="btn btn-success">Save and
						Continue</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<?php include BASE_PATH . "views/footer.php";
}
