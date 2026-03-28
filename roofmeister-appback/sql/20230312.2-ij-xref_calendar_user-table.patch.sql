CREATE TABLE `xref_calendar_user` (
  `calendar_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `access`  int(10) unsigned NOT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  KEY `created` (`created`),
  KEY `updated` (`updated`),
  KEY `calendar_id` (`calendar_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `xref_calendar_user_ibfk_1` FOREIGN KEY (`calendar_id`) REFERENCES `calendar` (`calendar_id`),
  CONSTRAINT `xref_calendar_user_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;