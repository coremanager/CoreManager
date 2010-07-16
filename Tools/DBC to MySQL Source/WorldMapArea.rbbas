#tag Class
Protected Class WorldMapArea
Inherits RunnerClass
	#tag Event
		Sub Run()
		  do
		    dim ID As integer
		    dim Map As integer
		    dim AreaTable As integer
		    dim RefCon As string
		    dim Y1 As Single
		    dim Y2 As Single
		    dim X1 As Single
		    dim X2 As Single
		    dim Map2 As integer
		    dim DungeonMap As integer
		    dim Unknown As integer
		    
		    if record < recordCount then
		      Window1.ProgWorldMapArea.text = str(Record) + "/" + str(recordCount - 1)
		      Window1.ProgWorldMapArea.Refresh
		      
		      ID = b.ReadInt32
		      Map = b.ReadInt32
		      AreaTable = b.ReadInt32
		      
		      offset = b.Position
		      stringPos = b.ReadUInt32
		      RefCon = MySQLPrepare(GetString(stringStart + stringPos, b))
		      //skip
		      offset = offset + 4
		      b.Position = offset
		      
		      Y1 = b.ReadSingle
		      Y2 = b.ReadSingle
		      X1 = b.ReadSingle
		      X2 = b.ReadSingle
		      Map2 = b.ReadInt32
		      DungeonMap = b.ReadInt32
		      Unknown = b.ReadInt32
		      
		      dim query as string
		      query = "INSERT INTO worldmaparea VALUES (" + str(ID) + ", " + str(Map) + ", " + str(AreaTable) + ", '" + RefCon + "', " + str(Y1) + ", " _
		      + str(Y2) + ", " + str(X1) + ", " + str(X2) + ", " + str(Map2) + ", " + str(DungeonMap) + ", " + str(Unknown) + ")"
		      
		      db.SQLExecute(query)
		      
		      if db.ErrorMessage <> "" then
		        Window1.TextArea1.text = Window1.TextArea1.text + chr(13) + db.ErrorMessage + "(" + query + ")"
		        exit do
		      end if
		      Record = Record + 1
		    else
		      Window1.ProgWorldMapArea.text = "COMPLETE"
		      Window1.ProgWorldMapArea.Refresh
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
