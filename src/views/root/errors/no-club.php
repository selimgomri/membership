<?php

http_response_code(404);
$pagetitle = "Error 404 - Page not found";

include BASE_PATH . "views/root/header.php";

?>

<div class="container">
  <div class="row">
    <div class="col-lg-8">
      <h1>No club</h1>
      <p class="lead">We could not find a tenant matching <span class="mono"><?=htmlspecialchars($club)?></span>.</p>

      <p>Please try visiting our homepage to find your club.</p>

      <p>HTTP Error 404 - File or directory not found.</p>
      <hr>

      <p class="mt-2">Contact our <a href="mailto:support@myswimmingclub.uk" title="Support Hotline">support hotline</a> if the issue persists.</p>
      
    </div>
  </div>
</div>

<?php $footer = new \SCDS\RootFooter();
$footer->render(); ?>