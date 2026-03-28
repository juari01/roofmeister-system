/* */
CREATE TABLE `xref_folder_group` (
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `folder_id` int(10) unsigned NOT NULL,
  `group_id` int(10) unsigned NOT NULL,
  `access` char(1) DEFAULT NULL,
  KEY `created` (`created`),
  KEY `updated` (`updated`),
  KEY `folder_id` (`folder_id`),
  KEY `group_id` (`group_id`),
  KEY `access` (`access`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
/* */ 
