
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user`(
  `id`        INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user`  VARCHAR(12) NOT NULL,
  `email` VARCHAR(99) NOT NULL,
  `name`  VARCHAR(66) NOT NULL,
  `pass`     CHAR(40) NOT NULL,            -- sha1 --
  `perm`   TINYINT(1) NOT NULL  DEFAULT 0, -- bitwise permissions --
  `date`    TIMESTAMP NOT NULL  DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

INSERT INTO `user` (`user`,`email`,`name`,`pass`,`perm`,`date`) VALUES 
  ('et0r','etor.mx@gmail.com',   'Héctor Menéndez','e81e65fcd586c7ebac8164764d9d1e831897e902',1,'1981-06-23 03:33:33'),
  ('bltd','bestlabs@hotmail.com','Roberto Valdés', 'c6eedfae90c4fa6ac692a85b1f83ed20a90e4fe5',1,'2011-06-08 00:00:00');

DROP TABLE IF EXISTS `language`;
CREATE TABLE `language`(
  `id`    TINYINT(1) UNSIGNED NOT NULL AUTO_INCREMENT,
  `code`     CHAR(2) NOT NULL,
  `name` VARCHAR(12) NOT NULL,
  PRIMARY KEY(`id`),
  INDEX (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

INSERT INTO `language` (`code`,`name`) VALUES
  ('es','español'),
  ('en','english');

DROP TABLE IF EXISTS `category`;
CREATE TABLE `category`(
  `id`               INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_language`   TINYINT(1) UNSIGNED NOT NULL,
  `name`         VARCHAR(66) NOT NULL,
  PRIMARY KEY(`id`),
  INDEX(`id_language`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `product`;
CREATE TABLE `product`(
  `id`               INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_category`      INT(10) UNSIGNED NOT NULL,
  `id_language`   TINYINT(1) UNSIGNED NOT NULL,
  `code`            CHAR(32) NOT NULL,
  `name`         VARCHAR(66) NOT NULL,
  `content`       MEDIUMTEXT NOT NULL,
  `image`       VARCHAR(512) NOT NULL,
  `description` VARCHAR(300) NOT NULL,
  `keywords`    VARCHAR(300) NOT NULL,
  `price`       DECIMAL(9,2) NOT NULL DEFAULT 0,
  PRIMARY KEY(`id`),
  INDEX(`id_category`),
  INDEX(`id_language`),
  INDEX(`code`),
  INDEX(`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

