CREATE TABLE `property_type` (
  `type_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `active` tinyint(1) NOT NULL DEFAULT 0,
  `name` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`type_id`),
  KEY `created` (`created`),
  KEY `updated` (`updated`),
  KEY `active` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;