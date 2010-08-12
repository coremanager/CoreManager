DROP TABLE IF EXISTS `config_forums`;
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

INSERT INTO `config_forums` VALUES ('1', '1', 'News', 'News and info about the server', null, '0', '3', '3');
INSERT INTO `config_forums` VALUES ('2', '1', 'General', 'Talk about everything related to the server', null, '0', '0', '0');
INSERT INTO `config_forums` VALUES ('3', '2', 'Bugs and Problems', 'Ask here for help from GMs or Admins, not to beg for Money, Items or XP.', '', '0', '0', '0');
INSERT INTO `config_forums` VALUES ('4', '2', 'Horde and Alliance forums', 'Talk about everything related to the game', null, '0', '0', '0');
INSERT INTO `config_forums` VALUES ('5', '2', 'Horde Only forum', 'Only Horde players can see this', 'H', '0', '0', '0');
INSERT INTO `config_forums` VALUES ('6', '2', 'Alliance Only forum', 'Only Alliance players can see this', 'A', '0', '0', '0');
INSERT INTO `config_forums` VALUES ('7', '2', 'Admin Only forums', 'Only Admins can see this', null, '3', '3', '0');

DROP TABLE IF EXISTS `config_forum_categories`;
CREATE TABLE `config_forum_categories` (
  `Index` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`Index`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

INSERT INTO `config_forum_categories` VALUES ('1', 'Server Category');
INSERT INTO `config_forum_categories` VALUES ('2', 'Game Category');
