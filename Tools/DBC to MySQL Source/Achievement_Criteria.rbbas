#tag Class
Protected Class Achievement_Criteria
Inherits RunnerClass
	#tag Event
		Sub Run()
		  do
		    dim ID As integer
		    dim Achievement As integer
		    dim Type As integer
		    dim Requirement_1 As Integer
		    dim Value_1 As integer
		    dim Requirement_2 As integer
		    dim Value_2 As integer
		    dim Requirement_3 As  Integer
		    dim Value_3 As integer
		    dim Description As String
		    dim CompletionFlag As integer
		    dim GroupFlag As integer
		    dim Unknown As integer
		    dim Timelimit As integer
		    dim Order As integer
		    
		    dim red, blue As integer
		    
		    if record < recordCount then
		      Window1.ProgAchievement_Criteria.text = str(Record) + "/" + str(recordCount - 1)
		      blue = floor((Record / recordCount) * 255)
		      red = 255 - blue
		      Window1.ProgAchievement_Criteria.TextColor = RGB(red, 0, blue)
		      Window1.ProgAchievement_Criteria.Refresh
		      
		      id = b.ReadInt32
		      Achievement = b.ReadInt32
		      Type = b.ReadInt32
		      Requirement_1 = b.ReadInt32
		      Value_1 = b.ReadInt32
		      Requirement_2 = b.ReadInt32
		      Value_2 = b.ReadInt32
		      Requirement_3 = b.ReadInt32
		      Value_3 = b.ReadInt32
		      
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
		      
		      CompletionFlag = b.ReadInt32
		      GroupFlag = b.ReadInt32
		      Unknown = b.ReadInt32
		      Timelimit = b.ReadInt32
		      order = b.ReadInt32
		      
		      dim query as string
		      query = "INSERT INTO achievement_criteria VALUES(" + str(ID) + ", " + str(Achievement) + ", " + str(Type) + ", " + str(Requirement_1) + ", " _
		      + str(Value_1) + ", " + str(Requirement_2) + ", " + str(Value_2) + ", " + str(Requirement_3) + ", " + str(Value_3) + ", '" + Description + "', " _
		      + str(CompletionFlag) + ", " + str(GroupFlag) + ", " + str(Unknown) + ", " + str(Timelimit) + ", " + str(order) + ")"
		      
		      db.SQLExecute(query)
		      
		      if db.ErrorMessage <> "" then
		        Window1.TextArea1.text = Window1.TextArea1.text + chr(13) + db.ErrorMessage + "(" + query + ")"
		        exit do
		      end if
		      Record = Record + 1
		    else
		      Window1.ProgAchievement_Criteria.text = "COMPLETE"
		      Window1.ProgAchievement_Criteria.TextColor = &c0000FF
		      Window1.ProgAchievement_Criteria.Refresh
		      exit do
		    end if
		  loop
		End Sub
	#tag EndEvent


	#tag Note, Name = LICENSE
		
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
