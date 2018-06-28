<?php

$id = mysqli_real_escape_string($link, $id);
$userID = $_SESSION['UserID'];

// Get the swimmer name
$sqlSecurityCheck = "SELECT `MForename`, `MSurname`, `UserID` FROM `members`
WHERE MemberID = '$id';";
$resultSecurityCheck = mysqli_query($link, $sqlSecurityCheck);
$swimmersSecurityCheck = mysqli_fetch_array($resultSecurityCheck, MYSQLI_ASSOC);

if ($swimmersSecurityCheck['UserID'] != $userID) {
  halt(404);
}

$pagetitle;
if ($swimmersSecurityCheck['UserID'] == $userID && $resultSecurityCheck) {
  $pagetitle = "Edit: " . $swimmersSecurityCheck['MForename'] . " " .
  $swimmersSecurityCheck['MSurname'];
  $sqlSwim = "SELECT members.MForename, members.MForename, members.MMiddleNames,
  members.MSurname, users.EmailAddress, members.ASANumber, squads.SquadName,
  squads.SquadFee, squads.SquadCoach, squads.SquadTimetable, squads.SquadCoC,
  members.DateOfBirth, members.Gender, members.OtherNotes, members.AccessKey,
  memberPhotography.Website, memberPhotography.Social,
  memberPhotography.Noticeboard, memberPhotography.FilmTraining,
  memberPhotography.ProPhoto FROM (((members INNER JOIN users ON members.UserID =
  users.UserID) INNER JOIN squads ON members.SquadID = squads.SquadID) LEFT JOIN
  `memberPhotography` ON members.MemberID = memberPhotography.MemberID) WHERE
  members.MemberID = '$id';";
  $resultSwim = mysqli_query($link, $sqlSwim);
  $rowSwim = mysqli_fetch_array($resultSwim, MYSQLI_ASSOC);
  $age = date_diff(date_create($rowSwim['DateOfBirth']),
  date_create('today'))->y;
  $title = null;
  ?>
  <?php include BASE_PATH . "views/header.php"; ?>
  <div class="container">
    <div class="row align-items-center">
      <div class="col-sm-8">
        <h1>Editing <?php echo $swimmersSecurityCheck['MForename'] . ' ' .
        $swimmersSecurityCheck['MSurname']?></h1>
      </div>
      <div class="col-sm-4 text-right">
        <button type="submit" class="btn btn-success">Save</button> <a
        class="btn btn-dark" href="<?php echo autoUrl("swimmers/" . $id)?>">Exit
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
    <!-- Main Info Content -->
    <form method="post">
      <div class="my-3 p-3 bg-white rounded box-shadow">
    <div class="form-group">
      <label for="forename">Forename</label>
      <input type="text" class="form-control" id="forename" name="forename"
      placeholder="Enter a forename" value="<?php echo $rowSwim['MForename']; ?>"
      required>
    </div>
    <div class="form-group">
      <label for="middlenames">Middle Names</label>
      <input type="text" class="form-control" id="middlenames" name="middlenames" placeholder="Enter a middlename" value="<?php echo $rowSwim['MMiddleNames']; ?>">
    </div>
    <div class="form-group">
      <label for="surname">Surname</label>
      <input type="text" class="form-control" id="surname" name="surname" placeholder="Enter a surname" value="<?php echo $rowSwim['MSurname']; ?>" required>
    </div>
    <div class="form-group">
      <label for="datebirth">Date of Birth</label>
      <input type="date" class="form-control" id="datebirth" name="datebirth" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" placeholder="YYYY-MM-DD" value="<?php echo $rowSwim['DateOfBirth']; ?>" required>
    </div>
    <div class="form-group">
      <label for="asaregnumber">ASA Registration Number</label>
      <input type="test" class="form-control" id="asaregnumber" name="asaregnumber" placeholder="ASA Registration Numer" value="<?php echo $rowSwim['ASANumber']; ?>" readonly>
    </div>
    <?php if ($rowSwim['Gender'] == "Male") { ?>
    <div class="form-group">
      <label for="sex">Sex</label>
      <select class="custom-select" id="sex" name="sex" placeholder="Select">
        <option value="Male" selected>Male</option>
        <option value="Female">Female</option>
      </select>
    </div>
  <?php } else { ?>
    <div class="form-group">
      <label for="sex">Sex</label>
      <select class="custom-select" id="sex" name="sex" placeholder="Select">
        <option value="Male">Male</option>
        <option value="Female" selected>Female</option>
      </select>
    </div>
  <?php } ?>
    <div class="form-group">
      <label for="medicalNotes">Medical Notes</label>
      <div class="alert alert-info">
        <p class="mb-0">
          <strong>
            You no longer provide medical details on this page.
          </strong>
        </p>
        <p class="mb-0">
          Head to <a class="alert-link" href="<? echo autoUrl("swimmers/" . $id .
          "/medical"); ?>" target="_blank">the new medical form</a> to ensure
          your swimmer's medical details are up to date.
        </p>
      </div>
    </div>
    <div class="form-group">
      <label for="otherNotes">Other Notes</label>
      <textarea class="form-control" id="otherNotes" name="otherNotes" rows="3" placeholder="Tell us any other notes for coaches"><?php echo $rowSwim['OtherNotes']; ?></textarea>
    </div>
    <? if ($age < 18) { ?>
    <div class="form-group">
      <?
      $photo = [];
      if ($rowSwim['Website'] == 1) {
        $photo[0] = " checked ";
      }
      if ($rowSwim['Social'] == 1) {
        $photo[1] = " checked ";
      }
      if ($rowSwim['Noticeboard'] == 1) {
        $photo[2] = " checked ";
      }
      if ($rowSwim['FilmTraining'] == 1) {
        $photo[3] = " checked ";
      }
      if ($rowSwim['ProPhoto'] == 1) {
        $photo[4] = " checked ";
      } ?>
      <p>
  			I, <? echo getUserName($userID); ?> agree to photography of <?php echo
  			$rowSwim['MForename'] . " " . $rowSwim['MSurname']; ?> in the following
  			circumstances. Tick boxes only if you wish to grant us photography
  			permission.
  		</p>
  		<div class="custom-control custom-checkbox">
  			<input type="checkbox" value="1" class="custom-control-input"
  			name="webPhoto" id="webPhoto" <? echo $photo[0]; ?>>
  			<label class="custom-control-label" for="webPhoto">
  				Take photographs to use on the clubs website
  			</label>
  		</div>
  		<div class="custom-control custom-checkbox">
  			<input type="checkbox" value="1" class="custom-control-input"
  			name="socPhoto" id="socPhoto" <? echo $photo[1]; ?>>
  			<label class="custom-control-label" for="socPhoto">
  				Take photographs to use on social media sites
  			</label>
  		</div>
  		<div class="custom-control custom-checkbox">
  			<input type="checkbox" value="1" class="custom-control-input"
  			name="noticePhoto" id="noticePhoto" <? echo $photo[2]; ?>>
  			<label class="custom-control-label" for="noticePhoto">
  				Take photographs to use on club noticeboards
  			</label>
  		</div>
  		<div class="custom-control custom-checkbox">
  			<input type="checkbox" value="1" class="custom-control-input"
  			name="trainFilm" id="trainFilm" <? echo $photo[3]; ?>>
  			<label class="custom-control-label" for="trainFilm">
  				Filming for training purposes only
  			</label>
  		</div>
  		<div class="custom-control custom-checkbox">
  			<input type="checkbox" value="1" class="custom-control-input"
  			name="proPhoto" id="proPhoto" <? echo $photo[4]; ?>>
  			<label class="custom-control-label" for="proPhoto">
  				Employ a professional photographer (approved by the club) who will take
  				photographs in competitions and/or club events.
  			</label>
  		</div>
      <ul></ul>
    </div>
    <? } ?>
    <button type="submit" class="btn btn-outline-dark">Update</button>
    </div>

    <?php
    // Danger Zone at Bottom of Page
    $disconnectKey = generateRandomString(8);
    ?>
      <div class="alert alert-danger">
        <p><strong>Danger Zone</strong> <br>Actions here can be irreversible. Be careful what you do.</p>
        <div class="form-group">
          <label for="disconnect">Disconnect swimmer from your account with this Key <span class="mono">"<?php echo $disconnectKey; ?>"</span></label>
          <input type="text" class="form-control" id="disconnect" name="disconnect" aria-describedby="disconnectHelp" placeholder="Enter the key" onselectstart="return false" onpaste="return false;" onCopy="return false" onCut="return false" onDrag="return false" onDrop="return false" autocomplete=off>
          <small id="disconnectHelp" class="form-text">Enter the key in quotes above and press the <strong>Delete or Disconnect</strong> button. This will dissassociate this swimmer from your account in all of our systems. You will need a new Access Key to add the swimmer again.</small>
        </div>
        <input type="hidden" value="<?php echo $disconnectKey; ?>" name="disconnectKey">
        <div class="form-group">
          <label for="swimmerDeleteDanger">Delete this Swimmer with this Key <span class="mono">"<?php echo $rowSwim['AccessKey']; ?>"</span></label>
          <input type="text" class="form-control" id="swimmerDeleteDanger" name="swimmerDeleteDanger" aria-describedby="swimmerDeleteDangerHelp" placeholder="Enter the key" onselectstart="return false" onpaste="return false;" onCopy="return false" onCut="return false" onDrag="return false" onDrop="return false" autocomplete=off>
          <small id="swimmerDeleteDangerHelp" class="form-text">Enter the key in quotes above and press <strong>Delete or Disconnect</strong>. This will delete this swimmer from all of our systems.</small>
        </div>
        <p class="mb-0">
          <button type="submit" class="btn btn-danger">Delete or
          Disconnect</button>
        </p>
      </div>
    </form>
  </div>
  <?php } ?>
<?php include BASE_PATH . "views/footer.php";
