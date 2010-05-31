#tag Class
Protected Class ItemSet
Inherits RunnerClass
	#tag Event
		Sub Run()
		  do
		    dim ID As integer
		    dim ItemName As string
		    dim Item1 As Integer
		    dim Item2 As Integer
		    dim Item3 As Integer
		    dim Item4 As Integer
		    dim Item5 As Integer
		    dim Item6 As Integer
		    dim Item7 As Integer
		    dim Item8 As Integer
		    dim Item9 As Integer
		    dim Item10 As Integer
		    dim Spell1 As integer
		    dim Spell2 As integer
		    dim Spell3 As integer
		    dim Spell4 As integer
		    dim Spell5 As integer
		    dim Spell6 As integer
		    dim Spell7 As integer
		    dim Spell8 As integer
		    dim Bonus1 As integer
		    dim Bonus2 As integer
		    dim Bonus3 As integer
		    dim Bonus4 As integer
		    dim Bonus5 As integer
		    dim Bonus6 As integer
		    dim Bonus7 As integer
		    dim Bonus8 As integer
		    dim SkillLine As integer
		    dim ReqSkillLevel as integer
		    
		    if record < recordCount then
		      Window1.ProgItemSet.text = str(Record) + "/" + str(recordCount - 1)
		      Window1.ProgItemSet.Refresh
		      
		      ID = b.ReadInt32
		      
		      offset = b.Position
		      stringPos = b.ReadUInt32
		      ItemName = MySQLPrepare(GetString(stringStart + stringPos, b))
		      //skip useless data
		      offset = offset + (17 * 4)
		      b.Position = offset
		      
		      Item1 = b.ReadInt32
		      Item2 = b.ReadInt32
		      Item3 = b.ReadInt32
		      Item4 = b.ReadInt32
		      Item5 = b.ReadInt32
		      Item6 = b.ReadInt32
		      Item7 = b.ReadInt32
		      Item8 = b.ReadInt32
		      Item9 = b.ReadInt32
		      Item10 = b.ReadInt32
		      
		      //skip nulls
		      offset = b.Position
		      offset = offset + (7 * 4)
		      b.Position = offset
		      
		      Spell1 = b.ReadInt32
		      Spell2 = b.ReadInt32
		      Spell3 = b.ReadInt32
		      Spell4 = b.ReadInt32
		      Spell5 = b.ReadInt32
		      Spell6 = b.ReadInt32
		      Spell7 = b.ReadInt32
		      Spell8 = b.ReadInt32
		      Bonus1 = b.ReadInt32
		      Bonus2 = b.ReadInt32
		      Bonus3 = b.ReadInt32
		      Bonus4 = b.ReadInt32
		      Bonus5 = b.ReadInt32
		      Bonus6 = b.ReadInt32
		      Bonus7 = b.ReadInt32
		      Bonus8 = b.ReadInt32
		      SkillLine = b.ReadInt32
		      ReqSkillLevel = b.ReadInt32
		      
		      dim query as string
		      query = "INSERT INTO itemset VALUES(" + str(ID) + ", '" + ItemName + "', " + str(Item1) + ", " + str(Item2) + ", " + str(Item3) + ", " _
		      + str(Item4) + ", " + str(Item5) + ", " + str(Item6) + ", " + str(Item7) + ", " + str(Item8) + ", " + str(Item9) + ", " + str(Item10) + ", " _
		      + str(Spell1) + ", " + str(Spell2) + ", " + str(Spell3) + ", " + str(Spell4) + ", " + str(Spell5) + ", " + str(Spell6) + ", " + str(Spell7) + ", " _
		      + str(Spell8) + ", " + str(Bonus1) + ", " + str(Bonus2) + ", " + str(Bonus3) + ", " + str(Bonus4) + ", " + str(Bonus5) + ", " + str(Bonus6) + ", " _
		      + str(Bonus7) + ", " + str(Bonus8) + ", " + str(SkillLine) + ", " + str(ReqSkillLevel) + ")"
		      
		      db.SQLExecute(query)
		      
		      if db.ErrorMessage <> "" then
		        Window1.TextArea1.text = Window1.TextArea1.text + chr(13) + db.ErrorMessage + "(" + query + ")"
		        exit do
		      end if
		      Record = Record + 1
		    else
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
