<?php

$db->query(
  "ALTER TABLE galaEntries ADD AmountRefunded INT DEFAULT '0';"
);