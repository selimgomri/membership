<?php

http_response_code(404);
$pagetitle = "Error 404 - Page not found";

include BASE_PATH . "views/root/header.php";

?>

<div class="container">
  <div class="row">
    <div class="col-lg-8">
      <h1>The page you requested cannot be found</h1>
      <p class="lead">The page you are looking for might have been removed, had its name changed, or is temporarily
        unavailable. You may also not be authorised to view the page.</p>

      <hr>
      <p>Please try the following:</p>
      <ul>
        <li>Make sure that the Web site address displayed in the address bar of your browser is spelled and formatted
          correctly.</li>
        <li>If you reached this page by clicking a link, contact the Web site administrator to alert them that the link
          is incorrectly formatted.</li>
        <li>Click the <a href="javascript:history.back(1)">Back</a> button to try another link.</li>
      </ul>
      <p>HTTP Error 404 - File or directory not found.</p>
      <hr>

      <p class="mt-2">Contact our <a href="mailto:support@myswimmingclub.uk" title="Support Hotline">support hotline</a><?php if (!bool(env('IS_CLS'))) { ?>*<?php } ?> if the issue persists.</p>

      <?php if (!bool(env('IS_CLS'))) { ?>
      <p>* <a href="mailto:<?=htmlspecialchars(app()->tenant->getKey('CLUB_EMAIL'))?>" title="<?=htmlspecialchars(app()->tenant->getKey('CLUB_NAME'))?>">Contact your own club</a> in the first instance</p>
      <?php } ?>
    </div>
  </div>
</div>

<?php $footer = new \SCDS\RootFooter();
$footer->render(); ?>