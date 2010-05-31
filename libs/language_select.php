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


function generate_language_selectbox()
{
  global $locales_search_option;

  include_once("get_lib.php");
  $selectedlanguage = get_lang_id();

  // generate a html option list with available locales_entries
  // taken from $locales_search_option in your scripts/config.php

  // if user language is supported select this one, else default language
  $select_option = ( $locales_search_option & pow(2,$selectedlanguage) == 0  ) ? 0 : $selectedlanguage;

  $searchbox = "
    <select name=\"language\">
      <option value=\"0\">".lang('global', 'language_0')."</option>";
  for ($i=1; $i<9;$i++)
  {
    if ( ($locales_search_option & pow(2,$i-1)) != 0 )
    {
      $searchbox .= "
      <option value=\"{$i}\"";
      if ($select_option == $i)
        $searchbox .= "selected=\"selected\"";
      $searchbox .= ">".lang('global', 'language_'.$i)."</option>";
    }
  }
  $searchbox .= "
    </select>";
  return $searchbox;
}


?>
