ALTER TABLE `config_accounts` ADD COLUMN `TempPassword` varchar(75) AFTER `WebAdmin`;

INSERT INTO `config_misc` (`Key`, `Value`) VALUES ('Send_Confirmation_Mail_On_Creation', '0');
