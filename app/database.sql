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
  `url`   VARCHAR(22) NOT NULL,
  `keyw` VARCHAR(300) NOT NULL,
  `desc` VARCHAR(300) NOT NULL,
  PRIMARY KEY(`id`),
  INDEX(`lang`),
  INDEX(`class`),
  INDEX(`url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

/**
 * @author Hector Menendez <h@cun.mx>
 * @licence http://etor.mx/licence.txt
 * @updated 2011/SEP/16 03:37 Fixed error, removed `class` as UNIQUE.
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
  `urln`  VARCHAR(128) NOT NULL, /* url safe name     */
  `urlc`   VARCHAR(22) NOT NULL, /* url safe category */
  `urli`  VARCHAR(512) NOT NULL, /* url image         */
  `price` DECIMAL(9,2) NOT NULL DEFAULT 0,
  PRIMARY KEY(`id`),
  INDEX(`class`),
  INDEX(`lang`),
  INDEX(`categ`),
  INDEX(`urln`),
  INDEX(`urlc`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

/**
 * @author Hector Menendez <h@cun.mx>
 * @licence http://etor.mx/licence.txt
 * @created 2011/SEP/19 13:26
 */
DROP TABLE IF EXISTS `stock`;
CREATE TABLE `stock`(
  `id`         CHAR(16) NOT NULL,
  `product` VARCHAR(44) NOT NULL,
  `created`    DATETIME NOT NULL,
  `expires`    DATETIME NOT NULL,
  `valided`  TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  `printed`  TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY(`id`),
  INDEX(`product`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/**
 * @author Hector Menendez <h@cun.mx>
 * @licence http://etor.mx/licence.txt
 * @created 2011/SEP/21 15:00
 */
DROP TABLE IF EXISTS `static`;
CREATE TABLE `static`(
  `id`         INT(4) UNSIGNED NOT NULL AUTO_INCREMENT,
  `class` VARCHAR(12) NOT NULL,
  `lang`      CHAR(2) NOT NULL,
  `name`  VARCHAR(24) NOT NULL,
  `keyw` VARCHAR(300) NOT NULL,
  `desc` VARCHAR(300) NOT NULL,
  PRIMARY KEY(`id`),
  INDEX(`class`),
  INDEX(`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;
INSERT INTO `static` (`lang`,`class`,`name`,`keyw`,`desc`) VALUES
  ('es','products'  ,'productos'   ,' ', ' '),
  ('en','products'  ,'products'    ,' ', ' '),
  ('es','authentic' ,'autenticidad',' ', ' '),
  ('en','authentic' ,'authenticity',' ', ' '),
  ('es','about-us'  ,'nosotros'    ,' ', ' '),
  ('en','about-us'  ,'about us'    ,' ', ' '),
  ('es','contact-us','contacto'    ,' ', ' '),
  ('en','contact-us','contact us'  ,' ', ' ')
;