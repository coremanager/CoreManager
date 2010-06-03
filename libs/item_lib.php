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

//#############################################################################
//get item set name by its id

 function get_itemset_name($id)
{
  global $arcm_db, $sql;
  
  $itemset = $sql['dbc']->fetch_row($sql['dbc']->query("SELECT `ItemName` FROM `itemset` WHERE `ID`={$id} LIMIT 1"));
  return $itemset[0];
}


//#############################################################################
//generate item border from item_template.entry

function get_item_border($item_id)
{
  global $sql, $core;

  if($item_id)
  {
    if ( $core == 1 )
      $result = $sql['world']->query('SELECT quality FROM items WHERE entry = '.$item_id.'');
    else
      $result = $sql['world']->query('SELECT Quality AS quality FROM item_template WHERE entry = '.$item_id.'');
    $iborder = (1 == $sql['world']->num_rows($result)) ? $sql['world']->result($result, 0, 'quality'): 'Quality: '.$iborder.' Not Found' ;

    return 'icon_border_'.$iborder.'';
  }
  else
    return 'icon_border_0';
}


//#############################################################################
//get item name from item_template.entry

function get_item_name($item_id)
{
  global $world_db, $realm_id, $sql;

  if($item_id)
  {
    $deplang = get_lang_id();
    $result = $sql['world']->query('SELECT name1 FROM items WHERE entry = '.$item_id.'');
    $item_name = (1 == $sql['world']->num_rows($result)) ? $sql['world']->result($result, 0, 'name1') : 'ItemID: '.$item_id.' Not Found' ;

    return $item_name;
  }
  else
    return NULL;
}


//#############################################################################
//get item icon - if icon not exists in item_icons folder D/L it from web.

function get_item_icon($itemid)
{
  global $arcm_db, $world_db, $realm_id, $proxy_cfg, $get_icons_from_web, $item_icons,
         $sql, $core;

  if ( $core == 1 )
    $result = $sql['world']->query("SELECT `displayid` FROM `items` WHERE `entry` = $itemid LIMIT 1");
  else
    $result = $sql['world']->query("SELECT `displayid` FROM `item_template` WHERE `entry` = $itemid LIMIT 1");
  $displayid_record = $sql['world']->fetch_assoc($result);
  $displayid = $displayid_record['displayid'];

  $result = $sql['dbc']->query("SELECT `IconName` FROM itemdisplayinfo WHERE id = '".$displayid."'");
  $icon_fields = $sql['dbc']->fetch_assoc($result);
  return $item_icons."/".$icon_fields['IconName'].".png";
}


//#############################################################################
//generate item tooltip from item_template.entry

function get_item_tooltip($item_id)
{
  global $world_db, $realm_id, $language, $sql;

  if($item_id)
  {
    $deplang = get_lang_id();
    $result_1 = $sql['world']->query("SELECT stat_type1,stat_value1,stat_type2,
      stat_value2,stat_type3,stat_value3,stat_type4,stat_value4,stat_type5,
      stat_value5,stat_type6,stat_value6,stat_type7,stat_value7,stat_type8,
      stat_value8,stat_type9,stat_value9,stat_type10,stat_value10,armor,
      holy_res,fire_res,nature_res,frost_res,arcane_res,shadow_res,spellid_1,
      spellid_2,spellid_3,spellid_4,spellid_5,
      IFNULL(".($deplang<>0?"name_loc$deplang":"NULL").",name),class,subclass,
      Quality,RequiredLevel,dmg_min1,dmg_max1,dmg_type1,dmg_min2,dmg_max2,
      dmg_type2,delay,bonding,description,itemset,item_template.entry,
      InventoryType,ItemLevel,displayid,maxcount,spelltrigger_1,spelltrigger_2,
      spelltrigger_3,spelltrigger_4,spelltrigger_5,ContainerSlots,
      spellcharges_1,spellcharges_2,spellcharges_3,spellcharges_4,
      spellcharges_5,AllowableClass,socketColor_1,socketColor_2,socketColor_3,
      RandomProperty,RandomSuffix
      FROM item_template LEFT JOIN locales_item ON item_template.entry = locales_item.entry
      WHERE item_template.entry = '$item_id' LIMIT 1");
    if ($item = $sql['world']->fetch_row($result_1))
    {
      $tooltip = "";

      $itemname = htmlspecialchars($item[32]);
      switch ($item[35])
      {
        case 0: //Grey Poor
          $tooltip .= "<font color='#b2c2b9' class='large'>$itemname</font><br />";
          break;
        case 1: //White Common
          $tooltip .= "<font color='white' class='large'>$itemname</font><br />";
          break;
        case 2: //Green Uncommon
          $tooltip .= "<font color='#1eff00' class='large'>$itemname</font><br />";
          break;
        case 3: //Blue Rare
          $tooltip .= "<font color='#0070dd' class='large'>$itemname</font><br />";
          break;
        case 4: //Purple Epic
          $tooltip .= "<font color='#a335ee' class='large'>$itemname</font><br />";
          break;
        case 5: //Orange Legendary
          $tooltip .= "<font color='orange' class='large'>$itemname</font><br />";
          break;
        case 6: //Red Artifact
          $tooltip .= "<font color='red' class='large'>$itemname</font><br />";
          break;
        default:
      }

      $tooltip .= "<font color='white'>";

      switch ($item[53])
      {
        case 1: //Binds when Picked Up
          $tooltip .= lang('item', 'bop')."<br />";
          break;
        case 2: //Binds when Equipped
          $tooltip .= lang('item', 'boe')."<br />";
          break;
        case 3: //Binds when Used
          $tooltip .= lang('item', 'bou')."<br />";
          break;
        case 4: //Quest Item
          $tooltip .= lang('item', 'quest_item')."<br />";
          break;
        default:
      }

      if ($item[60]) $tooltip .= lang('item', 'unique')."<br />";

      $tooltip .= "<br />";

      switch ($item[57])
      {
        case 1:
          $tooltip .= lang('item', 'head')." - ";
          break;
        case 2:
          $tooltip .= lang('item', 'neck')." - ";
          break;
        case 3:
          $tooltip .= lang('item', 'shoulder')." - ";
          break;
        case 4:
          $tooltip .= lang('item', 'shirt')." - ";
          break;
        case 5:
          $tooltip .= lang('item', 'chest')." - ";
          break;
        case 6:
          $tooltip .= lang('item', 'belt')." - ";
          break;
        case 7:
          $tooltip .= lang('item', 'legs')." - ";
          break;
        case 8:
          $tooltip .= lang('item', 'feet')." - ";
          break;
        case 9:
          $tooltip .= lang('item', 'wrist')." - ";
          break;
        case 10:
          $tooltip .= lang('item', 'gloves')." - ";
          break;
        case 11:
          $tooltip .= lang('item', 'finger')." - ";
          break;
        case 12:
          $tooltip .= lang('item', 'trinket')." - ";
          break;
        case 13:
          $tooltip .= lang('item', 'one_hand')." - ";
          break;
        case 14:
          $tooltip .= lang('item', 'off_hand')." - ";
          break;
        case 16:
          $tooltip .= lang('item', 'back')." - ";
          break;
        case 18:
          $tooltip .= lang('item', 'bag')."";
          break;
        case 19:
          $tooltip .= lang('item', 'tabard')." - ";
          break;
        case 20:
          $tooltip .= lang('item', 'robe')." - ";
          break;
        case 21:
          $tooltip .= lang('item', 'main_hand')." - ";
          break;
        case 23:
          $tooltip .= lang('item', 'tome')." - ";
          break;
        default:
      }
      switch ($item[33])
      {
        case 0: //Consumable
          $tooltip .= lang('item', 'consumable')."<br />";
          break;
        case 2: //Weapon
          switch ($item[34])
          {
            case 0:
              $tooltip .= lang('item', 'axe_1h')."<br />";
              break;
            case 1:
              $tooltip .= lang('item', 'axe_2h')."<br />";
              break;
            case 2:
              $tooltip .= lang('item', 'bow')."<br />";
              break;
            case 3:
              $tooltip .= lang('item', 'rifle')."<br />";
              break;
            case 4:
              $tooltip .= lang('item', 'mace_1h')."<br />";
              break;
            case 5:
              $tooltip .= lang('item', 'mace_2h')."<br />";
              break;
            case 6:
              $tooltip .= lang('item', 'polearm')."<br />";
              break;
            case 7:
              $tooltip .= lang('item', 'sword_1h')."<br />";
              break;
            case 8:
              $tooltip .= lang('item', 'sword_2h')."<br />";
              break;
            case 10:
              $tooltip .= lang('item', 'staff')."<br />";
              break;
            case 11:
              $tooltip .= lang('item', 'exotic_1h')."<br />";
              break;
            case 12:
              $tooltip .= lang('item', 'exotic_2h')."<br />";
              break;
            case 13:
              $tooltip .= lang('item', 'fist_weapon')."<br />";
              break;
            case 14:
              $tooltip .= lang('item', 'misc_weapon')."<br />";
              break;
            case 15:
              $tooltip .= lang('item', 'dagger')."<br />";
              break;
            case 16:
              $tooltip .= lang('item', 'thrown')."<br />";
              break;
            case 17:
              $tooltip .= lang('item', 'spear')."<br />";
              break;
            case 18:
              $tooltip .= lang('item', 'crossbow')."<br />";
              break;
            case 19:
              $tooltip .= lang('item', 'wand')."<br />";
              break;
            case 20:
              $tooltip .= lang('item', 'fishing_pole')."<br />";
              break;
            default:
          }
          break;
        case 4: //Armor
          switch ($item[34])
          {
            case 0:
              $tooltip .= lang('item', 'misc')."<br />";
              break;
            case 1:
              $tooltip .= lang('item', 'cloth')."<br />";
              break;
            case 2:
              $tooltip .= lang('item', 'leather')."<br />";
              break;
            case 3:
              $tooltip .= lang('item', 'mail')."<br />";
              break;
            case 4:
              $tooltip .= lang('item', 'plate')."<br />";
              break;
            case 6:
              $tooltip .= lang('item', 'shield')."<br />";
              break;
            default:
          }
          break;
        case 6: //Projectile
          switch ($item[34])
          {
            case 2:
              $tooltip .= lang('item', 'arrows')."<br />";
              break;
            case 3:
              $tooltip .= lang('item', 'bullets')."<br />";
              break;
            default:
          }
          break;
        case 7: //Trade Goods
          switch ($item[34])
          {
            case 0:
              $tooltip .= lang('item', 'trade_goods')."<br />";
              break;
            case 1:
              $tooltip .= lang('item', 'parts')."<br />";
              break;
            case 2:
              $tooltip .= lang('item', 'explosives')."<br />";
              break;
            case 3:
              $tooltip .= lang('item', 'devices')."<br />";
              break;
            default:
          }
          break;
        case 9: //Recipe
          switch ($item[34])
          {
            case 0:
              $tooltip .= lang('item', 'book')."<br />";
              break;
            case 1:
              $tooltip .= lang('item', 'LW_pattern')."<br />";
              break;
            case 2:
              $tooltip .= lang('item', 'tailoring_pattern')."<br />";
              break;
            case 3:
              $tooltip .= lang('item', 'ENG_Schematic')."<br />";
              break;
            case 4:
              $tooltip .= lang('item', 'BS_plans')."<br />";
              break;
            case 5:
              $tooltip .= lang('item', 'cooking_recipe')."<br />";
              break;
            case 6:
              $tooltip .= lang('item', 'alchemy_recipe')."<br />";
              break;
            case 7:
              $tooltip .= lang('item', 'FA_manual')."<br />";
              break;
            case 8:
              $tooltip .= lang('item', 'ench_formula')."<br />";
              break;
            case 9:
              $tooltip .= lang('item', 'JC_formula')."<br />";
              break;
            default:
          }
          break;
        case 11: //Quiver
          switch ($item[34])
          {
            case 2:
              $tooltip .= " ".lang('item', 'quiver')."<br />";
              break;
            case 3:
              $tooltip .= " ".lang('item', 'ammo_pouch')."<br />";
              break;
            default:
          }
          break;
        case 12: //Quest
          if ($item[53] != 4)
            $tooltip .= lang('item', 'quest_item')."<br />";
          break;
        case 13: //key
          switch ($item[34])
          {
            case 0:
              $tooltip .= lang('item', 'key')."<br />";
              break;
            case 1:
              $tooltip .= lang('item', 'lockpick')."<br />";
              break;
            default:
          }
          break;
        default:
      }
      $tooltip .= "$item[20] ".lang('item', 'armor')."<br />";

      for($f=37;$f<=51;$f+=3)
      {
        $dmg_type = $item[$f+2];
        $min_dmg_value = $item[$f];
        $max_dmg_value = $item[$f+1];

        if ($min_dmg_value && $max_dmg_value)
        {
          switch ($dmg_type)
          {
            case 0: // Physical
              $tooltip .= "$min_dmg_value - $max_dmg_value ".lang('item', 'damage')."<br />(".($item[52] ? round(((($min_dmg_value+$max_dmg_value)/2)/($item[52]/1000)),2): $min_dmg_value)." DPS)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".lang('item', 'speed')." : ".(($item[52])/1000)."<br />";
              break;
            case 1: // Holy
              $tooltip .= "$min_dmg_value - $max_dmg_value ".lang('item', 'holy_dmg')."<br />";
              break;
            case 2: // Fire
              $tooltip .= "$min_dmg_value - $max_dmg_value ".lang('item', 'fire_dmg')."<br />";
              break;
            case 3: // Nature
              $tooltip .= "$min_dmg_value - $max_dmg_value ".lang('item', 'nature_dmg')."<br />";
              break;
            case 4: // Frost
              $tooltip .= "$min_dmg_value - $max_dmg_value ".lang('item', 'frost_dmg')."<br />";
              break;
            case 5: // Shadow
              $tooltip .= "$min_dmg_value - $max_dmg_value ".lang('item', 'shadow_dmg')."<br />";
              break;
            case 6: // Arcane
              $tooltip .= "$min_dmg_value - $max_dmg_value ".lang('item', 'arcane_dmg')."<br />";
              break;
            default:
          }
        }
      }

      //basic status
      for($s=0;$s<=18;$s+=2)
      {
        $stat_value = $item[$s+1];
        if ($item[$s] && $stat_value)
        {
          switch ($item[$s])
          {
            case 1:
              $tooltip .= "+$stat_value ".lang('item', 'health')."<br />";
              break;
            case 2:
              $tooltip .= "+$stat_value ".lang('item', 'mana')."<br />";
              break;
            case 3:
              $tooltip .= "+$stat_value ".lang('item', 'agility')."<br />";
              break;
            case 4:
              $tooltip .= "+$stat_value ".lang('item', 'strength')."<br />";
              break;
            case 5:
              $tooltip .= "+$stat_value ".lang('item', 'intellect')."<br />";
              break;
            case 6:
              $tooltip .= "+$stat_value ".lang('item', 'spirit')."<br />";
              break;
            case 7:
              $tooltip .= "+$stat_value ".lang('item', 'stamina')."<br />";
              break;
            default:
              $flag_rating = 1;
          }
        }
      }

      if ($item[21]) $tooltip .= "$item[21] ".lang('item', 'res_holy')."<br />";
      if ($item[25]) $tooltip .= "$item[25] ".lang('item', 'res_arcane')."<br />";
      if ($item[22]) $tooltip .= "$item[22] ".lang('item', 'res_fire')."<br />";
      if ($item[23]) $tooltip .= "$item[23] ".lang('item', 'res_nature')."<br />";
      if ($item[24]) $tooltip .= "$item[24] ".lang('item', 'res_frost')."<br />";
      if ($item[26]) $tooltip .= "$item[26] ".lang('item', 'res_shadow')."<br />";

      //sockets
      for($p=72;$p<=74;$p++)
      {
        if($item[$p])
        {
          switch ($item[$p])
          {
            case 1:
              $tooltip .= "<img src='img/socket_meta.gif' alt='' /><font color='gray'> ".lang('item', 'socket_meta')."</font><br />";
              break;
            case 2:
              $tooltip .= "<img src='img/socket_red.gif' alt='' /><font color='red'> ".lang('item', 'socket_red')."</font><br />";
              break;
            case 4:
              $tooltip .= "<img src='img/socket_yellow.gif' alt='' /><font color='yellow'> ".lang('item', 'socket_yellow')."</font><br />";
              break;
            case 8:
              $tooltip .= "<img src='img/socket_blue.gif' alt='' /><font color='blue'> ".lang('item', 'socket_blue')."</font><br />";
              break;
            default:
          }
        }
      }

      //level requierment
      if($item[36])
        $tooltip .= lang('item', 'lvl_req')." $item[36]<br />";

      //allowable classes
      if (($item[71])&&($item[71] != -1)&&($item[71] != 1503))
      {
        $tooltip .= lang('item', 'class').":";
        if ($item[71] & 1) $tooltip .= " ".lang('id_tab', 'warrior')." ";
        if ($item[71] & 2) $tooltip .= " ".lang('id_tab', 'paladin')." ";
        if ($item[71] & 4) $tooltip .= " ".lang('id_tab', 'hunter')." ";
        if ($item[71] & 8) $tooltip .= " ".lang('id_tab', 'rogue')." ";
        if ($item[71] & 16) $tooltip .= " ".lang('id_tab', 'priest')." ";
        if ($item[71] & 64) $tooltip .= " ".lang('id_tab', 'shaman')." ";
        if ($item[71] & 128) $tooltip .= " ".lang('id_tab', 'mage')." ";
        if ($item[71] & 256) $tooltip .= " ".lang('id_tab', 'warlock')." ";
        if ($item[71] & 1024) $tooltip .= " ".lang('id_tab', 'druid')." ";
        $tooltip .= "<br />";
      }

      //number of bag slots
      if ($item[66])
        $tooltip .= " $item[66] ".lang('item', 'slots')."<br />";

      $tooltip .= "</font><br /><font color='#1eff00'>";
      //random enchantments
      if ($item[75] || $item[76])
        $tooltip .= "&lt; Random enchantment &gt;<br />";

      //Ratings additions.
      if (isset($flag_rating))
      {
        for($s=0;$s<=18;$s+=2)
        {
          $stat_type = $item[$s];
          $stat_value = $item[$s+1];
          if ($stat_type && $stat_value)
          {
            switch ($stat_type)
            {
              case 12:
                $tooltip .= lang('item', 'spell_equip').": ".lang('item', 'improves')." ".lang('item', 'DEFENCE_RATING')." ".lang('item', 'rating_by')." $stat_value.<br />";
                break;
              case 13:
                $tooltip .= lang('item', 'spell_equip').": ".lang('item', 'improves')." ".lang('item', 'DODGE_RATING')." ".lang('item', 'rating_by')." $stat_value.<br />";
                break;
              case 14:
                $tooltip .= lang('item', 'spell_equip').": ".lang('item', 'improves')." ".lang('item', 'PARRY_RATING')." ".lang('item', 'rating_by')." $stat_value.<br />";
                break;
              case 15:
                $tooltip .= lang('item', 'spell_equip').": ".lang('item', 'improves')." ".lang('item', 'SHIELD_BLOCK_RATING')." ".lang('item', 'rating_by')." $stat_value.<br />";
                break;
              case 16:
                $tooltip .= lang('item', 'spell_equip').": ".lang('item', 'improves')." ".lang('item', 'MELEE_HIT_RATING')." ".lang('item', 'rating_by')." $stat_value.<br />";
                break;
              case 17:
                $tooltip .= lang('item', 'spell_equip').": ".lang('item', 'improves')." ".lang('item', 'RANGED_HIT_RATING')." ".lang('item', 'rating_by')." $stat_value.<br />";
                break;
              case 18:
                $tooltip .= lang('item', 'spell_equip').": ".lang('item', 'improves')." ".lang('item', 'SPELL_HIT_RATING')." ".lang('item', 'rating_by')." $stat_value.<br />";
                break;
              case 19:
                $tooltip .= lang('item', 'spell_equip').": ".lang('item', 'improves')." ".lang('item', 'MELEE_CS_RATING')." ".lang('item', 'rating_by')." $stat_value.<br />";
                break;
              case 20:
                $tooltip .= lang('item', 'spell_equip').": ".lang('item', 'improves')." ".lang('item', 'RANGED_CS_RATING')." ".lang('item', 'rating_by')." $stat_value.<br />";
                break;
              case 21:
                $tooltip .= lang('item', 'spell_equip').": ".lang('item', 'improves')." ".lang('item', 'SPELL_CS_RATING')." ".lang('item', 'rating_by')." $stat_value.<br />";
                break;
              case 22:
                $tooltip .= lang('item', 'spell_equip').": ".lang('item', 'improves')." ".lang('item', 'MELEE_HA_RATING')." ".lang('item', 'rating_by')." $stat_value.<br />";
                break;
              case 23:
                $tooltip .= lang('item', 'spell_equip').": ".lang('item', 'improves')." ".lang('item', 'RANGED_HA_RATING')." ".lang('item', 'rating_by')." $stat_value.<br />";
                break;
              case 24:
                $tooltip .= lang('item', 'spell_equip').": ".lang('item', 'improves')." ".lang('item', 'SPELL_HA_RATING')." ".lang('item', 'rating_by')." $stat_value.<br />";
                break;
              case 25:
                $tooltip .= lang('item', 'spell_equip').": ".lang('item', 'improves')." ".lang('item', 'MELEE_CA_RATING')." ".lang('item', 'rating_by')." $stat_value.<br />";
                break;
              case 26:
                $tooltip .= lang('item', 'spell_equip').": ".lang('item', 'improves')." ".lang('item', 'RANGED_CA_RATING')." ".lang('item', 'rating_by')." $stat_value.<br />";
                break;
              case 27:
                $tooltip .= lang('item', 'spell_equip').": ".lang('item', 'improves')." ".lang('item', 'SPELL_CA_RATING')." ".lang('item', 'rating_by')." $stat_value.<br />";
                break;
              case 28:
                $tooltip .= lang('item', 'spell_equip').": ".lang('item', 'improves')." ".lang('item', 'MELEE_HASTE_RATING')." ".lang('item', 'rating_by')." $stat_value.<br />";
                break;
              case 29:
                $tooltip .= lang('item', 'spell_equip').": ".lang('item', 'improves')." ".lang('item', 'RANGED_HASTE_RATING')." ".lang('item', 'rating_by')." $stat_value.<br />";
                break;
              case 30:
                $tooltip .= lang('item', 'spell_equip').": ".lang('item', 'improves')." ".lang('item', 'SPELL_HASTE_RATING')." ".lang('item', 'rating_by')." $stat_value.<br />";
                break;
              case 31:
                $tooltip .= lang('item', 'spell_equip').": ".lang('item', 'improves')." ".lang('item', 'HIT_RATING')." ".lang('item', 'rating_by')." $stat_value.<br />";
                break;
              case 32:
                $tooltip .= lang('item', 'spell_equip').": ".lang('item', 'improves')." ".lang('item', 'CS_RATING')." ".lang('item', 'rating_by')." $stat_value.<br />";
                break;
              case 33:
                $tooltip .= lang('item', 'spell_equip').": ".lang('item', 'improves')." ".lang('item', 'HA_RATING')." ".lang('item', 'rating_by')." $stat_value.<br />";
                break;
              case 34:
                $tooltip .= lang('item', 'spell_equip').": ".lang('item', 'improves')." ".lang('item', 'CA_RATING')." ".lang('item', 'rating_by')." $stat_value.<br />";
                break;
              case 35:
                $tooltip .= lang('item', 'spell_equip').": ".lang('item', 'improves')." ".lang('item', 'RESILIENCE_RATING')." ".lang('item', 'rating_by')." $stat_value.<br />";
                break;
              case 36:
                $tooltip .= lang('item', 'spell_equip').": ".lang('item', 'improves')." ".lang('item', 'HASTE_RATING')." ".lang('item', 'rating_by')." $stat_value.<br />";
                break;
              default:
            }
          }
        }
      }
      //add equip spellid to status
      for($s1=27;$s1<=31;$s1++)
      {
        if ($item[$s1])
        {
          switch ($item[$s1+34])
          {
            case 0:
              $tooltip .= lang('item', 'spell_use').": ";
              break;
            case 1:
              $tooltip .= lang('item', 'spell_equip').": ";
              break;
            case 2:
              $tooltip .= lang('item', 'spell_coh').": ";
              break;
            default:
          }
          $tooltip .= " $item[$s1]<br />";
          if ($item[$s1])
          {
            if ($item[$s1+40])
              $tooltip.= abs($item[$s1+40])." ".lang('item', 'charges').".<br />";
          }
        }
      }

      $tooltip .= "</font>";

      if ($item[55])
      {
        include_once("id_tab.php");
        $tooltip .= "<br /><font color='orange'>".lang('item', 'item_set')." : ".get_itemset_name($item[55])." ($item[55])</font>";
      }
      if ($item[54])
        $tooltip .= "<br /><font color='orange'>''".str_replace("\"", " '", $item[54])."'</font>";

    }
    else
      $tooltip = "Item ID: $item_id Not Found" ;

    return $tooltip;
  }
  else
    return(NULL);
}


?>
