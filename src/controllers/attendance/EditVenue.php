<?php

global $db;

$data = $db->prepare("SELECT VenueName, Location FROM sessionsVenues WHERE VenueID = ?");
$data->execute([$id]);
$venue = $data->fetch(PDO::FETCH_ASSOC);

$pagetitle = "Editing " . htmlspecialchars($venue);
include BASE_PATH . "views/header.php";

?>

<div class="container">
  <h1>Editing <?=htmlspecialchars($venue['VenueName'])?></h1>
  <div class="row">
    <div class="col-md-8">

      <?php if (isset($_SESSION['EditVenueError']) && $_SESSION['EditVenueError']['Status']) { ?>
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

      <?php if (isset($_SESSION['EditVenueSuccess']) && $_SESSION['EditVenueSuccess']) { ?>
        <div class="alert alert-success">
          <p class="mb-0">
            <strong>
              We've successfully updated the venue
            </strong>
          </p>
        </div>
      <?php } ?>

      <?php if (isset($_SESSION['NewVenueSuccess']) && $_SESSION['NewVenueSuccess']) { ?>
        <div class="alert alert-success">
          <p class="mb-0">
            <strong>
              We've successfully added the new venue
            </strong>
          </p>
        </div>
      <?php } ?>

      <form method="post" class="needs-validation" novalidate>
        <div class="form-group">
          <label for="name">Venue Name</label>
          <input type="text" class="form-control" name="name" id="name" aria-describedby="nameHelp" placeholder="Enter name" value="<?=$venue['VenueName']?>" required>
          <div class="invalid-feedback">
            You must enter a venue name
          </div>
          <small id="nameHelp" class="form-text text-muted">Enter the venue name, not the building. For example, this might be the "Small Pool, Anytown Leisure Centre" or the "Main Pool, Anytown Leisure Centre".</small>
        </div>

        <div class="form-group">
          <label for="address">Address</label>
          <input type="text" class="form-control" name="address" id="address" aria-describedby="addressHelp" placeholder="Enter address" value="<?=$venue['Location']?>" required>
          <div class="invalid-feedback">
            You must enter an address
          </div>
          <small id="addressHelp" class="form-text text-muted">Enter the address for the venue with each line separated by commas. For example, "Anytown Leisure Centre, Main Road, Anytown, AN1 1TON"</small>
        </div>

        <p>
          <button class="btn btn-primary" type="submit">
            Save changes
          </button>
        </p>
      </form>

    </div>
  </div>
</div>

<script async src="<?=autoUrl("public/js/NeedsValidation.js")?>"></script>

<?php

unset($_SESSION['EditVenueError']);
unset($_SESSION['EditVenueSuccess']);
unset($_SESSION['NewVenueSuccess']);

include BASE_PATH . "views/footer.php";