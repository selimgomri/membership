<?php

$pagetitle = "Add a New Extra";

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/paymentsMenu.php";

?>

<div class="container">
  <div class="my-3 p-3 bg-white rounded shadow">
    <h1 class="border-bottom border-gray pb-2 mb-2">New Extra</h1>
    <p class="lead">Add a new extra fee such as CrossFit.</p>

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
            <input type="text" class="form-control" id="name" name="name" placeholder="Enter name">
          </div>

          <div class="form-group">
            <label for="price">Price</label>
            <div class="input-group mb-3">
              <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon3">&pound;</span>
              </div>
              <input type="text" class="form-control" id="price" name="price" placeholder="Enter price">
            </div>
          </div>

          <p class="mb-0">
            <button type="submit" class="btn btn-dark">
              Add
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
