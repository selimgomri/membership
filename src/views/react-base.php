<?php

$pagetitle = "React";

include BASE_PATH . "views/header.php";

?>
<div id="root"></div>
<?php

$footer = new SCDS\Footer();
$footer->addJs(getCompiledAsset('main-react-router.js'));
$footer->render();
