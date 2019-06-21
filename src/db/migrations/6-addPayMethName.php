<?php

$db->query(
  "ALTER TABLE stripePayMethods ADD `Name` varchar(255) COLLATE utf8mb4_bin;"
);