<?php

use Respect\Validation\Validator as v;

if (v::email()->validate($_POST['reply'])) {
  setUserOption($_SESSION['UserID'], 'NotifyReplyAddress', $_POST['reply']);
  $_SESSION['SetReplySuccess'] = true;
} else {
  $_SESSION['SetReplyFalse'] = true;
}

header("Location: " . autoUrl("notify/reply-to"));