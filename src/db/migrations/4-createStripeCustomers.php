<?php

$db->query(
  "CREATE TABLE IF NOT EXISTS `stripeCustomers` (
    `ID` int(11) NOT NULL AUTO_INCREMENT,
    `User` int(11) NOT NULL,
    `CustomerID` varchar(255) COLLATE utf8mb4_bin NOT NULL,
    PRIMARY KEY (`ID`),
    FOREIGN KEY (User) REFERENCES users(UserID)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;"
);