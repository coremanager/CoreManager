ALTER TABLE `config_servers` ADD COLUMN `Name` VARCHAR(64) NOT NULL DEFAULT '' AFTER `Index`;
ALTER TABLE `config_servers` ADD COLUMN `External_Address` VARCHAR(63) NOT NULL DEFAULT '127.0.0.1' AFTER `Address`;
ALTER TABLE `config_servers` ADD COLUMN `Icon` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' AFTER `Both_Factions`;
ALTER TABLE `config_servers` ADD COLUMN `Color` TINYINT(3) UNSIGNED NOT NULL DEFAULT '2' AFTER `Icon`;
ALTER TABLE `config_servers` ADD COLUMN `Timezone` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' AFTER `Color`;
ALTER TABLE `config_servers` ADD COLUMN `Allowed_Security_Level` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' AFTER `Timezone`;
ALTER TABLE `config_servers` ADD COLUMN `Population` FLOAT UNSIGNED NOT NULL DEFAULT '0' AFTER `Allowed_Security_Level`;

UPDATE `config_servers` SET Name=(SELECT name FROM realmlist WHERE id=1), External_Address=(SELECT address FROM realmlist WHERE id=1), Icon=(SELECT icon FROM realmlist WHERE id=1), Color=(SELECT color FROM realmlist WHERE id=1), Timezone=(SELECT timezone FROM realmlist WHERE id=1), Allowed_Security_Level=(SELECT allowedSecurityLevel FROM realmlist WHERE id=1), Population=(SELECT population FROM realmlist WHERE id=1) WHERE `Index`=1;