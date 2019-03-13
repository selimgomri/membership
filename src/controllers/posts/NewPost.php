<?php
$use_white_background = true;

$pagetitle = "New Post";
include BASE_PATH . "views/header.php";
include BASE_PATH . "views/postsMenu.php";

 ?>

<div class="container">
	<form method="post">
		<div class="row">
			<div class="col-md-8">
				<div class="cell">
					<h1 class="mb-0">New Post</h1>
				  <hr>
					<div class="form-group">
						<label for="title">Title</label>
						<input type="text" class="form-control" name="title" id="title"
			      placeholder="Post Title" autocomplete="off" <?if($people){?>value="<?=getUserName($_SESSION['UserID'])?>" readonly<?}?>>
					</div>

					<div class="form-group mb-0">
						<label for="content">Content</label>
						<textarea class="form-control" id="content" name="content" rows="10">
			      </textarea>
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
							Publish
						</button>
					</p>
					<p class="mb-0">We will publish this immediately.</p>
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
			      placeholder="Leave blank to use Post ID" autocomplete="off">
					</div>
					<div class="form-group">
						<label for="date">Date</label>
						<input type="datetime-local" class="form-control" name="date" id="date">
					</div>
					<div class="form-group">
						<label for="type">Type</label>
						<select class="custom-select" name="type">
							<option value="user_notice">Notice to Users</option>
							<option value="gala_notice">Gala Notice</option>
						  <option value="conduct_code">Code of Conduct</option>
						  <option value="terms_conditions">Terms and Conditions</option>
						  <option value="staff_notice">Staff Notice</option>
							<option value="account_help">Account Help</option>
							<option value="people_pages">People</option>
						</select>
					</div>
					<div class="form-group mb-0">
						<label for="mime">MIME Type</label>
						<select class="custom-select" name="mime">
						  <option value="text/html">text/html</option>
						  <option value="text/plain">text/plain</option>
						</select>
					</div>
				</div>
				<?php } ?>

				<div class="cell">
					<h3>SEO</h3>
					<div class="form-group mb-0">
						<label for="excerpt">Excerpt</label> <textarea class="form-control"
						name="excerpt" id="excerpt" placeholder="This is about"
						autocomplete="off"></textarea>
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
    ],
    relative_urls : false,
    remove_script_host : false
      //toolbar: "link",
 });
</script>
<?php include BASE_PATH . "views/footer.php";
