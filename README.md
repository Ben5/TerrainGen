TerrainGen
==========

A little PHP app to generate and draw a 2D map consisting of different terrain types (ocean, beach, grass, etc).

Requires the following lines in httpd.conf:

    <Directory {document root dir}>    
    RewriteEngine on
    RewriteRule /?html/(.*)/(.*) reverb/gateway/gateway_html.php?_component=$1&_action=$2 [NC,QSA]
    RewriteRule /?html/(.*) reverb/gateway/gateway_html.php?_component=$1&_action=Index [NC,QSA]
    </Directory>
