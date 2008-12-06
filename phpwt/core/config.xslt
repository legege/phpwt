<?xml version="1.0" encoding="utf-8"?>
<!--
   - PHPWT - PHP Website Tookit
   - Copyright (C) 2005-2008  Georges-Etienne Legendre
   -
   - This program is free software: you can redistribute it and/or modify
   - it under the terms of the GNU Lesser General Public License as published by
   - the Free Software Foundation, either version 3 of the License, or
   - (at your option) any later version.
   -
   - This program is distributed in the hope that it will be useful,
   - but WITHOUT ANY WARRANTY; without even the implied warranty of
   - MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   - GNU Lesser General Public License for more details.
   - 
   - You should have received a copy of the GNU Lesser General Public License
   - along with this program.  If not, see <http://www.gnu.org/licenses/>.
   - -->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:template match="/">
    <xsl:element name="toolkit">
      <xsl:apply-templates select="toolkit/import"/>
      <xsl:copy-of select="toolkit/*[name() != 'import']"/>
    </xsl:element>
  </xsl:template>
  <xsl:template match="import">    
    <xsl:copy-of select="document(@file)"/>
  </xsl:template>
</xsl:stylesheet>