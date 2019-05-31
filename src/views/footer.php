<?php

global $currentUser;
$cvp = CLUB_VIEW_PATH;
if (defined('IS_CLS') && IS_CLS && $currentUser->getUserBooleanOption('UsesGenericTheme')) {
  $cvp = 'generic';
}

include $cvp . '/footer.php';
