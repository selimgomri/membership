<?php

$db = app()->db;

$start = 0;

$perPage = 20;

if (isset($_GET['page']) && ((int) $_GET['page']) != 0) {
  $page = (int) $_GET['page'];
  $start = ($page - 1) * $perPage;
} else {
  $page = 1;
}

if ($page == 1) {
  // Clear old logs
  $date = new DateTime('-29 days', new DateTimeZone('Europe/London'));
  $date->setTimezone(new DateTimeZone('Europe/London'));
  $date = $date->format('Y-m-d H:i:s');

  $delete = $db->prepare("DELETE FROM auditLogging WHERE `Event` = 'HTTP_REQUEST' AND `Time` < ?");
  $delete->execute([
    $date
  ]);
}

$numLogs = $numPages = $sql = $row = $user = null;

if (isset($_GET['user'])) {
  try {
    $user = new User($_GET['user'], false);
  } catch (Exception $e) {
    halt(404);
  }

  $sql = $db->prepare("SELECT COUNT(*) FROM auditLogging INNER JOIN users ON auditLogging.User = users.UserID WHERE auditLogging.User = ? AND `Event` = 'HTTP_REQUEST'");
  $sql->execute([
    $_GET['user']
  ]);
  $numLogs  = $sql->fetchColumn();
  $numPages = ((int)($numLogs / $perPage)) + 1;

  if ($start > $numLogs) {
    halt(404);
  }

  $sql = $db->prepare("SELECT `Forename`, `Surname`, `UserID`, `Active`, auditLogging.ID, `Event`, `Description`, `Time`, tenants.ID TID FROM `auditLogging` INNER JOIN `users` ON auditLogging.User = users.UserID INNER JOIN tenants ON users.Tenant = tenants.ID WHERE auditLogging.User = :user AND `Event` = 'HTTP_REQUEST' ORDER
BY `Time` DESC LIMIT :offset, :num");
  $sql->bindValue(':user', $_GET['user'], PDO::PARAM_INT);
  $sql->bindValue(':offset', $start, PDO::PARAM_INT);
  $sql->bindValue(':num', $perPage, PDO::PARAM_INT);
  $sql->execute();
} else {
  $sql = $db->query("SELECT COUNT(*) FROM auditLogging INNER JOIN users ON auditLogging.User = users.UserID WHERE `Event` = 'HTTP_REQUEST'");

  $numLogs  = $sql->fetchColumn();
  $numPages = ((int)($numLogs / $perPage)) + 1;

  if ($start > $numLogs) {
    halt(404);
  }

  $sql = $db->prepare("SELECT `Forename`, `Surname`, `UserID`, `Active`, auditLogging.ID, `Event`, `Description`, `Time`, tenants.ID TID FROM `auditLogging` INNER JOIN `users` ON auditLogging.User = users.UserID INNER JOIN tenants ON users.Tenant = tenants.ID WHERE `Event` = 'HTTP_REQUEST' ORDER
BY `Time` DESC LIMIT :offset, :num");
  $sql->bindValue(':offset', $start, PDO::PARAM_INT);
  $sql->bindValue(':num', $perPage, PDO::PARAM_INT);
  $sql->execute();
}

$row = $sql->fetch(PDO::FETCH_ASSOC);

$pagetitle = "HTTP Requests - Audit Logs";

include BASE_PATH . "views/root/header.php";

?>

<div class="container-xl">

  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= htmlspecialchars(autoUrl("admin/audit")) ?>">Audit</a></li>
      <li class="breadcrumb-item active" aria-current="page">Logs</li>
    </ol>
  </nav>

  <div class="row">
    <div class="col">
      <h1>HTTP Requests</h1>

      <?php if ($user) { ?>
        <p class="lead"><?= htmlspecialchars($user->getFullName()) ?>, <?= htmlspecialchars($user->getEmail()) ?></p>
      <?php } ?>

      <p class="lead">
        Page <?= htmlspecialchars($page) ?>
      </p>

      <?php if ($row) { ?>

        <ul class="list-group">
          <?php do {

            $time = new DateTime($row['Time'], new DateTimeZone('UTC'));
            $time->setTimezone(new DateTimeZone('Europe/London'));
            $tenant = Tenant::fromId($row['TID']);

          ?>
            <li class="list-group-item">
              <div class="row">
                <div class="col">
                  <strong><a target="_blank" href="<?= htmlspecialchars(autoUrl($tenant->getCodeId() . '/users/' . $row['UserID'])) ?>"><?= htmlspecialchars($row['Forename'] . ' ' . $row['Surname']) ?></a></strong>
                </div>
                <div class="col text-center">
                  <strong><?= htmlspecialchars($time->format('H:i:s d/m/Y')) ?></strong>
                </div>
                <div class="col text-end">
                  <strong><a target="_blank" href="<?= htmlspecialchars(autoUrl($tenant->getCodeId())) ?>"><?= htmlspecialchars($tenant->getName()) ?></a></strong>
                </div>
              </div>
              <p class="mb-0 font-monospace">
                <?= htmlspecialchars($row['Event']) ?>
              </p>
              <p class="mb-0">
                <?= htmlspecialchars($row['Description']) ?>
              </p>
            </li>
          <?php } while ($row = $sql->fetch(PDO::FETCH_ASSOC)); ?>
        </ul>


        <nav aria-label="Page navigation">
          <ul class="pagination">
            <?php if ($numLogs <= $perPage) { ?>
              <li class="page-item active"><a class="page-link" href="<?php echo autoUrl("admin/audit/requests?page="); ?><?php echo $page ?><?php if ($user) { ?>&user=<?= htmlspecialchars($user->getId()) ?><?php } ?>"><?php echo $page ?></a></li>
            <?php } else if ($numLogs <= 20) { ?>
              <?php if ($page == 1) { ?>
                <li class="page-item active"><a class="page-link" href="<?php echo autoUrl("admin/audit/requests?page="); ?><?php echo $page ?><?php if ($user) { ?>&user=<?= htmlspecialchars($user->getId()) ?><?php } ?>"><?php echo $page ?></a></li>
                <li class="page-item"><a class="page-link" href="<?php echo autoUrl("admin/audit/requests?page="); ?><?php echo $page + 1 ?><?php if ($user) { ?>&user=<?= htmlspecialchars($user->getId()) ?><?php } ?>"><?php echo $page + 1 ?></a></li>
                <li class="page-item"><a class="page-link" href="<?php echo autoUrl("admin/audit/requests?page="); ?><?php echo $page + 1 ?><?php if ($user) { ?>&user=<?= htmlspecialchars($user->getId()) ?><?php } ?>">Next</a></li>
              <?php } else { ?>
                <li class="page-item"><a class="page-link" href="<?php echo autoUrl("admin/audit/requests?page="); ?><?php echo $page - 1 ?><?php if ($user) { ?>&user=<?= htmlspecialchars($user->getId()) ?><?php } ?>">Previous</a></li>
                <li class="page-item"><a class="page-link" href="<?php echo autoUrl("admin/audit/requests?page="); ?><?php echo $page - 1 ?><?php if ($user) { ?>&user=<?= htmlspecialchars($user->getId()) ?><?php } ?>"><?php echo $page - 1 ?></a></li>
                <li class="page-item active"><a class="page-link" href="<?php echo autoUrl("admin/audit/requests?page="); ?><?php echo $page ?><?php if ($user) { ?>&user=<?= htmlspecialchars($user->getId()) ?><?php } ?>"><?php echo $page ?></a></li>
              <?php } ?>
            <?php } else { ?>
              <?php if ($page == 1) { ?>
                <li class="page-item active"><a class="page-link" href="<?php echo autoUrl("admin/audit/requests?page="); ?><?php echo $page ?><?php if ($user) { ?>&user=<?= htmlspecialchars($user->getId()) ?><?php } ?>"><?php echo $page ?></a></li>
                <li class="page-item"><a class="page-link" href="<?php echo autoUrl("admin/audit/requests?page="); ?><?php echo $page + 1 ?><?php if ($user) { ?>&user=<?= htmlspecialchars($user->getId()) ?><?php } ?>"><?php echo $page + 1 ?></a></li>
                <li class="page-item"><a class="page-link" href="<?php echo autoUrl("admin/audit/requests?page="); ?><?php echo $page + 2 ?><?php if ($user) { ?>&user=<?= htmlspecialchars($user->getId()) ?><?php } ?>"><?php echo $page + 2 ?></a></li>
                <li class="page-item"><a class="page-link" href="<?php echo autoUrl("admin/audit/requests?page="); ?><?php echo $page + 1 ?><?php if ($user) { ?>&user=<?= htmlspecialchars($user->getId()) ?><?php } ?>">Next</a></li>
              <?php } else { ?>
                <li class="page-item"><a class="page-link" href="<?php echo autoUrl("admin/audit/requests?page="); ?><?php echo $page - 1 ?><?php if ($user) { ?>&user=<?= htmlspecialchars($user->getId()) ?><?php } ?>">Previous</a></li>
                <?php if ($page > 2) { ?>
                  <li class="page-item"><a class="page-link" href="<?php echo autoUrl("admin/audit/requests?page="); ?><?php echo $page - 2 ?><?php if ($user) { ?>&user=<?= htmlspecialchars($user->getId()) ?><?php } ?>"><?php echo $page - 2 ?></a></li>
                <?php } ?>
                <li class="page-item"><a class="page-link" href="<?php echo autoUrl("admin/audit/requests?page="); ?><?php echo $page - 1 ?><?php if ($user) { ?>&user=<?= htmlspecialchars($user->getId()) ?><?php } ?>"><?php echo $page - 1 ?></a></li>
                <li class="page-item active"><a class="page-link" href="<?php echo autoUrl("admin/audit/requests?page="); ?><?php echo $page ?><?php if ($user) { ?>&user=<?= htmlspecialchars($user->getId()) ?><?php } ?>"><?php echo $page ?></a></li>
                <?php if ($numLogs > $page * $perPage) { ?>
                  <li class="page-item"><a class="page-link" href="<?php echo autoUrl("admin/audit/requests?page="); ?><?php echo $page + 1 ?><?php if ($user) { ?>&user=<?= htmlspecialchars($user->getId()) ?><?php } ?>"><?php echo $page + 1 ?></a></li>
                  <?php if ($numLogs > $page * $perPage + $perPage) { ?>
                    <li class="page-item"><a class="page-link" href="<?php echo autoUrl("admin/audit/requests?page="); ?><?php echo $page + 2 ?><?php if ($user) { ?>&user=<?= htmlspecialchars($user->getId()) ?><?php } ?>"><?php echo $page + 2 ?></a></li>
                  <?php } ?>
                  <li class="page-item"><a class="page-link" href="<?php echo autoUrl("admin/audit/requests?page="); ?><?php echo $page + 1 ?><?php if ($user) { ?>&user=<?= htmlspecialchars($user->getId()) ?><?php } ?>">Next</a></li>
                <?php } ?>
              <?php } ?>
            <?php } ?>
          </ul>
        </nav>

      <?php } else { ?>
        <div class="alert alert-info">
          <p class="mb-0">
            <strong>There are no log items to display</strong>
          </p>
        </div>
      <?php } ?>
    </div>
  </div>
</div>

<?php

$footer = new \SCDS\RootFooter();
$footer->render();
