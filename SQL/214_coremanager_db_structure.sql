/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `char_changes` (
  `guid` int(11) NOT NULL,
  `new_name` varchar(50) DEFAULT NULL,
  `new_race` int(11) DEFAULT NULL,
  `new_acct` int(11) DEFAULT NULL,
  PRIMARY KEY (`guid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `config_accounts` (
  `Login` varchar(32) DEFAULT NULL,
  `ScreenName` varchar(32) DEFAULT NULL,
  `SecurityLevel` int(11) NOT NULL DEFAULT '0',
  `TempPassword` varchar(75) DEFAULT NULL,
  `TempEmail` varchar(255) NOT NULL DEFAULT '',
  `JoinDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Avatar` varchar(255) NOT NULL DEFAULT '',
  `Info` mediumtext,
  `Signature` mediumtext
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `config_character_databases` (
  `Index` int(11) NOT NULL AUTO_INCREMENT,
  `Address` varchar(255) DEFAULT NULL,
  `Port` int(11) DEFAULT '0',
  `Name` varchar(255) DEFAULT NULL,
  `User` varchar(255) DEFAULT NULL,
  `Password` varchar(255) DEFAULT NULL,
  `Encoding` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`Index`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `config_dbc_database` (
  `Index` int(11) NOT NULL AUTO_INCREMENT,
  `Address` varchar(255) DEFAULT NULL,
  `Port` int(11) DEFAULT '0',
  `Name` varchar(255) DEFAULT NULL,
  `User` varchar(255) DEFAULT NULL,
  `Password` varchar(255) DEFAULT NULL,
  `Encoding` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`Index`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `config_forum_categories` (
  `Index` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`Index`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `config_forums` (
  `Index` int(11) NOT NULL AUTO_INCREMENT,
  `Category` int(11) DEFAULT NULL,
  `Name` varchar(75) DEFAULT NULL,
  `Desc` varchar(150) DEFAULT NULL,
  `Side_Access` varchar(5) DEFAULT NULL,
  `Min_Security_Level_Read` int(11) DEFAULT '0',
  `Min_Security_Level_Post` int(11) DEFAULT '0',
  `Min_Security_Level_Create_Topic` int(11) DEFAULT '0',
  PRIMARY KEY (`Index`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `config_gm_level_names` (
  `Index` int(11) NOT NULL AUTO_INCREMENT,
  `Security_Level` int(11) NOT NULL,
  `Full_Name` varchar(20) DEFAULT NULL,
  `Short_Name` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`Index`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `config_logon_database` (
  `Index` int(11) NOT NULL AUTO_INCREMENT,
  `Address` varchar(255) DEFAULT NULL,
  `Port` int(11) DEFAULT '0',
  `Name` varchar(255) DEFAULT NULL,
  `User` varchar(255) DEFAULT NULL,
  `Password` varchar(255) DEFAULT NULL,
  `Encoding` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`Index`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `config_menus` (
  `Index` int(11) NOT NULL AUTO_INCREMENT,
  `Menu` int(11) DEFAULT NULL,
  `Order` int(11) DEFAULT NULL,
  `Action` varchar(255) DEFAULT NULL,
  `Name` varchar(50) DEFAULT NULL,
  `View` int(11) DEFAULT NULL,
  `Insert` int(11) DEFAULT NULL,
  `Update` int(11) DEFAULT NULL,
  `Delete` int(11) DEFAULT NULL,
  `Enabled` int(11) DEFAULT NULL,
  PRIMARY KEY (`Index`)
) ENGINE=MyISAM AUTO_INCREMENT=50 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `config_misc` (
  `Key` varchar(255) DEFAULT NULL,
  `Value` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `config_servers` (
  `Index` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(64) NOT NULL DEFAULT '',
  `Address` varchar(255) DEFAULT NULL,
  `External_Address` varchar(63) NOT NULL DEFAULT '127.0.0.1',
  `Port` int(11) DEFAULT NULL,
  `Telnet_Port` int(11) DEFAULT NULL,
  `Telnet_User` varchar(75) DEFAULT NULL,
  `Telnet_Pass` varchar(75) DEFAULT NULL,
  `Both_Factions` int(11) DEFAULT NULL,
  `Icon` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `Color` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `Timezone` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `Allowed_Security_Level` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `Population` float unsigned NOT NULL DEFAULT '0',
  `Stats_XML` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`Index`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `config_top_menus` (
  `Index` int(11) NOT NULL DEFAULT '0',
  `Action` varchar(50) DEFAULT NULL,
  `Name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`Index`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `config_valid_ip_mask` (
  `Index` int(11) NOT NULL DEFAULT '0',
  `ValidIPMask` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`Index`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `config_world_databases` (
  `Index` int(11) NOT NULL AUTO_INCREMENT,
  `Address` varchar(255) DEFAULT NULL,
  `Port` int(11) DEFAULT '0',
  `Name` varchar(255) DEFAULT NULL,
  `User` varchar(255) DEFAULT NULL,
  `Password` varchar(255) DEFAULT NULL,
  `Encoding` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`Index`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `forum_posts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `authorid` bigint(20) unsigned NOT NULL DEFAULT '0',
  `authorname` varchar(16) NOT NULL DEFAULT '',
  `forum` bigint(20) unsigned NOT NULL DEFAULT '0',
  `topic` bigint(20) unsigned NOT NULL DEFAULT '0',
  `lastpost` bigint(20) unsigned NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL DEFAULT '',
  `text` longtext,
  `time` varchar(255) NOT NULL,
  `announced` tinyint(3) DEFAULT NULL,
  `sticked` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `closed` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ip2nation` (
  `ip` int(11) unsigned NOT NULL,
  `country` char(2) NOT NULL DEFAULT '',
  KEY `idx_ip2nation_ip` (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ip2nationcountries` (
  `code` varchar(4) NOT NULL DEFAULT '',
  `country` varchar(255) NOT NULL DEFAULT '',
  `lat` float NOT NULL DEFAULT '0',
  `lon` float NOT NULL DEFAULT '0',
  PRIMARY KEY (`code`),
  KEY `code` (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `motd` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Message` longtext,
  `Created` datetime DEFAULT NULL,
  `Created_By` int(11) unsigned DEFAULT '0',
  `Last_Edited` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `Last_Edited_By` int(11) unsigned DEFAULT '0',
  `Priority` int(11) DEFAULT '0',
  `Target` int(11) DEFAULT '0',
  `Enabled` int(11) DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `point_system_invites` (
  `entry` int(11) NOT NULL AUTO_INCREMENT,
  `PlayersAccount` char(50) DEFAULT NULL,
  `InvitedBy` char(50) DEFAULT NULL,
  `InviterAccount` char(50) DEFAULT NULL,
  `Treated` int(1) NOT NULL DEFAULT '0',
  `Rewarded` int(1) NOT NULL DEFAULT '0',
  UNIQUE KEY `entry` (`entry`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `worldmaparea_fine` (
  `ID` int(11) NOT NULL DEFAULT '0',
  `AreaTable` int(11) DEFAULT NULL,
  `Map` int(11) DEFAULT NULL,
  `RefCon` varchar(255) DEFAULT NULL,
  `Name` varchar(255) DEFAULT NULL,
  `Y1` float DEFAULT NULL,
  `Y2` float DEFAULT NULL,
  `X1` float DEFAULT NULL,
  `X2` float DEFAULT NULL,
  `Z1` float DEFAULT NULL,
  `Z2` float DEFAULT NULL,
  `Yw` int(11) DEFAULT NULL,
  `Xw` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xp_to_level` (
  `level` int(10) unsigned NOT NULL,
  `xp_for_next_level` int(10) unsigned NOT NULL,
  PRIMARY KEY (`level`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
