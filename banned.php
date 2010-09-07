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
valid_login($action_permission['view']);

//########################################################################################################################
// SHOW BANNED LIST
//########################################################################################################################
function show_list()
{
  global $realm_id, $output, $logon_db, $characters_db, $itemperpage, $action_permission, $user_lvl, $sql,
    $core;

  valid_login($action_permission['view']);

  $ban_type = (isset($_GET['ban_type'])) ? $sql['logon']->quote_smart($_GET['ban_type']) : "accounts";

  switch($ban_type)
  {
    case "accounts":
    {
      $key_field = "acct";
      break;
    }
    case "characters":
    {
      $key_field = "guid";
      break;
    }
    case "ipbans":
    {
      $key_field = "ip";
      break;
    }
  }

  //==========================$_GET and SECURE=================================
  $start = (isset($_GET['start'])) ? $sql['logon']->quote_smart($_GET['start']) : 0;
  if (is_numeric($start)); else $start=0;

  $order_by = (isset($_GET['order_by'])) ? $sql['logon']->quote_smart($_GET['order_by']) : "$key_field";
  if (!preg_match("/^[_[:lower:]]{1,12}$/", $order_by)) $order_by="$key_field";

  $dir = (isset($_GET['dir'])) ? $sql['logon']->quote_smart($_GET['dir']) : 1;
  if (!preg_match("/^[01]{1}$/", $dir)) $dir=1;

  $order_dir = ($dir) ? "ASC" : "DESC";
  $dir = ($dir) ? 0 : 1;
  //==========================$_GET and SECURE end=============================

  switch($ban_type)
  {
    case "accounts":
    {
      if ( $core == 1 )
        $query_1 = $sql['logon']->query("SELECT count(*) FROM accounts WHERE banned <> 0");
      else
        $query_1 = $sql['logon']->query("SELECT count(*) FROM account_banned WHERE active <> 0");
      break;
    }
    case "characters":
    {
      if ( $core == 1 )
        $query_1 = $sql['char']->query("SELECT count(*) FROM characters WHERE banned <> 0");
      else
        ;//MaNGOS & Trinity don't ban by character
      break;
    }
    case "ipbans":
    {
      if ( $core == 1 )
        $query_1 = $sql['logon']->query("SELECT count(*) FROM ipbans");
      else
        $query_1 = $sql['logon']->query("SELECT count(*) FROM ip_banned");
      break;
    }
  }

  $all_record = $sql['logon']->result($query_1,0);

  switch($ban_type)
  {
    case "accounts":
    {
      if ( $core == 1 )
        $result = $sql['logon']->query("SELECT acct, banned FROM accounts WHERE banned <> 0 ORDER BY $order_by $order_dir LIMIT $start, $itemperpage");
      else
        $result = $sql['logon']->query("SELECT id AS acct, active AS banned FROM account_banned WHERE active<>0 AND unbandate>UNIX_TIMESTAMP() ORDER BY $order_by $order_dir LIMIT $start, $itemperpage");
      break;
    }
    case "characters":
    {
      if ( $core == 1 )
        $result = $sql['char']->query("SELECT guid, name, banned, banReason FROM $ban_type WHERE banned <> 0 ORDER BY $order_by $order_dir LIMIT $start, $itemperpage");
      else
        ;// MaNGOS & Trinity don't ban by character
      break;
    }
    case "ipbans":
    {
      if ( $core == 1 )
        $result = $sql['logon']->query("SELECT ip, expire FROM ipbans ORDER BY $order_by $order_dir LIMIT $start, $itemperpage");
      else
        $result = $sql['logon']->query("SELECT ip, unbandate AS expire FROM ip_banned ORDER BY $order_by $order_dir LIMIT $start, $itemperpage");
      break;
    }
  }
  
  $this_page = $sql['logon']->num_rows($result);

  $output .= "
        <center>
          <table class=\"top_hidden\">
            <tr>
              <td>";
  switch($ban_type)
  {
    case "accounts":
    {
      if ( $core == 1 )
      {
        makebutton(lang('banned', 'banned_characters'), "banned.php?ban_type=characters",130);
        makebutton(lang('banned', 'banned_ips'), "banned.php?ban_type=ipbans",130);
      }
      else
      {
        // MaNGOS & Trinity don't ban characters, so don't show the "Characters" button
        makebutton(lang('banned', 'banned_ips'), "banned.php?ban_type=ipbans",130);
      }
      break;
    }
    case "characters":
    {
      makebutton(lang('banned', 'banned_accounts'), "banned.php?ban_type=accounts",130);
      makebutton(lang('banned', 'banned_ips'), "banned.php?ban_type=ipbans",130);
      break;
    }
    case "ipbans":
    {
      if ( $core == 1 )
      {
        makebutton(lang('banned', 'banned_accounts'), "banned.php?ban_type=accounts",130);
        makebutton(lang('banned', 'banned_characters'), "banned.php?ban_type=characters",130);
      }
      else
      {
        // MaNGOS & Trinity don't ban characters, so don't show the "Characters" button
        makebutton(lang('banned', 'banned_accounts'), "banned.php?ban_type=accounts",130);
      }
      break;
    }
  }

  if($user_lvl >= $action_permission['insert'])
    makebutton(lang('banned', 'add_to_banned'), "banned.php?action=add_entry\" type=\"wrn",180);
  makebutton(lang('global', 'back'), "javascript:window.history.back()\" type=\"def",130);

  $output .= "
              </td>
              <td align=\"right\">".generate_pagination("banned.php?action=show_list&amp;order_by=$order_by&amp;ban_type=$ban_type&amp;dir=".!$dir, $all_record, $itemperpage, $start)."</td>
            </tr>
          </table>
          <script type=\"text/javascript\">
            answerbox.btn_ok='".lang('global', 'yes_low')."';
            answerbox.btn_cancel='".lang('global', 'no')."';
            var del_banned = 'banned.php?action=do_delete_entry&amp;ban_type=$ban_type&amp;entry=';
          </script>
          <table class=\"lined\">";
  $output .= "<tr class=\"large_bold\"><td colspan=\"5\" class=\"hidden\" align=\"left\">Banned ".$lang_banned[$ban_type].":</td></tr>";
  switch($ban_type)
  {
    case "accounts":
    {
      $output .= "
            <tr>
              <th width=\"5%\">".lang('global', 'delete_short')."</th>
              <th width=\"19%\"><a href=\"banned.php?order_by=$key_field&amp;ban_type=$ban_type&amp;dir=$dir\"".($order_by==$key_field ? " class=\"$order_dir\"" : "").">".lang('banned', 'ip_acc')."</a></th>
              <th width=\"18%\"><a href=\"banned.php?order_by=banned&amp;ban_type=$ban_type&amp;dir=$dir\"".($order_by=='banned' ? " class=\"$order_dir\"" : "").">".lang('banned', 'unbandate')."</a></th>
              <th width=\"25%\"></th>
              <th width=\"33%\"></th>
            </tr>";
      break;
    }
    case "characters":
    {
      $output .= "
            <tr>
              <th width=\"5%\">".lang('global', 'delete_short')."</th>
              <th width=\"19%\"><a href=\"banned.php?order_by=$key_field&amp;ban_type=$ban_type&amp;dir=$dir\"".($order_by==$key_field ? " class=\"$order_dir\"" : "").">".lang('banned', 'ip_acc')."</a></th>
              <th width=\"18%\"><a href=\"banned.php?order_by=guid&amp;ban_type=$ban_type&amp;dir=$dir\"".($order_by=='guid' ? " class=\"$order_dir\"" : "").">".lang('banned', 'character')."</a></th>
              <th width=\"18%\"><a href=\"banned.php?order_by=banned&amp;ban_type=$ban_type&amp;dir=$dir\"".($order_by=='banned' ? " class=\"$order_dir\"" : "").">".lang('banned', 'unbandate')."</a></th>
              <th width=\"25%\"><a href=\"banned.php?order_by=banreason&amp;ban_type=$ban_type&amp;dir=$dir\"".($order_by=='banreason' ? " class=\"$order_dir\"" : "").">".lang('banned', 'banreason')."</a></th>
              <th width=\"33%\"></th>
            </tr>";
      break;
    }
    case "ipbans":
    {
      $output .= "
            <tr>
              <th width=\"5%\">".lang('global', 'delete_short')."</th>
              <th width=\"19%\"><a href=\"banned.php?order_by=$key_field&amp;ban_type=$ban_type&amp;dir=$dir\"".($order_by==$key_field ? " class=\"$order_dir\"" : "").">".lang('banned', 'ip')."</a></th>
              <th width=\"18%\"><a href=\"banned.php?order_by=expire&amp;ban_type=$ban_type&amp;dir=$dir\"".($order_by=='expire' ? " class=\"$order_dir\"" : "").">".lang('banned', 'unbandate')."</a></th>
              <th width=\"25%\"></th>
              <th width=\"33%\"></th>
            </tr>";
      break;
    }
  }

  while ($ban = $sql['logon']->fetch_assoc($result))
  {
    if ($ban_type == "accounts")
    {
      if ( $core == 1 )
        $result1 = $sql['logon']->query("SELECT login FROM accounts WHERE acct='".$ban['acct']."'");
      else
        $result1 = $sql['logon']->query("SELECT username AS login FROM account WHERE id='".$ban['acct']."'");
      $row_name = $sql['logon']->result($result1, 0, 'login');
      $name_out = "<a href=\"user.php?action=edit_user&amp;error=11&amp;acct=".$ban['acct']."\">$row_name</a>";
    }
    elseif ($ban_type == "characters")
    {
      $result = $sql['char']->query("SELECT acct,name FROM characters WHERE guid='".$ban['guid']."'");
      $row_name = $sql['char']->result($result, 0, 'name');
      $owner_acc = $sql['char']->result($result, 0, 'acct');
      $result = $sql['logon']->query("SELECT login FROM accounts WHERE acct='".$owner_acc."'");
      $name_out = $sql['logon']->result($result, 0, 'login');
    }
    else
    {
      $name_out = $ban['ip'];
      $row_name = $ban['ip'];
    }
    $output .= "
            <tr>
              <td>";
    switch($ban_type)
    {
      case "accounts":
      {
        if($user_lvl >= $action_permission['delete'])
          $output .= "
                <img src=\"img/aff_cross.png\" alt=\"\" onclick=\"answerBox('".lang('banned', 'delete').": <font color=white>$row_name</font><br />".lang('global', 'are_you_sure')."', del_banned + '".$ban['acct']."');\" id=\"banned_delete_cursor\" alt=\"\" />";
        $output .= "
              </td>
              <td>$name_out</td>
              <td>".date('d-m-Y G:i', $ban['banned'])."</td>
              <td></td>
              <td></td>
            </tr>";
        break;
      }
      case "characters":
      {
        if($user_lvl >= $action_permission['delete'])
          $output .= "
                <img src=\"img/aff_cross.png\" alt=\"\" onclick=\"answerBox('".lang('banned', 'delete').": <font color=white>$row_name</font><br />".lang('global', 'are_you_sure')."', del_banned + '".$ban['guid']."');\" id=\"banned_delete_cursor\" alt=\"\" />";
        $output .= "
              </td>
              <td>$name_out</td>
              <td>".$ban['name']."</td>
              <td>".date('d-m-Y G:i', $ban['banned'])."</td>
              <td>".$ban['banReason']."</td>
              <td></td>
            </tr>";
        break;
      }
      case "ipbans":
      {
        if($user_lvl >= $action_permission['delete'])
          $output .= "
                <img src=\"img/aff_cross.png\" alt=\"\" onclick=\"answerBox('".lang('banned', 'delete').": <font color=white>$row_name</font><br />".lang('global', 'are_you_sure')."', del_banned + '".$ban['ip']."');\" id=\"banned_delete_cursor\" alt=\"\" />";
        $output .= "
              </td>
              <td>$name_out</td>
              <td>".date('d-m-Y G:i', $ban['expire'])."</td>
              <td></td>
              <td></td>
            </tr>";
        break;
      }
    }
  }
  $output .= "
            <tr>
              <td colspan=\"6\" align=\"right\" class=\"hidden\">".lang('banned', 'tot_banned')." : $all_record</td>
            </tr>
          </table>
          <br/>
        </center>
";

}


//########################################################################################################################
// DO DELETE ENTRY FROM LIST
//########################################################################################################################
function do_delete_entry()
{
  global $logon_db, $characters_db, $realm_id, $action_permission, $user_lvl, $sql;
  valid_login($action_permission['delete']);

  if(isset($_GET['ban_type']))
    $ban_type = $sql['logon']->quote_smart($_GET['ban_type']);
  else
    redirect("banned.php?error=1");

  if(isset($_GET['entry']))
    $entry = $sql['logon']->quote_smart($_GET['entry']);
  else
    redirect("banned.php?error=1");

  switch($ban_type)
  {
    case "accounts":
    {
      $sql['logon']->query("UPDATE accounts SET banned = '0' WHERE acct = '".$entry."'");

      if ($sql['logon']->affected_rows())
        redirect("banned.php?error=3&ban_type=$ban_type");
      else
        redirect("banned.php?error=2&ban_type=$ban_type");

      break;
    }
    case "characters":
    {
      $sql['char']->query("UPDATE characters SET banned = '0', banReason = '' WHERE guid = '".$entry."'");

      if ($sql['char']->affected_rows())
        redirect("banned.php?error=3&ban_type=$ban_type");
      else
        redirect("banned.php?error=2&ban_type=$ban_type");

      break;
    }
    case "ipbans":
    {
      $sql['logon']->query("DELETE FROM ipbans WHERE ip = '".$entry."'");

      if ($sql['logon']->affected_rows())
        redirect("banned.php?error=3&ban_type=$ban_type");
      else
        redirect("banned.php?error=2&ban_type=$ban_type");

      break;
    }
  }
}


//########################################################################################################################
//  BAN NEW IP
//########################################################################################################################
function add_entry()
{
  global $output, $action_permission, $user_lvl;
  valid_login($action_permission['insert']);

  $output .= '
        <center>
          <div class="half_frame fieldset_border">
            <span class="legend">'.lang('banned', 'ban_entry').'</span>
            <form method="get" action="banned.php" name="form">
              <input type="hidden" name="action" value="do_add_entry" />
              <table class="flat">
                <tr>
                  <td>'.lang('banned', 'ban_type').'</td>
                  <td>
                    <select name="ban_type">
                      <option value="ipbans">'.lang('banned', 'ip').'</option>
                      <option value="accounts">'.lang('banned', 'account').'</option>
                      <option value="characters">'.lang('banned', 'character').'</option>
                    </select>
                  </td>
                </tr>
                <tr>
                  <td>'.lang('banned', 'entry').'</td>
                  <td>
                    <input type="text" name="entry" size="24" maxlength="20" value="" />
                  </td>
                </tr>
                <tr>
                  <td>'.lang('banned', 'ban_time').'</td>
                  <td>
                    <input type="text" name="bantime" size="24" maxlength="40" value="1" />
                  </td>
                </tr>
                <tr>
                  <td>'.lang('banned', 'ban_reason').'</td>
                  <td>
                    <input type="text" name="banreason" size="24" maxlength="255" value="" />
                  </td>
                </tr>
                <tr>
                  <td>';
                    makebutton(lang('banned', 'ban'), "javascript:do_submit()\" type=\"wrn",180);
  $output .= '
                  </td>
                  <td>';
                    makebutton(lang('global', 'back'), "banned.php\" type=\"def",130);
  $output .= '
                  </td>
                </tr>
              </table>
            </form>
          </div>
          <br/>
          <br/>
        </center>';
}


//########################################################################################################################
//DO BAN NEW IP/ACC
//########################################################################################################################
function do_add_entry()
{
  global $logon_db, $characters_db, $realm_id, $user_name, $output, $action_permission, $user_lvl, $sql;
  valid_login($action_permission['insert']);

  if( (empty($_GET['ban_type'])) || (empty($_GET['entry'])) || (empty($_GET['bantime'])) )
    redirect("banned.php?error=1&action=add_entry");

  $ban_type = $sql['logon']->quote_smart($_GET['ban_type']);

  $entry = $sql['logon']->quote_smart($_GET['entry']);

  switch($ban_type)
  {
    case "accounts":
    {
      $result1 = $sql['logon']->query("SELECT acct FROM accounts WHERE login ='".$entry."'");
      if (!$sql['logon']->num_rows($result1))
        redirect("banned.php?error=4&action=add_entry");
      else
        $entry = $sql['logon']->result($result1, 0, 'acct');
      break;
    }
    case "characters":
    {
      $result1 = $sql['char']->query("SELECT guid FROM characters WHERE name ='".$entry."'");
      if (!$sql['char']->num_rows($result1))
        redirect("banned.php?error=4&action=add_entry");
      else
        $entry = $sql['char']->result($result1, 0, 'guid');
      break;
    }
    case "ipbans":
    {
      break;
    }
  }

  $bantime = time() + (3600 * $sql['logon']->quote_smart($_GET['bantime']));
  $banreason = (isset($_GET['banreason']) && ($_GET['banreason'] != '')) ? $sql['logon']->quote_smart($_GET['banreason']) : "none";

  switch($ban_type)
  {
    case "accounts":
    {
      $result = $sql['logon']->query("SELECT banned FROM accounts WHERE acct = '".$entry."'");
      $acct_banned = $sql['logon']->result($result, 0);
      if($acct_banned == 0)
        $sql['logon']->query("UPDATE accounts SET banned = '".$bantime."' WHERE acct='".$entry."'");

      if ($sql['logon']->affected_rows())
        redirect("banned.php?error=3&ban_type=$ban_type");
      else
        redirect("banned.php?error=2&ban_type=$ban_type");
      break;
    }
    case "characters":
    {
      $result = $sql['char']->query("SELECT banned FROM characters WHERE guid = '".$entry."'");
      $char_banned = $sql['char']->result($result, 0);
      if($char_banned == 0)
        $sql['char']->query("UPDATE characters SET banned = '".$bantime."', banReason = '".$banreason."' WHERE guid = '".$entry."'");
      
      if ($sql['char']->affected_rows())
        redirect("banned.php?error=3&ban_type=$ban_type");
      else
        redirect("banned.php?error=2&ban_type=$ban_type");
      break;
    }
    case "ipbans":
    {
      $result = $sql['logon']->query("SELECT ip FROM ipbans WHERE ip = '".$entry."'");
      if(!$sql['logon']->num_rows($result))
        $sql['logon']->query("INSERT INTO ipbans (ip, expire) VALUES ('".$entry."','".$bantime."')");

      if ($sql['logon']->affected_rows())
        redirect("banned.php?error=3&ban_type=$ban_type");
      else
        redirect("banned.php?error=2&ban_type=$ban_type");
      break;
    }
  }

}


//########################################################################################################################
// MAIN
//########################################################################################################################
$err = (isset($_GET['error'])) ? $_GET['error'] : NULL;

$output .= "
      <div class=\"bubble\">
          <div class=\"top\">";

//$lang_banned = lang_banned();

switch ($err)
{
  case 1:
    $output .= "
          <h1><font class=\"error\">".lang('global', 'empty_fields')."</font></h1>";
    break;
  case 2:
    $output .= "
          <h1><font class=\"error\">".lang('banned', 'err_del_entry')."</font></h1>";
    break;
  case 3:
    $output .= "
          <h1><font class=\"error\">".lang('banned', 'updated')."</font></h1>";
    break;
  case 4:
    $output .= "
          <h1><font class=\"error\">".lang('banned', 'acc_not_found')."</font></h1>";
    break;
  default: //no error
    $output .= "
          <h1>".lang('banned', 'banned_list')."</h1>";
}
unset($err);

$output .= "
        </div>";

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

switch ($action)
{
  case "do_delete_entry":
    do_delete_entry();
    break;
  case "add_entry":
    add_entry();
    break;
  case "do_add_entry":
    do_add_entry();
    break;
  default:
    show_list();
}

unset($action);
unset($action_permission);
//unset($lang_banned);

require_once("footer.php");

?>
