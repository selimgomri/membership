<?php

$currentUser = null;
if (isset(app()->user)) {
  $currentUser = app()->user;
}
$cvp = 'generic';
if (isset(app()->tenant) && app()->tenant->isCLS() && $currentUser != null && $currentUser->getUserBooleanOption('UsesGenericTheme')) {
  $cvp = 'generic';
} else if (isset(app()->tenant) && app()->tenant->isCLS()) {
  $cvp = 'chester';
}

include $cvp . '/GlobalHead.php';
