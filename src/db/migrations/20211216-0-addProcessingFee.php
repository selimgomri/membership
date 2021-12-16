<?php

$db->query(
  "ALTER TABLE `galas`
  ADD COLUMN `ProcessingFee` int DEFAULT 0;"
);