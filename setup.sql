create database COP4331;
use COP4331;

create user 'karel' identified by 'group9';
grant all privileges on COP4331.* to 'karel'@'%';


CREATE TABLE
    `users` (
        `ID` INT NOT NULL AUTO_INCREMENT,
        `firstname` VARCHAR(50) NOT NULL DEFAULT '',
        `lastname` VARCHAR(50) NOT NULL DEFAULT '',
        `username` VARCHAR(50) NOT NULL DEFAULT '',
        `password` VARCHAR(60) NOT NULL DEFAULT '',
        PRIMARY KEY (`ID`)
    ) ENGINE = InnoDB;

CREATE TABLE
    `contacts` (
        `ID` INT NOT NULL AUTO_INCREMENT,
        `firstname` VARCHAR(50) NOT NULL DEFAULT '',
        `lastname` VARCHAR(50) NOT NULL DEFAULT '',
        `email` VARCHAR(50) NOT NULL DEFAULT '',
        `phone` VARCHAR(50) NOT NULL DEFAULT '',
        `UserID` INT NOT NULL DEFAULT 0,
        PRIMARY KEY (`ID`),
        CONSTRAINT `fk_contacts_users` FOREIGN KEY (`UserID`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE = InnoDB;

INSERT INTO
    users (firstname, lastname, username, password)
VALUES
    ("Ty", "Singh", "kight", "0192023a7bbd73250516f069df18b500");
INSERT INTO
    users (firstname, lastname, username, password)
VALUES
    ("Daniyl", "idk", "carman", "512d25bbb054694d9460fde14fa6861d");


INSERT INTO
    contacts (firstname, phone, userid)
VALUES
    ("john", "123456680", 1);

INSERT INTO
    contacts (firstname, phone, userid)
VALUES
    ("jane", "123456680", 1);

INSERT INTO
    contacts (firstname, phone, userid)
VALUES
    ("jack", "123456680", 1);


INSERT INTO
    contacts (firstname, phone, userid)
VALUES
    ("jill", "123456680", 1);

INSERT INTO
    contacts (firstname, phone, userid)
VALUES
    ("john", "123456680", 2);

INSERT INTO
    contacts (firstname, phone, userid)
VALUES
    ("jane", "123456680", 2);

INSERT INTO
    contacts (firstname, phone, userid)
VALUES
    ("jack", "123456680", 2);

INSERT INTO
    contacts (firstname, phone, userid)
VALUES
    ("jill", "123456680", 2);