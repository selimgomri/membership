<?php

$db->query(
  "ALTER TABLE joinSwimmers MODIFY COLUMN ASA varchar(255) COLLATE utf8mb4_bin;"
);