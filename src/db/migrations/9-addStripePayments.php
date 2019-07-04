<?php

$db->query(
  "CREATE TABLE IF NOT EXISTS `stripePayments` (
    `ID` int(11) NOT NULL AUTO_INCREMENT,
    `User` int(11) NOT NULL,
    `DateTime` DATETIME NOT NULL,
    `Method` int(11) COLLATE utf8mb4_bin,
    `Intent` varchar(255) COLLATE utf8mb4_bin,
    `Amount` int(11) NOT NULL,
    `Currency` varchar(3) NOT NULL,
    `ServedBy` int(11),
    `Paid` BOOLEAN NOT NULL,
    PRIMARY KEY (`ID`),
    FOREIGN KEY (`User`) REFERENCES users(UserID),
    FOREIGN KEY (Method) REFERENCES stripePayMethods(ID),
    FOREIGN KEY (ServedBy) REFERENCES users(UserID)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;"
);