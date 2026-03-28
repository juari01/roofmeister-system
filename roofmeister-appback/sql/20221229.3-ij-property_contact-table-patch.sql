CREATE TABLE `property_contact` (
  `contact_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `property_id` int(10) unsigned NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 0,
  `company` varchar(128) DEFAULT NULL,
  `first_name` varchar(128) DEFAULT NULL,
  `last_name` varchar(128) DEFAULT NULL,
  `phone_work` varchar(32) DEFAULT NULL,
  `phone_mobile` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`contact_id`),
  KEY `created` (`created`),
  KEY `updated` (`updated`),
  KEY `property_id` (`property_id`),
  KEY `active` (`active`),
  CONSTRAINT `property_contact_ibfk_1` FOREIGN KEY (`property_id`) REFERENCES `property` (`property_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;