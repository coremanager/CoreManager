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


//#############################################################################
//get country code and country name by IP
// given IP, returns array('code','country')
// 'code' is country code, 'country' is country name.

function misc_get_country_by_ip($ip)
{
  global $sql;

  $country = $sql["mgr"]->fetch_assoc($sql["mgr"]->query(
    "SELECT c.code, c.country FROM ip2nationcountries c, ip2nation i
      WHERE i.ip<=INET_ATON('".$ip."') AND c.code=i.country
        ORDER BY i.ip DESC LIMIT 0,1;"));
  $country["actualip"] = $ip;

  return $country;
}


//#############################################################################
//get country code and country name by IP
// given account ID, returns array('code','country')
// 'code' is country code, 'country' is country name.

function misc_get_country_by_account($account)
{
  global $sql, $core;

  if ( $core == 1 )
    $ip = $sql["logon"]->fetch_assoc($sql["logon"]->query("SELECT lastip FROM accounts WHERE acct='".$account."';"));
  else
    $ip = $sql["logon"]->fetch_assoc($sql["logon"]->query("SELECT last_ip AS lastip FROM account WHERE id='".$account."';"));

  return misc_get_country_by_ip($ip["lastip"]);
}


?>
