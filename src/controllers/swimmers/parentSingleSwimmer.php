<?php

// Get all countries
$countries = getISOAlpha2CountriesWithHomeNations();

$db = app()->db;
$tenant = app()->tenant;

$locked = mb_strtoupper($tenant->getKey('ASA_CLUB_CODE')) == 'UOSZ';

$update = false;

$getSwimmer = $db->prepare("SELECT members.MForename, members.MForename, members.MMiddleNames,
members.MSurname, users.EmailAddress, members.ASANumber,
members.DateOfBirth, members.Gender, members.OtherNotes, members.AccessKey,
memberPhotography.Website, memberPhotography.Social,
memberPhotography.Noticeboard, memberPhotography.FilmTraining,
memberPhotography.ProPhoto, members.Country FROM ((members INNER JOIN users ON members.UserID =
users.UserID) LEFT JOIN
`memberPhotography` ON members.MemberID = memberPhotography.MemberID) WHERE members.Tenant = ? AND members.MemberID = ? AND members.UserID = ?");
$getSwimmer->execute([
  $tenant->getId(),
  $id,
  $_SESSION['TENANT-' . app()->tenant->getId()]['UserID']
]);

$row = $getSwimmer->fetch(PDO::FETCH_ASSOC);

if ($row == null) {
  halt(404);
}

$age = date_diff(
  date_create($row['DateOfBirth']),
  date_create('today')
)->y;
$title = null;
?>
<?php include BASE_PATH . "views/header.php"; ?>
<div class="container">

  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= autoUrl("members") ?>">Members</a></li>
      <li class="breadcrumb-item"><a href="<?= autoUrl("members/" . $id) ?>"><?= htmlspecialchars($row["MForename"]) ?> <?= htmlspecialchars($row["MSurname"][0]) ?></a></li>
      <li class="breadcrumb-item active" aria-current="page">Edit</li>
    </ol>
  </nav>

  <form method="post">

    <div class="row align-items-center">
      <div class="col-sm-8">
        <h1>Editing <?= htmlspecialchars($row['MForename'] . ' ' . $row['MSurname']) ?></h1>
      </div>
      <div class="col-sm-4 text-end">
        <button type="submit" class="btn btn-success">Save</button> <a class="btn btn-dark btn-outline-light-d" href="<?= autoUrl("members/" . $id) ?>">Exit
          Edit Mode</a>
      </div>
    </div>

    <?php
    if ($update) { ?>
      <div class="alert alert-success">
        <strong>We have updated</strong>
        <ul class="mb-0"><?php
                          if ($forenameUpdate) { ?><li>Your first name</li><?php }
                                                                          if ($middlenameUpdate) { ?><li>Your middle name(s)</li><?php }
                                                                                                                                if ($surnameUpdate) { ?><li>Your last address</li><?php }
                                                                                                                                                                                if ($dateOfBirthUpdate) { ?><li>Your date of birth</li><?php }
                                                                                                                                                                                                                                      if ($sexUpdate) { ?><li>Your sex</li><?php }
                                                                                                                                                                                                                                                                          if ($otherNotesUpdate) { ?><li>Your other notes</li><?php }
                                                                                                                                                                                                                                                                                                                            if ($photoUpdate) { ?><li>Your photography permissions</li><?php } ?>
        </ul>
      </div>
    <?php } ?>

    <?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['SetMemberPassSuccess'])) { ?>
      <div class="alert alert-success">
        <p class="mb-0">
          <strong>Member password updated.</strong>
        </p>
      </div>
    <?php unset($_SESSION['TENANT-' . app()->tenant->getId()]['SetMemberPassSuccess']);
    } ?>

    <!-- Main Info Content -->
    <div class="row">
      <div class="col-md-8">

        <?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['SwimmerSaved']) && $_SESSION['TENANT-' . app()->tenant->getId()]['SwimmerSaved']) { ?>
          <div class="alert alert-success">
            <p class="mb-0">
              <strong>We have saved your changes</strong>
            </p>
          </div>
        <?php unset($_SESSION['TENANT-' . app()->tenant->getId()]['SwimmerSaved']);
        } ?>

        <?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['SwimmerNotSaved']) && $_SESSION['TENANT-' . app()->tenant->getId()]['SwimmerNotSaved']) { ?>
          <div class="alert alert-danger">
            <p class="mb-0">
              <strong>We could not save your changes</strong>
            </p>
            <p class="mb-0">
              Please check and try again
            </p>
          </div>
        <?php unset($_SESSION['TENANT-' . app()->tenant->getId()]['SwimmerNotSaved']);
        } ?>

        <?php if ($locked) { ?>
          <div class="alert alert-info">
            <p class="mb-0">
              <strong>Sport Sheffield have provided us with some of the details shown on this page</strong>
            </p>
            <p class="mb-0">
              If you need your name, date of birth or sex to be changed, please ask a committee member.
            </p>
          </div>
        <?php } ?>

        <div class="row">
          <div class="col-sm-4">
            <div class="mb-3">
              <label class="form-label" for="forename">Forename</label>
              <input type="text" class="form-control" id="forename" name="forename" placeholder="Enter a forename" value="<?= htmlspecialchars($row['MForename']) ?>" required <?php if ($locked) { ?>disabled<?php } ?>>
            </div>
          </div>
          <div class="col-sm-4">
            <div class="mb-3">
              <label class="form-label" for="middlenames">Middle Names</label>
              <input type="text" class="form-control" id="middlenames" name="middlenames" placeholder="Enter a middlename" value="<?= htmlspecialchars($row['MMiddleNames']) ?>" <?php if ($locked) { ?>disabled<?php } ?>>
            </div>
          </div>
          <div class="col-sm-4">
            <div class="mb-3">
              <label class="form-label" for="surname">Surname</label>
              <input type="text" class="form-control" id="surname" name="surname" placeholder="Enter a surname" value="<?= htmlspecialchars($row['MSurname']) ?>" required <?php if ($locked) { ?>disabled<?php } ?>>
            </div>
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label" for="datebirth">Date of Birth</label>
          <input type="date" class="form-control" id="datebirth" name="datebirth" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" placeholder="YYYY-MM-DD" value="<?= htmlspecialchars($row['DateOfBirth']) ?>" required <?php if ($locked) { ?>disabled<?php } ?>>
        </div>
        <div class="mb-3">
          <label class="form-label" for="asaregnumber">Swim England Registration Number</label>
          <input type="test" class="form-control" id="asaregnumber" name="asaregnumber" placeholder="Swim England Registration Numer" value="<?= htmlspecialchars($row['ASANumber']) ?>" readonly>
        </div>

        <div class="mb-3">
          <label class="form-label" for="country">Home Nations Country</label>
          <select class="form-select" id="country" name="country" placeholder="Select">
            <?php foreach ($countries as $key => $value) {
              $selected = '';
              if ($row['Country'] == $key) {
                $selected = ' selected ';
              } ?>
              <option value="<?= htmlspecialchars($key) ?>" <?= $selected ?>><?= htmlspecialchars($value) ?></option>
            <?php } ?>
          </select>
        </div>

        <?php if ($row['Gender'] == "Male") { ?>
          <div class="mb-3">
            <label class="form-label" for="sex">Sex</label>
            <select class="form-select" id="sex" name="sex" placeholder="Select" <?php if ($locked) { ?>disabled<?php } ?>>
              <option value="Male" selected>Male</option>
              <option value="Female">Female</option>
            </select>
          </div>
        <?php } else { ?>
          <div class="mb-3">
            <label class="form-label" for="sex">Sex</label>
            <select class="form-select" id="sex" name="sex" placeholder="Select" <?php if ($locked) { ?>disabled<?php } ?>>
              <option value="Male">Male</option>
              <option value="Female" selected>Female</option>
            </select>
          </div>
        <?php } ?>
        <div class="mb-3">
          <p>Medical Notes</p>
          <p class="mb-0">
            <a class="btn btn-primary" href="<?= autoUrl("members/" . $id .
                                                "/medical") ?>" target="_blank">Edit medical details</a>
          </p>
        </div>
        <div class="mb-3">
          <label class="form-label" for="otherNotes">Other Notes</label>
          <textarea class="form-control" id="otherNotes" name="otherNotes" rows="3" placeholder="Tell us any other notes for coaches"><?= htmlspecialchars($row['OtherNotes']) ?></textarea>
        </div>
        <?php if ($age < 18) { ?>
          <div class="mb-3">
            <?php
            $photo = [];
            if ($row['Website'] == 1) {
              $photo[0] = " checked ";
            }
            if ($row['Social'] == 1) {
              $photo[1] = " checked ";
            }
            if ($row['Noticeboard'] == 1) {
              $photo[2] = " checked ";
            }
            if ($row['FilmTraining'] == 1) {
              $photo[3] = " checked ";
            }
            if ($row['ProPhoto'] == 1) {
              $photo[4] = " checked ";
            } ?>
            <p>
              I, <?php echo getUserName($_SESSION['TENANT-' . app()->tenant->getId()]['UserID']); ?> agree to photography of
              <?= htmlspecialchars($row['MForename'] . " " .
                $row['MSurname']) ?> in the following circumstances. Tick boxes only
              if you wish to grant us photography permission.
            </p>
            <div class="form-check">
              <input type="checkbox" value="1" class="form-check-input" name="webPhoto" id="webPhoto" <?= $photo[0] ?>>
              <label class="form-check-label" for="webPhoto">
                Take photographs to use on the clubs website
              </label>
            </div>
            <div class="form-check">
              <input type="checkbox" value="1" class="form-check-input" name="socPhoto" id="socPhoto" <?= $photo[1] ?>>
              <label class="form-check-label" for="socPhoto">
                Take photographs to use on social media sites
              </label>
            </div>
            <div class="form-check">
              <input type="checkbox" value="1" class="form-check-input" name="noticePhoto" id="noticePhoto" <?= $photo[2] ?>>
              <label class="form-check-label" for="noticePhoto">
                Take photographs to use on club noticeboards
              </label>
            </div>
            <div class="form-check">
              <input type="checkbox" value="1" class="form-check-input" name="trainFilm" id="trainFilm" <?= $photo[3] ?>>
              <label class="form-check-label" for="trainFilm">
                Filming for training purposes only
              </label>
            </div>
            <div class="form-check">
              <input type="checkbox" value="1" class="form-check-input" name="proPhoto" id="proPhoto" <?= $photo[4] ?>>
              <label class="form-check-label" for="proPhoto">
                Employ a professional photographer (approved by the club) who will take
                photographs in competitions and/or club events.
              </label>
            </div>
            <ul></ul>
          </div>

        <?php } ?>

        <?= \SCDS\CSRF::write() ?>

        <p class="mb-5">
          <button type="submit" class="btn btn-success">Save Changes</button>
        </p>

        <?php
        // Danger Zone at Bottom of Page
        $disconnectKey = generateRandomString(8);
        ?>
        <div class="alert alert-danger">
          <p><strong>Danger Zone</strong> <br>Actions here can be irreversible. Be careful what you do.</p>
          <div class="mb-3">
            <label class="form-label" for="disconnect">Disconnect swimmer from your account with this key: <span class="font-monospace"><?= htmlspecialchars($disconnectKey) ?></span></label>
            <input type="text" class="form-control font-monospace" id="disconnect" name="disconnect" aria-describedby="disconnectHelp" placeholder="Enter the key" onselectstart="return false" onpaste="return false;" onCopy="return false" onCut="return false" onDrag="return false" onDrop="return false" autocomplete=off>
            <small id="disconnectHelp" class="form-text">Enter the key above and press the <strong>Delete or Disconnect</strong> button. This will dissassociate this swimmer from your account in all of our systems. You will need to request a new Access Key to add the swimmer again.</small>
          </div>
          <input type="hidden" value="<?= $disconnectKey ?>" name="disconnectKey">
          <div class="mb-3">
            <label class="form-label" for="swimmerDeleteDanger">Delete this Swimmer with this key: <span class="font-monospace"><?= htmlspecialchars($row['AccessKey']) ?></span></label>
            <input type="text" class="form-control font-monospace" id="swimmerDeleteDanger" name="swimmerDeleteDanger" aria-describedby="swimmerDeleteDangerHelp" placeholder="Enter the key" onselectstart="return false" onpaste="return false;" onCopy="return false" onCut="return false" onDrag="return false" onDrop="return false" autocomplete=off>
            <small id="swimmerDeleteDangerHelp" class="form-text">Enter the key above and press <strong>Delete or Disconnect</strong>. This will delete this swimmer from all of our systems.</small>
          </div>
          <p class="mb-0">
            <button type="submit" class="btn btn-danger">Delete or
              Disconnect</button>
          </p>
        </div>
      </div>
    </div>
  </form>
</div>

<?php $footer = new \SCDS\Footer();
$footer->render();
