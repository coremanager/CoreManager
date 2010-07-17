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


// because error needs a header but header.php requires databases,
// we make our own page header, and get any additional required libraries
session_start();

//---------------------Loading User Theme and Language Settings----------------
if (isset($_COOKIE['theme']))
{
  if (is_dir('themes/'.$_COOKIE['theme']))
    if (is_file('themes/'.$_COOKIE['theme'].'/'.$_COOKIE['theme'].'_1024.css'))
      $theme = $_COOKIE['theme'];
}
else
  $theme = "Sulfur";

if (isset($_COOKIE['lang']))
{
  $lang = $_COOKIE['lang'];
  if (file_exists('lang/'.$lang.'.php'))
    ;
  else
    $lang = 'english';
}
else
  $lang = 'english';

require_once 'libs/global_lib.php';
require_once 'lang/'.$lang.'.php';
require_once 'libs/lang_lib.php';

// sets encoding defined in config for language support
header('Content-Type: text/html; charset='.$site_encoding);
header('Expires: Tue, 01 Jan 2000 00:00:00 GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
$output .= '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>'.$title.'</title>
    <meta http-equiv="Content-Type" content="text/html; charset='.$site_encoding.'" />
    <meta http-equiv="Content-Type" content="text/javascript; charset='.$site_encoding.'" />
    <link rel="stylesheet" type="text/css" href="themes/'.$theme.'/'.$theme.'_1024.css" title="1024" />
    <link rel="stylesheet" type="text/css" href="themes/'.$theme.'/'.$theme.'_1280.css" title="1280" />
    <link rel="SHORTCUT ICON" href="img/favicon.ico" />
    <script type="text/javascript" charset="utf-8"></script>
    <script type="text/javascript" src="libs/js/general.js"></script>
    <script type="text/javascript" src="libs/js/layout.js"></script>
  </head>';

$output .= '
  <body onload="dynamicLayout();">
    <center>
      <table class="table_top">
        <tr>
          <td class="table_top_left" valign="top">
          </td>
          <td class="table_top_middle"></td>
          <td class="table_top_right"></td>
        </tr>
      </table>
      <div id="body_main">';
// end of header

// we get the error message which was passed to us
$err = (isset($_SESSION['pass_error'])) ? ($_SESSION['pass_error']) : 'Oopsy...';

// we start with a lead of 10 spaces,
//  because last line of header is an opening tag with 8 spaces
//  keep html indent in sync, so debuging from browser source would be easy to read
$output .= '
        <div class="bubble">
          <!-- start of error.php -->
          <center>
            <br />
            <table width="400" class="flat">
              <tr>
                <td align="center">
                  <h1>
                    <font class="error">
                      <img src="img/warn_red.gif" width="48" height="48" alt="error" />
                      <br />ERROR!
                    </font>
                  </h1>
                  <br />'.$err.'<br />
                </td>
              </tr>
            </table>
            <br />
            <table width="300" class="hidden">
              <tr>
                <td align="center">';
                  makebutton(lang('global', 'home'), 'index.php', 130);
                  makebutton(lang('global', 'back'), 'javascript:window.history.back()', 130);
unset($err);
$output .= '
                </td>
              </tr>
            </table>
            <br />
          </center>
          <!-- end of error.php -->';

require_once 'footer.php';


?>
