ALTER TABLE `config_accounts` ADD COLUMN `SecurityLevel` int(11) NOT NULL DEFAULT '0' AFTER `ScreenName`;
RENAME TABLE `config_arcmanager_database` TO `config_dbc_database`;
ALTER TABLE `config_character_databases` MODIFY COLUMN `Address` varchar(255) DEFAULT NULL;
ALTER TABLE `config_character_databases` MODIFY COLUMN `Port` int(11) DEFAULT '0';
DROP TABLE IF EXISTS `config_gm_level_names`;
CREATE TABLE `config_gm_level_names` (
  `Index` int(11) NOT NULL AUTO_INCREMENT,
  `Security_Level` int(11) NOT NULL,
  `Full_Name` varchar(20) DEFAULT NULL,
  `Short_Name` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`Index`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;
INSERT INTO `config_gm_level_names` VALUES (1,-1,'Guest','G'),(2,0,'Player','P'),(3,1,'GM','GM'),(4,2,'Full GM','GM+'),(5,3,'Admin','Admin'),(6,4,'SysOp','SysOp');
CREATE TABLE `config_lang_forum` (
  `Key` varchar(50) NOT NULL,
  `Lang` varchar(50) NOT NULL,
  `Value` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`Key`,`Lang`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
INSERT INTO `config_lang_forum` VALUES ('admin','english','Admins forums only'),('admin','german','Nur Admin-Forum'),('admin','persian','Admins forums only'),('admindesc','english','Only admins can see this'),('admindesc','german','Nur Admins k&ouml;nnen das sehen'),('admindesc','persian','Only admins can see this'),('alliance','english','Alliance forum only'),('alliance','german','Nur Allianz-Forum'),('alliance','persian','Alliance forum only'),('alliancedesc','english','Only alliance players can see this'),('alliancedesc','german','Nur Allianz-Spieler k&ouml;nnen das sehen'),('alliancedesc','persian','Only alliance players can see this'),('both','english','Horde and alliance forums'),('both','german','Horde und Allianz Forum'),('both','persian','Horde and alliance forums'),('bothdesc','english','Talk about everything related to the game'),('bothdesc','german','Hier wird &uuml;ber alles geredet, dass mit dem Spiel zu tun hat'),('bothdesc','persian','Talk about everything related to the game'),('bugs','english','Bugs and problems'),('bugs','german','Fehler und Probleme'),('bugs','persian','Bugs and problems'),('bugsdesc','english','Ask here help from GM or Admin, not to beg money item or xp, thx.'),('bugsdesc','german','Hier kann man nach Hilfe von GMs und Admins fragen'),('bugsdesc','persian','Ask here help from GM or Admin, not to beg money item or xp, thx.'),('gamecat','english','Game Category'),('gamecat','german','Spiele Kategorie'),('gamecat','persian','Game Category'),('general','english','General Talks'),('general','german','Allgemein'),('general','persian','General Talks'),('generaldesc','english','Talk about everything related to the server'),('generaldesc','german','Hier kann man &uuml;bern den Realm reden'),('generaldesc','persian','Talk about everything related to the server'),('horde','english','Horde forum only'),('horde','german','Nur Horde-Forum'),('horde','persian','Horde forum only'),('hordedesc','english','Only horde players can see this'),('hordedesc','german','Nur Horde-Spieler k&ouml;nnen das sehen'),('hordedesc','persian','Only horde players can see this'),('news','english','News'),('news','german','Neuigkeiten'),('news','persian','News'),('newsdesc','english','News and infos about the server'),('newsdesc','german','Nachrichten und Infos &uuml;ber den Server'),('newsdesc','persian','News and infos about the server'),('servcat','english','Server Category'),('servcat','german','Server Kategorie'),('servcat','persian','Server Category');
ALTER TABLE `config_logon_database` MODIFY COLUMN `Address` varchar(255) DEFAULT NULL;
ALTER TABLE `config_logon_database` MODIFY COLUMN `Port` int(11) DEFAULT '0';
UPDATE `config_menus` SET Enabled=0 WHERE `Index`=8;
UPDATE `config_menus` SET Enabled=1 WHERE `Index`=9;
UPDATE `config_menus` SET Enabled=1 WHERE `Index`=16;
UPDATE `config_menus` SET Enabled=1 WHERE `Index`=17;
UPDATE `config_menus` SET `Insert`=1, `Update`=1 WHERE `Index`=40;
DELETE FROM `config_menus` WHERE `Index`=18;
DELETE FROM `config_menus` WHERE `Index`=19;
DELETE FROM `config_menus` WHERE `Index`=20;
DELETE FROM `config_menus` WHERE `Index`=21;
DELETE FROM `config_menus` WHERE `Index`=22;
DELETE FROM `config_menus` WHERE `Index`=23;
DELETE FROM `config_menus` WHERE `Index`=24;
DELETE FROM `config_menus` WHERE `Index`=27;
INSERT INTO `config_menus` VALUES (43,1,'change_char_name.php','namechanger',0,4,4,4,1),(44,1,'change_char_race.php','racechanger',0,4,4,4,1),(45,1,'hearthstone.php','unstuck',0,4,4,4,1);
UPDATE `config_misc` SET `Value`='#CoreManager' WHERE `Key`='IRC_Channel';
UPDATE `config_misc` SET `Value`='1' WHERE `Key`='Enabled_Captcha';
UPDATE `config_misc` SET `Value`='1' WHERE `Key`='Remember_Me_Checked';
UPDATE `config_misc` SET `Value`='0' WHERE `Key`='Mail_GMailSender';
UPDATE `config_misc` SET `Value`='CoreManager' WHERE `Key`='Site_Title' AND `Value`='ArcManager for ArcEmu: World of Warcraft Emulator';
UPDATE `config_misc` SET `Value`='english' WHERE `Key`='Default_Language';
INSERT INTO `config_misc` VALUES ('Test_Mode','0'),('Multi_Realm','1'),('Installed','0'),('Hide_Max_Players','0'),('Hide_Avg_Latency','0'),('Hide_Plr_Latency','0'),('Hide_Server_Mem','1'),('Use_Recaptcha','1'),('Recaptcha_Public_Key',''),('Recaptcha_Private_Key','');
ALTER TABLE `config_servers` MODIFY COLUMN `Address` varchar(255) DEFAULT NULL;
DELETE FROM `config_top_menus` WHERE `Index`=2;
ALTER TABLE `config_world_databases` MODIFY COLUMN `Address` varchar(255) DEFAULT NULL;
ALTER TABLE `config_world_databases` MODIFY COLUMN `Port` int(11) DEFAULT '0';
DROP TABLE IF EXISTS `ip2nation`;
CREATE TABLE `ip2nation` (
  `ip` int(11) unsigned NOT NULL,
  `country` char(2) NOT NULL DEFAULT '',
  KEY `idx_ip2nation_ip` (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
ALTER TABLE `realmlist` MODIFY COLUMN `name` varchar(32) NOT NULL DEFAULT '';
ALTER TABLE `realmlist` MODIFY COLUMN `port` int(11) NOT NULL DEFAULT '8085';
ALTER TABLE `realmlist` MODIFY COLUMN `icon` tinyint(3) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `realmlist` MODIFY COLUMN `color` tinyint(3) unsigned NOT NULL DEFAULT '2';
ALTER TABLE `realmlist` MODIFY COLUMN `timezone` tinyint(3) unsigned NOT NULL DEFAULT '0';
