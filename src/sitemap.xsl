<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet
    version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:sitemap="http://www.sitemaps.org/schemas/sitemap/0.9"
    xmlns:xhtml="http://www.w3.org/1999/xhtml"
    xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"
    exclude-result-prefixes="sitemap xhtml image">

    <xsl:output method="html" encoding="UTF-8" indent="yes"/>

    <!-- Variables to count URLs (with or without namespace) -->
    <xsl:variable name="urls-with-ns" select="sitemap:urlset/sitemap:url"/>
    <xsl:variable name="urls-without-ns" select="urlset/url"/>
    <xsl:variable name="total-urls" select="count($urls-with-ns) + count($urls-without-ns)"/>

    <!-- Variables to count sitemap index entries (with or without namespace) -->
    <xsl:variable name="sitemaps-with-ns" select="sitemap:sitemapindex/sitemap:sitemap"/>
    <xsl:variable name="sitemaps-without-ns" select="sitemapindex/sitemap"/>
    <xsl:variable name="total-sitemaps" select="count($sitemaps-with-ns) + count($sitemaps-without-ns)"/>

    <!-- Determine sitemap type -->
    <xsl:variable name="is-index" select="$total-sitemaps &gt; 0"/>

    <xsl:template match="/">
        <html lang="en">
            <head>
                <title>
                    <xsl:choose>
                        <xsl:when test="$is-index">XML Sitemap Index</xsl:when>
                        <xsl:otherwise>XML Sitemap</xsl:otherwise>
                    </xsl:choose>
                </title>
                <style>
                    body {
                        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
                        color: #333;
                        max-width: 1200px;
                        margin: 0 auto;
                        padding: 20px;
                        background: #f5f5f5;
                    }
                    h1 {
                        color: #0073aa;
                        border-bottom: 3px solid #0073aa;
                        padding-bottom: 10px;
                    }
                    .sitemap-info {
                        background: white;
                        padding: 15px;
                        border-radius: 5px;
                        margin-bottom: 20px;
                        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                    }
                    table {
                        width: 100%;
                        border-collapse: collapse;
                        background: white;
                        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                        border-radius: 5px;
                        overflow: hidden;
                    }
                    th {
                        background: #0073aa;
                        color: white;
                        padding: 12px 15px;
                        text-align: left;
                        font-weight: 600;
                    }
                    td {
                        padding: 12px 15px;
                        border-bottom: 1px solid #eee;
                        vertical-align: top;
                    }
                    tr:nth-child(odd) td {
                        background: #ffffff;
                    }
                    tr:nth-child(even) td {
                        background: #f9f9f9;
                    }
                    tr:hover td {
                        background: #eef6fc;
                    }
                    a {
                        color: #0073aa;
                        text-decoration: none;
                        word-break: break-all;
                    }
                    a:hover {
                        text-decoration: underline;
                    }
                    .translations {
                        margin-top: 10px;
                        padding-top: 10px;
                        border-top: 1px dashed #ddd;
                    }
                    .translation-row {
                        display: block;
                        margin: 5px 0;
                        font-size: 13px;
                    }
                    .lang-badge {
                        display: inline-block;
                        min-width: 28px;
                        padding: 3px 8px;
                        margin-right: 10px;
                        background: #0073aa;
                        color: white;
                        border-radius: 3px;
                        font-size: 11px;
                        text-align: center;
                        font-weight: bold;
                        text-transform: uppercase;
                    }
                    .image-count {
                        display: inline-block;
                        padding: 3px 8px;
                        background: #46b450;
                        color: white;
                        border-radius: 3px;
                        font-size: 11px;
                        font-weight: bold;
                    }
                    .credit {
                        margin-top: 30px;
                        padding: 15px;
                        background: white;
                        border-radius: 5px;
                        text-align: center;
                        font-size: 13px;
                        color: #666;
                        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                    }
                    .credit a {
                        font-weight: 600;
                    }
                    .url-count {
                        color: #666;
                        font-size: 14px;
                    }
                </style>
            </head>
            <body>
                <xsl:choose>
                    <!-- Sitemap Index -->
                    <xsl:when test="$is-index">
                        <h1>XML Sitemap Index</h1>
                        
                        <div class="sitemap-info">
                            <p class="url-count">
                                This sitemap index contains <strong><xsl:value-of select="$total-sitemaps"/></strong> sitemaps.
                            </p>
                        </div>

                        <table>
                            <thead>
                                <tr>
                                    <th>Sitemap</th>
                                    <th style="width: 180px;">Last Modified</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Sitemaps with namespace -->
                                <xsl:for-each select="sitemap:sitemapindex/sitemap:sitemap">
                                    <xsl:call-template name="sitemap-row">
                                        <xsl:with-param name="loc" select="sitemap:loc"/>
                                        <xsl:with-param name="lastmod" select="sitemap:lastmod"/>
                                    </xsl:call-template>
                                </xsl:for-each>
                                
                                <!-- Sitemaps without namespace (fallback) -->
                                <xsl:for-each select="sitemapindex/sitemap">
                                    <xsl:call-template name="sitemap-row">
                                        <xsl:with-param name="loc" select="loc"/>
                                        <xsl:with-param name="lastmod" select="lastmod"/>
                                    </xsl:call-template>
                                </xsl:for-each>
                            </tbody>
                        </table>
                    </xsl:when>

                    <!-- URL Sitemap -->
                    <xsl:otherwise>
                        <h1>XML Sitemap</h1>
                        
                        <div class="sitemap-info">
                            <p class="url-count">
                                This sitemap contains <strong><xsl:value-of select="$total-urls"/></strong> URLs.
                            </p>
                        </div>

                        <table>
                            <thead>
                                <tr>
                                    <th>URL</th>
                                    <th style="width: 100px;">Images</th>
                                    <th style="width: 180px;">Last Modified</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- URLs with namespace -->
                                <xsl:for-each select="sitemap:urlset/sitemap:url">
                                    <xsl:call-template name="url-row">
                                        <xsl:with-param name="loc" select="sitemap:loc"/>
                                        <xsl:with-param name="lastmod" select="sitemap:lastmod"/>
                                        <xsl:with-param name="links" select="xhtml:link[@rel='alternate']"/>
                                        <xsl:with-param name="images" select="image:image"/>
                                    </xsl:call-template>
                                </xsl:for-each>
                                
                                <!-- URLs without namespace (fallback) -->
                                <xsl:for-each select="urlset/url">
                                    <xsl:call-template name="url-row">
                                        <xsl:with-param name="loc" select="loc"/>
                                        <xsl:with-param name="lastmod" select="lastmod"/>
                                        <xsl:with-param name="links" select="xhtml:link[@rel='alternate']"/>
                                        <xsl:with-param name="images" select="image:image"/>
                                    </xsl:call-template>
                                </xsl:for-each>
                            </tbody>
                        </table>
                    </xsl:otherwise>
                </xsl:choose>

                <div class="credit">
                    Multilingual XML Sitemap powered by 
                    <a href="https://wplingua.com" target="_blank" rel="noopener">wpLingua</a>
                </div>
            </body>
        </html>
    </xsl:template>

    <!-- Template to display a sitemap index row -->
    <xsl:template name="sitemap-row">
        <xsl:param name="loc"/>
        <xsl:param name="lastmod"/>
        
        <tr>
            <td>
                <a href="{$loc}">
                    <xsl:value-of select="$loc"/>
                </a>
            </td>
            <td>
                <xsl:value-of select="$lastmod"/>
            </td>
        </tr>
    </xsl:template>

    <!-- Template to display a URL row -->
    <xsl:template name="url-row">
        <xsl:param name="loc"/>
        <xsl:param name="lastmod"/>
        <xsl:param name="links"/>
        <xsl:param name="images"/>
        
        <tr>
            <td>
                <a href="{$loc}">
                    <xsl:value-of select="$loc"/>
                </a>
                <xsl:if test="$links">
                    <div class="translations">
                        <xsl:for-each select="$links">
                            <span class="translation-row">
                                <span class="lang-badge">
                                    <xsl:value-of select="@hreflang"/>
                                </span>
                                <a href="{@href}">
                                    <xsl:value-of select="@href"/>
                                </a>
                            </span>
                        </xsl:for-each>
                    </div>
                </xsl:if>
            </td>
            <td>
                <xsl:variable name="image-count" select="count($images)"/>
                <xsl:if test="$image-count &gt; 0">
                    <span class="image-count">
                        <xsl:value-of select="$image-count"/>
                    </span>
                </xsl:if>
            </td>
            <td>
                <xsl:value-of select="$lastmod"/>
            </td>
        </tr>
    </xsl:template>

</xsl:stylesheet>