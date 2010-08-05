ALTER TABLE `config_servers` ADD COLUMN `Telnet_Port` int(11) DEFAULT NULL AFTER `Port`;
ALTER TABLE `config_servers` ADD COLUMN `Telnet_User` varchar(75) AFTER `Telnet_Port`;
ALTER TABLE `config_servers` ADD COLUMN `Telnet_Pass` varchar(75) AFTER `Telnet_User`;
