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

    <div class="form-group">
			<label for="from">From</label>
      <select class="custom-select" name="from" id="from">
        <option value="current-user" selected><?=htmlspecialchars($myName . " <" . $myEmail . ">")?></option>
        <option value="club-sending-account"><?=htmlspecialchars(env('CLUB_NAME') . " <noreply@" . env('EMAIL_DOMAIN') . ">")?></option>
      </select>
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

    <?php if ($canReply) { ?>
      <p><?=$canReply?></p>
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
</script>
<?php $footer = new \SCDS\Footer();
$footer->render();
