ALTER TABLE `config_accounts` ADD COLUMN `Avatar` VARCHAR(255) NOT NULL DEFAULT '' AFTER `JoinDate`;
ALTER TABLE `config_accounts` ADD COLUMN `Info` TEXT AFTER `Avatar`;
ALTER TABLE `config_accounts` ADD COLUMN `Signature` TEXT AFTER `Info`;