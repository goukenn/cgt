<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" 
xmlns="http://www.w3.org/1999/xhtml"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
xmlns:cgt="http://pivot.tourismewallonie.be/files/xsd/pivot/3.1" 
>
<xsl:template match="/">
<div> Choice
<div><xsl:value-of select='cgt:thesaurus/cgt:spec/@urn'></xsl:value-of></div>
<xsl:for-each select="cgt:thesaurus/cgt:spec/cgt:spec">
<div>
	<input type='checkbox' id='choice' value='' /><xsl:value-of select="current()/cgt:label[@lang='en']/cgt:value" ></xsl:value-of>
</div>
</xsl:for-each>
</div>
</xsl:template>
</xsl:stylesheet>