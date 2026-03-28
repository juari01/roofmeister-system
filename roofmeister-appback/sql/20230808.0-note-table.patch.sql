CREATE TABLE `note` (
  `note_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_id` int(10) unsigned DEFAULT NULL,
  `note` text DEFAULT NULL,
  `is_system` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`note_id`),
  KEY `created` (`created`),
  KEY `updated` (`updated`),
  KEY `user_id` (`user_id`),
  KEY `is_system` (`is_system`),
  CONSTRAINT `note_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6706 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;