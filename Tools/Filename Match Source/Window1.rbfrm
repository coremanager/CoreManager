#tag Note, Name = LICENSE
		
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
	#tag EndNote


#tag Window
Begin Window Window1
   BackColor       =   &hFFFFFF
   Backdrop        =   ""
   CloseButton     =   True
   Composite       =   False
   Frame           =   0
   FullScreen      =   False
   HasBackColor    =   False
   Height          =   400
   ImplicitInstance=   True
   LiveResize      =   True
   MacProcID       =   0
   MaxHeight       =   32000
   MaximizeButton  =   False
   MaxWidth        =   32000
   MenuBar         =   966508543
   MenuBarVisible  =   True
   MinHeight       =   64
   MinimizeButton  =   True
   MinWidth        =   64
   Placement       =   0
   Resizeable      =   True
   Title           =   "Filename Match"
   Visible         =   True
   Width           =   600
   Begin Timer Timer1
      Height          =   32
      Index           =   -2147483648
      Left            =   385
      LockedInPosition=   False
      Mode            =   0
      Period          =   1
      Scope           =   0
      TabPanelIndex   =   0
      Top             =   4
      Width           =   32
   End
   Begin PushButton PushButton1
      AutoDeactivate  =   True
      Bold            =   ""
      Cancel          =   ""
      Caption         =   "GO!"
      Default         =   ""
      Enabled         =   True
      Height          =   22
      HelpTag         =   ""
      Index           =   -2147483648
      InitialParent   =   ""
      Italic          =   ""
      Left            =   20
      LockBottom      =   ""
      LockedInPosition=   False
      LockLeft        =   ""
      LockRight       =   ""
      LockTop         =   ""
      Scope           =   0
      TabIndex        =   0
      TabPanelIndex   =   0
      TabStop         =   True
      TextFont        =   "System"
      TextSize        =   0
      TextUnit        =   0
      Top             =   14
      Underline       =   ""
      Visible         =   True
      Width           =   80
   End
   Begin TextArea TextArea1
      AcceptTabs      =   ""
      Alignment       =   0
      AutoDeactivate  =   True
      BackColor       =   &hFFFFFF
      Bold            =   ""
      Border          =   True
      DataField       =   ""
      DataSource      =   ""
      Enabled         =   True
      Format          =   ""
      Height          =   332
      HelpTag         =   ""
      HideSelection   =   True
      Index           =   -2147483648
      InitialParent   =   ""
      Italic          =   ""
      Left            =   20
      LimitText       =   0
      LockBottom      =   ""
      LockedInPosition=   False
      LockLeft        =   ""
      LockRight       =   ""
      LockTop         =   ""
      Mask            =   ""
      Multiline       =   True
      ReadOnly        =   ""
      Scope           =   0
      ScrollbarHorizontal=   ""
      ScrollbarVertical=   True
      Styled          =   True
      TabIndex        =   1
      TabPanelIndex   =   0
      TabStop         =   True
      Text            =   ""
      TextColor       =   &h000000
      TextFont        =   "System"
      TextSize        =   0
      TextUnit        =   0
      Top             =   48
      Underline       =   ""
      UseFocusRing    =   True
      Visible         =   True
      Width           =   560
   End
   Begin TextField TextField1
      AcceptTabs      =   ""
      Alignment       =   0
      AutoDeactivate  =   True
      BackColor       =   &hFFFFFF
      Bold            =   ""
      Border          =   True
      CueText         =   ""
      DataField       =   ""
      DataSource      =   ""
      Enabled         =   True
      Format          =   ""
      Height          =   22
      HelpTag         =   ""
      Index           =   -2147483648
      InitialParent   =   ""
      Italic          =   ""
      Left            =   112
      LimitText       =   0
      LockBottom      =   ""
      LockedInPosition=   False
      LockLeft        =   ""
      LockRight       =   ""
      LockTop         =   ""
      Mask            =   ""
      Password        =   ""
      ReadOnly        =   ""
      Scope           =   0
      TabIndex        =   2
      TabPanelIndex   =   0
      TabStop         =   True
      Text            =   ""
      TextColor       =   &h000000
      TextFont        =   "System"
      TextSize        =   0
      TextUnit        =   0
      Top             =   14
      Underline       =   ""
      UseFocusRing    =   True
      Visible         =   True
      Width           =   159
   End
End
#tag EndWindow

#tag WindowCode
	#tag Event
		Sub Open()
		  db = new MySQLCommunityServer
		  
		  dim configFile As FolderItem
		  
		  configFile = GetFolderItem("").Child("config.txt")
		  
		  if not configFile.Exists then
		    Dim dlg as New OpenDialog
		    dlg.ActionButtonCaption="Select"
		    
		    dlg.Title="Select config.txt"
		    
		    dlg.PromptText="Please locate config.txt."
		    
		    dlg.InitialDirectory=GetFolderItem("")
		    configFile = dlg.ShowModal()
		    
		    if configFile = nil then
		      exit sub
		    end if
		  end if
		  
		  dim config As string
		  dim lineCount As integer
		  dim i As integer
		  dim line As string
		  dim host, user, pass, name As string
		  dim port As integer
		  
		  if not configFile.Exists then
		    TextArea1.text = "Config.txt not found!"
		    exit sub
		  else
		    config = configFile.OpenAsTextFile.ReadAll
		    lineCount = CountFields(config, chr(13) + chr(10))
		    
		    do until i = lineCount + 1
		      line = NthField(config, chr(13) + chr(10), i)
		      select case trim(NthField(line, "=", 1))
		      case "HOST"
		        host = trim(NthField(line, "=", 2))
		      case "PORT"
		        port = val(trim(NthField(line, "=", 2)))
		      case "USER"
		        user = trim(NthField(line, "=", 2))
		      case "PASS"
		        pass = trim(NthField(line, "=", 2))
		      case "NAME"
		        name = trim(NthField(line, "=", 2))
		      end select
		      i = i + 1
		    loop
		  end if
		  
		  db.Host = host
		  db.UserName = User
		  db.Password = pass
		  db.DatabaseName = name
		  
		  Dim dlg as New SelectFolderDialog
		  dlg.ActionButtonCaption="Select"
		  
		  dlg.Title="Select directory"
		  
		  dlg.PromptText="Select the directory containing the Icon files."
		  
		  dlg.InitialDirectory=GetFolderItem("")
		  d = dlg.ShowModal()
		  
		  if d = nil then
		    exit sub
		  end if
		  
		  curFile = 1
		  
		  
		End Sub
	#tag EndEvent


	#tag Method, Flags = &h0
		Function MySQLPrepare(inp As string) As string
		  dim temp As string
		  
		  temp = inp
		  
		  temp = ReplaceAll(temp, "\", "\\")
		  
		  temp = ReplaceAll(temp, "'", "\'")
		  
		  temp = ReplaceAll(temp, chr(34), "\" + chr(34))
		  
		  return temp
		End Function
	#tag EndMethod


	#tag Property, Flags = &h0
		curFile As Integer
	#tag EndProperty

	#tag Property, Flags = &h0
		d As FolderItem
	#tag EndProperty

	#tag Property, Flags = &h0
		db As MySQLCommunityServer
	#tag EndProperty

	#tag Property, Flags = &h0
		totFiles As Integer
	#tag EndProperty


#tag EndWindowCode

#tag Events Timer1
	#tag Event
		Sub Action()
		  dim f As FolderItem
		  dim subName As string
		  dim dbcName As string
		  
		  if curFile <= totFiles then
		    f = d.Item(curFile)
		    
		    if f.Directory or f.Name = "Thumbs.db" or f.name = "icons.zip" or f.name = "Index.html" then
		      // skip other junk
		    else
		      subName = left(f.Name, len(f.Name) - 4)
		      
		      dim r As RecordSet
		      
		      if db.Connect then
		        r = db.SQLSelect("SELECT * FROM itemdisplayinfo WHERE IconName = '" + MySQLPrepare(subName) + "'")
		        if r.recordCount = 0 then
		          r = db.SQLSelect("SELECT * FROM spellicon WHERE Name = '" + MySQLPrepare("Interface\Icons\" + subName) + "'")
		          dbcName = r.field("Name").value
		          dbcName = NthField(dbcName, "\", 3)
		        else
		          dbcName = r.field("IconName").value
		        end if
		      end if
		      
		      if dbcName <> "" then
		        if StrComp(subName,dbcName,0) <> 0 then
		          f.Name = dbcName + ".png"
		        end if
		      end if
		    end if
		    
		    TextField1.text = str(curFile) + "/" + str(totFiles)
		    
		    curFile = curFile + 1
		  end if
		End Sub
	#tag EndEvent
#tag EndEvents
#tag Events PushButton1
	#tag Event
		Sub Action()
		  if d <> nil then
		    totFiles = d.Count
		    Timer1.Mode = 2
		  end if
		End Sub
	#tag EndEvent
#tag EndEvents
