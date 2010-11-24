INSERT INTO `config_misc` (`Key`, `Value`) VALUES ('Datasite_Base', 'http://www.wowhead.com/');
INSERT INTO `config_misc` (`Key`, `Value`) VALUES ('Datasite_Name', 'wowhead.com');
UPDATE `config_misc` SET `Value`='?item=' WHERE `Key`='Datasite_Item';
UPDATE `config_misc` SET `Value`='?quest=' WHERE `Key`='Datasite_Quest';
UPDATE `config_misc` SET `Value`='?npc=' WHERE `Key`='Datasite_Creature';
UPDATE `config_misc` SET `Value`='?spell=' WHERE `Key`='Datasite_Spell';
UPDATE `config_misc` SET `Value`='?spells=' WHERE `Key`='Datasite_Skill';
UPDATE `config_misc` SET `Value`='?object=' WHERE `Key`='Datasite_GO';
UPDATE `config_misc` SET `Value`='?achievement=' WHERE `Key`='Datasite_Achievement';