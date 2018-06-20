<?php

$pagetitle = "Swimmers";

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/paymentsMenu.php";

?>

<div class="container">
  <h1>New Extra</h1>
  <p class="lead">Add a new extra fee such as CrossFit.</p>

  <hr>

  <div class="row">
    <div class="col-lg-8">
      <form>
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

        <p>
          <button type="submit" class="btn btn-dark">
            Add
          </button>
        </p>
      </form>
    </div>
  </div>
</div>

<?php

include BASE_PATH . "views/footer.php";

?>
