<?php

global $db;

$user = $_SESSION['UserId'];
$pagetitle = "Targetted Lists";
$use_white_background = true;

$lists = $db->query("SELECT * FROM `targetedLists` ORDER BY `Name` ASC");
$row = $lists->fetch(PDO::FETCH_ASSOC);

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/notifyMenu.php";

 ?>

<div class="container">
  <div class="">
    <div class="row">
      <div class="col-md-8">
      	<h1>Targetted Lists</h1>
        <p class="lead">
          Targetted lists are custom mailing lists for messaging groups of parents
          outside of normal squads.
        </p>
        <p>
          A useful use case would be for the Junior League
        </p>
      </div>
    </div>
    <?php if ($row != null) { ?>
      <div class="table-responsive-md">
        <table class="table">
          <thead class="thead-light">
            <tr>
              <th>Name</th>
              <th>Description</th>
            </tr>
          </thead>
          <tbody>
          <?php do { ?>
            <tr>
              <td><a href="<?php echo autoUrl("notify/lists/" . $row['ID']); ?>"><?=htmlspecialchars($row['Name'])?></a></td>
              <td><?=htmlspecialchars($row['Description'])?></td>
            </tr>
          <?php } while ($row = $lists->fetch(PDO::FETCH_ASSOC)); ?>
        </tbody>
      </table>
    </div>
    <?php } else { ?>
    <div class="alert alert-info">
      <strong>There are no lists available</strong>
    </div>
    <?php } ?>
    <p class="mb-0">
      <a href="<?php echo autoUrl("notify/lists/new"); ?>"
        class="btn btn-success">
        Add New List
      </a>
    </p>
  </div>
</div>

<?php include BASE_PATH . "views/footer.php";
