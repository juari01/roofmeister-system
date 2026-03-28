CREATE TABLE `customer` (
  `customer_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `name` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`customer_id`),
  KEY `created` (`created`),
  KEY `updated` (`updated`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `state` (
  `state_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `state` varchar(64) DEFAULT NULL,
  `state_abbr` varchar(4) DEFAULT NULL,
  PRIMARY KEY (`state_id`),
  KEY `created` (`created`),
  KEY `updated` (`updated`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `property` (
  `property_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `name` varchar(128) DEFAULT NULL,
  `address1` varchar(128) DEFAULT NULL,
  `address2` varchar(128) DEFAULT NULL,
  `city` varchar(128) DEFAULT NULL,
  `state_id` int(10) unsigned NOT NULL,
  `zip` varchar(16) DEFAULT NULL,
  PRIMARY KEY (`property_id`),
  KEY `created` (`created`),
  KEY `updated` (`updated`),
  KEY `state_id` (`state_id`),
  CONSTRAINT `property_ibfk_1` FOREIGN KEY (`state_id`) REFERENCES `state` (`state_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `xref_customer_property` (
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `customer_id` int(10) unsigned NOT NULL,
  `property_id` int(10) unsigned NOT NULL,
  KEY `created` (`created`),
  KEY `updated` (`updated`),
  KEY `customer_id` (`customer_id`),
  KEY `property_id` (`property_id`),
  CONSTRAINT `xref_customer_property_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`),
  CONSTRAINT `xref_customer_property_ibfk_2` FOREIGN KEY (`property_id`) REFERENCES `property` (`property_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

