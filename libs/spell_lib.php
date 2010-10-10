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
//get spell name by its id
// this_is_junk: This isn't used.

function spell_get_name($id)
{
  global $sql;

  $spell_name = $sql["dbc"]->fetch_assoc($sql["dbc"]->query('SELECT spellname_loc0 FROM dbc_spell WHERE spellID='.$id.' LIMIT 1'));
  return $spell_name["spellname_loc0"];
}


//#############################################################################
//get spell icon - if icon not exists in item_icons folder D/L it from web.

function spell_get_icon($auraid)
{
  global $proxy_cfg, $get_icons_from_web, $item_icons, $sql;

  $result = $sql["dbc"]->query('SELECT spellIconId FROM spell WHERE ID = '.$auraid.' LIMIT 1');

  if ($result)
    $displayid = $sql["dbc"]->result($result, 0);
  else
    $displayid = 0;

  if ($displayid)
  {
    $result = $sql["dbc"]->query('SELECT Name FROM spellicon WHERE id = '.$displayid.' LIMIT 1');

    if($result)
    {
      $aura = $sql["dbc"]->result($result, 0);
      $aura = explode("\\", $aura);
      $aura = $aura[count($aura) - 1];

      if ($aura)
      {
        //if (file_exists(''.$item_icons.'/'.$aura.'.png'))
        //{
          return ''.$item_icons.'/'.$aura.'.png';
        //}
        //else
          //$aura = '';
        
        //if (file_exists(''.$item_icons.'/'.$aura.'.png'))
        //{
        //  if (filesize(''.$item_icons.'/'.$aura.'.png') > 349)
        //  {
        //    return ''.$item_icons.'/'.$aura.'.png';
        //  }
        //  else
        //  {
        //    $sql["mgr"]->query('DELETE FROM dbc_spellicon WHERE id = '.$displayid.'');
        //    if (file_exists(''.$item_icons.'/'.$aura.'.png'))
        //      unlink(''.$item_icons.'/'.$aura.'.png');
        //    $aura = '';
        //  }
        //}
        //else
        //  $aura = '';*/
      }
      else
        $aura = '';
    }
    else
      $aura = '';
  }
  else
    $aura = '';
}


?>
