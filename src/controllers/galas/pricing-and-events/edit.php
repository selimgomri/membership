<?php

global $db;
$getGala = $db->prepare("SELECT GalaName `name`, ClosingDate FROM galas WHERE GalaID = ?");
$getGala->execute([$id]);
$gala = $getGala->fetch(PDO::FETCH_ASSOC);

if ($gala == null) {
  halt(404);
}

$today = new DateTime('now', new DateTimeZone('Europe/London'));
$closingDate = new DateTime($gala['ClosingDate'], new DateTimeZone('Europe/London'));

// Arrays of swims used to check whever to print the name of the swim entered
// BEWARE This is in an order to ease inputting data into SportSystems, contrary to these arrays in other files
$swimsArray = [
  '50Free' => '50&nbsp;Free',
  '100Free' => '100&nbsp;Free',
  '200Free' => '200&nbsp;Free',
  '400Free' => '400&nbsp;Free',
  '800Free' => '800&nbsp;Free',
  '1500Free' => '1500&nbsp;Free',
  '50Back' => '50&nbsp;Back',
  '100Back' => '100&nbsp;Back',
  '200Back' => '200&nbsp;Back',
  '50Breast' => '50&nbsp;Breast',
  '100Breast' => '100&nbsp;Breast',
  '200Breast' => '200&nbsp;Breast',
  '50Fly' => '50&nbsp;Fly',
  '100Fly' => '100&nbsp;Fly',
  '200Fly' => '200&nbsp;Fly',
  '100IM' => '100&nbsp;IM',
  '150IM' => '150&nbsp;IM',
  '200IM' => '200&nbsp;IM',
  '400IM' => '400&nbsp;IM'
];

$pagetitle = 'Pricing and Events';
include BASE_PATH . 'views/header.php';

?>

<div class="container">

  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?=autoUrl("galas")?>">Galas</a></li>
      <li class="breadcrumb-item"><a href="<?=autoUrl("galas/" . $id)?>">This gala</a></li>
      <li class="breadcrumb-item active" aria-current="page">Prices and events</li>
    </ol>
  </nav>

  <div class="row">
    <div class="col-lg-8">
      <h1>Pricing and events</h1>
      <p class="lead">Manage events and entry fees for <?=htmlspecialchars($gala['name'])?>.</p>

      <p>You can select events which will be run at this gala and enter the price for each of these events.</p>

      <p>To do this, tick or untick the box on the left of each event and enter the price on the right.</p>

      <form method="post">

        <ul class="list-group mb-3">
        <?php foreach ($swimsArray as $eventKey => $event) { ?>
        <li class="list-group-item">
          <h2><?=$event?></h2>

          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <div class="input-group-text">
                <input type="checkbox" aria-label="Tick to confirm event">
              </div>
              <span class="input-group-text" id="<?=$eventKey?>-price-addon">&pound;</span>
            </div>
            <input type="number" step="0.01" min="0" class="form-control" id="<?=$eventKey?>-price" name="<?=$eventKey?>-price" aria-label="<?=$event?> price">
          </div>
        </li>
        <?php } ?>
        </ul>

        <p>
          <buttton type="submit" class="btn btn-primary">
            Save
          </buttton>
        </p>

      </form>

    </div>
  </div>
</div>

<?php

include BASE_PATH . 'views/footer.php';