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
// Core Selection
//
// 1 - ArcEmu
// 2 - MaNGOS
// 3 - Trinity

$core = 0;

//#############################################################################
// CoreManager Database Configuration

$arcm_db['addr']     = '127.0.0.1:3306';         // SQL server IP:port your CoreManager DB is located on
$arcm_db['user']     = 'root';                   // SQL server login your CoreManager DB is located on
$arcm_db['pass']     = 'password';               // SQL server pass your CoreManager DB is located on
$arcm_db['name']     = 'db name';                // CoreManager DB name
$arcm_db['encoding'] = 'utf8';                   // SQL connection encoding

//#############################################################################
// SQL Configuration
//
//  SQL server type  :
//  'MySQL'   - Mysql
//  'PgSQL'   - PostgreSQL
//  'MySQLi'  - MySQLi
//  'SQLLite' - SQLite

$db_type          = 'MySQL';

//#############################################################################
//
// DO NOT CHANGE ANYTHING AFTER THIS POINT
//
//#############################################################################

require_once 'libs/config_lib.php';

?>
