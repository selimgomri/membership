<?php

global $db;

try {

  $get = $db->prepare("SELECT COUNT(*) FROM linkedAccounts WHERE ID = ? AND `Key` = ? AND Active = ?");
  $get->execute([$id, $key, 0]);

  if ($get->fetchColumn() == 0) {
    halt(404);
  }

  $update = $db->prepare("UPDATE linkedAccounts SET Active = ? WHERE ID = ?");
  $update->execute([1, $id]);

} catch (Exception $e) {
  halt(500);
}

include BASE_PATH . 'views/header.php';

?>

<div class="container">
  <div class="row">
    <div class="col-lg-8">
      <h1>We've confirmed your linked account!</h1>
      <p class="lead">You can get going straight away.</p>
      <p><a href="<?=autoUrl("")?>">Return to home</a></p>
    </div>
  </div>
</div>

<?php

$footer = new \SCDS\Footer();
$footer->render();