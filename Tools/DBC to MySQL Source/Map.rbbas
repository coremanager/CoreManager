#tag Class
Protected Class Map
Inherits RunnerClass
	#tag Event
		Sub Run()
		  do
		    dim ID As integer
		    dim InternalName As string
		    dim AreaType As Integer
		    dim IsBattleground As Integer
		    dim Name As string
		    dim AreaTable As Integer
		    dim Description1 As string
		    dim Description2 As string
		    dim LoadingScreen As Integer
		    dim BattlefieldMapIconScale As Integer
		    dim ParentArea As Integer
		    dim XCoord As Integer
		    dim YCoord As Integer
		    dim TimeOfDayOverride As Integer
		    dim Expansion As Integer
		    dim ResetTimeOverride As integer
		    dim NumberOfPlayers As integer
		    
		    if record < recordCount then
		      Window1.ProgMap.text = str(Record) + "/" + str(recordCount - 1)
		      Window1.ProgMap.Refresh
		      
		      ID = b.ReadInt32
		      
		      offset = b.Position
		      stringPos = b.ReadUInt32
		      InternalName = MySQLPrepare(GetString(stringStart + stringPos, b))
		      //compensate for string frame
		      offset = offset + 4
		      b.Position = offset
		      
		      AreaType = b.ReadInt32
		      IsBattleground = b.ReadInt32
		      
		      // new field added in 3.3... we don't need it
		      b.Position = b.Position + 4
		      
		      offset = b.Position
		      stringPos = b.ReadUInt32
		      Name = MySQLPrepare(GetString(stringStart + stringPos, b))
		      //skip useless data
		      offset = offset + (17 * 4)
		      b.Position = offset
		      
		      AreaTable = b.ReadInt32
		      
		      offset = b.Position
		      stringPos = b.ReadUInt32
		      Description1 = MySQLPrepare(GetString(stringStart + stringPos, b))
		      //skip useless data
		      offset = offset + (17 * 4)
		      b.Position = offset
		      
		      offset = b.Position
		      stringPos = b.ReadUInt32
		      Description2 = MySQLPrepare(GetString(stringStart + stringPos, b))
		      //skip useless data
		      offset = offset + (17 * 4)
		      b.Position = offset
		      
		      LoadingScreen = b.ReadInt32
		      BattlefieldMapIconScale = b.ReadSingle
		      ParentArea = b.ReadInt32
		      XCoord = b.ReadSingle
		      YCoord = b.ReadSingle
		      TimeOfDayOverride = b.ReadInt32
		      Expansion = b.ReadInt32
		      ResetTimeOverride = b.ReadInt32
		      NumberOfPlayers = b.ReadInt32
		      
		      dim query as string
		      query = "INSERT INTO map VALUES(" + str(ID) + ", '" + InternalName + "', " + str(AreaType) + ", " + str(IsBattleground) + ", '" + name + "', " _
		      + str(AreaTable) + ", '" + Description1 + "', '" + Description2 + "', " + str(LoadingScreen) + ", " + str(BattlefieldMapIconScale) + ", " _
		      + str(ParentArea) + ", " + str(XCoord) + ", " + str(YCoord) + ", " + str(TimeOfDayOverride) + ", " + str(Expansion) + ", " + str(ResetTimeOverride) + ", " _
		      + str(NumberOfPlayers) + ")"
		      
		      db.SQLExecute(query)
		      
		      if db.ErrorMessage <> "" then
		        Window1.TextArea1.text = Window1.TextArea1.text + chr(13) + db.ErrorMessage + "(" + query + ")"
		        exit do
		      end if
		      Record = Record + 1
		    else
		      Window1.ProgMap.text = "COMPLETE"
		      Window1.ProgMap.Refresh
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
