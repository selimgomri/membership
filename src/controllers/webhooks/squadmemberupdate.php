<?

// Mandatory Startup Sequence to carry out squad updates
$sql = "SELECT * FROM `moves` WHERE MovingDate <= CURDATE();";
$result = mysqli_query($link, $sql);
$count = mysqli_num_rows($result);
for ($i = 0; $i < $count; $i++) {
  try {
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    $squadID = $row['SquadID'];
    $member = $row['MemberID'];

    $query = $db->prepare("UPDATE `members` SET `SquadID` = ? WHERE `MemberID` = ?");
    $data = array($squadID, $member);
    $query->execute($data);

    $query = $db->prepare("DELETE FROM `moves` WHERE `MemberID` = ?");
    $data = array($member);
    $query->execute($data);
  }
  catch (PDOException $e) {
    halt(500);
  }
}
