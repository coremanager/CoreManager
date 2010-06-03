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
valid_login($action_permission['view']);

//#############################################################################
//  BROWSE  TICKETS
//#############################################################################
function browse_tickets()
{
  global $output, $characters_db, $realm_id, $action_permission, $user_lvl, $itemperpage, $sql, $core;

  //==========================$_GET and SECURE=================================
  $start = (isset($_GET['start'])) ? $sql['char']->quote_smart($_GET['start']) : 0;
  if (is_numeric($start)); else $start=0;

  $order_by = (isset($_GET['order_by'])) ? $sql['char']->quote_smart($_GET['order_by']) : 'guid';
  if (preg_match('/^[_[:lower:]]{1,10}$/', $order_by)); else $order_by = 'guid';

  $dir = (isset($_GET['dir'])) ? $sql['char']->quote_smart($_GET['dir']) : 1;
  if (preg_match('/^[01]{1}$/', $dir)); else $dir=1;

  $order_dir = ($dir) ? 'ASC' : 'DESC';
  $dir = ($dir) ? 0 : 1;
  //==========================$_GET and SECURE end=============================

  //get total number of items
  if ( $core == 1 )
    $query_1 = $sql['char']->query('SELECT count(*) FROM gm_tickets WHERE deleted=0');
  else
    $query_1 = $sql['char']->query('SELECT count(*) FROM gm_tickets WHERE closed=0');
  $all_record = $sql['char']->result($query_1,0);
  unset($query_1);

  if ( $core == 1 )
    $query = $sql['char']->query("SELECT gm_tickets.ticketid, gm_tickets.playerGuid, gm_tickets.message,
                           `characters`.name, gm_tickets.deleted, gm_tickets.timestamp
                         FROM gm_tickets
                         LEFT JOIN `characters` ON gm_tickets.playerGuid = `characters`.`guid`
                         ORDER BY $order_by $order_dir LIMIT $start, $itemperpage");
  else
    $query = $sql['char']->query("SELECT gm_tickets.guid, gm_tickets.playerGuid, gm_tickets.message,
                           `characters`.name, gm_tickets.closed, gm_tickets.timestamp
                         FROM gm_tickets
                         LEFT JOIN `characters` ON gm_tickets.playerGuid = `characters`.`guid`
                         ORDER BY $order_by $order_dir LIMIT $start, $itemperpage");

  $output .="
        <script type=\"text/javascript\" src=\"libs/js/check.js\"></script>
        <center>
          <table class=\"top_hidden\">
            <tr>
              <td width=\"25%\" align=\"right\">";
  $output .= generate_pagination("ticket.php?action=browse_tickets&amp;order_by=$order_by&amp;dir=".!$dir, $all_record, $itemperpage, $start);
  $output .= "
              </td>
            </tr>
          </table>";
  $output .= "
          <form method=\"get\" action=\"ticket.php\" name=\"form\">
            <input type=\"hidden\" name=\"action\" value=\"delete_tickets\" />
            <input type=\"hidden\" name=\"start\" value=\"$start\" />
            <table class=\"lined\">
              <tr>";
  if($user_lvl >= $action_permission['delete'])
    $output .="
                <th width=\"7%\"><input name=\"allbox\" type=\"checkbox\" value=\"Check All\" onclick=\"CheckAll(document.form);\" /></th>";
  if($user_lvl >= $action_permission['update'])
    $output .="
                <th width=\"7%\">".lang('global', 'edit')."</th>";
  $output .="
                <th width=\"10%\"><a href=\"ticket.php?order_by=guid&amp;start=$start&amp;dir=$dir\">".($order_by=='guid' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."".lang('ticket', 'id')."</a></th>
                <th width=\"16%\"><a href=\"ticket.php?order_by=guid&amp;start=$start&amp;dir=$dir\">".($order_by=='guid' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."".lang('ticket', 'sender')."</a></th>";
  $output .="
                <th width=\"40%\">".lang('ticket', 'message')."</th>
                <th width=\"10%\">".lang('ticket', 'date')."</th>
              </tr>";
  while ($ticket = $sql['char']->fetch_row($query))
  {
    if (!$ticket[4])
    {
    $output .= "
              <tr>";
    if($user_lvl >= $action_permission['delete'])
      $output .="
                <td><input type=\"checkbox\" name=\"check[]\" value=\"$ticket[0]\" onclick=\"CheckCheckAll(document.form);\" /></td>";
    if($user_lvl >= $action_permission['update'])
      $output .="
                <td><a href=\"ticket.php?action=edit_ticket&amp;error=4&amp;id=$ticket[0]\"><img src='./img/edit.png' alt='".lang('global', 'edit')."' /></a></td>";
    $output .="
                <td>$ticket[0]</td>
                <td><a href=\"char.php?id=$ticket[1]\">".htmlentities($ticket[3])."</a></td>
                <td>".htmlentities($ticket[2])."</td>
                <td>".date('G:i:s m-d-Y', $ticket[5])."</td>
              </tr>";
    }
  }
  unset($query);
  unset($ticket);
  $output .= "
              <tr>
                <td colspan=\"5\" align=\"right\" class=\"hidden\" width=\"25%\">";
  $output .= generate_pagination("ticket.php?action=browse_tickets&amp;order_by=$order_by&amp;dir=".!$dir, $all_record, $itemperpage, $start);
  $output .= "
                </td>
              </tr>
              <tr>
                <td colspan=\"3\" align=\"left\" class=\"hidden\">";
  if($user_lvl >= $action_permission['delete'])
                  makebutton(lang('ticket', 'del_selected_tickets'), "javascript:do_submit()\" type=\"wrn",230);
  $output .= "
                </td>
                <td colspan=\"2\" align=\"right\" class=\"hidden\">".lang('ticket', 'tot_tickets').": $all_record</td>
              </tr>
            </table>
          </form>
          <br />
        </center>
";

}


//########################################################################################################################
//  DELETE TICKETS
//########################################################################################################################
function delete_tickets()
{
  global $characters_db, $realm_id, $action_permission, $sql;

  valid_login($action_permission['delete']);

  if(!isset($_GET['check'])) redirect("ticket.php?error=1");

  $check = $sql['char']->quote_smart($_GET['check']);

  $deleted_tickets = 0;
  for ($i=0; $i<count($check); $i++)
  {
    if ($check[$i] != "" )
    {
      $query = $sql['char']->query("DELETE FROM gm_tickets WHERE ticketid = '$check[$i]'");
      $deleted_tickets++;
    }
  }

  if (0 == $deleted_tickets)
    redirect('ticket.php?error=3');
  else
    redirect('ticket.php?error=2');
}


//########################################################################################################################
//  EDIT TICKET
//########################################################################################################################
function edit_ticket()
{
  global  $output, $characters_db, $realm_id, $action_permission, $sql, $core;

  valid_login($action_permission['update']);

  if(!isset($_GET['id'])) redirect("Location: ticket.php?error=1");

  $id = $sql['char']->quote_smart($_GET['id']);
  if(is_numeric($id)); else redirect("ticket.php?error=1");

  if ( $core == 1 )
    $query = $sql['char']->query("SELECT gm_tickets.ticketid, gm_tickets.playerGuid, gm_tickets.message,
                           `characters`.name, gm_tickets.timestamp
                         FROM gm_tickets
                         LEFT JOIN characters ON gm_tickets.`playerGuid` = `characters`.`guid`
                         WHERE gm_tickets.playerGuid = `characters`.`guid` AND gm_tickets.ticketid = '$id'");
  else
    $query = $sql['char']->query("SELECT gm_tickets.guid, gm_tickets.playerGuid, gm_tickets.message,
                           `characters`.name, gm_tickets.timestamp
                         FROM gm_tickets
                         LEFT JOIN characters ON gm_tickets.`playerGuid` = `characters`.`guid`
                         WHERE gm_tickets.playerGuid = `characters`.`guid` AND gm_tickets.guid = '$id'");

  if ($ticket = $sql['char']->fetch_row($query))
  {
    $output .= "
        <center>
          <fieldset id=\"ticket_edit_field\">
            <legend>".lang('ticket', 'edit_reply')."</legend>
            <form method=\"post\" action=\"ticket.php?action=do_edit_ticket\" name=\"form\">
              <input type=\"hidden\" name=\"id\" value=\"$id\" />
                <table class=\"flat\">
                  <tr>
                    <td>".lang('ticket', 'id')."</td>
                    <td>$id</td>
                  </tr>
                  <tr>
                    <td>".lang('ticket', 'submitted_by').":</td>
                    <td><a href=\"char.php?id=$ticket[1]\">".htmlentities($ticket[3])."</a></td>
                  </tr>
                  <tr>
                    <td>".lang('ticket', 'date').":</td>
                    <td>".date('G:i:s m-d-Y', $ticket[4])."</td>
                  </tr>
                  <tr>
                    <td valign=\"top\">".lang('ticket', 'message')."</td>
                    <td><textarea name=\"new_text\" rows=\"5\" cols=\"40\">".htmlentities($ticket[2])."</textarea></td>
                  </tr>
                  <tr>
                    <td>
                      <table class=\"hidden\">
                        <tr>
                          <td>";
                            makebutton(lang('ticket', 'update'), "javascript:do_submit()\" type=\"wrn",140);
    $output .= "          </td>
                        </tr>
                        <tr>
                          <td>";
                            makebutton(lang('ticket', 'send_ingame_mail'), "mail.php?type=ingame_mail&amp;to=$ticket[3]",140);
    $output .= "          </td>
                        </tr>
                      </table>
                    </td>
                    <td>
                      <table class=\"hidden\">
                        <tr>
                          <td>";
                            makebutton(lang('ticket', 'abandon'), "ticket.php?action=do_mark_ticket&amp;id=$id\" type=\"wrn",230);
    $output .= "
                          </td>
                       </tr>
                       <tr>
                          <td>";
                            makebutton(lang('global', 'back'), "javascript:window.history.back()\" type=\"def",130);
    $output .= "
                          </td>
                        </tr>
                      </table>";
    $output .= "
                    </td>
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


//########################################################################################################################
//  DO EDIT  TICKET
//########################################################################################################################
function do_edit_ticket()
{
  global $characters_db, $realm_id, $action_permission, $sql;

  valid_login($action_permission['update']);

  if(empty($_POST['new_text']) || empty($_POST['id']) )
    redirect("ticket.php?error=1");

  $new_text = $sql['char']->quote_smart($_POST['new_text']);
  $id = $sql['char']->quote_smart($_POST['id']);
  if(is_numeric($id)); else redirect("ticket.php?error=1");

  $query = $sql['char']->query("UPDATE gm_tickets SET message='$new_text' WHERE ticketid = '$id'");

  if ($sql['char']->affected_rows())
  {
    redirect("ticket.php?error=5");
  }
  else
  {
    redirect("ticket.php?error=6");
  }
}


//########################################################################################################################
//  DO MARK TICKET AS ABANDONED
//########################################################################################################################
function do_mark_ticket()
{
  global $characters_db, $realm_id, $action_permission, $sql;

  valid_login($action_permission['update']);

  if (empty($_GET['id']))
    redirect("ticket.php?error=1");

  $id = $sql['char']->quote_smart($_GET['id']);
  if(!is_numeric($id))
    redirect("ticket.php?error=1");
  $query = $sql['char']->query("UPDATE gm_tickets SET deleted=1 WHERE ticketid = '$id'");

  if ($sql['char']->affected_rows())
  {
    redirect("ticket.php?error=5");
  }
  else
  {
    redirect("ticket.php?error=6");
  }
}


//########################################################################################################################
// MAIN
//########################################################################################################################
$err = (isset($_GET['error'])) ? $_GET['error'] : NULL;

$output .= "
      <div class=\"bubble\">
        <div class=\"top\">";

//$lang_ticket = lang_ticket();

switch ($err)
{
  case 1:
    $output .= "
          <h1><font class=\"error\">".lang('global', 'empty_fields')."</font></h1>";
    break;
  case 2:
    $output .= "
          <h1><font class=\"error\">".lang('ticket', 'ticked_deleted')."</font></h1>";
    break;
  case 3:
    $output .= "
          <h1><font class=\"error\">".lang('ticket', 'ticket_not_deleted')."</font></h1>";
    break;
  case 4:
    $output .= "
          <h1>".lang('ticket', 'edit_ticked')."</h1>";
    break;
  case 5:
    $output .= "
          <h1><font class=\"error\">".lang('ticket', 'ticket_updated')."</font></h1>";
    break;
  case 6:
    $output .= "
          <h1><font class=\"error\">".lang('ticket', 'ticket_update_err')."</font></h1>";
    break;
  default: //no error
    $output .= "
          <h1>".lang('ticket', 'browse_tickets')."</h1>";
}

unset($err);

$output .= "
        </div>";

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

switch ($action)
{
  case "browse_tickets":
    browse_tickets();
    break;
  case "delete_tickets":
    delete_tickets();
    break;
  case "edit_ticket":
    edit_ticket();
    break;
  case "do_edit_ticket":
    do_edit_ticket();
    break;
  case "do_mark_ticket":
    do_mark_ticket();
    break;
  default:
    browse_tickets();
}

unset($action);
unset($action_permission);
//unset($lang_tikcet);

require_once 'footer.php';

?>
