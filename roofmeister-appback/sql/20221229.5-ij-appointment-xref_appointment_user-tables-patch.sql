--
-- Table structure for table `appointment`
--
CREATE TABLE `appointment` (
  `appointment_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `customer_id` int(10) unsigned DEFAULT NULL,
  `property_id` int(10) unsigned DEFAULT NULL,
  `project_id` int(10) unsigned DEFAULT NULL,
  `type_id` int(10) unsigned NOT NULL,
  `start` datetime DEFAULT NULL,
  `end` datetime DEFAULT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`appointment_id`),
  KEY `created` (`created`),
  KEY `updated` (`updated`),
  KEY `customer_id` (`customer_id`),
  KEY `property_id` (`property_id`),
  KEY `project_id` (`project_id`),
  KEY `type_id` (`type_id`),
  KEY `start` (`start`),
  CONSTRAINT `ck_id` CHECK (`customer_id` is not null and `property_id` is not null and `project_id` is null or `customer_id` is null and `property_id` is null and `project_id` is not null)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Table structure for table `xref_appointment_user`
--
CREATE TABLE `xref_appointment_user` (
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `appointment_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  KEY `created` (`created`),
  KEY `updated` (`updated`),
  KEY `user_id` (`user_id`),
  KEY `appointment_id` (`appointment_id`),
  CONSTRAINT `xref_appointment_user_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointment` (`appointment_id`),
  CONSTRAINT `xref_appointment_user_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;