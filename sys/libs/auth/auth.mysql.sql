DROP TABLE IF EXISTS `auth`;
CREATE TABLE `auth`(
  `id`    INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` CHAR(32) NOT NULL,
  `pass` CHAR(40) NOT NULL,
  `date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;