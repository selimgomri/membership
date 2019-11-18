<?php

/**
 * User mandate list for membership system
 * Displays list of users and their primary mandate
 */

global $db;
$getMandates = $db->query("SELECT Forename, Surname, users.UserID, Mandate, BankName, AccountHolderName, AccountNumEnd FROM ((users LEFT JOIN paymentPreferredMandate ON users.UserID = paymentPreferredMandate.UserID) LEFT JOIN paymentMandates ON paymentPreferredMandate.MandateID = paymentMandates.MandateID) WHERE AccessLevel = 'Parent' ORDER BY Surname ASC, Forename ASC");
$mandate = $getMandates->fetch(PDO::FETCH_ASSOC);

$pagetitle = 'User Mandates';
include BASE_PATH . 'views/header.php';

?>

<div class="container">
  <div class="row">
    <div class="col-lg-8">
      <h1>User mandates</h1>
      <p class="lead">Direct Debit mandates by user</p>
    </div>
  </div>

  <?php if ($mandate == null) { ?>
    <div class="alert alert-warning">
      <strong>There are no users to display</strong>
    </div>
  <?php } else { ?>
    <div class="list-group">
    <?php do { ?>
      <a href="<?=htmlspecialchars(autoUrl("users/" . $mandate['UserID']))?>" class="list-group-item list-group-item-action <?php if (!$mandate['BankName']) { ?> list-group-item-danger <?php }?>">
        <div class="row align-items-center">
          <div class="col-md-6">
            <h2 class="mb-0 h4"><?=htmlspecialchars($mandate['Surname'] . ', ' . $mandate['Forename'])?></h2>
            <div class="mb-3 d-md-none"></div>
          </div>
          <div class="col text-md-right">
            <?php if ($mandate['BankName']) { ?>
            <p class="mono mb-0"><?=htmlspecialchars(getBankName($mandate['BankName']))?></p>
            <p class="mono mb-0"><?=htmlspecialchars($mandate['AccountHolderName'])?>, &#0149;&#0149;&#0149;&#0149;&#0149;&#0149;<?=htmlspecialchars($mandate['AccountNumEnd'])?></p>
            <?php } else { ?>
            <p class="mono mb-0">No mandate</p>
            <p class="mono mb-0">Contact user</p>
            <?php } ?>
          </div>
        </div>
      </a>
    <?php } while ($mandate = $getMandates->fetch(PDO::FETCH_ASSOC)); ?>
    </div>
  <?php } ?>
</div>

<?php

include BASE_PATH . 'views/footer.php';