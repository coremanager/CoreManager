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


if (ini_get('session.auto_start'));
else session_start();

unset($_SESSION['user_id']);
unset($_SESSION['login']);
unset($_SESSION['screenname']);
unset($_SESSION['user_lvl']);
unset($_SESSION['gm_lvl']);
unset($_SESSION['realm_id']);
unset($_SESSION['client_ip']);
unset($_SESSION['logged_in']);

session_destroy();

if (strpos($_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS') === false)
{
  header('Location: http://'.$_SERVER['HTTP_HOST'].rtrim(dirname($_SERVER['PHP_SELF']), '/\\').'/index.php');
  exit();
}
else
  die('<meta http-equiv="refresh" content="0;URL=index.php" />');


?>
