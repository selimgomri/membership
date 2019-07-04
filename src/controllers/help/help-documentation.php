<?php

$markdown = new ParsedownForMembership();

$path = ltrim($this[0], '/');
$file = file_get_contents(BASE_PATH . 'help/' . $path . '.md');

if ($file === false) {
  $file = file_get_contents(BASE_PATH . 'help/' . $path . '/index.md');
}

if ($file === false) {
  halt(404);
}

$pagetitle = "Help and Support Documentation";

$markdownOutput = $markdown->text($file);
$headings = $markdown->getHeadings();

if (sizeof($headings) > 0) {
  $pagetitle = htmlspecialchars($headings[0]);
}

include BASE_PATH . 'views/header.php';

?>

<div class="container">
  <div class="row justify-content-center">
    <div class="col-lg-8 col-md-10">
      <main class="my-5 serif">
        <?= $markdown->text($file) ?>
      </main>
      <aside class="cell">
        <h2>Onward Content</h2>
      </aside>
    </div>
  </div>
</div>

<?php

include BASE_PATH . 'views/footer.php';