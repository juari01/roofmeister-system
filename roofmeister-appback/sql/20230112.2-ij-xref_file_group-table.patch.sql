/* */
CREATE TABLE `xref_file_group` (
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `file_id` int(10) unsigned NOT NULL,
  `group_id` int(10) unsigned NOT NULL,
  `access` char(1) DEFAULT NULL,
  KEY `created` (`created`),
  KEY `updated` (`updated`),
  KEY `file_id` (`file_id`),
  KEY `group_id` (`group_id`),
  KEY `access` (`access`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
/* */ 
