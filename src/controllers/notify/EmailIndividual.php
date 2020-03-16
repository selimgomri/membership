<?php
$use_white_background = true;

if (is_null($user)) {
  halt(400);
}

global $db;
$query = $db->prepare("SELECT Forename, Surname, EmailAddress FROM users WHERE
UserID = ?");
$query->execute([$user]);
$userInfo = $query->fetchAll(PDO::FETCH_ASSOC);
$query->execute([$_SESSION['UserID']]);
$curUserInfo = $query->fetchAll(PDO::FETCH_ASSOC);

if (sizeof($userInfo) != 1) {
  halt(400);
}

$swimmerDetails = $db->prepare("SELECT MForename fn, MSurname sn, MemberID id FROM `members` WHERE `members`.`MemberID` = ?");
$swimmerDetails->execute([$id]);
$mySwimmer = $swimmerDetails->fetch(PDO::FETCH_ASSOC);

$userInfo = $userInfo[0];
$curUserInfo = $curUserInfo[0];

$name = $userInfo['Forename'] . ' ' . $userInfo['Surname'];
$email = $userInfo['EmailAddress'];
$myName = $curUserInfo['Forename'] . ' ' . $curUserInfo['Surname'];
$myEmail; $canReply;

if (strpos($curUserInfo['EmailAddress'], '@chesterlestreetasc.co.uk') == mb_strlen(str_replace('@chesterlestreetasc.co.uk', '', $curUserInfo['EmailAddress']))) {
  $myEmail = $curUserInfo['EmailAddress'];
} else {
  $myEmail = mb_strtolower($curUserInfo['Forename'] . '.' . $curUserInfo['Surname'] . ".volunteer-noreply@chesterlestreetasc.co.uk");
  $canReply = "As you don't have a club email address, we can't allow parents to directly reply to you. If parents reply, their email will go to our enquiries team, who can forward it on to you.";
}

$replyMe = false;
if (getUserOption($_SESSION['UserID'], 'NotifyReplyAddress')) {
  $replyMe = true;
}

$pagetitle = "Email " . $name;
include BASE_PATH . "views/header.php";
include BASE_PATH . "views/notifyMenu.php";

 ?>

<div class="container">

  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?=autoUrl("swimmers")?>">Swimmers</a></li>
      <li class="breadcrumb-item"><a href="<?=autoUrl("swimmers/" . $id)?>"><?=htmlspecialchars($mySwimmer['fn'])?> <?=htmlspecialchars(mb_substr($mySwimmer['sn'], 0, 1, 'utf-8'))?></a></li>
      <li class="breadcrumb-item active" aria-current="page">Contact parent</li>
    </ol>
  </nav>

	<h1>Contact a parent</h1>
	<p class="lead">Send an email</p>
  <hr>
	<form method="post" onkeypress="return event.keyCode != 13;">
    <div class="form-group">
			<label for="recipient">To</label>
			<input type="text" class="form-control" name="recipient" id="recipient"
      placeholder="Recipient" autocomplete="off" value="<?=htmlspecialchars($name . " <" . $email . ">")?>" disabled>
		</div>

    <div class="row">
      <div class="col-md">
        <div class="form-group">
          <label for="from">Send message as</label>
          <div class="custom-control custom-radio">
            <input type="radio" id="from-club" name="from" class="custom-control-input" value="club-sending-account" required>
            <label class="custom-control-label" for="from-club"><?=htmlspecialchars(env('CLUB_NAME'))?></label>
          </div>
          <div class="custom-control custom-radio">
            <input type="radio" id="from-user" name="from" class="custom-control-input" value="current-user" checked>
            <label class="custom-control-label" for="from-user"><?=htmlspecialchars($curUserInfo['Forename'] . ' ' . $curUserInfo['Surname'])?></label>
          </div>
          <div class="invalid-feedback">
            Choose a send-as option
          </div>
        </div>
      </div>

      <div class="col-md">
        <div class="form-group">
          <label for="ReplyToMe">Send replies to</label>
          <div class="custom-control custom-radio">
            <input type="radio" id="ReplyTo-Club" name="ReplyToMe" class="custom-control-input" value="0" <?php if (!$replyMe) { ?>checked<?php } ?> required>
            <label class="custom-control-label" for="ReplyTo-Club">Main club address</label>
          </div>
          <div class="custom-control custom-radio">
            <input type="radio" id="ReplyTo-Me" name="ReplyToMe" class="custom-control-input" value="1" <?php if (!$replyMe) { ?>disabled<?php } else { ?>checked<?php } ?>>
            <label class="custom-control-label" for="ReplyTo-Me">My reply-to email address</label>
          </div>
          <small class="form-text text-muted">
            <a href="<?=htmlspecialchars(autoUrl("notify/reply-to"))?>" target="_blank">Manage reply-to address</a>
          </small>
          <div class="invalid-feedback">
            Choose a reply-to address
          </div>
        </div>
      </div>
		</div>

		<div class="form-group">
			<label for="subject">Message Subject</label>
			<input type="text" class="form-control" name="subject" id="subject"
      placeholder="Message Subject" autocomplete="off">
		</div>

		<div class="form-group">
			<label for="message">Your Message</label>
			<textarea class="form-control" id="message" name="message" rows="10">
      </textarea>
			<small id="messageHelp" class="form-text text-muted">
        Styling will be stripped from this message
      </small>
		</div>

    <?=SCDS\CSRF::write()?>
    <?=SCDS\FormIdempotency::write()?>

		<p><button class="btn btn-success" id="submit" value="submitted" type="submit">Send the email</button></p>
	</form>
</div>

<!-- <script>
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
</script> -->
<?php $footer = new \SCDS\Footer();
$footer->addJS("public/js/tinymce/tinymce.min.js");
$footer->addJS("public/js/notify/TinyMCE.js");
$footer->render();
