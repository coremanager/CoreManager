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

function CheckAll(obj)
{
  for (var i=0;i<obj.elements.length;i++)
  {
    var e = obj.elements[i];
    if ((e.name != 'allbox') && (e.type=='checkbox') && (!e.disabled))
    {
      e.checked = obj.allbox.checked;
    }
  }
}

function CheckCheckAll(obj)
{
  var TotalBoxes = 0;
  var TotalOn = 0;
  for (var i=0;i<obj.elements.length;i++)
  {
    var e = obj.elements[i];
    if ((e.name != 'allbox') && (e.type=='checkbox'))
    {
      TotalBoxes++;
      if (e.checked)
      {
        TotalOn++;
      }
    }
  }

  if (TotalBoxes==TotalOn)
  {
    obj.allbox.checked=true;
  }
  else
  {
    obj.allbox.checked=false;
  }
}
