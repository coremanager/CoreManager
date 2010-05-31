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
require_once 'libs/bb2html_lib.php';
valid_login($action_permission['insert'],'Insert');

//#############################################################################
// BROWSE MOTDs
//#############################################################################
function browse_motd()
{
  global $output, $action_permission, $sqlm;

  valid_login($action_permission['insert']);

  $motds = $sqlm->query("SELECT * FROM motd ORDER BY Priority ASC");

  $output .= '
        <center>
          <table class="lined" align="center">
            <tr>
              <th width="1%">'.lang('global', 'delete_short').'</th>
              <th width="1%">'.lang('global', 'edit').'</th>
              <th width="1%">'.lang('motd', 'enabled').'</th>
              <th width="20%">'.lang('motd', 'message').'</th>
            </tr>';

  while ($motd = $sqlm->fetch_assoc($motds))
  {
    $output .= '
            <tr>
              <td>
                <a href="motd.php?action=delete_motd&id='.$motd['ID'].'&redirect=1"><img src="img/cross.png" /></a>
              </td>
              <td>
                <a href="motd.php?action=edit_motd&id='.$motd['ID'].'&redirect=1"><img src="img/edit.png" /></a>
              </td>
              <td>
                '.($motd['Enabled'] ? '<img src="img/up.gif">' : '<img src="img/down.gif">').'
              </td>
              <td>
                '.bb2html($motd['Message']).'
              </td>
            </tr>';
  }
  $output .= '
          </table>
          <br />
        </center>';
  makebutton(lang('motd', 'add_motd'), 'motd.php?action=add_motd&error=4&redirect=1" type="def', 180);
  $output .= '
        <br />
        <br />';
}


//#############################################################################
// ADD MOTD
//#############################################################################
function add_motd()
{
  global $output, $action_permission, $sqlm;

  valid_login($action_permission['insert']);

  if (isset($_GET['redirect']))
    $redirect = $sqlm->quote_smart($_GET['redirect']);

  $output .= '
          <center>
            <form action="motd.php" method="get" name="form">
              <input type="hidden" name="action" value="do_add_motd" />
              <input type="hidden" name="redirect" value="'.$redirect.'" />
              <table class="top_hidden">
                <tr>
                  <td colspan="3">';
                    bbcode_add_editor();
  $output .= '
                  </td>
                </tr>
                <tr>
                  <td>'.lang('motd', 'priority').': 
                    <select name="priority">
                      <option value="0">'.lang('motd', 'veryhigh').'</option>
                      <option value="1">'.lang('motd', 'high').'</option>
                      <option value="2">'.lang('motd', 'med').'</option>
                      <option value="3">'.lang('motd', 'low').'</option>
                      <option value="4">'.lang('motd', 'verylow').'</option>
                    </select>
                  </td>
                  <td>
                    <input type="checkbox" name="enabled" checked="checked" />
                    '.lang('motd', 'enabled').'
                  </td>
                </tr>
                <tr>
                  <td colspan="3">
                    <textarea id="msg" name="msg" rows="26" cols="97"></textarea>
                  </td>
                </tr>
                <tr>
                  <td>'.lang('motd', 'post_rules').'</td>
                  <td>';
  makebutton(lang('motd', 'post_motd'), 'javascript:do_submit()" type="wrn', 230);
  $output .= '
                  </td>
                  <td>';
  makebutton(lang('global', 'back'), 'javascript:window.history.back()" type="def', 130);
  $output .= '
                  </td>
                </tr>
              </table>
            </form>
            <br />
          </center>';

}


//#############################################################################
// EDIT MOTD
//#############################################################################
function edit_motd()
{
  global $output, $action_permission, $sqlm;

  valid_login($action_permission['update']);

  if(empty($_GET['id'])) redirect('motd.php?error=1');
  $id = $sqlm->quote_smart($_GET['id']);
  if(is_numeric($id)); else redirect('motd.php?error=1');

  $msg = $sqlm->result($sqlm->query('SELECT message FROM motd WHERE id = '.$id.''), 0);
  $priority = $sqlm->result($sqlm->query('SELECT priority FROM motd WHERE id = '.$id.''), 0);
  $enabled = $sqlm->result($sqlm->query('SELECT enabled FROM motd WHERE id = '.$id.''), 0);

  if (isset($_GET['redirect']))
    $redirect = $sqlm->quote_smart($_GET['redirect']);

  $output .= '
          <center>
            <form action="motd.php" method="get" name="form">
              <input type="hidden" name="id" value="'.$id.'" />
              <input type="hidden" name="action" value="do_edit_motd" />
              <input type="hidden" name="redirect" value="'.$redirect.'" />
              <table class="top_hidden">
                <tr>
                  <td colspan="3">';
  unset($id);
                    bbcode_add_editor();
  $output .= '
                  </td>
                </tr>
                <tr>
                  <td>'.lang('motd', 'priority').': 
                    <select name="priority">
                      <option value="0" '.($priority == 0 ? 'selected="selected"' : '').'>'.lang('motd', 'veryhigh').'</option>
                      <option value="1" '.($priority == 1 ? 'selected="selected"' : '').'>'.lang('motd', 'high').'</option>
                      <option value="2" '.($priority == 2 ? 'selected="selected"' : '').'>'.lang('motd', 'med').'</option>
                      <option value="3" '.($priority == 3 ? 'selected="selected"' : '').'>'.lang('motd', 'low').'</option>
                      <option value="4" '.($priority == 4 ? 'selected="selected"' : '').'>'.lang('motd', 'verylow').'</option>
                    </select>
                  </td>
                  <td>
                    <input type="checkbox" name="enabled" '.($enabled ? 'checked="checked"' : '').' />
                    '.lang('motd', 'enabled').'
                  </td>
                </tr>
                <tr>
                  <td colspan="3">
                    <textarea id="msg" name="msg" rows="26" cols="97">'.$msg.'</textarea>
                  </td>
                </tr>
                <tr>
                  <td>'.lang('motd', 'post_rules').'</td>
                  <td>';
  unset($msg);
  makebutton(lang('motd', 'post_motd'), 'javascript:do_submit()" type="wrn', 230);
  $output .= '
                  </td>
                  <td>';
  makebutton(lang('global', 'back'), 'javascript:window.history.back()" type="def', 130);
  $output .= '
                  </td>
                </tr>
              </table>
            </form>
            <br />
          </center>';

}


//#####################################################################################################
// DO ADD MOTD
//#####################################################################################################
function do_add_motd()
{
  global $action_permission, $user_name, $sqlm;

  valid_login($action_permission['insert']);

  if (empty($_GET['msg']))
    redirect('motd.php?error=1');

  if (empty($_GET['priority']))
    $priority = 0;
  else
    $priority = $sqlm->quote_smart($_GET['priority']);

  if ($_GET['enabled'] == 'on')
    $enabled = 1;
  else
    $enabled = 0;

  $msg = $sqlm->quote_smart($_GET['msg']);
  $oldmsg = $sqlm->quote_smart($_GET['oldmsg']);
  if (4096 < strlen($msg))
    redirect('motd.php?error=2');

  $name = $sqlm->result($sqlm->query("SELECT ScreenName FROM config_accounts WHERE Login='".$user_name."'"), 0);
  if ($name == "")
    $name = $user_name;

  $by = 'Posted by: '.$name.' ('.date('m/d/Y H:i:s').')';
  $sqlm->query("INSERT INTO motd (Message, Priority, Enabled, `By`) VALUES ('".$msg."', '".$priority."', '".$enabled."', '".$by."')");

  unset($by);
  unset($msg);
  if ($_GET['redirect'] == 1)
    redirect('motd.php');
  else
    redirect('index.php');

}


//#####################################################################################################
// DO EDIT MOTD
//#####################################################################################################
function do_edit_motd()
{
  global $action_permission, $user_name, $sqlm;

  valid_login($action_permission['update']);

  if (empty($_GET['msg']) || empty($_GET['id']))
    redirect('motd.php?error=1');

  if (empty($_GET['priority']))
    $priority = 0;
  else
    $priority = $sqlm->quote_smart($_GET['priority']);

  if ($_GET['enabled'] == 'on')
    $enabled = 1;
  else
    $enabled = 0;

  $id = $sqlm->quote_smart($_GET['id']);
  if(is_numeric($id)); else redirect('motd.php?error=1');

  $msg = $sqlm->quote_smart($_GET['msg']);
  $oldmsg = $sqlm->quote_smart($sqlm->result($sqlm->query("SELECT Message FROM motd WHERE ID = '".$id."'"), 0));
  if (4096 < strlen($msg))
    redirect('motd.php?error=2');

  $name = $sqlm->result($sqlm->query("SELECT ScreenName FROM config_accounts WHERE Login='".$user_name."'"), 0);
  if ($name == "")
    $name = $user_name;

  if ($oldmsg <> $msg)
  {
    $by = $sqlm->result($sqlm->query("SELECT `By` FROM motd WHERE ID = '".$id."'"), 0);
    $by = split('<br />', $by, 2);
    $by = $by[0].'<br />'.'Edited by: '.$name.' ('.date('m/d/Y H:i:s').')';
    $sqlm->query("UPDATE motd SET Message = '".$msg."', Priority = '".$priority."', Enabled = '".$enabled."', `By` = '".$by."' WHERE ID = '".$id."'");
  }
  else
  {
    $sqlm->query("UPDATE motd SET Message = '".$msg."', Priority = '".$priority."', Enabled = '".$enabled."' WHERE ID = '".$id."'");
  }

  unset($by);
  unset($msg);
  unset($id);
  if ($_GET['redirect'] == 1)
    redirect('motd.php');
  else
    redirect('index.php');

}


//#####################################################################################################
// DELETE MOTD
//#####################################################################################################
function delete_motd()
{
  global $action_permission, $sqlm;

  valid_login($action_permission['delete']);

  if (empty($_GET['id'])) redirect('index.php');
  $id = $sqlm->quote_smart($_GET['id']);
  if(is_numeric($id)); else redirect('motd.php?error=1');

  $sqlm->query('DELETE FROM motd WHERE id ='.$id.'');
  unset($id);
  if ($_GET['redirect'] == 1)
    redirect('motd.php');
  else
    redirect('index.php');

}


//########################################################################################################################
// MAIN
//########################################################################################################################
$err = (isset($_GET['error'])) ? $_GET['error'] : NULL;

//$lang_motd = lang_motd();

$output .= '
      <div class="bubble">
          <div class="top">';

if (1 == $err)
  $output .= '
            <h1><font class="error">'.$lang_global['empty_fields'].'</font></h1>';
elseif (2 == $err)
  $output .= '
            <h1><font class="error">'.$lang_motd['err_max_len'].'</font></h1>';
elseif (3 == $err)
  $output .= '
            <h1>'.$lang_motd['edit_motd'].'</h1>';
elseif (4 == $err)
  $output .= '
            <h1>'.$lang_motd['add_motd'].'</h1>';
else
  $output .= '
            <h1>'.$lang_motd['browse_motd'].'</h1>';

unset($err);

$output .= '
          </div>';

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

if ('delete_motd' == $action)
  delete_motd();
elseif ('add_motd' == $action)
  add_motd();
elseif ('do_add_motd' == $action)
  do_add_motd();
elseif ('edit_motd' == $action)
  edit_motd();
elseif ('do_edit_motd' == $action)
  do_edit_motd();
else
  browse_motd();

unset($action);
unset($action_permission);
//unset($lang_motd);

require_once 'footer.php';


?>
