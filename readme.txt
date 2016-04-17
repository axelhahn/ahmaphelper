-------------------------------------------------------------------------------

ahmaphelper

map position helper (Php class)
This helper class can parse urls of a map services Google maps, Openstreetmap 
and Yandex. It fetches the position longitude and latitude from an url.

It generates urls with the same position with other map services.


Licence GPL 3.0

author: Axel Hahn
http://www.axel-hahn.de

-------------------------------------------------------------------------------

USAGE:

require_once 'ahmaphelper.class.php';
$oMaphelper = new ahmaphelper();

(1)
get a position from url:
$aPos = $oMaphelper->getPos($sUrl);

(2)
generate urls with the same position
print_r($oMaphelper->generateUrls($aPos['lat'], $aPos['lon'], $aPos['zoom']));

(3)
generate url with the same position to a specific provider
$oMaphelper->getProviders()
... returns names of known providers

echo $oMaphelper->generateUrl([provider], $aPos['lat'], $aPos['lon'], $aPos['zoom']);

