<?php
/*
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
*/

require_once 'header.php';
require_once 'libs/char_lib.php';
require_once 'libs/item_lib.php';
require_once 'libs/spell_lib.php';
require_once 'libs/map_zone_lib.php';
valid_login($action_permission['view']);

//########################################################################################################################
// SHOW GENERAL CHARACTERS INFO
//########################################################################################################################
function char_main()
{
  global $output,
    $realm_id, $logon_db, $characters_db, $world_db, $server, $corem_db,
    $action_permission, $user_lvl, $user_name, $user_id,
    $item_datasite, $spell_datasite, $showcountryflag, $timezone, $sql, $core;

  // this page uses wowhead tooltops
  //wowhead_tt();

  // we need at either an id or a name or we would have nothing to show
  if (empty($_GET['id']))
    if (empty($_GET['name']))
      error(lang('global', 'empty_fields'));

  // this is multi realm support, as of writing still under development
  // this page is already implementing it
  if (empty($_GET['realm']))
    $realmid = $realm_id;
  else
  {
    $realmid = $sql['logon']->quote_smart($_GET['realm']);
    if (is_numeric($realmid))
      $sql['char']->connect($characters_db[$realmid]['addr'], $characters_db[$realmid]['user'], $characters_db[$realmid]['pass'], $characters_db[$realmid]['name']);
    else
      $realmid = $realm_id;
  }

  if (empty($_GET['id']))
  {
    $name = $sql['char']->quote_smart($_GET['name']);
    if ( $core == 1 )
      $result = $sql['char']->query("SELECT guid, acct, race FROM characters WHERE name = '".$name."' LIMIT 1");
    else
      $result = $sql['char']->query("SELECT guid, id AS acct, race FROM characters WHERE name = '".$name."' LIMIT 1");
    $id_result = $sql['char']->fetch_assoc($result);
    $id = $id_result['guid'];
  }
  else
  {
    $id = $sql['char']->quote_smart($_GET['id']);
  }

  if (is_numeric($id))
    ;
  else 
    error(lang('global', 'empty_fields'));

  if ( $core == 1 )
    $result = $sql['char']->query('SELECT acct, race FROM characters WHERE guid = '.$id.' LIMIT 1');
  else
    $result = $sql['char']->query('SELECT account AS acct, race FROM characters WHERE guid = '.$id.' LIMIT 1');

  if ($sql['char']->num_rows($result))
  {
    //resrict by owner's gmlvl
    $owner_acc_id = $sql['char']->result($result, 0, 'acct');
    if ( $core == 1 )
      $query = $sql['logon']->query("SELECT login FROM accounts WHERE acct='".$owner_acc_id."'");
    else
      $query = $sql['logon']->query("SELECT username as login FROM account WHERE id='".$owner_acc_id."'");
    $owner_name = $sql['logon']->result($query, 0, 'login');

    $query = $sql['mgr']->query("SELECT SecurityLevel AS gm FROM config_accounts WHERE Login='".$owner_name."'");
    $owner_gmlvl = $sql['mgr']->result($query, 0, 'gm');

    if($user_lvl || $server[$realmid]['both_factions'])
    {
      $side_v = 0;
      $side_p = 0;
    }
    else
    {
      $side_p = (in_array($sql['char']->result($result, 0, 'race'),array(2,5,6,8,10))) ? 1 : 2;
      $result_1 = $sql['char']->query('SELECT race FROM characters WHERE acct = '.$user_id.' LIMIT 1');
      if ($sql['char']->num_rows($result))
        $side_v = (in_array($sql['char']->result($result_1, 0, 'race'), array(2,5,6,8,10))) ? 1 : 2;
      else
        $side_v = 0;
      unset($result_1);
    }

    if ($user_lvl >= gmlevel($owner_gmlvl) && (($side_v === $side_p) || !$side_v))
    {
      if ( $core == 1 )
      {
        $result = $sql['char']->query("SELECT guid, name, race, class, level, zoneid, mapid, online, gender,
          SUBSTRING_INDEX(SUBSTRING_INDEX(playedtime, ' ', 2), ' ', -1) AS totaltime,
          acct, data, timestamp 
          FROM characters WHERE guid = '".$id."'");
      }
      else
      {
        $result = $sql['char']->query("SELECT guid, name, race, class, level, zone AS zoneid, map AS mapid, online, gender,
          totaltime, account AS acct, logout_time AS timestamp, health, 
					power1, power2, power3, power4, power5, power6, power7 
          FROM characters WHERE guid = '".$id."'");
      }
      $char = $sql['char']->fetch_assoc($result);
      
      if ( $core == 1 )
      {
        $char_data = $char['data'];
        if (empty($char_data))
          $char_data = str_repeat('0;', PLAYER_END);
        $char_data = explode(';',$char_data);
      }
      else
      {
        $query = "SELECT * FROM characters LEFT JOIN character_stats ON characters.guid=character_stats.guid WHERE characters.guid='".$id."'";
        $char_data_result = $sql['char']->query($query);
        $char_data_fields = $sql['char']->fetch_assoc($char_data_result);
        $char_data[PLAYER_BLOCK_PERCENTAGE] = $char_data_fields['blockPct'];
        $char_data[PLAYER_DODGE_PERCENTAGE] = $char_data_fields['dodgePct'];
        $char_data[PLAYER_PARRY_PERCENTAGE] = $char_data_fields['parryPct'];
        $char_data[PLAYER_CRIT_PERCENTAGE] = $char_data_fields['critPct'];
        $char_data[PLAYER_RANGED_CRIT_PERCENTAGE] = $char_data_fields['rangedCritPct'];
        $char_data[UNIT_FIELD_MAXDAMAGE] = $char_data_fields['attackPower'];
        $char_data[UNIT_FIELD_MINDAMAGE] = $char_data_fields['attackPower'];
        $char_data[UNIT_FIELD_MAXRANGEDDAMAGE] = $char_data_fields['rangedAttackPower'];
        $char_data[UNIT_FIELD_MINRANGEDDAMAGE] = $char_data_fields['rangedAttackPower'];
        $char_data[PLAYER_SPELL_CRIT_PERCENTAGE1] = $char_data_fields['spellCritPct'];
        $char_data[PLAYER_FIELD_MOD_DAMAGE_DONE_POS] = $char_data_fields['spellPower'];
        $char_data[UNIT_FIELD_STAT0] = $char_data_fields['strength'];
        $char_data[UNIT_FIELD_STAT1] = $char_data_fields['agility'];
        $char_data[UNIT_FIELD_STAT2] = $char_data_fields['stamina'];
        $char_data[UNIT_FIELD_STAT3] = $char_data_fields['intellect'];
        $char_data[UNIT_FIELD_STAT4] = $char_data_fields['spirit'];
        $char_data[UNIT_FIELD_RESISTANCES] = $char_data_fields['armor'];
        $char_data[UNIT_FIELD_RESISTANCES + 1] = $char_data_fields['resHoly'];
        $char_data[UNIT_FIELD_RESISTANCES + 2] = $char_data_fields['resArcane'];
        $char_data[UNIT_FIELD_RESISTANCES + 3] = $char_data_fields['resFire'];
        $char_data[UNIT_FIELD_RESISTANCES + 4] = $char_data_fields['resNature'];
        $char_data[UNIT_FIELD_RESISTANCES + 5] = $char_data_fields['resFrost'];
        $char_data[UNIT_FIELD_RESISTANCES + 6] = $char_data_fields['resShadow'];
        $char_data[UNIT_FIELD_HEALTH] = $char['health'];
        $char_data[UNIT_FIELD_MAXHEALTH] = $char_data_fields['maxhealth'];
        $char_data[UNIT_FIELD_POWER1] = $char['power1'];
        $char_data[UNIT_FIELD_POWER2] = $char['power2'];
        $char_data[UNIT_FIELD_POWER3] = $char['power3'];
        $char_data[UNIT_FIELD_POWER4] = $char['power4'];
        $char_data[UNIT_FIELD_POWER5] = $char['power5'];
        $char_data[UNIT_FIELD_POWER6] = $char['power6'];
        $char_data[UNIT_FIELD_POWER7] = $char['power7'];
        $char_data[UNIT_FIELD_MAXPOWER1] = $char_data_fields['maxpower1'];
        $char_data[UNIT_FIELD_MAXPOWER2] = $char_data_fields['maxpower2'];
        $char_data[UNIT_FIELD_MAXPOWER3] = $char_data_fields['maxpower3'];
        $char_data[UNIT_FIELD_MAXPOWER4] = $char_data_fields['maxpower4'];
        $char_data[UNIT_FIELD_MAXPOWER5] = $char_data_fields['maxpower5'];
        $char_data[UNIT_FIELD_MAXPOWER6] = $char_data_fields['maxpower6'];
        $char_data[UNIT_FIELD_MAXPOWER7] = $char_data_fields['maxpower7'];
        $char_data[PLAYER_FIELD_MOD_HEALING_DONE_POS] = "ERR";
        $char_data[PLAYER_FIELD_COMBAT_RATING_1+5] = "ERR";
        $char_data[PLAYER_FIELD_COMBAT_RATING_1+17] = "ERR";
        $char_data[PLAYER_FIELD_COMBAT_RATING_1+6] = "ERR";
        $char_data[PLAYER_FIELD_COMBAT_RATING_1+7] = "ERR";
        $char_data[PLAYER_EXPERTISE] = "ERR";
        $char_data[PLAYER_OFFHAND_EXPERTISE] = "ERR";
      }

      if ( $core == 1 )
      {
        $guild_id = $sql['char']->result($sql['char']->query("SELECT guildid FROM guild_data WHERE playerid='".$char['guid']."'"), 0);
        $guild_rank = $sql['char']->result($sql['char']->query("SELECT guildRank FROM guild_data WHERE playerid='".$char['guid']."'"), 0);
        $guild_name = $sql['char']->result($sql['char']->query("SELECT guildName FROM guilds WHERE guildid='".$guild_id."'"));
      }
      else
      {
        $guild_id = $sql['char']->result($sql['char']->query("SELECT guildid FROM guild_member WHERE guid='".$char['guid']."'"), 0);
        $guild_rank = $sql['char']->result($sql['char']->query("SELECT rank AS guildRank FROM guild_member WHERE guid='".$char['guid']."'"), 0);
        $guild_name = $sql['char']->result($sql['char']->query("SELECT name AS guildName FROM guild WHERE guildid='".$guild_id."'"));
      }

      $online = ($char['online']) ? lang('char', 'online') : lang('char', 'offline');

      if($guild_id)
      {
        //$guild_name = $sql['char']->result($sql['char']->query('SELECT name FROM guild WHERE guildid ='.$char_data[CHAR_DATA_OFFSET_GUILD_ID].''), 0, 'name');
        $guild_name = '<a href="guild.php?action=view_guild&amp;realm='.$realmid.'&amp;error=3&amp;id='.$guild_id.'" >'.$guild_name.'</a>';
        $mrank = $guild_rank;
        if ( $core == 1 )
          $guild_rank = $sql['char']->result($sql['char']->query('SELECT rankname FROM guild_ranks WHERE guildid ='.$guild_id.' AND rankId='.$mrank.''), 0, 'rankname');
        else
          $guild_rank = $sql['char']->result($sql['char']->query('SELECT rname AS rankname FROM guild_rank WHERE guildid ='.$guild_id.' AND rid='.$mrank.''), 0, 'rankname');
      }
      else
      {
        $guild_name = lang('global', 'none');
        $guild_rank = lang('global', 'none');
      }

      if ( $core == 1 )
      {
        $block           = unpack('f', pack('L', $char_data[PLAYER_BLOCK_PERCENTAGE]));
        $block           = round($block[1],2);
        $dodge           = unpack('f', pack('L', $char_data[PLAYER_DODGE_PERCENTAGE]));
        $dodge           = round($dodge[1],2);
        $parry           = unpack('f', pack('L', $char_data[PLAYER_PARRY_PERCENTAGE]));
        $parry           = round($parry[1],2);
        $crit            = unpack('f', pack('L', $char_data[PLAYER_CRIT_PERCENTAGE]));
        $crit            = round($crit[1],2);
        $ranged_crit     = unpack('f', pack('L', $char_data[PLAYER_RANGED_CRIT_PERCENTAGE]));
        $ranged_crit     = round($ranged_crit[1],2);
        $maxdamage       = unpack('f', pack('L', $char_data[UNIT_FIELD_MAXDAMAGE]));
        $maxdamage       = round($maxdamage[1],0);
        $mindamage       = unpack('f', pack('L', $char_data[UNIT_FIELD_MINDAMAGE]));
        $mindamage       = round($mindamage[1],0);
        $maxrangeddamage = unpack('f', pack('L', $char_data[UNIT_FIELD_MAXRANGEDDAMAGE]));
        $maxrangeddamage = round($maxrangeddamage[1],0);
        $minrangeddamage = unpack('f', pack('L', $char_data[UNIT_FIELD_MINRANGEDDAMAGE]));
        $minrangeddamage = round($minrangeddamage[1],0);
      }
      else
      {
        $block           = $char_data[PLAYER_BLOCK_PERCENTAGE];
        $block           = round($block,2);
        $dodge           = $char_data[PLAYER_DODGE_PERCENTAGE];
        $dodge           = round($dodge,2);
        $parry           = $char_data[PLAYER_PARRY_PERCENTAGE];
        $parry           = round($parry,2);
        $crit            = $char_data[PLAYER_CRIT_PERCENTAGE];
        $crit            = round($crit,2);
        $ranged_crit     = $char_data[PLAYER_RANGED_CRIT_PERCENTAGE];
        $ranged_crit     = round($ranged_crit,2);
        $maxdamage       = $char_data[UNIT_FIELD_MAXDAMAGE];
        $maxdamage       = round($maxdamage,0);
        $mindamage       = $char_data[UNIT_FIELD_MINDAMAGE];
        $mindamage       = round($mindamage,0);
        $maxrangeddamage = $char_data[UNIT_FIELD_MAXRANGEDDAMAGE];
        $maxrangeddamage = round($maxrangeddamage,0);
        $minrangeddamage = $char_data[UNIT_FIELD_MINRANGEDDAMAGE];
        $minrangeddamage = round($minrangeddamage,0);
      }

      if ( $core == 1 )
      {
        $spell_crit = 100;
        for ($i=0; $i<6; ++$i)
        {
          $temp = unpack('f', pack('L', $char_data[PLAYER_SPELL_CRIT_PERCENTAGE1+1+$i]));
          if ($temp[1] < $spell_crit)
          $spell_crit = $temp[1];
        }
        $spell_crit = round($spell_crit,2);
      }
      else
      {
        $spell_crit = $char_data[PLAYER_SPELL_CRIT_PERCENTAGE1];
        $spell_crit = round($spell_crit,2);
      }

      if ( $core == 1 )
      {
        $spell_damage = 9999;
        for ($i=0; $i<6; ++$i)
        {
          if ($char_data[PLAYER_FIELD_MOD_DAMAGE_DONE_POS+1+$i] < $spell_damage)
          $spell_damage = $char_data[PLAYER_FIELD_MOD_DAMAGE_DONE_POS+1+$i];
        }
      }
      else
      {
        $spell_damage = $char_data[PLAYER_FIELD_MOD_DAMAGE_DONE_POS];
      }

      $spell_heal = $char_data[PLAYER_FIELD_MOD_HEALING_DONE_POS];

      // this_is_junk: PLAYER_FIELD_COMBAT_RATING_1 +5, +6, and +7 seem to have the same value as +5
      //               I'm not sure which of these fields is which hit rating. :/
      $spell_hit = $char_data[PLAYER_FIELD_COMBAT_RATING_1+5];

      // this_is_junk: PLAYER_FIELD_COMBAT_RATING_1 +18 and +19 seem to have the same value as +5
      //               I'm not sure which of these fields is really spell haste. :/
      $spell_haste = $char_data[PLAYER_FIELD_COMBAT_RATING_1+17];

      // this_is_junk: PLAYER_FIELD_COMBAT_RATING_1 +5, +6, and +7 seem to have the same value as +5
      //               I'm not sure which of these fields is which hit rating. :/
      $ranged_hit = $char_data[PLAYER_FIELD_COMBAT_RATING_1+6];

      // this_is_junk: PLAYER_FIELD_COMBAT_RATING_1 +5, +6, and +7 seem to have the same value as +5
      //               I'm not sure which of these fields is which hit rating. :/
      $melee_hit = $char_data[PLAYER_FIELD_COMBAT_RATING_1+7];

      $expertise  = ''.$char_data[PLAYER_EXPERTISE].' / '.$char_data[PLAYER_OFFHAND_EXPERTISE].'';

      if ( $core == 1 )
      {
        $EQU_HEAD      = $char_data[PLAYER_FIELD_INV_SLOT_HEAD + 0];
        $EQU_NECK      = $char_data[PLAYER_FIELD_INV_SLOT_HEAD + 2];
        $EQU_SHOULDER  = $char_data[PLAYER_FIELD_INV_SLOT_HEAD + 4];
        $EQU_SHIRT     = $char_data[PLAYER_FIELD_INV_SLOT_HEAD + 6];
        $EQU_CHEST     = $char_data[PLAYER_FIELD_INV_SLOT_HEAD + 8];
        $EQU_BELT      = $char_data[PLAYER_FIELD_INV_SLOT_HEAD + 10];
        $EQU_LEGS      = $char_data[PLAYER_FIELD_INV_SLOT_HEAD + 12];
        $EQU_FEET      = $char_data[PLAYER_FIELD_INV_SLOT_HEAD + 14];
        $EQU_WRIST     = $char_data[PLAYER_FIELD_INV_SLOT_HEAD + 16];
        $EQU_GLOVES    = $char_data[PLAYER_FIELD_INV_SLOT_HEAD + 18];
        $EQU_FINGER1   = $char_data[PLAYER_FIELD_INV_SLOT_HEAD + 20];
        $EQU_FINGER2   = $char_data[PLAYER_FIELD_INV_SLOT_HEAD + 22];
        $EQU_TRINKET1  = $char_data[PLAYER_FIELD_INV_SLOT_HEAD + 24];
        $EQU_TRINKET2  = $char_data[PLAYER_FIELD_INV_SLOT_HEAD + 26];
        $EQU_BACK      = $char_data[PLAYER_FIELD_INV_SLOT_HEAD + 28];
        $EQU_MAIN_HAND = $char_data[PLAYER_FIELD_INV_SLOT_HEAD + 30];
        $EQU_OFF_HAND  = $char_data[PLAYER_FIELD_INV_SLOT_HEAD + 32];
        $EQU_RANGED    = $char_data[PLAYER_FIELD_INV_SLOT_HEAD + 34];
        $EQU_TABARD    = $char_data[PLAYER_FIELD_INV_SLOT_HEAD + 36];
      }
      else
      {
        $world_db_name = $world_db[$realm_id]['name'];
        $char_equip_query = "SELECT * FROM character_inventory WHERE guid='".$id."' AND character_inventory.bag=0";
        $char_equip_result = $sql['char']->query($char_equip_query);
        
        while ( $equip_row = $sql['char']->fetch_assoc($char_equip_result) )
        {
          switch ( $equip_row['slot'] )
          {
            case 0:
            {
              $EQU_HEAD = $equip_row['item_template'];
              break;
            }
            case 1:
            {
              $EQU_NECK = $equip_row['item_template'];
              break;
            }
            case 2:
            {
              $EQU_SHOULDER = $equip_row['item_template'];
              break;
            }
            case 3:
            {
              $EQU_SHIRT = $equip_row['item_template'];
              break;
            }
            case 4:
            {
              $EQU_CHEST = $equip_row['item_template'];
              break;
            }
            case 5:
            {
              $EQU_BELT = $equip_row['item_template'];
              break;
            }
            case 6:
            {
              $EQU_LEGS = $equip_row['item_template'];
              break;
            }
            case 7:
            {
              $EQU_FEET = $equip_row['item_template'];
              break;
            }
            case 8:
            {
              $EQU_WRIST = $equip_row['item_template'];
              break;
            }
            case 9:
            {
              $EQU_GLOVES = $equip_row['item_template'];
              break;
            }
            case 10:
            {
              $EQU_FINGER1 = $equip_row['item_template'];
              break;
            }
            case 11:
            {
              $EQU_FINGER2 = $equip_row['item_template'];
              break;
            }
            case 12:
            {
              $EQU_TRINKET1 = $equip_row['item_template'];
              break;
            }
            case 13:
            {
              $EQU_TRINKET2 = $equip_row['item_template'];
              break;
            }
            case 14:
            {
              $EQU_BACK = $equip_row['item_template'];
              break;
            }
            case 15:
            {
              $EQU_MAIN_HAND = $equip_row['item_template'];
              break;
            }
            case 16:
            {
              $EQU_OFF_HAND = $equip_row['item_template'];
              break;
            }
            case 17:
            {
              $EQU_RANGED = $equip_row['item_template'];
              break;
            }
            case 18:
            {
              $EQU_TABARD = $equip_row['item_template'];
              break;
            }
          }
        }
      }
/*
// reserved incase we want to use back CoreManager's built in tooltip, instead of wowheads'
// CoreManager's item tooltip needs updating, but it can show enchantments and sockets.

      $equiped_items = array
      (
         1 => array(($EQU_HEAD      ? get_item_tooltip($EQU_HEAD)      : 0),($EQU_HEAD      ? get_item_icon($EQU_HEAD)      : 0),($EQU_HEAD      ? get_item_border($EQU_HEAD)      : 0)),
         2 => array(($EQU_NECK      ? get_item_tooltip($EQU_NECK)      : 0),($EQU_NECK      ? get_item_icon($EQU_NECK)      : 0),($EQU_NECK      ? get_item_border($EQU_NECK)      : 0)),
         3 => array(($EQU_SHOULDER  ? get_item_tooltip($EQU_SHOULDER)  : 0),($EQU_SHOULDER  ? get_item_icon($EQU_SHOULDER)  : 0),($EQU_SHOULDER  ? get_item_border($EQU_SHOULDER)  : 0)),
         4 => array(($EQU_SHIRT     ? get_item_tooltip($EQU_SHIRT)     : 0),($EQU_SHIRT     ? get_item_icon($EQU_SHIRT)     : 0),($EQU_SHIRT     ? get_item_border($EQU_SHIRT)     : 0)),
         5 => array(($EQU_CHEST     ? get_item_tooltip($EQU_CHEST)     : 0),($EQU_CHEST     ? get_item_icon($EQU_CHEST)     : 0),($EQU_CHEST     ? get_item_border($EQU_CHEST)     : 0)),
         6 => array(($EQU_BELT      ? get_item_tooltip($EQU_BELT)      : 0),($EQU_BELT      ? get_item_icon($EQU_BELT)      : 0),($EQU_BELT      ? get_item_border($EQU_BELT)      : 0)),
         7 => array(($EQU_LEGS      ? get_item_tooltip($EQU_LEGS)      : 0),($EQU_LEGS      ? get_item_icon($EQU_LEGS)      : 0),($EQU_LEGS      ? get_item_border($EQU_LEGS)      : 0)),
         8 => array(($EQU_FEET      ? get_item_tooltip($EQU_FEET)      : 0),($EQU_FEET      ? get_item_icon($EQU_FEET)      : 0),($EQU_FEET      ? get_item_border($EQU_FEET)      : 0)),
         9 => array(($EQU_WRIST     ? get_item_tooltip($EQU_WRIST)     : 0),($EQU_WRIST     ? get_item_icon($EQU_WRIST)     : 0),($EQU_WRIST     ? get_item_border($EQU_WRIST)     : 0)),
        10 => array(($EQU_GLOVES    ? get_item_tooltip($EQU_GLOVES)    : 0),($EQU_GLOVES    ? get_item_icon($EQU_GLOVES)    : 0),($EQU_GLOVES    ? get_item_border($EQU_GLOVES)    : 0)),
        11 => array(($EQU_FINGER1   ? get_item_tooltip($EQU_FINGER1)   : 0),($EQU_FINGER1   ? get_item_icon($EQU_FINGER1)   : 0),($EQU_FINGER1   ? get_item_border($EQU_FINGER1)   : 0)),
        12 => array(($EQU_FINGER2   ? get_item_tooltip($EQU_FINGER2)   : 0),($EQU_FINGER2   ? get_item_icon($EQU_FINGER2)   : 0),($EQU_FINGER2   ? get_item_border($EQU_FINGER2)   : 0)),
        13 => array(($EQU_TRINKET1  ? get_item_tooltip($EQU_TRINKET1)  : 0),($EQU_TRINKET1  ? get_item_icon($EQU_TRINKET1)  : 0),($EQU_TRINKET1  ? get_item_border($EQU_TRINKET1)  : 0)),
        14 => array(($EQU_TRINKET2  ? get_item_tooltip($EQU_TRINKET2)  : 0),($EQU_TRINKET2  ? get_item_icon($EQU_TRINKET2)  : 0),($EQU_TRINKET2  ? get_item_border($EQU_TRINKET2)  : 0)),
        15 => array(($EQU_BACK      ? get_item_tooltip($EQU_BACK)      : 0),($EQU_BACK      ? get_item_icon($EQU_BACK)      : 0),($EQU_BACK      ? get_item_border($EQU_BACK)      : 0)),
        16 => array(($EQU_MAIN_HAND ? get_item_tooltip($EQU_MAIN_HAND) : 0),($EQU_MAIN_HAND ? get_item_icon($EQU_MAIN_HAND) : 0),($EQU_MAIN_HAND ? get_item_border($EQU_MAIN_HAND) : 0)),
        17 => array(($EQU_OFF_HAND  ? get_item_tooltip($EQU_OFF_HAND)  : 0),($EQU_OFF_HAND  ? get_item_icon($EQU_OFF_HAND)  : 0),($EQU_OFF_HAND  ? get_item_border($EQU_OFF_HAND)  : 0)),
        18 => array(($EQU_RANGED    ? get_item_tooltip($EQU_RANGED)    : 0),($EQU_RANGED    ? get_item_icon($EQU_RANGED)    : 0),($EQU_RANGED    ? get_item_border($EQU_RANGED)    : 0)),
        19 => array(($EQU_TABARD    ? get_item_tooltip($EQU_TABARD)    : 0),($EQU_TABARD    ? get_item_icon($EQU_TABARD)    : 0),($EQU_TABARD    ? get_item_border($EQU_TABARD)    : 0))
      );
*/

      $equiped_items = array
      (
         1 => array('',($EQU_HEAD      ? get_item_icon($EQU_HEAD)      : 0),($EQU_HEAD      ? get_item_border($EQU_HEAD)      : 0)),
         2 => array('',($EQU_NECK      ? get_item_icon($EQU_NECK)      : 0),($EQU_NECK      ? get_item_border($EQU_NECK)      : 0)),
         3 => array('',($EQU_SHOULDER  ? get_item_icon($EQU_SHOULDER)  : 0),($EQU_SHOULDER  ? get_item_border($EQU_SHOULDER)  : 0)),
         4 => array('',($EQU_SHIRT     ? get_item_icon($EQU_SHIRT)     : 0),($EQU_SHIRT     ? get_item_border($EQU_SHIRT)     : 0)),
         5 => array('',($EQU_CHEST     ? get_item_icon($EQU_CHEST)     : 0),($EQU_CHEST     ? get_item_border($EQU_CHEST)     : 0)),
         6 => array('',($EQU_BELT      ? get_item_icon($EQU_BELT)      : 0),($EQU_BELT      ? get_item_border($EQU_BELT)      : 0)),
         7 => array('',($EQU_LEGS      ? get_item_icon($EQU_LEGS)      : 0),($EQU_LEGS      ? get_item_border($EQU_LEGS)      : 0)),
         8 => array('',($EQU_FEET      ? get_item_icon($EQU_FEET)      : 0),($EQU_FEET      ? get_item_border($EQU_FEET)      : 0)),
         9 => array('',($EQU_WRIST     ? get_item_icon($EQU_WRIST)     : 0),($EQU_WRIST     ? get_item_border($EQU_WRIST)     : 0)),
        10 => array('',($EQU_GLOVES    ? get_item_icon($EQU_GLOVES)    : 0),($EQU_GLOVES    ? get_item_border($EQU_GLOVES)    : 0)),
        11 => array('',($EQU_FINGER1   ? get_item_icon($EQU_FINGER1)   : 0),($EQU_FINGER1   ? get_item_border($EQU_FINGER1)   : 0)),
        12 => array('',($EQU_FINGER2   ? get_item_icon($EQU_FINGER2)   : 0),($EQU_FINGER2   ? get_item_border($EQU_FINGER2)   : 0)),
        13 => array('',($EQU_TRINKET1  ? get_item_icon($EQU_TRINKET1)  : 0),($EQU_TRINKET1  ? get_item_border($EQU_TRINKET1)  : 0)),
        14 => array('',($EQU_TRINKET2  ? get_item_icon($EQU_TRINKET2)  : 0),($EQU_TRINKET2  ? get_item_border($EQU_TRINKET2)  : 0)),
        15 => array('',($EQU_BACK      ? get_item_icon($EQU_BACK)      : 0),($EQU_BACK      ? get_item_border($EQU_BACK)      : 0)),
        16 => array('',($EQU_MAIN_HAND ? get_item_icon($EQU_MAIN_HAND) : 0),($EQU_MAIN_HAND ? get_item_border($EQU_MAIN_HAND) : 0)),
        17 => array('',($EQU_OFF_HAND  ? get_item_icon($EQU_OFF_HAND)  : 0),($EQU_OFF_HAND  ? get_item_border($EQU_OFF_HAND)  : 0)),
        18 => array('',($EQU_RANGED    ? get_item_icon($EQU_RANGED)    : 0),($EQU_RANGED    ? get_item_border($EQU_RANGED)    : 0)),
        19 => array('',($EQU_TABARD    ? get_item_icon($EQU_TABARD)    : 0),($EQU_TABARD    ? get_item_border($EQU_TABARD)    : 0))
      );

      $output .= '
          <!-- start of char.php -->
          <center>
            <div id="tab">
              <ul>
                <li id="selected"><a href="char.php?id='.$id.'&amp;realm='.$realmid.'">'.lang('char', 'char_sheet').'</a></li>';

      if (($user_lvl > $owner_gmlvl)||($owner_name === $user_name)||($user_lvl == gmlevel('4')))
      {
        $output .= '
                <li><a href="char_inv.php?id='.$id.'&amp;realm='.$realmid.'">'.lang('char', 'inventory').'</a></li>
                '.(($char['level'] < 10) ? '' : '<li><a href="char_talent.php?id='.$id.'&amp;realm='.$realmid.'">'.lang('char', 'talents').'</a></li>').'
                <li><a href="char_achieve.php?id='.$id.'&amp;realm='.$realmid.'">'.lang('char', 'achievements').'</a></li>
                <li><a href="char_quest.php?id='.$id.'&amp;realm='.$realmid.'">'.lang('char', 'quests').'</a></li>
                <li><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'">'.lang('char', 'friends').'</a></li>
                <li><a href="char_view.php?id='.$id.'&amp;realm='.$realmid.'">'.lang('char', 'view').'</a></li>
              </ul>
            </div>
            <div id="tab_content">
              <div id="tab">
                <ul>
                  <li id="selected"><a href="char.php?id='.$id.'&amp;realm='.$realmid.'">'.lang('char', 'char_sheet').'</a></li>';
        if (char_get_class_name($char['class']) === 'Hunter' )
          $output .= '
                  <li><a href="char_pets.php?id='.$id.'&amp;realm='.$realmid.'">'.lang('char', 'pets').'</a></li>';
        $output .= '
                  <li><a href="char_rep.php?id='.$id.'&amp;realm='.$realmid.'">'.lang('char', 'reputation').'</a></li>
                  <li><a href="char_skill.php?id='.$id.'&amp;realm='.$realmid.'">'.lang('char', 'skills').'</a></li>';
        if ( $owner_name == $user_name )
          $output .= '
                  <li><a href="char_mail.php?id='.$id.'&amp;realm='.$realmid.'">'.lang('char', 'mail').'</a></li>';
      }
      else
        $output .='
              </ul>
            </div>
            <div id="tab_content">
              <div id="tab">
                <ul>';
      $output .='
                </ul>
              </div>
              <div id="tab_content2">
                <table class="lined" id="char_character_sheet">
                  <tr>
                    <td colspan="2">
                      <div>
                        <img src="'.char_get_avatar_img($char['level'], $char['gender'], $char['race'], $char['class'], 0).'" alt="avatar" />
                      </div>
                      <div>';
      // this_is_junk: auras are stored in a string in the characters table.
      // not sure how to query a string as though it were a record
      /*$a_results = $sql['char']->query('SELECT DISTINCT spell FROM character_aura WHERE guid = '.$id.'');
      if ($sql['char']->num_rows($a_results))
      {
        while ($aura = $sql['char']->fetch_assoc($a_results))
        {
                 $output .= '
                        <a id="char_icon_padding" href="'.$spell_datasite.$aura['spell'].'" target="_blank">
                          <img src="'.spell_get_icon($aura['spell'], $sql['mgr']).'" alt="'.$aura['spell'].'" width="24" height="24" />
                        </a>';
        }
      }*/
      $output .= '
                      </div>
                    </td>
                    <td colspan="4">
                      <font class="bold">
                        '.htmlentities($char['name']).' -
                        <img src="img/c_icons/'.$char['race'].'-'.$char['gender'].'.gif" onmousemove="toolTip(\''.char_get_race_name($char['race']).'\', \'item_tooltip\')" onmouseout="toolTip()" alt="" />
                        <img src="img/c_icons/'.$char['class'].'.gif" onmousemove="toolTip(\''.char_get_class_name($char['class']).'\', \'item_tooltip\')" onmouseout="toolTip()" alt="" />
                       - '.lang('char', 'level_short').char_get_level_color($char['level']).'
                      </font>
                      <br />'.lang('char', 'location').': '.get_map_name($char['mapid']).' - '.get_zone_name($char['zoneid']).'
                      <br />'.lang('char', 'honor_points').': '.$char_data[PLAYER_FIELD_HONOR_CURRENCY].' | '.lang('char', 'arena_points').': '.$char_data[PLAYER_FIELD_ARENA_CURRENCY].' | '.lang('char', 'honor_kills').': '.$char_data[PLAYER_FIELD_LIFETIME_HONORBALE_KILLS].'
                      <br />'.lang('char', 'guild').': '.$guild_name.' | '.lang('char', 'rank').': '.htmlentities($guild_rank).'
                      <br />'.lang('char', 'online').': '.(($char['online']) ? '<img src="img/up.gif" onmousemove="toolTip(\'Online\', \'item_tooltip\')" onmouseout="toolTip()" alt="online" />' : '<img src="img/down.gif" onmousemove="toolTip(\'Offline\', \'item_tooltip\')" onmouseout="toolTip()" alt="offline" />');
      if ($showcountryflag)
      {
        require_once 'libs/misc_lib.php';
        $country = misc_get_country_by_account($char['acct']);
        $output .= ' | '.lang('global', 'country').': '.(($country['code']) ? '<img src="img/flags/'.$country['code'].'.png" onmousemove="toolTip(\''.($country['country']).'\', \'item_tooltip\')" onmouseout="toolTip()" alt="" />' : '-');
        unset($country);
      }
      $output .= '
                    </td>
                  </tr>
                  <tr>
                    <td width="6%">';
      if (($equiped_items[1][1]))
        $output .= '
                      <a id="char_icon_padding" href="'.$item_datasite.$EQU_HEAD.'" target="_blank">
                        <img src="'.$equiped_items[1][1].'" class="'.$equiped_items[1][2].'" alt="Head" />
                      </a>';
      else
        $output .= '
                      <img src="img/INV/INV_empty_head.png" class="icon_border_0" alt="empty" />';
      $output .= '
                    </td>
                    <td class="half_line" colspan="2" align="center" width="50%">
                      <div class="gradient_p" id="char_hp_name">'.lang('item', 'health').':</div>
                      <div class="gradient_pp" id="char_hp_value">'.$char_data[UNIT_FIELD_HEALTH].'/'.$char_data[UNIT_FIELD_MAXHEALTH].'</li></ul></div>';
      if ($char['class'] == 11) //druid
        $output .= '
                      </br>
                      <div class="gradient_p" id="char_energy_name">'.lang('item', 'mana').':</div>
                      <div class="gradient_pp" id="char_energy_value">'.$char_data[UNIT_FIELD_POWER1].'/'.$char_data[UNIT_FIELD_MAXPOWER1].'</div>';
      $output .= '
                    </td>
                    <td class="half_line" colspan="2" align="center" width="50%">';
      if ($char['class'] == 1) // warrior
      {
        $output .= '
                      <div class="gradient_p" id="char_energy_name">'.lang('item', 'rage').':</div>
                      <div class="gradient_pp" id="char_energy_value">'.($char_data[UNIT_FIELD_POWER2]/10).'/'.($char_data[UNIT_FIELD_MAXPOWER2]/10).'</div>';
      }
      elseif ($char['class'] == 4) // rogue
      {
        $output .= '
                      <div class="gradient_p" id="char_energy_name">'.lang('item', 'energy').':</div>
                      <div class="gradient_pp" id="char_energy_value">'.$char_data[UNIT_FIELD_POWER4].'/'.$char_data[UNIT_FIELD_MAXPOWER4].'</div>';
      }
      elseif ($char['class'] == 6) // death knight
      {
        $output .= '
                      <div class="gradient_p" id="char_energy_name">'.lang('item', 'runic').':</div>
                      <div class="gradient_pp" id="char_energy_value">'.($char_data[UNIT_FIELD_POWER7]/10).'/'.($char_data[UNIT_FIELD_MAXPOWER7]/10).'</div>';
      }
      elseif ($char['class'] == 11) // druid
      {
        $output .= '
                      <div class="gradient_p" id="char_energy_name"">'.lang('item', 'mana').':</div>
                      <div class="gradient_pp" id="char_energy_value">'.$char_data[UNIT_FIELD_POWER1].'/'.$char_data[UNIT_FIELD_MAXPOWER1].'</div>
                      </br>
                      <div class="gradient_p" id="char_energy_name"">'.lang('item', 'rage').':</div>
                      <div class="gradient_pp" id="char_energy_value">'.($char_data[UNIT_FIELD_POWER2]/10).'/'.($char_data[UNIT_FIELD_MAXPOWER2]/10).'</div>';
      }
      elseif ($char['class'] == 2 || // paladin
              $char['class'] == 3 || // hunter
              $char['class'] == 5 || // priest
              $char['class'] == 7 || // shaman
              $char['class'] == 8 || // mage
              $char['class'] == 9)   // warlock
      {
        $output .= '
                      <div class="gradient_p" id="char_energy_name"">'.lang('item', 'mana').':</div>
                      <div class="gradient_pp" id="char_energy_value">'.$char_data[UNIT_FIELD_POWER1].'/'.$char_data[UNIT_FIELD_MAXPOWER1].'</div>';
      }
      $output .= '
                    </td>
                    <td width="6%">';
      if (($equiped_items[10][1]))
        $output .= '
                      <a id="char_icon_padding" href="'.$item_datasite.$EQU_GLOVES.'" target="_blank">
                        <img src="'.$equiped_items[10][1].'" class="'.$equiped_items[10][2].'" alt="Gloves" />
                      </a>';
      else
        $output .= '
                      <img src="img/INV/INV_empty_gloves.png" class="icon_border_0" alt="empty" />';
      $output .= '
                    </td>
                  </tr>
                  <tr>
                    <td width="1%">';
      if (($equiped_items[2][1]))
        $output .= '
                      <a id="char_icon_padding" href="'.$item_datasite.$EQU_NECK.'" target="_blank">
                        <img src="'.$equiped_items[2][1].'" class="'.$equiped_items[2][2].'" alt="Neck" />
                      </a>';
      else
        $output .= '
                      <img src="img/INV/INV_empty_neck.png" class="icon_border_0" alt="empty" />';
      $output .= '
                    </td>
                    <td class="half_line" colspan="2" rowspan="3" align="center" width="50%">
                      <div class="gradient_p">
                        '.lang('item', 'strength').':<br />
                        '.lang('item', 'agility').':<br />
                        '.lang('item', 'stamina').':<br />
                        '.lang('item', 'intellect').':<br />
                        '.lang('item', 'spirit').':<br />
                        '.lang('item', 'armor').':
                      </div>
                      <div class="gradient_pp">
                        '.$char_data[UNIT_FIELD_STAT0].'<br />
                        '.$char_data[UNIT_FIELD_STAT1].'<br />
                        '.$char_data[UNIT_FIELD_STAT2].'<br />
                        '.$char_data[UNIT_FIELD_STAT3].'<br />
                        '.$char_data[UNIT_FIELD_STAT4].'<br />
                        '.$char_data[UNIT_FIELD_RESISTANCES].'
                      </div>
                    </td>
                    <td class="half_line" colspan="2" rowspan="3" align="center" width="50%">
                      <div class="gradient_p">
                        '.lang('item', 'res_holy').':<br />
                        '.lang('item', 'res_arcane').':<br />
                        '.lang('item', 'res_fire').':<br />
                        '.lang('item', 'res_nature').':<br />
                        '.lang('item', 'res_frost').':<br />
                        '.lang('item', 'res_shadow').':
                      </div>
                      <div class="gradient_pp">
                        '.$char_data[UNIT_FIELD_RESISTANCES + 1].'<br />
                        '.$char_data[UNIT_FIELD_RESISTANCES + 2].'<br />
                        '.$char_data[UNIT_FIELD_RESISTANCES + 3].'<br />
                        '.$char_data[UNIT_FIELD_RESISTANCES + 4].'<br />
                        '.$char_data[UNIT_FIELD_RESISTANCES + 5].'<br />
                        '.$char_data[UNIT_FIELD_RESISTANCES + 6].'
                      </div>
                    </td>
                    <td width="1%">';
      if (($equiped_items[6][1]))
        $output .= '
                      <a id="char_icon_padding" href="'.$item_datasite.$EQU_BELT.'" target="_blank">
                        <img src="'.$equiped_items[6][1].'" class="'.$equiped_items[6][2].'" alt="Belt" />
                      </a>';
      else
        $output .= '
                      <img src="img/INV/INV_empty_waist.png" class="icon_border_0" alt="empty" />';
      $output .= '
                    </td>
                  </tr>
                  <tr>
                    <td width="1%">';
      if (($equiped_items[3][1]))
        $output .= '
                      <a id="char_icon_padding" href="'.$item_datasite.$EQU_SHOULDER.'" target="_blank">
                        <img src="'.$equiped_items[3][1].'" class="'.$equiped_items[3][2].'" alt="Shoulder" />
                      </a>';
      else
        $output .= '
                      <img src="img/INV/INV_empty_shoulder.png" class="icon_border_0" alt="empty" />';
      $output .= '
                    </td>
                    <td width="1%">';
      if (($equiped_items[7][1]))
        $output .= '
                      <a id="char_icon_padding" href="'.$item_datasite.$EQU_LEGS.'" target="_blank">
                        <img src="'.$equiped_items[7][1].'" class="'.$equiped_items[7][2].'" alt="Legs" />
                      </a>';
      else
        $output .= '
                      <img src="img/INV/INV_empty_legs.png" class="icon_border_0" alt="empty" />';
      $output .= '
                    </td>
                  </tr>
                  <tr>
                    <td width="1%">';
      if (($equiped_items[15][1]))
        $output .= '
                      <a id="char_icon_padding" href="'.$item_datasite.$EQU_BACK.'" target="_blank">
                        <img src="'.$equiped_items[15][1].'" class="'.$equiped_items[15][2].'" alt="Back" />
                      </a>';
      else
        $output .= '
                      <img src="img/INV/INV_empty_chest_back.png" class="icon_border_0" alt="empty" />';
      $output .= '
                    </td>
                    <td width="1%">';
      if (($equiped_items[8][1]))
        $output .= '
                      <a id="char_icon_padding" href="'.$item_datasite.$EQU_FEET.'" target="_blank">
                        <img src="'.$equiped_items[8][1].'" class="'.$equiped_items[8][2].'" alt="Feet" />
                      </a>';
      else
        $output .= '
                      <img src="img/INV/INV_empty_feet.png" class="icon_border_0" alt="empty" />';
      $output .= '
                    </td>
                  </tr>
                  <tr>
                    <td width="1%">';
      if (($equiped_items[5][1]))
        $output .= '
                      <a id="char_icon_padding" href="'.$item_datasite.$EQU_CHEST.'" target="_blank">
                        <img src="'.$equiped_items[5][1].'" class="'.$equiped_items[5][2].'" alt="Chest" />
                      </a>';
      else
        $output .= '
                      <img src="img/INV/INV_empty_chest_back.png" class="icon_border_0" alt="empty" />';
      $output .= '
                    </td>
                    <td class="half_line" colspan="2" rowspan="2" align="center" width="50%">
                      <div class="gradient_p" id="char_melee_name">
                        '.lang('char', 'melee_d').':<br />
                        '.lang('char', 'melee_ap').':<br />
                        '.lang('char', 'melee_hit').':<br />
                        '.lang('char', 'melee_crit').':<br />
                        '.lang('char', 'expertise').':<br />
                      </div>
                      <div class="gradient_pp" id="char_melee_value">
                        '.$mindamage.'-'.$maxdamage.'<br />
                        '.($char_data[UNIT_FIELD_ATTACK_POWER]+$char_data[UNIT_FIELD_ATTACK_POWER_MODS]).'<br />
                        '.$melee_hit.'<br />
                        '.$crit.'%<br />
                        '.$expertise.'<br />
                      </div>
                    </td>
                    <td class="half_line" colspan="2" rowspan="2" align="center" width="50%">
                      <div class="gradient_p">
                        '.lang('char', 'spell_d').':<br />
                        '.lang('char', 'spell_heal').':<br />
                        '.lang('char', 'spell_hit').':<br />
                        '.lang('char', 'spell_crit').':<br />
                        '.lang('char', 'spell_haste').'
                      </div>
                      <div class="gradient_pp">
                        '.$spell_damage.'<br />
                        '.$spell_heal.'<br />
                        '.$spell_hit.'<br />
                        '.$spell_crit.'%<br />
                        '.$spell_haste.'
                      </div>
                    </td>
                    <td width="1%">';
      if (($equiped_items[11][1]))
        $output .= '
                      <a id="char_icon_padding" href="'.$item_datasite.$EQU_FINGER1.'" target="_blank">
                        <img src="'.$equiped_items[11][1].'" class="'.$equiped_items[11][2].'" alt="Finger1" />
                      </a>';
      else
        $output .= '
                      <img src="img/INV/INV_empty_finger.png" class="icon_border_0" alt="empty" />';
      $output .= '
                    </td>
                  </tr>
                  <tr>
                    <td width="1%">';
      if (($equiped_items[4][1]))
        $output .= '
                      <a id="char_icon_padding" href="'.$item_datasite.$EQU_SHIRT.'" target="_blank">
                        <img src="'.$equiped_items[4][1].'" class="'.$equiped_items[4][2].'" alt="Shirt" />
                      </a>';
      else
        $output .= '
                      <img src="img/INV/INV_empty_shirt.png" class="icon_border_0" alt="empty" />';
      $output .= '
                    </td>
                    <td width="1%">';
      if (($equiped_items[12][1]))
        $output .= '
                      <a id="char_icon_padding" href="'.$item_datasite.$EQU_FINGER2.'" target="_blank">
                        <img src="'.$equiped_items[12][1].'" class="'.$equiped_items[12][2].'" alt="Finger2" />
                      </a>';
      else $output .= '
                      <img src="img/INV/INV_empty_finger.png" class="icon_border_0" alt="empty" />';
      $output .= '
                    </td>
                  </tr>
                  <tr>
                    <td width="1%">';
      if (($equiped_items[19][1]))
        $output .= '
                      <a id="char_icon_padding" href="'.$item_datasite.$EQU_TABARD.'" target="_blank">
                        <img src="'.$equiped_items[19][1].'" class="'.$equiped_items[19][2].'" alt="Tabard" />
                      </a>';
      else $output .= '
                      <img src="img/INV/INV_empty_tabard.png" class="icon_border_0" alt="empty" />';
      $output .= '
                    </td>
                    <td class="half_line" colspan="2" rowspan="2" align="center" width="50%">
                      <div class="gradient_p">
                        '.lang('char', 'dodge').':<br />
                        '.lang('char', 'parry').':<br />
                        '.lang('char', 'block').':
                      </div>
                      <div class="gradient_pp">
                        '.$dodge.'%<br />
                        '.$parry.'%<br />
                        '.$block.'%
                      </div>
                    </td>
                    <td class="half_line" colspan="2" rowspan="2" align="center" width="50%">
                      <div class="gradient_p" id="char_ranged_name">
                        '.lang('char', 'ranged_d').':<br />
                        '.lang('char', 'ranged_ap').':<br />
                        '.lang('char', 'ranged_hit').':<br />
                        '.lang('char', 'ranged_crit').':<br />
                      </div>
                      <div class="gradient_pp" id="char_ranged_value">
                        '.$minrangeddamage.'-'.$maxrangeddamage.'<br />
                        '.($char_data[UNIT_FIELD_RANGED_ATTACK_POWER]+$char_data[UNIT_FIELD_RANGED_ATTACK_POWER_MODS]).'<br />
                        '.$ranged_hit.'<br />
                        '.$ranged_crit.'%<br />
                      </div>
                    </td>
                    <td width="1%">';
      if (($equiped_items[13][1]))
        $output .= '
                      <a id="char_icon_padding" href="'.$item_datasite.$EQU_TRINKET1.'" target="_blank">
                        <img src="'.$equiped_items[13][1].'" class="'.$equiped_items[13][2].'" alt="Trinket1" />
                      </a>';
      else
        $output .= '
                      <img src="img/INV/INV_empty_trinket.png" class="icon_border_0" alt="empty" />';
      $output .= '
                    </td>
                  </tr>
                  <tr>
                    <td width="1%">';
      if (($equiped_items[9][1]))
        $output .= '
                      <a id="char_icon_padding" href="'.$item_datasite.$EQU_WRIST.'" target="_blank">
                        <img src="'.$equiped_items[9][1].'" class="'.$equiped_items[9][2].'" alt="Wrist" />
                      </a>';
      else
        $output .= '
                      <img src="img/INV/INV_empty_wrist.png" class="icon_border_0" alt="empty" />';
      $output .= '
                    </td>
                    <td width="1%">';
      if (($equiped_items[14][1]))
        $output .= '
                      <a id="char_icon_padding" href="'.$item_datasite.$EQU_TRINKET2.'" target="_blank">
                        <img src="'.$equiped_items[14][1].'" class="'.$equiped_items[14][2].'" alt="Trinket2" />
                      </a>';
      else
        $output .= '
                      <img src="img/INV/INV_empty_trinket.png" class="icon_border_0" alt="empty" />';
      $output .= '
                    </td>
                  </tr>
                  <tr>
                    <td></td>
                    <td width="15%">';
      if (($equiped_items[16][1]))
        $output .= '
                      <a id="char_icon_padding" href="'.$item_datasite.$EQU_MAIN_HAND.'" target="_blank">
                        <img src="'.$equiped_items[16][1].'" class="'.$equiped_items[16][2].'" alt="MainHand" />
                      </a>';
      else
        $output .= '
                      <img src="img/INV/INV_empty_main_hand.png" class="icon_border_0" alt="empty" />';
      $output .= '
                    </td>
                    <td width="15%">';
      if (($equiped_items[17][1]))
        $output .= '
                      <a id="char_icon_padding" href="'.$item_datasite.$EQU_OFF_HAND.'" target="_blank">
                        <img src="'.$equiped_items[17][1].'" class="'.$equiped_items[17][2].'" alt="OffHand" />
                      </a>';
      else
        $output .= '
                      <img src="img/INV/INV_empty_off_hand.png" class="icon_border_0" alt="empty" />';
      $output .= '
                    </td>
                    <td width="15%">';
      if (($equiped_items[18][1]))
        $output .= '
                      <a id="char_icon_padding" href="'.$item_datasite.$EQU_RANGED.'" target="_blank">
                        <img src="'.$equiped_items[18][1].'" class="'.$equiped_items[18][2].'" alt="Ranged" />
                      </a>';
      else
        $output .= '
                      <img src="img/INV/INV_empty_ranged.png" class="icon_border_0" alt="empty" />';
      $output .= '
                    </td>
                    <td width="15%"></td>
                    <td></td>
                  </tr>';
      if (($user_lvl > $owner_gmlvl)||($owner_name === $user_name)||($user_lvl == gmlevel('4')))
      {
        //total time played
        $tot_time = $char['totaltime'];
        $tot_days = (int)($tot_time/86400);
        $tot_time = $tot_time - ($tot_days*86400);
        $total_hours = (int)($tot_time/3600);
        $tot_time = $tot_time - ($total_hours*3600);
        $total_min = (int)($tot_time/60);
      
        $time_offset = $timezone * 3600;
      
        if ( $char['timestamp'] <> 0 )
          $lastseen = date("F j, Y @ Hi", $char['timestamp'] + $time_offset);
        else
          $lastseen = '-';

        $output .= '
                  <tr>
                    <td colspan="6">
                      '.lang('char', 'tot_play_time').': '.$tot_days.' '.lang('char', 'days').' '.$total_hours.' '.lang('char', 'hours').' '.$total_min.' '.lang('char', 'min').'
                    </td>
                  </tr>';

        $output .= '
                  <tr>
                    <td colspan="6">
                      '.lang('char', 'lastseen').': '.$lastseen.'
                    </td>
                  </tr>';
      }
      $output .= '
                </table>
              </div>
              <br />
            </div>
            <br />
            <table class="hidden">
              <tr>
                <td>';
                  // button to user account page, user account page has own security
                  makebutton(lang('char', 'chars_acc'), 'user.php?action=edit_user&amp;acct='.$owner_acc_id.'', 130);
      $output .= '
                </td>
                <td>';

      // only higher level GM with delete access can edit character
      //  character edit allows removal of character items, so delete permission is needed
      if ( ($user_lvl > $owner_gmlvl) && ($user_lvl >= $action_permission['delete']) )
      {
                  //makebutton($lang_char['edit_button'], 'char_edit.php?id='.$id.'&amp;realm='.$realmid.'', 130);
        $output .= '
                </td>
                <td>';
      }
      // only higher level GM with delete access, or character owner can delete character
      if ( ( ($user_lvl > $owner_gmlvl) && ($user_lvl >= $action_permission['delete']) ) || ($owner_name === $user_name) )
      {
                  makebutton(lang('char', 'del_char'), 'char_list.php?action=del_char_form&amp;check%5B%5D='.$id.'" type="wrn', 130);
        $output .= '
                </td>
                <td>';
      }
      // only GM with update permission can send mail, mail can send items, so update permission is needed
      if ($user_lvl >= $action_permission['update'])
      {
                  makebutton(lang('char', 'send_mail'), 'mail.php?type=ingame_mail&amp;to='.$char['name'].'', 130);
        $output .= '
                </td>
                <td>';
      }
                  makebutton(lang('global', 'back'), 'javascript:window.history.back()" type="def', 130);
      $output .= '
                </td>
              </tr>
            </table>
            <br />
          </center>
          <!-- end of char.php -->';
    }
    else
      ;//error($lang_char['no_permission']);
  }
  else
    error(lang('char', 'no_char_found'));

}


//########################################################################################################################
// MAIN
//########################################################################################################################

//$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

//$lang_char = lang_char();

$output .= "
      <div class=\"bubble\">";

char_main();

//unset($action);
unset($action_permission);
//unset($lang_char);

require_once 'footer.php';


?>
