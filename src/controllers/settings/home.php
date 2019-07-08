<?php

$fluidContainer = true;

global $db;
$codesOfConduct = $db->query("SELECT Title, ID FROM posts WHERE `Type` = 'conduct_code' ORDER BY Title ASC");

global $systemInfo;
$parentCode = $systemInfo->getSystemOption('ParentCodeOfConduct');

$Extra = new ParsedownExtra();
$Extra->setSafeMode(true);
$search  = array("\n##### ", "\n#### ", "\n### ", "\n## ", "\n# ");
$replace = array("\n###### ", "\n###### ", "\n##### ", "\n#### ", "\n### ");

$codeOfConduct = null;
if ($parentCode != null && $parentCode != "") {
  $codeOfConduct = $db->prepare("SELECT Content FROM posts WHERE ID = ?");
  $codeOfConduct->execute([$parentCode]);
  $codeOfConduct = str_replace($search, $replace, $codeOfConduct->fetchColumn());
  if ($codeOfConduct[0] == '#') {
    $codeOfConduct = '##' . $codeOfConduct;
  }
}

$pagetitle = "Parent Code of Conduct Options";

include BASE_PATH . 'views/header.php';

?>

<div class="container-fluid">
  <div class="row justify-content-between">
    <aside class="col-md-3 d-none d-md-block">
      <?php
        $list = new \CLSASC\BootstrapComponents\ListGroup(file_get_contents(BASE_PATH . 'controllers/settings/SettingsLinkGroup.json'));
        echo $list->render('settings-home');
      ?>
    </aside>
    <div class="col-md-9">
      <main>
        <h1>System Settings</h1>
        <p class="lead">Manage system options</p>
      </main>
    </div>
  </div>
</div>

<?php

include BASE_PATH . 'views/footer.php';