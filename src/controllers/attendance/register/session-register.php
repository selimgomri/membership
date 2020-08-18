<?php

function registerSheetGenerator($date, $sessionId)
{
  $db = app()->db;

  $photoPermissionDescriptions = [
    'Website' => 'Take photos of this member for our website',
    'Social' => 'Take photos of this member for our social media',
    'Noticeboard' => 'Take photos of this member for our noticeboard',
    'FilmTraining' => 'Film this member for the purposes of training',
    'ProPhoto' => 'Take professional photographs of this member',
  ];

  try {

    // Get session
    $session = TrainingSession::get($sessionId);

    if ($session->getDayOfWeekInt() != (int) $date->format('w')) {
      throw new Exception('Invalid');
    }

    $squads = $session->getSquads();

    $register = $session->getRegister();

    $markdown = new ParsedownExtra();
    $markdown->setSafeMode(true);

?>
    <div class="card mb-3">
      <div class="card-header">
        Take register
      </div>
      <div class="card-body">
        <p class="mb-0">
          <strong><?= htmlspecialchars($session->getName()) ?></strong> (<?= htmlspecialchars($session->getStartTime()->format('H:i')) ?> - <?= htmlspecialchars($session->getEndTime()->format('H:i')) ?>)
        </p>

        <p class="mb-0">
          <em><?php for ($i = 0; $i < sizeof($squads); $i++) { ?><?php if ($i > 0) { ?>, <?php } ?><?= htmlspecialchars($squads[$i]->getName()) ?><?php } ?></em>
        </p>

        <?php if (sizeof($register) == 0) { ?>
          <div class="alert alert-warning">
            <p class="mb-0">
              <strong>There are no members for this session</strong>
            </p>
          </div>
        <?php } ?>
      </div>
      <?php if (sizeof($register) > 0) { ?>
        <ul class="list-group list-group-flush accordion" id="registerAccordion">
          <?php foreach ($register as $row) { ?>
            <li class="list-group-item py-3">
              <div class="bg-white my-n3 py-3 sticky-top">
                <div class="row align-items-center">
                  <div class="col">
                    <div class="custom-control custom-checkbox">
                      <input type="checkbox" class="custom-control-input" id="<?= htmlspecialchars($row['id']) ?>" <?php if (bool($row['tick'])) { ?>checked<?php } ?>>
                      <label class="custom-control-label d-block" for="<?= htmlspecialchars($row['id']) ?>"><?= htmlspecialchars($row['fn'] . ' ' . $row['sn']) ?></label>
                    </div>
                  </div>
                  <?php if (sizeof($row['medical']) > 0 || sizeof($row['photo']) > 0 || $row['notes'] || sizeof($row['contacts']) > 0) { ?>
                    <div class="col-auto">
                      <div class="btn-group">
                        <?php if (sizeof($row['medical']) > 0 || sizeof($row['photo']) > 0 || $row['notes']) { ?>
                          <button class="btn btn-sm btn-warning" type="button" data-toggle="collapse" data-target="#extra-info-<?= htmlspecialchars($row['id']) ?>" aria-expanded="false" aria-controls="extra-info-<?= htmlspecialchars($row['id']) ?>">Medical, photo &amp; notes info <span class="fa fa-caret-down"></span></button>
                        <?php } ?>
                        <?php if (sizeof($row['contacts']) > 0) { ?>
                          <button class="btn btn-sm btn-danger" type="button" data-toggle="collapse" data-target="#emergency-contacts-<?= htmlspecialchars($row['id']) ?>" aria-expanded="false" aria-controls="emergency-contacts-<?= htmlspecialchars($row['id']) ?>"><span class="fa fa-phone"></span> <span class="fa fa-caret-down"></span></button>
                        <?php } ?>
                      </div>
                    </div>
                  <?php } ?>
                </div>
              </div>
              <?php if (sizeof($row['contacts']) > 0) { ?>
                <div class="d-block">
                  <div class="collapse mb-n3" id="emergency-contacts-<?= htmlspecialchars($row['id']) ?>" data-parent="#registerAccordion">
                    <hr>
                    <p class="h5 mb-0">
                      Emergency contacts
                    </p>
                    <p>
                      In an emergency, dial one of the contact numbers shown below.
                    </p>

                    <div class="cell">
                      <div class="row">
                        <?php foreach ($row['contacts'] as $ec) { ?>

                          <div class="col-md-6 col-xl-4">
                            <div class="card card-body mb-2">
                              <div class="row align-items-center">
                                <div class="col-sm-6 col-md-12 col-lg-6">
                                  <div class="text-truncate"><strong><?= htmlspecialchars($ec->getName()) ?></strong></div>
                                  <div class="text-truncate"><?= htmlspecialchars($ec->getRelation()) ?></div>
                                  <div class="mb-2 d-sm-none d-md-flex d-lg-none"></div>
                                </div>
                                <div class="col">
                                  <a href="<?= htmlspecialchars($ec->getRFCContactNumber()) ?>" class="btn btn-block btn-success">
                                    <i class="fa fa-phone" aria-hidden="true"></i> <?= htmlspecialchars($ec->getNationalContactNumber()) ?>
                                  </a>
                                </div>
                              </div>
                            </div>
                          </div>

                        <?php } ?>
                      </div>
                    </div>
                  </div>
                </div>
              <?php } ?>
              <?php if (sizeof($row['medical']) > 0 || sizeof($row['photo']) > 0 || $row['notes']) { ?>
                <div class="d-block">
                  <div class="collapse mb-n3" id="extra-info-<?= htmlspecialchars($row['id']) ?>" data-parent="#registerAccordion">
                    <hr>
                    <?php if (sizeof($row['medical']) > 0) { ?>
                      <div class="cell">
                        <p class="h5 mb-3">
                          <strong>Medical</strong>
                        </p>

                        <div class="row mb-n3">
                          <?php if (isset($row['medical']['Conditions'])) { ?>
                            <div class="col-md pb-3">
                              <div class="card card-body border-danger h-100">
                                <p class="text-danger">
                                  <strong>
                                    Medical conditions
                                  </strong>
                                </p>
                                <div class="">
                                  <?= $markdown->text($row['medical']['Conditions']) ?>
                                </div>
                              </div>
                            </div>
                          <?php } ?>

                          <?php if (isset($row['medical']['Allergies'])) { ?>
                            <div class="col-md pb-3">
                              <div class="card card-body border-danger h-100">
                                <p class="text-danger">
                                  <strong>
                                    Allergies
                                  </strong>
                                </p>
                                <div class="">
                                  <?= $markdown->text($row['medical']['Allergies']) ?>
                                </div>
                              </div>
                            </div>
                          <?php } ?>

                          <?php if (isset($row['medical']['Medication'])) { ?>
                            <div class="col-md pb-3">
                              <div class="card card-body border-danger h-100">
                                <p class="text-danger">
                                  <strong>
                                    Medication
                                  </strong>
                                </p>
                                <div class="">
                                  <?= $markdown->text($row['medical']['Medication']) ?>
                                </div>
                              </div>
                            </div>
                          <?php } ?>
                        </div>
                      </div>
                    <?php } ?>

                    <?php if (sizeof($row['photo']) > 0) { ?>
                      <div class="cell">
                        <p class="h5 mb-3">
                          <strong>Photo restrictions</strong>
                        </p>
                        <div class="row mb-n3">
                          <div class="col-md pb-3">
                            <div class="card card-body border-danger h-100">
                              <p class="text-danger">
                                <i class="fa fa-exclamation-circle" aria-hidden="true"></i> <strong>You must not</strong>
                              </p>

                              <ul class="list-unstyled mb-0">
                                <?php foreach ($row['photo'] as $disallowed => $bool) { ?>
                                  <li><?= htmlspecialchars($photoPermissionDescriptions[$disallowed]) ?></li>
                                <?php } ?>
                              </ul>
                            </div>
                            <div class="d-md-none mb-3"></div>
                          </div>

                          <?php if (sizeof($row['photo']) < 5) { ?>
                            <div class="col-md pb-3">
                              <div class="card card-body border-success h-100">
                                <p class="text-success">
                                  <i class="fa fa-check-circle" aria-hidden="true"></i> <strong>You may</strong>
                                </p>

                                <ul class="list-unstyled mb-0">
                                  <?php foreach ($photoPermissionDescriptions as $key => $text) {
                                    if (!isset($row['photo'][$key])) { ?>
                                      <li><?= htmlspecialchars($text) ?></li>
                                  <?php }
                                  } ?>
                                </ul>
                              </div>
                            </div>
                          <?php } ?>
                        </div>
                      </div>
                    <?php } ?>

                    <?php if ($row['notes']) { ?>
                      <div class="cell">
                        <p class="h5 mb-3">
                          <strong>Other notes</strong>
                        </p>
                        <div>
                          <?= $markdown->text($row['notes']) ?>
                        </div>
                      </div>
                    <?php } ?>
                  </div>
                </div>
              <?php } ?>
            </li>
          <?php } ?>
        </ul>
      <?php } ?>
    </div>
<?php

  } catch (Exception $e) {
    pre($e);
  }
}
