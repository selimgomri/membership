<?php

global $db;

$use_white_background = true;

$null = $page;

if (isset($_GET['page'])) {
  $page = (int) $_GET['page'];
} else {
  $page = 1;
}

$start = 0;

if ($page != null) {
  $start = ($page-1)*10;
} else {
  $page = 1;
}

if ($page == 1 && $null != null) {
  header("Location: " . autoUrl("posts"));
  die();
}

$sql = "SELECT `ID` FROM `posts`";
try {
	$query = $db->prepare($sql);
	$query->execute();
} catch (PDOException $e) {
	halt(500);
}
$numPosts = sizeof($query->fetchAll(PDO::FETCH_ASSOC));
$numPages = ((int)($numPosts/10)) + 1;

if ($start > $numPosts) {
  halt(404);
}

$sql = "SELECT * FROM `posts` ORDER BY `Title` ASC, `Date` DESC LIMIT :start, 10;";
try {
	$query = $db->prepare($sql);
  $query->bindParam('start', $start, PDO::PARAM_INT);
	$query->execute();
} catch (PDOException $e) {
	 halt(500);
}
$rows = $query->fetchAll(PDO::FETCH_ASSOC);

$pagetitle = "Posts";

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/postsMenu.php";?>

<div class="container">
  <main class="">
    <h1>All Posts</h1>
    <?php if (sizeof($rows) > 0) { ?>
    <p class="lead pb-3 mb-0 border-bottom border-gray">
      Page <?php echo $page; ?> of <?php echo $numPages; ?>
    </p>
    <?php for ($i = 0; $i < sizeof($rows); $i++) {
      $row = $rows[$i]; ?>
      <div class="media pt-3">
        <div class="media-body pb-3 mb-0 lh-125 border-bottom border-gray force-wrap">
          <div class="d-block text-gray-dark mb-0">
            <?php
            $post_title = htmlspecialchars($row['Title']);
            $truncate = "";
            if (strlen($post_title) == 0) {
              $post_title = autoUrl($url);
              $truncate = "text-truncate";
            } ?>
            <p class="mb-0 <?=$truncate?>">
              <?php
              $url = "posts/" . $row['ID'];
              if ($row['Path'] != null && mb_strlen($row['Path']) > 0) {
                $url = "pages/" . $row['Path'];
              }
              ?>
							<a href="<?= autoUrl($url) ?>">
	              <strong>
                  <?=$post_title?>
	              </strong>
							</a>
            </p>
						<?php if ($row['Excerpt'] != "") { ?>
						<p class="mb-0">
							<?= htmlspecialchars($row['Excerpt']) ?>
						</p>
						<?php } ?>
            <p class="mb-0">
              First Published <?php echo date("d F Y", strtotime($row['Date'])); ?>,
              Last Updated <?php echo date("d F Y", strtotime($row['Modified'])); ?>
            </p>
          </div>
        </div>
      </div>
    <?php } ?>
    <?php } else { ?>
    <div class="alert alert-warning">
      <p class="mb-0">
        <strong>There are no posts to display</strong>
      </p>
      <p class="mb-0">
        Add a post and it will show up here
      </p>
    </div>
    <?php } ?>

    <nav aria-label="Page navigation">
      <ul class="pagination">
        <?php if ($numPosts <= 10) { ?>
        <li class="page-item active"><a class="page-link" href="<?php echo autoUrl("posts?page="); ?><?php echo $page ?>"><?php echo $page ?></a></li>
        <?php } else if ($numPosts <= 20) { ?>
          <?php if ($page == 1) { ?>
          <li class="page-item active"><a class="page-link" href="<?php echo autoUrl("posts?page="); ?><?php echo $page ?>"><?php echo $page ?></a></li>
    			<li class="page-item"><a class="page-link" href="<?php echo autoUrl("posts?page="); ?><?php echo $page+1 ?>"><?php echo $page+1 ?></a></li>
    			<li class="page-item"><a class="page-link" href="<?php echo autoUrl("posts?page="); ?><?php echo $page+1 ?>">Next</a></li>
          <?php } else { ?>
          <li class="page-item"><a class="page-link" href="<?php echo autoUrl("posts?page="); ?><?php echo $page-1 ?>">Previous</a></li>
    	    <li class="page-item"><a class="page-link" href="<?php echo autoUrl("posts?page="); ?><?php echo $page-1 ?>"><?php echo $page-1 ?></a></li>
    	    <li class="page-item active"><a class="page-link" href="<?php echo autoUrl("posts?page="); ?><?php echo $page ?>"><?php echo $page ?></a></li>
          <?php } ?>
        <?php } else { ?>
    			<?php if ($page == 1) { ?>
    			<li class="page-item active"><a class="page-link" href="<?php echo autoUrl("posts?page="); ?><?php echo $page ?>"><?php echo $page ?></a></li>
    	    <li class="page-item"><a class="page-link" href="<?php echo autoUrl("posts?page="); ?><?php echo $page+1 ?>"><?php echo $page+1 ?></a></li>
    			<li class="page-item"><a class="page-link" href="<?php echo autoUrl("posts?page="); ?><?php echo $page+2 ?>"><?php echo $page+2 ?></a></li>
    			<li class="page-item"><a class="page-link" href="<?php echo autoUrl("posts?page="); ?><?php echo $page+1 ?>">Next</a></li>
          <?php } else { ?>
    			<li class="page-item"><a class="page-link" href="<?php echo autoUrl("posts?page="); ?><?php echo $page-1 ?>">Previous</a></li>
    	    <li class="page-item"><a class="page-link" href="<?php echo autoUrl("posts?page="); ?><?php echo $page-1 ?>"><?php echo $page-1 ?></a></li>
    	    <li class="page-item active"><a class="page-link" href="<?php echo autoUrl("posts?page="); ?><?php echo $page ?>"><?php echo $page ?></a></li>
    			<?php if ($numPosts > $page*10) { ?>
    	    <li class="page-item"><a class="page-link" href="<?php echo autoUrl("posts?page="); ?><?php echo $page+1 ?>"><?php echo $page+1 ?></a></li>
    	    <li class="page-item"><a class="page-link" href="<?php echo autoUrl("posts?page="); ?><?php echo $page+1 ?>">Next</a></li>
          <?php } ?>
        <?php } ?>
      <?php } ?>
      </ul>
    </nav>
  </main>
</div>

<?php

include BASE_PATH . "views/footer.php";
