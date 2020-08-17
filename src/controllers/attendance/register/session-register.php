<?php

function registerSheetGenerator($date, $sessionId)
{
  $db = app()->db;

  try {

    // Get session
    $session = TrainingSession::get($sessionId);

    if ($session->getDayOfWeekInt() != (int) $date->format('w')) {
      throw new Exception('Invalid');
    }

    $squads = $session->getSquads();

    ?>
    <div class="card mb-3">
      <div class="card-header">
        Take register
      </div>
      <div class="card-body">
        <p>
          <strong><?= htmlspecialchars($session->getName()) ?></strong> (<?= htmlspecialchars($session->getStartTime()->format('H:i')) ?> - <?= htmlspecialchars($session->getEndTime()->format('H:i')) ?>) - <em> <?php for ($i = 0; $i < sizeof($squads); $i++) { ?><?php if ($i > 0) { ?>, <?php } ?><?= htmlspecialchars($squads[$i]->getName()) ?><?php } ?></em>
        </p>
      </div>
    </div>
<?php

  } catch (Exception $e) {
  }
}
