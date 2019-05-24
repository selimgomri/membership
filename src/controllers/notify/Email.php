<?php
$pagetitle = "Notify Composer";
$use_white_background = true;

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/notifyMenu.php";

$sql = "SELECT `SquadName`, `SquadID` FROM `squads` ORDER BY `SquadFee` DESC, `SquadName` ASC;";
$result = mysqli_query($link, $sql);

$sql = "SELECT * FROM `targetedLists` ORDER BY `Name` ASC;";
$lists = mysqli_query($link, $sql);

global $db;
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

if (!(defined('IS_CLS') && IS_CLS)) {
  $fromEmail .= '.' . urlencode(strtolower(str_replace(' ', '', CLUB_CODE)));
}

$fromEmail .= '@' . EMAIL_DOMAIN;

 ?>

<div class="container">
	<h1>Notify Composer</h1>
	<p class="lead">Send Emails to targeted groups of parents</p>
  <hr>
	<form method="post" onkeypress="return event.keyCode != 13;">
    <div class="form-group">
			<label>To parents of swimmers in the following targeted lists...</label>
			<div class="row">
			<?php for ($i = 0; $i < mysqli_num_rows($lists); $i++) {
				$row = mysqli_fetch_array($lists, MYSQLI_ASSOC); ?>
				<div class="col-6 col-sm-6 col-md-4 col-lg-3">
					<div class="custom-control custom-checkbox">
					  <input type="checkbox" class="custom-control-input"
            id="TL-<?php echo $row['ID']; ?>" name="TL-<?php echo $row['ID']; ?>"
            value="1">
					  <label class="custom-control-label"
              for="TL-<?php echo $row['ID']; ?>">
              <?=htmlspecialchars($row['Name'])?>
            </label>
					</div>
				</div>
			<?php } ?>
			</div>
		</div>
		<div class="form-group">
			<label>To parents of swimmers in the following squads...</label>
			<div class="row">
			<?php for ($i = 0; $i < mysqli_num_rows($result); $i++) {
				$row = mysqli_fetch_array($result, MYSQLI_ASSOC); ?>
				<div class="col-6 col-sm-6 col-md-4 col-lg-3">
					<div class="custom-control custom-checkbox">
					  <input type="checkbox" class="custom-control-input"
            id="<?php echo $row['SquadID']; ?>" name="<?php echo $row['SquadID']; ?>"
            value="1">
					  <label class="custom-control-label"
              for="<?php echo $row['SquadID']; ?>">
              <?=htmlspecialchars($row['SquadName'])?> Squad
            </label>
					</div>
				</div>
			<?php } ?>
			</div>
		</div>

    <div class="form-group">
			<label for="from">From</label>
      <select class="custom-select" name="from" id="from">
        <option value="current-user"><?=htmlspecialchars($curUserInfo['Forename'] . ' ' . $curUserInfo['Surname'] . " <noreply@" . EMAIL_DOMAIN . ">  ")?></option>
        <option value="club-sending-account" selected><?=htmlspecialchars(CLUB_NAME . " <noreply@" . EMAIL_DOMAIN . ">")?></option>
      </select>
		</div>

		<div class="form-group">
			<label for="subject">Message Subject</label>
			<input type="text" class="form-control" name="subject" id="subject"
      placeholder="Message Subject" autocomplete="off">
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

    <?php if ($_SESSION['AccessLevel'] != "Coach") { ?>

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
