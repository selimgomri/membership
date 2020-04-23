<?php

$db = app()->db;

if ($_POST['response'] == "getSwimmers") {
  // $swimmers = $db->prepare("SELECT MForename, MSurname, SquadName, ReferenceID FROM (((`targetedListMembers` INNER JOIN `members` ON
  // members.MemberID = targetedListMembers.ReferenceID) INNER JOIN `targetedLists`
  // ON targetedLists.ID = targetedListMembers.ListID) INNER JOIN `squads` ON
  // members.SquadID = squads.SquadID) WHERE `targetedListMembers`.`ListID` =
  // ? ORDER BY ReferenceID ASC");

  $swimmers = $db->prepare("SELECT combined.MForename, combined.MSurname, combined.SquadName, combined.ID FROM (SELECT MForename, MSurname, SquadName, targetedListMembers.ID FROM ((`targetedListMembers` INNER JOIN `members` ON members.MemberID = targetedListMembers.ReferenceID) INNER JOIN `squads` ON members.SquadID = squads.SquadID) WHERE `targetedListMembers`.`ListID` = :list AND targetedListMembers.ReferenceType = 'Member' UNION SELECT Forename AS MForename, Surname AS MSurname, 'User' AS SquadName, targetedListMembers.ID FROM (`targetedListMembers` INNER JOIN `users` ON users.UserID = targetedListMembers.ReferenceID) WHERE `targetedListMembers`.`ListID` = :list AND targetedListMembers.ReferenceType = 'User') AS combined ORDER BY combined.ID ASC ");

  $swimmers->execute(['list' => $id]);
  $row = $swimmers->fetch(PDO::FETCH_ASSOC);

  ?>

  <div class="">
    <?php if ($row != null) { ?>
    <div class="card">
      <div class="card-header">
        List members
      </div>
      <ul class="list-group list-group-flush">
        <?php do { ?>
        <li class="list-group-item">
          <div class="row align-items-center">
            <div class="col-auto">
              <p class="mb-0">
                <strong>
                  <?=htmlspecialchars($row['MForename'] . " " . $row['MSurname'])?>
                </strong>
              </p>
              <p class="mb-0">
                <?=htmlspecialchars($row['SquadName'])?>
              </p>
            </div>
            <div class="col text-right">
              <button type="button" id="RelationDrop-<?=$row['ID']?>"
                class="btn btn-link" value="<?=$row['ID']?>">
                Remove
              </button>
            </div>
          </div>
        </li>
        <?php } while ($row = $swimmers->fetch(PDO::FETCH_ASSOC)); ?>
      </ul>
    </div>
    <?php } else { ?>
    <div class="alert alert-info mb-0">
      <strong>There are no swimmers linked to this targeted list</strong>
    </div>
    <?php } ?>
  </div>
<?php
} else if ($_POST['response'] == "squadSelect") {
  $status = false;
  $output = ' <option value="null" selected>Select a member</option>';

  try {
    $squad = $_POST['squadSelect'];
    $members == null;
    if ($squad != "all") {
      $members = $db->prepare("SELECT MemberID, MForename, MSurname FROM `members` WHERE `SquadID` = ? AND MemberID NOT IN (SELECT ReferenceID FROM targetedListMembers WHERE ListID = ? AND ReferenceType = 'Member') ORDER BY `MForename` ASC, `MSurname` ASC");
      $members->execute([$squad, $id]);
    } else {
      $members = $db->prepare("SELECT MemberID, MForename, MSurname FROM `members` WHERE MemberID NOT IN (SELECT ReferenceID FROM targetedListMembers WHERE ListID = ? AND ReferenceType = 'Member') ORDER BY `MForename` ASC, `MSurname` ASC");
      $members->execute([$id]);
    }
    while ($row = $members->fetch(PDO::FETCH_ASSOC)) {
      $output .= '<option value="' . htmlspecialchars($row['MemberID']) . '">' . htmlspecialchars($row['MForename'] . " " . $row['MSurname']) . '</option>';
      $status = true;
    }
  } catch (Exception $e) {
    // Do nothing, an empty, disabled select will be returned.
  }

  echo json_encode([
    'swimmerSelectContent' => $output,
    'status' => $status
  ]);
} else if ($_POST['response'] == "userSelect") {
  $status = false;
  $output = ' <option value="null" selected>Search for a user</option>';

  try {
    if (mb_strlen($_POST['searchTerm']) > 0) {
      $searchTerm = '%' . $_POST['searchTerm'] . '%';
      $members == null;
      $members = $db->prepare("SELECT UserID, Forename, Surname FROM `users` WHERE `Forename` COLLATE utf8mb4_general_ci LIKE :searchTerm OR `Surname` COLLATE utf8mb4_general_ci LIKE :searchTerm AND UserID NOT IN (SELECT ReferenceID FROM targetedListMembers WHERE ListID = :list AND ReferenceType = 'User') ORDER BY `Forename` ASC, `Surname` ASC");
      $members->execute([
        'searchTerm' => $searchTerm,
        'list' => $id
      ]);
      
      $usersOutput = '<option value="null" selected>Select a user</option>';
      while ($row = $members->fetch(PDO::FETCH_ASSOC)) {
        $usersOutput .= '<option value="' . htmlspecialchars($row['UserID']) . '">' . htmlspecialchars($row['Forename'] . " " . $row['Surname']) . '</option>';
        $status = true;
      }
      if ($status) {
        $output = $usersOutput;
      }
    }
  } catch (Exception $e) {
    // Do nothing, an empty, disabled select will be returned.
  }

  echo json_encode([
    'userSelectContent' => $output,
    'status' => $status
  ]); 
} else if ($_POST['response'] == "insert") {
  $swimmer = $_POST['swimmerInsert'];
  if ($swimmer != null && $swimmer != "") {
    try {
      // Check count
      $getCount = $db->prepare("SELECT COUNT(*) FROM targetedListMembers WHERE ListID = ? AND ReferenceID = ? AND ReferenceType = ?");
      $getCount->execute([
        $id,
        $swimmer,
        'Member'
      ]);
      if ($getCount->fetchColumn() > 0) {
        halt(403);
      } else {
        $insert = $db->prepare("INSERT INTO `targetedListMembers` (`ListID`, `ReferenceID`, `ReferenceType`) VALUES (?, ?, ?)");
        $insert->execute([
          $id,
          $swimmer,
          'Member'
        ]);
      }
    } catch (Exception $e) {
      halt(403);
    }
  }
} else if ($_POST['response'] == "insert-user") {
  $swimmer = $_POST['swimmerInsert'];
  if ($swimmer != null && $swimmer != "") {
    try {
      // Check count
      $getCount = $db->prepare("SELECT COUNT(*) FROM targetedListMembers WHERE ListID = ? AND ReferenceID = ? AND ReferenceType = ?");
      $getCount->execute([
        $id,
        $swimmer,
        'User'
      ]);
      if ($getCount->fetchColumn() > 0) {
        halt(403);
      } else {
        $insert = $db->prepare("INSERT INTO `targetedListMembers` (`ListID`, `ReferenceID`, `ReferenceType`) VALUES (?, ?, ?)");
        $insert->execute([
          $id,
          $swimmer,
          'User'
        ]);
      }
    } catch (Exception $e) {
      halt(403);
    }
  }
} else if ($_POST['response'] == "dropRelation") {
  try {
    $drop = $db->prepare("DELETE FROM `targetedListMembers` WHERE `ID` = ?");
    $drop->execute([$_POST['relation']]);
  } catch (Exception $e) {
    halt(403);
  }
} else {
  halt(404);
}
