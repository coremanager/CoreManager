#tag Class
Protected Class Talent
Inherits RunnerClass
	#tag Event
		Sub Run()
		  do
		    dim ID As integer
		    dim TalentTab as integer
		    dim Row as integer
		    dim Col as integer
		    dim Spell1 as integer
		    dim Spell2 as integer
		    dim Spell3 as integer
		    dim Spell4 as integer
		    dim Spell5 as integer
		    dim Spell6 as integer
		    dim Spell7 as integer
		    dim Spell8 as integer
		    dim Spell9 as integer
		    dim Talent1 as integer
		    dim Talent2 as integer
		    dim Talent3 as integer
		    dim TalentCount1 as integer
		    dim TalentCount2 as integer
		    dim TalentCount3 as integer
		    dim SinglePoint as integer
		    dim Unknown as integer
		    dim AllowForPetFlags1 as integer
		    dim AllowForPetFlags2 as integer
		    
		    dim red, blue As integer
		    
		    if record < recordCount then
		      Window1.ProgTalent.text = str(Record) + "/" + str(recordCount - 1)
		      blue = floor((Record / recordCount) * 255)
		      red = 255 - blue
		      Window1.ProgTalent.TextColor = RGB(red, 0, blue)
		      Window1.ProgTalent.Refresh
		      
		      ID = b.ReadInt32
		      TalentTab = b.ReadInt32
		      Row = b.ReadInt32
		      Col = b.ReadInt32
		      Spell1 = b.ReadInt32
		      Spell2 = b.ReadInt32
		      Spell3 = b.ReadInt32
		      Spell4 = b.ReadInt32
		      Spell5 = b.ReadInt32
		      Spell6 = b.ReadInt32
		      Spell7 = b.ReadInt32
		      Spell8 = b.ReadInt32
		      Spell9 = b.ReadInt32
		      Talent1 = b.ReadInt32
		      Talent2 = b.ReadInt32
		      Talent3 = b.ReadInt32
		      TalentCount1 = b.ReadInt32
		      TalentCount2 = b.ReadInt32
		      TalentCount3 = b.ReadInt32
		      SinglePoint = b.ReadInt32
		      Unknown = b.ReadInt32
		      AllowForPetFlags1 = b.ReadInt32
		      AllowForPetFlags2 = b.ReadInt32
		      
		      dim query as string
		      query = "INSERT INTO talent VALUES(" + str(ID) + ", " + str(TalentTab) + ", " + str(Row) + ", " + str(Col) + ", " + str(Spell1) + ", " _
		      + str(Spell2) + ", " + str(Spell3) + ", " + str(Spell4) + ", " + str(Spell5) + ", " + str(Spell6) + ", " + str(Spell7) + ", " + str(Spell8) + ", " _
		      + str(Spell9) + ", " + str(Talent1) + ", " + str(Talent2) + ", " + str(Talent3) + ", " + str(TalentCount1) + ", " + str(TalentCount2) + ", " _
		      + str(TalentCount3) + ", " + str(SinglePoint) + ", " + str(Unknown) + ", " + str(AllowForPetFlags1) + ", " + str(AllowForPetFlags2) + ")"
		      
		      db.SQLExecute(query)
		      
		      if db.ErrorMessage <> "" then
		        Window1.TextArea1.text = Window1.TextArea1.text + chr(13) + db.ErrorMessage + "(" + query + ")"
		        exit do
		      end if
		      Record = Record + 1
		    else
		      Window1.ProgTalent.text = "COMPLETE"
		      Window1.ProgTalent.TextColor = &c0000FF
		      Window1.ProgTalent.Refresh
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
