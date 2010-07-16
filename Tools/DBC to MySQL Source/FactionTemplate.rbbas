#tag Class
Protected Class FactionTemplate
Inherits RunnerClass
	#tag Event
		Sub Run()
		  do
		    dim ID As integer
		    dim Faction As integer
		    dim Flags As UInt32
		    dim FactionGroup As UInt32
		    dim FriendGroup As UInt32
		    dim EnemyGroup As UInt32
		    dim Enemies1 As UInt32
		    dim Enemies2 As  UInt32
		    dim Enemies3 As UInt32
		    dim Enemies4 As UInt32
		    dim Friend1 As UInt32
		    dim Friend2 As UInt32
		    dim Friend3 As UInt32
		    dim Friend4 As UInt32
		    
		    if record < recordCount then
		      Window1.ProgFactionTemplate.text = str(Record) + "/" + str(recordCount - 1)
		      Window1.ProgFactionTemplate.Refresh
		      
		      ID = b.ReadInt32
		      Faction = b.ReadUInt32
		      Flags = b.ReadUInt32
		      FactionGroup = b.ReadUInt32
		      FriendGroup = b.ReadUInt32
		      EnemyGroup = b.ReadUInt32
		      Enemies1 = b.ReadUInt32
		      Enemies2 = b.ReadUInt32
		      Enemies3 = b.ReadUInt32
		      Enemies4 = b.ReadUInt32
		      Friend1 = b.ReadUInt32
		      Friend2 = b.ReadUInt32
		      Friend3 = b.ReadUInt32
		      Friend4 = b.ReadUInt32
		      
		      dim query as string
		      query = "INSERT INTO factiontemplate VALUES(" + str(ID) + ", " + str(Faction) + ", " + str(Flags) + ", " + str(FactionGroup) + ", " _
		      + str(FriendGroup) + ", " + str(EnemyGroup) + ", " + str(Enemies1) + ", " + str(Enemies2) + ", " + str(Enemies3) + ", " + str(Enemies4) + ", " _
		      + str(Friend1) + ", " + str(Friend2) + ", " + str(Friend3) + ", " + str(Friend4) + ")"
		      
		      db.SQLExecute(query)
		      
		      if db.ErrorMessage <> "" then
		        Window1.TextArea1.text = Window1.TextArea1.text + chr(13) + db.ErrorMessage + "(" + query + ")"
		        exit do
		      end if
		      Record = Record + 1
		    else
		      Window1.ProgFactionTemplate.text = "COMPLETE"
		      Window1.ProgFactionTemplate.Refresh
		      exit do
		    end if
		  loop
		End Sub
	#tag EndEvent


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


	#tag ViewBehavior
		#tag ViewProperty
			Name="Index"
			Visible=true
			Group="ID"
			InheritedFrom="thread"
		#tag EndViewProperty
		#tag ViewProperty
			Name="Left"
			Visible=true
			Group="Position"
			Type="Integer"
			InheritedFrom="thread"
		#tag EndViewProperty
		#tag ViewProperty
			Name="Name"
			Visible=true
			Group="ID"
			InheritedFrom="thread"
		#tag EndViewProperty
		#tag ViewProperty
			Name="Priority"
			Visible=true
			Group="Behavior"
			InitialValue="5"
			Type="Integer"
			InheritedFrom="thread"
		#tag EndViewProperty
		#tag ViewProperty
			Name="StackSize"
			Visible=true
			Group="Behavior"
			InitialValue="0"
			Type="Integer"
			InheritedFrom="thread"
		#tag EndViewProperty
		#tag ViewProperty
			Name="Super"
			Visible=true
			Group="ID"
			InheritedFrom="thread"
		#tag EndViewProperty
		#tag ViewProperty
			Name="Top"
			Visible=true
			Group="Position"
			Type="Integer"
			InheritedFrom="thread"
		#tag EndViewProperty
	#tag EndViewBehavior
End Class
#tag EndClass
