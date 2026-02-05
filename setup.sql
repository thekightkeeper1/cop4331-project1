create database project1;

use project1;

CREATE TABLE
    `project1`.`users` (
        `ID` INT NOT NULL AUTO_INCREMENT,
        `firstname` VARCHAR(50) NOT NULL DEFAULT '',
        `lastname` VARCHAR(50) NOT NULL DEFAULT '',
        `username` VARCHAR(50) NOT NULL DEFAULT '',
        `password` VARCHAR(50) NOT NULL DEFAULT '',
        PRIMARY KEY (`ID`)
    ) ENGINE = InnoDB

CREATE TABLE
    `project1`.`contacts` (
        `ID` INT NOT NULL AUTO_INCREMENT,
        `firstname` VARCHAR(50) NOT NULL DEFAULT '',
        `lastname` VARCHAR(50) NOT NULL DEFAULT '',
        `email` VARCHAR(50) NOT NULL DEFAULT '',
        `phone` VARCHAR(50) NOT NULL DEFAULT '',
        `UserID` INT NOT NULL DEFAULT 0,
        PRIMARY KEY (`ID`), -- <--- THIS COMMA IS REQUIRED
        CONSTRAINT `fk_contacts_users` FOREIGN KEY (`UserID`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE = InnoDB;

INSERT INTO
    users (firstname, lastname, username, password)
VALUES
    ("Ty", "Singh", "kight", "admin123");

INSERT INTO
    contacts (firstname, phone, userid)
VALUES
    ("Daniyl", "123456680", 1);

DELETE FROM `users` where `id` MATCHES 2; = 