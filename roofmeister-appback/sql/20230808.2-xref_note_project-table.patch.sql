CREATE TABLE `xref_note_project` (
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `note_id` int(10) unsigned NOT NULL,
  `project_id` int(10) unsigned NOT NULL,
  KEY `created` (`created`),
  KEY `updated` (`updated`),
  KEY `note_id` (`note_id`),
  KEY `project_id` (`project_id`),
  CONSTRAINT `xref_note_project_ibfk_1` FOREIGN KEY (`note_id`) REFERENCES `note` (`note_id`),
  CONSTRAINT `xref_note_project_ibfk_2` FOREIGN KEY (`project_id`) REFERENCES `project` (`project_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci