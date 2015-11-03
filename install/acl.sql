-- Adminer 4.1.0 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `groups`;
CREATE TABLE `groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `modified_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `groups` (`id`, `name`, `description`, `created_by`, `created_at`, `modified_at`) VALUES
(1,	'root',	'Developer account. Usually got special tools and sandbox for development purpose',	1,	'0000-00-00 00:00:00',	'2015-05-03 15:02:54'),
(2,	'superadmin',	'The system admin. This may be the highest user groups other than root. It roles are to manage other admins.',	1,	'0000-00-00 00:00:00',	'2015-05-03 15:02:46'),
(3,	'admin',	'This is the product owner',	1,	'2015-05-06 12:00:00',	'2015-05-06 07:25:20');

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `honorific` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `display_name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `hp` varchar(255) NOT NULL,
  `ic` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `fb_id` varchar(255) NOT NULL,
  `nexmo_credit` decimal(10,2) NOT NULL,
  `status` varchar(255) NOT NULL,
  `key` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  `modified_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `users` (`id`, `honorific`, `username`, `display_name`, `password`, `fullname`, `email`, `hp`, `ic`, `address`, `fb_id`, `nexmo_credit`, `status`, `key`, `created_at`, `modified_at`) VALUES
(1,	'',	'root',	'',	'dTci7i6Ce9f0c0006e8f919e0c515c66dbba3982f78504==',	'Root',	'',	'',	'',	'',	'',	0.00,	'',	'',	'0000-00-00 00:00:00',	'2015-05-03 15:03:11'),
(2,	'',	'superadmin',	'',	'eT6ieE1i9C1W1H7ZdJ7H50bee896fb0ad1e2ef5b0d366549efc11==',	'Super Admin',	'',	'',	'',	'',	'',	0.00,	'',	'',	'0000-00-00 00:00:00',	'2015-05-03 15:04:28'),
(3,	'',	'robotys',	'Izwan Robotys',	'7Tfi5EfibC6N2Pe3bf3c1b4810049298c2ce0389dff738007==',	'Izwan Robotys',	'robotys@gmail.com',	'0166611428',	'830531105809',	'Setiawangsa',	'',	0.00,	'',	'3b1ca1e5ba289ef3ded3ca9f5a2b94c6',	'2015-05-06 15:28:48',	'2015-05-06 09:15:11');

DROP TABLE IF EXISTS `user_group`;
CREATE TABLE `user_group` (
  `user_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `user_group` (`user_id`, `group_id`) VALUES
(1,	1),
(2,	2),
(3,	3);

-- 2015-08-04 09:18:33
