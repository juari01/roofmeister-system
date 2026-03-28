CREATE TABLE `folder_path` (
  `path_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `name` varchar(128) DEFAULT NULL,
  `folder_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`path_id`),
  KEY `created` (`created`),
  KEY `updated` (`updated`),
  KEY `name` (`name`),
  KEY `folder_id` (`folder_id`),
  CONSTRAINT `file_path_ibfk_1` FOREIGN KEY (`folder_id`) REFERENCES `file_folder` (`folder_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



INSERT INTO `folder_path` (`path_id`, `created`, `updated`, `name`, `folder_id`) VALUES (NULL, current_timestamp(), current_timestamp(), 'Customer', NULL);
INSERT INTO `folder_path` (`path_id`, `created`, `updated`, `name`, `folder_id`) VALUES (NULL, current_timestamp(), current_timestamp(), 'Project', NULL);
INSERT INTO `folder_path` (`path_id`, `created`, `updated`, `name`, `folder_id`) VALUES (NULL, current_timestamp(), current_timestamp(), 'Property', NULL);