/* */
CREATE TABLE `file` (
  `file_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `folder_id` int(10) unsigned NOT NULL DEFAULT '0',
  `hash` varchar(64) DEFAULT NULL,
  `name` varchar(128) DEFAULT NULL,
  `type` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`file_id`),
  KEY `created` (`created`),
  KEY `folder_id` (`folder_id`),
  KEY `updated` (`updated`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
/* */ 
