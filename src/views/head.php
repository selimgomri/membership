<?php

$currentUser = app()->user;
$cvp = 'generic';
if (app()->tenant->isCLS() && $currentUser != null && $currentUser->getUserBooleanOption('UsesGenericTheme')) {
  $cvp = 'generic';
} else if (app()->tenant->isCLS()) {
  $cvp = 'chester';
}

include $cvp . '/GlobalHead.php';
