#tag Class
Protected Class ItemRandomProperties
Inherits RunnerClass
	#tag Event
		Sub Run()
		  do
		    dim ID As integer
		    dim Name As string
		    dim SpellItemEnchantment_1 As integer
		    dim SpellItemEnchantment_2 As integer
		    dim SpellItemEnchantment_3 As integer
		    dim SpellItemEnchantment_4 As integer
		    dim SpellItemEnchantment_5 As integer
		    dim Suffix As String
		    dim SuffixFlags As integer
		    
		    if record < recordCount then
		      Window1.ProgItemRandomProperties.text = str(Record) + "/" + str(recordCount - 1)
		      Window1.ProgItemRandomProperties.Refresh
		      
		      ID = b.ReadInt32
		      
		      offset = b.Position
		      stringPos = b.ReadUInt32
		      Name = MySQLPrepare(GetString(stringStart + stringPos, b))
		      //skip to next field
		      offset = offset + 4
		      b.Position = offset
		      
		      SpellItemEnchantment_1 = b.ReadInt32
		      SpellItemEnchantment_2 = b.ReadInt32
		      SpellItemEnchantment_3 = b.ReadInt32
		      SpellItemEnchantment_4 = b.ReadInt32
		      SpellItemEnchantment_5 = b.ReadInt32
		      
		      offset = b.Position
		      stringPos = b.ReadUInt32
		      Suffix = MySQLPrepare(GetString(stringStart + stringPos, b))
		      //skip useless data
		      offset = offset + (16 * 4)
		      b.Position = offset
		      
		      SuffixFlags = b.ReadInt32
		      
		      dim query as string
		      query = "INSERT INTO itemrandomproperties VALUES(" + _
		      str(ID) + ", '" + _
		      Name + "', " + _
		      str(SpellItemEnchantment_1) + ", " + _
		      str(SpellItemEnchantment_2) + ", " + _
		      str(SpellItemEnchantment_3) + ", " + _
		      str(SpellItemEnchantment_4) + ", " + _
		      str(SpellItemEnchantment_5) + ", '" + _
		      Suffix + "', " + _
		      str(SuffixFlags) + _
		      ")"
		      
		      db.SQLExecute(query)
		      
		      if db.ErrorMessage <> "" then
		        Window1.TextArea1.text = Window1.TextArea1.text + chr(13) + db.ErrorMessage + "(" + query + ")"
		        exit do
		      end if
		      Record = Record + 1
		    else
		      Window1.ProgItemRandomProperties.text = "COMPLETE"
		      Window1.ProgItemRandomProperties.Refresh
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
