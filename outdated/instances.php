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


// page header, and any additional required libraries
require_once 'header.php';
require_once 'libs/map_zone_lib.php';
// minimum permission to view page
valid_login($action_permission['view']);

//#############################################################################
// INSTANCES
//#############################################################################
function instances()
{
  global $output, $lang_instances, $arcm_db,
    $realm_id, $world_db, $arcn_db,
    $itemperpage, $sqlw, $sqlm, $sqld;

  //-------------------SQL Injection Prevention--------------------------------
  // this page has multipage support and field ordering, so we need these
  $start = (isset($_GET['start'])) ? $sqlw->quote_smart($_GET['start']) : 0;
  if (is_numeric($start)); else $start=0;

  $order_by = (isset($_GET['order_by'])) ? $sqlw->quote_smart($_GET['order_by']) : 'minlevel';
  if (preg_match('/^[_[:lower:]]{1,11}$/', $order_by)); else $order_by='minlevel';

  $dir = (isset($_GET['dir'])) ? $sqlw->quote_smart($_GET['dir']) : 1;
  if (preg_match('/^[01]{1}$/', $dir)); else $dir=1;

  $order_dir = ($dir) ? 'ASC' : 'DESC';
  $dir = ($dir) ? 0 : 1;

  // for multipage support
  $all_record = $sqlw->result($sqlw->query('SELECT count(*) FROM worldmap_info'), 0);

  // main data that we need for this page, instances
    $result = $sqlw->query('SELECT entry, minlevel, minlevel_heroic, maxplayers
      FROM worldmap_info ORDER BY '.$order_by.' '.$order_dir.' LIMIT '.$start.', '.$itemperpage.';');

  //---------------Page Specific Data Starts Here--------------------------
  // we start with a lead of 10 spaces,
  //  because last line of header is an opening tag with 8 spaces
  //  keep html indent in sync, so debuging from browser source would be easy to read
  $output .= '
          <!-- start of instances.php -->
          <center>
            <table class="top_hidden">
              <tr>
                <td width="25%" align="right">';

  // multi page links
  $output .=
                  lang('instances', 'total').' : '.$all_record.'<br /><br />'.
                  generate_pagination('instances.php?order_by='.$order_by.'&amp;dir='.(($dir) ? 0 : 1), $all_record, $itemperpage, $start);

  // column headers, with links for sorting
  $output .= '
                </td>
              </tr>
            </table>
            <table class="lined">
              <tr>
                <th width="40%"><a href="instances.php?order_by=entry&amp;start='.$start.'&amp;dir='.$dir.'"'.($order_by==='entry' ? ' class="'.$order_dir.'"' : '').'>'.lang('instances', 'map').'</a></th>
                <th width="15%"><a href="instances.php?order_by=minlevel&amp;start='.$start.'&amp;dir='.$dir.'"'.($order_by==='minlevel' ? ' class="'.$order_dir.'"' : '').'>'.lang('instances', 'level_min').'</a></th>
                <th width="15%"><a href="instances.php?order_by=minlevel_heroic&amp;start='.$start.'&amp;dir='.$dir.'"'.($order_by==='minlevel_heroic' ? ' class="'.$order_dir.'"' : '').'>'.lang('instances', 'level_max').'</a></th>
                <th width="15%"><a href="instances.php?order_by=maxplayers&amp;start='.$start.'&amp;dir='.$dir.'"'.($order_by==='maxplayers' ? ' class="'.$order_dir.'"' : '').'>'.lang('instances', 'max_players').'</a></th>
              </tr>';

  while ($instances = $sqlw->fetch_assoc($result))
  {
    $output .= '
              <tr valign="top">
                <td>'.get_map_name($instances['entry'], $sqld).' ('.$instances['entry'].')</td>
                <td>'.$instances['minlevel'].'</td>
                <td>'.$instances['minlevel_heroic'].'</td>
                <td>'.$instances['maxplayers'].'</td>
              </tr>';
  }
  unset($reset);
  unset($hours);
  unset($days);
  unset($instances);
  unset($result);

  $output .= '
              <tr>
                <td colspan="5" class="hidden" align="right" width="25%">';
  // multi page links
  $output .= generate_pagination('instances.php?order_by='.$order_by.'&amp;dir='.(($dir) ? 0 : 1), $all_record, $itemperpage, $start);
  unset($start);
  $output .= '
                </td>
              </tr>
              <tr>
                <td colspan="5" class="hidden" align="right">'.lang('instances', 'total').' : '.$all_record.'</td>
              </tr>
            </table>
          </center>
          <!-- end of instances.php -->';

}


//#############################################################################
// MAIN
//#############################################################################

// error variable reserved for future use
//$err = (isset($_GET['error'])) ? $_GET['error'] : NULL;

//unset($err);

//$lang_instances = lang_instances();

$output .= '
      <div class="bubble">
          <div class="top">
            <h1>'.lang('instances', 'instances').'</h1>
          </div>';

// action variable reserved for future use
//$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

instances();

//unset($action);
unset($action_permission);
//unset($lang_instances);

require_once 'footer.php';


?>
