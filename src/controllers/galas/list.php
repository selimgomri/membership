<?php

global $db;

$start = 0;
$page = 0;

if (isset($_GET['page']) && ((int) $_GET['page']) != 0) {
  $page = (int) $_GET['page'];
  $start = ($page-1)*10;
} else {
  $page = 1;
}

$getCount = $db->query("SELECT COUNT(*) FROM galas");
$numGalas  = $getCount->fetchColumn();
$numPages = ((int)($numGalas/10)) + 1;

$getGalas = $db->prepare("SELECT GalaID `id`, GalaName `name`, GalaVenue venue, ClosingDate closes, GalaDate finishes FROM galas ORDER BY `GalaDate` DESC, ClosingDate DESC LIMIT :offset, :num");
$getGalas->bindValue(':offset', $start, PDO::PARAM_INT); 
$getGalas->bindValue(':num', 10, PDO::PARAM_INT); 
$getGalas->execute();
$gala = $getGalas->fetch(PDO::FETCH_ASSOC);

$now = new DateTime('today midnight', new DateTimeZone('Europe/London'));

$pagetitle = "All Galas - Page " . $page;

include BASE_PATH . 'views/header.php';

?>

<div class="bg-light mt-n3 py-3 mb-3">
  <div class="container">

    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <a href="<?=htmlspecialchars(autoUrl("galas"))?>">
            Galas
          </a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
          All
        </li>
      </ol>
    </nav>

    <div class="row align-items-center">
      <div class="col-lg-8">
        <h1>
          All galas
        </h1>
        <p class="lead mb-0">
          Find all past and future galas
        </p>
        <div class="mb-3 d-lg-none"></div>
      </div>
      <div class="col text-right">
        <!-- STUFF -->
      </div>
    </div>

  </div>
</div>

<div class="container">

  <div class="row">
    <div class="col-lg-8 mb-3">
      <?php if ($gala) { ?>
      <div class="row mb-3">
        <div class="col">
          <p class="lead mb-0">
            Page <?=htmlspecialchars($page)?> of <?=htmlspecialchars($numPages)?>
          </p>
        </div>
        <div class="col text-right">
          <p class="lead text-muted mb-0">
            <?=htmlspecialchars($numGalas)?> gala<?php if ($numGalas != 1) { ?>s<?php } ?> in total
          </p>
        </div>
      </div>

      <ul class="list-group">
        <?php do {
          $closes = new DateTime($gala['closes'], new DateTimeZone('UTC'));
          $closes->setTimezone(new DateTimeZone('Europe/London'));
          $closed = $closes <= $now;

          $finishes = new DateTime($gala['finishes'], new DateTimeZone('UTC'));
          $finishes->setTimezone(new DateTimeZone('Europe/London'));
          $finished = $finishes <= $now;
        ?>
        <li class="list-group-item" id="<?=htmlspecialchars("gala-" . $gala['id'])?>">
          <div class="row justify-content-between">
            <div class="col">
              <h2 class="mb-0"><a href="<?=htmlspecialchars(autoUrl("galas/" . $gala['id']))?>"><?=htmlspecialchars($gala['name'])?></a></h2>
            </div>
            <?php if ($_SESSION['AccessLevel'] != 'Parent') { ?>
            <div class="col-auto">
              <div class="btn-group" role="group">
                <a href="<?=htmlspecialchars(autoUrl("galas/entries?gala=" . $gala['id']))?>" class="btn btn-primary">
                  Entries
                </a>
                <a href="<?=htmlspecialchars(autoUrl("galas/" . $gala['id'] . "/edit"))?>" class="btn btn-dark">
                  Edit <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                </a>
              </div>
            </div>
          </div>
          <p class="lead">
            <?=htmlspecialchars($gala['venue'])?>
          </p>
          <dl class="row mb-0">
            <dt class="col-3">Close<?php if ($closed) { ?>d<?php } else { ?>s<?php } ?></dt>
            <dd class="col-9"><?=htmlspecialchars($closes->format("j F Y"))?></dd>

            <dt class="col-3">Finishe<?php if ($closed) { ?>d<?php } else { ?>s<?php } ?></dt>
            <dd class="col-9 mb-0"><?=htmlspecialchars($finishes->format("j F Y"))?></dd>
          </dl>
          <?php } ?>
        </li>
        <?php } while ($gala = $getGalas->fetch(PDO::FETCH_ASSOC)); ?>
      </ul>

      <!-- Pagination -->
      <nav aria-label="Page navigation">
        <ul class="pagination mb-3">
          <?php if ($numGalas <= 10) { ?>
          <li class="page-item active"><a class="page-link" href="<?php echo autoUrl("galas/all-galas?page="); ?><?php echo $page ?>"><?php echo $page ?></a></li>
          <?php } else if ($numGalas <= 20) { ?>
            <?php if ($page == 1) { ?>
            <li class="page-item active"><a class="page-link" href="<?php echo autoUrl("galas/all-galas?page="); ?><?php echo $page ?>"><?php echo $page ?></a></li>
            <li class="page-item"><a class="page-link" href="<?php echo autoUrl("galas/all-galas?page="); ?><?php echo $page+1 ?>"><?php echo $page+1 ?></a></li>
            <li class="page-item"><a class="page-link" href="<?php echo autoUrl("galas/all-galas?page="); ?><?php echo $page+1 ?>">Next</a></li>
            <?php } else { ?>
            <li class="page-item"><a class="page-link" href="<?php echo autoUrl("galas/all-galas?page="); ?><?php echo $page-1 ?>">Previous</a></li>
            <li class="page-item"><a class="page-link" href="<?php echo autoUrl("galas/all-galas?page="); ?><?php echo $page-1 ?>"><?php echo $page-1 ?></a></li>
            <li class="page-item active"><a class="page-link" href="<?php echo autoUrl("galas/all-galas?page="); ?><?php echo $page ?>"><?php echo $page ?></a></li>
            <?php } ?>
          <?php } else { ?>
            <?php if ($page == 1) { ?>
            <li class="page-item active"><a class="page-link" href="<?php echo autoUrl("galas/all-galas?page="); ?><?php echo $page ?>"><?php echo $page ?></a></li>
            <li class="page-item"><a class="page-link" href="<?php echo autoUrl("galas/all-galas?page="); ?><?php echo $page+1 ?>"><?php echo $page+1 ?></a></li>
            <li class="page-item"><a class="page-link" href="<?php echo autoUrl("galas/all-galas?page="); ?><?php echo $page+2 ?>"><?php echo $page+2 ?></a></li>
            <li class="page-item"><a class="page-link" href="<?php echo autoUrl("galas/all-galas?page="); ?><?php echo $page+1 ?>">Next</a></li>
            <?php } else { ?>
            <li class="page-item"><a class="page-link" href="<?php echo autoUrl("galas/all-galas?page="); ?><?php echo $page-1 ?>">Previous</a></li>
            <?php if ($page > 2) { ?>
            <li class="page-item"><a class="page-link" href="<?php echo autoUrl("galas/all-galas?page="); ?><?php echo $page-2 ?>"><?php echo $page-2 ?></a></li>
            <?php } ?>
            <li class="page-item"><a class="page-link" href="<?php echo autoUrl("galas/all-galas?page="); ?><?php echo $page-1 ?>"><?php echo $page-1 ?></a></li>
            <li class="page-item active"><a class="page-link" href="<?php echo autoUrl("galas/all-galas?page="); ?><?php echo $page ?>"><?php echo $page ?></a></li>
            <?php if ($numGalas > $page*10) { ?>
            <li class="page-item"><a class="page-link" href="<?php echo autoUrl("galas/all-galas?page="); ?><?php echo $page+1 ?>"><?php echo $page+1 ?></a></li>
            <?php if ($numGalas > $page*10+10) { ?>
            <li class="page-item"><a class="page-link" href="<?php echo autoUrl("galas/all-galas?page="); ?><?php echo $page+2 ?>"><?php echo $page+2 ?></a></li>
            <?php } ?>
            <li class="page-item"><a class="page-link" href="<?php echo autoUrl("galas/all-galas?page="); ?><?php echo $page+1 ?>">Next</a></li>
            <?php } ?>
          <?php } ?>
        <?php } ?>
        </ul>
      </nav>

      <?php } else { ?>
      <div class="alert alert-warning">
        <p class="mb-0">
          <strong>There are no logs to display</strong>
        </p>
        <p class="mb-0">
          <a href="<?=htmlspecialchars(autoUrl("galas/all-galas" . "/new"))?>" class="alert-link">Create a new training log</a> to get started.
        </p>
      </div>
      <?php } ?>
    </div>
    <div class="col">
      <!-- <div class="cell">
        <h2>Log books are new!</h2>
        <p class="lead">
          We have added log books to the membership system as a response to the coronavirus (COVID-19) outbreak.
        </p>
        <p>
          This is to allow members to log their home based land training.
        </p>
        <p class="mb-0">
          As always, feedback is very welcome. Send it to <a href="mailto:feedback@myswimmingclub.uk">feedback@myswimmingclub.uk</a>
        </p>
      </div> -->

    </div>
  </div>
</div>

<?php

$footer = new \SCDS\Footer();
$footer->render();