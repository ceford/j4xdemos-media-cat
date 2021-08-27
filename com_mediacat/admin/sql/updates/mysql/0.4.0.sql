ALTER TABLE `#__mediacat` ADD `alt` VARCHAR(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `size`;
ALTER TABLE `#__mediacat` ADD `tn_width` INT NOT NULL DEFAULT '0' AFTER `size`; 
