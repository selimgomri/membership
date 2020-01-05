<?php
$pagetitle = "Notify Composer";
$use_white_background = true;

$emailPrefix = '';
if (!bool(env('IS_CLS'))) {
	$emailPrefix = mb_strtolower(trim(env('ASA_CLUB_CODE'))) . '-';
}

global $db;

$squads = null;
if ($_SESSION['AccessLevel'] != 'Parent') {
  $squads = $db->query("SELECT `SquadName`, `SquadID` FROM `squads` ORDER BY `SquadFee` DESC, `SquadName` ASC;");
} else {
  $squads = $db->prepare("SELECT `SquadName`, `SquadID` FROM `squads` INNER JOIN squadReps ON squadReps.Squad = squads.SquadID WHERE squadReps.User = ? ORDER BY `SquadFee` DESC, `SquadName` ASC;");
  $squads->execute([$_SESSION['UserID']]);
}

$lists = null;
if ($_SESSION['AccessLevel'] != 'Parent') {
  $lists = $db->query("SELECT targetedLists.ID, targetedLists.Name FROM `targetedLists` ORDER BY `Name` ASC;");
} else {
  $lists = $db->prepare("SELECT targetedLists.ID, targetedLists.Name FROM `targetedLists` INNER JOIN listSenders ON listSenders.List = targetedLists.ID WHERE listSenders.User = ? ORDER BY `Name` ASC;");
  $lists->execute([$_SESSION['UserID']]);
}

$galas = $db->prepare("SELECT GalaName, GalaID FROM `galas` WHERE GalaDate >= ? ORDER BY `GalaName` ASC;");
$date = new DateTime('-1 week', new DateTimeZone('Europe/London'));
$galas->execute([$date->format('Y-m-d')]);

$query = $db->prepare("SELECT Forename, Surname, EmailAddress FROM users WHERE
UserID = ?");
$query->execute([$_SESSION['UserID']]);
$curUserInfo = $query->fetch(PDO::FETCH_ASSOC);

$senderNames = explode(' ', $curUserInfo['Forename'] . ' ' . $curUserInfo['Surname']);
$fromEmail = "";
for ($i = 0; $i < sizeof($senderNames); $i++) {
  $fromEmail .= urlencode(strtolower($senderNames[$i]));
  if ($i < sizeof($senderNames) - 1) {
    $fromEmail .= '.';
  }
}

if (!bool(env('IS_CLS'))) {
  $fromEmail .= '.' . urlencode(mb_strtolower(str_replace(' ', '', env('CLUB_CODE'))));
}

$fromEmail .= '@' . env('EMAIL_DOMAIN');

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/notifyMenu.php";

 ?>

<div class="container">
	<h1>Notify Composer</h1>
	<p class="lead">Send Emails to targeted groups of parents</p>
  <hr>
  <form method="post" onkeypress="return event.keyCode != 13;">

    <div class="form-group">
			<label>To parents of swimmers in the following targeted lists...</label>
			<div class="row">
			<?php while ($list = $lists->fetch(PDO::FETCH_ASSOC)) { ?>
				<div class="col-6 col-sm-6 col-md-4 col-lg-3">
					<div class="custom-control custom-checkbox">
					  <input type="checkbox" class="custom-control-input"
            id="TL-<?=$list['ID']?>" name="TL-<?=$list['ID']?>"
            value="1">
					  <label class="custom-control-label"
              for="TL-<?=$list['ID']?>">
              <?=htmlspecialchars($list['Name'])?>
            </label>
					</div>
				</div>
			<?php } ?>
			</div>
    </div>
    
		<div class="form-group">
			<label>To parents of swimmers in the following squads...</label>
			<div class="row">
			<?php while ($squad = $squads->fetch(PDO::FETCH_ASSOC)) { ?>
				<div class="col-6 col-sm-6 col-md-4 col-lg-3">
					<div class="custom-control custom-checkbox">
					  <input type="checkbox" class="custom-control-input"
            id="<?=$squad['SquadID']?>" name="<?=$squad['SquadID']?>"
            value="1">
					  <label class="custom-control-label"
              for="<?=$squad['SquadID']?>">
              <?=htmlspecialchars($squad['SquadName'])?> Squad
            </label>
					</div>
				</div>
			<?php } ?>
			</div>
		</div>

    <?php if ($_SESSION['AccessLevel'] != 'Parent') { ?>
    <div class="form-group">
			<label>To parents of swimmers entered in the following galas...</label>
			<div class="row">
			<?php while ($gala = $galas->fetch(PDO::FETCH_ASSOC)) { ?>
				<div class="col-6 col-sm-6 col-md-4 col-lg-3">
					<div class="custom-control custom-checkbox">
					  <input type="checkbox" class="custom-control-input"
            id="GALA-<?=$gala['GalaID']?>" name="GALA-<?=$gala['GalaID']?>"
            value="1">
					  <label class="custom-control-label"
              for="GALA-<?=$gala['GalaID']?>">
              <?=htmlspecialchars($gala['GalaName'])?>
            </label>
					</div>
				</div>
			<?php } ?>
			</div>
    </div>
    <?php } ?>

    <div class="row">
      <div class="col-md">
        <div class="form-group">
          <label for="from">Send message as</label>
          <div class="custom-control custom-radio">
            <input type="radio" id="from-club" name="from" class="custom-control-input" value="club-sending-account" <?php if ($_SESSION['AccessLevel'] != 'Parent') { ?>checked<?php } ?>>
            <label class="custom-control-label" for="from-club"><?=htmlspecialchars(env('CLUB_NAME'))?></label>
          </div>
          <div class="custom-control custom-radio">
            <input type="radio" id="from-user" name="from" class="custom-control-input" value="current-user" <?php if ($_SESSION['AccessLevel'] == 'Parent') { ?>checked<?php } ?>>
            <label class="custom-control-label" for="from-user"><?=htmlspecialchars($curUserInfo['Forename'] . ' ' . $curUserInfo['Surname'])?></label>
          </div>
        </div>
      </div>

      <div class="col-md">
        <div class="form-group">
          <label for="ReplyToMe">Send replies to</label>
          <div class="custom-control custom-radio">
            <input type="radio" id="ReplyTo-Club" name="ReplyToMe" class="custom-control-input" value="0" checked>
            <label class="custom-control-label" for="ReplyTo-Club">Main club address</label>
          </div>
          <div class="custom-control custom-radio">
            <input type="radio" id="ReplyTo-Me" name="ReplyToMe" class="custom-control-input" value="1" <?php if (!getUserOption($_SESSION['UserID'], 'NotifyReplyAddress')) { ?>disabled<?php } ?>>
            <label class="custom-control-label" for="ReplyTo-Me">My reply-to email address</label>
          </div>
          <small class="form-text text-muted">
            <a href="<?=htmlspecialchars(autoUrl("notify/reply-to"))?>" target="_blank">Manage reply-to address</a>
          </small>
        </div>
      </div>
		</div>

		<div class="form-group">
			<label for="subject">Message Subject</label>
			<input type="text" class="form-control" name="subject" id="subject"
      placeholder="Message Subject" autocomplete="off" required>
		</div>

		<div class="form-group">
			<label for="message">Your Message</label>
      <p>
        <em>
          Your message will begin with "Hello
          <span class="mono">Parent Name</span>,".
        </em>
      </p>
			<textarea class="form-control" id="message" name="message" rows="10">
      </textarea>
			<small id="messageHelp" class="form-text text-muted">
        Styling will be stripped from this message
      </small>
		</div>

    <?php if ($_SESSION['AccessLevel'] == "Admin" || $_SESSION['AccessLevel'] == "Galas") { ?>

    <div class="form-group">
      <div class="custom-control custom-checkbox">
        <input type="checkbox" class="custom-control-input" aria-describedby="forceHelp" id="force" name="force">
        <label class="custom-control-label" for="force">Force Send</label>
        <small id="forceHelp" class="form-text text-muted">
          Normally, messages will only be sent to those who have opted in to email
          notifications. Selecting Force Send overrides this. If you do this, you
          must be able to justify your reason for doing so to the System
          Administrator or the Chair Person.
        </small>
      </div>
    </div>

    <?php } ?>

    <?=SCDS\CSRF::write()?>
    <?=SCDS\FormIdempotency::write()?>

		<p><button class="btn btn-success" id="submit" value="submitted" type="submit">Send the email</button></p>
	</form>
</div>

<script>
 tinymce.init({
    selector: '#message',
    branding: false,
    plugins: [
      'autolink lists link image charmap print preview anchor textcolor',
      'searchreplace visualblocks code autoresize insertdatetime media table',
      'contextmenu paste code help wordcount'
    ],
    paste_as_text: true,
    toolbar: 'insert | undo redo |  formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
    content_css: [
      'https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i',
      '<?php echo autoUrl("css/tinymce.css"); ?>'
    ]
      //toolbar: "link",
 });

 $(document).ready(function() {
  $(window).keydown(function(event){
    if(event.keyCode == 13) {
      event.preventDefault();
      return false;
    }
  });
});
</script>
<?php include BASE_PATH . "views/footer.php";
