<?php

$user = $_SESSION['UserId'];
$pagetitle = "Targeted Lists";
$use_white_background = true;

$sql = "SELECT * FROM `targetedLists` ORDER BY `Name` ASC;";
$result = mysqli_query($link, $sql);

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/notifyMenu.php";

 ?>

<div class="container">
  <div class="">
  	<h1 class="border-bottom border-gray pb-2 mb-2">Targeted Lists</h1>
    <p class="lead">
      Targeted lists are custom mailing lists for messaging groups of parents
      outside of normal squads.
    </p>
    <p>
      A useful use case would be for the Junior League
    </p>
    <? if (mysqli_num_rows($result) > 0) { ?>
      <div class="table-responsive-md">
        <table class="table">
          <thead class="thead-light">
            <tr>
              <th>Name</th>
              <th>Description</th>
            </tr>
          </thead>
          <tbody>
          <? for ($i = 0; $i < mysqli_num_rows($result); $i++) {
            $row = mysqli_fetch_array($result, MYSQLI_ASSOC); ?>
            <tr>
              <td><a href="<? echo autoUrl("notify/lists/" . $row['ID']); ?>"><? echo $row['Name']; ?></a></td>
              <td><? echo $row['Description']; ?></td>
            </tr>
          <? } ?>
        </tbody>
      </table>
    </div>
    <? } else { ?>
    <div class="alert alert-info">
      <strong>There are no lists available</strong>
    </div>
    <? } ?>
    <p class="mb-0">
      <a href="<? echo autoUrl("notify/lists/new"); ?>"
        class="btn btn-dark">
        Add New List
      </a>
    </p>
  </div>
</div>

<?php include BASE_PATH . "views/footer.php";
