#tag Class
Protected Class AreaTable
Inherits RunnerClass
	#tag Event
		Sub Run()
		  do
		    dim ID As integer
		    dim Map As integer
		    dim AreaTable As integer
		    dim ExploreFlag As integer
		    dim Flags As UInt32
		    dim SoundPreferences As integer
		    dim Unk1 As integer
		    dim SoundAmbience As integer
		    dim ZoneMusic As integer
		    dim ZoneIntroMusicTable As integer
		    dim AreaLevel As integer
		    dim Name As string
		    dim FactionGroup As UInt32
		    dim Unk2 As integer
		    dim Unk3 As integer
		    dim Unk4 As integer
		    dim Unk5 As integer
		    dim Unk6 As single
		    dim Unk7 As single
		    dim Unk8 As integer
		    
		    if record < recordCount then
		      Window1.ProgAreaTable.text = str(Record) + "/" + str(recordCount - 1)
		      Window1.ProgAreaTable.Refresh
		      
		      ID = b.ReadInt32
		      Map = b.ReadInt32
		      AreaTable = b.ReadInt32
		      ExploreFlag = b.ReadInt32
		      Flags = b.ReadUInt32
		      SoundPreferences = b.ReadInt32
		      Unk1 = b.ReadInt32
		      SoundAmbience = b.ReadInt32
		      ZoneMusic = b.ReadInt32
		      ZoneIntroMusicTable = b.ReadInt32
		      AreaLevel = b.ReadInt32
		      
		      // Localization skip
		      b.Position = b.Position + (Localization * 4)
		      offset = b.Position
		      stringPos = b.ReadUInt32
		      Name = MySQLPrepare(GetString(stringStart + stringPos, b))
		      //skip
		      offset = offset + ((17 - Localization) * 4)
		      b.Position = offset
		      
		      FactionGroup = b.ReadUInt32
		      Unk2 = b.ReadInt32
		      Unk3 = b.ReadInt32
		      Unk4 = b.ReadInt32
		      Unk5 = b.ReadInt32
		      Unk6 = b.ReadSingle
		      Unk7 = b.ReadSingle
		      Unk8 = b.ReadInt32
		      
		      dim query as string
		      query = "INSERT INTO areatable VALUES (" + str(ID) + ", " + str(Map) + ", " + str(AreaTable) + ", " + str(ExploreFlag) + ", " + str(Flags) + ", " _
		      + str(SoundPreferences) + ", " + str(Unk1) + ", " + str(SoundAmbience) + ", " + str(ZoneMusic) + ", " + str(ZoneIntroMusicTable) + ", " _
		      + str(AreaLevel) + ", '" + Name + "', " + str(FactionGroup) + ", " + str(Unk2) + ", " + str(Unk3) + ", " + str(Unk4) + ", " + str(Unk5) + ", " _
		      + str(Unk6) + ", " + str(Unk7) + ", " + str(Unk8) + ")"
		      
		      db.SQLExecute(query)
		      if db.ErrorMessage <> "" then
		        Window1.TextArea1.text = Window1.TextArea1.text + chr(13) + db.ErrorMessage + "(" + query + ")"
		        exit do
		      end if
		      Record = Record + 1
		    else
		      Window1.ProgAreaTable.text = "COMPLETE"
		      Window1.ProgAreaTable.Refresh
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
