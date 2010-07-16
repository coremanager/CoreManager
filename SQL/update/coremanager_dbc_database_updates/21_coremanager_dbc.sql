CREATE TABLE `itemrandomproperties` (
  `ID` int(11) NOT NULL,
  `Name` varchar(255) DEFAULT NULL,
  `SpellItemEnchantment_1` int(11) DEFAULT NULL,
  `SpellItemEnchantment_2` int(11) DEFAULT NULL,
  `SpellItemEnchantment_3` int(11) DEFAULT NULL,
  `SpellItemEnchantment_4` int(11) DEFAULT NULL,
  `SpellItemEnchantment_5` int(11) DEFAULT NULL,
  `Suffix` varchar(255) DEFAULT NULL,
  `SuffixFlags` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `itemrandomsuffix` (
  `ID` int(11) NOT NULL,
  `Name` varchar(255) DEFAULT NULL,
  `InternalName` varchar(255) DEFAULT NULL,
  `SpellItemEnchantment_1` int(11) DEFAULT NULL,
  `SpellItemEnchantment_2` int(11) DEFAULT NULL,
  `SpellItemEnchantment_3` int(11) DEFAULT NULL,
  `SpellItemEnchantment_4` int(11) DEFAULT NULL,
  `SpellItemEnchantment_5` int(11) DEFAULT NULL,
  `SpellItemEnchantment_1_Value` int(11) DEFAULT NULL,
  `SpellItemEnchantment_2_Value` int(11) DEFAULT NULL,
  `SpellItemEnchantment_3_Value` int(11) DEFAULT NULL,
  `SpellItemEnchantment_4_Value` int(11) DEFAULT NULL,
  `SpellItemEnchantment_5_Value` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
