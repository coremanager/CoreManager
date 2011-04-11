ALTER TABLE `config_top_menus` ADD COLUMN `Enabled` INT(11) DEFAULT NULL;

UPDATE `config_top_menus` SET Enabled=1;