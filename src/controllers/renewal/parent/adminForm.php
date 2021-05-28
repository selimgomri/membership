<?php

$partial_reg = isPartialRegistration();

$db = app()->db;
$tenant = app()->tenant;

$sql = "SELECT `ID` FROM `posts` WHERE `Type` = ? AND Tenant = ? LIMIT 1";
try {
  $query = $db->prepare($sql);
  $query->execute([
    'terms_conditions',
    $tenant->getId()
  ]);
} catch (PDOException $e) {
  halt(500);
}

$terms_Id = app()->tenant->getKey('TermsAndConditions');

$row = [];

$name = getUserName($_SESSION['TENANT-' . app()->tenant->getId()]['UserID']);

if ($partial_reg) {
  $sql = "SELECT members.MemberID, members.MForename, members.MSurname,
	members.DateOfBirth, memberPhotography.Website, memberPhotography.Social,
	memberPhotography.Noticeboard, memberPhotography.FilmTraining,
	memberPhotography.ProPhoto FROM (`members` LEFT JOIN `memberPhotography` ON
	members.MemberID = memberPhotography.MemberID) WHERE `UserID` = ? AND
	members.RR = 1 ORDER BY `MForename` ASC, `MSurname` ASC;";
} else {
  $sql = "SELECT members.MemberID, members.MForename, members.MSurname,
	members.DateOfBirth, memberPhotography.Website, memberPhotography.Social,
	memberPhotography.Noticeboard, memberPhotography.FilmTraining,
	memberPhotography.ProPhoto FROM (`members` LEFT JOIN `memberPhotography` ON
	members.MemberID = memberPhotography.MemberID) WHERE `UserID` = ? ORDER
	BY `MForename` ASC, `MSurname` ASC;";
}

$getInfo = $db->prepare($sql);
$getInfo->execute([$_SESSION['TENANT-' . app()->tenant->getId()]['UserID']]);
$row = $getInfo->fetchAll(PDO::FETCH_ASSOC);

$pagetitle = "Administration Form";
include BASE_PATH . "views/header.php";
include BASE_PATH . "views/renewalTitleBar.php";
?>

<div class="container">
  <div class="row">
    <div class="col-lg-8">
      <form method="post" class="needs-validation" novalidate>
        <h1>Club Administration Form</h1>
        <?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['ErrorState'])) {
          echo $_SESSION['TENANT-' . app()->tenant->getId()]['ErrorState'];
          unset($_SESSION['TENANT-' . app()->tenant->getId()]['ErrorState']);
        } ?>
        <p class="lead">
          <?php if ($partial_reg) { ?>
            You must now complete the Club Administration Form. This form relates to Data Protection, Photography
            Permissions and the agreement to the Terms and Conditions of the club.
          <?php } else { ?>
            In this next step you, and/or your members will need to agree to the terms and conditions of the club.
          <?php } ?>
        </p>

        <?php if (!$partial_reg) { ?>
          <p>
            This form relates to yourself and the members listed below.
          </p>

          <?php echo mySwimmersTable(null, $_SESSION['TENANT-' . app()->tenant->getId()]['UserID']); ?>

        <?php } ?>

        <h2>Data Protection</h2>
        <p>
          I understand that, in compliance with the General Data Protection
          Regulation, all efforts will be made to ensure that information is accurate,
          kept up to date and secure, and that it is used only in connection with the
          purposes of <?= htmlspecialchars(app()->tenant->getKey('CLUB_NAME')) ?>. Information will be disclosed only to
          those members of the club for whom it is appropriate, and relevant officers
          of the Amateur Swimming Association (Swim England) or British Swimming.
          Information will not be kept once a person has left the club.
        </p>

        <div class="mb-3">
          <div class="custom-control custom-checkbox">
            <input type="checkbox" value="1" class="custom-control-input" name="data-agree" id="data-agree" <?php if ($partial_reg) { ?>checked <?php } ?> required>
            <label class="custom-control-label" for="data-agree">
              I (<?= htmlspecialchars($name) ?>) agree to the use of my data by <?= htmlspecialchars(app()->tenant->getKey('CLUB_NAME')) ?> as
              outlined above
              <?php if ($partial_reg) { ?> (You have already registered with a previous member, so have already
                consented to these terms)
              <?php } ?>
            </label>
            <div class="invalid-feedback">
              You must consent to the use of your data by <?= htmlspecialchars(app()->tenant->getKey('CLUB_NAME')) ?>
            </div>
          </div>
        </div>

        <h2>Terms and Conditions of the Club</h2>
        <div id="ts-and_cs">
          <?= getPostContent($terms_Id) ?>
        </div>

        <p>
          The Member, and the Parent or Guardian (in the case of a person under the age of 18 years), hereby
          acknowledges that they have read the Club Rules and the Policies and Procedures Documentation of
          <?= htmlspecialchars(app()->tenant->getKey('CLUB_NAME')) ?>, copies of which can be obtained from <a href="<?= htmlspecialchars(app()->tenant->getKey('CLUB_WEBSITE')) ?>" target="_blank">our website</a>. I confirm my
          understanding and acceptance that such rules (as amended from time to time) shall govern my membership of the
          club. I further acknowledge and accept the responsibilities of membership as set out in these rules and
          understand that it is my duty to read and abide by them (including any amendments). By providing my agreement,
          I consent to be bound by the Code of Conduct, Constitution, Rules and Policy Documents of the
          club.
        </p>

        <div class="alert alert-warning">
          <p>
            <strong>
              Each member must agree to this section separately
            </strong>
          </p>
          <p class="mb-0">
            We've provided a box where each member can tick for themselves. Ticking this checkbox is legally equivalent
            to signing an agreement on paper.
          </p>
        </div>

        <?php
        for ($i = 0; $i < sizeof($row); $i++) {
          $id[$i] = $row[$i]['MemberID'];
          $age[$i] = date_diff(date_create($row[$i]['DateOfBirth']), date_create('today'))->y; ?>

          <div class="cell">

            <h3><?= htmlspecialchars($row[$i]['MForename'] . " " . $row[$i]['MSurname']) ?></h3>

            <div class="mb-3 <?php if ($age[$i] >= 12) {
                                      echo "mb-0";
                                    } ?>">
              <div class="custom-control custom-checkbox">
                <input type="checkbox" value="1" class="custom-control-input" name="<?= htmlspecialchars($id[$i]) ?>-tc-confirm" id="<?= htmlspecialchars($id[$i]) ?>-tc-confirm" required>
                <label class="custom-control-label" for="<?= htmlspecialchars($id[$i]) ?>-tc-confirm">
                  I, <?= htmlspecialchars($row[$i]['MForename'] . " " . $row[$i]['MSurname']) ?> agree to the Terms and
                  Conditions of <?= htmlspecialchars(app()->tenant->getKey('CLUB_NAME')) ?> as outlined above
                </label>
                <div class="invalid-feedback">
                  <?= htmlspecialchars($row[$i]['MForename']) ?> must agree to the terms and conditions of membership.
                </div>
              </div>
            </div>

            <?php if ($age[$i] < 12) { ?>

              <p>
                In the case of a member under the age of twelve years the Parent or
                Guardian undertakes to explain the content and implications of the Terms
                and Conditions of Membership of <?= htmlspecialchars(app()->tenant->getKey('CLUB_NAME')) ?>.
              </p>

              <div class="mb-3 mb-0">
                <div class="custom-control custom-checkbox">
                  <input type="checkbox" value="1" class="custom-control-input" name="<?= htmlspecialchars($id[$i]) ?>-pg-understanding" id="<?= htmlspecialchars($id[$i]) ?>-pg-understanding" required>
                  <label class="custom-control-label" for="<?= htmlspecialchars($id[$i]) ?>-pg-understanding">
                    I, <?= htmlspecialchars($name) ?> have explained the content and implications to
                    <?= htmlspecialchars($row[$i]['MForename'] . " " . $row[$i]['MSurname']) ?> and can confirm that they
                    understood.
                  </label>
                  <div class="invalid-feedback">
                    You, <?= htmlspecialchars($name) ?>, must confirm that you have explained the content and implications to <?= htmlspecialchars($row[$i]['MForename']) ?>
                  </div>
                </div>
              </div>

            <?php } ?>

          </div>

        <?php } ?>

        <?php
        for ($i = 0; $i < sizeof($row); $i++) {
          $y = 0;
          if ($age[$i] < 18) {
            $y++;
          }
        }

        if ($y > 0) { ?>

          <h2>Photography Consent</h2>
          <p>
            Please read the Swim England/<?= htmlspecialchars(app()->tenant->getKey('CLUB_NAME')) ?> Photography Policy before you continue to
            give or withold consent for photography.
          </p>

          <p>
            <?= htmlspecialchars(app()->tenant->getKey('CLUB_NAME')) ?> may wish to take photographs of individuals and groups of swimmers
            under the age of 18, which may include your child during their membership of
            <?= htmlspecialchars(app()->tenant->getKey('CLUB_NAME')) ?>. Photographs will only be taken and published in accordance with Swim
            England policy which requires the club to obtain the consent of the Parent or Guardian to take and use
            photographs under the following circumstances.
          </p>

          <p>
            It is entirely up to you whether or not you choose to allow us to take photographs and/or video of your child.
            You can change your choices at any time by heading to Swimmers.
          </p>

          <?php for ($i = 0; $i < sizeof($row); $i++) {
            if ($age[$i] < 18) {
              $photo = [];
              if ($row[$i]['Website'] == 1) {
                $photo[0] = " checked ";
              }
              if ($row[$i]['Social'] == 1) {
                $photo[1] = " checked ";
              }
              if ($row[$i]['Noticeboard'] == 1) {
                $photo[2] = " checked ";
              }
              if ($row[$i]['FilmTraining'] == 1) {
                $photo[3] = " checked ";
              }
              if ($row[$i]['ProPhoto'] == 1) {
                $photo[4] = " checked ";
              } ?>
              <div class="cell">
                <h3><?= htmlspecialchars($row[$i]['MForename'] . " " . $row[$i]['MSurname']) ?></h3>
                <p>
                  I, <?= htmlspecialchars($name) ?> agree to photography in the following circumstances. <strong>Tick boxes only
                    if you wish to grant us photography permission.</strong>
                </p>
                <div class="custom-control custom-checkbox">
                  <input type="checkbox" value="1" <?= $photo[0] ?> class="custom-control-input" name="<?= htmlspecialchars($id[$i]) ?>-photo-web" id="<?= htmlspecialchars($id[$i]) ?>-photo-web">
                  <label class="custom-control-label" for="<?= htmlspecialchars($id[$i]) ?>-photo-web">
                    Take photographs to use on the club's website
                  </label>
                </div>
                <div class="custom-control custom-checkbox">
                  <input type="checkbox" value="1" <?= $photo[1] ?> class="custom-control-input" name="<?= htmlspecialchars($id[$i]) ?>-photo-soc" id="<?= htmlspecialchars($id[$i]) ?>-photo-soc">
                  <label class="custom-control-label" for="<?= htmlspecialchars($id[$i]) ?>-photo-soc">
                    Take photographs to use on social media sites
                  </label>
                </div>
                <div class="custom-control custom-checkbox">
                  <input type="checkbox" value="1" <?= $photo[2] ?> class="custom-control-input" name="<?= htmlspecialchars($id[$i]) ?>-photo-nb" id="<?= htmlspecialchars($id[$i]) ?>-photo-nb">
                  <label class="custom-control-label" for="<?= htmlspecialchars($id[$i]) ?>-photo-nb">
                    Take photographs to use on club noticeboards
                  </label>
                </div>
                <div class="custom-control custom-checkbox">
                  <input type="checkbox" value="1" <?= $photo[3] ?> class="custom-control-input" name="<?= htmlspecialchars($id[$i]) ?>-photo-film" id="<?= htmlspecialchars($id[$i]) ?>-photo-film">
                  <label class="custom-control-label" for="<?= htmlspecialchars($id[$i]) ?>-photo-film">
                    Filming for training purposes only
                  </label>
                </div>
                <div class="custom-control custom-checkbox">
                  <input type="checkbox" value="1" <?= $photo[4] ?> class="custom-control-input" name="<?= htmlspecialchars($id[$i]) ?>-photo-pro" id="<?= htmlspecialchars($id[$i]) ?>-photo-pro">
                  <label class="custom-control-label" for="<?= htmlspecialchars($id[$i]) ?>-photo-pro">
                    Employ a professional photographer (approved by the club) who will
                    take photographs in competitions and/or club events.
                  </label>
                </div>
              </div>
          <?php }
          } ?>

        <?php } ?>

        <?php if ($y > 0) { ?>

          <h2>Medical Consent</h2>
          <p>For Parents and Guardians of members under 18 years</p>

        <?php } ?>

        <?php for ($i = 0; $i < sizeof($row); $i++) {
          if ($age[$i] < 18) { ?>
            <div class="cell">

              <h3>
                Consent for <?= htmlspecialchars($row[$i]['MForename'] . " " . $row[$i]['MSurname']) ?>
              </h3>
              <p>
                I confirm that <?= htmlspecialchars($row[$i]['MForename'] . " " . $row[$i]['MSurname']) ?> has not been
                advised by a doctor to not take part in physical activities unless under medical supervision.
              </p>

              <!-- <div class="custom-control custom-checkbox">
            <input type="checkbox" value="1" class="custom-control-input" name="<?= htmlspecialchars($id[$i]) ?>-med"
              id="<?= htmlspecialchars($id[$i]) ?>-med">
            <label class="custom-control-label" for="<?= htmlspecialchars($id[$i]) ?>-med">
              Confirm
            </label>
          </div> -->

              <p>
                I, <?= htmlspecialchars($name) ?> hereby give permission for the coach or other appropriate person to give the
                authority on my behalf for any medical or surgical treatment recommended by competent medical authorities,
                where it would be contrary to my child's interest, in the doctor's opinion, for any delay to be incurred by
                seeking my personal consent.
              </p>

              <div class="custom-control custom-checkbox">
                <input type="checkbox" value="1" class="custom-control-input" name="<?= htmlspecialchars($id[$i]) ?>-med" id="<?= htmlspecialchars($id[$i]) ?>-med" required>
                <label class="custom-control-label" for="<?= htmlspecialchars($id[$i]) ?>-med">
                  Confirm
                </label>
                <div class="invalid-feedback">
                  Please confirm permission
                </div>
              </div>

            </div>
        <?php }
        } ?>

        <div class="mb-3">
          <button type="submit" class="btn btn-success">Save and Continue</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php

$footer = new \SCDS\Footer();
$footer->addJs('public/js/NeedsValidation.js');
$footer->render();
