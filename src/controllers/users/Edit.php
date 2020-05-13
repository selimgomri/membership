<?php

$db = app()->db;
$tenant = app()->tenant;

$userInfo = $db->prepare("SELECT Forename, Surname, EmailAddress, Mobile, ASAMember, ASANumber, ASAPrimary, ASACategory, ASAPaid, ClubMember, ClubPaid, ClubCategory FROM users WHERE UserID = ? AND Tenant = ?");
$userInfo->execute([
  $id,
  $tenant->getId()
]);

$info = $userInfo->fetch(PDO::FETCH_ASSOC);

if ($info == null) {
  halt(404);
}

// Is this parent also a swimmer member?
$swimmerToo = $db->prepare("SELECT MemberID FROM members WHERE UserID = ? AND ASANumber = ?");
$swimmerToo->execute([
  $id,
  $info['ASANumber']
]);
$swimmerToo = $swimmerToo->fetchColumn();

// User object for permissions
$userObject = new \User($id);

function selected($userObject, $perm) {
  if ($userObject->hasPermission($perm)) {
    return " checked ";
  } else {
    return "";
  }
}

$pagetitle = htmlspecialchars("Edit " . $info['Forename'] . ' ' . $info['Surname']);

include BASE_PATH . 'views/header.php';

?>

<div class="container">

  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?=htmlspecialchars(autoUrl("users"))?>">Users</a></li>
      <li class="breadcrumb-item"><a href="<?=htmlspecialchars(autoUrl("users/" . $id))?>"><?=htmlspecialchars(mb_substr($info["Forename"], 0, 1, 'utf-8') . mb_substr($info["Surname"], 0, 1, 'utf-8'))?></a></li>
      <li class="breadcrumb-item active" aria-current="page">
        Edit
      </li>
    </ol>
  </nav>

  <h1>Edit <?=htmlspecialchars($info['Forename'])?></h1>
  <p class="lead">Edit personal details, membership options and more</p>

  <div class="row">
    <div class="col-lg-8">

      <?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['Success']) && $_SESSION['TENANT-' . app()->tenant->getId()]['Success']) { ?>
      <div class="alert alert-success">
        <p class="mb-0">User details updated.</p>
      </div>
      <?php unset($_SESSION['TENANT-' . app()->tenant->getId()]['Success']); } ?>

      <?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['InvalidEmail']) && $_SESSION['TENANT-' . app()->tenant->getId()]['InvalidEmail']) { ?>
      <div class="alert alert-danger">
        <p class="mb-0">The email you entered is invalid.</p>
      </div>
      <?php unset($_SESSION['TENANT-' . app()->tenant->getId()]['InvalidEmail']); } ?>

      <?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['UsedEmail']) && $_SESSION['TENANT-' . app()->tenant->getId()]['UsedEmail']) { ?>
      <div class="alert alert-danger">
        <p class="mb-0">The email you entered is already in use by another account.</p>
      </div>
      <?php unset($_SESSION['TENANT-' . app()->tenant->getId()]['UsedEmail']); } ?>

      <?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['InvalidPhone']) && $_SESSION['TENANT-' . app()->tenant->getId()]['InvalidPhone']) { ?>
      <div class="alert alert-danger">
        <p class="mb-0">The phone number you entered is invalid.</p>
      </div>
      <?php unset($_SESSION['TENANT-' . app()->tenant->getId()]['InvalidPhone']); } ?>

      <?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['GeneralError']) && $_SESSION['TENANT-' . app()->tenant->getId()]['GeneralError']) { ?>
      <div class="alert alert-danger">
        <p class="mb-0">An unspecified error occurred. Please try again.</p>
      </div>
      <?php unset($_SESSION['TENANT-' . app()->tenant->getId()]['GeneralError']); } ?>

      <form method="post" id="main-form" class="needs-validation" novalidate>
        <h2>Personal details</h2>

        <div class="form-row">
          <div class="col">
            <div class="form-group">
              <label for="first-name">First name</label>
              <input type="text" class="form-control" id="first-name" name="first-name" placeholder="First" value="<?=htmlspecialchars($info['Forename'])?>" required>
              <div class="invalid-feedback">
                Please provide a first name.
              </div>
            </div>
          </div>
          <div class="col">
            <div class="form-group">
              <label for="last-name">Last name</label>
              <input type="text" class="form-control" id="last-name" name="last-name" placeholder="Last" value="<?=htmlspecialchars($info['Surname'])?>" required>
              <div class="invalid-feedback">
                Please provide a surname name.
              </div>
            </div>
          </div>
        </div>

        <div class="form-group">
          <label for="email-address">Email address</label>
          <input type="email" class="form-control" id="email-address" name="email-address" value="<?=htmlspecialchars($info['EmailAddress'])?>" required data-valid="true" data-ajax-url="<?=htmlspecialchars(autoUrl("users/" . $id . "/edit/email"))?>">
          <div class="invalid-feedback" id="email-error-message">
            Please provide a valid email address.
          </div>
        </div>

        <div class="form-group">
          <label for="mobile-phone">Mobile</label>
          <input type="tel" class="form-control" id="mobile-phone" name="mobile-phone" value="<?=htmlspecialchars($info['Mobile'])?>" aria-describedby="phone-help" required>
          <small id="phone-help" class="form-text text-muted">You must use a UK phone number.</small>
          <div class="invalid-feedback">
            Please provide a mobile number.
          </div>
        </div>

        <div class="d-none">
          <h2>Swim England Membership</h2>
          <div class="form-group">
            <div class="custom-control custom-radio">
              <input type="radio" id="is-not-se-member" name="is-se-member" class="custom-control-input" <?php if (!bool($info['ASAMember'])) { ?>checked<?php } ?> value="0">
              <label class="custom-control-label" for="is-not-se-member"><?=htmlspecialchars($info['Forename'])?> is not a Swim England member</label>
            </div>
            <div class="custom-control custom-radio">
              <input type="radio" id="is-se-member" name="is-se-member" class="custom-control-input" <?php if (bool($info['ASAMember'])) { ?>checked<?php } ?> value="1">
              <label class="custom-control-label" for="is-se-member"><?=htmlspecialchars($info['Forename'])?> is a Swim England member</label>
            </div>
          </div>

          <div class="cell <?php if (!bool($info['ASAMember'])) { ?>d-none<?php } ?>" id="asa-details">

            <?php if ($swimmerToo) { ?>
              <p class="text-danger"> 
                <i class="fa fa-exclamation-circle" aria-hidden="true"></i> <?=htmlspecialchars($info['Forename'])?> is also a club sport member. Changes here will apply to the member too.
              </p>
            <?php } ?>

            <div class="form-row">
              <div class="col">
                <div class="form-group">
                  <label for="se-number">Swim England Number</label>
                  <input type="text" class="form-control mono" id="se-number" name="se-number" placeholder="123456" value="<?=htmlspecialchars($info['ASANumber'])?>">
                </div>
              </div>
              <div class="col">
                <div class="form-group">
                  <label for="last-name">Swim England Category</label>
                  <select class="custom-select" name="se-category" id="se-category">
                    <option value="1" <?php if ($info['ASACategory'] == 1) { ?>selected<?php } ?>>Category One</option>
                    <option value="2" <?php if ($info['ASACategory'] == 2) { ?>selected<?php } ?>>Category Two</option>
                    <option value="3" <?php if ($info['ASACategory'] == 3) { ?>selected<?php } ?>>Category Three</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="form-row">
              <div class="col">
                <div class="form-group">
                  <div class="custom-control custom-radio">
                    <input type="radio" id="is-se-primary-club" name="is-se-primary-club" class="custom-control-input" <?php if (bool($info['ASAPrimary'])) { ?>checked<?php } ?> value="1">
                    <label class="custom-control-label" for="is-se-primary-club">This is <?=htmlspecialchars($info['Forename'])?>'s paying club</label>
                  </div>
                  <div class="custom-control custom-radio">
                    <input type="radio" id="is-not-se-primary-club" name="is-se-primary-club" class="custom-control-input" <?php if (!bool($info['ASAPrimary'])) { ?>checked<?php } ?> value="0">
                    <label class="custom-control-label" for="is-not-se-primary-club"><?=htmlspecialchars($info['Forename'])?> pays with another club</label>
                  </div>
                </div>
              </div>
              <div class="col">
                <div class="form-group">
                  <div class="custom-control custom-radio">
                    <input type="radio" id="se-user-pays" name="se-club-pays" class="custom-control-input" <?php if (!bool($info['ASAPaid'])) { ?>checked<?php } ?>  <?php if (!bool($info['ASAPrimary'])) { ?>disabled<?php } ?> value="0">
                    <label class="custom-control-label" for="se-user-pays"><?=htmlspecialchars($info['Forename'])?> pays Swim England fees</label>
                  </div>
                  <div class="custom-control custom-radio">
                    <input type="radio" id="se-club-pays" name="se-club-pays" class="custom-control-input" <?php if (bool($info['ASAPaid'])) { ?>checked<?php } ?>  <?php if (!bool($info['ASAPrimary'])) { ?>disabled<?php } ?> value="1">
                    <label class="custom-control-label" for="se-club-pays">Club pays <?=htmlspecialchars($info['Forename'])?>'s Swim England fees</label>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>

        <div class="d-none">
          <h2 id="club-membership-status" data-swimmer-too="<?=htmlspecialchars(json_encode(bool($swimmerToo)))?>">Club Membership</h2>
          <?php if ($swimmerToo) { ?>
            <p class="text-danger"> 
              <i class="fa fa-exclamation-circle" aria-hidden="true"></i> <?=htmlspecialchars($info['Forename'])?> is also a club sport member. You can only edit their club membership details on their <a class="text-danger alert-link" target="_blank" href="<?=htmlspecialchars(autoUrl("members/" . $swimmerToo))?>">member page</a>.
            </p>
          <?php } else { ?>
          <div class="form-group">
            <div class="custom-control custom-radio">
              <input type="radio" id="is-not-club-member" name="is-club-member" class="custom-control-input" <?php if (!bool($info['ClubMember'])) { ?>checked<?php } ?> value="0">
              <label class="custom-control-label" for="is-not-club-member"><?=htmlspecialchars($info['Forename'])?> is not a club member</label>
            </div>
            <div class="custom-control custom-radio">
              <input type="radio" id="is-club-member" name="is-club-member" class="custom-control-input" <?php if (bool($info['ClubMember'])) { ?>checked<?php } ?> value="1">
              <label class="custom-control-label" for="is-club-member"><?=htmlspecialchars($info['Forename'])?> is a club member</label>
            </div>
          </div>

          <div class="cell <?php if (!bool($info['ClubMember'])) { ?>d-none<?php } ?>" id="club-details">

            <div class="form-group">
              <label for="last-name">Club Membership Category</label>
              <select class="custom-select" name="club-category" id="club-category" aria-describedby="club-category-help">
                <?php if ($info['ClubCategory'] == null) { ?><option selected>Select a category</option><?php } ?>
                <option value="1" <?php if ($info['ClubCategory'] == 1) { ?>selected<?php } ?>>Standard club membership</option>
                <option value="2" <?php if ($info['ClubCategory'] == 2) { ?>selected<?php } ?>>Standalone club membership</option>
              </select>
              <small id="club-category-help" class="form-text text-muted">Standard club membership counts this person as part of a family when calculating fees. Standalone membership is a fixed fee for each member.</small>
            </div>

            <div class="form-group mb-0">
              <div class="custom-control custom-radio">
                <input type="radio" id="club-user-pays" name="club-club-pays" class="custom-control-input" <?php if (!bool($info['ClubPaid'])) { ?>checked<?php } ?> value="0">
                <label class="custom-control-label" for="club-user-pays"><?=htmlspecialchars($info['Forename'])?> pays club membership fees</label>
              </div>
              <div class="custom-control custom-radio">
                <input type="radio" id="club-club-pays" name="club-club-pays" class="custom-control-input" <?php if (bool($info['ClubPaid'])) { ?>checked<?php } ?> value="1">
                <label class="custom-control-label" for="club-club-pays">Club pays <?=htmlspecialchars($info['Forename'])?>'s club membership fees</label>
              </div>
            </div>
          </div>

          <?php } ?>
        </div>

        <h2>Access permissions</h2>

        <div class="form-group">
          <p>
            Select access permissions for <?=htmlspecialchars($info['Forename'])?>
          </p>
          <div class="custom-control custom-switch">
            <input type="checkbox" class="custom-control-input" id="permissions-parent" name="permissions-parent" value="1" <?=selected($userObject, 'Parent')?>>
            <label class="custom-control-label" for="permissions-parent">Parent/user</label>
          </div>
          <div class="custom-control custom-switch">
            <input type="checkbox" class="custom-control-input" id="permissions-gala" name="permissions-gala" value="1" <?=selected($userObject, 'Galas')?>>
            <label class="custom-control-label" for="permissions-gala">Gala coordinator</label>
          </div>
          <div class="custom-control custom-switch">
            <input type="checkbox" class="custom-control-input" id="permissions-coach" name="permissions-coach" value="1" <?=selected($userObject, 'Coach')?>>
            <label class="custom-control-label" for="permissions-coach">Coach</label>
          </div>
          <div class="custom-control custom-switch">
            <input type="checkbox" class="custom-control-input" id="permissions-admin" name="permissions-admin" value="1" <?=selected($userObject, 'Admin')?>>
            <label class="custom-control-label" for="permissions-admin">Admin</label>
          </div>
        </div>

        <p>Users can switch access level by selecting their name in the main menu.</p>

        <p>
          <button type="submit" class="btn btn-success">
            Save changes
          </button>
        </p>
      </form>
    </div>
  </div>
</div>

<?php

$footer = new \SCDS\Footer();
$footer->addJs("public/js/users/Edit.js");
$footer->render();