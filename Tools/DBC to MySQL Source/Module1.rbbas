#tag Module
Protected Module Module1
	#tag Method, Flags = &h0
		Function GetString(position As UInt32, b As BinaryStream) As string
		  dim temp As string
		  dim c,c1,c2 As integer
		  
		  b.Position = position
		  
		  do
		    c = b.ReadUInt8
		    if c <> 0 then
		      if c < 128 then
		        temp = temp + temp.Encoding.Chr(c)
		      else
		        if Localization = 1 or Localization = 4 or Localization = 5 then
		          c1 = b.ReadUInt8
		          c2 = b.ReadUInt8
		          c = (((c * 256) + c1) * 256) + c2
		          temp = temp + temp.Encoding.Chr(c)
		        else
		          c1 = b.ReadUInt8
		          c = (c * 256) + c1
		          temp = temp + temp.Encoding.Chr(c)
		        end if
		      end if
		    else
		      exit do
		    end if
		  loop
		  
		  return temp
		End Function
	#tag EndMethod

	#tag Method, Flags = &h0
		Function MySQLPrepare(inp As string) As string
		  dim temp As string
		  
		  temp = inp
		  
		  temp = ReplaceAll(temp, "\", "\\")
		  
		  temp = ReplaceAll(temp, "'", "\'")
		  
		  temp = ReplaceAll(temp, chr(34), "\" + chr(34))
		  
		  return temp
		End Function
	#tag EndMethod


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


	#tag Property, Flags = &h0
		b As binarystream
	#tag EndProperty

	#tag Property, Flags = &h0
		d As folderitem
	#tag EndProperty

	#tag Property, Flags = &h0
		db As MySQLCommunityServer
	#tag EndProperty

	#tag Property, Flags = &h0
		f As folderitem
	#tag EndProperty

	#tag Property, Flags = &h0
		fieldCount As UInt32 = 0
	#tag EndProperty

	#tag Property, Flags = &h0
		FileList(-1) As string
	#tag EndProperty

	#tag Property, Flags = &h0
		Localization As Integer
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
			InitialValue="-2147483648"
			InheritedFrom="Object"
		#tag EndViewProperty
		#tag ViewProperty
			Name="Left"
			Visible=true
			Group="Position"
			InitialValue="0"
			InheritedFrom="Object"
		#tag EndViewProperty
		#tag ViewProperty
			Name="Localization"
			Group="Behavior"
			Type="Integer"
		#tag EndViewProperty
		#tag ViewProperty
			Name="Name"
			Visible=true
			Group="ID"
			InheritedFrom="Object"
		#tag EndViewProperty
		#tag ViewProperty
			Name="Super"
			Visible=true
			Group="ID"
			InheritedFrom="Object"
		#tag EndViewProperty
		#tag ViewProperty
			Name="Top"
			Visible=true
			Group="Position"
			InitialValue="0"
			InheritedFrom="Object"
		#tag EndViewProperty
	#tag EndViewBehavior
End Module
#tag EndModule
