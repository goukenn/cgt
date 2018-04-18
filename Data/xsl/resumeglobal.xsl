<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" 
xmlns="http://www.w3.org/1999/xhtml"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
xmlns:cgt="http://pivot.tourismewallonie.be/files/xsd/pivot/3.1" 
>


	<xsl:variable name="min" select="%min%" ></xsl:variable>
	<xsl:variable name="max" select="%max%" ></xsl:variable>
	<xsl:variable name="imguri" select="'%imguri%'" ></xsl:variable>
	<xsl:variable name="total" select="count(offres/offre)" ></xsl:variable>
	<xsl:variable name="pages" select="floor($total div ($max - $min))" ></xsl:variable>
<xsl:template match="/">
	<div class="view-z igk-row">
		
		<xsl:for-each select="offres/offre">
		<xsl:if test="(position() > $min) and not(position()>$max)" > 
		<div style="padding: 10px 4px" class="igk-col igk-col-5-1 igk-col-sm-4-2 igk-col-xsm-3-3 offre">
			<div>
			
				<xsl:variable name="cgtcode" select="current()/@codeCgt" ></xsl:variable>
				<xsl:variable name="fieldn" select="current()/nom" ></xsl:variable>
				<xsl:variable name="typeoffre" select="current()/typeOffre/@idTypeOffre"></xsl:variable>
				
				<img src="{$imguri}/{$cgtcode}" style="width:120px; height:90px" class="fitc" alt="offer picture"/>
				<div>Position : <xsl:value-of select="position()"></xsl:value-of></div>
				<a href="getDetails/{current()/@codeCgt}"><h3><xsl:value-of select="current()/nom" ></xsl:value-of></h3></a>
				<div>
				<xsl:value-of select="current()/adresse1/localite" ></xsl:value-of> - 
				<xsl:value-of select="current()/adresse1/commune" ></xsl:value-of>
				
				<div>
					<ul>						
						<li rol='lat'><xsl:value-of select="current()/adresse1/latitude" ></xsl:value-of></li>
						<li rol='lng'><xsl:value-of select="current()/adresse1/longitude" ></xsl:value-of></li>
						<li rol='type'>Type:<xsl:value-of select="current()/typeOffre/@idTypeOffre" ></xsl:value-of></li>
						<input type="hidden" class="geo" value="{{lat:{current()/adresse1/latitude}, lng:{current()/adresse1/longitude}, icon:'{$imguri}/urn:typ:{$typeoffre};w=16;h=16' }}" />
					</ul>
				</div>
				</div>
			</div>
		</div>
		</xsl:if>	
	</xsl:for-each>
	</div>
	<div class="pagination-z">
		<xsl:call-template name="pagination"></xsl:call-template>
	</div>
</xsl:template>

<xsl:template name="pagination" match="/s" >
<div>
<xsl:comment>Pagination Presentation</xsl:comment>
<div>TOTAL PAGES : <xsl:value-of select="$pages"></xsl:value-of></div>
<xsl:for-each select="offres/offre">	
	<xsl:variable name="page" select="round(position() div number($max - $min))" ></xsl:variable>	
	<xsl:variable name="pagen" select="round((position() + 1) div number($max - $min))" ></xsl:variable>	
		<xsl:if test="($pages >= $page) and not($page = $pagen)">
			<li style="display:inline-block; padding: 12px 4px;"><a href="/page/{$page + 1}" class='nav' value="{$page }" onclick="return false;" ><xsl:value-of select="$page + 1" ></xsl:value-of></a></li>
		</xsl:if>		
</xsl:for-each>
		
		
</div>
</xsl:template>
</xsl:stylesheet>