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

function get_uptime($statsfile)
{
  $file_obj = fopen($statsfile, "r");

  for ($i = 0; $i < 4; $i++)
    $temp = fgets($file_obj);

  $out["platform"] = explode(' ', fgets($file_obj));
  $out["platform"][4] = str_replace("<platform>","",$out["platform"][4]);
  $out["uptime"] = fgets($file_obj);
  $out["uptime"] = str_replace("<uptime>","",$out["uptime"]);
  $out["uptime"] = str_replace("</uptime>","",$out["uptime"]);
  for ($i = 0; $i < 1; $i++)
    $temp = fgets($file_obj);
  $out["cpu"] = fgets($file_obj);
  $out["cpu"] = str_replace("<cpu>","",$out["cpu"]);
  $out["cpu"] = str_replace("</cpu>","",$out["cpu"]);
  for ($i = 0; $i < 1; $i++)
    $temp = fgets($file_obj);
  $out["ram"] = fgets($file_obj);
  $out["ram"] = str_replace("<ram>","",$out["ram"]);
  $out["ram"] = str_replace("</ram>","",$out["ram"]);
  $out["avglat"] = fgets($file_obj);
  $out["avglat"] = str_replace("<avglat>","",$out["avglat"]);
  $out["avglat"] = str_replace("</avglat>","",$out["avglat"]);
  $out["threads"] = fgets($file_obj);
  $out["threads"] = str_replace("<threads>","",$out["threads"]);
  $out["threads"] = str_replace("</threads>","",$out["threads"]);
  for ($i = 0; $i < 7; $i++)
    $temp = fgets($file_obj);

  $out["peak"] = fgets($file_obj);
  $out["peak"] = str_replace("<peakcount>","",$out["peak"]);
  $out["peak"] = str_replace("</peakcount>","",$out["peak"]);

  if (file_exists($statsfile))
  {
    $xml = simplexml_load_file($statsfile);

    $plrs = array();
    foreach($xml->children() as $child)
    {
      if ($child->getName() == "sessions")
      {
        foreach($child->children() as $sess_child)
        {
          if ($sess_child->getName() == "plr")
          {
            $plr = array();
            foreach($sess_child->children() as $plr_child)
            {
              switch ($plr_child->getName())
              {
                case "name":
                {
                  array_push($plr, $plr_child."");
                  break;
                }
                case "latency":
                {
                  array_push($plr, $plr_child."");
                  break;
                }
              }
            }
            if (count($plr) <> 0)
              array_push($plrs, $plr);
          } 
        }
      }
    }
  }

  $out["plrs_lat"] = $plrs;

  if (file_exists($statsfile))
  {
    $xml = simplexml_load_file($statsfile);

    $plrs = array();
    foreach($xml->children() as $child)
    {
      if ($child->getName() == "sessions")
      {
        foreach($child->children() as $sess_child)
        {
          if ($sess_child->getName() == "plr")
          {
            $plr = array();
            foreach($sess_child->children() as $plr_child)
            {
              switch ($plr_child->getName())
              {
                case "name":
                {
                  array_push($plr, $plr_child."");
                  break;
                }
                case "areaid":
                {
                  array_push($plr, $plr_child."");
                  break;
                }
              }
            }
            if (count($plr) <> 0)
              array_push($plrs, $plr);
          } 
        }
      }
    }
  }

  $out["plrs_area"] = $plrs;

  return $out;
}
?>
