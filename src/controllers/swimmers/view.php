<?php

/**
 * New single member view and edit page
 */

try {
  $member = new Member($id);
} catch (Exception $e) {
  halt(404);
}

$user = $member->getUser();

if ($_SESSION['AccessLevel'] == 'Parent' && (!$user || $user->getId() != $_SESSION['UserID'])) {
  halt(404);
}

$squads = $member->getSquads();

$pagetitle = htmlspecialchars($member->getFullName());

include BASE_PATH . 'views/header.php';

?>

<div class="container">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?=autoUrl("members")?>">Members</a></li>
      <li class="breadcrumb-item active" aria-current="page">#<?=htmlspecialchars($member->getId())?></li>
    </ol>
  </nav>

  <h1>
    <?=htmlspecialchars($member->getFullName())?>
  </h1>
  <p class="lead">
    <?php if (sizeof($squads) > 0) { ?><?php for ($i=0; $i < sizeof($squads); $i++) { ?><?=htmlspecialchars($squads[$i]->getName())?><?php if ($i < sizeof($squads)-1) { ?>, <?php } ?><?php } ?> Squad<?php if (sizeof($squads) != 1) { ?>s<?php } ?><?php } else { ?>Not assigned to any squads<?php } ?>
  </p>
</div>

<?php

$footer = new \SCDS\Footer();
$footer->useFluidContainer();
$footer->render();