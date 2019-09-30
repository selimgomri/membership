<?php

global $db;

$now = new DateTime('now', new DateTimeZone('Europe/London'));

$galas = $db->prepare("SELECT GalaID, GalaName, ClosingDate, GalaDate, GalaVenue, CourseLength FROM galas WHERE ClosingDate >= ? ORDER BY GalaDate ASC");
$galas->execute([$now->format('Y-m-d')]);
$gala = $galas->fetch(PDO::FETCH_ASSOC);
$entriesOpen = false;

?>

<div class="cell">
	<h2>
		Open galas
	</h2>
	<?php if ($gala != null) { 
		$entriesOpen = true;
		
		?>
		<div class="my-3">
		<?php do {
			$closingDate = new DateTime($gala['ClosingDate'], new DateTimeZone('Europe/London'));
			$endDate = new DateTime($gala['GalaDate'], new DateTimeZone('Europe/London'));
			?>
			<a href="<?=autoUrl("galas/" . $gala['GalaID'])?>" class="p-3 bg-white">
				<div>
					<span class="title mb-0 justify-content-between align-items-start">
						<span><?=htmlspecialchars($gala['GalaName'])?></span>
						<span class="ml-2 badge badge-success">ENTRIES OPEN</span>
					</span>
					<span class="d-flex mb-3"><?=htmlspecialchars($gala['GalaVenue'])?></span>
				</div>
				<span class="category">Entries close on <?=$closingDate->format('j F Y')?></span>
			</a>
		<?php } while ($gala = $galas->fetch(PDO::FETCH_ASSOC)); ?>
		</div>
	<?php if ($entriesOpen) { ?>
	<p>
		<a href="<?=autoUrl("galas/entergala")?>" class="btn btn-success">
			Enter a gala
		</a>
	</p>
  <p class="mb-0">
    Enter via My Account
  </p>
  <?php } else { ?>
  <p class="mb-0">
    There are no galas open for entries at the moment.
  </p>
  <?php } ?>
  <?php } else { ?>
  <p class="mb-0">
		There are no galas open for entries at the moment.
	</p>
  <?php } ?>
</div>
