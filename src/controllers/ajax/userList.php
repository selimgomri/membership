<?php

global $db;

$access = $_SESSION['AccessLevel'];

if ($access != "Admin" && $access != "Galas") {
  halt(404);
}

$users = null;
if (isset($_POST["search"])) {
  // get the search term parameter from post
  $search = '%' . trim($_POST["search"]) . '%';

  $sql = "";
  $search_terms = explode(' ', $_POST["search"]);
  $names = [];
  $sql = "";
  for ($i = 0; $i < sizeof($search_terms); $i++) {
    if ($i > 0) {
      $sql .= " OR ";
    }
    $sql .= " Forename COLLATE utf8mb4_general_ci LIKE ? OR Surname COLLATE utf8mb4_general_ci LIKE ? ";
    for ($y = 0; $y < 2; $y++) {
      $names[] = "%" . $search_terms[$i] . "%";
    }
  }

  $sql = "SELECT Forename, Surname, UserID, AccessLevel FROM users WHERE " . $sql . " ORDER BY Forename, Surname ASC";

  $users = $db->prepare($sql);
  $users->execute($names);
}

if ($users == null) {
  halt(404);
}

$user = $users->fetch(PDO::FETCH_ASSOC);

if ($user != null) { ?>

<div class="list-group">

  <?php do { ?>
  <a href="<?=autoUrl("users/" . $user['UserID'])?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
    <?=htmlspecialchars($user['Forename'] . " " . $user['Surname'])?>
    <span class="badge badge-primary badge-pill">
      <?php if ($user['AccessLevel'] == 'Committee') { ?>Team Manager<?php } else { ?><?=htmlspecialchars($user['AccessLevel'])?><?php } ?>
    </span>
  </a>
  <?php } while ($user = $users->fetch(PDO::FETCH_ASSOC)); ?>
</div>

<?php } else { ?>

<div class="alert alert-warning">
  <strong>No users found for that name</strong><br>
  Please try another search
</div>

<?php } ?>