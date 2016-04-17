<?php

/**
 * AHMAPHELPER<br>
 * <br>
 * helper class to parse positions from a map url and generates links<br>
 * to the same position with different providers<br>
 * <br>
 * THERE IS NO WARRANTY FOR THE PROGRAM, TO THE EXTENT PERMITTED BY APPLICABLE <br>
 * LAW. EXCEPT WHEN OTHERWISE STATED IN WRITING THE COPYRIGHT HOLDERS AND/OR <br>
 * OTHER PARTIES PROVIDE THE PROGRAM ?AS IS? WITHOUT WARRANTY OF ANY KIND, <br>
 * EITHER EXPRESSED OR IMPLIED, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED <br>
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE. THE <br>
 * ENTIRE RISK AS TO THE QUALITY AND PERFORMANCE OF THE PROGRAM IS WITH YOU. <br>
 * SHOULD THE PROGRAM PROVE DEFECTIVE, YOU ASSUME THE COST OF ALL NECESSARY <br>
 * SERVICING, REPAIR OR CORRECTION.<br>
 * <br>
 * --------------------------------------------------------------------------------<br>
 * <br>
 * --- HISTORY:<br>
 * 2016-04-17  1.0  first public release<br>
 * <br>
 * @author Axel Hahn
 * @license GPL
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 */
class ahmaphelper {
    // ----------------------------------------------------------------------
    // CONFIG
    // ----------------------------------------------------------------------

    /**
     * pattern definitions for map websites to parse its url and fetch
     * longitude and latitude from it.
     * - keys are the names of service provider.
     * - subkeys are
     *   - regex - the regex with 3 hits
     *   - lat,lon,zoom - number of position in regex (1..3)
     *   - url - url template with placeholders [lat], [lon], [zoom]
     *   - defaultzoom - default zoom level
     * @var array
     */
    protected $_aPatterns = array(
        /*
          GOOGLE:
          https://www.google.com/maps/@47.258818,11.4208611,14z
          https://www.google.com/maps/place/Tivoli+Stadion+Tirol,+6020+Innsbruck/@47.2572743,11.4191015,15z/data=!4m2!3m1!1s0x479d69432145b01f:0x8c24f2d1161f1c90
         */
        'google' => array(
            'regex' => '#//www\.google\..*/maps/.*@([\-0-9\.]*)\,([\-0-9\.]*),([0-9]*)z#',
            'lat' => 1,
            'lon' => 2,
            'zoom' => 3,
            'url' => 'https://www.google.com/maps/@[lat],[lon],[zoom]z',
            'defaultzoom' => 11,
        ),
        /*
          OSM:
          http://www.openstreetmap.org/#map=13/46.9545/7.4693
         */
        'osm' => array(
            'regex' => '#//www\.openstreetmap\.org.*\#map=([0-9]*)/([\-0-9\.]*)/([\-0-9\.]*)#',
            'lat' => 2,
            'lon' => 3,
            'zoom' => 1,
            'url' => 'http://www.openstreetmap.org/#map=[zoom]/[lat]/[lon]',
            'defaultzoom' => 11,
        ),
        /*
          Yandex:
          https://yandex.ru/maps/10513/bern/?ll=7.444947%2C46.943538&z=15
          https://yandex.ru/maps/?ll=7.444947%2C46.943538&z=15
         */
        'yandex' => array(
            'regex' => '#yandex\.ru/maps/.*?.*ll=([\-0-9\.]*)\%2C([\-0-9\.]*)&z=([0-9]*)#',
            'lat' => 2,
            'lon' => 1,
            'zoom' => 3,
            'url' => 'https://yandex.ru/maps/?ll=[lon]%2C[lat]&z=[zoom]',
            'defaultzoom' => 11,
        ),
    );

    // ----------------------------------------------------------------------
    // METHODS
    // ----------------------------------------------------------------------

    /**
     * constructor
     * @return bool
     */
    public function __construct() {
        return true;
    }

    /**
     * get a position by parsing a url. It returns an array with the keys
     * - source - given url
     * - provider - name of the provider that matches the url
     * - lat, lon - position
     * - zoom - zoom level
     * It returns false if an url was not detected as a map url of any of the
     * known map providers
     * 
     * @param string $sUrl
     * @return array
     */
    public function getPos($sUrl) {
        $aPosition = false;
        // echo "$sUrl<br>";
        foreach ($this->_aPatterns as $sKey => $aTmp) {
            // echo "regex: " . $aTmp['regex'] . "<br>";
            preg_match_all($aTmp['regex'], $sUrl, $aMatches);
            if (count($aMatches) >= 2 && count($aMatches[2])) {
                // echo "$sKey <pre>".print_r($aMatches, 1)."</pre>";
                $aPosition = array(
                    'source' => $sUrl,
                    'provider' => $sKey,
                    'lat' => $aMatches[$aTmp['lat']][0],
                    'lon' => $aMatches[$aTmp['lon']][0],
                    'zoom' => $aMatches[$aTmp['zoom']][0],
                );
                break;
            }
        }
        return $aPosition;
    }

    /**
     * get a flat array for a list of known map providers
     * @return array
     */
    public function getProviders() {
        return array_keys($this->_aPatterns);
    }

    /**
     * generate an url to a map with a given position
     * @see getProviders() to get a list of known providers
     * 
     * @param string  $sProvider  provider of a map website
     * @param float   $lat        position - latitude
     * @param float   $lon        position - longitude
     * @param integer $zoomlevel  zoomlevel
     * @return string
     */
    public function generateUrl($sProvider, $lat = 0, $lon = 0, $zoomlevel = false) {
        if (!array_key_exists($sProvider, $this->_aPatterns)) {
            echo 'WARNING: provider [' . $sProvider . '] does not exist in ' . __CLASS__ . '.';
            return false;
        }
        $fLat = (float) $lat;
        $fLon = (float) $lon;
        if ($fLat === false || $fLon === false) {
            echo "WARNING: position has wrong format lat = $lat, lon = $lon.";
            return false;
        }
        if (!$zoomlevel) {
            $iZoom = $this->_aPatterns[$sProvider]['zoom'];
        }
        $iZoom = (int) $zoomlevel;
        if (!$iZoom) {
            echo "WARNING: set a zoom level.";
            return false;
        }
        $sReturn = str_replace(
                array("[lat]", "[lon]", "[zoom]"), array($fLat, $fLon, $iZoom), $this->_aPatterns[$sProvider]['url']
        );

        return $sReturn;
    }

    /**
     * get a list with links to a given position position with all map providers
     * @param float   $lat        position - latitude
     * @param float   $lon        position - longitude
     * @param integer $zoomlevel  zoomlevel
     * @return array
     */
    public function generateUrls($lat = 0, $lon = 0, $zoomlevel = false) {
        $aReturn = array();
        foreach ($this->getProviders() as $sProvider) {
            $aReturn[$sProvider] = $this->generateUrl($sProvider, $lat, $lon, $zoomlevel);
        }
        return $aReturn;
    }

}
