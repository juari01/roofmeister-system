ALTER TABLE `project` ADD `folder_id` INT(10) UNSIGNED AFTER `project_id`,
ADD CONSTRAINT `project_ibfk_3` FOREIGN KEY (`folder_id`) REFERENCES `file_folder`(`folder_id`)