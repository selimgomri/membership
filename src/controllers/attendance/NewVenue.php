<?php

global $db;

$venue_details = $_SESSION['NewVenueError']['Data'];

$pagetitle = "Add a venue";
include BASE_PATH . "views/header.php";

?>

<div class="container">
  <div class="row">
    <div class="col-md-8">
      <h1>Add a venue</h1>

      <?php if (isset($_SESSION['NewVenueError']) && $_SESSION['NewVenueError']['Status']) { ?>
        <div class="alert alert-warning">
          <p class="mb-0">
            <strong>
              Some required information was missing
            </strong>
          </p>
          <p class="mb-0">
            Please check the data you supplied and try again.
          </p>
        </div>
      <?php } ?>

      <form method="post" class="needs-validation" novalidate>
        <div class="form-group">
          <label for="name">Venue Name</label>
          <input type="text" class="form-control" name="name" id="name" aria-describedby="nameHelp" placeholder="Enter name" value="<?=$venue_details['name']?>" required>
          <div class="invalid-feedback">
            You must enter a venue name
          </div>
          <small id="nameHelp" class="form-text text-muted">Enter the venue name, not the building. For example, this might be the "Small Pool, Anytown Leisure Centre" or the "Main Pool, Anytown Leisure Centre".</small>
        </div>

        <div class="form-group">
          <label for="address">Address</label>
          <input type="text" class="form-control" name="address" id="address" aria-describedby="addressHelp" placeholder="Enter address" value="<?=$venue_details['address']?>" required>
          <div class="invalid-feedback">
            You must enter an address
          </div>
          <small id="addressHelp" class="form-text text-muted">Enter the address for the venue with each line separated by commas. For example, "Anytown Leisure Centre, Main Road, Anytown, AN1 1TON"</small>
        </div>

        <p>
          <button class="btn btn-primary" type="submit">
            Add venue
          </button>
        </p>
      </form>
    </div>
  </div>
</div>

<?php

unset($_SESSION['NewVenueError']);

$footer = new \SDCS\Footer();
$footer->addJs("public/js/NeedsValidation.js");
$footer->render();
