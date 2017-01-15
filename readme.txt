-------------------------------------------------------------------------------

ahmaphelper

map position helper (Php class)
This helper class can parse urls of a map services Google maps, Openstreetmap 
and Yandex. It fetches the position longitude and latitude from an url.

It generates urls with the same position with other map services.


Licence GPL 3.0

author: Axel Hahn
https://www.axel-hahn.de

see DEMO
https://www.axel-hahn.de/demos/maphelper/

DOCS
https://www.axel-hahn.de/docs/ahmaphelper/index.htm

-------------------------------------------------------------------------------

2016-04-17  1.0  first public release
2016-05-25  1.1  added options minzoom; new methods getUrls() + fixPosition()
2017-01-15  1.2  added  support for regional maps (provider map1eu)

-------------------------------------------------------------------------------

USAGE:

require_once 'ahmaphelper.class.php';
$oMaphelper = new ahmaphelper();

(1)
get a position from url:
go to a map provider:
  https://www.google.ch/maps
  http://www.openstreetmap.org/
  https://yandex.ru/maps/
Zoom in, go to any position and copy the url

$aPos = $oMaphelper->getPos($sUrl);

(2)
generate urls with the same position
print_r($oMaphelper->getUrls());

(3)
fix position data to be compatible to all map providers. 
print_r($oMaphelper->fixPosition());
print_r($oMaphelper->getUrls());

(4)
manually generate links with position data and zoom level (returns an array):
print_r($oMaphelper->generateUrls($aPos['lat'], $aPos['lon'], $aPos['zoom']));

(5)
manually generate link with position data and zoom level to a given provider 
(returns a string):
echo $oMaphelper->generateUrl([provider], $aPos['lat'], $aPos['lon'], $aPos['zoom']);
echo $oMaphelper->generateUrls("google", $aPos['lat'], $aPos['lon'], $aPos['zoom']);

(6)
helper functions:

  generate url with the same position to a specific provider
  $oMaphelper->getProviders()
  ... returns names of known providers

  $oMaphelper->getMinZoom()
  ... returns lowest maxzoom level of all providers

-------------------------------------------------------------------------------
