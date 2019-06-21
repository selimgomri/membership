<?php

$db->query(
  "CREATE TABLE IF NOT EXISTS `stripePayMethods` (
    `ID` int(11) NOT NULL AUTO_INCREMENT,
    `Customer` varchar(255) COLLATE utf8mb4_bin NOT NULL,
    `MethodID` varchar(255) COLLATE utf8mb4_bin NOT NULL,
    `CardName` varchar(255) COLLATE utf8mb4_bin,
    `City` varchar(255) COLLATE utf8mb4_bin,
    `Country` varchar(255) COLLATE utf8mb4_bin,
    `Line1` varchar(255) COLLATE utf8mb4_bin,
    `Line2` varchar(255) COLLATE utf8mb4_bin,
    `PostCode` varchar(255) COLLATE utf8mb4_bin,
    `Brand` varchar(255) COLLATE utf8mb4_bin,
    `IssueCountry` varchar(255) COLLATE utf8mb4_bin,
    `ExpMonth` varchar(255) COLLATE utf8mb4_bin,
    `ExpYear` varchar(255) COLLATE utf8mb4_bin,
    `Funding` varchar(255) COLLATE utf8mb4_bin,
    `Last4` varchar(255) COLLATE utf8mb4_bin,
    PRIMARY KEY (`ID`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;"
);