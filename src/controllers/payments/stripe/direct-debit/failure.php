<?php

$_SESSION['TENANT-' . app()->tenant->getId()]['StripeDDError'] = true;
header("location: " . autoUrl("payments/direct-debit/set-up"));