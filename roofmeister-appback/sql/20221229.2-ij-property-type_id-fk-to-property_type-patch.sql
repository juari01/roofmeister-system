ALTER TABLE property ADD type_id INT UNSIGNED DEFAULT NULL AFTER updated;
ALTER TABLE property ADD CONSTRAINT FOREIGN KEY (type_id) REFERENCES property_type(type_id);