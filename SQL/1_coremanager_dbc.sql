-- MySQL dump 10.13  Distrib 5.1.45, for debian-linux-gnu (i486)
--
-- Host: localhost    Database: temporary_dbc
-- ------------------------------------------------------
-- Server version	5.1.45-1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `achievement`
--

DROP TABLE IF EXISTS `achievement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `achievement` (
  `id` int(11) NOT NULL DEFAULT '0',
  `faction` int(11) NOT NULL DEFAULT '0',
  `map` int(11) NOT NULL DEFAULT '0',
  `previous` int(11) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` varchar(255) DEFAULT '',
  `category` int(11) unsigned NOT NULL DEFAULT '0',
  `points` int(11) unsigned NOT NULL DEFAULT '0',
  `orderInGroup` int(11) unsigned NOT NULL DEFAULT '0',
  `flags` int(11) unsigned NOT NULL DEFAULT '0',
  `spellIcon` int(11) unsigned NOT NULL DEFAULT '0',
  `reward` varchar(255) DEFAULT '',
  `demands` int(11) unsigned NOT NULL DEFAULT '0',
  `referencedAchievement` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `achievement`
--

LOCK TABLES `achievement` WRITE;
/*!40000 ALTER TABLE `achievement` DISABLE KEYS */;
/*!40000 ALTER TABLE `achievement` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `achievement_category`
--

DROP TABLE IF EXISTS `achievement_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `achievement_category` (
  `ID` int(11) NOT NULL,
  `ParentID` int(11) NOT NULL,
  `Name` varchar(255) DEFAULT NULL,
  `GroupID` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `achievement_category`
--

LOCK TABLES `achievement_category` WRITE;
/*!40000 ALTER TABLE `achievement_category` DISABLE KEYS */;
/*!40000 ALTER TABLE `achievement_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `achievement_criteria`
--

DROP TABLE IF EXISTS `achievement_criteria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `achievement_criteria` (
  `ID` int(11) NOT NULL,
  `Achievement` int(11) DEFAULT NULL,
  `Type` int(11) DEFAULT NULL,
  `Requirement_1` int(11) DEFAULT NULL,
  `Value_1` int(11) DEFAULT NULL,
  `Requirement_2` int(11) DEFAULT NULL,
  `Value_2` int(11) DEFAULT NULL,
  `Requirement_3` int(11) DEFAULT NULL,
  `Value_3` int(11) DEFAULT NULL,
  `Description` varchar(255) DEFAULT NULL,
  `CompletionFlag` int(11) DEFAULT NULL,
  `GroupFlag` int(11) DEFAULT NULL,
  `Unknown` int(11) DEFAULT NULL,
  `Timelimit` int(11) DEFAULT NULL,
  `Order` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `achievement_criteria`
--

LOCK TABLES `achievement_criteria` WRITE;
/*!40000 ALTER TABLE `achievement_criteria` DISABLE KEYS */;
/*!40000 ALTER TABLE `achievement_criteria` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `areatable`
--

DROP TABLE IF EXISTS `areatable`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `areatable` (
  `ID` int(11) NOT NULL,
  `Map` int(11) DEFAULT NULL,
  `AreaTable` int(11) DEFAULT NULL,
  `ExploreFlag` int(11) DEFAULT NULL,
  `Flags` int(10) unsigned DEFAULT NULL,
  `SoundPreferences` int(11) DEFAULT NULL,
  `Unk1` int(11) DEFAULT NULL,
  `SoundAmbience` int(11) DEFAULT NULL,
  `ZoneMusic` int(11) DEFAULT NULL,
  `ZoneIntroMusicTable` int(11) DEFAULT NULL,
  `AreaLevel` int(11) DEFAULT NULL,
  `Name` varchar(255) DEFAULT NULL,
  `FactionGroup` int(10) unsigned DEFAULT NULL,
  `Unk2` int(11) DEFAULT NULL,
  `Unk3` int(11) DEFAULT NULL,
  `Unk4` int(11) DEFAULT NULL,
  `Unk5` int(11) DEFAULT NULL,
  `Unk6` float DEFAULT NULL,
  `Unk7` float DEFAULT NULL,
  `Unk8` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `areatable`
--

LOCK TABLES `areatable` WRITE;
/*!40000 ALTER TABLE `areatable` DISABLE KEYS */;
/*!40000 ALTER TABLE `areatable` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `faction`
--

DROP TABLE IF EXISTS `faction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `faction` (
  `ID` int(11) NOT NULL,
  `UniqueGainID` int(11) DEFAULT NULL,
  `AtWar` int(11) DEFAULT NULL,
  `Allied` int(11) DEFAULT NULL,
  `Unknown1` int(11) DEFAULT NULL,
  `Unknown2` int(100) unsigned DEFAULT NULL,
  `Unknown3` int(11) DEFAULT NULL,
  `Unknown4` int(11) DEFAULT NULL,
  `Unknown5` int(11) DEFAULT NULL,
  `Unknown6` int(100) unsigned DEFAULT NULL,
  `BaseReputation` int(11) DEFAULT NULL,
  `Modifier1` int(11) DEFAULT NULL,
  `Modifier2` int(11) DEFAULT NULL,
  `Modifier3` int(11) DEFAULT NULL,
  `Condition1` int(11) DEFAULT NULL,
  `Condition2` int(11) DEFAULT NULL,
  `Condition3` int(11) DEFAULT NULL,
  `Condition4` int(11) DEFAULT NULL,
  `ParentFaction` int(11) DEFAULT NULL,
  `Name` varchar(255) DEFAULT NULL,
  `Description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `faction`
--

LOCK TABLES `faction` WRITE;
/*!40000 ALTER TABLE `faction` DISABLE KEYS */;
/*!40000 ALTER TABLE `faction` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `factiontemplate`
--

DROP TABLE IF EXISTS `factiontemplate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `factiontemplate` (
  `ID` int(11) NOT NULL,
  `Faction` int(11) DEFAULT NULL,
  `Flags` int(11) DEFAULT NULL,
  `FactionGroup` int(11) DEFAULT NULL,
  `FriendGroup` int(11) DEFAULT NULL,
  `EnemyGroup` int(11) DEFAULT NULL,
  `Enemies1` int(11) DEFAULT NULL,
  `Enemies2` int(11) DEFAULT NULL,
  `Enemies3` int(11) DEFAULT NULL,
  `Enemies4` int(11) DEFAULT NULL,
  `Friend1` int(11) DEFAULT NULL,
  `Friend2` int(11) DEFAULT NULL,
  `Friend3` int(11) DEFAULT NULL,
  `Friend4` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `factiontemplate`
--

LOCK TABLES `factiontemplate` WRITE;
/*!40000 ALTER TABLE `factiontemplate` DISABLE KEYS */;
/*!40000 ALTER TABLE `factiontemplate` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gemproperties`
--

DROP TABLE IF EXISTS `gemproperties`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gemproperties` (
  `ID` int(11) NOT NULL,
  `SpellItemEnchantment` int(11) DEFAULT NULL,
  `Unknown1` int(11) DEFAULT NULL,
  `Unknown2` int(11) DEFAULT NULL,
  `Color` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gemproperties`
--

LOCK TABLES `gemproperties` WRITE;
/*!40000 ALTER TABLE `gemproperties` DISABLE KEYS */;
/*!40000 ALTER TABLE `gemproperties` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `glyphproperties`
--

DROP TABLE IF EXISTS `glyphproperties`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `glyphproperties` (
  `ID` int(11) NOT NULL,
  `SpellId` int(11) DEFAULT NULL,
  `TypeFlags` int(11) DEFAULT NULL,
  `GlyphIconId` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `glyphproperties`
--

LOCK TABLES `glyphproperties` WRITE;
/*!40000 ALTER TABLE `glyphproperties` DISABLE KEYS */;
/*!40000 ALTER TABLE `glyphproperties` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `item`
--

DROP TABLE IF EXISTS `item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `item` (
  `ItemID` int(11) NOT NULL,
  `ItemClass` int(11) DEFAULT NULL,
  `ItemSubClass` int(11) DEFAULT NULL,
  `Unknown` int(11) DEFAULT NULL,
  `MaterialID` int(11) DEFAULT NULL,
  `ItemDisplayInfo` int(11) DEFAULT NULL,
  `InventorySlotID` int(11) DEFAULT NULL,
  `SheathID` int(11) DEFAULT NULL,
  PRIMARY KEY (`ItemID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `item`
--

LOCK TABLES `item` WRITE;
/*!40000 ALTER TABLE `item` DISABLE KEYS */;
/*!40000 ALTER TABLE `item` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `itemdisplayinfo`
--

DROP TABLE IF EXISTS `itemdisplayinfo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `itemdisplayinfo` (
  `ID` int(11) NOT NULL,
  `IconName` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `itemdisplayinfo`
--

LOCK TABLES `itemdisplayinfo` WRITE;
/*!40000 ALTER TABLE `itemdisplayinfo` DISABLE KEYS */;
/*!40000 ALTER TABLE `itemdisplayinfo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `itemextendedcost`
--

DROP TABLE IF EXISTS `itemextendedcost`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `itemextendedcost` (
  `ID` int(11) NOT NULL,
  `ReqHonorPoints` int(11) DEFAULT NULL,
  `ReqArenaPoints` int(11) DEFAULT NULL,
  `Unknown` int(11) DEFAULT NULL,
  `RequiredItem1` int(11) DEFAULT NULL,
  `RequiredItem2` int(11) DEFAULT NULL,
  `RequiredItem3` int(11) DEFAULT NULL,
  `RequiredItem4` int(11) DEFAULT NULL,
  `RequiredItem5` int(11) DEFAULT NULL,
  `RequiredItemCount1` int(11) DEFAULT NULL,
  `RequiredItemCount2` int(11) DEFAULT NULL,
  `RequiredItemCount3` int(11) DEFAULT NULL,
  `RequiredItemCount4` int(11) DEFAULT NULL,
  `RequiredItemCount5` int(11) DEFAULT NULL,
  `RequiredPersonalArenaRating` int(11) DEFAULT NULL,
  `PurchaseGroup` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `itemextendedcost`
--

LOCK TABLES `itemextendedcost` WRITE;
/*!40000 ALTER TABLE `itemextendedcost` DISABLE KEYS */;
/*!40000 ALTER TABLE `itemextendedcost` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `itemset`
--

DROP TABLE IF EXISTS `itemset`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `itemset` (
  `ID` int(11) NOT NULL,
  `ItemName` varchar(255) DEFAULT NULL,
  `Item1` int(11) DEFAULT NULL,
  `Item2` int(11) DEFAULT NULL,
  `Item3` int(11) DEFAULT NULL,
  `Item4` int(11) DEFAULT NULL,
  `Item5` int(11) DEFAULT NULL,
  `Item6` int(11) DEFAULT NULL,
  `Item7` int(11) DEFAULT NULL,
  `Item8` int(11) DEFAULT NULL,
  `Item9` int(11) DEFAULT NULL,
  `Item10` int(11) DEFAULT NULL,
  `Spell1` int(11) DEFAULT NULL,
  `Spell2` int(11) DEFAULT NULL,
  `Spell3` int(11) DEFAULT NULL,
  `Spell4` int(11) DEFAULT NULL,
  `Spell5` int(11) DEFAULT NULL,
  `Spell6` int(11) DEFAULT NULL,
  `Spell7` int(11) DEFAULT NULL,
  `Spell8` int(11) DEFAULT NULL,
  `Bonus1` int(11) DEFAULT NULL,
  `Bonus2` int(11) DEFAULT NULL,
  `Bonus3` int(11) DEFAULT NULL,
  `Bonus4` int(11) DEFAULT NULL,
  `Bonus5` int(11) DEFAULT NULL,
  `Bonus6` int(11) DEFAULT NULL,
  `Bonus7` int(11) DEFAULT NULL,
  `Bonus8` int(11) DEFAULT NULL,
  `SkillLine` int(11) DEFAULT NULL,
  `ReqSkillLevel` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `itemset`
--

LOCK TABLES `itemset` WRITE;
/*!40000 ALTER TABLE `itemset` DISABLE KEYS */;
/*!40000 ALTER TABLE `itemset` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `map`
--

DROP TABLE IF EXISTS `map`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `map` (
  `ID` int(11) NOT NULL,
  `InternalName` varchar(255) DEFAULT NULL,
  `AreaType` int(11) DEFAULT NULL,
  `IsBattleground` int(11) DEFAULT NULL,
  `Name` varchar(255) NOT NULL,
  `AreaTable` int(11) DEFAULT NULL,
  `Description1` longtext NOT NULL,
  `Description2` longtext NOT NULL,
  `LoadingScreen` int(11) DEFAULT NULL,
  `BattlefieldMapIconScale` float DEFAULT NULL,
  `ParentArea` int(11) DEFAULT NULL,
  `XCoord` float DEFAULT NULL,
  `YCoord` float DEFAULT NULL,
  `TimeOfDayOverride` int(11) DEFAULT NULL,
  `Expansion` int(11) DEFAULT NULL,
  `ResetTimeOverride` int(11) DEFAULT NULL,
  `NumberOfPlayers` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `map`
--

LOCK TABLES `map` WRITE;
/*!40000 ALTER TABLE `map` DISABLE KEYS */;
/*!40000 ALTER TABLE `map` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `skillline`
--

DROP TABLE IF EXISTS `skillline`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `skillline` (
  `ID` int(11) NOT NULL,
  `SkillLineCategory` int(11) DEFAULT NULL,
  `SkillCostID` int(11) DEFAULT NULL,
  `Name` varchar(255) DEFAULT NULL,
  `Description` varchar(255) DEFAULT NULL,
  `SpellIcon` int(11) DEFAULT NULL,
  `Verb` varchar(255) DEFAULT NULL,
  `CanLink` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `skillline`
--

LOCK TABLES `skillline` WRITE;
/*!40000 ALTER TABLE `skillline` DISABLE KEYS */;
/*!40000 ALTER TABLE `skillline` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `skilllineability`
--

DROP TABLE IF EXISTS `skilllineability`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `skilllineability` (
  `ID` int(11) NOT NULL,
  `SkillLine` int(11) DEFAULT NULL,
  `Spell` int(11) DEFAULT NULL,
  `ChrRaces` int(11) DEFAULT NULL,
  `ChrClasses` int(11) DEFAULT NULL,
  `ExcludeRace` int(11) DEFAULT NULL,
  `ExcludeClass` int(11) DEFAULT NULL,
  `ReqSkillValue` int(11) DEFAULT NULL,
  `SpellParent` int(11) DEFAULT NULL,
  `AcquireMethod` int(11) DEFAULT NULL,
  `SkillGreyLevel` int(11) DEFAULT NULL,
  `SkillGreenLevel` int(11) DEFAULT NULL,
  `CharacterPoints1` int(11) DEFAULT NULL,
  `CharacterPoints2` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `skilllineability`
--

LOCK TABLES `skilllineability` WRITE;
/*!40000 ALTER TABLE `skilllineability` DISABLE KEYS */;
/*!40000 ALTER TABLE `skilllineability` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `skillraceclassinfo`
--

DROP TABLE IF EXISTS `skillraceclassinfo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `skillraceclassinfo` (
  `ID` int(11) NOT NULL,
  `SkillLine` int(11) DEFAULT NULL,
  `ChrRaces` int(11) unsigned DEFAULT NULL,
  `ChrClasses` int(11) unsigned DEFAULT NULL,
  `Flags` int(11) unsigned DEFAULT NULL,
  `ReqLevel` int(11) unsigned DEFAULT NULL,
  `SkillTierID` int(11) unsigned DEFAULT NULL,
  `SkillCostID` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `skillraceclassinfo`
--

LOCK TABLES `skillraceclassinfo` WRITE;
/*!40000 ALTER TABLE `skillraceclassinfo` DISABLE KEYS */;
/*!40000 ALTER TABLE `skillraceclassinfo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `spell`
--

DROP TABLE IF EXISTS `spell`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `spell` (
  `Id` int(11) NOT NULL,
  `Category` int(11) unsigned DEFAULT NULL,
  `DispelType` int(11) unsigned DEFAULT NULL,
  `MechanicsType` int(11) unsigned DEFAULT NULL,
  `Attributes` int(11) unsigned DEFAULT NULL,
  `AttributesEx` int(11) unsigned DEFAULT NULL,
  `AttributesExB` int(11) unsigned DEFAULT NULL,
  `AttributesExC` int(11) unsigned DEFAULT NULL,
  `AttributesExD` int(11) unsigned DEFAULT NULL,
  `AttributesExE` int(11) unsigned DEFAULT NULL,
  `AttributesExF` int(11) unsigned DEFAULT NULL,
  `unk1` int(11) unsigned DEFAULT NULL,
  `RequiredShapeShift` int(11) unsigned DEFAULT NULL,
  `unk2` int(11) unsigned DEFAULT NULL,
  `ShapeshiftExclude` int(11) unsigned DEFAULT NULL,
  `unk3` int(11) unsigned DEFAULT NULL,
  `Targets` int(11) unsigned DEFAULT NULL,
  `TargetCreatureType` int(11) unsigned DEFAULT NULL,
  `RequiresSpellFocus` int(11) unsigned DEFAULT NULL,
  `FacingCasterFlags` int(11) unsigned DEFAULT NULL,
  `CasterAuraState` int(11) unsigned DEFAULT NULL,
  `TargetAuraState` int(11) unsigned DEFAULT NULL,
  `ExcludeCasterAuraState1` int(11) unsigned DEFAULT NULL,
  `ExcludeTargetAuraState1` int(11) unsigned DEFAULT NULL,
  `casterAuraSpell` int(11) unsigned DEFAULT NULL,
  `targetAuraSpell` int(11) unsigned DEFAULT NULL,
  `ExcludeCasterAuraState2` int(11) unsigned DEFAULT NULL,
  `ExcludeTargetAuraState2` int(11) unsigned DEFAULT NULL,
  `CastingTimeIndex` int(11) unsigned DEFAULT NULL,
  `RecoveryTime` int(11) unsigned DEFAULT NULL,
  `CategoryRecoveryTime` int(11) unsigned DEFAULT NULL,
  `InterruptFlags` int(11) unsigned DEFAULT NULL,
  `AuraInterruptFlags` int(11) unsigned DEFAULT NULL,
  `ChannelInterruptFlags` int(11) unsigned DEFAULT NULL,
  `procFlags` int(11) unsigned DEFAULT NULL,
  `procChance` int(11) unsigned DEFAULT NULL,
  `procCharges` int(11) unsigned DEFAULT NULL,
  `maxLevel` int(11) unsigned DEFAULT NULL,
  `baseLevel` int(11) unsigned DEFAULT NULL,
  `spellLevel` int(11) unsigned DEFAULT NULL,
  `DurationIndex` int(11) unsigned DEFAULT NULL,
  `powerType` int(11) unsigned DEFAULT NULL,
  `manaCost` int(11) unsigned DEFAULT NULL,
  `manaCostPerlevel` int(11) unsigned DEFAULT NULL,
  `manaPerSecond` int(11) unsigned DEFAULT NULL,
  `manaPerSecondPerLevel` int(11) unsigned DEFAULT NULL,
  `rangeIndex` int(11) unsigned DEFAULT NULL,
  `speed` float DEFAULT NULL,
  `modalNextSpell` int(11) unsigned DEFAULT NULL,
  `maxstack` int(11) unsigned DEFAULT NULL,
  `Totem1` int(11) unsigned DEFAULT NULL,
  `Totem2` int(11) unsigned DEFAULT NULL,
  `Reagent1` int(11) unsigned DEFAULT NULL,
  `Reagent2` int(11) unsigned DEFAULT NULL,
  `Reagent3` int(11) unsigned DEFAULT NULL,
  `Reagent4` int(11) unsigned DEFAULT NULL,
  `Reagent5` int(11) unsigned DEFAULT NULL,
  `Reagent6` int(11) unsigned DEFAULT NULL,
  `Reagent7` int(11) unsigned DEFAULT NULL,
  `Reagent8` int(11) unsigned DEFAULT NULL,
  `ReagentCount1` int(11) unsigned DEFAULT NULL,
  `ReagentCount2` int(11) unsigned DEFAULT NULL,
  `ReagentCount3` int(11) unsigned DEFAULT NULL,
  `ReagentCount4` int(11) unsigned DEFAULT NULL,
  `ReagentCount5` int(11) unsigned DEFAULT NULL,
  `ReagentCount6` int(11) unsigned DEFAULT NULL,
  `ReagentCount7` int(11) unsigned DEFAULT NULL,
  `ReagentCount8` int(11) unsigned DEFAULT NULL,
  `EquippedItemClass` int(11) unsigned DEFAULT NULL,
  `EquippedItemSubClass` int(11) unsigned DEFAULT NULL,
  `RequiredItemFlags` int(11) unsigned DEFAULT NULL,
  `Effect1` int(11) unsigned DEFAULT NULL,
  `Effect2` int(11) unsigned DEFAULT NULL,
  `Effect3` int(11) unsigned DEFAULT NULL,
  `EffectDieSides1` int(11) unsigned DEFAULT NULL,
  `EffectDieSides2` int(11) unsigned DEFAULT NULL,
  `EffectDieSides3` int(11) unsigned DEFAULT NULL,
  `EffectBaseDice1` int(11) unsigned DEFAULT NULL,
  `EffectBaseDice2` int(11) unsigned DEFAULT NULL,
  `EffectBaseDice3` int(11) unsigned DEFAULT NULL,
  `EffectDicePerLevel1` int(11) unsigned DEFAULT NULL,
  `EffectDicePerLevel2` int(11) unsigned DEFAULT NULL,
  `EffectDicePerLevel3` int(11) unsigned DEFAULT NULL,
  `EffectRealPointsPerLevel1` int(11) unsigned DEFAULT NULL,
  `EffectRealPointsPerLevel2` int(11) unsigned DEFAULT NULL,
  `EffectRealPointsPerLevel3` int(11) unsigned DEFAULT NULL,
  `EffectBasePoints1` int(11) unsigned DEFAULT NULL,
  `EffectBasePoints2` int(11) unsigned DEFAULT NULL,
  `EffectBasePoints3` int(11) unsigned DEFAULT NULL,
  `EffectMechanic1` int(11) unsigned DEFAULT NULL,
  `EffectMechanic2` int(11) unsigned DEFAULT NULL,
  `EffectMechanic3` int(11) unsigned DEFAULT NULL,
  `EffectImplicitTargetA1` int(11) unsigned DEFAULT NULL,
  `EffectImplicitTargetA2` int(11) unsigned DEFAULT NULL,
  `EffectImplicitTargetA3` int(11) unsigned DEFAULT NULL,
  `EffectImplicitTargetB1` int(11) unsigned DEFAULT NULL,
  `EffectImplicitTargetB2` int(11) unsigned DEFAULT NULL,
  `EffectImplicitTargetB3` int(11) unsigned DEFAULT NULL,
  `EffectRadiusIndex1` int(11) unsigned DEFAULT NULL,
  `EffectRadiusIndex2` int(11) unsigned DEFAULT NULL,
  `EffectRadiusIndex3` int(11) unsigned DEFAULT NULL,
  `EffectApplyAuraName1` int(11) unsigned DEFAULT NULL,
  `EffectApplyAuraName2` int(11) unsigned DEFAULT NULL,
  `EffectApplyAuraName3` int(11) unsigned DEFAULT NULL,
  `EffectAmplitude1` int(11) unsigned DEFAULT NULL,
  `EffectAmplitude2` int(11) unsigned DEFAULT NULL,
  `EffectAmplitude3` int(11) unsigned DEFAULT NULL,
  `Effectunknown1` int(11) unsigned DEFAULT NULL,
  `Effectunknown2` int(11) unsigned DEFAULT NULL,
  `Effectunknown3` int(11) unsigned DEFAULT NULL,
  `EffectChainTarget1` int(11) unsigned DEFAULT NULL,
  `EffectChainTarget2` int(11) unsigned DEFAULT NULL,
  `EffectChainTarget3` int(11) unsigned DEFAULT NULL,
  `EffectSpellGroupRelation1` int(11) unsigned DEFAULT NULL,
  `EffectSpellGroupRelation2` int(11) unsigned DEFAULT NULL,
  `EffectSpellGroupRelation3` int(11) unsigned DEFAULT NULL,
  `EffectMiscValue1` int(11) unsigned DEFAULT NULL,
  `EffectMiscValue2` int(11) unsigned DEFAULT NULL,
  `EffectMiscValue3` int(11) unsigned DEFAULT NULL,
  `EffectMiscValueB1` int(11) unsigned DEFAULT NULL,
  `EffectMiscValueB2` int(11) unsigned DEFAULT NULL,
  `EffectMiscValueB3` int(11) unsigned DEFAULT NULL,
  `EffectTriggerSpell1` int(11) unsigned DEFAULT NULL,
  `EffectTriggerSpell2` int(11) unsigned DEFAULT NULL,
  `EffectTriggerSpell3` int(11) unsigned DEFAULT NULL,
  `EffectPointsPerComboPoint1` int(11) unsigned DEFAULT NULL,
  `EffectPointsPerComboPoint2` int(11) unsigned DEFAULT NULL,
  `EffectPointsPerComboPoint3` int(11) unsigned DEFAULT NULL,
  `EffectUnk0_1` int(11) unsigned DEFAULT NULL,
  `EffectUnk0_2` int(11) unsigned DEFAULT NULL,
  `EffectUnk0_3` int(11) unsigned DEFAULT NULL,
  `EffectUnk1_1` int(11) unsigned DEFAULT NULL,
  `EffectUnk1_2` int(11) unsigned DEFAULT NULL,
  `EffectUnk1_3` int(11) unsigned DEFAULT NULL,
  `EffectUnk2_1` int(11) unsigned DEFAULT NULL,
  `EffectUnk2_2` int(11) unsigned DEFAULT NULL,
  `EffectUnk2_3` int(11) unsigned DEFAULT NULL,
  `SpellVisual` int(11) unsigned DEFAULT NULL,
  `field114` int(11) unsigned DEFAULT NULL,
  `spellIconID` int(11) unsigned DEFAULT NULL,
  `activeIconID` int(11) unsigned DEFAULT NULL,
  `spellPriority` int(11) unsigned DEFAULT NULL,
  `Name` varchar(255) DEFAULT NULL,
  `Rank` varchar(255) DEFAULT NULL,
  `Description` longtext,
  `BuffDescription` longtext,
  `ManaCostPercentage` int(11) unsigned DEFAULT NULL,
  `unkflags` int(11) unsigned DEFAULT NULL,
  `StartRecoveryTime` int(11) unsigned DEFAULT NULL,
  `StartRecoveryCategory` int(11) unsigned DEFAULT NULL,
  `MaxTargetLevel` int(11) unsigned DEFAULT NULL,
  `SpellFamilyName` int(11) unsigned DEFAULT NULL,
  `SpellGroupType1` int(11) unsigned DEFAULT NULL,
  `SpellGroupType2` int(11) unsigned DEFAULT NULL,
  `MaxTargets` int(11) unsigned DEFAULT NULL,
  `Spell_Dmg_Type` int(11) unsigned DEFAULT NULL,
  `PreventionType` int(11) unsigned DEFAULT NULL,
  `StanceBarOrder` int(11) unsigned DEFAULT NULL,
  `dmg_multiplier1` float DEFAULT NULL,
  `dmg_multiplier2` float DEFAULT NULL,
  `dmg_multiplier3` float DEFAULT NULL,
  `MinFactionID` int(11) unsigned DEFAULT NULL,
  `MinReputation` int(11) unsigned DEFAULT NULL,
  `RequiredAuraVision` int(11) unsigned DEFAULT NULL,
  `TotemCategory1` int(11) unsigned DEFAULT NULL,
  `TotemCategory2` int(11) unsigned DEFAULT NULL,
  `RequiresAreaId` int(11) unsigned DEFAULT NULL,
  `School` int(11) unsigned DEFAULT NULL,
  `unk4` int(11) unsigned DEFAULT NULL,
  `unk5` int(11) unsigned DEFAULT NULL,
  `unk6` int(11) unsigned DEFAULT NULL,
  `unk7` int(11) unsigned DEFAULT NULL,
  `unk8` int(11) unsigned DEFAULT NULL,
  `unk9` int(11) unsigned DEFAULT NULL,
  `unk10` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `spell`
--

LOCK TABLES `spell` WRITE;
/*!40000 ALTER TABLE `spell` DISABLE KEYS */;
/*!40000 ALTER TABLE `spell` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `spellicon`
--

DROP TABLE IF EXISTS `spellicon`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `spellicon` (
  `ID` int(11) NOT NULL,
  `Name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `spellicon`
--

LOCK TABLES `spellicon` WRITE;
/*!40000 ALTER TABLE `spellicon` DISABLE KEYS */;
/*!40000 ALTER TABLE `spellicon` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `spellitemenchantment`
--

DROP TABLE IF EXISTS `spellitemenchantment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `spellitemenchantment` (
  `ID` int(11) NOT NULL,
  `EnchantmentName` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `spellitemenchantment`
--

LOCK TABLES `spellitemenchantment` WRITE;
/*!40000 ALTER TABLE `spellitemenchantment` DISABLE KEYS */;
/*!40000 ALTER TABLE `spellitemenchantment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `talent`
--

DROP TABLE IF EXISTS `talent`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `talent` (
  `ID` int(11) NOT NULL,
  `TalentTab` int(11) DEFAULT NULL,
  `Row` int(11) DEFAULT NULL,
  `Col` int(11) DEFAULT NULL,
  `Spell1` int(11) DEFAULT NULL,
  `Spell2` int(11) DEFAULT NULL,
  `Spell3` int(11) DEFAULT NULL,
  `Spell4` int(11) DEFAULT NULL,
  `Spell5` int(11) DEFAULT NULL,
  `Spell6` int(11) DEFAULT NULL,
  `Spell7` int(11) DEFAULT NULL,
  `Spell8` int(11) DEFAULT NULL,
  `Spell9` int(11) DEFAULT NULL,
  `Talent1` int(11) DEFAULT NULL,
  `Talent2` int(11) DEFAULT NULL,
  `Talent3` int(11) DEFAULT NULL,
  `TalentCount1` int(11) DEFAULT NULL,
  `TalentCount2` int(11) DEFAULT NULL,
  `TalentCount3` int(11) DEFAULT NULL,
  `SinglePoint` int(11) DEFAULT NULL,
  `Unknown` int(11) DEFAULT NULL,
  `AllowForPetFlags1` int(11) DEFAULT NULL,
  `AllowForPetFlags2` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `talent`
--

LOCK TABLES `talent` WRITE;
/*!40000 ALTER TABLE `talent` DISABLE KEYS */;
/*!40000 ALTER TABLE `talent` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `talenttab`
--

DROP TABLE IF EXISTS `talenttab`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `talenttab` (
  `ID` int(11) NOT NULL,
  `Name` varchar(255) DEFAULT NULL,
  `SpellIcon` int(11) DEFAULT NULL,
  `Races` int(11) unsigned DEFAULT NULL,
  `Classes` int(11) unsigned DEFAULT NULL,
  `CreatureFamily` int(11) unsigned DEFAULT NULL,
  `TabNumber` int(11) DEFAULT NULL,
  `InternalName` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `talenttab`
--

LOCK TABLES `talenttab` WRITE;
/*!40000 ALTER TABLE `talenttab` DISABLE KEYS */;
/*!40000 ALTER TABLE `talenttab` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `worldmaparea`
--

DROP TABLE IF EXISTS `worldmaparea`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `worldmaparea` (
  `ID` int(11) NOT NULL,
  `Map` int(11) DEFAULT NULL,
  `AreaTable` int(11) DEFAULT NULL,
  `RefCon` varchar(255) DEFAULT NULL,
  `Y1` float DEFAULT NULL,
  `Y2` float DEFAULT NULL,
  `X1` float DEFAULT NULL,
  `X2` float DEFAULT NULL,
  `Map2` int(11) DEFAULT NULL,
  `DungeonMap` int(11) DEFAULT NULL,
  `Unknown` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `worldmaparea`
--

LOCK TABLES `worldmaparea` WRITE;
/*!40000 ALTER TABLE `worldmaparea` DISABLE KEYS */;
/*!40000 ALTER TABLE `worldmaparea` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2010-05-08 17:09:15
