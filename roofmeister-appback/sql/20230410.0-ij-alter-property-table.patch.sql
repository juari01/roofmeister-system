ALTER TABLE `property` ADD `folder_id` INT(10) UNSIGNED AFTER `property_id`,
ADD CONSTRAINT `property_ibfk_3` FOREIGN KEY (`folder_id`) REFERENCES `file_folder`(`folder_id`)