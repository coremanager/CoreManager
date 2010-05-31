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

require_once("header.php");
include_once("libs/get_lib.php");
valid_login($action_permission['view']);


// return npcflag
function get_npcflag($flag)
{
  $temp = "";
  if ($flag & 1) $temp .= " ".lang('creature', 'gossip')." ";
  if ($flag & 2) $temp .= " ".lang('creature', 'quest_giver')." ";
  if ($flag & 16) $temp .= " ".lang('creature', 'trainer')." ";
  if ($flag & 128) $temp .= " ".lang('creature', 'vendor')." ";
  if ($flag & 4096) $temp .= " ".lang('creature', 'armorer')." ";
  if ($flag & 8192) $temp .= " ".lang('creature', 'taxi')." ";
  if ($flag & 16384) $temp .= " ".lang('creature', 'spirit_healer')." ";
  if ($flag & 65536) $temp .= " ".lang('creature', 'inn_keeper')." ";
  if ($flag & 131072) $temp .= " ".lang('creature', 'banker')." ";
  if ($flag & 262144) $temp .= " ".lang('creature', 'retitioner')." ";
  if ($flag & 524288) $temp .= " ".lang('creature', 'tabard_vendor')." ";
  if ($flag & 1048576) $temp .= " ".lang('creature', 'battlemaster')." ";
  if ($flag & 2097152) $temp .= " ".lang('creature', 'auctioneer')." ";
  if ($flag & 4194304) $temp .= " ".lang('creature', 'stable_master')." ";
  if ($flag & 268435456) $temp .= " ".lang('creature', 'guard')." ";

  if ($temp != "") return $temp;
    else return lang('creature', 'none');
}

$creature_type = Array(
  0 => array(0,lang('creature', 'normal')),
  1 => array(1,lang('creature', 'elite')),
  2 => array(2,lang('creature', 'rare_elite')),
  3 => array(3,lang('creature', 'world_boss')),
  4 => array(4,lang('creature', 'rare'))
);

function makeinfocell($text,$tooltip)
{
 return "<a href=\"#\" onmouseover=\"toolTip('".addslashes($tooltip)."','info_tooltip')\" onmouseout=\"toolTip()\">$text</a>";
}

//########################################################################################################################
//  PRINT  ITEM SEARCH FORM
//########################################################################################################################
function search()
{
  global $locales_search_option, $output, $world_db, $realm_id, $creature_type, $sqlw;

  include_once("./libs/language_select.php");

  // this_is_junk:  this is just a record count, we'll use names for this
  $result = $sqlw->query("SELECT count(*) FROM creature_names");
  $tot_items = $sqlw->result($result, 0);
  //$sqlw->close();
  //unset($sqlw);


  $output .= "
  <center>
    <fieldset class=\"full_frame\">
      <legend>".lang('creature', 'search_template')."</legend><br />
      <form action=\"creature.php?action=do_search&amp;error=2\" method=\"post\" name=\"form\">
        <table class=\"hidden\">
          <tr>
            <td>".lang('creature', 'entry').":</td>
            <td><input type=\"text\" size=\"10\" maxlength=\"11\" name=\"entry\" /></td>
            <td>".lang('creature', 'name').":</td>
            <td><input type=\"text\" size=\"25\" maxlength=\"50\" name=\"name\" /></td>
          </tr>
          <tr>
            <td>".lang('creature', 'level').":</td>
            <td><input type=\"text\" size=\"10\" maxlength=\"3\" name=\"level\" /></td>
            <td>".lang('creature', 'health').":</td>
            <td><input type=\"text\" size=\"10\" maxlength=\"5\" name=\"health\" /></td>
          </tr>
          <tr>
            <td>".lang('creature', 'faction_A').":</td>
            <td><input type=\"text\" size=\"10\" maxlength=\"4\" name=\"faction_A\" /></td>
            <td>".lang('creature', 'rank').":</td>
            <td>
              <select name=\"rank\">
                <option value=\"\">- ".lang('creature', 'select')." -</option>";
  foreach ($creature_type as $flag)
  {
    $output .= "
                <option value=\"{$flag[0]}\">{$flag[1]}</option>";
  }
    $output .= "
              </select>
            </td>

          </tr>
          <tr>
            <td>".lang('creature', 'type').":</td>
            <td>
              <select name=\"type\">
                <option value=\"\">- ".lang('creature', 'select')." -</option>
                <option value=\"0\">0 - ".lang('creature', 'other')."</option>
                <option value=\"1\">1 - ".lang('creature', 'beast')."</option>
                <option value=\"2\">2 - ".lang('creature', 'dragonkin')."</option>
                <option value=\"3\">3 - ".lang('creature', 'demon')."</option>
                <option value=\"4\">4 - ".lang('creature', 'elemental')."</option>
                <option value=\"5\">5 - ".lang('creature', 'giant')."</option>
                <option value=\"6\">6 - ".lang('creature', 'undead')."</option>
                <option value=\"7\">7 - ".lang('creature', 'humanoid')."</option>
                <option value=\"8\">8 - ".lang('creature', 'critter')."</option>
                <option value=\"9\">9 - ".lang('creature', 'mechanical')."</option>
              <option value=\"10\">10 - ".lang('creature', 'not_specified')."</option>
            </select>
          </td>
          <td>".lang('creature', 'npc_flag').":</td>
          <td>
            <select name=\"npcflag\">
              <option value=\"\">- ".lang('creature', 'select')." -</option>
              <option value=\"1\">".lang('creature', 'gossip')."</option>
              <option value=\"2\">".lang('creature', 'quest_giver')."</option>
              <option value=\"16\">".lang('creature', 'trainer')."</option>
              <option value=\"128\">".lang('creature', 'vendor')."</option>
              <option value=\"4096\">".lang('creature', 'armorer')."</option>
              <option value=\"8192\">".lang('creature', 'taxi')."</option>
              <option value=\"16384\">".lang('creature', 'spirit_healer')."</option>
              <option value=\"65536\">".lang('creature', 'inn_keeper')."</option>
              <option value=\"131072\">".lang('creature', 'banker')."</option>
              <option value=\"262144\">".lang('creature', 'retitioner')."</option>
              <option value=\"524288\">".lang('creature', 'tabard_vendor')."</option>
              <option value=\"1048576\">".lang('creature', 'battlemaster')."</option>
              <option value=\"2097152\">".lang('creature', 'auctioneer')."</option>
              <option value=\"4194304\">".lang('creature', 'stable_master')."</option>
              <option value=\"268435456\">".lang('creature', 'guard')."</option>
            </select>
          </td>
        </tr>
        <tr>
          <td>".lang('creature', 'family').":</td>
          <td>
            <select name=\"family\">
              <option value=\"\">- ".lang('creature', 'select')." -</option>
              <option value=\"0\">0 - ".lang('creature', 'other')."</option>
              <option value=\"1\">1 - ".lang('creature', 'wolf')."</option>
              <option value=\"2\">2 - ".lang('creature', 'cat')."</option>
              <option value=\"3\">3 - ".lang('creature', 'spider')."</option>
              <option value=\"4\">4 - ".lang('creature', 'bear')."</option>
              <option value=\"5\">5 - ".lang('creature', 'boar')."</option>
              <option value=\"6\">6 - ".lang('creature', 'crocolisk')."</option>
              <option value=\"7\">7 - ".lang('creature', 'carrion_bird')."</option>
              <option value=\"8\">8 - ".lang('creature', 'crab')."</option>
              <option value=\"9\">9 - ".lang('creature', 'gorilla')."</option>
              <option value=\"11\">11 - ".lang('creature', 'raptor')."</option>
              <option value=\"12\">12 - ".lang('creature', 'tallstrider')."</option>
              <option value=\"13\">13 - ".lang('creature', 'other')."</option>
              <option value=\"14\">14 - ".lang('creature', 'other')."</option>
              <option value=\"15\">15 - ".lang('creature', 'felhunter')."</option>
              <option value=\"16\">16 - ".lang('creature', 'voidwalker')."</option>
              <option value=\"17\">17 - ".lang('creature', 'succubus')."</option>
              <option value=\"18\">18 - ".lang('creature', 'other')."</option>
              <option value=\"19\">19 - ".lang('creature', 'doomguard')."</option>
              <option value=\"20\">20 - ".lang('creature', 'scorpid')."</option>
              <option value=\"21\">21 - ".lang('creature', 'turtle')."</option>
              <option value=\"22\">22 - ".lang('creature', 'scorpid')."</option>
              <option value=\"23\">23 - ".lang('creature', 'imp')."</option>
              <option value=\"24\">24 - ".lang('creature', 'bat')."</option>
              <option value=\"25\">25 - ".lang('creature', 'hyena')."</option>
              <option value=\"26\">26 - ".lang('creature', 'owl')."</option>
              <option value=\"27\">27 - ".lang('creature', 'wind_serpent')."</option>
            </select>
          </td>
          <td>".lang('creature', 'spell').":</td>
          <td>
            <input type=\"text\" size=\"10\" maxlength=\"11\" name=\"spell\" />
          </td>
        </tr>
        <tr>
          <td>".lang('creature', 'custom_search').":</td>
          <td colspan=\"2\">
            <input type=\"text\" size=\"25\" maxlength=\"50\" name=\"custom_search\" />
          </td>
          <td>&nbsp</td>
        </tr>
        <tr>
          <td>".lang('global', 'language_select').":</td>
          <td>".generate_language_selectbox()."
          </td>
          <td>&nbsp;</td>
          <td>";
    makebutton(lang('creature', 'search'), "javascript:do_submit()",150);
    $output .= "
          </td>
        </tr>
        <tr>
          <td colspan=\"4\"><hr></td>
        </tr>
        <tr>
          <td></td>
          <td colspan=\"2\">";
    makebutton(lang('creature', 'add_new'), "creature.php?action=add_new&error=3",200);
    $output .= "
          </td>
          <td colspan=\"2\">".lang('creature', 'tot_creature_templ').": $tot_items</td>
        </tr>
      </table>
    </form>
  </fieldset>
  <br />
  <br />
  </center>";
}


//########################################################################################################################
// SHOW SEARCH RESULTS
//########################################################################################################################
function do_search() {
 global $output, $world_db, $realm_id, $creature_datasite, $sql_search_limit,
    $creature_type, $creature_npcflag, $language, $sqlw;

$where = '';

// language // if $_POST['language'] > 0 also search locales_XXX
// prepare sql_query
if ($_POST['language'] != '0') {
  $loc_language  = (is_numeric($_POST['language']))  ? $sqlw->quote_smart($_POST['language'])  : redirect("creature.php?error=8");
}
else $loc_language = '0';

// check input and prepare sql query

if ($_POST['npcflag'] != '') {
  $npcflag = (is_numeric($_POST['npcflag'])) ? $sqlw->quote_smart($_POST['npcflag']) : redirect("creature.php?error=8");
  $where .= "cp.npcflags = '$npcflag' ";
}
else if ($_POST['type'] != '') {
  $type    = (is_numeric($_POST['type']))    ? $sqlw->quote_smart($_POST['type'])    : redirect("creature.php?error=8");
  $where .= "cn.type = '$type' ";

}
else if ($_POST['rank'] != '') {
  $rank    = (is_numeric($_POST['rank']))    ? $sqlw->quote_smart($_POST['rank'])    : redirect("creature.php?error=8");
  $where .= "cn.rank = '$rank' ";
}
else if  ($_POST['family'] != '') {
  $family  = (is_numeric($_POST['family']))  ? $sqlw->quote_smart($_POST['family'])  : redirect("creature.php?error=8");
  $where .= "cn.family = '$family' ";
}
else if ($_POST['entry'] != '') {
  $entry   = (is_numeric($_POST['entry']))   ? $sqlw->quote_smart($_POST['entry'])   : redirect("creature.php?error=8");
  $where .= "cn.entry = '$entry' ";
}
else if ($_POST['name'] != '') {
  $name    = (preg_match('/^[\t\v\b\f\a\n\r\\\"\? <>[](){}_=+-|!@#$%^&*~`.,\0]{1,30}$/', $_POST['name']))  ?  "test" : $sqlw->quote_smart($_POST['name']);

  if ($loc_language)
    $where .= "lc.name_loc{$loc_language} LIKE '%$name%' ";
  else
    $where .= "cn.`name`LIKE '%$name%' ";

}
else if ($_POST['level'] != '') {
  $level   = (is_numeric($_POST['level']))   ? $sqlw->quote_smart($_POST['level'])   : redirect("creature.php?error=8");
  $where .= "cp.minlevel <= $level AND cp.maxlevel >= $level ";
}
else if ($_POST['health'] != '') {
  $health  = (is_numeric($_POST['health']))  ? $sqlw->quote_smart($_POST['health'])  : redirect("creature.php?error=8");
  $where .= "cp.minhealth <= $health AND cp.maxhealth >= $health ";
}
else if ($_POST['faction_A'] != '') {
  $faction_A = (is_numeric($_POST['faction_A'])) ? $sqlw->quote_smart($_POST['faction_A']) : redirect("creature.php?error=8");
  $where .= "cp.faction = '$faction_A' ";
}
else if ($_POST['faction_H'] != '') {
  $faction_H = (is_numeric($_POST['faction_H'])) ? $sqlw->quote_smart($_POST['faction_H']) : redirect("creature.php?error=8");
  $where .= "cp.faction = '$faction_H' ";
}

else if ($_POST['spell'] != '') {
  $spell   = (is_numeric($_POST['spell']))   ? $sqlw->quote_smart($_POST['spell'])   : redirect("creature.php?error=8");
  $where .= "(cp.spell1 = '$spell' OR cp.spell2 = '$spell' OR cp.spell3 = '$spell' OR cp.spell4 = '$spell') ";
}

// this_is_junk: disabling this for now... ArcEmu handles loot differently
//else if ($_POST['lootid'] != '') {
//  $lootid  = (is_numeric($_POST['lootid']))  ? $sqlw->quote_smart($_POST['lootid'])  : redirect("creature.php?error=8");
//  $where .= "ct.lootid = '$lootid' ";
//}

// this_is_junk: ArcEmu handles scripting differently
//else if ($_POST['ScriptName'] != '') {
//  $ScriptName = (preg_match("/^[_[:alpha:]]{1,32}$/", $_POST['ScriptName'])) ? $sqlw->quote_smart($_POST['ScriptName']) : "mob_generic";
//  $where .= "ct.ScriptName LIKE '%$ScriptName%' ";
//}

// this_is_junk: I'm not sure what this is, ArcEmu doesn't have fields matching
//else if ($_POST['heroic'] != '') {
//  $heroic  = (is_numeric($_POST['heroic']))  ? $sqlw->quote_smart($_POST['heroic'])  : redirect("creature.php?error=8");
//  $where .= "ct.heroic_entry = '$heroic'";
//}

// additional search query
if ($_POST['custom_search'] != '') {
  $custom_search  = (preg_match('/^[\t\v\b\f\a\n\r\\\"\?[](){}=+-|!@#$%^&*~`.,\0]{1,30}$/', $_POST['$custom_search']))  ? 0 : $sqlw->quote_smart($_POST['$custom_search']);
  $where .= ($where == '') ? "ct.{$custom_search}" : "AND cn.{$custom_search}";
}


/* no search value, go home! */
if ($where == '') redirect("creature.php?error=1");

// this_is_junk: disabling localization
//if ($loc_language)
//  $db_query = "SELECT cn.entry, cn.name, ct.maxlevel, ct.maxhealth, cn.rank, cn.npcflag, lc.name_loc{$loc_language} FROM creature_names ct
//               LEFT OUTER JOIN creature_names_localized lc on lc.entry = ct.entry
//               LEFT JOIN creature_proto on creature_proto.entry = creature_names.entry
//               WHERE {$where} ORDER BY ct.entry LIMIT 100";
//else
  $db_query = "SELECT cn.entry, cn.name, cp.maxlevel, cp.maxhealth, cn.rank, cp.npcflags FROM creature_names cn
               LEFT JOIN creature_proto cp on cn.entry = cp.entry
               WHERE {$where} ORDER BY cn.entry LIMIT 100";


 $result = $sqlw->query($db_query);
 $total_found = $sqlw->num_rows($result);

  $output .= "<center>
  <table class=\"top_hidden\"></td>
       <tr><td>";
    makebutton(lang('creature', 'new_search'), "creature.php",160);
  $output .= "</td>
     <td align=\"right\">".lang('creature', 'tot_found')." : $total_found : ".lang('global', 'limit')." $sql_search_limit</td>
   </tr></table>";

  $output .= "<table class=\"lined\">
   <tr>
  <th>".lang('creature', 'entry')."</th>
  <th>".lang('creature', 'name')."</th>
  <th>".lang('creature', 'level')."</th>
  <th>".lang('creature', 'health')."</th>
  <th>".lang('creature', 'rank')."</th>
  <th>".lang('creature', 'npc_flag')."</th>
  </tr>";

 for ($i=1; $i<=$total_found; $i++){
  $creature = $sqlw->fetch_row($result);

  $output .= "<tr>
              <td><a href=\"$creature_datasite$creature[0]\" target=\"_blank\">$creature[0]</a></td>";

  if ($loc_language)
    $output .= "<td><a href=\"creature.php?action=edit&amp;entry=$creature[0]&amp;error=4\">".htmlentities($creature[6])." ( {$creature[1]} )</a></td>";
  else
    $output .= "<td><a href=\"creature.php?action=edit&amp;entry=$creature[0]&amp;error=4\">$creature[1]</a></td>";

  $output .= "<td>$creature[2]</td>
              <td>$creature[3]</td>
              <td>{$creature_type[$creature[4]][1]}</td>
              <td>".get_npcflag($creature[5])."</td>
           </tr>";
  }
  $output .= "</table></center><br />";

 //$sql->close();
 //unset($sql);
}


//########################################################################################################################
// EDIT CREATURE FORM
//########################################################################################################################
function do_insert_update($do_insert) {
 global $output, $world_db, $realm_id, $creature_datasite,$item_datasite, $quest_datasite,
    $spell_datasite, $language, $action_permission, $user_lvl, $locales_search_option, $arcm_db, $sqlw,
    $sqlm, $sqld;

  //wowhead_tt();

 require_once("./libs/get_lib.php");
 require_once 'libs/item_lib.php';


 // entry only needed on update
 if (!$do_insert) {
   if (!isset($_GET['entry']) ) redirect("creature.php?error=1");

   // this_is_junk: We're going to handle vendors and trainers separately
   //               Also, loot
   //               The data in my PetDefaultSpells doesn't completely match the data I have for MaNGOS,
   //               but the entries line up... so what ever.
   $entry = (is_numeric($_GET['entry']))   ? $sqlw->quote_smart($_GET['entry'])   : redirect("creature.php?error=8");
   $query = "SELECT cn.`entry`, cn.`KillCredit1`, cn.`KillCredit2`, cn.`male_displayid`, cn.`male_displayid2`,
             cn.`female_displayid`, cn.`female_displayid2`, cn.`name`, cn.`subname`, cn.`info_str`, cp.`minlevel`, 
             cp.`maxlevel`, cp.`minhealth`, cp.`maxhealth`,cp.`mana`, cp.`armor`, cp.`faction`, cp.`npcflags`,
             cp.`walk_speed`,cp.`scale`,cn.`rank`, cp.`mindamage`, cp.`maxdamage`, cp.`attacktime`,
             cp.`rangedattacktime`, cn.`family`, cp.`rangedmindamage`, cp.`rangedmaxdamage`, cn.`type`,
             cn.`flags1`, cp.`resistance1`, cp.`resistance2`, cp.`resistance3`, cp.`resistance4`,
             cp.`resistance5`, cp.`resistance6`, cp.`spell1`, cp.`spell2`, cp.`spell3`, cp.`spell4`, ps.`spell`, 
             cp.`boss`
             FROM creature_names cn
             LEFT JOIN creature_proto cp ON cn.entry = cp.entry
             LEFT JOIN petdefaultspells ps ON cn.entry = ps.entry
             WHERE cn.entry = '$entry'";
   $result = $sqlw->query($query);
 }
 else {

  // get new free id
  $result = $sqlw->query("SELECT max(entry)+1 as newentry from creature_proto");
  $entry  = $sqlw->result($result, 0, 'newentry');
  $result = $sqlw->query("SELECT $entry as `entry`, 0 as `heroic_entry`, 0 as `KillCredit1`, 0 as `KillCredit2`, 0 as `modelid_A`, 0 as `modelid_A2`, 0 as `modelid_H`, 0 as `modelid_H2`, 'new creature' as`name`,'' as `subname`, '' as `IconName`, 1 as `minlevel`, 1 as `maxlevel`, 1 as `minhealth`, 1 as `maxhealth`, 0 as `minmana`, 0 as `maxmana`, 0 as `armor`,0 as `faction_A`, 0 as `faction_H`, 0 as `npcflag`, 1 as `speed`, 1 as `scale`,0 as `rank`, 1 as `mindmg`, 1 as `maxdmg`, 0 as `dmgschool`, 0 as `attackpower`, 2000 as `baseattacktime`, 0 as `rangeattacktime`, 0 as `unit_flags`,0 as `dynamicflags`, 0 as `family`, 0 as `trainer_type`, 0 as `trainer_spell`, 0 as `trainer_class`,0 as `trainer_race`,0 as `minrangedmg`, 0 as `maxrangedmg`, 0 as `rangedattackpower`, 0 as `type`,0 as `type_flags`,0 as `lootid`, 0 as `pickpocketloot`, 0 as `skinloot`, 0 as `resistance1`, 0 as `resistance2`, 0 as `resistance3`, 0 as `resistance4`, 0 as `resistance5`, 0 as `resistance6`, 0 as`spell1`, 0 as`spell2`, 0 as `spell3`, 0 as `spell4`, 0 as `PetSpellDataId`, 100 as `mingold`, 250 as `maxgold`, '' as `AIName`, 0 as `MovementType`, 1 as `InhabitType`, 0 as `RacialLeader`, 1 as `RegenHealth`, 0 as `equipment_id`, 0 as `mechanic_immune_mask`, 0 as `flags_extra`, '' as `ScriptName`");
  // use id for new creature_template
 }



 if ($mob = $sqlw->fetch_assoc($result)){

  $output .= "<script type=\"text/javascript\" src=\"libs/js/tab.js\"></script>
   <center>
    <br /><br /><br />
    <form method=\"post\" action=\"creature.php?action=do_update\" name=\"form1\">
    <input type=\"hidden\" name=\"backup_op\" value=\"0\"/>
    <input type=\"hidden\" name=\"entry\" value=\"$entry\"/>
    <input type=\"hidden\" name=\"insert\" value=\"$do_insert\"/>

<div class=\"jtab-container\" id=\"container\">
  <ul class=\"jtabs\">
    <li><a href=\"#\" onclick=\"return showPane('pane1', this)\" id=\"tab1\">".lang('creature', 'general')."</a></li>
    <li><a href=\"#\" onclick=\"return showPane('pane3', this)\">".lang('creature', 'stats')."</a></li>
  <li><a href=\"#\" onclick=\"return showPane('pane4', this)\">".lang('creature', 'models')."</a></li>
  <li><a href=\"#\" onclick=\"return showPane('pane2', this)\">".lang('creature', 'additional')."</a></li>";

  $quest_flag = 0;
  $vendor_flag = 0;
  $trainer_flag = 0;

if (!$mob['npcflag']) $output .= "";
else{
  if ($mob['npcflag'] & 1) $output .= ""; //gossip
  if ($mob['npcflag'] & 2) {
    $quest_flag = 1;
    $output .= "<li><a href=\"#\" onclick=\"return showPane('pane6', this)\">".lang('creature', 'quests')."</a></li>";
  }
  if ($mob['npcflag'] & 4) {
    $vendor_flag = 1;
    $output .= "<li><a href=\"#\" onclick=\"return showPane('pane7', this)\">".lang('creature', 'vendor')."</a></li>";
  }
  if ($mob['npcflag'] & 16) {
    $trainer_flag = 1;
    $output .= "<li><a href=\"#\" onclick=\"return showPane('pane8', this)\">".lang('creature', 'trainer')."</a></li>";
    }
  }
  if ($mob['npcflag'] & 128) {
    $vendor_flag = 1;
    $output .= "<li><a href=\"#\" onclick=\"return showPane('pane7', this)\">".lang('creature', 'vendor')."</a></li>";
  }
  if ($mob['npcflag'] & 16384) {
    $vendor_flag = 1;
    $output .= "<li><a href=\"#\" onclick=\"return showPane('pane7', this)\">".lang('creature', 'vendor')."</a></li>";
  }
if ($mob['lootid']) {
  $output .= "<li><a href=\"#\" onclick=\"return showPane('pane5', this)\">".lang('creature', 'loot')."</a></li>";
}
if ($mob['skinloot']) {
  $output .= "<li><a href=\"#\" onclick=\"return showPane('pane9', this)\">".lang('creature', 'skin_loot')."</a></li>";
}
if ($mob['pickpocketloot']) {
  $output .= "<li><a href=\"#\" onclick=\"return showPane('pane10', this)\">".lang('creature', 'pickpocket_loot')."</a></li>";
}
  if ($locales_search_option != 0) $output .= "<li><a href=\"#\" onclick=\"return showPane('pane11', this)\">".lang('creature', 'locales')."</a></li>";

  $output .= "</ul>
              <div class=\"jtab-panes\">";

$output .= "<div id=\"pane1\">
    <br /><br />
<table class=\"lined\" id=\"ch_cre_proto\">
<tr class=\"large_bold\"><td colspan=\"6\" class=\"hidden\" align=\"left\">".lang('creature', 'general').":</td></tr>
<tr>
 <td>".makeinfocell(lang('creature', 'entry'),lang('creature', 'entry_desc'))."</td>
 <td><a href=\"$creature_datasite$entry\" target=\"_blank\">$entry</a></td>

 <td>".makeinfocell(lang('creature', 'name'),lang('creature', 'name_desc'))."</td>
 <td colspan=\"3\"><input type=\"text\" name=\"name\" size=\"50\" maxlength=\"100\" value=\"{$mob['name']}\" /></td>
 </tr>

 <tr>
 <td>".makeinfocell(lang('creature', 'sub_name'),lang('creature', 'sub_name_desc'))."</td>
 <td colspan=\"2\"><input type=\"text\" name=\"subname\" size=\"25\" maxlength=\"100\" value=\"{$mob['subname']}\" /></td>

 <td></td>
 <td colspan=\"2\"></td>
</tr>


<tr class=\"large_bold\"><td colspan=\"6\" class=\"hidden\" align=\"left\">".lang('creature', 'basic_status').":</td></tr>
<tr>

 <td>".makeinfocell(lang('creature', 'min_level'),lang('creature', 'min_level_desc'))."</td>
 <td><input type=\"text\" name=\"minlevel\" size=\"8\" maxlength=\"3\" value=\"{$mob['minlevel']}\" /></td>

 <td>".makeinfocell(lang('creature', 'max_level'),lang('creature', 'max_level_desc'))."</td>
 <td><input type=\"text\" name=\"maxlevel\" size=\"8\" maxlength=\"3\" value=\"{$mob['maxlevel']}\" /></td>

 <td></td>
 <td></td>
</tr>

<tr>
<td>".makeinfocell(lang('creature', 'min_health'),lang('creature', 'min_health_desc'))."</td>
 <td><input type=\"text\" name=\"minhealth\" size=\"14\" maxlength=\"10\" value=\"{$mob['minhealth']}\" /></td>

 <td>".makeinfocell(lang('creature', 'max_health'),lang('creature', 'max_health_desc'))."</td>
 <td><input type=\"text\" name=\"maxhealth\" size=\"14\" maxlength=\"10\" value=\"{$mob['maxhealth']}\" /></td>

 <td>".makeinfocell(lang('creature', 'min_mana'),lang('creature', 'min_mana_desc'))."</td>
 <td colspan=\"1\"><input type=\"text\" name=\"minmana\" size=\"14\" maxlength=\"10\" value=\"{$mob['mana']}\" /></td>

";
// this_is_junk:
$query = "SELECT faction FROM factiontemplate WHERE id = '".$mob['faction']."'";
$result = $sqld->query($query);
$faction_id = $sqld->fetch_assoc($result);
$query = "SELECT name FROM faction WHERE id = '".$faction_id['faction']."'";
$result = $sqld->query($query);
$faction_name = $sqld->fetch_assoc($result);

$output .= "</tr>
<tr>
 <td>".makeinfocell(lang('creature', 'faction_A'),lang('creature', 'faction_A_desc'))."</td>
 <td colspan=\"2\"><input type=\"text\" name=\"faction_A\" size=\"25\" maxlength=\"10\" value=\"".$faction_name['name']." (".$mob['faction'].")\" /></td>

 <td></td>
 <td colspan=\"2\"></td>
</tr>
<tr>";
 $rank = array(0 => "", 1 => "", 3 => "", 2 => "", 4 => "");
  $rank[$mob['rank']] = " selected=\"selected\" ";

 $output .= "<td >".makeinfocell(lang('creature', 'rank'),lang('creature', 'rank_desc'))."</td>
  <td><select name=\"rank\">
  <option value=\"0\" {$rank[0]}>0 - ".lang('creature', 'normal')."</option>
  <option value=\"1\" {$rank[1]}>1 - ".lang('creature', 'elite')."</option>
  <option value=\"2\" {$rank[2]}>2 - ".lang('creature', 'rare_elite')."</option>
  <option value=\"3\" {$rank[3]}>3 - ".lang('creature', 'world_boss')."</option>
  <option value=\"4\" {$rank[4]}>4 - ".lang('creature', 'rare')."</option>
  </select></td>";
 unset($rank);

 $type = array(0 => "", 1 => "", 2 => "", 3 => "", 4 => "", 5 => "", 6 => "", 7 => "", 8 => "", 9 => "", 10 => "");
 $type[$mob['type']] = " selected=\"selected\" ";

$output .= "<td colspan=\"1\">".makeinfocell(lang('creature', 'type'),lang('creature', 'type_desc'))."</td>
 <td colspan=\"2\"><select name=\"type\">
    <option value=\"0\" {$type[0]}>0 - ".lang('creature', 'other')."</option>
    <option value=\"1\" {$type[1]}>1 - ".lang('creature', 'beast')."</option>
    <option value=\"2\" {$type[2]}>2 - ".lang('creature', 'dragonkin')."</option>
    <option value=\"3\" {$type[3]}>3 - ".lang('creature', 'demon')."</option>
    <option value=\"4\" {$type[4]}>4 - ".lang('creature', 'elemental')."</option>
    <option value=\"5\" {$type[5]}>5 - ".lang('creature', 'giant')."</option>
    <option value=\"6\" {$type[6]}>6 - ".lang('creature', 'undead')."</option>
    <option value=\"7\" {$type[7]}>7 - ".lang('creature', 'humanoid')."</option>
    <option value=\"8\" {$type[8]}>8 - ".lang('creature', 'critter')."</option>
    <option value=\"9\" {$type[9]}>9 - ".lang('creature', 'mechanical')."</option>
    <option value=\"10\" {$type[10]}>10 - ".lang('creature', 'not_specified')."</option>
     </select></td><td></td>
</tr>
<tr>";
 unset($type);

$npcflag = array(0 => "", 1 => "", 2 => "", 4 => "", 8 => "", 16 => "", 32 => "", 64 => "", 128 => "",
 256 => "", 512 => "", 1024 => "", 2048 => "", 4096 => "", 8192 => "", 16384 => "", 65536 => "",
 131072 => "", 262144 => "", 524288 => "", 1048576 => "", 2097152 => "", 4194304 => "", 268435456 => "");

 if($mob['npcflag'] == 0) $npcflag[0] = " selected=\"selected\" ";
else {
  if ($mob['npcflag'] & 1) $npcflag[1] = " selected=\"selected\" ";
  if ($mob['npcflag'] & 2) $npcflag[2] = " selected=\"selected\" ";
  if ($mob['npcflag'] & 4) $npcflag[4] = " selected=\"selected\" ";
  if ($mob['npcflag'] & 8) $npcflag[8] = " selected=\"selected\" ";
  if ($mob['npcflag'] & 16) $npcflag[16] = " selected=\"selected\" ";
  if ($mob['npcflag'] & 32) $npcflag[32] = " selected=\"selected\" ";
  if ($mob['npcflag'] & 64) $npcflag[64] = " selected=\"selected\" ";
  if ($mob['npcflag'] & 128) $npcflag[128] = " selected=\"selected\" ";
  if ($mob['npcflag'] & 256) $npcflag[256] = " selected=\"selected\" ";
  if ($mob['npcflag'] & 512) $npcflag[512] = " selected=\"selected\" ";
  if ($mob['npcflag'] & 1024) $npcflag[1024] = " selected=\"selected\" ";
  if ($mob['npcflag'] & 2048) $npcflag[2048] = " selected=\"selected\" ";
  if ($mob['npcflag'] & 4096) $npcflag[4096] = " selected=\"selected\" ";
  if ($mob['npcflag'] & 8192) $npcflag[8192] = " selected=\"selected\" ";
  if ($mob['npcflag'] & 16384) $npcflag[16384] = " selected=\"selected\" ";
  if ($mob['npcflag'] & 65536) $npcflag[65536] = " selected=\"selected\" ";
  if ($mob['npcflag'] & 131072) $npcflag[131072] = " selected=\"selected\" ";
  if ($mob['npcflag'] & 262144) $npcflag[262144] = " selected=\"selected\" ";
  if ($mob['npcflag'] & 524288) $npcflag[524288] = " selected=\"selected\" ";
  if ($mob['npcflag'] & 1048576) $npcflag[1048576] = " selected=\"selected\" ";
  if ($mob['npcflag'] & 2097152) $npcflag[2097152] = " selected=\"selected\" ";
  if ($mob['npcflag'] & 4194304) $npcflag[4194304] = " selected=\"selected\" ";
  if ($mob['npcflag'] & 268435456) $npcflag[268435456] = " selected=\"selected\" ";
  }

$output .= "<td rowspan='2'>".makeinfocell(lang('creature', 'npc_flag'),lang('creature', 'npc_flag_desc'))."</td>
     <td colspan='2' rowspan='2'><select multiple='multiple' name='npcflag[]' size='3'>
    <option value='0' {$npcflag[0]}>".lang('creature', 'none')."</option>
    <option value='1' {$npcflag[1]}>".lang('creature', 'gossip')."</option>
    <option value='2' {$npcflag[2]}>".lang('creature', 'quest_giver')."</option>
    <option value='4' {$npcflag[4]}>".lang('creature', 'vendor')."</option>
    <option value='8' {$npcflag[8]}>".lang('creature', 'taxi')."</option>
    <option value='16' {$npcflag[16]}>".lang('creature', 'trainer')."</option>
    <option value='32' {$npcflag[32]}>".lang('creature', 'spirit_healer')."</option>
    <option value='64' {$npcflag[64]}>".lang('creature', 'guard')."</option>
    <option value='128' {$npcflag[128]}>".lang('creature', 'inn_keeper')."</option>
    <option value='256' {$npcflag[256]}>".lang('creature', 'banker')."</option>
    <option value='512' {$npcflag[512]}>".lang('creature', 'retitioner')."</option>
    <option value='1024' {$npcflag[1024]}>".lang('creature', 'tabard_vendor')."</option>
    <option value='2048' {$npcflag[2048]}>".lang('creature', 'battlemaster')."</option>
    <option value='4096' {$npcflag[4096]}>".lang('creature', 'auctioneer')."</option>
    <option value='8192' {$npcflag[8192]}>".lang('creature', 'stable_master')."</option>
    <option value='16384' {$npcflag[16384]}>".lang('creature', 'armorer')."</option>
     </select></td>";
  unset($npcflag);

 //$trainer_type = array(0 => "", 1 => "", 2 => "", 3 => "");
 //$trainer_type[$mob['trainer_type']] = " selected=\"selected\" ";

$output .= "
</tr>
<tr>";
  //unset($trainer_type);

 $family = array(0 => "", 1 => "", 2 => "", 3 => "", 4 => "", 5 => "", 6 => "", 7 => "", 8 => "", 9 => "",
 11 => "", 12 => "", 13 => "", 14 => "", 15 => "", 16 => "", 17 => "", 18 => "", 19 => "", 20 => "", 21 => "",
 22 => "", 23 => "", 24 => "", 25 => "", 26 => "", 27 => "" );
 $family[$mob['family']] = " selected=\"selected\" ";

$output .= "<td>".makeinfocell(lang('creature', 'family'),lang('creature', 'family_desc'))."</td>
     <td colspan=\"2\"><select name=\"family\">
    <option value=\"0\" {$family[0]}>0 - ".lang('creature', 'other')."</option>
    <option value=\"1\" {$family[1]}>1 - ".lang('creature', 'wolf')."</option>
    <option value=\"2\" {$family[2]}>2 - ".lang('creature', 'cat')."</option>
    <option value=\"3\" {$family[3]}>3 - ".lang('creature', 'spider')."</option>
    <option value=\"4\" {$family[4]}>4 - ".lang('creature', 'bear')."</option>
    <option value=\"5\" {$family[5]}>5 - ".lang('creature', 'boar')."</option>
    <option value=\"6\" {$family[6]}>6 - ".lang('creature', 'crocolisk')."</option>
    <option value=\"7\" {$family[7]}>7 - ".lang('creature', 'carrion_bird')."</option>
    <option value=\"8\" {$family[8]}>8 - ".lang('creature', 'crab')."</option>
    <option value=\"9\" {$family[9]}>9 - ".lang('creature', 'gorilla')."</option>
    <option value=\"11\" {$family[11]}>11 - ".lang('creature', 'raptor')."</option>
    <option value=\"12\" {$family[12]}>12 - ".lang('creature', 'tallstrider')."</option>
    <option value=\"13\" {$family[13]}>13 - ".lang('creature', 'other')."</option>
    <option value=\"14\" {$family[14]}>14 - ".lang('creature', 'other')."</option>
    <option value=\"15\" {$family[15]}>15 - ".lang('creature', 'felhunter')."</option>
    <option value=\"16\" {$family[16]}>16 - ".lang('creature', 'voidwalker')."</option>
    <option value=\"17\" {$family[17]}>17 - ".lang('creature', 'succubus')."</option>
    <option value=\"18\" {$family[18]}>18 - ".lang('creature', 'other')."</option>
    <option value=\"19\" {$family[19]}>19 - ".lang('creature', 'doomguard')."</option>
    <option value=\"20\" {$family[20]}>20 - ".lang('creature', 'scorpid')."</option>
    <option value=\"21\" {$family[21]}>21 - ".lang('creature', 'turtle')."</option>
    <option value=\"22\" {$family[22]}>22 - ".lang('creature', 'scorpid')."</option>
    <option value=\"23\" {$family[23]}>23 - ".lang('creature', 'imp')."</option>
    <option value=\"24\" {$family[24]}>24 - ".lang('creature', 'bat')."</option>
    <option value=\"25\" {$family[25]}>25 - ".lang('creature', 'hyena')."</option>
    <option value=\"26\" {$family[26]}>26 - ".lang('creature', 'owl')."</option>
    <option value=\"27\" {$family[27]}>27 - ".lang('creature', 'wind_serpent')."</option>
     </select></td>
  </tr>";
  unset($family);

$result1 = $sqlw->query("SELECT COUNT(*) FROM creature_spawns WHERE entry = '{$mob['entry']}'");
$output .= "<tr><td colspan=\"6\">".lang('creature', 'creature_swapned')." : ".$sqlw->result($result1, 0)." ".lang('creature', 'times').".</td></tr>

</table>
<br /><br />
</div>";

$output .= "<div id=\"pane3\">
  <br /><br /><table class=\"lined\" id=\"ch_cre_proto\">
<tr class=\"large_bold\"><td colspan=\"6\" class=\"hidden\" align=\"left\">".lang('creature', 'basic_status').":</td></tr>
   <tr>
    <td>".makeinfocell(lang('creature', 'armor'),lang('creature', 'armor_desc'))."</td>
    <td colspan=\"1\"><input type=\"text\" name=\"armor\" size=\"12\" maxlength=\"10\" value=\"{$mob['armor']}\" /></td>

    <td></td>
    <td colspan=\"2\"></td>
 </tr>

<tr class=\"large_bold\"><td colspan=\"6\" class=\"hidden\" align=\"left\">".lang('creature', 'damage').":</td></tr>
   <tr>
    <td>".makeinfocell(lang('creature', 'min_damage'),lang('creature', 'min_damage_desc'))."</td>
    <td><input type=\"text\" name=\"mindmg\" size=\"8\" maxlength=\"45\" value=\"{$mob['mindamage']}\" /></td>

    <td>".makeinfocell(lang('creature', 'max_damage'),lang('creature', 'max_damage_desc'))."</td>
    <td><input type=\"text\" name=\"maxdmg\" size=\"8\" maxlength=\"45\" value=\"{$mob['maxdamage']}\" /></td>

    <td></td>
    <td></td>
 </tr>
 <tr>
    <td>".makeinfocell(lang('creature', 'min_range_dmg'),lang('creature', 'min_range_dmg_desc'))."</td>
    <td><input type=\"text\" name=\"minrangedmg\" size=\"8\" maxlength=\"45\" value=\"{$mob['rangedmindamage']}\" /></td>

    <td>".makeinfocell(lang('creature', 'max_range_dmg'),lang('creature', 'max_range_dmg_desc'))."</td>
    <td><input type=\"text\" name=\"maxrangedmg\" size=\"8\" maxlength=\"45\" value=\"{$mob['rangedmaxdamage']}\" /></td>

    <td></td>
    <td></td>
 </tr>
  <tr>
    <td>".makeinfocell(lang('creature', 'attack_time'),lang('creature', 'attack_time_desc'))."</td>
    <td><input type=\"text\" name=\"baseattacktime\" size=\"8\" maxlength=\"4\" value=\"{$mob['attacktime']}\" /></td>

    <td>".makeinfocell(lang('creature', 'range_attack_time'),lang('creature', 'range_attack_time_desc'))."</td>
    <td><input type=\"text\" name=\"rangeattacktime\" size=\"8\" maxlength=\"4\" value=\"{$mob['rangedattacktime']}\" /></td>

    <td></td>
    <td></td>
 </tr>";

$output .= "
<tr class=\"large_bold\"><td colspan=\"6\" class=\"hidden\" align=\"left\">".lang('creature', 'spells').":</td></tr>

<tr>
 <td>".makeinfocell(lang('creature', 'spell')." 1",lang('creature', 'spell_desc'))."</td>
 <td colspan=\"1\"><input type=\"text\" name=\"spell1\" size=\"14\" maxlength=\"11\" value=\"{$mob['spell1']}\" /></td>

 <td>".makeinfocell(lang('creature', 'spell')." 2",lang('creature', 'spell_desc'))."</td>
 <td colspan=\"1\"><input type=\"text\" name=\"spell2\" size=\"14\" maxlength=\"11\" value=\"{$mob['spell2']}\" /></td>
 <td></td><td></td>
</tr>
<tr>
 <td>".makeinfocell(lang('creature', 'spell')." 3",lang('creature', 'spell_desc'))."</td>
 <td colspan=\"1\"><input type=\"text\" name=\"spell3\" size=\"14\" maxlength=\"11\" value=\"{$mob['spell3']}\" /></td>

 <td>".makeinfocell(lang('creature', 'spell')." 4",lang('creature', 'spell_desc'))."</td>
 <td colspan=\"1\"><input type=\"text\" name=\"spell4\" size=\"14\" maxlength=\"11\" value=\"{$mob['spell4']}\" /></td>
 <td></td><td></td>
</tr>

<tr class=\"large_bold\"><td colspan=\"6\" class=\"hidden\" align=\"left\">".lang('creature', 'resistances').":</td></tr>
<tr>
  <td>".makeinfocell(lang('creature', 'resis_holy'),lang('creature', 'resis_holy_desc'))."</td>
  <td><input type=\"text\" name=\"resistance1\" size=\"8\" maxlength=\"10\" value=\"{$mob['resistance1']}\" /></td>

  <td>".makeinfocell(lang('creature', 'resis_fire'),lang('creature', 'resis_fire_desc'))."</td>
  <td><input type=\"text\" name=\"resistance2\" size=\"8\" maxlength=\"10\" value=\"{$mob['resistance2']}\" /></td>

  <td>".makeinfocell(lang('creature', 'resis_nature'),lang('creature', 'resis_nature_desc'))."</td>
  <td><input type=\"text\" name=\"resistance3\" size=\"8\" maxlength=\"10\" value=\"{$mob['resistance3']}\" /></td>
 </tr>
 <tr>
  <td>".makeinfocell(lang('creature', 'resis_frost'),lang('creature', 'resis_frost_desc'))."</td>
  <td><input type=\"text\" name=\"resistance4\" size=\"8\" maxlength=\"10\" value=\"{$mob['resistance4']}\" /></td>

  <td>".makeinfocell(lang('creature', 'resis_shadow'),lang('creature', 'resis_shadow_desc'))."</td>
  <td><input type=\"text\" name=\"resistance5\" size=\"8\" maxlength=\"10\" value=\"{$mob['resistance5']}\" /></td>

  <td>".makeinfocell(lang('creature', 'resis_arcane'),lang('creature', 'resis_arcane_desc'))."</td>
  <td><input type=\"text\" name=\"resistance6\" size=\"8\" maxlength=\"10\" value=\"{$mob['resistance6']}\" /></td>
 </tr>

 </table><br /><br />
</div>";

$output .= "<div id=\"pane4\">
  <br /><br /><table class=\"lined\" id=\"ch_cre_proto\">
<tr class=\"large_bold\"><td colspan=\"6\" class=\"hidden\" align=\"left\">".lang('creature', 'models').":</td></tr>
<tr>
  <td colspan=\"2\">".makeinfocell(lang('creature', 'modelid_A'),lang('creature', 'modelid_A_desc'))."</td>
  <td><input type=\"text\" name=\"modelid_A\" size=\"8\" maxlength=\"11\" value=\"{$mob['male_displayid']}\" /></td>

  <td colspan=\"2\">".makeinfocell(lang('creature', 'modelid_A2'),lang('creature', 'modelid_A2_desc'))."</td>
  <td><input type=\"text\" name=\"modelid_A2\" size=\"8\" maxlength=\"11\" value=\"{$mob['male_displayid2']}\" /></td>
</tr>
<tr>
  <td colspan=\"2\">".makeinfocell(lang('creature', 'modelid_H'),lang('creature', 'modelid_H_desc'))."</td>
  <td><input type=\"text\" name=\"modelid_H\" size=\"8\" maxlength=\"11\" value=\"{$mob['female_displayid']}\" /></td>

  <td colspan=\"2\">".makeinfocell(lang('creature', 'modelid_H2'),lang('creature', 'modelid_H2_desc'))."</td>
  <td><input type=\"text\" name=\"modelid_H2\" size=\"8\" maxlength=\"11\" value=\"{$mob['female_displayid2']}\" /></td>
</tr>
</table><br /><br />
";

// this_is_junk: ArcEmu handles creature epuips differently.  ...that and the data doesn't match between my DBs :/
//               We'll handle mob equipment with spawns
//$result1 = $sqlw->query("SELECT slot1item,slot2item,slot3item FROM creature_spawns WHERE entry = '{$mob['entry']}'");
/*if ($mobequip = $sqlw->fetch_assoc($result1)){

$output .= "<br /><br /><table class=\"lined\" style=\"width: 720px;\">
<tr class=\"large_bold\"><td colspan=\"6\" class=\"hidden\" align=\"left\">".lang('creature', 'equipment').":</td></tr>
<tr>
  <td>".makeinfocell($lang_creature['equip_slot']." 1",$lang_creature['equip_slot1_desc'])."</td>
  <td><input type=\"text\" name=\"equipslot1\" size=\"8\" maxlength=\"10\" value=\"{$mobequip['equipentry1']}\" /></td>

  <td>".makeinfocell($lang_creature['equip_model']." 1",$lang_creature['equip_model1_desc'])."</td>
  <td><input type=\"text\" name=\"equipmodel1\" size=\"8\" maxlength=\"10\" value=\"{$mobequip['slot1item']}\" /></td>

  <td>".makeinfocell($lang_creature['equip_info']." 1",$lang_creature['equip_info1_desc'])."</td>
  <td><input type=\"text\" name=\"equipinfo1\" size=\"8\" maxlength=\"10\" value=\"{$mobequip['equipinfo1']}\" /></td>
</tr>
<tr>
  <td>".makeinfocell($lang_creature['equip_slot']." 2",$lang_creature['equip_slot2_desc'])."</td>
  <td><input type=\"text\" name=\"equipslot2\" size=\"8\" maxlength=\"10\" value=\"{$mobequip['equipentry2']}\" /></td>

  <td>".makeinfocell($lang_creature['equip_model']." 2",$lang_creature['equip_model2_desc'])."</td>
  <td><input type=\"text\" name=\"equipmodel2\" size=\"8\" maxlength=\"10\" value=\"{$mobequip['slot2item']}\" /></td>

  <td>".makeinfocell($lang_creature['equip_info']." 2",$lang_creature['equip_info2_desc'])."</td>
  <td><input type=\"text\" name=\"equipinfo2\" size=\"8\" maxlength=\"10\" value=\"{$mobequip['equipinfo2']}\" /></td>
</tr>
<tr>
  <td>".makeinfocell($lang_creature['equip_slot']." 3",$lang_creature['equip_slot3_desc'])."</td>
  <td><input type=\"text\" name=\"equipslot3\" size=\"8\" maxlength=\"10\" value=\"{$mobequip['equipentry3']}\" /></td>

  <td>".makeinfocell($lang_creature['equip_model']." 3",$lang_creature['equip_model3_desc'])."</td>
  <td><input type=\"text\" name=\"equipmodel3\" size=\"8\" maxlength=\"10\" value=\"{$mobequip['slot3item']}\" /></td>

  <td>".makeinfocell($lang_creature['equip_info']." 3",$lang_creature['equip_info3_desc'])."</td>
  <td><input type=\"text\" name=\"equipinfo3\" size=\"8\" maxlength=\"10\" value=\"{$mobequip['equipinfo3']}\" /></td>
</tr>
</table><br /><br />
</div>";
}
else
{
$output .= "<br /><br /><table class=\"lined\" style=\"width: 720px;\">
<tr class=\"large_bold\"><td colspan=\"6\" class=\"hidden\" align=\"left\">".lang('creature', 'equipment').":</td></tr>
</table><br /><br />
</div>";
}*/
$output .= "</div>";

$output .= "<div id=\"pane2\">
  <br /><br /><table class=\"lined\" id=\"ch_cre_proto\">
<tr class=\"large_bold\"><td colspan=\"6\" class=\"hidden\" align=\"left\">".lang('creature', 'other').":</td></tr>";

 if ($mob['RacialLeader']) $RacialLeader = "checked";
  else $RacialLeader = "";

$output .= "<td>".makeinfocell(lang('creature', 'RacialLeader'),lang('creature', 'RacialLeader_desc'))."</td>
  <td><input type=\"checkbox\" name=\"RacialLeader\" value=\"1\" $RacialLeader /></td>
  <td>".makeinfocell(lang('creature', 'flag_1'),lang('creature', 'flag_1_desc'))."</td>
  <td><input type=\"text\" name=\"type_flags\" size=\"8\" maxlength=\"11\" value=\"{$mob['flags1']}\" /></td>
  <td></td>
  <td></td>
";

$output .= "<td></td>
     <td></td>";

$output .= "

   </table><br /><br />
    </div>";

/*****************
/  LOCALES
*****************/
if ($locales_search_option != 0) {

  if ($do_insert)
    $result_loc = $sqlw->query("SELECT '' as `name_loc1`, '' as `name_loc2`, '' as `name_loc3`, '' as `name_loc4`, '' as `name_loc5`, '' as `name_loc6`, '' as `name_loc7`, '' as `name_loc8`, '' as `subname_loc1`, '' as `subname_loc2`, '' as `subname_loc3`, '' as `subname_loc4`, '' as `subname_loc5`, '' as `subname_loc6`, '' as `subname_loc7`, '' as `subname_loc8`");
  else  // update
    $result_loc = $sqlw->query("SELECT `name_loc1`, `name_loc2`, `name_loc3`, `name_loc4`, `name_loc5`, `name_loc6`, `name_loc7`, `name_loc8`, `subname_loc1`, `subname_loc2`, `subname_loc3`, `subname_loc4`, `subname_loc5`, `subname_loc6`, `subname_loc7`, `subname_loc8` FROM `locales_creature` WHERE `entry` = '$entry'");


  $loc = $sqlw->fetch_assoc($result_loc);

  $output .= "<div id=\"pane11\">
    <br /><br /><table class=\"lined\" id=\"ch_cre_proto\">

  <tr class=\"large_bold\"><td colspan=\"6\" class=\"hidden\" align=\"left\">".lang('global', 'language_1').":</td></tr>
  <tr>
   <td>".makeinfocell(lang('creature', 'name'),lang('creature', 'name_desc'))."</td>
   <td colspan=\"2\"><input type=\"text\" name=\"name_loc1\" size=\"24\" maxlength=\"128\" value=\"{$loc['name_loc1']}\" /></td>

   <td>".makeinfocell(lang('creature', 'sub_name'),lang('creature', 'sub_name_desc'))."</td>
   <td colspan=\"2\"><input type=\"text\" name=\"subname_loc1\" size=\"24\" maxlength=\"64\" value=\"{$loc['subname_loc1']}\" /></td>
  </tr>

  <tr class=\"large_bold\"><td colspan=\"6\" class=\"hidden\" align=\"left\">".lang('global', 'language_2').":</td></tr>
  <tr>
   <td>".makeinfocell(lang('creature', 'name'),lang('creature', 'name_desc'))."</td>
   <td colspan=\"2\"><input type=\"text\" name=\"name_loc2\" size=\"24\" maxlength=\"64\" value=\"{$loc['name_loc2']}\" /></td>

   <td>".makeinfocell(lang('creature', 'sub_name'),lang('creature', 'sub_name_desc'))."</td>
   <td colspan=\"2\"><input type=\"text\" name=\"subname_loc2\" size=\"24\" maxlength=\"64\" value=\"{$loc['subname_loc2']}\" /></td>
  </tr>
  <tr class=\"large_bold\"><td colspan=\"6\" class=\"hidden\" align=\"left\">".lang('global', 'language_3').":</td></tr>
  <tr>
   <td>".makeinfocell(lang('creature', 'name'),lang('creature', 'name_desc'))."</td>
   <td colspan=\"2\"><input type=\"text\" name=\"name_loc3\" size=\"24\" maxlength=\"64\" value=\"{$loc['name_loc3']}\" /></td>

   <td>".makeinfocell(lang('creature', 'sub_name'),lang('creature', 'sub_name_desc'))."</td>
   <td colspan=\"2\"><input type=\"text\" name=\"subname_loc3\" size=\"24\" maxlength=\"64\" value=\"{$loc['subname_loc3']}\" /></td>
  </tr>
  <tr class=\"large_bold\"><td colspan=\"6\" class=\"hidden\" align=\"left\">".lang('global', 'language_4').":</td></tr>
  <tr>
   <td>".makeinfocell(lang('creature', 'name'),lang('creature', 'name_desc'))."</td>
   <td colspan=\"2\"><input type=\"text\" name=\"name_loc4\" size=\"24\" maxlength=\"64\" value=\"{$loc['name_loc4']}\" /></td>

   <td>".makeinfocell(lang('creature', 'sub_name'),lang('creature', 'sub_name_desc'))."</td>
   <td colspan=\"2\"><input type=\"text\" name=\"subname_loc4\" size=\"24\" maxlength=\"64\" value=\"{$loc['subname_loc4']}\" /></td>
  </tr>
  <tr class=\"large_bold\"><td colspan=\"6\" class=\"hidden\" align=\"left\">".lang('global', 'language_5').":</td></tr>
  <tr>
   <td>".makeinfocell(lang('creature', 'name'),lang('creature', 'name_desc'))."</td>
   <td colspan=\"2\"><input type=\"text\" name=\"name_loc5\" size=\"24\" maxlength=\"64\" value=\"{$loc['name_loc5']}\" /></td>

   <td>".makeinfocell(lang('creature', 'sub_name'),lang('creature', 'sub_name_desc'))."</td>
   <td colspan=\"2\"><input type=\"text\" name=\"subname_loc5\" size=\"24\" maxlength=\"64\" value=\"{$loc['subname_loc5']}\" /></td>
  </tr>
  <tr class=\"large_bold\"><td colspan=\"6\" class=\"hidden\" align=\"left\">".lang('global', 'language_6').":</td></tr>
  <tr>
   <td>".makeinfocell(lang('creature', 'name'),lang('creature', 'name_desc'))."</td>
   <td colspan=\"2\"><input type=\"text\" name=\"name_loc6\" size=\"24\" maxlength=\"64\" value=\"{$loc['name_loc6']}\" /></td>

   <td>".makeinfocell(lang('creature', 'sub_name'),lang('creature', 'sub_name_desc'))."</td>
   <td colspan=\"2\"><input type=\"text\" name=\"subname_loc6\" size=\"24\" maxlength=\"64\" value=\"{$loc['subname_loc6']}\" /></td>
  </tr>
  <tr class=\"large_bold\"><td colspan=\"6\" class=\"hidden\" align=\"left\">".lang('global', 'language_7').":</td></tr>
  <tr>
   <td>".makeinfocell(lang('creature', 'name'),lang('creature', 'name_desc'))."</td>
   <td colspan=\"2\"><input type=\"text\" name=\"name_loc7\" size=\"24\" maxlength=\"64\" value=\"{$loc['name_loc7']}\" /></td>

   <td>".makeinfocell(lang('creature', 'sub_name'),lang('creature', 'sub_name_desc'))."</td>
   <td colspan=\"2\"><input type=\"text\" name=\"subname_loc7\" size=\"24\" maxlength=\"64\" value=\"{$loc['subname_loc7']}\" /></td>
  </tr>
  <tr class=\"large_bold\"><td colspan=\"6\" class=\"hidden\" align=\"left\">".lang('global', 'language_8').":</td></tr>
  <tr>
   <td>".makeinfocell(lang('creature', 'name'),lang('creature', 'name_desc'))."</td>
   <td colspan=\"2\"><input type=\"text\" name=\"name_loc8\" size=\"24\" maxlength=\"64\" value=\"{$loc['name_loc8']}\" /></td>

   <td>".makeinfocell(lang('creature', 'sub_name'),lang('creature', 'sub_name_desc'))."</td>
   <td colspan=\"2\"><input type=\"text\" name=\"subname_loc8\" size=\"24\" maxlength=\"64\" value=\"{$loc['subname_loc8']}\" /></td>
  </tr>


</table><br /><br />
           </div>";
}

if($mob['lootid']){
$output .= "<div id=\"pane5\">
  <br /><br /><table class=\"lined\" id=\"ch_cre_proto\">
  <tr class=\"large_bold\"><td colspan=\"6\" class=\"hidden\" align=\"left\">".lang('creature', 'loot_tmpl_id').": ".$mob['lootid']."</td></tr>
<tr>
  <td colspan=\"6\">";

  $cel_counter = 0;
  $row_flag = 0;
  $output .= "<table class=\"hidden\" align=\"center\"><tr>";
  $result1 = $sqlw->query("SELECT item,ChanceOrQuestChance,`groupid`,mincountOrRef,maxcount, lootcondition, condition_value1,condition_value2 FROM creature_loot_template WHERE entry = {$mob['lootid']} ORDER BY ChanceOrQuestChance DESC");
  while ($item = $sqlw->fetch_row($result1)){
    $cel_counter++;
    $tooltip = get_item_name($item[0])." ($item[0])<br />".lang('creature', 'drop_chance').": $item[1]%<br />".lang('creature', 'quest_drop_chance').": $item[2]%<br />".lang('creature', 'drop_chance').": $item[3]-$item[4]<br />".lang('creature', 'lootcondition').": $item[5]<br />".lang('creature', 'condition_value1').": $item[6]<br />".lang('creature', 'condition_value2').": $item[7]";
    $output .= "<td>";
    $output .= maketooltip("<img src=\"".get_item_icon($item[0])."\" class=\"icon_border\" alt=\"\" />", "$item_datasite$item[0]", $tooltip, "item_tooltip");
    $output .= "<br /><input type=\"checkbox\" name=\"del_loot_items[]\" value=\"$item[0]\" /></td>";

    if ($cel_counter >= 14) {
      $cel_counter = 0;
      $output .= "</tr><tr>";
      $row_flag++;
      }
  };
  if ($row_flag) $output .= "<td colspan=\"".(16 - $cel_counter)."\"></td>";
  $output .= "</td></tr></table>
 </td>
</tr>
<tr class=\"large_bold\"><td colspan=\"6\" class=\"hidden\" align=\"left\">".lang('creature', 'add_items_to_templ').":</td></tr>
<tr>
<td>".makeinfocell(lang('creature', 'loot_item_id'),lang('creature', 'loot_item_id_desc'))."</td>
  <td><input type=\"text\" name=\"item\" size=\"8\" maxlength=\"10\" value=\"\" /></td>
<td>".makeinfocell(lang('creature', 'loot_drop_chance'),lang('creature', 'loot_drop_chance_desc'))."</td>
  <td><input type=\"text\" name=\"ChanceOrQuestChance\" size=\"8\" maxlength=\"11\" value=\"0\" /></td>
<td>".makeinfocell(lang('creature', 'loot_quest_drop_chance'),lang('creature', 'loot_quest_drop_chance_desc'))."</td>
  <td><input type=\"text\" name=\"groupid\" size=\"8\" maxlength=\"10\" value=\"0\" /></td>
</tr>
<tr>
<td>".makeinfocell(lang('creature', 'min_count'),lang('creature', 'min_count_desc'))."</td>
  <td><input type=\"text\" name=\"mincountOrRef\" size=\"8\" maxlength=\"3\" value=\"1\" /></td>
<td>".makeinfocell(lang('creature', 'max_count'),lang('creature', 'max_count_desc'))."</td>
  <td><input type=\"text\" name=\"maxcount\" size=\"8\" maxlength=\"3\" value=\"1\" /></td>
</tr>
<tr>
<td>".makeinfocell(lang('creature', 'lootcondition'),lang('creature', 'lootcondition_desc'))."</td>
  <td><input type=\"text\" name=\"lootcondition\" size=\"8\" maxlength=\"3\" value=\"0\" /></td>
<td>".makeinfocell(lang('creature', 'condition_value1'),lang('creature', 'condition_value1_desc'))."</td>
  <td><input type=\"text\" name=\"condition_value1\" size=\"8\" maxlength=\"3\" value=\"0\" /></td>
<td>".makeinfocell(lang('creature', 'condition_value2'),lang('creature', 'condition_value2_desc'))."</td>
  <td><input type=\"text\" name=\"condition_value2\" size=\"8\" maxlength=\"3\" value=\"0\" /></td>
</tr>
</table><br />".lang('creature', 'check_to_delete')."<br /><br />
</div>";
}

if ($quest_flag) {
$output .= "<div id=\"pane6\">
  <br /><br /><table class=\"lined\" id=\"ch_cre_proto\">
  <tr class=\"large_bold\"><td colspan=\"2\" class=\"hidden\" align=\"left\">".lang('creature', 'start_quests').":</td></tr>";

   $deplang = get_lang_id();

  $result1 = $sqlw->query("SELECT quest FROM creature_questrelation WHERE id = {$mob['entry']}");
  while ($quest = $sqlw->fetch_row($result1)){
    $query1 = $sqlw->query("SELECT QuestLevel,IFNULL(".($deplang<>0?"title_loc$deplang":"NULL").",`title`) as title FROM quest_template LEFT JOIN locales_quest ON quest_template.entry = locales_quest.entry WHERE quest_template.entry ='$quest[0]'");
    $quest_templ = $sqlw->fetch_row($query1);

    $output .= "<tr><td width=\"5%\"><input type=\"checkbox\" name=\"del_questrelation[]\" value=\"$quest[0]\" /></td>
          <td width=\"95%\" align=\"left\"><a class=\"tooltip\" href=\"$quest_datasite$quest[0]\" target=\"_blank\">({$quest_templ[0]}) $quest_templ[1]</a></td></tr>";
  };

$output .= "<tr class=\"large_bold\" align=\"left\"><td colspan=\"2\" class=\"hidden\">".lang('creature', 'add_starts_quests').":</td></tr>
  <tr><td colspan=\"2\" align=\"left\">".makeinfocell(lang('creature', 'quest_id'),lang('creature', 'quest_id_desc'))." :
    <input type=\"text\" name=\"questrelation\" size=\"8\" maxlength=\"8\" value=\"\" /></td></tr>

<tr class=\"large_bold\"><td colspan=\"2\" class=\"hidden\" align=\"left\">".lang('creature', 'ends_quests').":</td></tr>";

  $result1 = $sqlw->query("SELECT quest FROM creature_involvedrelation WHERE id = {$mob['entry']}");
  while ($quest = $sqlw->fetch_row($result1)){
    $query1 = $sqlw->query("SELECT QuestLevel,IFNULL(".($deplang<>0?"title_loc$deplang":"NULL").",`title`) as title FROM quest_template LEFT JOIN locales_quest ON quest_template.entry = locales_quest.entry WHERE quest_template.entry ='$quest[0]'");
    $quest_templ = $sqlw->fetch_row($query1);

    $output .= "<tr><td width=\"5%\"><input type=\"checkbox\" name=\"del_involvedrelation[]\" value=\"$quest[0]\" /></td>
        <td width=\"95%\" align=\"left\"><a class=\"tooltip\" href=\"$quest_datasite$quest[0]\" target=\"_blank\">({$quest_templ[0]}) $quest_templ[1]</a></td></tr>";
  };

$output .= "<tr class=\"large_bold\" align=\"left\"><td colspan=\"2\" class=\"hidden\">".lang('creature', 'add_ends_quests').":</td></tr>
  <tr><td colspan=\"2\" align=\"left\">".makeinfocell(lang('creature', 'quest_id'),lang('creature', 'quest_id_desc'))." :
    <input type=\"text\" name=\"involvedrelation\" size=\"8\" maxlength=\"8\" value=\"\" /></td></tr>

</table><br />".lang('creature', 'check_to_delete')."<br /><br />
</div>";
}

if ($vendor_flag) {
$output .= "<div id=\"pane7\">
  <br /><br /><table class=\"lined\" id=\"ch_cre_proto\">
  <tr class=\"large_bold\"><td colspan=\"8\" class=\"hidden\" align=\"left\">".lang('creature', 'sells').":</td></tr>
  <tr><td colspan=\"8\">";

  $cel_counter = 0;
  $row_flag = 0;
  $output .= "<table class=\"hidden\" align=\"center\"><tr>";
  $result1 = $sqlw->query("SELECT item, maxcount, incrtime, ExtendedCost FROM npc_vendor WHERE entry = {$mob['entry']}");
  while ($item = $sqlw->fetch_row($result1)){
    $cel_counter++;
    if (!$item[1]) $count = "".lang('creature', 'unlimited')."";
      else $count = $item[1];
    $tooltip = get_item_name($item[0])."<br />".lang('creature', 'count')." : $count<br />".lang('creature', 'vendor_incrtime')." : $item[2]";
    $output .= "<td>";
    $output .= maketooltip("<img src=\"".get_item_icon($item[0])."\" class=\"icon_border\" alt=\"\" />", "$item_datasite$item[0]", $tooltip, "item_tooltip");
    $output .= "<br /><input type=\"checkbox\" name=\"del_vendor_item[]\" value=\"$item[0]\" /></td>";

    if ($cel_counter >= 14) {
      $cel_counter = 0;
      $output .= "</tr><tr>";
      $row_flag++;
      }
  };

if ($row_flag) $output .= "<td colspan=\"".(16 - $cel_counter)."\"></td>";
  $output .= "</td></tr></table>
 </td></tr>
<tr class=\"large_bold\"><td colspan=\"8\" class=\"hidden\" align=\"left\">".lang('creature', 'add_items_to_vendor').":</td></tr>
<tr>
<td>".makeinfocell(lang('creature', 'vendor_item_id'),lang('creature', 'vendor_item_id_desc'))."</td>
  <td><input type=\"text\" name=\"vendor_item\" size=\"8\" maxlength=\"10\" value=\"\" /></td>
<td>".makeinfocell(lang('creature', 'vendor_max_count'),lang('creature', 'vendor_max_count_desc'))."</td>
  <td><input type=\"text\" name=\"vendor_maxcount\" size=\"8\" maxlength=\"3\" value=\"0\" /></td>
<td>".makeinfocell(lang('creature', 'vendor_incrtime'),lang('creature', 'vendor_incrtime_desc'))."</td>
  <td><input type=\"text\" name=\"vendor_incrtime\" size=\"8\" maxlength=\"10\" value=\"0\" /></td>
<td>".makeinfocell(lang('creature', 'vendor_extended_cost'),lang('creature', 'vendor_extended_cost_desc'))."</td>
  <td><input type=\"text\" name=\"vendor_extended_cost\" size=\"8\" maxlength=\"10\" value=\"0\" /></td>
</tr>
</table><br />".lang('creature', 'check_to_delete')."<br /><br />
</div>";
}

if ($trainer_flag) {
$output .= "<div id=\"pane8\">
  <br /><br /><table class=\"lined\" id=\"ch_cre_proto\">
  <tr class=\"large_bold\"><td colspan=\"6\" class=\"hidden\" align=\"left\">".lang('creature', 'trains').":</td></tr>
  <tr><td colspan=\"6\">";

  $cel_counter = 0;
  $row_flag = 0;
  $output .= "<table class=\"hidden\" align=\"center\"><tr>";
  $result1 = $sqlw->query("SELECT spell, spellcost, reqskill, reqskillvalue, reqlevel FROM npc_trainer WHERE entry = {$mob['entry']} ORDER BY reqlevel");
  while ($spell = $sqlw->fetch_row($result1)){
    $cel_counter++;
    $tooltip = "".lang('creature', 'spell_id')." : $spell[0]<br />".lang('creature', 'cost')." :  $spell[1](c)<br />".lang('creature', 'req_skill')." : $spell[2]<br />".lang('creature', 'req_skill_lvl')." :  $spell[3]<br />".lang('creature', 'req_level')." $spell[4]";
    $output .= "<td>";
    $output .= maketooltip($spell[0], "$spell_datasite$spell[0]", $tooltip, "info_tooltip");
    $output .= "<br /><input type=\"checkbox\" name=\"del_trainer_spell[]\" value=\"$spell[0]\" /></td>";

    if ($cel_counter >= 16) {
      $cel_counter = 0;
      $output .= "</tr><tr>";
      $row_flag++;
      }
  };

if ($row_flag) $output .= "<td colspan=\"".(16 - $cel_counter)."\"></td>";
  $output .= "</td></tr></table>
 </td></tr>
<tr class=\"large_bold\"><td colspan=\"6\" class=\"hidden\" align=\"left\">".lang('creature', 'add_spell_to_trainer').":</td></tr>
<tr>
  <td>".makeinfocell(lang('creature', 'train_spell_id'),lang('creature', 'train_spell_id_desc'))."</td>
  <td colspan=\"3\"><input type=\"text\" name=\"trainer_spell\" size=\"40\" maxlength=\"10\" value=\"\" /></td>
  <td>".makeinfocell(lang('creature', 'train_cost'),lang('creature', 'train_cost_desc'))."</td>
  <td><input type=\"text\" name=\"spellcost\" size=\"8\" maxlength=\"10\" value=\"0\" /></td>
</tr>
<tr>
  <td>".makeinfocell(lang('creature', 'req_skill'),lang('creature', 'req_skill_desc'))."</td>
  <td><input type=\"text\" name=\"reqskill\" size=\"8\" maxlength=\"10\" value=\"0\" /></td>
  <td>".makeinfocell(lang('creature', 'req_skill_value'),lang('creature', 'req_skill_value_desc'))."</td>
  <td><input type=\"text\" name=\"reqskillvalue\" size=\"8\" maxlength=\"10\" value=\"0\" /></td>
  <td>".makeinfocell(lang('creature', 'req_level'),lang('creature', 'req_level_desc'))."</td>
  <td><input type=\"text\" name=\"reqlevel\" size=\"8\" maxlength=\"10\" value=\"0\" /></td>
</tr>

</table><br />".lang('creature', 'check_to_delete')."<br /><br />
</div>";
}

if ($mob['skinloot']) {
$output .= "<div id=\"pane9\">
  <br /><br /><table class=\"lined\" id=\"ch_cre_proto\">
  <tr class=\"large_bold\"><td colspan=\"6\" class=\"hidden\" align=\"left\">".lang('creature', 'skinning_loot_tmpl_id').": {$mob['skinloot']}</td></tr>
  <tr><td colspan=\"6\">";

  $cel_counter = 0;
  $row_flag = 0;
  $output .= "<table class=\"hidden\" align=\"center\"><tr>";
  $result1 = $sqlw->query("SELECT item,ChanceOrQuestChance,`groupid`,mincountOrRef,maxcount, lootcondition, condition_value1, condition_value2 FROM skinning_loot_template WHERE entry = {$mob['skinloot']} ORDER BY ChanceOrQuestChance DESC");
  while ($item = $sqlw->fetch_row($result1)){
    $cel_counter++;
    $tooltip = get_item_name($item[0])." ($item[0])<br />".lang('creature', 'drop_chance').": $item[1]%<br />".lang('creature', 'quest_drop_chance').": $item[2]%<br />".lang('creature', 'drop_chance').": $item[3]-$item[4]<br />".lang('creature', 'lootcondition').": $item[5]<br />".lang('creature', 'condition_value1').": $item[6]<br />".lang('creature', 'condition_value2').": $item[7]";
    $output .= "<td>";
    $output .= maketooltip("<img src=\"".get_item_icon($item[0])."\" class=\"icon_border\" alt=\"\" />", "$item_datasite$item[0]", $tooltip, "item_tooltip");
    $output .= "<br /><input type=\"checkbox\" name=\"del_skin_items[]\" value=\"$item[0]\" /></td>";

    if ($cel_counter >= 16) {
      $cel_counter = 0;
      $output .= "</tr><tr>";
      $row_flag++;
      }
  };
  if ($row_flag) $output .= "<td colspan=\"".(16 - $cel_counter)."\"></td>";
  $output .= "</td></tr></table>
 </td>
</tr>
<tr class=\"large_bold\"><td colspan=\"6\" class=\"hidden\" align=\"left\">".lang('creature', 'add_items_to_templ').":</td></tr>
<tr>
<td>".makeinfocell(lang('creature', 'loot_item_id'),lang('creature', 'loot_item_id_desc'))."</td>
  <td><input type=\"text\" name=\"skin_item\" size=\"8\" maxlength=\"10\" value=\"\" /></td>
<td>".makeinfocell(lang('creature', 'loot_drop_chance'),lang('creature', 'loot_drop_chance_desc'))."</td>
  <td><input type=\"text\" name=\"skin_ChanceOrQuestChance\" size=\"8\" maxlength=\"11\" value=\"0\" /></td>
<td>".makeinfocell(lang('creature', 'loot_quest_drop_chance'),lang('creature', 'loot_quest_drop_chance_desc'))."</td>
  <td><input type=\"text\" name=\"skin_groupid\" size=\"8\" maxlength=\"10\" value=\"0\" /></td>
</tr>
<tr>
<td>".makeinfocell(lang('creature', 'min_count'),lang('creature', 'min_count_desc'))."</td>
  <td><input type=\"text\" name=\"skin_mincountOrRef\" size=\"8\" maxlength=\"3\" value=\"1\" /></td>
<td>".makeinfocell(lang('creature', 'max_count'),lang('creature', 'max_count_desc'))."</td>
  <td><input type=\"text\" name=\"skin_maxcount\" size=\"8\" maxlength=\"3\" value=\"1\" /></td>
</tr>
<tr>
<td>".makeinfocell(lang('creature', 'lootcondition'),lang('creature', 'lootcondition_desc'))."</td>
  <td><input type=\"text\" name=\"skin_lootcondition\" size=\"8\" maxlength=\"3\" value=\"0\" /></td>
<td>".makeinfocell(lang('creature', 'condition_value1'),lang('creature', 'condition_value1_desc'))."</td>
  <td><input type=\"text\" name=\"skin_condition_value1\" size=\"8\" maxlength=\"3\" value=\"0\" /></td>
<td>".makeinfocell(lang('creature', 'condition_value2'),lang('creature', 'condition_value2_desc'))."</td>
  <td><input type=\"text\" name=\"skin_condition_value2\" size=\"8\" maxlength=\"3\" value=\"0\" /></td>
</tr>
</table><br />".lang('creature', 'check_to_delete')."<br /><br />
</div>";

}

if ($mob['pickpocketloot']) {
$output .= "<div id=\"pane10\">
  <br /><br /><table class=\"lined\" id=\"ch_cre_proto\">
  <tr class=\"large_bold\"><td colspan=\"6\" class=\"hidden\" align=\"left\">".lang('creature', 'pickpocketloot_tmpl_id').": {$mob['pickpocketloot']}</td></tr>
  <tr><td colspan=\"6\">";

  $cel_counter = 0;
  $row_flag = 0;
  $output .= "<table class=\"hidden\" align=\"center\"><tr>";
  $result1 = $sqlw->query("SELECT item,ChanceOrQuestChance,`groupid`,mincountOrRef,maxcount, lootcondition, condition_value1, condition_value2 FROM pickpocketing_loot_template WHERE entry = {$mob['pickpocketloot']} ORDER BY ChanceOrQuestChance DESC");
  while ($item = $sqlw->fetch_row($result1)){
    $cel_counter++;
    $tooltip = get_item_name($item[0])." ($item[0])<br />".lang('creature', 'drop_chance').": $item[1]%<br />".lang('creature', 'quest_drop_chance').": $item[2]%<br />".lang('creature', 'drop_chance').": $item[3]-$item[4]<br />".lang('creature', 'lootcondition').": $item[5]<br />".lang('creature', 'condition_value1').": $item[6]<br />".lang('creature', 'condition_value2').": $item[7]";
    $output .= "<td>";
    $output .= maketooltip("<img src=\"".get_item_icon($item[0])."\" class=\"icon_border\" alt=\"\" />", "$item_datasite$item[0]", $tooltip, "item_tooltip");
    $output .= "<br /><input type=\"checkbox\" name=\"del_pp_items[]\" value=\"$item[0]\" /></td>";

    if ($cel_counter >= 16) {
      $cel_counter = 0;
      $output .= "</tr><tr>";
      $row_flag++;
      }
  };
  if ($row_flag) $output .= "<td colspan=\"".(16 - $cel_counter)."\"></td>";
  $output .= "</td></tr></table>
 </td>
</tr>
<tr class=\"large_bold\"><td colspan=\"6\" class=\"hidden\" align=\"left\">".lang('creature', 'add_items_to_templ').":</td></tr>
<tr>
<td>".makeinfocell(lang('creature', 'loot_item_id'),lang('creature', 'loot_item_id_desc'))."</td>
  <td><input type=\"text\" name=\"pp_item\" size=\"8\" maxlength=\"10\" value=\"\" /></td>
<td>".makeinfocell(lang('creature', 'loot_drop_chance'),lang('creature', 'loot_drop_chance_desc'))."</td>
  <td><input type=\"text\" name=\"pp_ChanceOrQuestChance\" size=\"8\" maxlength=\"11\" value=\"0\" /></td>
<td>".makeinfocell(lang('creature', 'loot_quest_drop_chance'),lang('creature', 'loot_quest_drop_chance_desc'))."</td>
  <td><input type=\"text\" name=\"pp_groupid\" size=\"8\" maxlength=\"10\" value=\"0\" /></td>
</tr>
<tr>
<td>".makeinfocell(lang('creature', 'min_count'),lang('creature', 'min_count_desc'))."</td>
  <td><input type=\"text\" name=\"pp_mincountOrRef\" size=\"8\" maxlength=\"3\" value=\"1\" /></td>
<td>".makeinfocell(lang('creature', 'max_count'),lang('creature', 'max_count_desc'))."</td>
  <td><input type=\"text\" name=\"pp_maxcount\" size=\"8\" maxlength=\"3\" value=\"1\" /></td>
</tr>
<tr>
<td>".makeinfocell(lang('creature', 'lootcondition'),lang('creature', 'lootcondition_desc'))."</td>
  <td><input type=\"text\" name=\"pp_lootcondition\" size=\"8\" maxlength=\"3\" value=\"0\" /></td>
<td>".makeinfocell(lang('creature', 'condition_value1'),lang('creature', 'condition_value1_desc'))."</td>
  <td><input type=\"text\" name=\"pp_condition_value1\" size=\"8\" maxlength=\"3\" value=\"0\" /></td>
<td>".makeinfocell(lang('creature', 'condition_value2'),lang('creature', 'condition_value2_desc'))."</td>
  <td><input type=\"text\" name=\"pp_condition_value2\" size=\"8\" maxlength=\"3\" value=\"0\" /></td>
</tr>
</table><br />".lang('creature', 'check_to_delete')."<br /><br />
</div>";
}

$output .= "</div>
</div>
<br />
</form>

<script type=\"text/javascript\">setupPanes(\"container\", \"tab1\")</script>
<table class=\"hidden\">
          <tr><td>";

if($do_insert) {
  //if ($user_lvl >= $action_permission['insert'] && $do_insert) makebutton($lang_creature['save_to_db'], "javascript:do_submit('form1',0)",180);
}
else {
  //if ($user_lvl >= $action_permission['insert']) makebutton($lang_creature['save_to_db'], "javascript:do_submit('form1',0)",180);
  if ($user_lvl >= $action_permission['delete']) makebutton(lang('creature', 'del_creature'), "creature.php?action=delete&amp;entry=$entry",180);
  if ($user_lvl >= $action_permission['delete']) makebutton(lang('creature', 'del_spawns'), "creature.php?action=delete_spwn&amp;entry=$entry",180);
}

  // scripts/export should be okay without permission check
       //makebutton($lang_creature['save_to_script'], "javascript:do_submit('form1',1)",180);
 $output .= "</td></tr><tr><td>";
       makebutton(lang('creature', 'lookup_creature'), "creature.php",760);
 $output .= "</td></tr>
        </table></center>";

 //$sql->close();
 //unset($sql);
 } else {
    //$sql->close();
    //unset($sql);
    error(lang('creature', 'item_not_found'));
    exit();
    }
}


//########################################################################################################################
//DO UPDATE CREATURE TEMPLATE
//########################################################################################################################

function do_update() {
 global $world_db, $realm_id, $action_permission, $user_lvl, $locales_search_option, $sqlw;

 // on update, use replace.. and else insert
  if ($_POST['insert'] == "1") {
    if (  $user_lvl < $action_permission['insert'] ) redirect("creature.php?error=9");
    $db_action_creature = "INSERT";
  }
  else {
    if (  $user_lvl < $action_permission['update'] ) redirect("creature.php?error=9");
    $db_action_creature = "REPLACE";
  }
  if ( ($del_trainer_spell || $del_loot_items || $del_skin_items || $del_pp_items || $del_questrelation || $del_involvedrelation || $del_vendor_item )
       && $user_lvl < $action_permission['delete'] )
         redirect("creature.php?error=9");

 $deplang = get_lang_id();

 if (!isset($_POST['entry']) || $_POST['entry'] === '') redirect("creature.php?error=1");

 $entry = $sqlw->quote_smart($_POST['entry']);
   if (isset($_POST['heroic_entry']) && $_POST['heroic_entry'] != '') $modelid_A = $sqlw->quote_smart($_POST['heroic_entry']);
     else $heroic_entry = 0;
   if (isset($_POST['modelid_A']) && $_POST['modelid_A'] != '') $modelid_A = $sqlw->quote_smart($_POST['modelid_A']);
     else $modelid_A = 0;
   if (isset($_POST['modelid_H']) && $_POST['modelid_H'] != '') $modelid_H = $sqlw->quote_smart($_POST['modelid_H']);
     else $modelid_H = 0;
   if (isset($_POST['name']) && $_POST['name'] != '') $name = $sqlw->quote_smart($_POST['name']);
     else $name = "";
   if (isset($_POST['subname']) && $_POST['subname'] != '') $subname = $sqlw->quote_smart($_POST['subname']);
    else $subname = "";
   if (isset($_POST['minlevel']) && $_POST['minlevel'] != '') $minlevel = $sqlw->quote_smart($_POST['minlevel']);
    else $minlevel = 0;
   if (isset($_POST['maxlevel']) && $_POST['maxlevel'] != '') $maxlevel = $sqlw->quote_smart($_POST['maxlevel']);
    else $maxlevel = 0;
   if (isset($_POST['minhealth']) && $_POST['minhealth'] != '') $minhealth = $sqlw->quote_smart($_POST['minhealth']);
    else $minhealth = 0;
   if (isset($_POST['maxhealth']) && $_POST['maxhealth'] != '') $maxhealth = $sqlw->quote_smart($_POST['maxhealth']);
    else $maxhealth = 0;
   if (isset($_POST['minmana']) && $_POST['minmana'] != '') $minmana = $sqlw->quote_smart($_POST['minmana']);
    else $minmana = 0;
   if (isset($_POST['maxmana']) && $_POST['maxmana'] != '') $maxmana = $sqlw->quote_smart($_POST['maxmana']);
    else $maxmana = 0;
   if (isset($_POST['armor']) && $_POST['armor'] != '') $armor = $sqlw->quote_smart($_POST['armor']);
    else $armor = 0;
   if (isset($_POST['faction_A']) && $_POST['faction_A'] != '') $faction_A = $sqlw->quote_smart($_POST['faction_A']);
    else $faction_A = 0;
   if (isset($_POST['faction_H']) && $_POST['faction_H'] != '') $faction_H = $sqlw->quote_smart($_POST['faction_H']);
    else $faction_H = 0;
   if (isset($_POST['npcflag'])) $npcflag = $sqlw->quote_smart($_POST['npcflag']);
    else $npcflag = 0;
   if (isset($_POST['speed']) && $_POST['speed'] != '') $speed = $sqlw->quote_smart($_POST['speed']);
    else $speed = 0;
   if (isset($_POST['rank']) && $_POST['rank'] != '') $rank = $sqlw->quote_smart($_POST['rank']);
    else $rank = 0;
   if (isset($_POST['mindmg']) && $_POST['mindmg'] != '') $mindmg = $sqlw->quote_smart($_POST['mindmg']);
    else $mindmg = 0;
   if (isset($_POST['maxdmg']) && $_POST['maxdmg'] != '') $maxdmg = $sqlw->quote_smart($_POST['maxdmg']);
    else $maxdmg = 0;
   if (isset($_POST['dmgschool']) && $_POST['dmgschool'] != '') $dmgschool = $sqlw->quote_smart($_POST['dmgschool']);
    else $dmgschool = 0;
   if (isset($_POST['attackpower']) && $_POST['attackpower'] != '') $attackpower = $sqlw->quote_smart($_POST['attackpower']);
    else $attackpower = 0;
   if (isset($_POST['baseattacktime']) && $_POST['baseattacktime'] != '') $baseattacktime = $sqlw->quote_smart($_POST['baseattacktime']);
    else $baseattacktime = 0;
   if (isset($_POST['rangeattacktime']) && $_POST['rangeattacktime'] != '') $rangeattacktime = $sqlw->quote_smart($_POST['rangeattacktime']);
    else $rangeattacktime = 0;
   if (isset($_POST['unit_flags']) && $_POST['unit_flags'] != '') $unit_flags = $sqlw->quote_smart($_POST['unit_flags']);
    else $unit_flags = 0;
   if (isset($_POST['dynamicflags']) && $_POST['dynamicflags'] != '') $dynamicflags = $sqlw->quote_smart($_POST['dynamicflags']);
    else $dynamicflags = 0;
   if (isset($_POST['family']) && $_POST['family'] != '') $family = $sqlw->quote_smart($_POST['family']);
    else $family = 0;
   if (isset($_POST['trainer_type']) && $_POST['trainer_type'] != '') $trainer_type = $sqlw->quote_smart($_POST['trainer_type']);
    else $trainer_type = 0;
   if (isset($_POST['trainer_spell']) && $_POST['trainer_spell'] != '') $trainer_spell = $sqlw->quote_smart($_POST['trainer_spell']);
    else $trainer_spell = 0;
   if (isset($_POST['trainer_class']) && $_POST['trainer_class'] != '') $trainer_class = $sqlw->quote_smart($_POST['trainer_class']);
    else $trainer_class = 0;
   if (isset($_POST['trainer_race']) && $_POST['trainer_race'] != '') $trainer_race = $sqlw->quote_smart($_POST['trainer_race']);
    else $trainer_race = 0;
   if (isset($_POST['minrangedmg']) && $_POST['minrangedmg'] != '') $minrangedmg = $sqlw->quote_smart($_POST['minrangedmg']);
    else $minrangedmg = 0;
   if (isset($_POST['maxrangedmg']) && $_POST['maxrangedmg'] != '') $maxrangedmg = $sqlw->quote_smart($_POST['maxrangedmg']);
    else $maxrangedmg = 0;
   if (isset($_POST['rangedattackpower']) && $_POST['rangedattackpower'] != '') $rangedattackpower = $sqlw->quote_smart($_POST['rangedattackpower']);
    else $rangedattackpower = 0;
   if (isset($_POST['combat_reach']) && $_POST['combat_reach'] != '') $combat_reach = $sqlw->quote_smart($_POST['combat_reach']);
    else $combat_reach = 0;
   if (isset($_POST['type']) && $_POST['type'] != '') $type = $sqlw->quote_smart($_POST['type']);
    else $type = 0;
   if (isset($_POST['flags_extra']) && $_POST['flags_extra'] != '') $flags_extra = $sqlw->quote_smart($_POST['flags_extra']);
       else $flags_extra = 0;
   if (isset($_POST['type_flags']) && $_POST['type_flags'] != '') $type_flags = $sqlw->quote_smart($_POST['type_flags']);
    else $type_flags = 0;
   if (isset($_POST['lootid']) && $_POST['lootid'] != '') $lootid = $sqlw->quote_smart($_POST['lootid']);
     else $lootid = 0;
   if (isset($_POST['pickpocketloot']) && $_POST['pickpocketloot'] != '') $pickpocketloot = $sqlw->quote_smart($_POST['pickpocketloot']);
    else $pickpocketloot = 0;
   if (isset($_POST['skinloot']) && $_POST['skinloot'] != '') $skinloot = $sqlw->quote_smart($_POST['skinloot']);
    else $skinloot = 0;
   if (isset($_POST['resistance1']) && $_POST['resistance1'] != '') $resistance1 = $sqlw->quote_smart($_POST['resistance1']);
    else $resistance1 = 0;
   if (isset($_POST['resistance2']) && $_POST['resistance2'] != '') $resistance2 = $sqlw->quote_smart($_POST['resistance2']);
    else $resistance2 = 0;
   if (isset($_POST['resistance3']) && $_POST['resistance3'] != '') $resistance3 = $sqlw->quote_smart($_POST['resistance3']);
    else $resistance3 = 0;
   if (isset($_POST['resistance4']) && $_POST['resistance4'] != '') $resistance4 = $sqlw->quote_smart($_POST['resistance4']);
    else $resistance4 = 0;
   if (isset($_POST['resistance5']) && $_POST['resistance5'] != '') $resistance5 = $sqlw->quote_smart($_POST['resistance5']);
    else $resistance5 = 0;
   if (isset($_POST['resistance6']) && $_POST['resistance6'] != '') $resistance6 = $sqlw->quote_smart($_POST['resistance6']);
    else $resistance6 = 0;
   if (isset($_POST['spell1']) && $_POST['spell1'] != '') $spell1 = $sqlw->quote_smart($_POST['spell1']);
    else $spell1 = 0;
   if (isset($_POST['spell2']) && $_POST['spell2'] != '') $spell2 = $sqlw->quote_smart($_POST['spell2']);
    else $spell2 = 0;
   if (isset($_POST['spell3']) && $_POST['spell3'] != '') $spell3 = $sqlw->quote_smart($_POST['spell3']);
    else $spell3 = 0;
   if (isset($_POST['spell4']) && $_POST['spell4'] != '') $spell4 = $sqlw->quote_smart($_POST['spell4']);
    else $spell4 = 0;
   if (isset($_POST['mingold']) && $_POST['mingold'] != '') $mingold = $sqlw->quote_smart($_POST['mingold']);
    else $mingold = 0;
   if (isset($_POST['maxgold']) && $_POST['maxgold'] != '') $maxgold = $sqlw->quote_smart($_POST['maxgold']);
    else $maxgold = 0;
   if (isset($_POST['AIName']) && $_POST['AIName'] != '') $AIName = $sqlw->quote_smart($_POST['AIName']);
    else $AIName = "";
   if (isset($_POST['MovementType']) && $_POST['MovementType'] != '') $MovementType = $sqlw->quote_smart($_POST['MovementType']);
    else $MovementType = 0;
   if (isset($_POST['InhabitType']) && $_POST['InhabitType'] != '') $InhabitType = $sqlw->quote_smart($_POST['InhabitType']);
    else $InhabitType = 0;
   if (isset($_POST['ScriptName']) && $_POST['ScriptName'] != '') $ScriptName = $sqlw->quote_smart($_POST['ScriptName']);
    else $ScriptName = "";
   if (isset($_POST['RacialLeader']) && $_POST['RacialLeader'] != '') $RacialLeader = $sqlw->quote_smart($_POST['RacialLeader']);
    else $RacialLeader = 0;

  if (isset($_POST['ChanceOrQuestChance']) && $_POST['ChanceOrQuestChance'] != '') $ChanceOrQuestChance = $sqlw->quote_smart($_POST['ChanceOrQuestChance']);
    else $ChanceOrQuestChance = 0;
  if (isset($_POST['groupid']) && $_POST['groupid'] != '') $groupid = $sqlw->quote_smart($_POST['groupid']);
    else $groupid = 0;
  if (isset($_POST['mincountOrRef']) && $_POST['mincountOrRef'] != '') $mincountOrRef = $sqlw->quote_smart($_POST['mincountOrRef']);
    else $mincountOrRef = 0;
  if (isset($_POST['maxcount']) && $_POST['maxcount'] != '') $maxcount = $sqlw->quote_smart($_POST['maxcount']);
    else $maxcount = 0;

  if (isset($_POST['lootcondition']) && $_POST['lootcondition'] != '') $lootcondition = $sqlw->quote_smart($_POST['lootcondition']);
    else $lootcondition = 0;
  if (isset($_POST['condition_value1']) && $_POST['condition_value1'] != '') $condition_value1 = $sqlw->quote_smart($_POST['condition_value1']);
    else $condition_value1 = 0;
  if (isset($_POST['condition_value2']) && $_POST['condition_value2'] != '') $condition_value2 = $sqlw->quote_smart($_POST['condition_value2']);
    else $condition_value2 = 0;
  if (isset($_POST['item']) && $_POST['item'] != '') $item = $sqlw->quote_smart($_POST['item']);
    else $item = 0;
  if (isset($_POST['del_loot_items']) && $_POST['del_loot_items'] != '') $del_loot_items = $sqlw->quote_smart($_POST['del_loot_items']);
    else $del_loot_items = NULL;

  if (isset($_POST['involvedrelation']) && $_POST['involvedrelation'] != '') $involvedrelation = $sqlw->quote_smart($_POST['involvedrelation']);
    else $involvedrelation = 0;
  if (isset($_POST['del_involvedrelation']) && $_POST['del_involvedrelation'] != '') $del_involvedrelation = $sqlw->quote_smart($_POST['del_involvedrelation']);
    else $del_involvedrelation = NULL;
  if (isset($_POST['questrelation']) && $_POST['questrelation'] != '') $questrelation = $sqlw->quote_smart($_POST['questrelation']);
    else $questrelation = 0;
  if (isset($_POST['del_questrelation']) && $_POST['del_questrelation'] != '') $del_questrelation = $sqlw->quote_smart($_POST['del_questrelation']);
    else $del_questrelation = NULL;

  if (isset($_POST['del_vendor_item']) && $_POST['del_vendor_item'] != '') $del_vendor_item = $sqlw->quote_smart($_POST['del_vendor_item']);
    else $del_vendor_item = NULL;
  if (isset($_POST['vendor_item']) && $_POST['vendor_item'] != '') $vendor_item = $sqlw->quote_smart($_POST['vendor_item']);
    else $vendor_item = 0;
  if (isset($_POST['vendor_maxcount']) && $_POST['vendor_maxcount'] != '') $vendor_maxcount = $sqlw->quote_smart($_POST['vendor_maxcount']);
    else $vendor_maxcount = 0;
  if (isset($_POST['vendor_incrtime']) && $_POST['vendor_incrtime'] != '') $vendor_incrtime = $sqlw->quote_smart($_POST['vendor_incrtime']);
    else $vendor_incrtime = 0;
  if (isset($_POST['vendor_extended_cost']) && $_POST['vendor_extended_cost'] != '') $vendor_extended_cost = $sqlw->quote_smart($_POST['vendor_extended_cost']);
    else $vendor_extended_cost = 0;

  if (isset($_POST['skin_ChanceOrQuestChance']) && $_POST['skin_ChanceOrQuestChance'] != '') $skin_ChanceOrQuestChance = $sqlw->quote_smart($_POST['skin_ChanceOrQuestChance']);
    else $skin_ChanceOrQuestChance = 0;
  if (isset($_POST['skin_groupid']) && $_POST['skin_groupid'] != '') $skin_groupid = $sqlw->quote_smart($_POST['skin_groupid']);
    else $skin_groupid = 0;
  if (isset($_POST['skin_mincountOrRef']) && $_POST['skin_mincountOrRef'] != '') $skin_mincountOrRef = $sqlw->quote_smart($_POST['skin_mincountOrRef']);
    else $skin_mincountOrRef = 0;
  if (isset($_POST['skin_maxcount']) && $_POST['skin_maxcount'] != '') $skin_maxcount = $sqlw->quote_smart($_POST['skin_maxcount']);
    else $skin_maxcount = 0;

  if (isset($_POST['skin_lootcondition']) && $_POST['skin_lootcondition'] != '') $skin_lootcondition = $sqlw->quote_smart($_POST['skin_lootcondition']);
    else $skin_lootcondition = 0;
  if (isset($_POST['skin_condition_value1']) && $_POST['skin_condition_value1'] != '') $skin_condition_value1 = $sqlw->quote_smart($_POST['skin_condition_value1']);
    else $skin_condition_value1 = 0;
  if (isset($_POST['skin_condition_value2']) && $_POST['skin_condition_value2'] != '') $skin_condition_value2 = $sqlw->quote_smart($_POST['skin_condition_value2']);
    else $skin_condition_value2 = 0;

  if (isset($_POST['skin_item']) && $_POST['skin_item'] != '') $skin_item = $sqlw->quote_smart($_POST['skin_item']);
    else $skin_item = 0;
  if (isset($_POST['del_skin_items']) && $_POST['del_skin_items'] != '') $del_skin_items = $sqlw->quote_smart($_POST['del_skin_items']);
    else $del_skin_items = NULL;

  if (isset($_POST['pp_ChanceOrQuestChance']) && $_POST['pp_ChanceOrQuestChance'] != '') $pp_ChanceOrQuestChance = $sqlw->quote_smart($_POST['pp_ChanceOrQuestChance']);
    else $pp_ChanceOrQuestChance = 0;
  if (isset($_POST['pp_groupid']) && $_POST['pp_groupid'] != '') $pp_groupid = $sqlw->quote_smart($_POST['pp_groupid']);
    else $pp_groupid = 0;
  if (isset($_POST['pp_mincountOrRef']) && $_POST['pp_mincountOrRef'] != '') $pp_mincountOrRef = $sqlw->quote_smart($_POST['pp_mincountOrRef']);
    else $pp_mincountOrRef = 0;
  if (isset($_POST['pp_maxcount']) && $_POST['pp_maxcount'] != '') $pp_maxcount = $sqlw->quote_smart($_POST['pp_maxcount']);
    else $pp_maxcount = 0;

  if (isset($_POST['pp_lootcondition']) && $_POST['pp_lootcondition'] != '') $pp_lootcondition = $sqlw->quote_smart($_POST['pp_lootcondition']);
    else $pp_lootcondition = 0;
  if (isset($_POST['pp_condition_value1']) && $_POST['pp_condition_value1'] != '') $pp_condition_value1 = $sqlw->quote_smart($_POST['pp_condition_value1']);
    else $pp_condition_value1 = 0;
  if (isset($_POST['pp_condition_value2']) && $_POST['pp_condition_value2'] != '') $pp_condition_value2 = $sqlw->quote_smart($_POST['pp_condition_value2']);
    else $pp_condition_value2 = 0;
  if (isset($_POST['pp_item']) && $_POST['pp_item'] != '') $pp_item = $sqlw->quote_smart($_POST['pp_item']);
    else $pp_item = 0;
  if (isset($_POST['del_pp_items']) && $_POST['del_pp_items'] != '') $del_pp_items = $sqlw->quote_smart($_POST['del_pp_items']);
    else $del_pp_items = NULL;

  if (isset($_POST['trainer_spell']) && $_POST['trainer_spell'] != '') $trainer_spell = $sqlw->quote_smart($_POST['trainer_spell']);
    else $trainer_spell = 0;
  if (isset($_POST['spellcost']) && $_POST['spellcost'] != '') $spellcost = $sqlw->quote_smart($_POST['spellcost']);
    else $spellcost = 0;
  if (isset($_POST['reqskill']) && $_POST['reqskill'] != '') $reqskill = $sqlw->quote_smart($_POST['reqskill']);
    else $reqskill = 0;
  if (isset($_POST['reqskillvalue']) && $_POST['reqskillvalue'] != '') $reqskillvalue = $sqlw->quote_smart($_POST['reqskillvalue']);
    else $reqskillvalue = 0;
  if (isset($_POST['reqlevel']) && $_POST['reqlevel'] != '') $reqlevel = $sqlw->quote_smart($_POST['reqlevel']);
    else $reqlevel = 0;
  if (isset($_POST['del_trainer_spell']) && $_POST['del_trainer_spell'] != '') $del_trainer_spell = $sqlw->quote_smart($_POST['del_trainer_spell']);
    else $del_trainer_spell = NULL;

 if ($locales_search_option != 0) {
  // locales
  for ($lc = 1; $lc<9; $lc++) {
    if (isset($_POST['name_loc'.$lc]) && $_POST['name_loc'.$lc] != '' && !preg_match('/^[\t\v\b\f\a\n\r\\\"\? <>[](){}_=+-|!@#$%^&*~`.,\0]{1,30}$/', $_POST['name_loc'.$lc])) {
       $name_loc[$lc] = $sqlw->quote_smart($_POST['name_loc'.$lc]);
    }
    else $name_loc[$lc] = '';
    if (isset($_POST['subname_loc'.$lc]) && $_POST['subname_loc'.$lc] != '' && !preg_match('/^[\t\v\b\f\a\n\r\\\"\? <>[](){}_=+-|!@#$%^&*~`.,\0]{1,30}$/', $_POST['subname_loc'.$lc])) {
       $subname_loc[$lc] = $sqlw->quote_smart($_POST['subname_loc'.$lc]);
    }
    else $subname_loc[$lc] = '';
  }
}

  $tmp = 0;
  for ($t = 0; $t < count($npcflag); $t++){
    if ($npcflag[$t] & 1) $tmp = $tmp + 1;
    if ($npcflag[$t] & 2) $tmp = $tmp + 2;
    if ($npcflag[$t] & 16) $tmp = $tmp + 16;
    if ($npcflag[$t] & 128) $tmp = $tmp + 128;
    if ($npcflag[$t] & 4096) $tmp = $tmp + 4096;
    if ($npcflag[$t] & 8192) $tmp = $tmp + 8192;
    if ($npcflag[$t] & 16384) $tmp = $tmp + 16384;
    if ($npcflag[$t] & 65536) $tmp = $tmp + 65536;
    if ($npcflag[$t] & 131072) $tmp = $tmp + 131072;
    if ($npcflag[$t] & 262144) $tmp = $tmp + 262144;
    if ($npcflag[$t] & 524288) $tmp = $tmp + 524288;
    if ($npcflag[$t] & 1048576) $tmp = $tmp + 1048576;
    if ($npcflag[$t] & 2097152) $tmp = $tmp + 2097152;
    if ($npcflag[$t] & 4194304) $tmp = $tmp + 4194304;
    if ($npcflag[$t] & 268435456) $tmp = $tmp + 268435456;
    }
  $npcflag = ($tmp) ? $tmp : 0;

  // insert or update creature
  $sql_query = "{$db_action_creature} INTO creature_template ( entry, heroic_entry, modelid_A, modelid_H, name, subname, minlevel,
                maxlevel, minhealth, maxhealth, minmana, maxmana, armor, faction_A, faction_H, npcflag, speed, rank, mindmg,
                maxdmg, dmgschool, attackpower, baseattacktime, rangeattacktime, unit_flags, dynamicflags, family,
                trainer_type, trainer_spell, trainer_class, trainer_race, minrangedmg, maxrangedmg, rangedattackpower,
                type, flags_extra, type_flags, lootid, pickpocketloot, skinloot, resistance1,
                resistance2, resistance3, resistance4, resistance5, resistance6, spell1, spell2, spell3, spell4,
                mingold, maxgold, AIName, MovementType, InhabitType, RacialLeader, ScriptName) VALUES ( '$entry', '$heroic_entry', '$modelid_A', '$modelid_H', '$name',
                '$subname', '$minlevel', '$maxlevel', '$minhealth', '$maxhealth', '$minmana', '$maxmana', '$armor', '$faction_A', '$faction_A',  '$npcflag',
                '$speed', '$rank', '$mindmg', '$maxdmg', '$dmgschool', '$attackpower', '$baseattacktime', '$rangeattacktime', '$unit_flags',
                '$dynamicflags', '$family', '$trainer_type', '$trainer_spell', '$trainer_class', '$trainer_race',
                '$minrangedmg', '$maxrangedmg', '$rangedattackpower', '$type', '$flags_extra', '$type_flags',
                '$lootid', '$pickpocketloot', '$skinloot', '$resistance1', '$resistance2',
                '$resistance3', '$resistance4', '$resistance5', '$resistance6', '$spell1', '$spell2', '$spell3', '$spell4',
                '$mingold', '$maxgold', '$AIName', '$MovementType', '$InhabitType', '$RacialLeader', '$ScriptName' );\n";


  if ($trainer_spell){
  $sql_query .= "{$db_action_creature} INTO npc_trainer (entry, spell, spellcost, reqskill, reqskillvalue, reqlevel)
      VALUES ($entry,$trainer_spell,$spellcost,$reqskill ,$reqskillvalue ,$reqlevel);\n";
  }

  if ($del_trainer_spell){
  foreach($del_trainer_spell as $spell_id)
    $sql_query .= "DELETE FROM npc_trainer WHERE entry = $entry AND spell = $spell_id;\n";
  }

  if ($item){
  $sql_query .= "{$db_action_creature} INTO creature_loot_template (entry, item, ChanceOrQuestChance, `groupid`, mincountOrRef, maxcount, lootcondition, condition_value1, condition_value2)
      VALUES ($lootid,$item,'$ChanceOrQuestChance', '$groupid' ,$mincountOrRef ,$maxcount ,$lootcondition ,$condition_value1 ,$condition_value2);\n";
  }

  if ($del_loot_items){
  foreach($del_loot_items as $item_id)
    $sql_query .= "DELETE FROM creature_loot_template WHERE entry = $lootid AND item = $item_id;\n";
  }

  if ($skin_item){
  $sql_query .= "{$db_action_creature} INTO skinning_loot_template (entry, item, ChanceOrQuestChance, `groupid`, mincountOrRef, maxcount, lootcondition, condition_value1, condition_value2)
      VALUES ($skinloot,$skin_item,'$skin_ChanceOrQuestChance', '$skin_groupid' ,$skin_mincountOrRef ,$skin_maxcount ,$skin_lootcondition ,$skin_condition_value1 ,$skin_condition_value2);\n";
  }

  if ($del_skin_items){
  foreach($del_skin_items as $item_id)
    $sql_query .= "DELETE FROM skinning_loot_template WHERE entry = $skinloot AND item = $item_id;\n";
  }

  if ($pp_item){
  $sql_query .= "{$db_action_creature} INTO pickpocketing_loot_template (entry, item, ChanceOrQuestChance, `groupid`, mincountOrRef, maxcount, lootcondition, condition_value1, condition_value2)
      VALUES ($pickpocketloot,$pp_item,'$pp_ChanceOrQuestChance', '$pp_groupid' ,$pp_mincountOrRef ,$pp_maxcount ,$pp_lootcondition ,$pp_condition_value1 ,$pp_condition_value2);\n";
  }

  if ($del_pp_items){
  foreach($del_pp_items as $item_id)
    $sql_query .= "DELETE FROM pickpocketing_loot_template WHERE entry = $pickpocketloot AND item = $item_id;\n";
  }

  if ($questrelation){
  $sql_query .= "{$db_action_creature} INTO creature_questrelation (id, quest) VALUES ($entry,$questrelation);\n";
  }

  if ($involvedrelation){
  $sql_query .= "{$db_action_creature} INTO creature_involvedrelation (id, quest) VALUES ($entry,$involvedrelation);\n";
  }

  if ($del_questrelation){
  foreach($del_questrelation as $quest_id)
    $sql_query .= "DELETE FROM creature_questrelation WHERE id = $entry AND quest = $quest_id;\n";
  }

  if ($del_involvedrelation){
  foreach($del_involvedrelation as $quest_id)
    $sql_query .= "DELETE FROM creature_involvedrelation WHERE id = $entry AND quest = $quest_id;\n";
  }

  if ($del_vendor_item){
  foreach($del_vendor_item as $item_id)
    $sql_query .= "DELETE FROM npc_vendor WHERE entry = $entry AND item = $item_id;\n";
  }

  if ($vendor_item){
  $sql_query .= "{$db_action_creature} INTO npc_vendor (entry, item, maxcount, incrtime, ExtendedCost)
      VALUES ($entry,$vendor_item,$vendor_maxcount,$vendor_incrtime,$vendor_extended_cost);\n";
  }

  if ($locales_search_option != 0){
    if ($name_loc) {
      $sql_query .= "{$db_action_creature} INTO locales_creature (`entry`,  `name_loc1`, `name_loc2`, `name_loc3`, `name_loc4`, `name_loc5`, `name_loc6`, `name_loc7`, `name_loc8`, `subname_loc1`, `subname_loc2`, `subname_loc3`, `subname_loc4`, `subname_loc5`, `subname_loc6`, `subname_loc7`, `subname_loc8`) VALUES
                     ('$entry', '$name_loc[1]', '$name_loc[2]', '$name_loc[3]', '$name_loc[4]', '$name_loc[5]', '$name_loc[6]', '$name_loc[7]', '$name_loc[8]', '$subname_loc[1]', '$subname_loc[2]', '$subname_loc[3]', '$subname_loc[4]', '$subname_loc[5]', '$subname_loc[6]', '$subname_loc[7]', '$subname_loc[8]');\n";
    }
  }

 if ( isset($_POST['backup_op']) && ($_POST['backup_op'] == 1) ){
  $sqlw->close();
  Header("Content-type: application/octet-stream");
  Header("Content-Disposition: attachment; filename=creatureid_$entry.sql");
  echo $sql_query;
  exit();
  redirect("creature.php?action=edit&entry=$entry&error=4");
  } else {
    $sql_query = explode(';',$sql_query);
    foreach($sql_query as $tmp_query) if(($tmp_query)&&($tmp_query != "\n")) $result = $sqlw->query($tmp_query);
    $sqlw->close();
    }

 if ($result) redirect("creature.php?action=edit&entry=$entry&error=4");
  else redirect("creature.php");

}


//#######################################################################################################
//  DELETE CREATURE TEMPLATE
//#######################################################################################################
function delete()
{
  global $output, $user_lvl, $action_permission;

  if ($user_lvl < $action_permission['delete'] ) redirect("creature.php?error=9");


  if(isset($_GET['entry'])) $entry = $_GET['entry'];
    else redirect("creature.php?error=1");


  $output .= "<center><h1><font class=\"error\">".lang('global', 'are_you_sure')."</font></h1><br />
      <font class=\"bold\">".lang('creature', 'creature_template').": <a href=\"creature.php?action=edit&amp;entry=$entry\" target=\"_blank\">$entry</a>
      ".lang('global', 'will_be_erased')."<br />".lang('creature', 'all_related_data')."</font><br /><br />
    <table class=\"hidden\">
          <tr>
            <td>";
      makebutton(lang('global', 'yes'), "creature.php?action=do_delete&amp;entry=$entry",120);
      makebutton(lang('global', 'no'), "creature.php",120);
  $output .= "</td>
          </tr>
        </table></center><br />";
}


//########################################################################################################################
//  DO DELETE CREATURE TEMPLATE
//########################################################################################################################
function do_delete() {
 global $world_db, $realm_id, $user_lvl, $action_permission, $sqlw;

 if ($user_lvl < $action_permission['delete'] ) redirect("creature.php?error=9");

 if(isset($_GET['entry'])) $entry = $_GET['entry'];
  else redirect("creature.php?error=1");

  $result = $sqlw->query("SELECT guid FROM creature WHERE id = '$entry'");
  while ($guid = $sqlw->fetch_row($result)){
  $sqlw->query("DELETE FROM creature_movement WHERE id = '$guid'");
  }
 $sqlw->query("DELETE FROM creature WHERE id = '$entry'");
 $sqlw->query("DELETE FROM creature_template WHERE entry = '$entry'");
 $sqlw->query("DELETE FROM creature_onkill_reputation WHERE creature_id = '$entry'");
 $sqlw->query("DELETE FROM creature_involvedrelation WHERE id = '$entry'");
 $sqlw->query("DELETE FROM creature_questrelation WHERE id = '$entry'");
 $sqlw->query("DELETE FROM npc_vendor WHERE entry = '$entry'");
 $sqlw->query("DELETE FROM npc_trainer WHERE entry = '$entry'");
 $sqlw->query("DELETE FROM npc_gossip WHERE npc_guid = '$entry'");

 $sqlw->close();
 redirect("creature.php");
 }


//########################################################################################################################
//   DELETE ALL CREATURE SPAWNS
//########################################################################################################################
function delete_spwn() {
 global $world_db, $realm_id, $user_lvl, $action_permission, $sqlw;

 if ($user_lvl < $action_permission['delete'] ) redirect("creature.php?error=9");

 if(isset($_GET['entry'])) $entry = $_GET['entry'];
  else redirect("creature.php?error=1");

 $result = $sqlw->query("SELECT guid FROM creature WHERE id = '$entry'");
 while ($guid = $sqlw->fetch_row($result)){
  $sqlw->query("DELETE FROM creature_movement WHERE id = '$guid'");
  }

 $sqlw->query("DELETE FROM creature WHERE id = '$entry'");
 $sqlw->close();
 redirect("creature.php?action=edit&entry=$entry&error=4");
 }


//########################################################################################################################
// MAIN
//########################################################################################################################
$err = (isset($_GET['error'])) ? $_GET['error'] : NULL;

$output .= "
    <div class=\"bubble\">
      <div class=\"top\">";
switch ($err) {
case 1:
   $output .= "<h1><font class=\"error\">".lang('global', 'empty_fields')."</font></h1>";
   break;
case 2:
   $output .= "<h1><font class=\"error\">".lang('creature', 'search_results')."</font></h1>";
   break;
case 3:
   $output .= "<h1><font class=\"error\">".lang('creature', 'add_new_mob_templ')."</font></h1>";
   break;
case 4:
   $output .= "<h1><font class=\"error\">".lang('creature', 'edit_mob_templ')."</font></h1>";
   break;
case 5:
   $output .= "<h1><font class=\"error\">".lang('creature', 'err_adding_new')."</font></h1>";
   break;
case 6:
   $output .= "<h1><font class=\"error\">".lang('creature', 'err_no_fields_updated')."</font></h1>";
   break;
case 7:
   $output .= "<h1><font class=\"error\">".lang('creature', 'add_new_success')."</font></h1>";
   break;
case 8:
   $output .= "<h1><font class=\"error\">".lang('global', 'err_invalid_input')."</font></h1>";
   break;
case 9:
   $output .= "<h1><font class=\"error\">".lang('global', 'err_no_permission')."</font></h1>";
   break;
default: //no error
    $output .= "<h1>".lang('creature', 'search_creatures')."</h1>";
}
$output .= "</div>";

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

switch ($action) {
case "search":
   search();
   break;
case "do_search":
   do_search();
   break;
case "add_new":
   do_insert_update(1);
   break;
case "do_update":
   do_update();
   break;
case "edit":
   do_insert_update(0);
   break;
case "delete":
   delete();
   break;
case "delete_spwn":
   delete_spwn();
   break;
case "do_delete":
   do_delete();
   break;
default:
    search();
}

require_once("footer.php");
?>
