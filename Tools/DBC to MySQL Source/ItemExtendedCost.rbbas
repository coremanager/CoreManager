#tag Class
Protected Class ItemExtendedCost
Inherits RunnerClass
	#tag Event
		Sub Run()
		  do
		    dim ID As integer
		    dim ReqHonorPoints As integer
		    dim ReqArenaPoints As integer
		    dim Unknown As integer
		    dim RequiredItem1 As integer
		    dim RequiredItem2 As integer
		    dim RequiredItem3 As integer
		    dim RequiredItem4 As integer
		    dim RequiredItem5 As integer
		    dim RequiredItemCount1 As integer
		    dim RequiredItemCount2 As integer
		    dim RequiredItemCount3 As integer
		    dim RequiredItemCount4 As integer
		    dim RequiredItemCount5 As integer
		    dim RequiredPersonalArenaRating As integer
		    dim PurchaseGroup As integer
		    
		    if record < recordCount then
		      Window1.ProgItemExtendedCost.text = str(Record) + "/" + str(recordCount - 1)
		      Window1.ProgItemExtendedCost.Refresh
		      
		      ID = b.ReadInt32
		      ReqHonorPoints = b.ReadInt32
		      ReqArenaPoints = b.ReadInt32
		      Unknown = b.ReadInt32
		      RequiredItem1 = b.ReadInt32
		      RequiredItem2 = b.ReadInt32
		      RequiredItem3 = b.ReadInt32
		      RequiredItem4 = b.ReadInt32
		      RequiredItem5 = b.ReadInt32
		      RequiredItemCount1 = b.ReadInt32
		      RequiredItemCount2 = b.ReadInt32
		      RequiredItemCount3 = b.ReadInt32
		      RequiredItemCount4 = b.ReadInt32
		      RequiredItemCount5 = b.ReadInt32
		      RequiredPersonalArenaRating = b.ReadInt32
		      PurchaseGroup = b.ReadInt32
		      
		      dim query as string
		      query = "INSERT INTO itemextendedcost VALUES (" + str(ID) + ", " + str(ReqHonorPoints) + ", " + str(ReqArenaPoints) + ", " + str(Unknown) + ", " _
		      + str(RequiredItem1) + ", " + str(RequiredItem2) + ", " + str(RequiredItem3) + ", " + str(RequiredItem4) + ", " + str(RequiredItem5) + ", " _
		      + str(RequiredItemCount1) + ", " + str(RequiredItemCount2) + ", " + str(RequiredItemCount3) + ", " + str(RequiredItemCount4) + ", " _
		      + str(RequiredItemCount5) + ", " + str(RequiredPersonalArenaRating) + ", " + str(PurchaseGroup) + ")"
		      
		      db.SQLExecute(query)
		      
		      if db.ErrorMessage <> "" then
		        Window1.TextArea1.text = Window1.TextArea1.text + chr(13) + db.ErrorMessage + "(" + query + ")"
		        exit do
		      end if
		      Record = Record + 1
		    else
		      Window1.ProgItemExtendedCost.text = "COMPLETE"
		      Window1.ProgItemExtendedCost.Refresh
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
