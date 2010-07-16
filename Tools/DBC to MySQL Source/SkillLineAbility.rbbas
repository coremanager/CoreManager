#tag Class
Protected Class SkillLineAbility
Inherits RunnerClass
	#tag Event
		Sub Run()
		  do
		    dim ID As integer
		    dim SkillLine As integer
		    dim Spell As integer
		    dim ChrRaces As UInt32
		    dim ChrClasses As UInt32
		    dim ExcludeRace As UInt32
		    dim ExcludeClass As UInt32
		    dim ReqSkillValue As integer
		    dim SpellParent As integer
		    dim AcquireMethod As integer
		    dim SkillGreyLevel As integer
		    dim SkillGreenLevel As integer
		    dim CharacterPoints1 As integer
		    dim CharacterPoints2 As integer
		    
		    if record < recordCount then
		      Window1.ProgSkillLineAbility.text = str(Record) + "/" + str(recordCount - 1)
		      Window1.ProgSkillLineAbility.Refresh
		      
		      ID = b.ReadInt32
		      SkillLine = b.ReadInt32
		      Spell = b.ReadInt32
		      ChrRaces = b.ReadUInt32
		      ChrClasses = b.ReadUInt32
		      ExcludeRace = b.ReadUInt32
		      ExcludeClass = b.ReadInt32
		      ReqSkillValue = b.ReadInt32
		      SpellParent = b.ReadInt32
		      AcquireMethod = b.ReadInt32
		      SkillGreyLevel = b.ReadInt32
		      SkillGreenLevel = b.ReadInt32
		      CharacterPoints1 = b.ReadInt32
		      CharacterPoints2 = b.ReadInt32
		      
		      dim query as string
		      query = "INSERT INTO skilllineability VALUES(" + str(ID) + ", " + str(SkillLine) + ", " + str(Spell) + ", " + str(ChrRaces) + ", " + str(ChrClasses) + ", " _
		      + str(ExcludeRace) + ", " + str(ExcludeClass) + ", " + str(ReqSkillValue) + ", " + str(SpellParent) + ", " + str(AcquireMethod) + ", " + str(SkillGreyLevel) + ", " _
		      + str(SkillGreenLevel) + ", " + str(CharacterPoints1) + ", " + str(CharacterPoints2) + ")"
		      
		      db.SQLExecute(query)
		      
		      if db.ErrorMessage <> "" then
		        Window1.TextArea1.text = Window1.TextArea1.text + chr(13) + db.ErrorMessage + "(" + query + ")"
		        exit do
		      end if
		      Record = Record + 1
		    else
		      Window1.ProgSkillLineAbility.text = "COMPLETE"
		      Window1.ProgSkillLineAbility.Refresh
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
