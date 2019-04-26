<?php

$path = null;

if ($type == 'files') {
  $path = autoUrl("files/" . $filename);
} else if ($type == 'public') {
  $path = autoUrl("public/" . $filename);
}

?>

<!DOCTYPE html>
<html>
  <head>
    <style>
      html, body {
        padding: 0;
        margin: 0;
        width: 100%;
        height: 100%;
        max-width: 100%;
        max-height: 100vh;
        overflow: hidden;
      }
      iframe {
        width: 100%;
        height: 100%;
        max-width: 100%;
        max-height: 100vh;
        padding: 0;
        margin: 0;
        border: 0;
      }
    </style>
  </head>
  <body>
    <iframe src="<?=autoUrl("public/js/pdf-js/web/viewer.html?file=" . urlencode($path))?>" height="100vh" width="100%"></iframe>
  </body>
</html>
