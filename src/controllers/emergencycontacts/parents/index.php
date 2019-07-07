<?php

$use_white_background = true;

global $db;

if ($renewal_trap) {
	header("Location: " . autoUrl("renewal/go"));
	exit();
}

$sql = $db->prepare("SELECT * FROM `users` WHERE `UserID` = ?");
$sql->execute([$_SESSION['UserID']]);
$row = $sql->fetch(PDO::FETCH_ASSOC);

$contacts = new EmergencyContacts($db);
$contacts->byParent($_SESSION['UserID']);

$contactsArray = $contacts->getContacts();

$pagetitle = "My Emergency Contacts";

$url_path = "emergency-contacts";
include BASE_PATH . 'views/header.php';
if (isset($renewal_trap) && $renewal_trap) {
	include BASE_PATH . 'views/renewalTitleBar.php';
	$url_path = "renewal/emergencycontacts";
}

?>

<div class="container">
  <div class="row">
    <div class="col-lg-8">
      <h1>
        My Emergency Contacts
      </h1>
      <p class="lead">
        Add, Edit or Remove Emergency Contacts
      </p>
      <p class="border-bottom border-gray pb-2 mb-0">
        We'll use these emergency contacts for all swimmers connected to your account if we can't reach you on your
        phone number. You can change your phone number in <a href="<?=autoUrl("my-account")?>">My Account</a>
      </p>
      <?php if (isset($_SESSION['AddNewSuccess'])) {
			echo $_SESSION['AddNewSuccess'];
			unset($_SESSION['AddNewSuccess']);
		} ?>
      <div class="mb-3">
        <div class="media pt-3">
          <div class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
            <div class="row align-items-center	">
              <div class="col-9">
                <p class="mb-0">
                  <strong class="d-block">
                    <?=htmlspecialchars($row['Forename'] . " " . $row['Surname'])?> (From My
                    Account)
                  </strong>
                  <a href="tel:<?=htmlspecialchars($row['Mobile'])?>">
                    <?=htmlspecialchars($row['Mobile'])?>
                  </a>
                </p>
              </div>
              <?php if (!$renewal_trap) { ?>
              <div class="col text-sm-right">
                <a href="<?=autoUrl("my-account")?>" class="btn
							btn-primary">
                  Edit
                </a>
              </div>
              <?php } ?>
            </div>
          </div>
        </div>
        <?php for ($i = 0; $i < sizeof($contactsArray); $i++) {
			?>
        <div class="media pt-3">
          <div class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
            <div class="row align-items-center">
              <div class="col-9">
                <p class="mb-0">
                  <strong class="d-block">
                    <?=htmlspecialchars($contactsArray[$i]->getName())?>
                  </strong>
                  <a href="tel:<?=htmlspecialchars($contactsArray[$i]->getContactNumber())?>">
                    <?=htmlspecialchars($contactsArray[$i]->getContactNumber())?>
                  </a>
                </p>
              </div>
              <div class="col text-sm-right">
                <a href="<?=autoUrl($url_path . "/edit/" . $contactsArray[$i]->getID())?>" class="btn btn-primary">
                  Edit
                </a>
              </div>
            </div>
          </div>
        </div>
        <?php
		} ?>
      </div>
      <p>
        <a href="<?php echo autoUrl($url_path . "/new"); ?>" class="btn btn-success">
          Add New
        </a>
      </p>
      <p>
        Please let people know if you have assigned them as your emergency contacts.
      </p>
    </div>
  </div>
</div>

<?php

include BASE_PATH . 'views/footer.php';