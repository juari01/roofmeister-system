CREATE TABLE `xref_note_customer` (
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `note_id` int(10) unsigned NOT NULL,
  `customer_id` int(10) unsigned NOT NULL,
  KEY `created` (`created`),
  KEY `updated` (`updated`),
  KEY `note_id` (`note_id`),
  KEY `customer_id` (`customer_id`),
  CONSTRAINT `xref_note_customer_ibfk_1` FOREIGN KEY (`note_id`) REFERENCES `note` (`note_id`),
  CONSTRAINT `xref_note_customer_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci