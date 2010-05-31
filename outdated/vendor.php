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
require_once("libs/item_lib.php");
valid_login($action_permission['view']);

function makeinfocell($text,$tooltip)
{
  return "<a href=\"#\" onmouseover=\"toolTip('".addslashes($tooltip)."','info_tooltip')\" onmouseout=\"toolTip()\">$text</a>";
}

//########################################################################################################################
//  PRINT VENDOR SEARCH FORM
//########################################################################################################################
function search()
{
  global $locales_search_option, $output, $world_db, $realm_id, $sqlw;

  include_once("./libs/language_select.php");

  $result = $sqlw->query("SELECT count(distinct(entry)) FROM vendors");
  $tot_items = $sqlw->result($result, 0);

  $output .= "
  <center>
    <fieldset class=\"full_frame\">
      <legend>".lang('vendor', 'search_vendors')."</legend><br />
      <form action=\"vendor.php?action=do_search&amp;error=2\" method=\"post\" name=\"form\">

        <table class=\"hidden\">
          <tr>
            <td>".lang('vendor', 'entry').":</td>
            <td>
              <input type=\"text\" size=\"10\" maxlength=\"11\" name=\"entry\" />
            </td>
            <td>".lang('vendor', 'item').":</td>
            <td>
              <input type=\"text\" size=\"10\" maxlength=\"11\" name=\"item\" />
            </td>
          </tr>
          <tr>
            <td>".lang('vendor', 'quantity').":</td>
            <td>
              <input type=\"text\" size=\"10\" maxlength=\"3\" name=\"quantity\" />
            </td>
            <td>".lang('vendor', 'maxquantity').":</td>
            <td>
              <input type=\"text\" size=\"10\" maxlength=\"5\" name=\"maxquantity\" />
            </td>
          </tr>
          <tr>
            <td>".lang('vendor', 'inctime').":</td>
            <td>
              <input type=\"text\" size=\"10\" maxlength=\"4\" name=\"inctime\" />
            </td>
            <td>".lang('vendor', 'extcost').":</td>
            <td>
              <input type=\"text\" size=\"10\" maxlength=\"11\" name=\"extcost\" />
            </td>
          </tr>
          <tr>
            <td>".lang('vendor', 'limit').":</td>
            <td>
              <input type=\"text\" size=\"10\" maxlength=\"11\" name=\"limit\" value='100'/>
            </td>
            <!-- td>".lang('vendor', 'custom_search').":</td>
            <td colspan=\"2\">
              <input type=\"text\" size=\"25\" maxlength=\"50\" name=\"custom_search\" />
            </td -->
            <td>&nbsp</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>";
  makebutton(lang('vendor', 'search'), "javascript:do_submit()",150);
  $output .= "
            </td>
            <td colspan=\"2\">".lang('vendor', 'tot_vendors').": $tot_items</td>
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
function do_search()
{
  global $output, $world_db, $arcm_db, $realm_id, $creature_datasite, $sql_search_limit,
    $creature_npcflag, $language, $action_permission, $user_lvl, $item_datasite, $sqlw, $sqlm, $sqld;

  $where = '';

  // check input and prepare sql query

  if ($_POST['entry'] != '')
  {
    $entry   = (is_numeric($_POST['entry']))   ? $sqlw->quote_smart($_POST['entry'])   : redirect("vendor.php?error=8");
    $where .= "entry = '$entry' ";
  }
  else if ($_POST['item'] != '')
  {
    $item    = (preg_match('/^[\t\v\b\f\a\n\r\\\"\? <>[](){}_=+-|!@#$%^&*~`.,\0]{1,30}$/', $_POST['item']))  ?  "test" : $sqlw->quote_smart($_POST['item']);

    $where .= "`item` LIKE '%$item%' ";
  }
  else if ($_POST['quantity'] != '')
  {
    $quantity   = (is_numeric($_POST['quantity']))   ? $sqlw->quote_smart($_POST['quantity'])   : redirect("vendor.php?error=8");
    $where .= "amount <= $quantity AND amount >= $quantity ";
  }
  else if ($_POST['maxquantity'] != '')
  {
    $maxquantity  = (is_numeric($_POST['maxquantity']))  ? $sqlw->quote_smart($_POST['maxquantity'])  : redirect("vendor.php?error=8");
    $where .= "maxquantity <= $maxquantity AND maxquantity >= $maxquantity ";
  }
  else if ($_POST['inctime'] != '')
  {
    $inctime = (is_numeric($_POST['inctime'])) ? $sqlw->quote_smart($_POST['inctime']) : redirect("vendor.php?error=8");
    $where .= "inctime = '$inctime' ";
  }
  else if ($_POST['extcost'] != '')
  {
    $extcost   = (is_numeric($_POST['extcost']))   ? $sqlw->quote_smart($_POST['extcost'])   : redirect("vendor.php?error=8");
    $where .= "extcost = '$extcost' ";
  }

  // did the user give a results limit?
  // if not, we go with 100
  if ($_POST['limit'] != '')
    $limit   = ((is_numeric($_POST['limit']))   ? $sqlw->quote_smart($_POST['limit'])   : '100');
  else
    $limit = '100';

  // additional search query
  if ($_POST['custom_search'] != '')
  {
    //$custom_search  = (preg_match('/^[\t\v\b\f\a\n\r\\\"\?[](){}=+-|!@#$%^&*~`.,\0]{1,30}$/', $_POST['$custom_search']))  ? 0 : $sqlw->quote_smart($_POST['$custom_search']);
    $custom_search  = $sqlw->quote_smart($_POST['$custom_search']);
    $where .= ($where == '') ? $custom_search : "AND ".$custom_search;
  }

  /* no search value, go home! */
  if ($where == '')
    redirect("vendor.php?error=1");

  $db_query = "SELECT * FROM vendors WHERE {$where} ORDER BY entry LIMIT {$limit}";


  $result = $sqlw->query($db_query);
  $total_found = $sqlw->num_rows($result);

  $output .= "
  <center>
    <table class=\"top_hidden\">
      <tr>
        <td>";
  makebutton(lang('vendor', 'new_search'), "vendor.php",160);
  $output .= "
        </td>
        <td align=\"right\">".lang('vendor', 'tot_found')." : $total_found : ".lang('global', 'limit')." $limit</td>
      </tr>
    </table>";

  $output .= "
    <table class=\"lined\">
      <tr>
        <!-- th>".lang('vendor', 'entry')."</th -->
        <th></th>
        <th>".lang('vendor', 'itemname')."</th>
        <th>".lang('vendor', 'quantity')."</th>
        <th>".lang('vendor', 'maxquantity')."</th>
        <th>".lang('vendor', 'inctime')."</th>
        <th>".lang('vendor', 'extcost')."</th>
      </tr>";

  $cur_vend = '';

  for ($i=1; $i<=$total_found; $i++)
  {
    $creature = $sqlw->fetch_assoc($result);

    $name_query = "SELECT name FROM creature_names WHERE entry = '".$creature['entry']."'";
    $name_result = $sqlw->query($name_query);
    $vendor_name = $sqlw->fetch_assoc($name_result);

    $item_query = "SELECT name1 FROM items WHERE entry = '".$creature['item']."'";
    $item_result = $sqlw->query($item_query);
    $item_name = $sqlw->fetch_assoc($item_result);

    if($creature['extended_cost'] <> 0)
    {
      $extcost_query = "SELECT * FROM itemextendedcost WHERE id = '".$creature['extended_cost']."'";
      $extcost_result = $sqld->query($extcost_query);
      $extcost = $sqld->fetch_assoc($extcost_result);

      $ext_cost = "";

      $reqHonor = $extcost['ReqHonorPoints'];
      $reqArena = $extcost['ReqArenaPoints'];
      $reqItem1 = $extcost['RequiredItem1'];
      $reqItem2 = $extcost['RequiredItem2'];
      $reqItem3 = $extcost['RequiredItem3'];
      $reqItem4 = $extcost['RequiredItem4'];
      $reqItem5 = $extcost['RequiredItem5'];
      $reqItemCount1 = $extcost['RequiredItemCount1'];
      $reqItemCount2 = $extcost['RequiredItemCount2'];
      $reqItemCount3 = $extcost['RequiredItemCount3'];
      $reqItemCount4 = $extcost['RequiredItemCount4'];
      $reqItemCount5 = $extcost['RequiredItemCount5'];

      if($reqHonor <> 0)
        $ext_cost .= $reqHonor."<img src='./img/money_alliance.gif' alt='Honor' /> ";
      if($reqArena <> 0)
        $ext_cost .= $reqArena."<img src='./img/money_arena.gif' alt='Arena' /> ";
      if($reqItem1 <> 0)
        $ext_cost .= "
      <a id='vend_req_item' href='".$item_datasite.$reqItem1."' target='_blank'>
        ".$reqItemCount1." <img src='".get_item_icon($reqItem1)."' width=16 height=16 alt='".get_item_name($ReqItem1)."' /> 
      </a>";
      if($reqItem2 <> 0)
        $ext_cost .= "
      <a id='vend_req_item' href='".$item_datasite.$reqItem2."' target='_blank'>
        ".$reqItemCount2." <img src='".get_item_icon($reqItem2)."' width=16 height=16 alt='".get_item_name($ReqItem2)."' /> 
      </a>";
      if($reqItem3 <> 0)
        $ext_cost .= "
      <a id='vend_req_item' href='".$item_datasite.$reqItem3."' target='_blank'>
         ".$reqItemCount3." <img src='".get_item_icon($reqItem3)."' width=16 height=16 alt='".get_item_name($ReqItem3)."' /> 
      </a>";
      if($reqItem4 <> 0)
        $ext_cost .= "
      <a id='vend_req_item' href='".$item_datasite.$reqItem4."' target='_blank'>
        ".$reqItemCount4." <img src='".get_item_icon($reqItem4)."' width=16 height=16 alt='".get_item_name($ReqItem4)."' /> 
      </a>";
      if($reqItem5 <> 0)
        $ext_cost .= "
      <a id='vend_req_item' href='".$item_datasite.$reqItem5."' target='_blank'>
        ".$reqItemCount5." <img src='".get_item_icon($reqItem5)."' width=16 height=16 alt='".get_item_name($ReqItem5)."' /> 
      </a>";
    }

    if($cur_vend <> $vendor_name['name'])
    {
      if ($user_lvl >= $action_permission['insert'])
      {
        $output .= "
      <tr class=\"large_bold\">
        <td colspan=\"8\" class=\"hidden\" align=\"left\">
          <a href=\"vendor.php?action=edit&amp;entry=".$creature['entry']."&amp;error=4\">".lang('vendor', 'editlabel').": ".$vendor_name['name']."</a>
        </td>
      </tr>";
      }
      else
      {
        $output .= "
      <tr class=\"large_bold\">
        <td colspan=\"8\" class=\"hidden\" align=\"left\">
          <a href=\"vendor.php?action=edit&amp;entry=".$creature['entry']."&amp;error=4\">".lang('vendor', 'viewlabel').": ".$vendor_name['name']."</a>
        </td>
      </tr>";
      }
      $cur_vend = $vendor_name['name'];
    }

    //$output .= "<tr>
      //<td><a href=\"$creature_datasite".$creature['entry']."\" target=\"_blank\">".$vendor_name['name']."</a></td>";

    $output .= "
        <td>
          <a id=\"vendor_item_icon\" href=\"$item_datasite".$creature['item']."\" target=\"_blank\">
            <img src=\"".get_item_icon($creature['item'])."\" class=\"".get_item_border($creature['item'])."\" alt=\"\" />
          </a>
        </td>
        <td>
          <a href=\"item.php?action=edit&entry=".$creature['item']."&amp;error=4\">".$item_name['name1']."</a>
        </td>";

    $output .= "
        <td>".$creature['amount']."</td>
        <td>".$creature['max_amount']."</td>
        <td>".$creature['inctime']."</td>
        <td>".$ext_cost."</td>
      </tr>";
  }
  $output .= "
    </table>
  </center>
  <br />";
}


//########################################################################################################################
// EDIT VENDOR FORM
//########################################################################################################################
function do_insert_update($do_insert)
{
  global $output, $world_db, $realm_id, $creature_datasite, $item_datasite,
    $quest_datasite, $spell_datasite, $action_permission, $user_lvl,
    $locales_search_option, $arcm_db, $sqlm, $sqlw, $sqld;

  //wowhead_tt();

  require_once("./libs/get_lib.php");
  require_once 'libs/item_lib.php';


  // entry only needed on update
  if (!$do_insert)
  {
    if (!isset($_GET['entry']) )
      redirect("vendor.php?error=1");

    $entry = (is_numeric($_GET['entry']))   ? $sqlw->quote_smart($_GET['entry'])   : redirect("vendor.php?error=8");

    $vend_query = "SELECT * FROM vendors WHERE entry = '$entry'";
    $vend_restrict_query = "SELECT * FROM vendor_restrictions WHERE entry = '$entry'";

    $vend_result = $sqlw->query($vend_query);
    $vend_restrict_result = $sqlw->query($vend_restrict_query);
  }
  else
  {
    // get new free id
    $result = $sqlw->query("SELECT max(entry)+1 as newentry from creature_proto");
    $entry  = $sqlw->result($result, 0, 'newentry');
    $result = $sqlw->query("SELECT $entry as `entry`, 0 as `heroic_entry`, 0 as `KillCredit1`, 0 as `KillCredit2`, 0 as `modelid_A`, 0 as `modelid_A2`, 0 as `modelid_H`, 0 as `modelid_H2`, 'new creature' as`name`,'' as `subname`, '' as `IconName`, 1 as `minlevel`, 1 as `maxlevel`, 1 as `minhealth`, 1 as `maxhealth`, 0 as `minmana`, 0 as `maxmana`, 0 as `armor`,0 as `faction_A`, 0 as `faction_H`, 0 as `npcflag`, 1 as `speed`, 1 as `scale`,0 as `rank`, 1 as `mindmg`, 1 as `maxdmg`, 0 as `dmgschool`, 0 as `attackpower`, 2000 as `baseattacktime`, 0 as `rangeattacktime`, 0 as `unit_flags`,0 as `dynamicflags`, 0 as `family`, 0 as `trainer_type`, 0 as `trainer_spell`, 0 as `trainer_class`,0 as `trainer_race`,0 as `minrangedmg`, 0 as `maxrangedmg`, 0 as `rangedattackpower`, 0 as `type`,0 as `type_flags`,0 as `lootid`, 0 as `pickpocketloot`, 0 as `skinloot`, 0 as `resistance1`, 0 as `resistance2`, 0 as `resistance3`, 0 as `resistance4`, 0 as `resistance5`, 0 as `resistance6`, 0 as`spell1`, 0 as`spell2`, 0 as `spell3`, 0 as `spell4`, 0 as `PetSpellDataId`, 100 as `mingold`, 250 as `maxgold`, '' as `AIName`, 0 as `MovementType`, 1 as `InhabitType`, 0 as `RacialLeader`, 1 as `RegenHealth`, 0 as `equipment_id`, 0 as `mechanic_immune_mask`, 0 as `flags_extra`, '' as `ScriptName`");
    // use id for new creature_template
  }

  $total_found = $sqlw->num_rows($vend_result);

  if ($mob = $sqlw->fetch_assoc($vend_result))
  {
    $name_query = "SELECT name FROM creature_names WHERE entry = '".$mob['entry']."'";
    $name_result = $sqlw->query($name_query);
    $vendor_name = $sqlw->fetch_assoc($name_result);

    $output .= "
  <script type=\"text/javascript\" src=\"libs/js/tab.js\"></script>
  <center>
    <span class='large_bold'>
      <a href=\"$creature_datasite".$mob['entry']."\">".$vendor_name['name']."</a>
    </span>
    <br />
    <br />
    <br />
    <br />
    <form method=\"post\" action=\"vendor.php?action=del_item\" name=\"form1\">
      <input type=\"hidden\" name=\"backup_op\" value=\"0\"/>
      <input type=\"hidden\" name=\"entry\" value=\"$entry\"/>
      <input type=\"hidden\" name=\"insert\" value=\"$do_insert\"/>

      <div class=\"jtab-container\" id=\"container\">
        <ul class=\"jtabs\">
          <li>
            <a href=\"#\" onclick=\"return showPane('pane1', this)\" id=\"tab1\">".lang('vendor', 'sells')."</a>
          </li>
          <li>
            <a href=\"#\" onclick=\"return showPane('pane3', this)\">".lang('vendor', 'restrictions')."</a>
          </li>";

    $quest_flag = 0;
    $vendor_flag = 0;
    $trainer_flag = 0;

    $output .= "
        </ul>
          <div class=\"jtab-panes\">";

    $output .= "
            <div id=\"pane1\">
              <br />
              <center>";

    $output .= "
                <table class=\"lined\" id=\"vendor_edit_vendor\">
                  <tr>
                    <th width='3%'>&nbsp;</th>
                    <th width='9%'></th>
                    <th width='28%'>".lang('vendor', 'itemname')."</th>
                    <th width='15%'>".lang('vendor', 'quantity')."</th>
                    <th width='15%'>".lang('vendor', 'maxquantity')."</th>
                    <th width='15%'>".lang('vendor', 'inctime')."</th>
                    <th width='15%'>".lang('vendor', 'extcost')."</th>
                  </tr>";

    $cur_vend = '';

    $vend_result2 = $sqlw->query($vend_query);

    for ($i=1; $i<=$total_found; $i++)
    {
      $vendor = $sqlw->fetch_assoc($vend_result2);
      $item_query = "SELECT name1 FROM items WHERE entry = '".$vendor['item']."'";
      $item_result = $sqlw->query($item_query);
      $item_name = $sqlw->fetch_assoc($item_result);

      if($vendor['extended_cost'] <> 0)
      {
        $extcost_query = "SELECT * FROM itemextendedcost WHERE id = '".$vendor['extended_cost']."'";
        $extcost_result = $sqld->query($extcost_query);
        $extcost = $sqld->fetch_assoc($extcost_result);

        $ext_cost = "";

        $reqHonor = $extcost['ReqHonorPoints'];
        $reqArena = $extcost['ReqArenaPoints'];
        $reqItem1 = $extcost['RequiredItem1'];
        $reqItem2 = $extcost['RequiredItem2'];
        $reqItem3 = $extcost['RequiredItem3'];
        $reqItem4 = $extcost['RequiredItem4'];
        $reqItem5 = $extcost['RequiredItem5'];
        $reqItemCount1 = $extcost['RequiredItemCount1'];
        $reqItemCount2 = $extcost['RequiredItemCount2'];
        $reqItemCount3 = $extcost['RequiredItemCount3'];
        $reqItemCount4 = $extcost['RequiredItemCount4'];
        $reqItemCount5 = $extcost['RequiredItemCount5'];

        if($reqHonor <> 0)
          $ext_cost .= $reqHonor."<img src='./img/money_alliance.gif' alt='Honor' /> ";
        if($reqArena <> 0)
          $ext_cost .= $reqArena."<img src='./img/money_arena.gif' alt='Arena' /> ";
        if($reqItem1 <> 0)
          $ext_cost .= "
            <a id='vend_req_item' href='".$item_datasite.$reqItem1."' target='_blank'>
              ".$reqItemCount1." <img src='".get_item_icon($reqItem1)."' width=16 height=16 alt='".get_item_name($ReqItem1)."' /> 
            </a>";
        if($reqItem2 <> 0)
          $ext_cost .= "
            <a id='vend_req_item' href='".$item_datasite.$reqItem2."' target='_blank'>
              ".$reqItemCount2." <img src='".get_item_icon($reqItem2)."' width=16 height=16 alt='".get_item_name($ReqItem2)."' /> 
            </a>";
        if($reqItem3 <> 0)
          $ext_cost .= "
            <a id='vend_req_item' href='".$item_datasite.$reqItem3."' target='_blank'>
              ".$reqItemCount3." <img src='".get_item_icon($reqItem3)."' width=16 height=16 alt='".get_item_name($ReqItem3)."' /> 
            </a>";
        if($reqItem4 <> 0)
          $ext_cost .= "
            <a id='vend_req_item' href='".$item_datasite.$reqItem4."' target='_blank'>
              ".$reqItemCount4." <img src='".get_item_icon($reqItem4)."' width=16 height=16 alt='".get_item_name($ReqItem4)."' /> 
            </a>";
        if($reqItem5 <> 0)
          $ext_cost .= "
            <a id='vend_req_item' href='".$item_datasite.$reqItem5."' target='_blank'>
              ".$reqItemCount5." <img src='".get_item_icon($reqItem5)."' width=16 height=16 alt='".get_item_name($ReqItem5)."' /> 
            </a>";
      }

      //$output .= "<tr>
        //<td><a href=\"$creature_datasite".$creature['entry']."\" target=\"_blank\">".$vendor_name['name']."</a></td>";

      //$output .= "<td></td>";
      if ($user_lvl >= $action_permission['delete'])
        $output .= '
                <tr>
                  <td>
                    <input type="checkbox" name="check[]" value="'.$vendor['item'].'" onclick="CheckCheckAll(document.form1);" />
                  </td>';
      else
        $output .= '
                  <td></td>';

      $output .= "
                  <td>
                    <a id=\"vendor_item_icon\" href=\"$item_datasite".$vendor['item']."\" target=\"_blank\">
                      <img src=\"".get_item_icon($vendor['item'])."\" class=\"".get_item_border($vendor['item'])."\" alt=\"\" />
                    </a>
                  </td>
                  <td>
                    <a href=\"item.php?action=edit&entry=".$mob['item']."&amp;error=4\">".$item_name['name1']."</a>
                  </td>";

      $output .= "
                  <td>".$vendor['amount']."</td>
                  <td>".$vendor['max_amount']."</td>
                  <td>".$vendor['inctime']."</td>
                  <td>".$ext_cost."</td>
                </tr>";
    }
    $output .= "
              </table>
            </center>
            <br />";
    if($user_lvl >= $action_permission['delete'])
      makebutton(lang('vendor', 'del_item'), 'javascript:do_submit(\'form1\',0)" type="wrn', 200);
    if ($user_lvl >= $action_permission['insert'])
      makebutton(lang('vendor', 'add_item'), 'vendor.php?action=add_item&error=10', 130);
    if ($user_lvl >= $action_permission['insert'])
      makebutton(lang('vendor', 'edit_item'), 'vendor.php?action=add_item&error=10', 150);

    $output .= "
            <br />
            <br />
          </div>
        </form>";

    $vend_restrict = $sqlw->fetch_assoc($vend_restrict_result);

    $output .= "
        <div id=\"pane3\">
          <br />
          <br />
          <form method=\"post\" action=\"vendor.php?action=do_rest_update\" name=\"form2\">
            <input type=\"hidden\" name=\"backup_op\" value=\"0\"/>
            <input type=\"hidden\" name=\"entry\" value=\"$entry\"/>
            <input type=\"hidden\" name=\"insert\" value=\"$do_insert\"/>
            <table class=\"lined\" id=\"vendor_restrictions\">";
    $output .= "
              <tr>
                <td>".makeinfocell(lang('vendor', 'racemask'),lang('vendor', 'racemaskdesc'))."</td>
                <td colspan=\"1\"><input type=\"text\" name=\"racemask\" size=\"12\" maxlength=\"10\" value=\"{$vend_restrict['racemask']}\" /></td>

                <td></td>
                <td colspan=\"2\"></td>
              </tr>
              <tr>
                <td>".makeinfocell(lang('vendor', 'reqrepfactionvalue'),lang('vendor', 'reqrepfactionvaluedesc'))."</td>
                <td><input type=\"text\" name=\"reqrepfactionvalue\" size=\"8\" maxlength=\"45\" value=\"{$vend_restrict['reqrepfactionvalue']}\" /></td>

                <td>".makeinfocell(lang('vendor', 'reqrepfaction'),lang('vendor', 'reqrepfactiondesc'))."</td>
                <td><input type=\"text\" name=\"reqrepfaction\" size=\"8\" maxlength=\"45\" value=\"{$vend_restrict['reqrepfaction']}\" /></td>
              </tr>
              <tr>
                <td>".makeinfocell(lang('vendor', 'canbuyattextid'),lang('vendor', 'canbuyattextiddesc'))."</td>
                <td><input type=\"text\" name=\"canbuyattextid\" size=\"8\" maxlength=\"45\" value=\"{$vend_restrict['canbuyattextid']}\" /></td>

                <td>".makeinfocell(lang('vendor', 'cannotbuyattextid'),lang('vendor', 'cannotbuyattextiddesc'))."</td>
                <td><input type=\"text\" name=\"cannotbuyattextid\" size=\"8\" maxlength=\"45\" value=\"{$vend_restrict['cannotbuyattextid']}\" /></td>
              </tr>";

    $output .= "
            </table>
            <br />
            <br />";
    if ($user_lvl >= $action_permission['insert'])
      makebutton(lang('vendor', 'save_to_db'), "javascript:do_submit('form2',0)",180);
    $output .= "
          </div>";

    $output .= "
        </div>
      </div>
      <br />
    </form>

    <script type=\"text/javascript\">setupPanes(\"container\", \"tab1\")</script>
    <table class=\"hidden\">
      <tr>
        <td>";

    if($do_insert)
    {
      if ($user_lvl >= $action_permission['insert'] && $do_insert)
        makebutton(lang('vendor', 'save_to_db'), "javascript:do_submit('form1',0)",180);
    }
    else
    {
      if ($user_lvl >= $action_permission['delete']) 
        makebutton(lang('vendor', 'del_creature'), "vendor.php?action=delete&amp;entry=$entry",180);
      //if ($user_lvl >= $action_permission['delete']) makebutton($lang_vendor['del_spawns'], "vendor.php?action=delete_spwn&amp;entry=$entry",180);
    }

    // scripts/export should be okay without permission check
    makebutton(lang('vendor', 'save_to_script'), "javascript:do_submit('form1',1)",180);
    $output .= "
        </td>
      </tr>
      <tr>
        <td>";
    makebutton(lang('vendor', 'lookup_vendor'), "vendor.php",760);
    $output .= "
        </td>
      </tr>
    </table>
  </center>";
  }
  else
  {
    error(lang('vendor', 'item_not_found'));
    exit();
  }
}


//########################################################################################################################
//DO UPDATE VENDOR
//########################################################################################################################

function do_update($mode)
{
  global $world_db, $realm_id, $action_permission, $user_lvl, $locales_search_option, $sqlw;

  $deplang = get_lang_id();

  if (!isset($_POST['entry']) || $_POST['entry'] === '')
    redirect("vendor.php?error=1");

  $entry = $sqlw->quote_smart($_POST['entry']);


  // ADD/EDIT ITEM
  if (isset($_POST['amount']) && $_POST['amount'] != '') 
    $amount = $sqlw->quote_smart($_POST['amount']);
  else 
    $amount = 0;
  
  if (isset($_POST['maxamount']) && $_POST['maxamount'] != '') 
    $max_amount = $sqlw->quote_smart($_POST['maxamount']);
  else 
    $max_amount = 0;
  
  if (isset($_POST['inctime']) && $_POST['inctime'] != '') 
    $inctime = $sqlw->quote_smart($_POST['inctime']);
  else 
    $inctime = 0;
  
  if (isset($_POST['item']) && $_POST['item'] != '') 
    $item = $sqlw->quote_smart($_POST['item']);
  else 
    $item = 0;
  
  if (isset($_POST['extended_cost']) && $_POST['extended_cost'] != '') 
    $extended_cost = $sqlw->quote_smart($_POST['extended_cost']);
  else 
    $extended_cost = 0;
  

  // RESTRICTIONS
  if (isset($_POST['racemask']) && $_POST['racemask'] != '')
    $racemask = $sqlw->quote_smart($_POST['racemask']);
  else 
    $racemask = 0;

  if (isset($_POST['reqrepfaction']) && $_POST['reqrepfaction'] != '') 
    $reqrepfaction = $sqlw->quote_smart($_POST['reqrepfaction']);
  else 
    $reqrepfaction = 0;

  if (isset($_POST['reqrepfactionvalue']) && $_POST['reqrepfactionvalue'] != '')
    $reqrepfactionvalue = $sqlw->quote_smart($_POST['reqrepfactionvalue']);
  else
    $reqrepfactionvalue = 0;

  if (isset($_POST['cannotbuyattextid']) && $_POST['cannotbuyattextid'] != '') 
    $cannotbuyattextid = $sqlw->quote_smart($_POST['cannotbuyattextid']);
  else 
    $cannotbuyattextid = 0;

  if (isset($_POST['canbuyattextid']) && $_POST['canbuyattextid'] != '') 
    $canbuyattextid = $sqlw->quote_smart($_POST['canbuyattextid']);
  else 
    $canbuyattextid = 0;


  // insert or update creature
  if($mode == "1")
  {
    // check if item already exists
    $query = "SELECT * FROM vendors WHERE entry = '".$entry."' AND item = '".$item."'";
    $result = $sqlw->query($query);
    if($sqlw->num_rows($result) > 0)
    {
      $sql_query = "UPDATE vendors SET entry = '".$entry."', item = '".$item."', amount = '".$amount."', 
                    max_amount = '".$max_amount."', inctime = '".$inctime."', 
                    extended_cost = '".$extended_cost."' WHERE entry = '".$entry."' AND item = '".$item."'";
    }
    else
    {
      // ADD NEW ITEM
      $sql_query = "INSERT INTO vendors ( entry, item, amount, max_amount, inctime,
                extended_cost) VALUES ( '$entry', '$item', '$amount', '$max_amount', '$inctime',
                '$extended_cost' )";
    }
  }
  elseif($mode == "2")
  {
    // DELETE ITEM(s)
    $sql_query = "";
    foreach($_POST['check'] as $item)
    {
      $sql_query .= "DELETE FROM vendors WHERE item = '".$item."';\n";
    }
  }
  elseif($mode == "3")
  {
    // UPDATE RESTRICTIONS
    // check if we have an entry
    // if we do, update it, else make a new one
    $query = "SELECT * FROM vendor_restrictions WHERE entry = '".$entry."'";
    $result = $sqlw->query($query);
    if($sqlw->num_rows($result) > 0)
    {
      $sql_query = "UPDATE vendor_restrictions SET racemask = '".$racemask."', reqrepfaction = '".$reqrepfaction."', 
                   reqrepfactionvalue = '".$reqrepfactionvalue."', cannotbuyattextid = '".$cannotbuyattextid."', 
                   canbuyattextid = '".$canbuyattextid."' WHERE entry = '".$entry."'";
    }
    else
    {
      $sql_query = "INSERT INTO vendor_restrictions (entry, racemask, reqrepfaction, reqrepfactionvalue,
                   cannotbuyattextid, canbuyattextid)
                   VALUES ('".$entry."', '".$racemask."', '".$reqrepfaction."', '".$reqrepfactionvalue."', '".
                   $cannotbuyattextid."', '".$canbuyattextid."')";
    }
  }

  if ( isset($_POST['backup_op']) && ($_POST['backup_op'] == 1) )
  {
    //$sqlw->close();
    Header("Content-type: application/octet-stream");
    Header("Content-Disposition: attachment; filename=vendor_$entry.sql");
    echo $sql_query;
    exit();
    redirect("vendor.php?action=edit&entry=$entry&error=4");
  }
  else
  {
    $sql_query = explode(';',$sql_query);
    foreach($sql_query as $tmp_query) if(($tmp_query)&&($tmp_query != "\n")) $result = $sqlw->query($tmp_query);
    //$sqlw->close();
  }

 if ($result)
   redirect("vendor.php?action=edit&entry=$entry&error=4");
 else
   redirect("vendor.php");

}


//########################################################################################################################
//  VENDOR ADD/EDIT ITEM FORM
//########################################################################################################################
function add_item()
{
  global $locales_search_option, $output, $world_db, $realm_id, $sqlw;

  include_once("./libs/language_select.php");

  $result = $sqlw->query("SELECT count(*) FROM vendors");
  $tot_items = $sqlw->result($result, 0);

  $output .= "<center>
    <fieldset class=\"full_frame\">
      <legend>".lang('vendor', 'editing_item')."</legend><br />
      <form action=\"vendor.php?action=do_add_item&amp;error=2\" method=\"post\" name=\"form\">

        <table class=\"hidden\">
          <tr>
            <td>".lang('vendor', 'entry').":</td>
            <td><input type=\"text\" size=\"10\" maxlength=\"11\" name=\"entry\" /></td>
            <td>".lang('vendor', 'item').":</td>
            <td><input type=\"text\" size=\"10\" maxlength=\"11\" name=\"item\" /></td>
          </tr>
          <tr>
            <td>".lang('vendor', 'quantity').":</td>
            <td><input type=\"text\" size=\"10\" maxlength=\"11\" name=\"amount\" /></td>
            <td>".lang('vendor', 'maxquantity').":</td>
            <td><input type=\"text\" size=\"10\" maxlength=\"11\" name=\"maxamount\" /></td>
          </tr>
          <tr>
            <td>".lang('vendor', 'inctime').":</td>
            <td><input type=\"text\" size=\"10\" maxlength=\"11\" name=\"inctime\" /></td>
            <td>".lang('vendor', 'extcost').":</td>
            <td><input type=\"text\" size=\"10\" maxlength=\"11\" name=\"extcost\" /></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>";
  makebutton(lang('vendor', 'save'), "javascript:do_submit()",150);
  $output .= "</td></tr>
        </table>
      </form>
    </fieldset><br /><br /></center>";
}


//#######################################################################################################
//  DELETE VENDOR
//#######################################################################################################
function delete()
{
  global $output, $user_lvl, $action_permission;

  if ($user_lvl < $action_permission['delete'] )
    redirect("vendor.php?error=9");


  if(isset($_GET['entry']))
    $entry = $_GET['entry'];
  else
    redirect("vendor.php?error=1");


  $output .= "
  <center>
    <h1><font class=\"error\">".lang('global', 'are_you_sure')."</font></h1>
    <br />
    <font class=\"bold\">".lang('vendor', 'vendorid').": 
      <a href=\"vendor.php?action=edit&amp;entry=$entry\" target=\"_blank\">$entry</a>
      ".lang('global', 'will_be_erased')."
      <br />
      ".lang('vendor', 'all_related_data')."
    </font>
    <br />
    <br />
    <table class=\"hidden\">
          <tr>
            <td>";
  makebutton(lang('global', 'yes'), "vendor.php?action=do_delete&amp;entry=$entry",120);
  makebutton(lang('global', 'no'), "vendor.php",120);
  $output .= "</td>
          </tr>
        </table>
      </center>
      <br />";
}


//########################################################################################################################
//  DO DELETE VENDOR
//########################################################################################################################
function do_delete()
{
  global $world_db, $realm_id, $user_lvl, $action_permission, $sqlw;

  if ($user_lvl < $action_permission['delete'] )
    redirect("vendor.php?error=9");

  if(isset($_GET['entry'])) $entry = $_GET['entry'];
    else redirect("vendor.php?error=1");

  $sqlw->query("DELETE FROM vendors WHERE entry = '$entry'");

  //$sqlw->close();
  redirect("vendor.php");
 }


//########################################################################################################################
// MAIN
//########################################################################################################################
$err = (isset($_GET['error'])) ? $_GET['error'] : NULL;

$output .= "
    <div class=\"bubble\">
      <div class=\"top\">";
switch ($err)
{
  case 1:
    $output .= "<h1><font class=\"error\">".lang('global', 'empty_fields')."</font></h1>";
    break;
  case 2:
    $output .= "<h1><font class=\"error\">".lang('vendor', 'search_results')."</font></h1>";
    break;
  case 3:
    $output .= "<h1><font class=\"error\">".lang('vendor', 'add_new_vendor')."</font></h1>";
    break;
  case 4:
    $output .= "<h1><font class=\"error\">".lang('vendor', 'edit_vendor')."</font></h1>";
    break;
  case 5:
    $output .= "<h1><font class=\"error\">".lang('vendor', 'err_adding_new')."</font></h1>";
    break;
  case 6:
    $output .= "<h1><font class=\"error\">".lang('vendor', 'err_no_fields_updated')."</font></h1>";
    break;
  case 7:
    $output .= "<h1><font class=\"error\">".lang('vendor', 'add_new_success')."</font></h1>";
    break;
  case 8:
    $output .= "<h1><font class=\"error\">".lang('global', 'err_invalid_input')."</font></h1>";
    break;
  case 9:
    $output .= "<h1><font class=\"error\">".lang('global', 'err_no_permission')."</font></h1>";
    break;
  case 10:
    $output .= "<h1><font class=\"error\">".lang('vendor', 'editing_item')."</font></h1>";
    break;
  default: //no error
    $output .= "<h1>".lang('vendor', 'search_creatures')."</h1>";
}
$output .= "</div>";

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

switch ($action)
{
  case "search":
    search();
    break;
  case "do_search":
    do_search();
    break;
//case "add_new":
   //do_insert_update(1);
   //break;
  case "add_item":
    add_item();
    break;
  case "do_add_item":
    do_update(1);
    break;
  case "del_item":
    do_update(2);
    break;
  case "do_update":
    do_update();
    break;
  case "do_rest_update":
    do_update(3);
    break;
  case "edit":
    do_insert_update(0);
    break;
  case "delete":
    delete();
    break;
  case "do_delete":
    do_delete();
    break;
  default:
     search();
}

require_once("footer.php");
?>
