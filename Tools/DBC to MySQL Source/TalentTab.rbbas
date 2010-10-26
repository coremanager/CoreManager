#tag Class
Protected Class TalentTab
Inherits RunnerClass
	#tag Event
		Sub Run()
		  do
		    dim ID As integer
		    dim Name As string
		    dim SpellIcon As Integer
		    dim Races As UInt32
		    dim Classes As UInt32
		    dim CreatureFamily As UInt32
		    dim TabNumber As Integer
		    dim InternalName As String
		    
		    dim red, blue As integer
		    
		    if record < recordCount then
		      Window1.ProgTalentTab.text = str(Record) + "/" + str(recordCount - 1)
		      blue = floor((Record / recordCount) * 255)
		      red = 255 - blue
		      Window1.ProgTalentTab.TextColor = RGB(red, 0, blue)
		      Window1.ProgTalentTab.Refresh
		      
		      ID = b.ReadInt32
		      
		      // Localization skip
		      b.Position = b.Position + (Localization * 4)
		      offset = b.Position
		      stringPos = b.ReadUInt32
		      Name = MySQLPrepare(GetString(stringStart + stringPos, b))
		      //skip
		      offset = offset + ((17 - Localization) * 4)
		      b.Position = offset
		      
		      SpellIcon = b.ReadInt32
		      Races = b.ReadUInt32
		      Classes = b.ReadUInt32
		      CreatureFamily = b.ReadUInt32
		      TabNumber = b.ReadInt32
		      
		      offset = b.Position
		      stringPos = b.ReadUInt32
		      InternalName = MySQLPrepare(GetString(stringStart + stringPos, b))
		      //skip
		      offset = offset + 4
		      b.Position = offset
		      
		      dim query as string
		      query = "INSERT INTO talenttab VALUES (" + str(ID) + ", '" + Name + "', " + str(SpellIcon) + ", " + str(Races) + ", " + str(Classes) + ", " _
		      + str(CreatureFamily) + ", " + str(TabNumber) + ", '" + InternalName + "')"
		      
		      db.SQLExecute(query)
		      
		      if db.ErrorMessage <> "" then
		        Window1.TextArea1.text = Window1.TextArea1.text + chr(13) + db.ErrorMessage + "(" + query + ")"
		        exit do
		      end if
		      Record = Record + 1
		    else
		      Window1.ProgTalentTab.text = "COMPLETE"
		      Window1.ProgTalentTab.TextColor = &c0000FF
		      Window1.ProgTalentTab.Refresh
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
			InheritedFrom="Thread"
		#tag EndViewProperty
		#tag ViewProperty
			Name="Left"
			Visible=true
			Group="Position"
			Type="Integer"
			InheritedFrom="Thread"
		#tag EndViewProperty
		#tag ViewProperty
			Name="Name"
			Visible=true
			Group="ID"
			InheritedFrom="Thread"
		#tag EndViewProperty
		#tag ViewProperty
			Name="Priority"
			Visible=true
			Group="Behavior"
			InitialValue="5"
			Type="Integer"
			InheritedFrom="Thread"
		#tag EndViewProperty
		#tag ViewProperty
			Name="StackSize"
			Visible=true
			Group="Behavior"
			InitialValue="0"
			Type="Integer"
			InheritedFrom="Thread"
		#tag EndViewProperty
		#tag ViewProperty
			Name="Super"
			Visible=true
			Group="ID"
			InheritedFrom="Thread"
		#tag EndViewProperty
		#tag ViewProperty
			Name="Top"
			Visible=true
			Group="Position"
			Type="Integer"
			InheritedFrom="Thread"
		#tag EndViewProperty
	#tag EndViewBehavior
End Class
#tag EndClass
