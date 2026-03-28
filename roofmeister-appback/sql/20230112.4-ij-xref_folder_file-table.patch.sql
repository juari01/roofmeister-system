
CREATE TABLE `xref_folder_file` (
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `folder_id` int(10) UNSIGNED DEFAULT NULL,
  `file_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(256) DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for table `xref_folder_file`
--
ALTER TABLE `xref_folder_file`
  ADD KEY `created` (`created`),
  ADD KEY `updated` (`updated`),
  ADD KEY `folder_id` (`folder_id`),
  ADD KEY `file_id` (`file_id`);

--
-- Constraints for table `xref_folder_file`
--
ALTER TABLE `xref_folder_file`
  ADD CONSTRAINT `xref_folder_file_ibfk_1` FOREIGN KEY (`folder_id`) REFERENCES `file_folder` (`folder_id`),
  ADD CONSTRAINT `xref_folder_file_ibfk_2` FOREIGN KEY (`file_id`) REFERENCES `file` (`file_id`);
COMMIT;

