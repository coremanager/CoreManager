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

//thanks to Jonathan Hedley
var panes = new Array();

function setupPanes(containerId, defaultTabId)
{
  panes[containerId] = new Array();
  var maxHeight = 0; var maxWidth = 0;
  var container = document.getElementById(containerId);
  var paneContainer = container.getElementsByTagName("div")[0];
  var paneList = paneContainer.childNodes;
  for (var i=0; i < paneList.length; i++ )
  {
    var pane = paneList[i];
    if (pane.nodeType != 1)
      continue;
    if (pane.offsetHeight > maxHeight)
      maxHeight = pane.offsetHeight;
    if (pane.offsetWidth  > maxWidth )
      maxWidth  = pane.offsetWidth;
    panes[containerId][pane.id] = pane;
    pane.style.display = "none";
  }
  paneContainer.style.height = maxHeight + "px";
  paneContainer.style.width  = maxWidth + "px";
  document.getElementById(defaultTabId).onclick();
}

function showPane(paneId, activeTab)
{
  for (var con in panes)
  {
    activeTab.blur();
    activeTab.className = "jtab-active";
    if (panes[con][paneId] != null)
    {
      var pane = document.getElementById(paneId);
      pane.style.display = "block";
      var container = document.getElementById(con);
      var tabs = container.getElementsByTagName("ul")[0];
      var tabList = tabs.getElementsByTagName("a")
      for (var i=0; i<tabList.length; i++ )
      {
        var tab = tabList[i];
        if (tab != activeTab)
          tab.className = "jtab-disabled";
      }
      for (var i in panes[con])
      {
        var pane = panes[con][i];
        if (pane == undefined)
          continue;
        if (pane.id == paneId)
          continue;
        pane.style.display = "none"
      }
    }
  }
  return false;
}
