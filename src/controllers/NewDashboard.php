<?

// https://chesterlestreetasc.co.uk/wp-json/wp/v2/posts
// https://www.swimming.org/sport/wp-json/wp/v2/posts

$json = file_get_contents('https://chesterlestreetasc.co.uk/wp-json/wp/v2/posts?rand_id=' . time());
$obj = json_decode($json);

$file = null;
$cache_file = BASE_PATH . 'cache/SE-News.json';
if(file_exists($cache_file)) {
  if(time() - filemtime($cache_file) > 10800) {
    // too old , re-fetch
    $cache = file_get_contents('https://www.swimming.org/sport/wp-json/wp/v2/posts?rand_id=' . time());
    file_put_contents($cache_file, $cache);
    $file = $cache;
  } else {
    $file = file_get_contents($cache_file);
  }
} else {
  // no cache, create one
  $cache = file_get_contents('https://www.swimming.org/sport/wp-json/wp/v2/posts?rand_id=' . time());
  file_put_contents($cache_file, $cache);
  $file = $cache;
}
$asa = json_decode($file);

$file = null;
$cache_file = BASE_PATH . '/cache/SE-NE.xml';
if (file_exists($cache_file)) {
  if (time() - filemtime($cache_file) > 10800) {
    // too old , re-fetch
    $cache = file_get_contents('http://asaner.org.uk/feed/');
    file_put_contents($cache_file, $cache);
    $file = $cache;
  } else {
    $file = file_get_contents($cache_file);
  }
} else {
  // no cache, create one
  $cache = file_get_contents('http://asaner.org.uk/feed/');
  file_put_contents($cache_file, $cache);
  $file = $cache;
}
$asa_ne = null;
try {
  $asa_ne = new SimpleXMLElement($file);
} catch (Exception $e) {
}

global $db;

$username = htmlspecialchars(explode(" ", getUserName($_SESSION['UserID']))[0]);

$day = date("w");
$time = date("H:i:s");
$time30 = date("H:i:s", strtotime("-30 minutes"));

$sql = "SELECT SessionID, squads.SquadID, SessionName, SquadName, VenueName, StartTime, EndTime FROM ((`sessions` INNER JOIN squads ON squads.SquadID = sessions.SquadID) INNER JOIN sessionsVenues ON sessions.VenueID = sessionsVenues.VenueID) WHERE SessionDay = :day AND StartTime <= :timenow AND (EndTime > :timenow OR EndTime > :time30) AND DisplayFrom <= CURDATE() AND DisplayUntil >= CURDATE() ORDER BY SquadFee DESC, SquadName ASC";
global $db;

$query = $db->prepare($sql);
$query->execute(['day' => $day, 'timenow' => $time, 'time30' => $time30]);
$sessions = $query->fetchAll(PDO::FETCH_ASSOC);

global $db;
$query = $db->prepare("SELECT EmailAddress FROM users WHERE
UserID = ?");
$query->execute([$_SESSION['UserID']]);
$userInfo = $query->fetch(PDO::FETCH_ASSOC);

$pagetitle = "Home";
include BASE_PATH . "views/header.php";

?>

<div class="front-page mb-n3">
  <div class="container">

		<h1>Hello <?=$username?></h1>
		<p class="lead mb-4">Welcome to your account</p>

    <? if (sizeof($sessions) > 0) { ?>
      <div class="mb-4">
        <h2 class="mb-4">Take Register for Current Sessions</h2>
        <div class="mb-4">
          <div class="news-grid">
        <? for ($i = 0; $i < sizeof($sessions); $i++) { ?>
          <a href="<?=autoUrl("attendance/register/" . $sessions[$i]['SquadID'] . "/" . $sessions[$i]['SessionID'])?>" title="<?=$sessions[$i]['SquadName']?> Squad Register, <?=$sessions[$i]['SessionName']?>">
            <div>
              <span class="title mb-0">
                Take <?=$sessions[$i]['SquadName']?> Squad Register
              </span>
              <span class="d-flex mb-3">
                <?=date("H:i", strtotime($sessions[$i]['StartTime']))?> - <?=date("H:i", strtotime($sessions[$i]['EndTime']))?>
              </span>
            </div>
            <span class="category">
              <?=$sessions[$i]['SessionName']?>, <?=$sessions[$i]['VenueName']?>
            </span>
          </a>
        <? } ?>
        </div>
      </div>
    </div>
    <? } ?>

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

    <? if (IS_CLS === true) { ?>
    <div class="mb-4">
      <h2 class="mb-4">Club News</h2>
      <div class="news-grid">
        <?
        $max_posts = 6;
        if (sizeof($obj) < $max_posts) {
          $max_posts = sizeof($obj);
        }
        for ($i = 0; $i < $max_posts; $i++) { ?>
				<a href="<?=$obj[$i]->link?>" target="_blank" title="<?=$obj[$i]->title->rendered?>">
					<span class="mb-3">
	          <span class="title mb-0">
							<?=$obj[$i]->title->rendered?>
						</span>
					</span>
          <span class="category">
						News
					</span>
        </a>
        <? } ?>
			</div>
		</div>
	 <? } ?>

  <? if ($asa != null && $asa != "") { ?>
	<div class="mb-4">
    <h2 class="mb-4">Swim England News</h2>
    <div class="news-grid">
      <?
      $max_posts = 6;
      if (sizeof($asa) < $max_posts) {
        $max_posts = sizeof($asa);
      }
      for ($i = 0; $i < $max_posts; $i++) { ?>
			<a href="<?=$asa[$i]->link?>" target="_blank" title="<?=$asa[$i]->title->rendered?>">
				<span class="mb-3">
          <span class="title mb-0">
						<?=$asa[$i]->title->rendered?>
					</span>
				</span>
        <span class="category">
					News
				</span>
      </a>
      <? } ?>
		</div>
	</div>
  <? } ?>

  <?php if ($asa_ne != null) { ?>
  <div class="mb-4">
    <h2 class="mb-4">Swim England North East News</h2>
    <div class="news-grid">
      <?
      $max_posts = 6;
      if (sizeof($asa_ne->channel->item) < $max_posts) {
        $max_posts = sizeof($asa_ne->channel->item);
      }
      for ($i = 0; $i < $max_posts; $i++) { ?>
      <a href="<?=$asa_ne->channel->item[$i]->link?>" target="_blank" title="<?=$asa_ne->channel->item[$i]->title?> (<?=$asa_ne->channel->item[$i]->category?>)">
        <span class="mb-3">
          <span class="title mb-0">
            <?=$asa_ne->channel->item[$i]->title?>
          </span>
        </span>
        <span class="category">
          <?=$asa_ne->channel->item[$i]->category?>
        </span>
      </a>
      <?php } ?>
    </div>
  </div>
  <?php } ?>

    <?php
    if (strpos($userInfo['EmailAddress'], '@chesterlestreetasc.co.uk')) {
    ?>

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

    <?php
    }
    ?>

	</div>
</div>

<?

include BASE_PATH . "views/footer.php";
