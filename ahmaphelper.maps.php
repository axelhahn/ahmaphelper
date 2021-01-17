<?php
/**
 * this file is included in ahmaphelper.class.php
 * TODO: 
 * https://www.qwant.com/maps/#map=10.45/52.7857268/13.2371361
 * 
 * pattern definitions for map websites to parse its url and fetch
 * longitude and latitude from it.
 * - keys are the names of service provider.
 * - subkeys are
 *   - regex - the regex with 3 hits
 *   - lat,lon,zoom - number of position in regex (1..3)
 *   - url - url template with placeholders [lat], [lon], [zoom]
 *   - defaultzoom - default zoom level
 *   - limitarea - array of regions with top left and bottom right position of a regional map
 * @var array
 */
return array(
    /*
      GOOGLE:
      https://www.google.com/maps/@47.258818,11.4208611,14z
      https://www.google.com/maps/place/Tivoli+Stadion+Tirol,+6020+Innsbruck/@47.2572743,11.4191015,15z/data=!4m2!3m1!1s0x479d69432145b01f:0x8c24f2d1161f1c90
     */
    'google' => array(
        'regex' => '#//www\.google\..*/maps/.*@([\-0-9\.]*)\,([\-0-9\.]*),([0-9\.]*)z#',
        'lat' => 1,
        'lon' => 2,
        'zoom' => 3,
        'url' => 'https://www.google.com/maps/@[lat],[lon],[zoom]z',
        'defaultzoom' => 11,
        'maxzoom' => 21,
        'zoomtype' => 'float',
    ),
    /*
      Map1 EU - Touristic Map of Europe
      http://beta.map1.eu/#zoom=11&lat=46.94827&lon=7.45145&layers=BT
     */
    'map1eu' => array(
        'regex' => '#//beta\.map1\.eu/\#zoom=([\0-9\.]*)&lat=([\-0-9\.]*)&lon=([\-0-9\.]*)#',
        'lat' => 2,
        'lon' => 3,
        'zoom' => 1,
        'url' => 'http://beta.map1.eu/#zoom=[zoom]&lat=[lat]&lon=[lon]&layers=BT',
        'defaultzoom' => 11,
        'maxzoom' => 17,
        'zoomtype' => 'int',
        'limitarea' => array(
            array(75, -9, 31, 43),
        ),
    ),
    /*
      Mapillary
      https://www.mapillary.com/app/?lat=46.9448304129896&lng=7.437269493180992&z=15.68258779448344
     */
    'mapillary' => array(
        'regex' => '#//www\.mapillary\.com/app/\?lat=([\0-9\.]*)&lng=([\-0-9\.]*)&z=([\-0-9\.]*)#',
        'lat' => 1,
        'lon' => 2,
        'zoom' => 3,
        'url' => 'https://www.mapillary.com/app/?lat=[lat]&lng=[lon]&z=[zoom]',
        'defaultzoom' => 11,
        'maxzoom' => 20,
        'zoomtype' => 'float',
    ),
    /*
      Flightradar24
      https://www.flightradar24.com/46.95,7.45/11
     */
    'fligthtradar24' => array(
        'regex' => '#//www\.flightradar24\.com/([\-0-9\.]*),([\-0-9\.]*)/([0-9]*)#',
        'lat' => 1,
        'lon' => 2,
        'zoom' => 3,
        'url' => 'https://www.flightradar24.com/[lat],[lon]/[zoom]',
        'defaultzoom' => 11,
        'maxzoom' => 21,
        'zoomtype' => 'int',
    ),
    /*
      Mappy
      https://en.mappy.com/#/16/M2/THome/N0,0,7.4553,46.95049/Z17/

      'mappy' => array(
      'regex' => '#//.*mappy\.com/\#.*,([\-0-9\.]*)\,([\-0-9\.]*)/Z([0-9\.]*).*#',
      'lat' => 1,
      'lon' => 2,
      'zoom' => 3,
      'url' => 'https://en.mappy.com/#N0,0,[lon],[lat]/Z[zoom]/',
      'defaultzoom' => 11,
      'maxzoom' => 19,
      'zoomtype' => 'int',

      // CHECK: you cannot access all points by given url
      'limitarea'=> array(
      array(66,-24, ??, ??),
      ),
      ),
     */

    /*
      OSM:
      http://www.openstreetmap.org/#map=13/46.9545/7.4693
     */
    'osm' => array(
        'regex' => '#//www\.openstreetmap\.org.*\#map=([0-9]*)/([\-0-9\.]*)/([\-0-9\.]*)#',
        'lat' => 2,
        'lon' => 3,
        'zoom' => 1,
        'url' => 'https://www.openstreetmap.org/#map=[zoom]/[lat]/[lon]',
        'defaultzoom' => 11,
        'maxzoom' => 19,
        'zoomtype' => 'int',
    ),
    /*
      Wikimapia
      http://wikimapia.org/#lang=en&lat=46.947135&lon=7.447250&z=16
     */
    'wikimapia' => array(
        'regex' => '#//wikimapia\.org/\#.*lat=([\-0-9\.]*)&lon=([\-0-9\.]*)&z=([0-9\.]*)#',
        'lat' => 1,
        'lon' => 2,
        'zoom' => 3,
        'url' => 'http://wikimapia.org/#lang=en&lat=[lat]&lon=[lon]&z=[zoom]&m=b',
        'defaultzoom' => 11,
        'maxzoom' => 20,
        'zoomtype' => 'int',
    ),
    /*
      Windy
      https://www.windy.com/?46.377,10.415,7
     */
    'windy' => array(
        'regex' => '#//www\.windy\.com/\?([\-0-9\.]*),([\-0-9\.]*),([0-9]*)#',
        'lat' => 1,
        'lon' => 2,
        'zoom' => 3,
        'url' => 'https://windy.com/?[lat],[lon],[zoom]',
        'defaultzoom' => 11,
        'maxzoom' => 17,
        'zoomtype' => 'int',
    ),
	
    /*
      Yandex:
      https://yandex.ru/maps/10513/bern/?ll=7.444947%2C46.943538&z=15
      https://yandex.ru/maps/?ll=7.444947%2C46.943538&z=15
     */
    'yandex' => array(
        'regex' => '#yandex\.ru/maps/.*?.*ll=([\-0-9\.]*)\%2C([\-0-9\.]*)&z=([0-9\.]*)#',
        'lat' => 2,
        'lon' => 1,
        'zoom' => 3,
        'url' => 'https://yandex.ru/maps/?ll=[lon]%2C[lat]&z=[zoom]',
        'defaultzoom' => 11,
        'maxzoom' => 18,
        'zoomtype' => 'float',
    ),
);
