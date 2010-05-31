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
// BROWSE SPELLS
//#############################################################################
function browse_spells()
{
  global $output, $world_db, $realm_id, $action_permission, $user_lvl, $itemperpage, $sqlw;

  valid_login($action_permission['view']);

  //==========================$_GET and SECURE=================================
  $start = (isset($_GET['start'])) ? $sqlw->quote_smart($_GET['start']) : 0;
  if (is_numeric($start)); else $start=0;

  $order_by = (isset($_GET['order_by'])) ? $sqlw->quote_smart($_GET['order_by']) : 'spellid';
  if (preg_match('/^[_[:lower:]]{1,12}$/', $order_by)); else $order_by = 'spellid';

  $dir = (isset($_GET['dir'])) ? $sqlw->quote_smart($_GET['dir']) : 1;
  if (preg_match('/^[01]{1}$/', $dir)); else $dir = 1;

  $order_dir = ($dir) ? 'ASC' : 'DESC';
  $dir = ($dir) ? 0 : 1;
  //==========================$_GET and SECURE end=============================

  //==========================Browse/Search CHECK==============================
  $search_by = '';
  $search_value = '';
  if(isset($_GET['search_value']) && isset($_GET['search_by']))
  {
    $search_value = $sqlw->quote_smart($_GET['search_value']);
    $search_by = $sqlw->quote_smart($_GET['search_by']);
    $search_menu = array('spellid', 'replacement_spellid');
    if (in_array($search_by, $search_menu)); else $search_by = 'spellid';

    $query_1 = $sqlw->query('SELECT count(*) FROM spell_disable WHERE '.$search_by.' LIKE \'%'.$search_value.'%\'');
    $result = $sqlw->query('SELECT spellid, replacement_spellid FROM spell_disable
      WHERE '.$search_by.' LIKE \'%'.$search_value.'%\' ORDER BY '.$order_by.' '.$order_dir.' LIMIT '.$start.', '.$itemperpage.'');
  }
  else
  {
    $query_1 = $sqlw->query('SELECT count(*) FROM spell_disable');
    $result = $sqlw->query('SELECT spellid, replacement_spellid FROM spell_disable
      ORDER BY '.$order_by.' '.$order_dir.' LIMIT '.$start.', '.$itemperpage.'');
  }
  //get total number of items
  $all_record = $sqlw->result($query_1,0);
  unset($query_1);

  //==========================top tage navigaion starts here========================
  $output .= '
          <script type="text/javascript" src="libs/js/check.js"></script>
          <center>
            <table class="top_hidden">
              <tr>
                <td>';
  if ($user_lvl >= $action_permission['insert'])
                  makebutton(lang('spelld', 'add_spell'), 'spelld.php?action=add_new" type="wrn', 130);
                  makebutton(lang('global', 'back'), 'javascript:window.history.back()', 130);
  ($search_by && $search_value) ? makebutton(lang('spelld', 'spell_list'), 'spelld.php', 130) : $output .= '';
  $output .= '
                </td>
                <td align="right" width="25%">';
  $output .= generate_pagination('spelld.php?order_by='.$order_by.'&amp;dir='.(($dir) ? 0 : 1).( $search_value && $search_by ? '&amp;search_by='.$search_by.'&amp;search_value='.$search_value.'' : '' ), $all_record, $itemperpage, $start);
  $output .= '
                </td>
              </tr>
              <tr align="left">
                <td rowspan="2">
                  <table class="hidden">
                    <tr>
                      <td>
                        <form action="spelld.php" method="get" name="form">
                          <input type="hidden" name="error" value="3" />
                          <input type="text" size="24\" maxlength="64" name="search_value" value="'.$search_value.'" />
                          <select name="search_by">
                            <option value="spellid"'.($search_by == 'spellid' ? ' selected="selected"' : '').'>'.lang('spelld', 'by_id').'</option>
                            <option value="replacement_spellid"'.($search_by == 'replacement_spellid' ? ' selected="selected"' : '').'>'.lang('spelld', 'by_disable').'</option>
                          </select>
                        </form>
                      </td>
                      <td>';
                        makebutton(lang('global', 'search'), 'javascript:do_submit()', 80);
  $output .= '
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>';
  //==========================top tage navigaion ENDS here ========================

  $output .= '
            <form method="get" action="spelld.php" name="form1">
              <input type="hidden" name="action" value="del_spell" />
              <input type="hidden" name="start" value="'.$start.'" />
              <table class="lined">
                <tr>';
  if($user_lvl >= $action_permission['delete'])
    $output .= '
                  <th width="1%"><input name="allbox" type="checkbox" value="Check All" onclick="CheckAll(document.form1);" /></th>';
  else
    $output .= '
                  <th width="1%"></th>';
  $output .= '
                  <th width="10%"><a href="spelld.php?order_by=spellid&amp;start='.$start.( $search_value && $search_by ? '&amp;error=3&amp;search_by='.$search_by.'&amp;search_value='.$search_value.'' : '' ).'&amp;dir='.$dir.'"'.($order_by==='spellid' ? ' class="'.$order_dir.'"' : '').'>'.lang('spelld', 'entry').'</a></th>
                  <th width="10%"><a href="spelld.php?order_by=replacement_spellid&amp;start='.$start.( $search_value && $search_by ? '&amp;error=3&amp;search_by='.$search_by.'&amp;search_value='.$search_value.'' : '' ).'&amp;dir='.$dir.'"'.($order_by==='replacement_spellid' ? ' class="'.$order_dir.'"' : '').'>'.lang('spelld', 'disable_mask').'</a></th>
                </tr>
                <tr>';

  while($spelld = $sqlw->fetch_assoc($result))
  {
    if($user_lvl >= $action_permission['delete'])
      $output .= '
                  <td><input type="checkbox" name="check[]" value="'.$spelld['spellid'].'" onclick="CheckCheckAll(document.form1);" /></td>';
    else
      $output .= '
                  <td></td>';
    $output .= '
                  <td>'.$spelld['spellid'].'</td>
                  <td>'.$spelld['replacement_spellid'].'</td>
                </tr>
                <tr>';
  }
  $output .= '
                  <td colspan="4" class="hidden" align="right" width="25%">';
  $output .= generate_pagination('spelld.php?order_by='.$order_by.'&amp;dir='.(($dir) ? 0 : 1).( $search_value && $search_by ? '&amp;search_by='.$search_by.'&amp;search_value='.$search_value.'' : '' ), $all_record, $itemperpage, $start);
  $output .= '
                  </td>
                </tr>
                <tr>
                  <td colspan="2" class="hidden" align="left">';
  if($user_lvl >= $action_permission['delete'])
                  makebutton(lang('spelld', 'del_selected_spells'), 'javascript:do_submit(\'form1\',0)" type="wrn', 180);
  $output .= '
                  </td>
                  <td colspan="2" class="hidden" align="right">'.lang('spelld', 'tot_spell').' : '.$all_record.'</td>
                </tr>
              </table>
            </form>
            <br />
          </center>';

}


//#####################################################################################################
//  ADD NEW SPELL
//#######################################################################################################
function add_new()
{
  global $output, $action_permission;

  valid_login($action_permission['insert']);

  $output .= '
          <center>
            <fieldset id="spelld_add_field">
              <legend>'.lang('spelld', 'add_new_spell').'</legend>
              <form method="get" action="spelld.php" name="form">
                <input type="hidden" name="action" value="doadd_new" />
                <table class="flat">
                  <tr>
                    <td>'.lang('spelld', 'entry2').'</td>
                    <td><input type="text" name="spellid" size="24" maxlength="11" value="" /></td>
                  </tr>
                  <tr>
                    <td>'.lang('spelld', 'comment2').'</td>
                    <td><input type="text" name="replacement_spellid" size="24" maxlength="11" value="" /></td>
                  </tr>
                  <tr>
                    <td>';
                      makebutton(lang('spelld', 'add_spell'), 'javascript:do_submit()" type="wrn', 130);
  $output .= '
                    </td>
                    <td>';
                      makebutton(lang('global', 'back'), 'javascript:window.history.back()" type="def', 130);
  $output .= '
                    </td>
                  </tr>
                </table>
              </form>
            </fieldset>
            <br />
          </center>';

}

//#########################################################################################################
// DO ADD NEW SPELL
//#########################################################################################################
function doadd_new()
{
  global $world_db, $realm_id, $action_permission, $sqlw;

  valid_login($action_permission['insert']);

  if ( empty($_GET['spellid']) && empty($_GET['replacement_spellid']) )
    redirect('spelld.php?error=1');

  $spellid = $sqlw->quote_smart($_GET['spellid']);
  if (is_numeric($spellid));
  else
    redirect('spelld.php?error=6');
  $replacement_spellid = $sqlw->quote_smart($_GET['replacement_spellid']);
  if (is_numeric($replacement_spellid));
  else
    redirect('spelld.php?error=6');

  $sqlw->query("INSERT INTO spell_disable (spellid, replacement_spellid) VALUES ('".$spellid."', '".$replacement_spellid."')");
  if ($sqlw->affected_rows())
    redirect('spelld.php?error=8');
  else
    redirect('spelld.php?error=7');

}


//#####################################################################################################
//  DELETE SPELL
//#####################################################################################################
function del_spell()
{
  global $world_db, $realm_id, $action_permission, $sqlw;

  valid_login($action_permission['delete']);

  if(isset($_GET['check'])); else redirect("spelld.php?error=1");

  $check = $sqlw->quote_smart($_GET['check']);

  $n_check=count($check);
  for ($i=0; $i<$n_check; ++$i)
    if ($check[$i] == '' );
    else
      $sqlw->query('DELETE FROM spell_disable WHERE spellid = '.$check[$i].'');
  unset($n_check);
  unset($check);

  if ($sqlw->affected_rows())
    redirect('spelld.php?error=4');
  else
    redirect('spelld.php?error=5');

}


//#############################################################################
// MAIN
//#############################################################################
$err = (isset($_GET['error'])) ? $_GET['error'] : NULL;

$output .= '
      <div class="bubble">
          <div class="top">';

//$lang_spelld = lang_spelld();

if (1 == $err)
  $output .= '
            <h1><font class="error">'.lang('global', 'empty_fields').'</font></h1>';
elseif (2 == $err)
  $output .= '
            <h1><font class="error">'.lang('global', 'err_no_search_passed').'</font></h1>';
elseif (3 == $err)
  $output .= '
            <h1>'.lang('spelld', 'search_results').'</h1>';
elseif (4 == $err)
  $output .= '
            <h1><font class="error">'.lang('spelld', 'spell_deleted').'</font></h1>';
elseif (5 == $err)
  $output .= '
            <h1><font class="error">'.lang('spelld', 'spell_not_deleted').'</font></h1>';
elseif (6 == $err)
  $output .= '
            <h1><font class="error">'.lang('spelld', 'wrong_fields').'</font></h1>';
elseif (7 == $err)
  $output .= '
            <h1><font class="error">'.lang('spelld', 'err_add_entry').'</font></h1>';
elseif (8 == $err)
  $output .= '
            <h1><font class="error">'.lang('spelld', 'spell_added').'</font></h1>';
else
  $output .= '
            <h1>'.lang('spelld', 'spells').'</h1>';

unset($err);

$output .= '
          </div>';

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

if ('add_new' === $action)
  add_new();
elseif ('doadd_new' === $action)
  doadd_new();
elseif ('del_spell' === $action)
  del_spell();
else
  browse_spells();

unset($action);
unset($action_permission);
//unset($lang_spelld);

require_once 'footer.php';

?>
