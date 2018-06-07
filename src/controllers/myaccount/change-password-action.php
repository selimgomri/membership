<?php
  $preventLoginRedirect = true;
  include "../database.php";

  $username = $_SESSION['Username'];
  $sql = "SELECT * FROM users WHERE Username = '$username' ";
  $result = mysqli_query($link, $sql);
  $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
  mysqli_free_result($result);
  $hash = $row['Password'];
  $userID = $row['UserID'];

  $currentPW = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['current'])));
  $password1 = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['new1'])));
  $password2 = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['new2'])));

  if ((password_verify($currentPW, $hash)) && ($password1 == $password2)) {
    $newHash = password_hash($password1, PASSWORD_BCRYPT);
    $sql = "UPDATE `users` SET `Password` = '$newHash' WHERE `UserID` = '$userID'";
    mysqli_query($link, $sql);
    header("Location: index.php");
  }
  else {
    echo "Error";
  }
?>
