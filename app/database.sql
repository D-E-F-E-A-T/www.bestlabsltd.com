/**
 * @author Hector Menendez <h@cun.mx>
 * @licence http://etor.mx/licence.txt
 * @updated 2011/SEP/16 03:36 Fixed error, removed AUTO_INCREMENT declaration.
 * @created 2011/SEP/03 03:46
 */
DROP TABLE IF EXISTS `language`;
CREATE TABLE `language`(
  `id`       CHAR(2) NOT NULL,
  `name` VARCHAR(12) NOT NULL,
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `language` (`id`,`name`) VALUES
  ('es','Español'),
  ('en','English');

/**
 * @author Hector Menendez <h@cun.mx>
 * @licence http://etor.mx/licence.txt
 * @created 2011/SEP/03 03:55
 */
DROP TABLE IF EXISTS `category`;
CREATE TABLE `category` (
  `id`         INT(4) UNSIGNED NOT NULL AUTO_INCREMENT,
  `lang`      CHAR(2) NOT NULL,
  `class` VARCHAR(22) NOT NULL,
  `name`  VARCHAR(22) NOT NULL,
  `keyw`  VARCHAR(300) NOT NULL,
  `desc`  VARCHAR(300) NOT NULL,
  PRIMARY KEY(`id`),
  INDEX(`lang`),
  INDEX(`class`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

/**
 * @author Hector Menendez <h@cun.mx>
 * @licence http://etor.mx/licence.txt
 * @updayed 2011/SEP/16 03:37 Fixed error, removed `class` as UNIQUE.
 * @created 2011/SEP/03 04:09
 */
DROP TABLE IF EXISTS `product`;
CREATE TABLE `product`(
  `id`         INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `lang`       CHAR(2) NOT NULL,
  `categ`  VARCHAR(22) NOT NULL,
  `class`  VARCHAR(44) NOT NULL,
  `name`   VARCHAR(66) NOT NULL,
  `cont`    MEDIUMTEXT NOT NULL,
  `keyw`  VARCHAR(300) NOT NULL,
  `desc`  VARCHAR(300) NOT NULL,
  `path`  VARCHAR(512) NOT NULL, /*       URI */
  `image` VARCHAR(512) NOT NULL, /* Image URL */
  `price` DECIMAL(9,2) NOT NULL DEFAULT 0,
  PRIMARY KEY(`id`),
  INDEX(`class`),
  INDEX(`lang`),
  INDEX(`categ`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

