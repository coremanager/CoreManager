<?php
/*
    CoreManager, PHP Front End for ArcEmu, MaNGOS, and TrinityCore
    Copyright (C) 2010-2011  CoreManager Project

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


// NOTE: THIS IS NOT A NORMAL LIB FILE.
//
// Do not attempt to pre-load it with require/include.
// It is intended to be used as the src for an <img> tag.
//
// Background: <img src="libs/banner_lib.php?action=banner&r=[red]&g=[green]&b=[blue]" />
// Border: <img src="libs/banner_lib.php?action=border&f=[border #]&r=[red]&g=[green]&b=[blue]" />
// Emblem: <img src="libs/banner_lib.php?action=emblem&f=[emblem #]&r=[red]&g=[green]&b=[blue]&s=[scale]" />

//########################################################################################################################
// GENERATE BACKGROUND
//########################################################################################################################
function banner()
{
  $f = "../img/arena_emblems/PVP-Banner-".$_GET["f"].".png";
  $img = @imagecreatefrompng($f);

  $color_r = $_GET["r"];
  $color_g = $_GET["g"];
  $color_b = $_GET["b"];

  $width = imagesx($img);
  $height = imagesy($img);

  $img_temp = imagecreatetruecolor($width, $height);
  imagealphablending($img_temp, false);
  imagesavealpha($img_temp, true);

  $trans = imagecolorallocatealpha($img_temp, 0, 0, 0, 127);

  for ( $y=0; $y<$height; $y++ )
  {
    for ( $x=0; $x<$width; $x++ )
    {
      $rgb = imagecolorat($img, $x, $y);

      $a = ($rgb >> 24) & 0xFF;
      $r = ($rgb >> 16) & 0xFF;
      $g = ($rgb >> 8) & 0xFF;
      $b = $rgb & 0xFF;

      if ( $a < 127 )
      {
        // banners are stored using grey scale
        $new_r = ( ( $r != 0 ) ? floor($color_r * ($r/255)) : 0 );
        $new_g = ( ( $g != 0 ) ? floor($color_g * ($g/255)) : 0 );
        $new_b = ( ( $b != 0 ) ? floor($color_b * ($b/255)) : 0 );

        $new_color = imagecolorallocatealpha($img_temp, $new_r, $new_g, $new_b, $a);
        imagesetpixel($img_temp, $x, $y, $new_color);
      }
      else
      {
        imagesetpixel($img_temp, $x, $y, $trans);
      }
    }
  }

  // output the image data
  imagepng($img_temp);

  imagedestroy($img);
  imagedestroy($img_temp);
}

//########################################################################################################################
// GENERATE BORDER
//########################################################################################################################
function border()
{
  $f = "../img/arena_emblems/PVP-Banner-".$_GET["f2"]."-Border-".$_GET["f"].".png";
  $img = @imagecreatefrompng($f);

  $color_r = $_GET["r"];
  $color_g = $_GET["g"];
  $color_b = $_GET["b"];

  $width = imagesx($img);
  $height = imagesy($img);

  $img_temp = imagecreatetruecolor($width, $height);
  imagealphablending($img_temp, false);
  imagesavealpha($img_temp, true);

  $trans = imagecolorallocatealpha($img_temp, 0, 0, 0, 127);

  for ( $y=0; $y<$height; $y++ )
  {
    for ( $x=0; $x<$width; $x++ )
    {
      $rgb = imagecolorat($img, $x, $y);

      $a = ($rgb >> 24) & 0xFF;
      $r = ($rgb >> 16) & 0xFF;
      $g = ($rgb >> 8) & 0xFF;
      $b = $rgb & 0xFF;

      if ( $a < 127 )
      {
        // borders are stored using gray scale
        $new_r = ( ( $r != 0 ) ? floor($color_r * ($r/255)) : 0 );
        $new_g = ( ( $g != 0 ) ? floor($color_g * ($g/255)) : 0 );
        $new_b = ( ( $b != 0 ) ? floor($color_b * ($b/255)) : 0 );

        $new_color = imagecolorallocatealpha($img_temp, $new_r, $new_g, $new_b, $a);
        imagesetpixel($img_temp, $x, $y, $new_color);
      }
      else
      {
        imagesetpixel($img_temp, $x, $y, $trans);
      }
    }
  }

  // output the image data
  imagepng($img_temp);

  imagedestroy($img);
  imagedestroy($img_temp);
}

//########################################################################################################################
// GENERATE EMBLEM
//########################################################################################################################
function emblem()
{
  $f = "../img/arena_emblems/PVP-Banner-Emblem-".$_GET["f"].".png";
  $img = @imagecreatefrompng($f);

  $color_r = $_GET["r"];
  $color_g = $_GET["g"];
  $color_b = $_GET["b"];

  $scale = $_GET["s"];

  $width = imagesx($img);
  $height = imagesy($img);

  $d_width = floor($width * $scale);
  $d_height = floor($height * $scale);

  $img_temp = imagecreatetruecolor($width, $height);
  imagealphablending($img_temp, false);
  imagesavealpha($img_temp, true);

  $img_out = imagecreatetruecolor($d_width, $d_height);
  imagealphablending($img_out, false);
  imagesavealpha($img_out, true);

  for ( $y=0; $y<$height; $y++ )
  {
    for ( $x=0; $x<$width; $x++ )
    {
      $rgb = imagecolorat($img, $x, $y);

      // emblems are stored with alpha variance
      $a = ($rgb >> 24) & 0xFF;

      $new_color = imagecolorallocatealpha($img_temp, $color_r, $color_g, $color_b, $a);
      imagesetpixel($img_temp, $x, $y, $new_color);
    }
  }

  // scale the emblem
  imagecopyresampled($img_out, $img_temp, 0, 0, 0, 0, $d_width, $d_height, $width, $height);

  // output the image data
  imagepng($img_out);

  imagedestroy($img);
  imagedestroy($img_out);
}


//########################################################################################################################
// MAIN
//########################################################################################################################
$action = $_GET["action"];

switch ( $action )
{
  case "banner":
  {
    banner();
    break;
  }
  case "border":
  {
    border();
    break;
  }
  case "emblem":
  {
    emblem();
    break;
  }
}

?>