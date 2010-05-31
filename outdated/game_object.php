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

//
// this_is_junk: this will require a rewrite to work with ArcEmu
//


require_once("header.php");
valid_login($action_permission['view']);

 $go_type = Array(
  0 => array(0,lang('game_object', 'DOOR')),
  1 => array(1,lang('game_object', 'BUTTON')),
  2 => array(2,lang('game_object', 'QUESTGIVER')),
  3 => array(3,lang('game_object', 'CHEST')),
  4 => array(4,lang('game_object', 'BINDER')),
  5 => array(5,lang('game_object', 'GENERIC')),
  6 => array(6,lang('game_object', 'TRAP')),
  7 => array(7,lang('game_object', 'CHAIR')),
  8 => array(8,lang('game_object', 'SPELL_FOCUS')),
  9 => array(9,lang('game_object', 'TEXT')),
  10 => array(10,lang('game_object', 'GOOBER')),
  11 => array(11,lang('game_object', 'TRANSPORT')),
  12 => array(12,lang('game_object', 'AREADAMAGE')),
  13 => array(13,lang('game_object', 'CAMERA')),
  14 => array(14,lang('game_object', 'MAP_OBJECT')),
  15 => array(15,lang('game_object', 'MO_TRANSPORT')),
  16 => array(16,lang('game_object', 'DUEL_FLAG')),
  17 => array(17,lang('game_object', 'FISHING_BOBBER')),
  18 => array(18,lang('game_object', 'RITUAL')),
  19 => array(19,lang('game_object', 'MAILBOX')),
  20 => array(20,lang('game_object', 'AUCTIONHOUSE')),
  21 => array(21,lang('game_object', 'GUARDPOST')),
  22 => array(22,lang('game_object', 'SPELLCASTER')),
  23 => array(23,lang('game_object', 'MEETING_STONE')),
  24 => array(24,lang('game_object', 'BG_Flag')),
  25 => array(25,lang('game_object', 'FISHING_HOLE')),
  26 => array(26,lang('game_object', 'FLAGDROP')),
  27 => array(27,lang('game_object', 'CUSTOM_TELEPORTER')),
  28 => array(28,lang('game_object', 'LOTTERY_KIOSK')),
  29 => array(29,lang('game_object', 'CAPTURE_POINT')),
  30 => array(30,lang('game_object', 'AURA_GENERATOR')),
  31 => array(31,lang('game_object', 'DUNGEON_DIFFICULTY'))
);

function get_go_type($flag){
 global $go_type;

 if (isset($go_type[$flag])) return $go_type[$flag][1];
  else return lang('game_object', 'unknown');
}

function makeinfocell($text,$tooltip){
 return "<a href=\"#\" onmouseover=\"toolTip('".addslashes($tooltip)."','info_tooltip')\" onmouseout=\"toolTip()\">$text</a>";
}

//########################################################################################################################
//  PRINT GO SEARCH FORM
//########################################################################################################################
function search() {
 global $output, $world_db, $realm_id, $go_type, $sqlw;

 /*$sql = new SQL;
 $sql->connect($world_db[$realm_id]['addr'], $world_db[$realm_id]['user'], $world_db[$realm_id]['pass'], $world_db[$realm_id]['name']);*/

 $result = $sqlw->query("SELECT count(*) FROM gameobject_names");
 $tot_go = $sqlw->result($result, 0);
 $sqlw->close();

 $output .= "<center>
 <fieldset class=\"full_frame\">
  <legend>".lang('game_object', 'search_template')."</legend><br />
  <form action=\"game_object.php?action=do_search&amp;error=2\" method=\"post\" name=\"form\">
  <table class=\"hidden\">
  <tr>
    <td>".lang('game_object', 'entry').":</td>
    <td><input type=\"text\" size=\"14\" maxlength=\"11\" name=\"entry\" /></td>
    <td>".lang('game_object', 'name').":</td>
    <td colspan=\"3\"><input type=\"text\" size=\"45\" maxlength=\"100\" name=\"name\" /></td>
  </tr>
  <tr>
   <td>".lang('game_object', 'script_name')."</td>
   <td><input type=\"text\" size=\"14\" maxlength=\"100\" name=\"ScriptName\" /></td>
   <td >".lang('game_object', 'type').":</td>
   <td colspan=\"3\"><select name=\"type\">
    <option value=\"-1\">".lang('game_object', 'select')."</option>";
   foreach ($go_type as $type) $output .= "<option value=\"$type[0]\">($type[0]) $type[1]</option>";
 $output .= "</select></td>
   </tr>
  <tr>
    <td>".lang('game_object', 'displayId').":</td>
    <td><input type=\"text\" size=\"14\" maxlength=\"11\" name=\"displayId\" /></td>
    <td>".lang('game_object', 'faction').":</td>
    <td><input type=\"text\" size=\"14\" maxlength=\"11\" name=\"faction\" /></td>
    <td>".lang('game_object', 'flags').":</td>
    <td><input type=\"text\" size=\"15\" maxlength=\"11\" name=\"flags\" /></td>
  </tr>
  <tr>
    <td>".lang('game_object', 'custom_search').":</td>
    <td colspan=\"3\"><input type=\"text\" size=\"45\" maxlength=\"512\" name=\"custom_search\" /></td>
    <td colspan=\"2\">";
     makebutton(lang('game_object', 'search'), "javascript:do_submit()",150);
$output .= "</td></tr>
  <tr>
    <td colspan=\"6\">-----------------------------------------------------------------------------------------------------------------------------------------------</td>
  </tr>
  <tr>
    <td></td>
    <td colspan=\"2\">";
      makebutton(lang('game_object', 'add_new'), "game_object.php?action=add_new&error=3",200);
 $output .= "</td>
    <td colspan=\"3\">".lang('game_object', 'tot_go_templ').": $tot_go</td>
  </tr>
 </table>
</form>
</fieldset><br /><br /></center>";
}


//########################################################################################################################
// SHOW SEARCH RESULTS
//########################################################################################################################
function do_search() {
 global $output, $world_db, $realm_id, $go_datasite, $sql_search_limit, $go_type, $sqlw;

  //wowhead_tt();

  require_once("./libs/get_lib.php");
  $deplang = get_lang_id();

 if((!isset($_POST['entry'])||$_POST['entry'] === '')&&(!isset($_POST['name'])||$_POST['name'] === '')&&(!isset($_POST['ScriptName'])||$_POST['ScriptName'] === '')&&(!isset($_POST['displayId'])||$_POST['displayId'] === '')
  &&(!isset($_POST['faction'])||$_POST['faction'] === '')&&(!isset($_POST['flags'])||$_POST['flags'] === '')&&(!isset($_POST['custom_search'])||$_POST['custom_search'] === '')
  &&($_POST['type'] == -1 )) {
  redirect("game_object.php?error=1");
  }

/*$sql = new SQL;
$sql->connect($world_db[$realm_id]['addr'], $world_db[$realm_id]['user'], $world_db[$realm_id]['pass'], $world_db[$realm_id]['name']);*/

if ($_POST['entry'] != '') $entry = $sqlw->quote_smart($_POST['entry']);
if ($_POST['name'] != '') $name = $sqlw->quote_smart($_POST['name']);
if ($_POST['type'] != -1) $type = $sqlw->quote_smart($_POST['type']);
if ($_POST['ScriptName'] != '') $ScriptName = $sqlw->quote_smart($_POST['ScriptName']);
if ($_POST['displayId'] != '') $displayId = $sqlw->quote_smart($_POST['displayId']);
if ($_POST['faction'] != '') $faction = $sqlw->quote_smart($_POST['faction']);
if ($_POST['flags'] != '') $flags = $sqlw->quote_smart($_POST['flags']);
if ($_POST['custom_search'] != '') $custom_search = $sqlw->quote_smart($_POST['custom_search']);
  else $custom_search = "";

 $where = "WHERE gameobject_names.entry > 0 ";
 if($custom_search != "") $where .= " $custom_search ";
 if(isset($entry)) $where .= "AND gameobject_names.entry = '$entry' ";
 if(isset($name)) $where .= "AND `name` LIKE '%$name%' ";
 if(isset($type)) $where .= "AND type = '$type' ";
 //if(isset($ScriptName)) $where .= "AND ScriptName LIKE '%$ScriptName%' ";
 if(isset($displayId)) $where .= "AND displayId = '$displayId' ";
 //if(isset($faction)) $where .= "AND gameobject_spawns.faction = '$faction' ";
 if(isset($flags)) $where .= "AND flags = '$flags' ";

 if($where == "WHERE gameobject_names.entry > 0 ") redirect("game_object.php?error=1");
 $result = $sqlw->query("SELECT gameobject_names.entry, type, displayId, name FROM gameobject_names $where ORDER BY gameobject_names.entry LIMIT $sql_search_limit");
 $total_found = $sqlw->num_rows($result);

  $output .= "<center>
  <table class=\"top_hidden\"></td>
       <tr><td>";
    makebutton(lang('game_object', 'new_search'), "game_object.php",160);
  $output .= "</td>
     <td align=\"right\">".lang('game_object', 'tot_found')." : $total_found : ".lang('global', 'limit')." $sql_search_limit</td>
   </tr></table>";

  $output .= "<table class=\"lined\">
   <tr>
  <th width=\"10%\">".lang('game_object', 'entry')."</th>
  <th width=\"40%\">".lang('game_object', 'name')."</th>
  <th width=\"20%\">".lang('game_object', 'type')."</th>
  <th width=\"15%\">".lang('game_object', 'displayId')."</th>
  <th width=\"15%\">".lang('game_object', 'faction')."</th>
  </tr>";

 for ($i=1; $i<=$total_found; $i++){
  $go = $sqlw->fetch_row($result);

  $output .= "<tr>
        <td><a href=\"$go_datasite$go[0]\" target=\"_blank\">$go[0]</a></td>
        <td><a href=\"game_object.php?action=edit&amp;entry=$go[0]&amp;error=4\">".htmlentities($go[3])."</a></td>
        <td>".get_go_type($go[1])."</td>
        <td>$go[2]</td>
        <td>$go[4]</td>
      </tr>";
  }
  $output .= "</table></center><br />";

 $sqlw->close();
}


//########################################################################################################################
// ADD GO
//########################################################################################################################
function add_new() {
 global $output, $go_type;

 $output .= "<script type=\"text/javascript\" src=\"libs/js/tab.js\"></script>
   <center>
    <br /><br /><br />
    <form method=\"post\" action=\"game_object.php?action=do_update\" name=\"form1\">
    <input type=\"hidden\" name=\"backup_op\" value=\"0\"/>
    <input type=\"hidden\" name=\"opp_type\" value=\"add_new\"/>

<div class=\"jtab-container\" id=\"container\">
  <ul class=\"jtabs\">
    <li><a href=\"#\" onclick=\"return showPane('pane1', this)\" id=\"tab1\">".lang('game_object', 'general')."</a></li>
    <li><a href=\"#\" onclick=\"return showPane('pane2', this)\">".lang('game_object', 'datas')."</a></li>
  </ul>
  <div class=\"jtab-panes\">";

// this_is_junk: what is this even for? O_o
$output .= "<div id=\"pane1\"><br /><br />
<table class=\"lined\" style=\"width: 720px;\">
<tr class=\"large_bold\"><td colspan=\"6\" class=\"hidden\" align=\"left\">".lang('game_object', 'general').":</td></tr>
<tr>
 <td>".makeinfocell(lang('game_object', 'entry'),lang('game_object', 'entry_desc'))."</td>
 <td><input type=\"text\" name=\"entry\" size=\"10\" maxlength=\"20\" value=\"0\" /></td>

 <td>".makeinfocell(lang('game_object', 'name'),lang('game_object', 'name_desc'))."</td>
 <td ><input type=\"text\" name=\"name\" size=\"25\" maxlength=\"100\" value=\"G.O.\" /></td>

  <td>".makeinfocell(lang('game_object', 'faction'),lang('game_object', 'faction_desc'))."</td>
 <td><input type=\"text\" name=\"faction\" size=\"10\" maxlength=\"4\" value=\"0\" /></td>
</tr>
<tr>
 <td>".makeinfocell(lang('game_object', 'type'),lang('game_object', 'type_desc'))."</td>
 <td colspan=\"3\"><select name=\"type\">";
   foreach ($go_type as $type) $output .= "<option value=\"$type[0]\">($type[0]) $type[1]</option>";
 $output .= "</select></td>
 <td>".makeinfocell(lang('game_object', 'displayId'),lang('game_object', 'displayId_desc'))."</td>
 <td><input type=\"text\" name=\"displayId\" size=\"10\" maxlength=\"11\" value=\"0\" /></td>

</tr>
<tr>
 <td>".makeinfocell(lang('game_object', 'flags'),lang('game_object', 'flags_desc'))."</td>
 <td><input type=\"text\" name=\"flags\" size=\"10\" maxlength=\"4\" value=\"0\" /></td>

 <td>".makeinfocell(lang('game_object', 'size'),lang('game_object', 'size_desc'))."</td>
 <td><input type=\"text\" name=\"size\" size=\"10\" maxlength=\"25\" value=\"1\" /></td>

 <td>".makeinfocell(lang('game_object', 'script_name'),lang('game_object', 'ScriptName_desc'))."</td>
 <td><input type=\"text\" name=\"ScriptName\" size=\"10\" maxlength=\"100\" value=\"\" /></td>
</tr>

<tr class=\"large_bold\"><td colspan=\"6\" class=\"hidden\" align=\"left\">".lang('game_object', 'data').":</td></tr>
<tr>
 <td>".makeinfocell(lang('game_object', 'data')." 0",lang('game_object', 'data_desc'))."</td>
 <td><input type=\"text\" name=\"data0\" size=\"10\" maxlength=\"11\" value=\"0\" /></td>

 <td>".makeinfocell(lang('game_object', 'data')." 1",lang('game_object', 'data_desc'))."</td>
 <td><input type=\"text\" name=\"data1\" size=\"10\" maxlength=\"11\" value=\"0\" /></td>

 <td>".makeinfocell(lang('game_object', 'data')." 2",lang('game_object', 'data_desc'))."</td>
 <td><input type=\"text\" name=\"data2\" size=\"10\" maxlength=\"11\" value=\"0\" /></td>
</tr>
<tr>
 <td>".makeinfocell(lang('game_object', 'data')." 3",lang('game_object', 'data_desc'))."</td>
 <td><input type=\"text\" name=\"data3\" size=\"10\" maxlength=\"11\" value=\"0\" /></td>

 <td>".makeinfocell(lang('game_object', 'data')." 4",lang('game_object', 'data_desc'))."</td>
 <td><input type=\"text\" name=\"data4\" size=\"10\" maxlength=\"11\" value=\"0\" /></td>

 <td>".makeinfocell(lang('game_object', 'data')." 5",lang('game_object', 'data_desc'))."</td>
 <td><input type=\"text\" name=\"data5\" size=\"10\" maxlength=\"11\" value=\"0\" /></td>
</tr>
</table>
<br /><br />
</div>

<div id=\"pane2\">
  <br /><br /><table class=\"lined\" style=\"width: 720px;\">

<tr class=\"large_bold\"><td colspan=\"6\" class=\"hidden\" align=\"left\">".lang('game_object', 'data').":</td></tr>
<tr>
 <td>".makeinfocell(lang('game_object', 'data')." 6",lang('game_object', 'data_desc'))."</td>
 <td><input type=\"text\" name=\"data6\" size=\"10\" maxlength=\"11\" value=\"0\" /></td>

 <td>".makeinfocell(lang('game_object', 'data')." 7",lang('game_object', 'data_desc'))."</td>
 <td><input type=\"text\" name=\"data7\" size=\"10\" maxlength=\"11\" value=\"0\" /></td>

 <td>".makeinfocell(lang('game_object', 'data')." 8",lang('game_object', 'data_desc'))."</td>
 <td><input type=\"text\" name=\"data8\" size=\"10\" maxlength=\"11\" value=\"0\" /></td>
</tr>
<tr>
 <td>".makeinfocell(lang('game_object', 'data')." 9",lang('game_object', 'data_desc'))."</td>
 <td><input type=\"text\" name=\"data9\" size=\"10\" maxlength=\"11\" value=\"0\" /></td>

 <td>".makeinfocell(lang('game_object', 'data')." 10",lang('game_object', 'data_desc'))."</td>
 <td><input type=\"text\" name=\"data10\" size=\"10\" maxlength=\"11\" value=\"0\" /></td>

 <td>".makeinfocell(lang('game_object', 'data')." 11",lang('game_object', 'data_desc'))."</td>
 <td><input type=\"text\" name=\"data11\" size=\"10\" maxlength=\"11\" value=\"0\" /></td>
</tr>
<tr>
 <td>".makeinfocell(lang('game_object', 'data')." 12",lang('game_object', 'data_desc'))."</td>
 <td><input type=\"text\" name=\"data12\" size=\"10\" maxlength=\"11\" value=\"0\" /></td>

 <td>".makeinfocell(lang('game_object', 'data')." 13",lang('game_object', 'data_desc'))."</td>
 <td><input type=\"text\" name=\"data13\" size=\"10\" maxlength=\"11\" value=\"0\" /></td>

 <td>".makeinfocell(lang('game_object', 'data')." 14",lang('game_object', 'data_desc'))."</td>
 <td><input type=\"text\" name=\"data14\" size=\"10\" maxlength=\"11\" value=\"0\" /></td>
</tr>
<tr>
 <td>".makeinfocell(lang('game_object', 'data')." 15",lang('game_object', 'data_desc'))."</td>
 <td><input type=\"text\" name=\"data15\" size=\"10\" maxlength=\"11\" value=\"0\" /></td>

 <td>".makeinfocell(lang('game_object', 'data')." 16",lang('game_object', 'data_desc'))."</td>
 <td><input type=\"text\" name=\"data16\" size=\"10\" maxlength=\"11\" value=\"0\" /></td>

 <td>".makeinfocell(lang('game_object', 'data')." 17",lang('game_object', 'data_desc'))."</td>
 <td><input type=\"text\" name=\"data17\" size=\"10\" maxlength=\"11\" value=\"0\" /></td>
</tr>
<tr>
 <td>".makeinfocell(lang('game_object', 'data')." 18",lang('game_object', 'data_desc'))."</td>
 <td><input type=\"text\" name=\"data18\" size=\"10\" maxlength=\"11\" value=\"0\" /></td>

 <td>".makeinfocell(lang('game_object', 'data')." 19",lang('game_object', 'data_desc'))."</td>
 <td><input type=\"text\" name=\"data19\" size=\"10\" maxlength=\"11\" value=\"0\" /></td>

 <td>".makeinfocell(lang('game_object', 'data')." 20",lang('game_object', 'data_desc'))."</td>
 <td><input type=\"text\" name=\"data20\" size=\"10\" maxlength=\"11\" value=\"0\" /></td>
</tr>
<tr>
 <td>".makeinfocell(lang('game_object', 'data')." 21",lang('game_object', 'data_desc'))."</td>
 <td><input type=\"text\" name=\"data21\" size=\"10\" maxlength=\"11\" value=\"0\" /></td>

 <td>".makeinfocell(lang('game_object', 'data')." 22",lang('game_object', 'data_desc'))."</td>
 <td><input type=\"text\" name=\"data22\" size=\"10\" maxlength=\"11\" value=\"0\" /></td>

 <td>".makeinfocell(lang('game_object', 'data')." 23",lang('game_object', 'data_desc'))."</td>
 <td><input type=\"text\" name=\"data23\" size=\"10\" maxlength=\"11\" value=\"0\" /></td>
</tr>
</table><br /><br />
</div>

</div>
<br />
</form>
<script type=\"text/javascript\">setupPanes(\"container\", \"tab1\")</script>";

 $output .= "<table class=\"hidden\">
          <tr><td>";
       makebutton(lang('game_object', 'save_to_db'), "javascript:do_submit('form1',0)",180);
       makebutton(lang('game_object', 'save_to_script'), "javascript:do_submit('form1',1)",180);
       makebutton(lang('game_object', 'lookup_go'), "game_object.php",180);
 $output .= "</td></tr>
        </table></center>";
}


//########################################################################################################################
// EDIT GO FORM
//########################################################################################################################
function edit() {
 global $output, $world_db, $realm_id, $item_datasite, $go_datasite, $go_type, $quest_datasite, $sqlw;

  //wowhead_tt();

 if (!isset($_GET['entry'])) redirect("game_object.php?error=1");

 /*$sql = new SQL;
 $sql->connect($world_db[$realm_id]['addr'], $world_db[$realm_id]['user'], $world_db[$realm_id]['pass'], $world_db[$realm_id]['name']);*/

 $entry = $sqlw->quote_smart($_GET['entry']);
 require_once("./libs/get_lib.php");
 $deplang = get_lang_id();
 // this_is_junk: the data* fields in MaNGOS don't line up with fields in ArcEmu, we'll skip them
 //               until we can figure it out.
 //$result = $sql->query("SELECT gameobject_spawns.`entry`,`type`,`displayId`, name,`flags`,`size`,`data0`,`data1`,`data2`,`data3`,`data4`,`data5`,`data6`,`data7`,`data8`,`data9`,`data10`,`data11`,`data12`,`data13`,`data14`,`data15`,`data16`,`data17`,`data18`,`data19`,`data20`,`data21`,`data22`,`data23`,`ScriptName` FROM gameobject_template LEFT JOIN locales_gameobject ON gameobject_template.entry = locales_gameobject.entry WHERE gameobject_template.entry = '$entry'");
 $result = $sqlw->query("SELECT gameobject_names.`entry`,`type`,`displayId`, name,`size` FROM gameobject_names WHERE gameobject_names.entry = '$entry'");

 if ($go = $sqlw->fetch_assoc($result)){

  $output .= "<script type=\"text/javascript\" src=\"libs/js/tab.js\"></script>
   <center>
    <br /><br /><br />
    <form method=\"post\" action=\"game_object.php?action=do_update\" name=\"form1\">
    <input type=\"hidden\" name=\"backup_op\" value=\"0\"/>
    <input type=\"hidden\" name=\"opp_type\" value=\"edit\"/>
    <input type=\"hidden\" name=\"entry\" value=\"$entry\"/>

<div class=\"jtab-container\" id=\"container\">
  <ul class=\"jtabs\">
    <li><a href=\"#\" onclick=\"return showPane('pane1', this)\" id=\"tab1\">".lang('game_object', 'general')."</a></li>
    <li><a href=\"#\" onclick=\"return showPane('pane2', this)\">".lang('game_object', 'datas')."</a></li>";
if ($go['type'] == 3) $output .= "<li><a href=\"#\" onclick=\"return showPane('pane3', this)\">".lang('game_object', 'loot')."</a></li>";
  $output .= "<li><a href=\"#\" onclick=\"return showPane('pane4', this)\">".lang('game_object', 'quests')."</a></li>
  </ul>
  <div class=\"jtab-panes\">";

  $output .= "<div id=\"pane1\"><br /><br />
<table class=\"lined\" style=\"width: 720px;\">
<tr class=\"large_bold\"><td colspan=\"6\" class=\"hidden\" align=\"left\">".lang('game_object', 'general').":</td></tr>
<tr>
 <td>".makeinfocell(lang('game_object', 'entry'),lang('game_object', 'entry_desc'))."</td>
 <td><a href=\"$go_datasite{$go['entry']}\" target=\"_blank\">{$go['entry']}</a></td>

 <td>".makeinfocell(lang('game_object', 'name'),lang('game_object', 'name_desc'))."</td>
 <td ><input type=\"text\" name=\"name\" size=\"25\" maxlength=\"100\" value=\"{$go['name']}\" /></td>

  <td>".makeinfocell(lang('game_object', 'faction'),lang('game_object', 'faction_desc'))."</td>
 <td><input type=\"text\" name=\"faction\" size=\"10\" maxlength=\"4\" value=\"{$go['faction']}\" /></td>
</tr>
<tr>
 <td>".makeinfocell(lang('game_object', 'type'),lang('game_object', 'type_desc'))."</td>
 <td colspan=\"3\"><select name=\"type\">";
   foreach ($go_type as $type) {
    $output .= "<option value=\"$type[0]\" ";
    if ($type[0] == $go['type']) $output .= "selected=\"selected\" ";
    $output .= ">($type[0]) $type[1]</option>";
    }
 $output .= "</select></td>
 <td>".makeinfocell(lang('game_object', 'displayId'),lang('game_object', 'displayId_desc'))."</td>
 <td><input type=\"text\" name=\"displayId\" size=\"10\" maxlength=\"11\" value=\"{$go['displayId']}\" /></td>

</tr>
<tr>
 <td>".makeinfocell(lang('game_object', 'flags'),lang('game_object', 'flags_desc'))."</td>
 <td><input type=\"text\" name=\"flags\" size=\"10\" maxlength=\"4\" value=\"{$go['flags']}\" /></td>

 <td>".makeinfocell(lang('game_object', 'size'),lang('game_object', 'size_desc'))."</td>
 <td><input type=\"text\" name=\"size\" size=\"10\" maxlength=\"25\" value=\"{$go['size']}\" /></td>

 <td>".makeinfocell(lang('game_object', 'script_name'),lang('game_object', 'ScriptName_desc'))."</td>
 <td><input type=\"text\" name=\"ScriptName\" size=\"10\" maxlength=\"100\" value=\"{$go['ScriptName']}\" /></td>
</tr>

<tr class=\"large_bold\"><td colspan=\"6\" class=\"hidden\" align=\"left\">".lang('game_object', 'data').":</td></tr>
<tr>
 <td>".makeinfocell(lang('game_object', 'data')." 0",lang('game_object', 'data_desc'))."</td>
 <td><input type=\"text\" name=\"data0\" size=\"10\" maxlength=\"11\" value=\"{$go['data0']}\" /></td>

 <td>".makeinfocell(lang('game_object', 'data')." 1",lang('game_object', 'data_desc'))."</td>
 <td><input type=\"text\" name=\"data1\" size=\"10\" maxlength=\"11\" value=\"{$go['data1']}\" /></td>

 <td>".makeinfocell(lang('game_object', 'data')." 2",lang('game_object', 'data_desc'))."</td>
 <td><input type=\"text\" name=\"data2\" size=\"10\" maxlength=\"11\" value=\"{$go['data2']}\" /></td>
</tr>
<tr>
 <td>".makeinfocell(lang('game_object', 'data')." 3",lang('game_object', 'data_desc'))."</td>
 <td><input type=\"text\" name=\"data3\" size=\"10\" maxlength=\"11\" value=\"{$go['data3']}\" /></td>

 <td>".makeinfocell(lang('game_object', 'data')." 4",lang('game_object', 'data_desc'))."</td>
 <td><input type=\"text\" name=\"data4\" size=\"10\" maxlength=\"11\" value=\"{$go['data4']}\" /></td>

 <td>".makeinfocell(lang('game_object', 'data')." 5",lang('game_object', 'data_desc'))."</td>
 <td><input type=\"text\" name=\"data5\" size=\"10\" maxlength=\"11\" value=\"{$go['data5']}\" /></td>
</tr>
</table><br />";

$result1 = $sqlw->query("SELECT COUNT(*) FROM gameobject_spawns WHERE entry = '{$go['entry']}'");
$output .= "<tr><td colspan=\"6\">".lang('game_object', 'go_swapned')." : ".$sqlw->result($result1, 0)." ".lang('game_object', 'times').".</td></tr>

<br />
</div>

<div id=\"pane2\">
  <br /><br /><table class=\"lined\" style=\"width: 720px;\">

<tr class=\"large_bold\"><td colspan=\"6\" class=\"hidden\" align=\"left\">".lang('game_object', 'data').":</td></tr>
<tr>
 <td>".makeinfocell(lang('game_object', 'data')." 6",lang('game_object', 'data_desc'))."</td>
 <td><input type=\"text\" name=\"data6\" size=\"10\" maxlength=\"11\" value=\"{$go['data6']}\" /></td>

 <td>".makeinfocell(lang('game_object', 'data')." 7",lang('game_object', 'data_desc'))."</td>
 <td><input type=\"text\" name=\"data7\" size=\"10\" maxlength=\"11\" value=\"{$go['data7']}\" /></td>

 <td>".makeinfocell(lang('game_object', 'data')." 8",lang('game_object', 'data_desc'))."</td>
 <td><input type=\"text\" name=\"data8\" size=\"10\" maxlength=\"11\" value=\"{$go['data8']}\" /></td>
</tr>
<tr>
 <td>".makeinfocell(lang('game_object', 'data')." 9",lang('game_object', 'data_desc'))."</td>
 <td><input type=\"text\" name=\"data9\" size=\"10\" maxlength=\"11\" value=\"{$go['data9']}\" /></td>

 <td>".makeinfocell(lang('game_object', 'data')." 10",lang('game_object', 'data_desc'))."</td>
 <td><input type=\"text\" name=\"data10\" size=\"10\" maxlength=\"11\" value=\"{$go['data10']}\" /></td>

 <td>".makeinfocell(lang('game_object', 'data')." 11",lang('game_object', 'data_desc'))."</td>
 <td><input type=\"text\" name=\"data11\" size=\"10\" maxlength=\"11\" value=\"{$go['data11']}\" /></td>
</tr>
<tr>
 <td>".makeinfocell(lang('game_object', 'data')." 12",lang('game_object', 'data_desc'))."</td>
 <td><input type=\"text\" name=\"data12\" size=\"10\" maxlength=\"11\" value=\"{$go['data12']}\" /></td>

 <td>".makeinfocell(lang('game_object', 'data')." 13",lang('game_object', 'data_desc'))."</td>
 <td><input type=\"text\" name=\"data13\" size=\"10\" maxlength=\"11\" value=\"{$go['data13']}\" /></td>

 <td>".makeinfocell(lang('game_object', 'data')." 14",lang('game_object', 'data_desc'))."</td>
 <td><input type=\"text\" name=\"data14\" size=\"10\" maxlength=\"11\" value=\"{$go['data14']}\" /></td>
</tr>
<tr>
 <td>".makeinfocell(lang('game_object', 'data')." 15",lang('game_object', 'data_desc'))."</td>
 <td><input type=\"text\" name=\"data15\" size=\"10\" maxlength=\"11\" value=\"{$go['data15']}\" /></td>

 <td>".makeinfocell(lang('game_object', 'data')." 16",lang('game_object', 'data_desc'))."</td>
 <td><input type=\"text\" name=\"data16\" size=\"10\" maxlength=\"11\" value=\"{$go['data16']}\" /></td>

 <td>".makeinfocell(lang('game_object', 'data')." 17",lang('game_object', 'data_desc'))."</td>
 <td><input type=\"text\" name=\"data17\" size=\"10\" maxlength=\"11\" value=\"{$go['data17']}\" /></td>
</tr>
<tr>
 <td>".makeinfocell(lang('game_object', 'data')." 18",lang('game_object', 'data_desc'))."</td>
 <td><input type=\"text\" name=\"data18\" size=\"10\" maxlength=\"11\" value=\"{$go['data18']}\" /></td>

 <td>".makeinfocell(lang('game_object', 'data')." 19",lang('game_object', 'data_desc'))."</td>
 <td><input type=\"text\" name=\"data19\" size=\"10\" maxlength=\"11\" value=\"{$go['data19']}\" /></td>

 <td>".makeinfocell(lang('game_object', 'data')." 20",lang('game_object', 'data_desc'))."</td>
 <td><input type=\"text\" name=\"data20\" size=\"10\" maxlength=\"11\" value=\"{$go['data20']}\" /></td>
</tr>
<tr>
 <td>".makeinfocell(lang('game_object', 'data')." 21",lang('game_object', 'data_desc'))."</td>
 <td><input type=\"text\" name=\"data21\" size=\"10\" maxlength=\"11\" value=\"{$go['data21']}\" /></td>

 <td>".makeinfocell(lang('game_object', 'data')." 22",lang('game_object', 'data_desc'))."</td>
 <td><input type=\"text\" name=\"data22\" size=\"10\" maxlength=\"11\" value=\"{$go['data22']}\" /></td>

 <td>".makeinfocell(lang('game_object', 'data')." 23",lang('game_object', 'data_desc'))."</td>
 <td><input type=\"text\" name=\"data23\" size=\"10\" maxlength=\"11\" value=\"{$go['data23']}\" /></td>
</tr>
</table>
<br />
</div>";

if ($go['type'] == 3){
$output .= "<div id=\"pane3\">
  <br /><br /><table class=\"lined\" style=\"width: 720px;\">
  <tr class=\"large_bold\"><td colspan=\"6\" class=\"hidden\" align=\"left\">".lang('game_object', 'loot_tmpl_id').":</td></tr>
<tr>
  <td colspan=\"6\">";
  require_once("libs/item_lib.php");

  $cel_counter = 0;
  $row_flag = 0;
  $output .= "<table class=\"hidden\" align=\"center\"><tr>";
  $result1 = $sqlw->query("SELECT itemid, normal10percentchance, ffa_loot, mincount, maxcount FROM loot_gameobjects WHERE entryid = {$go['entry']} ORDER BY normal10percentchance DESC");
  while ($item = $sqlw->fetch_row($result1)){
    $cel_counter++;
    $tooltip = get_item_name($item[0])." ($item[0])<br />".lang('game_object', 'drop_chance').": $item[1]%<br />".lang('game_object', 'quest_drop_chance').": $item[2]%<br />".lang('game_object', 'drop_chance').": $item[3]-$item[4]<br />".lang('game_object', 'lootcondition').": $item[5]<br />".lang('game_object', 'condition_value1').": $item[6]<br />".lang('game_object', 'condition_value2').": $item[7]";
    $output .= "<td>";
    $output .= maketooltip("<img src=\"".get_item_icon($item[0])."\" class=\"icon_border\" alt=\"\" />", "$item_datasite$item[0]", "$tooltip", "item_tooltip", "target=\"_blank\"");
    $output .= "<br /><input type=\"checkbox\" name=\"del_loot_items[]\" value=\"$item[0]\" /></td>";

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
<tr class=\"large_bold\"><td colspan=\"6\" class=\"hidden\" align=\"left\">".lang('game_object', 'add_items_to_templ').":</td></tr>
<tr>
<td>".makeinfocell(lang('game_object', 'loot_item_id'),lang('game_object', 'loot_item_id_desc'))."</td>
  <td><input type=\"text\" name=\"item\" size=\"8\" maxlength=\"10\" value=\"\" /></td>
<td>".makeinfocell(lang('game_object', 'loot_drop_chance'),lang('game_object', 'loot_drop_chance_desc'))."</td>
  <td><input type=\"text\" name=\"ChanceOrQuestChance\" size=\"8\" maxlength=\"11\" value=\"0\" /></td>
<td>".makeinfocell(lang('game_object', 'loot_quest_drop_chance'),lang('game_object', 'loot_quest_drop_chance_desc'))."</td>
  <td><input type=\"text\" name=\"groupid\" size=\"8\" maxlength=\"10\" value=\"0\" /></td>
</tr>
<tr>
<td>".makeinfocell(lang('game_object', 'min_count'),lang('game_object', 'min_count_desc'))."</td>
  <td><input type=\"text\" name=\"mincountOrRef\" size=\"8\" maxlength=\"3\" value=\"1\" /></td>
<td>".makeinfocell(lang('game_object', 'max_count'),lang('game_object', 'max_count_desc'))."</td>
  <td><input type=\"text\" name=\"maxcount\" size=\"8\" maxlength=\"3\" value=\"1\" /></td>
</tr>
<tr>
<td>".makeinfocell(lang('game_object', 'lootcondition'),lang('game_object', 'lootcondition_desc'))."</td>
  <td><input type=\"text\" name=\"lootcondition\" size=\"8\" maxlength=\"3\" value=\"0\" /></td>
<td>".makeinfocell(lang('game_object', 'condition_value1'),lang('game_object', 'condition_value1_desc'))."</td>
  <td><input type=\"text\" name=\"condition_value1\" size=\"8\" maxlength=\"3\" value=\"0\" /></td>
<td>".makeinfocell(lang('game_object', 'condition_value2'),lang('game_object', 'condition_value2'))."</td>
  <td><input type=\"text\" name=\"condition_value2\" size=\"8\" maxlength=\"3\" value=\"0\" /></td>
</tr>
</table><br />".lang('game_object', 'check_to_delete')."<br /><br />
</div>";
}

$output .= "<div id=\"pane4\">
  <br /><br /><table class=\"lined\" style=\"width: 720px;\">
  <tr class=\"large_bold\"><td colspan=\"2\" class=\"hidden\" align=\"left\">".lang('game_object', 'start_quests').":</td></tr>";

  $result1 = $sqlw->query("SELECT quest FROM gameobject_quest_starter WHERE id = {$go['entry']}");
  while ($quest = $sqlw->fetch_row($result1)){
    $query1 = $sqlw->query("SELECT QuestLevel, Title FROM quests WHERE quests.entry ='$quest[0]'");
    $quest_templ = $sqlw->fetch_row($query1);

    $output .= "<tr><td width=\"5%\"><input type=\"checkbox\" name=\"del_questrelation[]\" value=\"$quest[0]\" /></td>
          <td width=\"95%\" align=\"left\"><a class=\"tooltip\" href=\"$quest_datasite$quest[0]\" target=\"_blank\">({$quest_templ[0]}) $quest_templ[1]</a></td></tr>";
  };

$output .= "<tr class=\"large_bold\" align=\"left\"><td colspan=\"2\" class=\"hidden\">".lang('game_object', 'add_starts_quests').":</td></tr>
  <tr><td colspan=\"2\" align=\"left\">".makeinfocell(lang('game_object', 'quest_id'),lang('game_object', 'quest_id_desc'))." :
    <input type=\"text\" name=\"questrelation\" size=\"8\" maxlength=\"8\" value=\"\" /></td></tr>

<tr class=\"large_bold\"><td colspan=\"2\" class=\"hidden\" align=\"left\">".lang('game_object', 'ends_quests').":</td></tr>";

  $result1 = $sqlw->query("SELECT quest FROM gameobject_quest_finisher WHERE id = {$go['entry']}");
  while ($quest = $sqlw->fetch_row($result1)){
    $query1 = $sqlw->query("SELECT QuestLevel, Title FROM quests WHERE quests.entry ='$quest[0]'");
    $quest_templ = $sqlw->fetch_row($query1);

    $output .= "<tr><td width=\"5%\"><input type=\"checkbox\" name=\"del_involvedrelation[]\" value=\"$quest[0]\" /></td>
        <td width=\"95%\" align=\"left\"><a class=\"tooltip\" href=\"$quest_datasite$quest[0]\" target=\"_blank\">({$quest_templ[0]}) $quest_templ[1]</a></td></tr>";
  };

$output .= "<tr class=\"large_bold\" align=\"left\"><td colspan=\"2\" class=\"hidden\">".lang('game_object', 'add_ends_quests').":</td></tr>
  <tr><td colspan=\"2\" align=\"left\">".makeinfocell(lang('game_object', 'quest_id'),lang('game_object', 'quest_id_desc'))." :
    <input type=\"text\" name=\"involvedrelation\" size=\"8\" maxlength=\"8\" value=\"\" /></td></tr>

</table><br />".lang('game_object', 'check_to_delete')."<br /><br />
</div>

</div>
</div>
<br />
</form>

<script type=\"text/javascript\">setupPanes(\"container\", \"tab1\")</script>";

 $output .= "<table class=\"hidden\">
          <tr><td>";
       makebutton(lang('game_object', 'save_to_db'), "javascript:do_submit('form1',0)",180);
       makebutton(lang('game_object', 'del_go'), "game_object.php?action=delete&amp;entry=$entry",180);
       makebutton(lang('game_object', 'del_spawns'), "game_object.php?action=delete_spwn&amp;entry=$entry",180);
       makebutton(lang('game_object', 'save_to_script'), "javascript:do_submit('form1',1)",180);
 $output .= "</td></tr><tr><td>";
       makebutton(lang('game_object', 'lookup_go'), "game_object.php",760);
 $output .= "</td></tr>
        </table></center>";

 //$sql->close();
 } else {
    //$sql->close();
    error(lang('game_object', 'tmpl_not_found'));
    exit();
    }
}


//########################################################################################################################
//DO UPDATE GO TEMPLATE
//########################################################################################################################
function do_update() {
 global $world_db, $realm_id, $sqlw;

 if (!isset($_POST['opp_type']) || $_POST['opp_type'] === '') redirect("game_object.php?error=1");
 if (!isset($_POST['entry']) || $_POST['entry'] === '') redirect("game_object.php?error=1");

 /*sql = new SQL;
 $sql->connect($world_db[$realm_id]['addr'], $world_db[$realm_id]['user'], $world_db[$realm_id]['pass'], $world_db[$realm_id]['name']);*/

 $entry = $sqlw->quote_smart($_POST['entry']);
 if (isset($_POST['type']) && $_POST['type'] != '') $type = $sqlw->quote_smart($_POST['type']);
  else $type = 0;
 if (isset($_POST['displayId']) && $_POST['displayId'] != '') $displayId = $sqlw->quote_smart($_POST['displayId']);
  else $displayId = 0;
 if (isset($_POST['name']) && $_POST['name'] != '') $name = $sqlw->quote_smart($_POST['name']);
  else $name = "";
 if (isset($_POST['faction']) && $_POST['faction'] != '') $faction = $sqlw->quote_smart($_POST['faction']);
  else $faction = 0;
 if (isset($_POST['flags']) && $_POST['flags'] != '') $flags = $sqlw->quote_smart($_POST['flags']);
  else $flags = 0;
 if (isset($_POST['size']) && $_POST['size'] != '') $size = $sqlw->quote_smart($_POST['size']);
  else $size = 0;
 if (isset($_POST['ScriptName']) && $_POST['ScriptName'] != '') $ScriptName = $sqlw->quote_smart($_POST['ScriptName']);
  else $ScriptName = "";
 if (isset($_POST['data0']) && $_POST['data0'] != '') $data0 = $sqlw->quote_smart($_POST['data0']);
  else $data0 = 0;
 if (isset($_POST['data1']) && $_POST['data1'] != '') $data1 = $sqlw->quote_smart($_POST['data1']);
  else $data1 = 0;
 if (isset($_POST['data2']) && $_POST['data2'] != '') $data2 = $sqlw->quote_smart($_POST['data2']);
  else $data2 = 0;
 if (isset($_POST['data3']) && $_POST['data3'] != '') $data3 = $sqlw->quote_smart($_POST['data3']);
  else $data3 = 0;
 if (isset($_POST['data4']) && $_POST['data4'] != '') $data4 = $sqlw->quote_smart($_POST['data4']);
  else $data4 = 0;
 if (isset($_POST['data5']) && $_POST['data5'] != '') $data5 = $sqlw->quote_smart($_POST['data5']);
  else $data5 = 0;
 if (isset($_POST['data6']) && $_POST['data6'] != '') $data6 = $sqlw->quote_smart($_POST['data6']);
  else $data6 = 0;
 if (isset($_POST['data7']) && $_POST['data7'] != '') $data7 = $sqlw->quote_smart($_POST['data7']);
  else $data7 = 0;
 if (isset($_POST['data8']) && $_POST['data8'] != '') $data8 = $sqlw->quote_smart($_POST['data8']);
  else $data8 = 0;
 if (isset($_POST['data9']) && $_POST['data9'] != '') $data9 = $sqlw->quote_smart($_POST['data9']);
  else $data9 = 0;
 if (isset($_POST['data10']) && $_POST['data10'] != '') $data10 = $sqlw->quote_smart($_POST['data10']);
  else $data10 = 0;
 if (isset($_POST['data11']) && $_POST['data11'] != '') $data11 = $sqlw->quote_smart($_POST['data11']);
  else $data11 = 0;
 if (isset($_POST['data12']) && $_POST['data12'] != '') $data12 = $sqlw->quote_smart($_POST['data12']);
  else $data12 = 0;
 if (isset($_POST['data13']) && $_POST['data13'] != '') $data13 = $sqlw->quote_smart($_POST['data13']);
  else $data13 = 0;
 if (isset($_POST['data14']) && $_POST['data14'] != '') $data14 = $sqlw->quote_smart($_POST['data14']);
  else $data14 = 0;
 if (isset($_POST['data15']) && $_POST['data15'] != '') $data15 = $sqlw->quote_smart($_POST['data15']);
  else $data15 = 0;
 if (isset($_POST['data16']) && $_POST['data16'] != '') $data16 = $sqlw->quote_smart($_POST['data16']);
  else $data16 = 0;
 if (isset($_POST['data17']) && $_POST['data17'] != '') $data17 = $sqlw->quote_smart($_POST['data17']);
  else $data17 = 0;
 if (isset($_POST['data18']) && $_POST['data18'] != '') $data18 = $sqlw->quote_smart($_POST['data18']);
  else $data18 = 0;
 if (isset($_POST['data19']) && $_POST['data19'] != '') $data19 = $sqlw->quote_smart($_POST['data19']);
  else $data19 = 0;
 if (isset($_POST['data20']) && $_POST['data20'] != '') $data20 = $sqlw->quote_smart($_POST['data20']);
  else $data20 = 0;
 if (isset($_POST['data21']) && $_POST['data21'] != '') $data21 = $sqlw->quote_smart($_POST['data21']);
  else $data21 = 0;
 if (isset($_POST['data22']) && $_POST['data22'] != '') $data22 = $sqlw->quote_smart($_POST['data22']);
  else $data22 = 0;
 if (isset($_POST['data23']) && $_POST['data23'] != '') $data23 = $sqlw->quote_smart($_POST['data23']);
  else $data23 = 0;

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

  if ($_POST['opp_type'] == "add_new"){
  $sql_query = "INSERT INTO gameobject_template ( entry, type, displayId, name, faction, flags, size, data0, data1,
  data2, data3, data4, data5, data6, data7, data8, data9, data10, data11, data12, data13,
  data14, data15, data16, data17, data18, data19, data20, data21, data22, data23, ScriptName )
  VALUES ( '$entry', '$type', '$displayId', '$name', '$faction', '$flags', '$size', '$data0', '$data1',
  '$data2', '$data3', '$data4', '$data5', '$data6', '$data7', '$data8', '$data9', '$data10', '$data11',
  '$data12', '$data13', '$data14', '$data15', '$data16', '$data17', '$data18', '$data19', '$data20',
  '$data21', '$data22', '$data23', '$ScriptName' )";

 } elseif ($_POST['opp_type'] == "edit"){

  $sql_query = "UPDATE gameobject_template SET ";

  $result = $sqlw->query("SELECT gameobject_template.`entry`,`type`,`displayId`,IFNULL(".($deplang<>0?"name_loc$deplang":"NULL").",`name`) as name,`faction`,`flags`,`size`,`data0`,`data1`,`data2`,`data3`,`data4`,`data5`,`data6`,`data7`,`data8`,`data9`,`data10`,`data11`,`data12`,`data13`,`data14`,`data15`,`data16`,`data17`,`data18`,`data19`,`data20`,`data21`,`data22`,`data23`,`ScriptName` FROM gameobject_template LEFT JOIN locales_gameobject ON gameobject_template.entry = locales_gameobject.entry WHERE gameobject_template.entry = '$entry'");
  if ($go_templ = $sqlw->fetch_assoc($result)){
    if ($go_templ['type'] != $type) $sql_query .= "type='$type',";
    if ($go_templ['displayId'] != $displayId) $sql_query .= "displayId='$displayId',";
    if ($go_templ['name'] != $name) $sql_query .= "name='$name',";
    if ($go_templ['faction'] != $faction) $sql_query .= "faction='$faction',";
    if ($go_templ['flags'] != $flags) $sql_query .= "flags='$flags',";
    if ($go_templ['size'] != $size) $sql_query .= "size='$size',";
    if ($go_templ['data0'] != $data0) $sql_query .= "data0='$data0',";
    if ($go_templ['data1'] != $data1) $sql_query .= "data1='$data1',";
    if ($go_templ['data2'] != $data2) $sql_query .= "data2='$data2',";
    if ($go_templ['data3'] != $data3) $sql_query .= "data3='$data3',";
    if ($go_templ['data4'] != $data4) $sql_query .= "data4='$data4',";
    if ($go_templ['data5'] != $data5) $sql_query .= "data5='$data5',";
    if ($go_templ['data6'] != $data6) $sql_query .= "data6='$data6',";
    if ($go_templ['data7'] != $data7) $sql_query .= "data7='$data7',";
    if ($go_templ['data8'] != $data8) $sql_query .= "data8='$data8',";
    if ($go_templ['data9'] != $data9) $sql_query .= "data9='$data9',";
    if ($go_templ['data10'] != $data10) $sql_query .= "data10='$data10',";
    if ($go_templ['data11'] != $data11) $sql_query .= "data11='$data11',";
    if ($go_templ['data12'] != $data12) $sql_query .= "data12='$data12',";
    if ($go_templ['data13'] != $data13) $sql_query .= "data13='$data13',";
    if ($go_templ['data14'] != $data14) $sql_query .= "data14='$data14',";
    if ($go_templ['data15'] != $data15) $sql_query .= "data15='$data15',";
    if ($go_templ['data16'] != $data16) $sql_query .= "data16='$data16',";
    if ($go_templ['data17'] != $data17) $sql_query .= "data17='$data17',";
    if ($go_templ['data18'] != $data18) $sql_query .= "data18='$data18',";
    if ($go_templ['data19'] != $data19) $sql_query .= "data19='$data19',";
    if ($go_templ['data20'] != $data20) $sql_query .= "data20='$data20',";
    if ($go_templ['data21'] != $data21) $sql_query .= "data21='$data21',";
    if ($go_templ['data22'] != $data22) $sql_query .= "data22='$data22',";
    if ($go_templ['data23'] != $data23) $sql_query .= "data23='$data23',";
    if ($go_templ['ScriptName'] != $ScriptName) $sql_query .= "ScriptName='$ScriptName',";

    $sqlw->free_result($result);
    unset($go_templ);

  if (($sql_query == "UPDATE gameobject_template SET ")&&(!$item)&&(!$del_loot_items)
    &&(!$del_questrelation)&&(!$questrelation)&&(!$del_involvedrelation)&&(!$involvedrelation)){
    //$sql->close();
    redirect("game_object.php?action=edit&entry=$entry&error=6");
    } else {
        if ($sql_query != "UPDATE gameobject_template SET "){
          $sql_query[strlen($sql_query)-1] = " ";
          $sql_query .= " WHERE entry = '$entry';\n";
          } else $sql_query = "";
    }

  if ($item){
  $sql_query .= "INSERT INTO gameobject_loot_template (entry, item, ChanceOrQuestChance, `groupid`, mincountOrRef, maxcount, lootcondition, condition_value1, condition_value2)
      VALUES ($data1,$item,'$ChanceOrQuestChance', '$groupid' ,$mincountOrRef ,$maxcount ,$lootcondition ,$condition_value1 ,$condition_value2);\n";
  }

  if ($del_loot_items){
  foreach($del_loot_items as $item_id)
    $sql_query .= "DELETE FROM gameobject_loot_template WHERE entry = $data1 AND item = $item_id;\n";
  }

  if ($questrelation){
  $sql_query .= "INSERT INTO gameobject_questrelation (id, quest) VALUES ($entry,$questrelation);\n";
  }

  if ($involvedrelation){
  $sql_query .= "INSERT INTO gameobject_involvedrelation (id, quest) VALUES ($entry,$involvedrelation);\n";
  }

  if ($del_questrelation){
  foreach($del_questrelation as $quest_id)
    $sql_query .= "DELETE FROM gameobject_questrelation WHERE id = $entry AND quest = $quest_id;\n";
  }

  if ($del_involvedrelation){
  foreach($del_involvedrelation as $quest_id)
    $sql_query .= "DELETE FROM gameobject_involvedrelation WHERE id = $entry AND quest = $quest_id;\n";
  }


 } else {
    //$sql->close();
    redirect("game_object.php?error=5");
    }
 } else {
  //$sql->close();
  redirect("game_object.php?error=5");
  }

 if ( isset($_POST['backup_op']) && ($_POST['backup_op'] == 1) ){
  //$sql->close();
  Header("Content-type: application/octet-stream");
  Header("Content-Disposition: attachment; filename=goid_$entry.sql");
  echo $sql_query;
  exit();
  redirect("game_object.php?action=edit&entry=$entry&error=4");
  } else {
    $sql_query = explode(';',$sql_query);
    foreach($sql_query as $tmp_query) if(($tmp_query)&&($tmp_query != "\n")) $result = $sql->query($tmp_query);
    //$sql->close();
    }

 if ($result) redirect("game_object.php?action=edit&entry=$entry&error=4");
  else redirect("game_object.php");

}


//#######################################################################################################
//  DELETE GO TEMPLATE
//#######################################################################################################
function delete() {
global $output;

 if(isset($_GET['entry'])) $entry = $_GET['entry'];
  else redirect("game_object.php?error=1");

 $output .= "<center><h1><font class=\"error\">".lang('global', 'are_you_sure')."</font></h1><br />
      <font class=\"bold\">".lang('game_object', 'go_template').": <a href=\"game_object.php?action=edit&amp;entry=$entry\" target=\"_blank\">$entry</a>
      ".lang('global', 'will_be_erased')."<br />".lang('game_object', 'all_related_data')."</font><br /><br />
    <table class=\"hidden\">
          <tr>
            <td>";
      makebutton(lang('global', 'yes'), "game_object.php?action=do_delete&amp;entry=$entry",120);
      makebutton(lang('global', 'no'), "game_object.php",120);
 $output .= "</td>
          </tr>
        </table></center><br />";
}


//########################################################################################################################
//  DO DELETE GO TEMPLATE
//########################################################################################################################
function do_delete() {
 global $world_db, $realm_id, $sqlw;

 if(isset($_GET['entry'])) $entry = $_GET['entry'];
  else redirect("game_object.php?error=1");

 /*$sql = new SQL;
 $sql->connect($world_db[$realm_id]['addr'], $world_db[$realm_id]['user'], $world_db[$realm_id]['pass'], $world_db[$realm_id]['name']);*/

 $result = $sqlw->query("SELECT guid FROM gameobject WHERE id = '$entry'");
 while ($guid = $sqlw->fetch_row($result)){
  $result = $sqlw->query("DELETE FROM gameobject_respawn WHERE guid = '$guid'");
  }
 $sqlw->query("DELETE FROM gameobject_involvedrelation WHERE id = '$entry'");
 $sqlw->query("DELETE FROM gameobject_questrelation WHERE id = '$entry'");
 $sqlw->query("DELETE FROM gameobject_loot_template WHERE entry = '$data1'");
 $sqlw->query("DELETE FROM gameobject_template WHERE entry = '$entry'");

 //$sql->close();
 redirect("game_object.php");
 }


//########################################################################################################################
//   DELETE ALL GO SPAWNS
//########################################################################################################################
function delete_spwn() {
 global $world_db, $realm_id, $sqlw;

 if(isset($_GET['entry'])) $entry = $_GET['entry'];
  else redirect("game_object.php?error=1");

 /*$sql = new SQL;
 $sql->connect($world_db[$realm_id]['addr'], $world_db[$realm_id]['user'], $world_db[$realm_id]['pass'], $world_db[$realm_id]['name']);*/
 $sqlw->query("DELETE FROM gameobject WHERE id = '$entry'");
 //$sql->close();
 redirect("game_object.php?action=edit&entry=$entry&error=4");
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
   $output .= "<h1><font class=\"error\">".lang('item_edit', 'search_results')."</font></h1>";
   break;
case 3:
   $output .= "<h1><font class=\"error\">".lang('game_object', 'add_new_go_templ')."</font></h1>";
   break;
case 4:
   $output .= "<h1><font class=\"error\">".lang('game_object', 'edit_go_templ')."</font></h1>";
   break;
case 5:
   $output .= "<h1><font class=\"error\">".lang('game_object', 'err_adding_new')."</font></h1>";
   break;
case 6:
   $output .= "<h1><font class=\"error\">".lang('game_object', 'err_no_fields_updated')."</font></h1>";
   break;
default: //no error
    $output .= "<h1>".lang('game_object', 'search_go')."</h1>";
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
   add_new();
   break;
case "do_update":
   do_update();
   break;
case "edit":
   edit();
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
