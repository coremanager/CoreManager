#tag Class
Protected Class RunnerClass
Inherits Thread
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


	#tag Property, Flags = &h0
		b As BinaryStream
	#tag EndProperty

	#tag Property, Flags = &h0
		fieldCount As UInt32 = 0
	#tag EndProperty

	#tag Property, Flags = &h0
		offset As Uint64 = 0
	#tag EndProperty

	#tag Property, Flags = &h0
		record As UInt32 = 0
	#tag EndProperty

	#tag Property, Flags = &h0
		recordCount As UInt32 = 0
	#tag EndProperty

	#tag Property, Flags = &h0
		recordSize As UInt32 = 0
	#tag EndProperty

	#tag Property, Flags = &h0
		stringPos As UInt32 = 0
	#tag EndProperty

	#tag Property, Flags = &h0
		stringSize As UInt32 = 0
	#tag EndProperty

	#tag Property, Flags = &h0
		stringStart As UInt32 = 0
	#tag EndProperty


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
