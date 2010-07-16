#tag Class
Protected Class Item
Inherits RunnerClass
	#tag Event
		Sub Run()
		  do
		    dim ItemID As integer
		    dim ItemClass As integer
		    dim ItemSubClass As integer
		    dim Unknown As Integer
		    dim MaterialID As integer
		    dim ItemDisplayInfo As integer
		    dim InventorySlotID As integer
		    dim SheathID As  Integer
		    
		    if record < recordCount then
		      Window1.ProgItem.text = str(Record) + "/" + str(recordCount - 1)
		      Window1.ProgItem.Refresh
		      
		      ItemID = b.ReadInt32
		      ItemClass = b.ReadInt32
		      ItemSubClass = b.ReadInt32
		      Unknown = b.ReadInt32
		      MaterialID = b.ReadInt32
		      ItemDisplayInfo = b.ReadInt32
		      InventorySlotID = b.ReadInt32
		      SheathID = b.ReadInt32
		      
		      dim query as string
		      query = "INSERT INTO item VALUES (" + str(ItemID) + ", " + str(ItemClass) + ", " + str(ItemSubClass) + ", " + str(Unknown) + ", " + str(MaterialID) _
		      + ", " + str(ItemDisplayInfo) + ", " + str(InventorySlotID) + ", " + str(SheathID) + ")"
		      
		      db.SQLExecute(query)
		      
		      if db.ErrorMessage <> "" then
		        Window1.TextArea1.text = Window1.TextArea1.text + chr(13) + db.ErrorMessage + "(" + query + ")"
		        exit do
		      end if
		      Record = Record + 1
		    else
		      Window1.ProgItem.text = "COMPLETE"
		      Window1.ProgItem.Refresh
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
