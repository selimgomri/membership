<?php

$use_white_background = true;

global $db;
$query = null;

$exit_edit = true;

if ($int) {
	$sql = "SELECT * FROM `posts` WHERE `ID` = ?";
	try {
		$query = $db->prepare($sql);
		$query->execute([$id]);
	} catch (PDOException $e) {
		halt(500);
	}
} else {
	$sql = "SELECT * FROM `posts` WHERE `Path` = ?";
	try {
		$query = $db->prepare($sql);
		$query->execute([$id]);
	} catch (PDOException $e) {
		halt(500);
	}
}
$row = $query->fetch(PDO::FETCH_ASSOC);

if (!$row) {
  halt(404);
}

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
				<div class="cell">
					<h1 class="mb-0">Editing <?=htmlentities($row['Title'])?></h1>
				  <hr>
					<div class="form-group">
						<label for="title">Title</label>
						<?php if (!$people) { ?>
						<input type="text" class="form-control" name="title" id="title"
			      placeholder="Post Title" autocomplete="off" value="<?=htmlentities($row['Title'])?>">
			            <?php } else { ?>
			            <input type="text" class="form-control" name="title" id="title"
			      placeholder="Post Title" autocomplete="off" <?if($people){?>value="<?=getUserName($_SESSION['UserID'])?>" readonly<?}?>>
			            <?php } ?>
					</div>

					<div class="form-group mb-0">
						<label for="content">Content</label>
						<textarea class="form-control" id="content" name="content"
						rows="10"><?=$row['Content']?></textarea>
						<small id="contentHelp" class="form-text text-muted">
			        Styling may be stripped from this content
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
            <?php if ($people) { ?>
              View now at <a href="<?=autoUrl("people/" . $row['Path'])?>">/people/<?=$row['Path']?></a>
            <?php } else if ($row['Path'] != "") { ?>
              View now at <a href="<?=autoUrl("posts/" . $row['Path'])?>">/posts/<?=$row['Path']?></a>
            <?php } else { ?>
              View now at <a href="<?=autoUrl("posts/" . $row['ID'])?>">/posts/<?=$row['ID']?></a>
            <?php } ?>
          </p>
				</div>

                <?php if (!$people) { ?>
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
						id="date" value="<?=htmlentities($row['Date'])?>" disabled>
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
				<?php } ?>

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

<script>
 tinymce.init({
    selector: '#content',
    branding: false,
    plugins: [
      'autolink lists link image charmap print preview anchor textcolor',
      'searchreplace visualblocks code autoresize insertdatetime media table',
      'contextmenu paste code help wordcount'
    ],
    toolbar: 'insert | undo redo |  formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
    content_css: [
      'https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i',
      '<?php echo autoUrl("css/tinymce.css"); ?>'
    ]
      //toolbar: "link",
 });
</script>
<?php include BASE_PATH . "views/footer.php";
