ALTER TABLE `customer` ADD `folder_id` INT(10) UNSIGNED AFTER `customer_id`;
ALTER TABLE `customer` ADD CONSTRAINT `customer_ibfk_1` FOREIGN KEY (`folder_id`) REFERENCES `file_folder`(`folder_id`);