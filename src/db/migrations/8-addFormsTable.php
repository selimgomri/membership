<?php

$db->query(
  "CREATE TABLE IF NOT EXISTS `completedForms` (
    `ID` int(11) NOT NULL AUTO_INCREMENT,
    `Member` int(11) NULL,
    `User` int(11) NULL,
    `Form` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
    `Date` date NOT NULL,
    `About` text COLLATE utf8mb4_bin DEFAULT NULL,
    PRIMARY KEY (`ID`),
    FOREIGN KEY (User) REFERENCES users(UserID),
    FOREIGN KEY (Member) REFERENCES members(MemberID)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;"
);