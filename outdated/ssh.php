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
// minimum permission to view page
valid_login($action_permission['delete']);

if ( test_port($server[$realm_id]['addr_wan'], $server[$realm_id]['term_port']) )
{
  // we start with a lead of 10 spaces,
  //  because last line of header is an opening tag with 8 spaces
  //  keep html indent in sync, so debuging from browser source would be easy to read
  $output .= '
          <!-- start of ssh.php -->
          <center>
            <br />
            <applet codebase="." archive="libs/js/ssh.jar"
              code="de.mud.jta.Applet" width="780" height="350">
              <param name="plugins" value="Status,Socket,'.$server[$realm_id]['term_type'].',Terminal" />
              <param name="Socket.host" value="'.$server[$realm_id]['addr_wan'].'" />
              <param name="Socket.port" value="'.$server[$realm_id]['term_port'].'" />
            </applet>
            <br />
            <br />
          </center>
          <!-- end of ssh.php -->';
}
else
{
  $lang_ssh = lang_ssh();
  $output .= '
          <!-- start of ssh.php -->
          <div class="top">
            <h1><font class="error">'.$lang_ssh['server_offline'].'</font></h1>
          </div>
          <center>
            '.$lang_ssh['config_server_properly'].'
          </center>
          <!-- end of ssh.php -->';
  unset($lang_ssh);
}

unset($action_permission);

require_once 'footer.php';


?>
