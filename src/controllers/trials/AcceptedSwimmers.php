<?php

global $db;

$query = $db->prepare("SELECT COUNT(*) FROM joinParents");
$query->execute([$hash]);

$count = $query->fetchColumn();

$query = $db->prepare("SELECT ID, Hash, Email, joinSwimmers.First, joinSwimmers.Last, joinParents.First PFirst, joinParents.Last PLast, Comments FROM joinParents INNER JOIN joinSwimmers ON joinParents.Hash = joinSwimmers.Parent WHERE SquadSuggestion IS NOT NULL ORDER BY ID DESC");
$query->execute([$hash]);

$parents = $query->fetchAll(PDO::FETCH_ASSOC);

$pagetitle = "Accepted Swimmers";
$use_white_background = true;

include BASE_PATH . 'views/header.php';

?>

<div class="container">
  <h1>Accepted Swimmers</h1>
  <div class="row">
    <div class="col-md-10 col-lg-8">
      <p class="lead">
        Swimmers offered a squad place after a trial
      </p>

      <p>
        If some children of a parent have been offered a place but won't be
        joining <?=CLUB_NAME?>, please press <em>Reject Squad Place</em> on each
        swimmer so that they aren't added to the membership system.
      </p>

      <?php if ($count == 0) { ?>
      <div class="alert alert-warning">
        <strong>There are no accepted swimmers waiting to be added</strong>
      </div>
      <?php } ?>

      <?php
      foreach ($parents as $parent) { ?>
      <div class="cell">
        <h2><?=$parent['First']?> <?=$parent['Last']?></h2>
        <p>
          Contact <?=$parent['PFirst']?> <?=$parent['PLast']?> via email at <a href="mailto:<?=$parent['Email']?>"><?=$parent['Email']?></a>
        </p>

        <?php if ($parent['Comments'] != null && $parent['Comments'] != "") { ?>
        <p>
          <?=htmlspecialchars($parent['Comments'])?>
        </p>
        <?php } ?>

        <div class="form-row mb-1">
          <div class="col-md">
            <a href="<?=autoUrl($url_path . $parent['Hash'] . "/invite")?>" class="btn btn-block btn-dark">
              Invite Parent
            </a>
          </div>

          <div class="col mb-2 d-md-none">
          </div>

          <div class="col-md">
            <a href="<?=autoUrl("trials/recommendations/" . $parent['ID'])?>" class="btn btn-block btn-dark">
              Edit Recommendations
            </a>
          </div>

          <div class="col mb-2 d-md-none">
          </div>

          <div class="col-md">
            <div class="dropdown">
              <button class="btn btn-block btn-danger dropdown-toggle" type="button" id="deleteDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Advanced
              </button>
              <div class="dropdown-menu dropdown-menu-right" aria-labelledby="deleteDropdown">
                <a class="dropdown-item" href="<?=autoUrl($url_path . $parent['Hash'] . "/cancel/" . $parent['ID'])?>?redirect=<?=urlencode(app('request')->curl)?>">Reject Squad Place</a>
              </div>
            </div>
          </div>
        </div>

      </div>
      <?php }
      ?>

    </div>

  </div>
</div>

<?php

include BASE_PATH . 'views/footer.php';
