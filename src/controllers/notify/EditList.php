<?php

//global $db;

$row = null;

try {
  $list = $db->prepare("SELECT * FROM `targetedLists` WHERE `ID` = ?");
  $list->execute([$id]);
} catch (Exception $e) {
  halt(500);
}
$row = $list->fetch(PDO::FETCH_ASSOC);

if ($row == null) {
	halt(404);
}

$pagetitle = "Edit " . htmlspecialchars($row['Name']);

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/notifyMenu.php";

?>

<div class="container">
  <div class="row">
    <div class="col-lg-8">
      <h1 class="">
  			Edit <?=htmlspecialchars($row['Name'])?>
  		</h1>
      <p class="lead">Edit this targetted list.</p>

      <?
      if (isset($_SESSION['ErrorState'])) {
        echo $_SESSION['ErrorState'];
        unset($_SESSION['ErrorState']);
      }
      ?>
      <form method="post">
        <div class="form-group">
          <label for="name">Extra Name</label>
          <input type="text" class="form-control" id="name" name="name"
					placeholder="Enter name" value="<?=htmlspecialchars$(row['Name'])?>">
        </div>

        <div class="form-group">
					<label for="desc">Description</label>
          <input type="text" class="form-control" id="desc" name="desc" placeholder="Describe this group" value="<?=htmlspecialchars($row['Description'])?>">
        </div>

        <p class="mb-0">
          <button type="submit" class="btn btn-success">
            Save Changes
          </button>
        </p>
      </form>
    </div>
  </div>
</div>

<?php

include BASE_PATH . "views/footer.php";

?>
