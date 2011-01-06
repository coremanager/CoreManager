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


if ($db_type === 'MySQL')
  require_once 'db_lib/mysql.php';
elseif ($db_type === 'PgSQL')
  require_once 'db_lib/pgsql.php';
elseif ($db_type === 'MySQLi')
  require_once 'db_lib/mysqli.php';
elseif ($db_type === 'SQLLite')
  require_once 'db_lib/sqlite.php';
else
  exit('<center /><br /><code />'.$db_type.'</code> is not a valid database type.<br>
    Please check settings in <code>\'scripts/config.php\'</code>.</center>');


?>
