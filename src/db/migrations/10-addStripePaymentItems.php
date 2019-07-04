<?php

$db->query(
  "CREATE TABLE IF NOT EXISTS `stripePaymentItems` (
    `ID` int(11) NOT NULL AUTO_INCREMENT,
    `Payment` int(11) NOT NULL,
    `Name` varchar(255) COLLATE utf8mb4_bin,
    `Description` text COLLATE utf8mb4_bin,
    `Amount` int(11) NOT NULL,
    `Currency` varchar(3) NOT NULL,
    PRIMARY KEY (`ID`),
    FOREIGN KEY (Payment) REFERENCES stripePayments(ID)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;"
);