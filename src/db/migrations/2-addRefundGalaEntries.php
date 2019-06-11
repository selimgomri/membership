<?php

$db->query(
  "ALTER TABLE galaEntries ADD Refunded BOOLEAN DEFAULT '0';"
);