ALTER TABLE `#__mediacat_files` ADD `state` TINYINT(3) NOT NULL DEFAULT '1' AFTER `hash`; 
ALTER TABLE `#__mediacat_files` ADD INDEX(`state`); 

ALTER TABLE `#__mediacat_images` ADD `state` TINYINT(3) NOT NULL DEFAULT '1' AFTER `hash`; 
ALTER TABLE `#__mediacat_images` ADD INDEX(`state`); 
