#tag Class
Protected Class Faction
Inherits RunnerClass
	#tag Event
		Sub Run()
		  do
		    dim ID As integer
		    dim UniqueGainID As integer
		    dim AtWar As UInt32
		    dim Allied As UInt32
		    dim Unknown1 As UInt32
		    dim Unknown2 As UInt32
		    dim Unknown3 As UInt32
		    dim Unknown4 As  UInt32
		    dim Unknown5 As UInt32
		    dim Unknown6 As UInt32
		    dim BaseReputation As integer
		    dim Modifier1 As Integer
		    dim Modifier2 As integer
		    dim Modifier3 As integer
		    dim Condition1 As integer
		    dim Condition2 As  Integer
		    dim Condition3 As integer
		    dim Condition4 As integer
		    dim ParentFaction As integer
		    dim Name As string
		    dim Description As string
		    
		    dim red, blue As integer
		    
		    if record < recordCount then
		      Window1.ProgFaction.text = str(Record) + "/" + str(recordCount - 1)
		      blue = floor((Record / recordCount) * 255)
		      red = 255 - blue
		      Window1.ProgFaction.TextColor = RGB(red, 0, blue)
		      Window1.ProgFaction.Refresh
		      
		      ID = b.ReadInt32
		      UniqueGainID = b.ReadInt32
		      AtWar = b.ReadUInt32
		      Allied = b.ReadUInt32
		      Unknown1 = b.ReadUInt32
		      Unknown2 = b.ReadUInt32
		      Unknown3 = b.ReadUInt32
		      Unknown4 = b.ReadUInt32
		      Unknown5 = b.ReadUInt32
		      Unknown6 = b.ReadUInt32
		      BaseReputation = b.ReadInt32
		      Modifier1 = b.ReadInt32
		      Modifier2 = b.ReadInt32
		      Modifier3 = b.ReadInt32
		      Condition1 = b.ReadInt32
		      Condition2 = b.ReadInt32
		      Condition3 = b.ReadInt32
		      Condition4 = b.ReadInt32
		      ParentFaction = b.ReadInt32
		      
		      b.Position = b.Position + (4 * 4) // 4 new fields added in 3.3, we don't need them
		      
		      // Localization skip
		      b.Position = b.Position + (Localization * 4)
		      offset = b.Position
		      stringPos = b.ReadUInt32
		      Name = MySQLPrepare(GetString(stringStart + stringPos, b))
		      //skip
		      offset = offset + ((17 - Localization) * 4)
		      b.Position = offset
		      
		      // Localization skip
		      b.Position = b.Position + (Localization * 4)
		      offset = b.Position
		      stringPos = b.ReadUInt32
		      if stringPos <> 0 then
		        description = MySQLPrepare(GetString(stringStart + stringPos, b))
		      else
		        description = ""
		      end if
		      //skip useless data
		      offset = offset + ((17 - Localization) * 4)
		      b.Position = offset
		      
		      dim query as string
		      query = "INSERT INTO faction VALUES (" + str(id) + ", " + str(UniqueGainID) + ", " + str(AtWar) + ", " + str(Allied) + ", " + str(Unknown1) + ", " _
		      + str(Unknown2) + ", " + str(Unknown3) + ", " + str(Unknown4) + ", " + str(Unknown5) + ", " + str(Unknown6) + ", " + str(BaseReputation) + ", " _
		      + str(Modifier1) + ", " + str(Modifier2) + ", " + str(Modifier3) + ", " + str(Condition1) + ", " + str(Condition2) + ", " + str(Condition3) + ", " _
		      + str(Condition4) + ", " + str(ParentFaction) + ", '" + str(Name) + "', '" + str(Description) + "')"
		      
		      db.SQLExecute(query)
		      
		      if db.ErrorMessage <> "" then
		        Window1.TextArea1.text = Window1.TextArea1.text + chr(13) + db.ErrorMessage + "(" + query + ")"
		        exit do
		      end if
		      Record = Record + 1
		    else
		      Window1.ProgFaction.text = "COMPLETE"
		      Window1.ProgFaction.TextColor = &c0000FF
		      Window1.ProgFaction.Refresh
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
