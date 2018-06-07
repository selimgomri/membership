<?php $access = $_SESSION['AccessLevel']; ?>
<div class="nav-scroller bg-white box-shadow mb-3">
  <nav class="nav nav-underline">
    <a class="nav-link" href="<?php echo autoUrl("galas")?>">Gala Home</a>
    <?php if ($access == "Parent") {?>
    <a class="nav-link" href="<?php echo autoUrl("galas/entries")?>">My Entries</a>
    <?php } else {?>
    <a class="nav-link" href="<?php echo autoUrl("galas/addgala")?>">Add Gala</a>
    <a class="nav-link" href="<?php echo autoUrl("galas/entries")?>">View Entries</a>
    <?php } ?>
    <a class="nav-link" href="https://www.chesterlestreetasc.co.uk/competitions/" target="_blank">Gala Website <i class="fa fa-external-$link"></i></a>
    <a class="nav-link" href="https://www.chesterlestreetasc.co.uk/competitions/category/galas/" target="_blank">Upcoming Galas <i class="fa fa-external-$link"></i></a>
    <?php if ($access == "Parent") {?>
    <a class="nav-link" href="https://www.chesterlestreetasc.co.uk/competitions/enteracompetition/guidance/" target="_blank">Help with Entries <i class="fa fa-external-$link"></i></a>
    <?php } ?>
  </nav>
</div>
