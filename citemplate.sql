-- Adminer 4.1.0 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL DEFAULT 'user',
  `created_at` datetime NOT NULL,
  `modified_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `users` (`id`, `username`, `email`, `password`, `group`, `created_at`, `modified_at`) VALUES
(1,	'root',	'root@citemplate.dev',	'dTci7i6Ce9f0c0006e8f919e0c515c66dbba3982f78504==',	'developer',	'2015-08-24 19:49:23',	'2015-08-24 11:51:14');

-- 2015-08-31 05:36:26
