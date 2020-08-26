<?php

$db = app()->db;
$tenant = app()->tenant;
$pagetitle = 'COVID Health Screening';

$squad = null;

$user = app()->user;
if ($user->hasPermission('Admin') || $user->hasPermission('Coach') || $user->hasPermission('Galas')) {
  $showSquadOpts = true;
  $getSquad = $db->prepare("SELECT SquadName, SquadID FROM squads WHERE squads.Tenant = ? AND squads.SquadID = ?;");
  $getSquad->execute([
    $tenant->getId(),
    $id,
  ]);
  $squad = $getSquad->fetch(PDO::FETCH_ASSOC);
} else {
  $getSquad = $db->prepare("SELECT SquadName, SquadID FROM squads INNER JOIN squadReps ON squads.SquadID = squadReps.Squad WHERE squadReps.User = ? AND squadReps.Squad = ?;");
  $getSquad->execute([
    $_SESSION['TENANT-' . app()->tenant->getId()]['UserID'],
    $id,
  ]);
  $squad = $getSquad->fetch(PDO::FETCH_ASSOC);
}

if (!$squad) {
  halt(404);
}

$getMembers = $db->prepare("SELECT MForename, MSurname, MemberID FROM members INNER JOIN squadMembers ON squadMembers.Member = members.MemberID WHERE squadMembers.Squad = ? ORDER BY MForename ASC, MSurname ASC;");
$getMembers->execute([
  $id,
]);
$member = $getMembers->fetch(PDO::FETCH_ASSOC);

$getLatestCompletion = $db->prepare("SELECT `ID`, `DateTime`, `OfficerApproval`, `ApprovedBy`, `Forename`, `Surname`, `Document` FROM covidHealthScreen LEFT JOIN users ON covidHealthScreen.ApprovedBy = users.UserID WHERE Member = ? ORDER BY `DateTime` DESC LIMIT 1");

$pagetitle = htmlspecialchars($squad['SquadName']) . ' - Health Screening';

include BASE_PATH . 'views/header.php';

?>

<div class="bg-light mt-n3 py-3 mb-3">
  <div class="container">

    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= htmlspecialchars(autoUrl('covid')) ?>">COVID</a></li>
        <li class="breadcrumb-item"><a href="<?= htmlspecialchars(autoUrl('covid/health-screening')) ?>">Screening</a></li>
        <li class="breadcrumb-item"><a href="<?= htmlspecialchars(autoUrl('covid/health-screening#squads')) ?>">Squads</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($squad['SquadName']) ?></li>
      </ol>
    </nav>

    <div class="row align-items-center">
      <div class="col-lg-8">
        <h1>
          <?= htmlspecialchars($squad['SquadName']) ?> Health Screening
        </h1>
        <p class="lead mb-0">
          Keeping everyone safe
        </p>
      </div>
    </div>
  </div>
</div>

<div class="container">

  <div class="row">

    <div class="col-lg-8">

      <?php if ($member) { ?>

        <ul class="list-group mb-3" id="table-area">
          <?php do {
            $getLatestCompletion->execute([
              $member['MemberID']
            ]);

            $latest = $getLatestCompletion->fetch(PDO::FETCH_ASSOC);
          ?>
            <li class="list-group-item">
              <div class="row align-items-center">
                <div class="col-sm">
                  <h3 class="h6 mb-1"><?= htmlspecialchars($member['MForename'] . ' ' . $member['MSurname']) ?><?php if ($latest && bool($latest['OfficerApproval'])) { ?> <span class="text-success"><i class="fa fa-check-circle" aria-hidden="true"></i> Approved by <?= htmlspecialchars($latest['Forename'] . ' ' . $latest['Surname']) ?></span><?php } ?></h3>
                  <?php if ($latest) {
                    $time = new DateTime($latest['DateTime'], new DateTimeZone('UTC'));
                    $time->setTimezone(new DateTimeZone('Europe/London'));
                  ?>
                    <p class="mb-0">
                      Latest submission <?= htmlspecialchars($time->format('H:i, j F Y')) ?><?php if (!bool($latest['OfficerApproval']) && !$latest['ApprovedBy']) { ?> <span class="text-warning"><i class="fa fa-minus-circle" aria-hidden="true"></i> Awaiting approval</span><?php } else if (!bool($latest['OfficerApproval']) && $latest['ApprovedBy']) { ?> <span class="text-danger"><i class="fa fa-times-circle" aria-hidden="true"></i> Rejected by <?= htmlspecialchars($latest['Forename'] . ' ' . $latest['Surname']) ?></span><?php } ?>
                    </p>
                    <?php if (!bool($latest['OfficerApproval'])) { ?>
                      <p class="mt-3 mb-0">
                        <button class="btn btn-primary review-button" type="button" data-review-id="<?= htmlspecialchars($latest['ID']) ?>" data-review-document="<?= htmlspecialchars($latest['Document']) ?>" data-member-name="<?= htmlspecialchars($member['MForename'] . ' ' . $member['MSurname']) ?>">
                          Review<?php if ($latest['ApprovedBy']) { ?> again<?php } ?>
                        </button>
                      </p>
                    <?php } ?>
                  <?php } else { ?>
                    <p class="mb-0">
                      No survey submitted
                    </p>
                  <?php } ?>
                </div>
                <div class="col-auto">
                  <div class="btn-group">
                    <?php if ($latest) { ?>
                      <a href="<?= htmlspecialchars(autoUrl('covid/health-screening/members/' . $member['MemberID'])) ?>" class="btn btn-dark">View all</a>
                    <?php } ?>
                    <a href="<?= htmlspecialchars(autoUrl('covid/health-screening/members/' . $member['MemberID'] . '/new-survey')) ?>" class="btn btn-success">New submission</a>
                  </div>
                </div>
              </div>
            </li>
          <?php } while ($member = $getMembers->fetch(PDO::FETCH_ASSOC)); ?>
        </ul>
      <?php } else { ?>
        <div class="alert alert-warning">
          <p class="mb-0">
            <strong>There are no members in <?= htmlspecialchars($squad['SquadName']) ?></strong>
          </p>
        </div>
      <?php } ?>

      <p>
        Information about COVID-19 is available <a href="https://www.nhs.uk/conditions/coronavirus-covid-19/" target="_blank">on the NHS website</a>.
      </p>
    </div>

  </div>
</div>

<div class="modal " id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="reviewModalLabel">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="reviewModalBody">
        ...
      </div>
      <div class="modal-footer" id="reviewModalFooter">
        <div class="d-block w-100">
          <div class="row">
            <div class="col">
              <p class="mb-0">
                <button type="button" class="btn btn-dark" data-dismiss="modal">Close</button>
              </p>
            </div>
            <div class="col-auto">
              <p class="mb-0">
                <button type="button" class="btn btn-danger review-confirm-button" id="reject-button" data-action="reject">Reject</button>
                <button type="button" class="btn btn-success review-confirm-button" id="approve-button" data-action="approve">Approve</button>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="js-opts" data-ajax-url="<?= htmlspecialchars(autoUrl('covid/health-screening/approval')) ?>"></div>

<script>
  const sections = {
    'confirmed-infection': 'Confirmed infection',
    'exposure': 'Recent exposure',
    'underlying-medical': 'Underlying medical conditions',
    'live-with-shielder': 'Lives with shielder',
    'understand-return': 'Understands return to training protocols',
    'able-to-train': 'Able to train',
    'sought-advice': 'Sought advice',
    'advice-received': 'Advice received',
  }

  const options = document.getElementById('js-opts').dataset;

  let table = document.getElementById('table-area');
  if (table) {
    table.addEventListener('click', (event) => {
      if (event.target.tagName == 'BUTTON' && event.target.classList.contains('review-button')) {
        let button = event.target;
        let reviewId = button.dataset.reviewId;

        // Get review
        let review = JSON.parse(button.dataset.reviewDocument);

        document.getElementById('reviewModalLabel').textContent = 'Review submission for ' + button.dataset.memberName;
        body = document.getElementById('reviewModalBody');
        body.innerHTML = '';

        let rowNode, col1, col2;
        rowNode = document.createElement('DL');
        rowNode.classList.add('row', 'mb-0');

        let keys = Object.keys(review.form);
        let md = window.markdownParser;

        for (let i = 0; i < keys.length; i++) {
          col1 = document.createElement('DT');
          col1.classList.add('col-sm-3');
          col1.textContent = sections[keys[i]];
          col2 = document.createElement('DD');
          col2.classList.add('col-sm-9');

          console.log(review.form[keys[i]]);

          let stateTextNode = document.createTextNode('');
          if (review.form[keys[i]].state) {
            stateTextNode.textContent = 'Yes';
          } else {
            stateTextNode.textContent = 'No';
          }
          col2.appendChild(stateTextNode);

          if (review.form[keys[i]].notes) {
            let card = document.createElement('DIV');
            card.classList.add('card', 'card-body', 'p-2', 'pb-0');
            let html = md.render(review.form[keys[i]].notes);
            card.innerHTML = html;
            col2.appendChild(card);
          }

          rowNode.appendChild(col1);
          rowNode.appendChild(col2);
        }

        body.appendChild(rowNode);

        // Set id on buttons
        document.getElementById('approve-button').dataset.submission = reviewId;
        document.getElementById('reject-button').dataset.submission = reviewId;

        $('#reviewModal').modal('show');
      }
    });
  }

  document.getElementById('reviewModalFooter').addEventListener('click', (event) => {
    if (event.target.tagName == 'BUTTON' && event.target.classList.contains('review-confirm-button')) {
      let button = event.target;

      // Get type
      let type = button.dataset.action;
      let submission = button.dataset.submission;

      var req = new XMLHttpRequest();
      req.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
          let json = JSON.parse(this.responseText);
          if (json.status == 200) {
            location.reload();
          } else {
            alert(json.error);
          }
        } else if (this.readyState == 4) {
          // Not ok
          alert('An error occurred. Your action was not saved.');
        }
      }
      req.open('POST', options.ajaxUrl, true);
      req.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
      req.send('submission=' + encodeURI(submission) + '&type=' + encodeURI(type));
    }
  });
</script>

<?php

$footer = new \SCDS\Footer();
$footer->render();
