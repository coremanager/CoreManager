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
/*require_once("libs/map_zone_lib.php");*/
valid_login($action_permission['view']);

//#############################################################################
// BROWSE WORLD BROADCAST MESSAGES
//#############################################################################
function browse_wbm()
{
  global $output, $world_db, $realm_id, $arcm_db, $itemperpage,
    $action_permission, $user_lvl, $sqlw, $sqlm;

  //==========================$_GET and SECURE=================================
  $start = (isset($_GET['start'])) ? $sqlw->quote_smart($_GET['start']) : 0;
  if (is_numeric($start)); else $start=0;

  $order_by = (isset($_GET['order_by'])) ? $sqlw->quote_smart($_GET['order_by']) : "entry";
  if (!preg_match("/^[_[:lower:]]{1,12}$/", $order_by)) $order_by="entry";

  $dir = (isset($_GET['dir'])) ? $sqlw->quote_smart($_GET['dir']) : 1;
  if (!preg_match("/^[01]{1}$/", $dir)) $dir=1;

  $order_dir = ($dir) ? "ASC" : "DESC";
  $dir = ($dir) ? 0 : 1;
  //==========================$_GET and SECURE end=============================

  //==========================Browse/Search CHECK==============================
  $search_by = '';
  $search_value = '';
  if(isset($_GET['search_value']) && isset($_GET['search_by']))
  {
    $search_value = $sqlw->quote_smart($_GET['search_value']);
    $search_by = $sqlw->quote_smart($_GET['search_by']);
    $search_menu = array("entry", "text", "percent");
    if (!in_array($search_by, $search_menu)) $search_by = 'text';
    unset($search_menu);

    if (preg_match('/^[\t\v\b\f\a\n\r\\\"\'\? <>[](){}_=+-|!@#$%^&*~`.,0123456789\0]{1,30}$/', $search_value)) redirect("worldbroadcast.php?error=1");
    $query_1 = $sqlw->query("SELECT count(*) FROM worldbroadcast WHERE $search_by LIKE '%$search_value%'");
    $query = $sqlw->query("SELECT entry,text,percent
      FROM worldbroadcast WHERE $search_by LIKE '%$search_value%' ORDER BY $order_by $order_dir LIMIT  $start, $itemperpage");
  }
  else
  {
    $query_1 = $sqlw->query("SELECT count(*) FROM worldbroadcast");
    $query = $sqlw->query("SELECT entry,text,percent
      FROM worldbroadcast ORDER BY $order_by $order_dir LIMIT $start, $itemperpage");
  }

  $all_record = $sqlw->result($query_1,0);
  unset($query_1);

  //=====================top tage navigaion starts here========================
  $output .="
        <center>
          <table class=\"top_hidden\">
            <tr>
              <td>";
  if($user_lvl >= $action_permission['insert'])
  {
    makebutton(lang('wbm', 'add_new'), "worldbroadcast.php?action=add_wbm",130);
  }
                makebutton(lang('global', 'back'), "javascript:window.history.back()", 130);
  ($search_by && $search_value) ? makebutton(lang('wbm', 'messages'), "worldbroadcast.php\" type=\"def", 130) : $output .= "";
  $output .= "
              </td>
              <td width=\"25%\" align=\"right\" rowspan=\"2\">";
  $output .= generate_pagination("worldbroadcast.php?order_by=$order_by&amp;dir=".(($dir) ? 0 : 1).( $search_value && $search_by ? "&amp;search_by=$search_by&amp;search_value=$search_value" : "" ), $all_record, $itemperpage, $start);
  $output .= "
              </td>
            </tr>
            <tr align=\"left\">
              <td>
                <table class=\"hidden\">
                  <tr>
                    <td>
                      <form action=\"worldbroadcast.php\" method=\"get\" name=\"form\">
                        <input type=\"hidden\" name=\"error\" value=\"4\" />
                        <input type=\"text\" size=\"24\" name=\"search_value\" value=\"$search_value\" />
                        <select name=\"search_by\">
                          <option value=\"text\"".($search_by == 'text' ? " selected=\"selected\"" : "").">".lang('wbm', 'text')."</option>
                          <option value=\"entry\"".($search_by == 'entry' ? " selected=\"selected\"" : "").">".lang('wbm', 'entry')."</option>
                          <option value=\"percent\"".($search_by == 'percent' ? " selected=\"selected\"" : "").">".lang('wbm', 'percent')."</option>
                        </select>
                      </form>
                    </td>
                    <td>";
                      makebutton(lang('global', 'search'), "javascript:do_submit()",80);
  $output .= "
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>";
  //======================top tage navigaion ENDS here ========================

  $output .= "
          <script type=\"text/javascript\">
            answerbox.btn_ok='".lang('global', 'yes')."';
            answerbox.btn_cancel='".lang('global', 'no')."';
            var question = '".lang('global', 'are_you_sure')."';
            var del_wbm = 'worldbroadcast.php?action=del_wbm&amp;order_by=$order_by&amp;start=$start&amp;dir=$dir&amp;entry=';
          </script>
          <table class=\"lined\">
            <tr>";
  if($user_lvl >= $action_permission['delete'])
    $output .= "
              <th width=\"5%\">".lang('global', 'delete_short')."</th>";
  $output .= "
              <th width=\"5%\"><a href=\"worldbroadcast.php?order_by=entry&amp;start=$start".( $search_value && $search_by ? "&amp;search_by=$search_by&amp;search_value=$search_value" : "" )."&amp;dir=$dir\"".($order_by=='entry' ? " class=\"$order_dir\"" : "").">".lang('wbm', 'entry')."</a></th>
              <th width=\"45%\"><a href=\"worldbroadcast.php?order_by=name&amp;start=$start".( $search_value && $search_by ? "&amp;search_by=$search_by&amp;search_value=$search_value" : "" )."&amp;dir=$dir\"".($order_by=='text' ? " class=\"$order_dir\"" : "").">".lang('wbm', 'text')."</a></th>
              <th width=\"5%\"><a href=\"worldbroadcast.php?order_by=mapid&amp;start=$start".( $search_value && $search_by ? "&amp;search_by=$search_by&amp;search_value=$search_value" : "" )."&amp;dir=$dir\"".($order_by=='percent' ? " class=\"$order_dir\"" : "").">".lang('wbm', 'percent')."</a></th>
            </tr>";

  while ($data = $sqlw->fetch_row($query))
  {
    $output .= "
            <tr>";
    if($user_lvl >= $action_permission['delete'])
      $output .= "
              <td><img src=\"img/aff_cross.png\" alt=\"\" onclick=\"answerBox('".lang('global', 'delete').": &lt;font color=white&gt;{$data[1]}&lt;/font&gt;&lt;br /&gt; ' + question, del_wbm + $data[0]);\" id=\"delete_entry_cursor\" /></td>";
    $output .= "
              <td>$data[0]</td>
              <td>";
    if($user_lvl >= $action_permission['update'])
      $output .="
                <a href=\"worldbroadcast.php?action=edit_wbm&amp;entry=$data[0]\">$data[1]</a>";
    else
      $output .="$data[1]";
    $output .="
              </td>
              <td>$data[2]</td>
            </tr>";
  }
  unset($query);
  unset($data);

  $output .= "
            <tr>
              <td  colspan=\"7\" class=\"hidden\" align=\"right\" width=\"25%\">";
  $output .= generate_pagination("worldbroadcast.php?order_by=$order_by&amp;dir=".(($dir) ? 0 : 1).( $search_value && $search_by ? "&amp;search_by=$search_by&amp;search_value=$search_value" : "" ), $all_record, $itemperpage, $start);
  $output .= "
              </td>
            </tr>
            <tr>
              <td colspan=\"7\" class=\"hidden\" align=\"right\">".lang('wbm', 'tot_messages')." : $all_record</td>
            </tr>
          </table>
        </center>
";

}


//#############################################################################
// DO DELETE WORLD BROADCAST MESSAGE
//#############################################################################
function del_wbm()
{
  global $world_db, $realm_id, $action_permission, $sqlw;

  valid_login($action_permission['delete']);

  if(!isset($_GET['entry'])) redirect("Location: worldbroadcast.php?error=1");

  $entry = $sqlw->quote_smart($_GET['entry']);
  if(is_numeric($entry)); else redirect("worldbroadcast.php?error=1");

  //==========================$_GET and SECURE=================================
  $start = (isset($_GET['start'])) ? $sqlw->quote_smart($_GET['start']) : 0;
  if (is_numeric($start)); else $start=0;

  $order_by = (isset($_GET['order_by'])) ? $sqlw->quote_smart($_GET['order_by']) : "entry";
  if (!preg_match("/^[_[:lower:]]{1,10}$/", $order_by)) $order_by="entry";

  $dir = (isset($_GET['dir'])) ? $sqlw->quote_smart($_GET['dir']) : 1;
  if (!preg_match("/^[01]{1}$/", $dir)) $dir=1;

  $order_dir = ($dir) ? "ASC" : "DESC";
  $dir = ($dir) ? 0 : 1;
  //==========================$_GET and SECURE end=============================

  $sqlw->query("DELETE FROM worldbroadcast WHERE entry = '$entry'");
  if ($sqlw->affected_rows() != 0)
  {
    redirect("worldbroadcast.php?error=3&order_by=$order_by&start=$start&dir=$dir");
  }
  else
  {
    redirect("worldbroadcast.php?error=5");
  }
}


//#############################################################################
//  EDIT WORLD BROADCAST MESSAGE
//#############################################################################
function edit_wbm()
{
  global  $output, $world_db, $realm_id, $arcm_db, $action_permission, $user_lvl, $sqlm, $sqlw;

  valid_login($action_permission['update']);

  if(!isset($_GET['entry'])) redirect("Location: worldbroadcast.php?error=1");

  $entry = $sqlw->quote_smart($_GET['entry']);
  if(is_numeric($entry)); else redirect("worldbroadcast.php?error=1");

  $query = $sqlw->query("SELECT entry,text,percent FROM worldbroadcast WHERE entry = '$entry'");

  if ($sqlw->num_rows($query) == 1)
  {
    $wbm = $sqlw->fetch_row($query);
    $output .= "
        <script type=\"text/javascript\">
          answerbox.btn_ok='".lang('global', 'yes')."';
          answerbox.btn_cancel='".lang('global', 'no')."';
        </script>
        <center>
          <fieldset class=\"half_frame\">
            <legend>".lang('wbm', 'edit_wbm')."</legend>
            <form method=\"get\" action=\"worldbroadcast.php\" name=\"form\">
            <input type=\"hidden\" name=\"action\" value=\"do_edit_wbm\" />
            <input type=\"hidden\" name=\"entry\" value=\"$entry\" />
            <table class=\"flat\">
              <tr>
                <td>".lang('wbm', 'entry')."</td>
                <td>$wbm[0]</td>
              </tr>
              <tr>
                <td>".lang('wbm', 'text')."</td>
                <td><input type=\"text\" name=\"new_text\" size=\"42\" maxlength=\"98\" value=\"$wbm[1]\" /></td>
              </tr>
               <tr>
                 <td>".lang('wbm', 'percent')."</td>
                 <td><input type=\"text\" name=\"new_percent\" size=\"42\" maxlength=\"36\" value=\"$wbm[2]\" /></td>
               </tr>
               <tr>
                 <td>";
    if($user_lvl >= $action_permission['delete'])
      makebutton(lang('wbm', 'delete_wbm'), "#\" onclick=\"answerBox('".lang('global', 'delete').": &lt;font color=white&gt;{$wbm[1]}&lt;/font&gt; &lt;br /&gt; ".lang('global', 'are_you_sure')."', 'worldbroadcast.php?action=del_wbm&amp;entry=$entry');\" type=\"wrn",130);
    $output .= "
                 </td>
                 <td>";
                       makebutton(lang('wbm', 'update_wbm'), "javascript:do_submit()",130);
                       makebutton(lang('global', 'back'), "worldbroadcast.php\" type=\"def",130);
    $output .= "
                 </td>";
    $output .= "
               </tr>
             </table>
           </form>
         </fieldset>
         <br /><br />
       </center>";
  }
  else
    error(lang('global', 'err_no_records_found'));

}


//#############################################################################
//  DO EDIT WORLD BROADCAST MESSAGE
//#############################################################################
function do_edit_wbm()
{
  global $world_db, $realm_id, $action_permission, $sqlw;

  valid_login($action_permission['update']);

  if( empty($_GET['entry']) || !isset($_GET['new_text']) || !isset($_GET['new_percent']))
    redirect("worldbroadcast.php?error=1");

  $entry = $sqlw->quote_smart($_GET['entry']);
  $percent = $sqlw->quote_smart($_GET['new_percent']);
  if(is_numeric($entry)); else redirect("worldbroadcast.php?error=1");
  if(is_numeric($percent)); else redirect("worldbroadcast.php?error=1");

  $new_text = $sqlw->quote_smart($_GET['new_text']);
  $new_percent = $sqlw->quote_smart($_GET['new_percent']);

  $sqlw->query("UPDATE worldbroadcast SET text='".$new_text."', percent ='".$new_percent."' WHERE entry = '".$entry."'");

  if ($sqlw->affected_rows())
  {
    redirect("worldbroadcast.php?error=3");
  }
  else
  {
    redirect("worldbroadcast.php?error=5");
  }
}


//#############################################################################
//  ADD NEW WORLD BROADCAST MESSAGE
//#############################################################################
function add_wbm()
{
  global  $output, $arcm_db, $action_permission;

  valid_login($action_permission['insert']);

  $output .= "
        <center>
          <fieldset class=\"half_frame\">
            <legend>".lang('wbm', 'add_new_wbm')."</legend>
            <form method=\"get\" action=\"worldbroadcast.php\" name=\"form\">
              <input type=\"hidden\" name=\"action\" value=\"do_add_wbm\" />
              <table class=\"flat\">
                <tr>
                  <td>".lang('wbm', 'text')."</td>
                  <td><input type=\"text\" name=\"text\" size=\"42\" maxlength=\"255\" value=\"".lang('wbm', 'text')."\" /></td>
                </tr>
                <tr>
                  <td>".lang('wbm', 'percent')."</td>
                  <td><input type=\"text\" name=\"percent\" size=\"42\" maxlength=\"36\" value=\"0\" /></td>
                </tr>
                <tr>
                  <td>
                  </td>
                  <td>";
                    makebutton(lang('wbm', 'add_new'), "javascript:do_submit()",130);
                    makebutton(lang('global', 'back'), "worldbroadcast.php\" type=\"def",130);
  $output .= "
                  </td>
                </tr>
              </table>
            </form>
          </fieldset>
          <br /><br />
        </center>
";
}


//#############################################################################
//  DO ADD WORLD BROADCAST ENTRY
//#############################################################################
function do_add_wbm()
{
  global $world_db, $realm_id, $action_permission, $sqlw;

  valid_login($action_permission['insert']);
  if( !isset($_GET['text']) || !isset($_GET['percent']))
    redirect("worldbroadcast.php?error=1");

  $text = $sqlw->quote_smart($_GET['text']);
  $percent = $sqlw->quote_smart($_GET['percent']);

  $sqlw->query("INSERT INTO worldbroadcast (entry,text,percent) VALUES (NULL,'$text','$percent')");

  if ($sqlw->affected_rows())
  {
    redirect("worldbroadcast.php?error=3");
  }
  else
  {
    redirect("worldbroadcast.php?error=5");
  }
}


//#############################################################################
// MAIN
//#############################################################################
$err = (isset($_GET['error'])) ? $_GET['error'] : NULL;

$output .= "
      <div class=\"bubble\">
        <div class=\"top\">";

//$lang_wbm = lang_wbm();

switch ($err)
{
  case 1:
    $output .= "
          <h1><font class=\"error\">".lang('global', 'empty_fields')."</font></h1>";
    break;
  case 2:
    $output .= "
          <h1><font class=\"error\">".lang('global', 'err_no_search_passed')."</font></h1>";
    break;
  case 3:
    $output .= "
          <h1><font class=\"error\">".lang('wbm', 'wbm_updated')."</font></h1>";
    break;
  case 4:
    $output .= "
          <h1><font class=\"error\">".lang('wbm', 'search_results')."</font></h1>";
    break;
  case 5:
    $output .= "
          <h1><font class=\"error\">".lang('wbm', 'error_updating')."</font></h1>";
    break;
  default: //no error
    $output .= "
          <h1>".lang('wbm', 'wbm')."</h1>";
}

unset($err);

$output .= "
        </div>";

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

switch ($action)
{
  case "edit_wbm":
    edit_wbm();
    break;
  case "do_edit_wbm":
    do_edit_wbm();
    break;
  case "add_wbm":
    add_wbm();
    break;
  case "do_add_wbm":
    do_add_wbm();
    break;
  case "del_wbm":
    del_wbm();
    break;
  default:
    browse_wbm();
}

unset($action);
unset($action_permission);
//unset($lang_wbm);

require_once("footer.php");

?>
