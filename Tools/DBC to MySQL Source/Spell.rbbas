#tag Class
Protected Class Spell
Inherits RunnerClass
	#tag Event
		Sub Run()
		  do
		    dim Id as Integer
		    dim Category as UInt32
		    dim DispelType as UInt32
		    dim MechanicsType as UInt32
		    dim myAttributes as UInt32
		    dim AttributesEx as UInt32
		    dim AttributesExB as UInt32
		    dim AttributesExC as UInt32
		    dim AttributesExD as UInt32
		    dim AttributesExE as UInt32
		    dim AttributesExF as UInt32
		    dim unk1 as UInt32
		    dim RequiredShapeShift as UInt32
		    dim unk2 as UInt32
		    dim ShapeshiftExclude as UInt32
		    dim unk3 as UInt32
		    dim Targets as UInt32
		    dim TargetCreatureType as UInt32
		    dim RequiresSpellFocus as UInt32
		    dim FacingCasterFlags as UInt32
		    dim CasterAuraState as UInt32
		    dim TargetAuraState as UInt32
		    dim ExcludeCasterAuraState1 as UInt32
		    dim ExcludeTargetAuraState1 as UInt32
		    dim casterAuraSpell as UInt32
		    dim targetAuraSpell as UInt32
		    dim ExcludeCasterAuraState2 as UInt32
		    dim ExcludeTargetAuraState2 as UInt32
		    dim CastingTimeIndex as UInt32
		    dim RecoveryTime as UInt32
		    dim CategoryRecoveryTime as UInt32
		    dim InterruptFlags as UInt32
		    dim AuraInterruptFlags as UInt32
		    dim ChannelInterruptFlags as UInt32
		    dim procFlags as UInt32
		    dim procChance as UInt32
		    dim procCharges as UInt32
		    dim maxLevel as UInt32
		    dim baseLevel as UInt32
		    dim spellLevel as UInt32
		    dim DurationIndex as UInt32
		    dim powerType as UInt32
		    dim manaCost as UInt32
		    dim manaCostPerlevel as UInt32
		    dim manaPerSecond as UInt32
		    dim manaPerSecondPerLevel as UInt32
		    dim rangeIndex as UInt32
		    dim speed as Single
		    dim modalNextSpell as UInt32
		    dim maxstack as UInt32
		    dim Totem1 as UInt32
		    dim Totem2 as UInt32
		    dim Reagent1 as UInt32
		    dim Reagent2 as UInt32
		    dim Reagent3 as UInt32
		    dim Reagent4 as UInt32
		    dim Reagent5 as UInt32
		    dim Reagent6 as UInt32
		    dim Reagent7 as UInt32
		    dim Reagent8 as UInt32
		    dim ReagentCount1 as UInt32
		    dim ReagentCount2 as UInt32
		    dim ReagentCount3 as UInt32
		    dim ReagentCount4 as UInt32
		    dim ReagentCount5 as UInt32
		    dim ReagentCount6 as UInt32
		    dim ReagentCount7 as UInt32
		    dim ReagentCount8 as UInt32
		    dim EquippedItemClass as UInt32
		    dim EquippedItemSubClass as UInt32
		    dim RequiredItemFlags as UInt32
		    dim Effect1 as UInt32
		    dim Effect2 as UInt32
		    dim Effect3 as UInt32
		    dim EffectDieSides1 as UInt32
		    dim EffectDieSides2 as UInt32
		    dim EffectDieSides3 as UInt32
		    dim EffectBaseDice1 as UInt32
		    dim EffectBaseDice2 as UInt32
		    dim EffectBaseDice3 as UInt32
		    dim EffectDicePerLevel1 as UInt32
		    dim EffectDicePerLevel2 as UInt32
		    dim EffectDicePerLevel3 as UInt32
		    dim EffectRealPointsPerLevel1 as UInt32
		    dim EffectRealPointsPerLevel2 as UInt32
		    dim EffectRealPointsPerLevel3 as UInt32
		    dim EffectBasePoints1 as UInt32
		    dim EffectBasePoints2 as UInt32
		    dim EffectBasePoints3 as UInt32
		    dim EffectMechanic1 as UInt32
		    dim EffectMechanic2 as UInt32
		    dim EffectMechanic3 as UInt32
		    dim EffectImplicitTargetA1 as UInt32
		    dim EffectImplicitTargetA2 as UInt32
		    dim EffectImplicitTargetA3 as UInt32
		    dim EffectImplicitTargetB1 as UInt32
		    dim EffectImplicitTargetB2 as UInt32
		    dim EffectImplicitTargetB3 as UInt32
		    dim EffectRadiusIndex1 as UInt32
		    dim EffectRadiusIndex2 as UInt32
		    dim EffectRadiusIndex3 as UInt32
		    dim EffectApplyAuraName1 as UInt32
		    dim EffectApplyAuraName2 as UInt32
		    dim EffectApplyAuraName3 as UInt32
		    dim EffectAmplitude1 as UInt32
		    dim EffectAmplitude2 as UInt32
		    dim EffectAmplitude3 as UInt32
		    dim Effectunknown1 as UInt32
		    dim Effectunknown2 as UInt32
		    dim Effectunknown3 as UInt32
		    dim EffectChainTarget1 as UInt32
		    dim EffectChainTarget2 as UInt32
		    dim EffectChainTarget3 as UInt32
		    dim EffectSpellGroupRelation1 as UInt32
		    dim EffectSpellGroupRelation2 as UInt32
		    dim EffectSpellGroupRelation3 as UInt32
		    dim EffectMiscValue1 as UInt32
		    dim EffectMiscValue2 as UInt32
		    dim EffectMiscValue3 as UInt32
		    dim EffectMiscValueB1 as UInt32
		    dim EffectMiscValueB2 as UInt32
		    dim EffectMiscValueB3 as UInt32
		    dim EffectTriggerSpell1 as UInt32
		    dim EffectTriggerSpell2 as UInt32
		    dim EffectTriggerSpell3 as UInt32
		    dim EffectPointsPerComboPoint1 as UInt32
		    dim EffectPointsPerComboPoint2 as UInt32
		    dim EffectPointsPerComboPoint3 as UInt32
		    dim EffectUnk0_1 as UInt32
		    dim EffectUnk0_2 as UInt32
		    dim EffectUnk0_3 as UInt32
		    dim EffectUnk1_1 as UInt32
		    dim EffectUnk1_2 as UInt32
		    dim EffectUnk1_3 as UInt32
		    dim EffectUnk2_1 as UInt32
		    dim EffectUnk2_2 as UInt32
		    dim EffectUnk2_3 as UInt32
		    dim SpellVisual as UInt32
		    dim field114 as UInt32
		    dim spellIconID as UInt32
		    dim activeIconID as UInt32
		    dim spellPriority as UInt32
		    dim Name as String
		    dim Rank as String
		    dim Description as String
		    dim BuffDescription as String
		    dim ManaCostPercentage as UInt32
		    dim unkflags as UInt32
		    dim StartRecoveryTime as UInt32
		    dim StartRecoveryCategory as UInt32
		    dim MaxTargetLevel as UInt32
		    dim SpellFamilyName as UInt32
		    dim SpellGroupType1 as UInt32
		    dim SpellGroupType2 as UInt32
		    dim MaxTargets as UInt32
		    dim Spell_Dmg_Type as UInt32
		    dim PreventionType as UInt32
		    dim StanceBarOrder as UInt32
		    dim dmg_multiplier1 as single
		    dim dmg_multiplier2 as single
		    dim dmg_multiplier3 as single
		    dim MinFactionID as UInt32
		    dim MinReputation as UInt32
		    dim RequiredAuraVision as UInt32
		    dim TotemCategory1 as UInt32
		    dim TotemCategory2 as UInt32
		    dim RequiresAreaId as UInt32
		    dim School as UInt32
		    dim unk4 as UInt32
		    dim unk5 as UInt32
		    dim unk6 as UInt32
		    dim unk7 as UInt32
		    dim unk8 as UInt32
		    dim unk9 as UInt32
		    dim unk10 as UInt32
		    
		    if record < recordCount then
		      Window1.ProgSpell.text = str(Record) + "/" + str(recordCount - 1)
		      Window1.ProgSpell.Refresh
		      
		      Id = b.ReadInt32
		      Category = b.ReadUInt32
		      DispelType = b.ReadUInt32
		      MechanicsType = b.ReadUInt32
		      myAttributes = b.ReadUInt32
		      AttributesEx = b.ReadUInt32
		      AttributesExB = b.ReadUInt32
		      AttributesExC = b.ReadUInt32
		      AttributesExD = b.ReadUInt32
		      AttributesExE = b.ReadUInt32
		      AttributesExF = b.ReadUInt32
		      unk1 = b.ReadUInt32
		      RequiredShapeShift = b.ReadUInt32
		      unk2 = b.ReadUInt32
		      ShapeshiftExclude = b.ReadUInt32
		      unk3 = b.ReadUInt32
		      Targets = b.ReadUInt32
		      TargetCreatureType = b.ReadUInt32
		      RequiresSpellFocus = b.ReadUInt32
		      FacingCasterFlags = b.ReadUInt32
		      CasterAuraState = b.ReadUInt32
		      TargetAuraState = b.ReadUInt32
		      ExcludeCasterAuraState1 = b.ReadUInt32
		      ExcludeTargetAuraState1 = b.ReadUInt32
		      casterAuraSpell = b.ReadUInt32
		      targetAuraSpell = b.ReadUInt32
		      ExcludeCasterAuraState2 = b.ReadUInt32
		      ExcludeTargetAuraState2 = b.ReadUInt32
		      CastingTimeIndex = b.ReadUInt32
		      RecoveryTime = b.ReadUInt32
		      CategoryRecoveryTime = b.ReadUInt32
		      InterruptFlags = b.ReadUInt32
		      AuraInterruptFlags = b.ReadUInt32
		      ChannelInterruptFlags = b.ReadUInt32
		      procFlags = b.ReadUInt32
		      procChance = b.ReadUInt32
		      procCharges = b.ReadUInt32
		      maxLevel = b.ReadUInt32
		      baseLevel = b.ReadUInt32
		      spellLevel = b.ReadUInt32
		      DurationIndex = b.ReadUInt32
		      powerType = b.ReadUInt32
		      manaCost = b.ReadUInt32
		      manaCostPerlevel = b.ReadUInt32
		      manaPerSecond = b.ReadUInt32
		      manaPerSecondPerLevel = b.ReadUInt32
		      rangeIndex = b.ReadUInt32
		      speed = b.ReadSingle
		      modalNextSpell = b.ReadUInt32
		      maxstack = b.ReadUInt32
		      Totem1 = b.ReadUInt32
		      Totem2 = b.ReadUInt32
		      Reagent1 = b.ReadUInt32
		      Reagent2 = b.ReadUInt32
		      Reagent3 = b.ReadUInt32
		      Reagent4 = b.ReadUInt32
		      Reagent5 = b.ReadUInt32
		      Reagent6 = b.ReadUInt32
		      Reagent7 = b.ReadUInt32
		      Reagent8 = b.ReadUInt32
		      ReagentCount1 = b.ReadUInt32
		      ReagentCount2 = b.ReadUInt32
		      ReagentCount3 = b.ReadUInt32
		      ReagentCount4 = b.ReadUInt32
		      ReagentCount5 = b.ReadUInt32
		      ReagentCount6 = b.ReadUInt32
		      ReagentCount7 = b.ReadUInt32
		      ReagentCount8 = b.ReadUInt32
		      EquippedItemClass = b.ReadUInt32
		      EquippedItemSubClass = b.ReadUInt32
		      RequiredItemFlags = b.ReadUInt32
		      Effect1 = b.ReadUInt32
		      Effect2 = b.ReadUInt32
		      Effect3 = b.ReadUInt32
		      EffectDieSides1 = b.ReadUInt32
		      EffectDieSides2 = b.ReadUInt32
		      EffectDieSides3 = b.ReadUInt32
		      // as of 3.3.3 these no longer exist
		      //EffectBaseDice1 = b.ReadUInt32
		      //EffectBaseDice2 = b.ReadUInt32
		      //EffectBaseDice3 = b.ReadUInt32
		      //EffectDicePerLevel1 = b.ReadUInt32
		      //EffectDicePerLevel2 = b.ReadUInt32
		      //EffectDicePerLevel3 = b.ReadUInt32
		      EffectRealPointsPerLevel1 = b.ReadUInt32
		      EffectRealPointsPerLevel2 = b.ReadUInt32
		      EffectRealPointsPerLevel3 = b.ReadUInt32
		      EffectBasePoints1 = b.ReadUInt32
		      EffectBasePoints2 = b.ReadUInt32
		      EffectBasePoints3 = b.ReadUInt32
		      EffectMechanic1 = b.ReadUInt32
		      EffectMechanic2 = b.ReadUInt32
		      EffectMechanic3 = b.ReadUInt32
		      EffectImplicitTargetA1 = b.ReadUInt32
		      EffectImplicitTargetA2 = b.ReadUInt32
		      EffectImplicitTargetA3 = b.ReadUInt32
		      EffectImplicitTargetB1 = b.ReadUInt32
		      EffectImplicitTargetB2 = b.ReadUInt32
		      EffectImplicitTargetB3 = b.ReadUInt32
		      EffectRadiusIndex1 = b.ReadUInt32
		      EffectRadiusIndex2 = b.ReadUInt32
		      EffectRadiusIndex3 = b.ReadUInt32
		      EffectApplyAuraName1 = b.ReadUInt32
		      EffectApplyAuraName2 = b.ReadUInt32
		      EffectApplyAuraName3 = b.ReadUInt32
		      EffectAmplitude1 = b.ReadUInt32
		      EffectAmplitude2 = b.ReadUInt32
		      EffectAmplitude3 = b.ReadUInt32
		      Effectunknown1 = b.ReadUInt32
		      Effectunknown2 = b.ReadUInt32
		      Effectunknown3 = b.ReadUInt32
		      EffectChainTarget1 = b.ReadUInt32
		      EffectChainTarget2 = b.ReadUInt32
		      EffectChainTarget3 = b.ReadUInt32
		      EffectSpellGroupRelation1 = b.ReadUInt32
		      EffectSpellGroupRelation2 = b.ReadUInt32
		      EffectSpellGroupRelation3 = b.ReadUInt32
		      EffectMiscValue1 = b.ReadUInt32
		      EffectMiscValue2 = b.ReadUInt32
		      EffectMiscValue3 = b.ReadUInt32
		      EffectMiscValueB1 = b.ReadUInt32
		      EffectMiscValueB2 = b.ReadUInt32
		      EffectMiscValueB3 = b.ReadUInt32
		      EffectTriggerSpell1 = b.ReadUInt32
		      EffectTriggerSpell2 = b.ReadUInt32
		      EffectTriggerSpell3 = b.ReadUInt32
		      EffectPointsPerComboPoint1 = b.ReadUInt32
		      EffectPointsPerComboPoint2 = b.ReadUInt32
		      EffectPointsPerComboPoint3 = b.ReadUInt32
		      EffectUnk0_1 = b.ReadUInt32
		      EffectUnk0_2 = b.ReadUInt32
		      EffectUnk0_3 = b.ReadUInt32
		      EffectUnk1_1 = b.ReadUInt32
		      EffectUnk1_2 = b.ReadUInt32
		      EffectUnk1_3 = b.ReadUInt32
		      EffectUnk2_1 = b.ReadUInt32
		      EffectUnk2_2 = b.ReadUInt32
		      EffectUnk2_3 = b.ReadUInt32
		      SpellVisual = b.ReadUInt32
		      field114 = b.ReadUInt32
		      spellIconID = b.ReadUInt32
		      activeIconID = b.ReadUInt32
		      spellPriority = b.ReadUInt32
		      
		      offset = b.Position
		      stringPos = b.ReadUInt32
		      Name = MySQLPrepare(GetString(stringStart + stringPos, b))
		      //skip useless data
		      offset = offset + (17 * 4)
		      b.Position = offset
		      
		      offset = b.Position
		      stringPos = b.ReadUInt32
		      Rank = MySQLPrepare(GetString(stringStart + stringPos, b))
		      //skip useless data
		      offset = offset + (17 * 4)
		      b.Position = offset
		      
		      offset = b.Position
		      stringPos = b.ReadUInt32
		      Description = MySQLPrepare(GetString(stringStart + stringPos, b))
		      //skip useless data
		      offset = offset + (17 * 4)
		      b.Position = offset
		      
		      offset = b.Position
		      stringPos = b.ReadUInt32
		      BuffDescription = MySQLPrepare(GetString(stringStart + stringPos, b))
		      //skip useless data
		      offset = offset + (17 * 4)
		      b.Position = offset
		      
		      ManaCostPercentage = b.ReadUInt32
		      unkflags = b.ReadUInt32
		      StartRecoveryTime = b.ReadUInt32
		      StartRecoveryCategory = b.ReadUInt32
		      MaxTargetLevel = b.ReadUInt32
		      SpellFamilyName = b.ReadUInt32
		      SpellGroupType1 = b.ReadUInt32
		      SpellGroupType2 = b.ReadUInt32
		      MaxTargets = b.ReadUInt32
		      Spell_Dmg_Type = b.ReadUInt32
		      PreventionType = b.ReadUInt32
		      StanceBarOrder = b.ReadUInt32
		      dmg_multiplier1 = b.ReadSingle
		      dmg_multiplier2 = b.ReadSingle
		      dmg_multiplier3 = b.ReadSingle
		      MinFactionID = b.ReadUInt32
		      MinReputation = b.ReadUInt32
		      RequiredAuraVision = b.ReadUInt32
		      TotemCategory1 = b.ReadUInt32
		      TotemCategory2 = b.ReadUInt32
		      RequiresAreaId = b.ReadUInt32
		      School = b.ReadUInt32
		      unk4 = b.ReadUInt32
		      unk5 = b.ReadUInt32
		      unk6 = b.ReadUInt32
		      unk7 = b.ReadUInt32
		      unk8 = b.ReadUInt32
		      unk9 = b.ReadUInt32
		      unk10 = b.ReadUInt32
		      
		      b.Position = b.Position + 4 // new field added in 3.3, we don't need it
		      
		      dim query as string
		      query = "INSERT INTO spell VALUES(" + str(Id) + ", " + str(Category) + ", " + str(DispelType) + ", " + str(MechanicsType) + ", " _
		      + str(myAttributes) + ", " + str(AttributesEx) + ", " + str(AttributesExB) + ", " + str(AttributesExC) + ", " + str(AttributesExD) + ", " _
		      + str(AttributesExE) + ", " + str(AttributesExF) + ", " + str(unk1) + ", " + str(RequiredShapeShift) + ", " + str(unk2) + ", " _
		      + str(ShapeshiftExclude) + ", " + str(unk3) + ", " + str(Targets) + ", " + str(TargetCreatureType) + ", " + str(RequiresSpellFocus) + ", " _
		      + str(FacingCasterFlags) + ", " + str(CasterAuraState) + ", " + str(TargetAuraState) + ", " + str(ExcludeCasterAuraState1) + ", " _
		      + str(ExcludeTargetAuraState1) + ", " + str(casterAuraSpell) + ", " + str(targetAuraSpell) + ", " + str(ExcludeCasterAuraState2) + ", " _
		      + str(ExcludeTargetAuraState2) + ", " + str(CastingTimeIndex) + ", " + str(RecoveryTime) + ", " + str(CategoryRecoveryTime) + ", " _
		      + str(InterruptFlags) + ", " + str(AuraInterruptFlags) + ", " + str(ChannelInterruptFlags) + ", " + str(procFlags) + ", " + str(procChance) + ", " _
		      + str(procCharges) + ", " + str(maxLevel) + ", " + str(baseLevel) + ", " + str(spellLevel) + ", " + str(DurationIndex) + ", " + str(powerType) + ", " _
		      + str(manaCost) + ", " + str(manaCostPerlevel) + ", " + str(manaPerSecond) + ", " + str(manaPerSecondPerLevel) + ", " + str(rangeIndex) + ", " _
		      + str(speed) + ", " + str(modalNextSpell) + ", " + str(maxstack) + ", " + str(Totem1) + ", " + str(Totem2) + ", " + str(Reagent1) + ", " _
		      + str(Reagent2) + ", " + str(Reagent3) + ", " + str(Reagent4) + ", " + str(Reagent5) + ", " + str(Reagent6) + ", " + str(Reagent7) + ", " _
		      + str(Reagent8) + ", " + str(ReagentCount1) + ", " + str(ReagentCount2) + ", " + str(ReagentCount3) + ", " + str(ReagentCount4) + ", " _
		      + str(ReagentCount5) + ", " + str(ReagentCount6) + ", " + str(ReagentCount7) + ", " + str(ReagentCount8) + ", " + str(EquippedItemClass) + ", " _
		      + str(EquippedItemSubClass) + ", " + str(RequiredItemFlags) + ", " + str(Effect1) + ", " + str(Effect2) + ", " + str(Effect3) + ", " _
		      + str(EffectDieSides1) + ", " + str(EffectDieSides2) + ", " + str(EffectDieSides3) + ", " + str(EffectBaseDice1) + ", " + str(EffectBaseDice2) + ", " _
		      + str(EffectBaseDice3) + ", " + str(EffectDicePerLevel1) + ", " + str(EffectDicePerLevel2) + ", " + str(EffectDicePerLevel3) + ", " _
		      + str(EffectRealPointsPerLevel1) + ", " + str(EffectRealPointsPerLevel2) + ", " + str(EffectRealPointsPerLevel3) + ", " + str(EffectBasePoints1) + ", " _
		      + str(EffectBasePoints2) + ", " + str(EffectBasePoints3) + ", " + str(EffectMechanic1) + ", " + str(EffectMechanic2) + ", " + str(EffectMechanic3) + ", " _
		      + str(EffectImplicitTargetA1) + ", " + str(EffectImplicitTargetA2) + ", " + str(EffectImplicitTargetA3) + ", " + str(EffectImplicitTargetB1) + ", " _
		      + str(EffectImplicitTargetB2) + ", " + str(EffectImplicitTargetB3) + ", " + str(EffectRadiusIndex1) + ", " + str(EffectRadiusIndex2) + ", " _
		      + str(EffectRadiusIndex3) + ", " + str(EffectApplyAuraName1) + ", " + str(EffectApplyAuraName2) + ", " + str(EffectApplyAuraName3) + ", " _
		      + str(EffectAmplitude1) + ", " + str(EffectAmplitude2) + ", " + str(EffectAmplitude3) + ", " + str(Effectunknown1) + ", " + str(Effectunknown2) + ", " _
		      + str(Effectunknown3) + ", " + str(EffectChainTarget1) + ", " + str(EffectChainTarget2) + ", " + str(EffectChainTarget3) + ", " _
		      + str(EffectSpellGroupRelation1) + ", " + str(EffectSpellGroupRelation2) + ", " + str(EffectSpellGroupRelation3) + ", " + str(EffectMiscValue1) + ", " _
		      + str(EffectMiscValue2) + ", " + str(EffectMiscValue3) + ", " + str(EffectMiscValueB1) + ", " + str(EffectMiscValueB2) + ", " _
		      + str(EffectMiscValueB3) + ", " + str(EffectTriggerSpell1) + ", " + str(EffectTriggerSpell2) + ", " + str(EffectTriggerSpell3) + ", " _
		      + str(EffectPointsPerComboPoint1) + ", " + str(EffectPointsPerComboPoint2) + ", " + str(EffectPointsPerComboPoint3) + ", " + str(EffectUnk0_1) + ", " _
		      + str(EffectUnk0_2) + ", " + str(EffectUnk0_3) + ", " + str(EffectUnk1_1) + ", " + str(EffectUnk1_2) + ", " + str(EffectUnk1_3) + ", " _
		      + str(EffectUnk2_1) + ", " + str(EffectUnk2_2) + ", " + str(EffectUnk2_3) + ", " + str(SpellVisual) + ", " + str(field114) + ", " + str(spellIconID) + ", " _
		      + str(activeIconID) + ", " + str(spellPriority) + ", '" + Name + "', '" +Rank + "', '" +Description + "', '" +BuffDescription + "', " _
		      + str(ManaCostPercentage) + ", " + str(unkflags) + ", " + str(StartRecoveryTime) + ", " + str(StartRecoveryCategory) + ", " _
		      + str(MaxTargetLevel) + ", " + str(SpellFamilyName) + ", " + str(SpellGroupType1) + ", " + str(SpellGroupType2) + ", " + str(MaxTargets) + ", " _
		      + str(Spell_Dmg_Type) + ", " + str(PreventionType) + ", " + str(StanceBarOrder) + ", " + str(dmg_multiplier1) + ", " + str(dmg_multiplier2) + ", " _
		      + str(dmg_multiplier3) + ", " + str(MinFactionID) + ", " + str(MinReputation) + ", " + str(RequiredAuraVision) + ", " + str(TotemCategory1) + ", " _
		      + str(TotemCategory2) + ", " + str(RequiresAreaId) + ", " + str(School) + ", " + str(unk4) + ", " + str(unk5) + ", " + str(unk6) + ", " _
		      + str(unk7) + ", " + str(unk8) + ", " + str(unk9) + ", " + str(unk10) + ")"
		      
		      db.SQLExecute(query)
		      
		      if db.ErrorMessage <> "" then
		        Window1.TextArea1.text = Window1.TextArea1.text + chr(13) + db.ErrorMessage + "(" + query + ")"
		        exit do
		      end if
		      Record = Record + 1
		    else
		      Window1.ProgSpell.text = "COMPLETE"
		      Window1.ProgSpell.Refresh
		      exit do
		    end if
		  loop
		End Sub
	#tag EndEvent


	#tag Note, Name = LICENSE
		
		CoreManager, PHP Front End for ArcEmu, MaNGOS, and TrinityCore
		    Copyright (C) 2010  CoreManager Project
		
		This program is free software: you can redistribute it and/or modify
		it under the terms of the GNU General Public License as published by
		the Free Software Foundation, either version 3 of the License, or
		(at your option) any later version.
		
		This program is distributed in the hope that it will be useful,
		but WITHOUT ANY WARRANTY; without even the implied warranty of
		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
		GNU General Public License for more details.
		
		You should have received a copy of the GNU General Public License
		along with this program.  If not, see <http://www.gnu.org/licenses/>.
	#tag EndNote


	#tag ViewBehavior
		#tag ViewProperty
			Name="Index"
			Visible=true
			Group="ID"
			InheritedFrom="thread"
		#tag EndViewProperty
		#tag ViewProperty
			Name="Left"
			Visible=true
			Group="Position"
			Type="Integer"
			InheritedFrom="thread"
		#tag EndViewProperty
		#tag ViewProperty
			Name="Name"
			Visible=true
			Group="ID"
			InheritedFrom="thread"
		#tag EndViewProperty
		#tag ViewProperty
			Name="Priority"
			Visible=true
			Group="Behavior"
			InitialValue="5"
			Type="Integer"
			InheritedFrom="thread"
		#tag EndViewProperty
		#tag ViewProperty
			Name="StackSize"
			Visible=true
			Group="Behavior"
			InitialValue="0"
			Type="Integer"
			InheritedFrom="thread"
		#tag EndViewProperty
		#tag ViewProperty
			Name="Super"
			Visible=true
			Group="ID"
			InheritedFrom="thread"
		#tag EndViewProperty
		#tag ViewProperty
			Name="Top"
			Visible=true
			Group="Position"
			Type="Integer"
			InheritedFrom="thread"
		#tag EndViewProperty
	#tag EndViewBehavior
End Class
#tag EndClass
