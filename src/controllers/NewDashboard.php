<?

global $db;

$username = htmlspecialchars(explode(" ", getUserName($_SESSION['UserID']))[0]);

$pagetitle = "Homepage";
include BASE_PATH . "views/header.php";

?>

<div class="front-page" style="margin-bottom: -1rem;">
  <div class="container">

		<h1>Hello <?=$username?></h1>
		<p class="lead mb-4">Welcome to your account</p>

		<div class="mb-4">
      <h2 class="mb-4">Quick Tasks</h2>
      <div class="news-grid">

				<a href="<?=autoUrl('attendance/register')?>">
					<span class="mb-3">
	          <span class="title mb-0">
							Take Register
						</span>
						<span>
							Take the register for your squad
						</span>
					</span>
          <span class="category">
						Attendance
					</span>
        </a>

				<a href="<?=autoUrl('swimmers')?>">
					<span class="mb-3">
	          <span class="title mb-0">
							View Swimmer Notes
						</span>
						<span>
							Check important medical and other notes from parents
						</span>
					</span>
          <span class="category">
						Swimmers
					</span>
        </a>

				<a href="<?=autoUrl('squads/moves')?>">
					<span class="mb-3">
	          <span class="title mb-0">
							View Upcoming Squad Moves
						</span>
						<span>
							Check which swimmers are changing squads
						</span>
					</span>
          <span class="category">
						Squads
					</span>
        </a>

			</div>
		</div>

		<div class="mb-4">
      <h2 class="mb-4">Gala Tasks</h2>
      <div class="news-grid">
				<a href="<?=autoUrl('galas/entries')?>">
					<span class="mb-3">
	          <span class="title mb-0">
							Check Entries
						</span>
						<span>
							Check entries for galas
						</span>
					</span>
          <span class="category">
						Galas
					</span>
        </a>

				<a href="<?=autoUrl('galas/addgala')?>">
					<span class="mb-3">
	          <span class="title mb-0">
							Add a Gala
						</span>
						<span>
							Add a gala to the system to allow entries
						</span>
					</span>
          <span class="category">
						Galas
					</span>
        </a>
			</div>
		</div>

		<div class="mb-4">
      <h2 class="mb-4">Access G Suite</h2>
      <div class="news-grid">
				<a href="https://mail.google.com/a/chesterlestreetasc.co.uk" target="_blank">
					<span class="mb-3">
	          <span class="title mb-0">
							Gmail
						</span>
						<span>
							Access your club email
						</span>
					</span>
          <span class="category">
						G Suite
					</span>
        </a>

				<a href="https://drive.google.com/a/chesterlestreetasc.co.uk" target="_blank">
					<span class="mb-3">
	          <span class="title mb-0">
							Google Drive
						</span>
						<span>
							Create Docs, Sheets, Slides and more - Club letterhead templates are available
						</span>
					</span>
          <span class="category">
						G Suite
					</span>
        </a>

				<a href="https://calendar.google.com/a/chesterlestreetasc.co.uk" target="_blank">
					<span class="mb-3">
	          <span class="title mb-0">
							Google Calendar
						</span>
						<span>
							Manage your schedule and plan meetings with ease
						</span>
					</span>
          <span class="category">
						G Suite
					</span>
        </a>

				<a href="https://docs.google.com/a/chesterlestreetasc.co.uk/document/u/1/?tgif=d&ftv=1" target="_blank">
					<span class="mb-3">
	          <span class="title mb-0">
							Use Club Letterheads
						</span>
						<span>
							Effortlessly create great club documents
						</span>
					</span>
          <span class="category">
						G Suite
					</span>
        </a>

				<a href="https://www.google.com/accounts/AccountChooser?hd=chesterlestreetasc.co.uk&continue=https://apps.google.com/user/hub" target="_blank">
					<span class="mb-3">
	          <span class="title mb-0">
							More Applications
						</span>
						<span>
							Collaborate in real-time
						</span>
					</span>
          <span class="category">
						G Suite
					</span>
        </a>
			</div>
		</div>

	</div>
</div>

<?

include BASE_PATH . "views/footer.php";
