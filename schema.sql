CREATE SCHEMA `forum` DEFAULT CHARACTER SET utf8mb4 DEFAULT COLLATE utf8mb4_unicode_ci;

USE `forum` ;

CREATE TABLE `post` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `thread` VARCHAR(256) NOT NULL,
  `author` VARCHAR(64) NOT NULL,
  `subject` VARCHAR(160) NOT NULL,
  `post` TEXT NOT NULL,
  `timestamp` DATETIME NOT NULL,
  `ip` VARCHAR(15) NOT NULL,
  `token` VARCHAR(16) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `child` (`thread` ASC)
);


CREATE TABLE `forum`.`image` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `post_id` INT UNSIGNED NOT NULL,
  `image` VARCHAR(128) NOT NULL,
  `thumbnail` VARCHAR(128) NULL,
  PRIMARY KEY (`id`),
  INDEX `image_idx` (`post_id` ASC),
  CONSTRAINT `image`
    FOREIGN KEY (`post_id`)
    REFERENCES `post` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
);

