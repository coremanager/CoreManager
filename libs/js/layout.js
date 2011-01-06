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

// getBrowserWidth is taken from The Man in Blue Resolution Dependent Layout Script
function getBrowserWidth()
{
  if (window.innerWidth)
  {
    return window.innerWidth;
  }
  else if (document.documentElement && document.documentElement.clientWidth != 0)
  {
    return document.documentElement.clientWidth;
  }
  else if (document.body)
  {
    return document.body.clientWidth;
  }
  return 0;
}

// changeLayout is based on setActiveStyleSheet function by Paul Sowdon 
// http://www.alistapart.com/articles/alternate/
function dynamicLayout()
{
  var i, a;
  for(i=0; (a = document.getElementsByTagName("link")[i]); i++)
  {
    if (a.getAttribute("title") == 1280)
    {
      ( getBrowserWidth() > 1024) ? a.disabled = false : a.disabled = true;
    }
  }
}

function addEvent( obj, type, fn )
{
  if (obj.addEventListener)
  {
    obj.addEventListener( type, fn, false );
  }
  else if (obj.attachEvent)
  {
    obj["e"+type+fn] = fn;
    obj[type+fn] = function()
    {
      obj["e"+type+fn]( window.event );
    }
    obj.attachEvent( "on"+type, obj[type+fn] );
  }
}

addEvent(window, 'load', dynamicLayout);
addEvent(window, 'resize', dynamicLayout);
