<?php

global $db;

$galas = $db->query("SELECT GalaID, GalaName, ClosingDate, GalaDate, GalaVenue, CourseLength FROM galas WHERE ClosingDate >= CURDATE() ORDER BY GalaDate ASC");
$gala = $galas->fetch(PDO::FETCH_ASSOC);
$entriesOpen = false;

?>

<div class="cell">
	<h2>
		Open galas
	</h2>
	<?php if ($gala != null) { ?>
	<div class="news-grid my-3">
		<?php do {
			$now = new DateTime();
			$closingDate = new DateTime($gala['ClosingDate']);
			$endDate = new DateTime($gala['GalaDate']);

			if ($now <= $closingDate) {
				$entriesOpen = true;
			}

			?>
			<a href="<?=autoUrl("galas/" . $gala['GalaID'])?>">
				<div>
					<span class="title mb-0 justify-content-between align-items-start">
						<span><?=htmlspecialchars($gala['GalaName'])?></span>
						<?php if ($now <= $closingDate) { $entriesOpen = true;?><span class="ml-2 badge badge-success">ENTRIES OPEN</span><?php } ?>
					</span>
					<span class="d-flex mb-3"><?=htmlspecialchars($gala['GalaVenue'])?></span>
				</div>
				<?php if ($now <= $closingDate) { ?>
				<span class="category">Entries close on <?=$closingDate->format('j F Y')?></span>
				<?php } else { ?>
				<span class="category">Ends on <?=$endDate->format('j F Y')?></span>
				<?php } ?>
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
