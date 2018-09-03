<?php

$id = mysqli_real_escape_string($link, $id);
$sql = "SELECT * FROM `targetedLists` WHERE `ID` = '$id';";
$result = mysqli_query($link, $sql);

if (mysqli_num_rows($result) != 1) {
	halt(404);
}

$row = mysqli_fetch_array($result, MYSQLI_ASSOC);

$pagetitle = "Edit " . $row['Name'];

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/notifyMenu.php";

?>

<div class="container">
  <div class="my-3 p-3 bg-white rounded shadow">
    <h1 class="border-bottom border-gray pb-2 mb-2">
			Edit <? echo $row['Name']; ?>
		</h1>
    <p class="lead">Edit this targeted list.</p>

    <hr>

    <div class="row">
      <div class="col-lg-8">
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
						placeholder="Enter name" value="<? echo $row['Name']; ?>">
          </div>

          <div class="form-group">
						<label for="desc">Description</label>
            <input type="text" class="form-control" id="desc" name="desc" placeholder="Describe this group" value="<? echo $row['Description']; ?>">
          </div>

          <p class="mb-0">
            <button type="submit" class="btn btn-dark">
              Save Changes
            </button>
          </p>
        </form>
      </div>
    </div>
  </div>
</div>

<?php

include BASE_PATH . "views/footer.php";

?>
