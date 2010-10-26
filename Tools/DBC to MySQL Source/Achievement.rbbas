#tag Class
Protected Class Achievement
Inherits RunnerClass
	#tag Event
		Sub Run()
		  do
		    dim id As integer
		    dim faction As integer
		    dim map As integer
		    dim previous As integer
		    dim name As string
		    dim description As string
		    dim category As integer
		    dim points As integer
		    dim orderInGroup As integer
		    dim flags As integer
		    dim spellIcon As integer
		    dim reward as string
		    dim demands as integer
		    dim referencedAchievement as integer
		    
		    dim red, blue As integer
		    
		    if record < recordCount then
		      Window1.ProgAchievement.text = str(Record) + "/" + str(recordCount - 1)
		      blue = floor((Record / recordCount) * 255)
		      red = 255 - blue
		      Window1.ProgAchievement.TextColor = RGB(red, 0, blue)
		      Window1.ProgAchievement.Refresh
		      
		      id = b.ReadInt32
		      faction = b.ReadInt32
		      map = b.ReadInt32
		      previous = b.ReadInt32
		      
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
		      
		      category = b.ReadInt32
		      points = b.ReadInt32
		      orderInGroup = b.ReadInt32
		      flags = b.ReadInt32
		      spellIcon = b.ReadInt32
		      
		      // Localization skip
		      b.Position = b.Position + (Localization * 4)
		      offset = b.Position
		      stringPos = b.ReadUInt32
		      if stringPos <> 0 then
		        reward = MySQLPrepare(GetString(stringStart + stringPos, b))
		      else
		        reward = ""
		      end if
		      //skip useless data
		      offset = offset + ((17 - Localization) * 4)
		      b.Position = offset
		      
		      demands = b.ReadInt32
		      referencedAchievement = b.ReadUInt32
		      
		      dim query as string
		      query = "INSERT INTO achievement VALUES(" + str(id) + ", " + str(faction) + ", " + str(map) + ", " + str(previous) + ", '" + name + "', '" _
		      + description + "', " + str(category) + ", " + str(points) + ", " + str(orderInGroup) + ", " + str(flags) + ", " + str(spellIcon) + ", '" + reward + "', " _
		      + str(demands) + ", " + str(referencedAchievement) + ")"
		      
		      db.SQLExecute(query)
		      
		      if db.ErrorMessage <> "" then
		        Window1.TextArea1.text = Window1.TextArea1.text + chr(13) + db.ErrorMessage + "(" + query + ")"
		        exit do
		      end if
		      Record = Record + 1
		    else
		      Window1.ProgAchievement.text = "COMPLETE"
		      Window1.ProgAchievement.TextColor = &c0000FF
		      Window1.ProgAchievement.Refresh
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
