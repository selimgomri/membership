<?php
$pagetitle = "[REACT DEV] Notify Composer";

$db = app()->db;
$tenant = app()->tenant;

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/notifyMenu.php";

?>

<div id="scds-react-root"></div>

<?php $footer = new \SCDS\Footer();
// $footer->addJS("js/tinymce/5/tinymce.min.js");
// $footer->addJS("js/notify/TinyMCE.js?v=1");
// $footer->addJS("js/notify/FileUpload.js");
// $footer->addJS("js/dropzone/dropzone.js");
// $footer->addJS("js/notify/FileUploadDropzone.js");
$footer->render();
