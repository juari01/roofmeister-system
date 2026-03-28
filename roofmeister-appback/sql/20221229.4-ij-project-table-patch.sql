CREATE TABLE `project` (
  `project_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `customer_id` int(10) unsigned DEFAULT NULL,
  `property_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(128) DEFAULT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`project_id`),
  KEY `created` (`created`),
  KEY `updated` (`updated`),
  KEY `customer_id` (`customer_id`),
  KEY `property_id` (`property_id`),
  CONSTRAINT `project_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`),
  CONSTRAINT `project_ibfk_2` FOREIGN KEY (`property_id`) REFERENCES `property` (`property_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;