ALTER TABLE `motd` ADD COLUMN `Target` INT(11) DEFAULT '0' AFTER `Priority`;

UPDATE TABLE `motd` SET `Target`=0;