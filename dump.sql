-- Adminer 4.2.5 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `customer`;
CREATE TABLE `customer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `cnp` tinyint(1) NOT NULL,
  `balance` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `customer` (`id`, `name`, `cnp`, `balance`) VALUES
(1,	'customer1',	1,	0.00),
(2,	'customer2',	1,	0.00);

DROP TABLE IF EXISTS `migration_versions`;
CREATE TABLE `migration_versions` (
  `version` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `migration_versions` (`version`) VALUES
('20170302223206'),
('20170302231255'),
('20170302235250'),
('20170304005808');

DROP TABLE IF EXISTS `transaction`;
CREATE TABLE `transaction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customerId` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `date` datetime NOT NULL,
  `status` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `transaction` (`id`, `customerId`, `amount`, `date`, `status`) VALUES
(2,	2,	50.00,	'2017-03-02 23:34:08',	0),
(5,	1,	250.00,	'2017-03-03 22:14:37',	0),
(6,	1,	693.00,	'2017-03-03 22:14:48',	0),
(10,	1,	5479.65,	'2017-03-03 22:18:21',	0),
(11,	1,	950.00,	'2017-03-03 23:58:10',	0),
(12,	1,	489.00,	'2017-03-03 23:58:18',	0),
(13,	1,	867.00,	'2017-03-03 23:58:26',	0),
(14,	1,	670.00,	'2017-03-04 10:50:57',	0),
(15,	1,	960.00,	'2017-03-04 12:33:47',	0),
(16,	1,	540.00,	'2017-03-04 12:34:31',	0),
(17,	1,	678.00,	'2017-03-04 12:43:48',	0),
(18,	1,	456.00,	'2017-03-04 12:44:35',	0);

DROP TABLE IF EXISTS `transactions_total`;
CREATE TABLE `transactions_total` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `total` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `transactions_total` (`id`, `date`, `total`) VALUES
(1,	'2017-03-03 00:00:00',	8231.65);

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(180) COLLATE utf8_unicode_ci NOT NULL,
  `username_canonical` varchar(180) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(180) COLLATE utf8_unicode_ci NOT NULL,
  `email_canonical` varchar(180) COLLATE utf8_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `salt` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `confirmation_token` varchar(180) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password_requested_at` datetime DEFAULT NULL,
  `roles` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT '(DC2Type:array)',
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8D93D64992FC23A8` (`username_canonical`),
  UNIQUE KEY `UNIQ_8D93D649A0D96FBF` (`email_canonical`),
  UNIQUE KEY `UNIQ_8D93D649C05FB297` (`confirmation_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `user` (`id`, `username`, `username_canonical`, `email`, `email_canonical`, `enabled`, `salt`, `password`, `last_login`, `confirmation_token`, `password_requested_at`, `roles`, `created`, `updated`) VALUES
(1,	'admin',	'admin',	'admin@admin.com',	'admin@admin.com',	1,	NULL,	'$2y$13$Gk0Ml.skGrUSOkmJpwpCUOhhzwkRXAt/Omfk1cFud72E0FS8mF6oy',	'2017-03-04 10:13:41',	NULL,	NULL,	'a:1:{i:0;s:10:\"ROLE_ADMIN\";}',	'2017-03-03 14:36:15',	'2017-03-04 10:13:41');

-- 2017-03-04 12:53:00
