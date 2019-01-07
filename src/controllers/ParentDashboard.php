<?

global $db;

$json = file_get_contents('https://chesterlestreetasc.co.uk/wp-json/wp/v2/posts');
$obj = json_decode($json);

$file = null;
$cache_file = BASE_PATH . 'cache/SE-News.json';
if(file_exists($cache_file)) {
  if(time() - filemtime($cache_file) > 10800) {
    // too old , re-fetch
    $cache = file_get_contents('https://www.swimming.org/sport/wp-json/wp/v2/posts');
    file_put_contents($cache_file, $cache);
    $file = $cache;
  } else {
    $file = file_get_contents($cache_file);
  }
} else {
  // no cache, create one
  $cache = file_get_contents('https://www.swimming.org/sport/wp-json/wp/v2/posts');
  file_put_contents($cache_file, $cache);
  $file = $cache;
}
$asa = json_decode($file);

try {
	$sql = 'SELECT `MemberID`, `MForename`, `MSurname`, `SquadFee`, `SquadName` FROM `members` INNER JOIN `squads` ON `members`.`SquadID` =
	`squads`.`SquadID` WHERE `members`.`UserID` = ? ORDER BY `MForename` ASC,
	`MSurname` ASC';
	$query = $db->prepare($sql);
	$query->execute([$_SESSION['UserID']]);
} catch (Exception $e) {
	halt(500);
}

$swimmers = $query->fetchAll(PDO::FETCH_ASSOC);

try {
	$sql = 'SELECT `MForename`, `MSurname`, `GalaName`, `FeeToPay`, `EntryID` FROM
	((`galaEntries` INNER JOIN `members` ON `members`.`MemberID` =
	`galaEntries`.`MemberID`) INNER JOIN `galas` ON `galas`.`GalaID` =
	`galaEntries`.`GalaID`) WHERE `members`.`UserID` = ? AND `GalaDate` >=
	CURDATE() ORDER BY `GalaDate` ASC, `MForename` ASC, `MSurname` ASC';
	$query = $db->prepare($sql);
	$query->execute([$_SESSION['UserID']]);
} catch (Exception $e) {
	halt(500);
}

$galas = $query->fetchAll(PDO::FETCH_ASSOC);

$username = htmlspecialchars(explode(" ", getUserName($_SESSION['UserID']))[0]);

$pagetitle = "Home";
include BASE_PATH . "views/header.php";

?>

<div class="front-page" style="margin-bottom: -1rem;">
  <div class="container">

		<? if (!isSubscribed($_SESSION['UserID'], 'Notify')) { ?>
	  <div class="alert alert-danger">
	    <p class="mb-0">
	      <strong>
	        You're missing out on email updates from <?=CLUB_NAME?>
	      </strong>
	    </p>
	    <p class="mb-0">
	      Head to <a href="<? echo autoUrl("myaccount/email"); ?>" class="alert-link">My
	      Account</a> to change your email preferences and stay up to date!
	    </p>
	  </div>
		<? } ?>

		<h1>Hello <?=$username?></h1>
		<p class="lead mb-4">Welcome to your account</p>

    <div class="mb-4">
      <h2 class="mb-4">Membership Renewal for 2019 is open now</h2>
      <div class="news-grid">
        <a href="<?=autoUrl("renewal")?>">
          <span class="mb-3">
            <span class="title mb-0">
              Complete membership renewal online
            </span>
          </span>
          <span class="category">
            Renewal
          </span>
        </a>
        <a target="_blank" href="https://www.chesterlestreetasc.co.uk/support/membershiprenewal/completing-your-membership-renewal/">
          <span class="mb-3">
            <span class="title mb-0">
              Help with membership renewal
            </span>
          </span>
          <span class="category">
            Help and Support
          </span>
        </a>
      </div>
    </div>

    <? if (!userHasMandates($_SESSION['UserID'])) { ?>
    <div class="mb-4">
      <h2 class="mb-4">Want to set up a Direct Debit?</h2>
      <div class="news-grid">
        <a href="<?=autoUrl("payments")?>">
          <span class="mb-3">
            <span class="title mb-0">
              Setup a Direct Debit Now
            </span>
          </span>
          <span class="category">
            Payments
          </span>
        </a>
        <a href="https://www.chesterlestreetasc.co.uk/support/directdebit/">
          <span class="mb-3">
            <span class="title mb-0">
              Learn more about Direct Debits
            </span>
          </span>
          <span class="category">
            Payments
          </span>
        </a>
      </div>
    </div>
    <? } ?>

		<div class="mb-4">
      <h2 class="mb-4">My Swimmers</h2>
      <div class="news-grid">
				<?
				if (sizeof($swimmers) > 0) {
				foreach ($swimmers as $s) {
					$fee = "&pound;0.00 - Exempt from fees";
					if (!$s['ClubPays']) {
						$fee = "&pound;" . htmlspecialchars($s['SquadFee']);
					}?>
				<a href="<?=autoUrl("swimmers/" . $s['MemberID'])?>">
					<span class="mb-3">
	          <span class="title mb-0">
							<?=htmlspecialchars($s['MForename'] . ' ' . $s['MSurname'])?>
						</span>
						<span>
							<?=$fee?>
						</span>
					</span>
          <span class="category">
						<?=htmlspecialchars($s['SquadName'])?> Squad
					</span>
        </a>
				<? }
			} else { ?>
				<p class="mb-0">You do not have any swimmers connected to your account</p>
			<? } ?>
			</div>
		</div>

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

		<div class="mb-4">
      <h2 class="mb-4">My Gala Entries</h2>
      <div class="news-grid">
				<?
				if (sizeof($galas) > 0) {
				foreach ($galas as $g) { ?>
				<a href="<?=autoUrl("galas/entries/" . $g['EntryID'])?>">
					<span class="mb-3">
	          <span class="title mb-0">
							<?=htmlspecialchars($g['MForename'] . ' ' . $g['MSurname'])?>
						</span>
						<span>
							&pound;<?=htmlspecialchars(number_format($g['FeeToPay'], 2, '.', ','))?>
						</span>
					</span>
          <span class="category">
						<?=htmlspecialchars($g['GalaName'])?>
					</span>
        </a>
			<? }
			} else { ?>
				<p class="mb-0">You have no current gala entries</p>
			<? } ?>
			</div>
		</div>

	</div>
</div>

<?

include BASE_PATH . "views/footer.php";
