<?php

global $db;

$pagetitle = "My Swimmers";
$userID = $_SESSION['UserID'];

include BASE_PATH . "views/header.php";

$getSwimmers = $db->prepare("SELECT MemberID id, MForename `first`, MSurname `last`, SquadName `squad`, SquadFee `fee`, ClubPays free FROM members INNER JOIN squads ON members.SquadID = squads.SquadID WHERE members.UserID = ? ORDER BY `first` ASC, `last` ASC");
$getSwimmers->execute([$_SESSION['UserID']]);

?>

<div class="front-page mb-n3">
	<div class="container">
		<h1>My Swimmers</h1>
		<?php if ($swimmer = $getSwimmers->fetch(PDO::FETCH_ASSOC)) { ?>
		<div class="row">
			<div class="col-md-8">
				<p class="lead">
					Here are your swimmers
				</p>
				<p>
					Please remember that it is your responsibility to also keep the Swim England Membership System up to date with personal details.
				</p>
			</div>
		</div>

		<div class="news-grid mb-4">
			<?php do { ?>
				<a href="<?=autoUrl("swimmers/" . $swimmer['id'])?>">
					<span class="mb-3">
						<span class="title mb-0">
							<?=htmlspecialchars($swimmer['first'] . ' ' . $swimmer['last'])?>
						</span>
						<span>
						<?=htmlspecialchars($swimmer['squad'])?> Squad
						</span>
					</span>
					<span class="category">
						<?php if ($swimmer['free']) { ?>
						This swimmer doesn't pay
						<?php } else { ?>
						&pound;<?=number_format($swimmer['fee'], 2, '.', '')?> per month
						<?php } ?>
					</span>
				</a>
			<?php } while ($swimmer = $getSwimmers->fetch(PDO::FETCH_ASSOC)); ?>
		</div>
		<?php } else { ?>
		<div class="row">
			<div class="col-md-8">
				<p class="lead">
					Swimmers connected to your account will be shown here once you add some.
				</p>
			</div>
		</div>
		<?php } ?>

		<p>
			<a href="<?php echo autoUrl('myaccount/addswimmer'); ?>" class="btn btn-success">Add a Swimmer</a>
		</p>
	</div>
</div>

<?php include BASE_PATH . "views/footer.php";
