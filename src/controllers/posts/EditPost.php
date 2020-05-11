<?php

$db = app()->db;
$tenant = app()->db;

$query = null;

$exit_edit = true;

if ($int) {
	$sql = "SELECT * FROM `posts` WHERE `ID` = ? AND Tenant = ?";
	try {
		$query = $db->prepare($sql);
		$query->execute([
			$id,
			$tenant->getId()
		]);
	} catch (PDOException $e) {
		halt(404);
	}
} else {
	$sql = "SELECT * FROM `posts` WHERE `Path` = ? AND Tenant = ?";
	try {
		$query = $db->prepare($sql);
		$query->execute([
			$id,
			$tenant->getId()
		]);
	} catch (PDOException $e) {
		halt(404);
	}
}
$row = $query->fetch(PDO::FETCH_ASSOC);

if (!$row) {
  halt(404);
}

$date = new DateTime($row['Date'], new DateTimeZone('UTC'));
$date->setTimezone(new DateTimeZone('Europe/London'));

include 'support/PostTypes.php';
include 'support/MimeTypes.php';

$pagetitle = "Editing " . $row['Title'];

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/postsMenu.php";

?>

<div class="container">
	<form method="post">
		<div class="row">
			<div class="col-md-8">
				<div>
					<h1>Editing <?=htmlspecialchars($row['Title'])?></h1>
					<div class="form-group">
						<label for="title">Title</label>
						<input type="text" class="form-control" name="title" id="title"
			      placeholder="Post Title" autocomplete="off" value="<?=htmlentities($row['Title'])?>">
					</div>

					<div class="form-group mb-0">
						<label for="content">Content</label>
						<textarea class="form-control auto-grow mono" id="content" name="content" oninput="autoGrow(this)"><?=htmlspecialchars($row['Content'])?></textarea>
						<small id="contentHelp" class="form-text text-muted">
							Posts are written in <a href="https://www.markdownguide.org" target="_blank">Markdown</a>. HTML is not allowed for security reasons.
			      </small>
					</div>

					<!--<p><button class="btn btn-secondary" id="submit" value="submitted" type="submit">Publish</button></p>-->
				</div>
			</div>
			<div class="col-12 col-md-4">
				<div class="cell">
					<p>
						<button class="btn btn-secondary" id="submit" value="submitted" type="submit">
							Save
						</button>
					</p>
					<p class="">We will publish this update immediately.</p>
          <p class="mb-0">
            <?php if ($row['Path'] != "") { ?>
              View now at <a href="<?=autoUrl("posts/" . $row['Path'])?>">/posts/<?=$row['Path']?></a>
            <?php } else { ?>
              View now at <a href="<?=autoUrl("posts/" . $row['ID'])?>">/posts/<?=$row['ID']?></a>
            <?php } ?>
          </p>
				</div>

				<div class="cell">
					<h3>Meta</h3>
					<div class="form-group">
						<label for="path">Path</label>
						<p class="small mb-0">
							<?= autoUrl("posts/") ?>
						</p>
						<input type="text" class="form-control" name="path" id="path"
						placeholder="Leave blank to use Post ID" autocomplete="off"
						value="<?=htmlentities($row['Path'])?>">
					</div>
					<div class="form-group">
						<label for="date">Date</label>
						<input type="datetime-local" class="form-control" name="date"
						id="date" value="<?=$date->format("c")?>" disabled>
					</div>
					<div class="form-group">
						<label for="type">Type</label>
						<select class="custom-select" name="type">
							<?php for ($i = 0; $i < sizeof($post_types); $i++) {
								$s = null;
								if ($post_types[$i]['value'] == $row['Type']) {
									$s = "selected";
								}?>
							<option value="<?=$post_types[$i]['value']?>" <?= $s ?>>
								<?=$post_types[$i]['description']?>
							</option>
							<?php } ?>
						</select>
					</div>
					<div class="form-group mb-0">
						<label for="mime">MIME Type</label>
						<select class="custom-select" name="mime">
							<?php for ($i = 0; $i < sizeof($mimes); $i++) {
								$s = null;
								if ($mimes[$i]['value'] == $row['MIME']) {
									$s = "selected";
								}?>
							<option value="<?=$mimes[$i]['value']?>" <?= $s ?>>
								<?=$mimes[$i]['description']?>
							</option>
							<?php } ?>
						</select>
					</div>
				</div>

				<div class="cell">
					<h3>SEO</h3>
					<div class="form-group mb-0">
						<label for="excerpt">Excerpt</label>
						<textarea class="form-control" name="excerpt" id="excerpt"
						placeholder="This is about"
						autocomplete="off"><?=htmlentities($row['Excerpt'])?></textarea>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>

<?php $footer = new \SCDS\Footer();
$footer->addJs("public/js/posts/PostEditor.js");
$footer->render();
