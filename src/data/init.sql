CREATE DATABASE IF NOT EXISTS shrink;
USE shrink;

CREATE TABLE IF NOT EXISTS `urls` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `alias` varchar(50) NOT NULL,
  `url` text NOT NULL,
  `ip` varchar(39) NOT NULL,
  `time` timestamp DEFAULT CURRENT_TIMESTAMP,
  `active` tinyint(1) DEFAULT 1 NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `alias` (`alias`)
);

CREATE TABLE IF NOT EXISTS `visits` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `alias` varchar(50) NOT NULL,
  `ip` varchar(39) NOT NULL,
  `browser` text,
  `referrer` text,
  `time` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `errors` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `error_code` varchar(50) NOT NULL,
  `message` text,
  PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `error_log` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `error_code` varchar(50) NOT NULL,
  `message` text,
  `ip` varchar(39) NOT NULL,
  `browser` text,
  `referrer` text,
  `time` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `phish_tank` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `phish_id` int(255) unsigned NOT NULL,
  `url` text,
  PRIMARY KEY (`id`),
  UNIQUE INDEX (`phish_id`)
);

INSERT INTO `errors` (`error_code`,`message`) VALUES 
('phish_on_the_line','This url has previously been flagged for phishing. <a href="http://www.phishtank.com/phish_detail.php?phish_id={var_replace[0]}" target="_blank">More Info...</a>');