<?php

/**
 * AHMAPHELPER<br>
 * https://github.com/axelhahn/ahmaphelper<br>
 * <br>
 * helper class to parse positions from a map url and generates links<br>
 * to the same position with different providers<br>
 * <br>
 * THERE IS NO WARRANTY FOR THE PROGRAM, TO THE EXTENT PERMITTED BY APPLICABLE <br>
 * LAW. EXCEPT WHEN OTHERWISE STATED IN WRITING THE COPYRIGHT HOLDERS AND/OR <br>
 * OTHER PARTIES PROVIDE THE PROGRAM "AS IS" WITHOUT WARRANTY OF ANY KIND, <br>
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
 * 2016-05-25  1.1  added options minzoom; new methods getUrls() + fixPosition()<br>
 * 2017-01-15  1.2  added  support for regional maps (provider map1eu)<br>
 * <br>
 * @author Axel Hahn
 * @license GPL
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 */
class ahmaphelper {
    // ----------------------------------------------------------------------
    // CONFIG
    // ----------------------------------------------------------------------

    protected $_aPatterns = false;
    protected $_aPosition = false;

    // ----------------------------------------------------------------------
    // METHODS
    // ----------------------------------------------------------------------

    /**
     * constructor
     * @return bool
     */
    public function __construct() {
        $this->_aPatterns = include('ahmaphelper.maps.php');
        if (!$this->_aPatterns) {
            echo 'ERROR: file [ahmaphelper.maps.php] was not found.';
            return false;
        }
        return true;
    }

    // ----------------------------------------------------------------------
    // PRIVATE
    // ----------------------------------------------------------------------

    /**
     * helper function for generateUrl(): check parameters for position and zoom
     * it returns true/ false
     * @see getProviders() to get a list of known providers
     * 
     * @param string  $sProvider  provider of a map website
     * @param float   $lat        position - latitude
     * @param float   $lon        position - longitude
     * @param float   $zoomlevel  zoomlevel
     * @return string
     */
    private function _isValidPositionSet($sProvider, $lat, $lon, $zoomlevel) {
        if (!array_key_exists($sProvider, $this->_aPatterns)) {
            echo 'WARNING: provider [' . $sProvider . '] does not exist in ' . __CLASS__ . '.';
            return false;
        }
        if ($lat === false || $lon === false || $zoomlevel === false) {
            return false;
        }
        return true;
    }

    /**
     * helper function for generateUrl(): check parameters if they fit strict
     * requirements for a provider.
     * it returns true/ false
     * @see getProviders() to get a list of known providers
     * 
     * @param string  $sProvider  provider of a map website
     * @param float   $lat        position - latitude
     * @param float   $lon        position - longitude
     * @param float   $zoomlevel  zoomlevel
     * @return string
     */
    private function _isStrictPositionSet($sProvider, $lat, $lon, $zoomlevel) {
        if (!$this->_isValidPositionSet($sProvider, $lat, $lon, $zoomlevel)) {
            return false;
        }
        if ($this->_aPatterns[$sProvider]['zoomtype'] === 'int' && $zoomlevel != (int) $zoomlevel) {
            // echo "$sProvider requires an integer zoom level - $zoomlevel<br>";
            return false;
        }
        if ($this->_aPatterns[$sProvider]['maxzoom'] < $zoomlevel) {
            // echo "max zoom for $sProvider is ".$this->_aPatterns[$sProvider]['maxzoom']."<br>";
            return false;
        }

        // check position in a regional map
        if (array_key_exists('limitarea', $this->_aPatterns[$sProvider]) && is_array($this->_aPatterns[$sProvider]['limitarea'])) {
            foreach ($this->_aPatterns[$sProvider]['limitarea'] as $aArea) {
                if ($lat > $aArea[0] || $lat < $aArea[2] || $lon < $aArea[1] || $lon > $aArea[3]) {
                    return false;
                }
            }
        }

        return true;
    }

    // ----------------------------------------------------------------------
    // PUBLIC FUNCTIONS
    // ----------------------------------------------------------------------

    /**
     * fix positition data to compatible values that match all providers
     * @return type
     */
    public function fixPosition() {
        if (!$this->_aPosition || array_key_exists('_orig', $this->_aPosition)) {
            return $this->_aPosition;
        }

        $this->_aPosition['_warnings'] = array();
        $this->_aPosition['_orig'] = array();

        // check: zoomlevel is integer?
        if ($this->_aPosition['zoom'] - (int) $this->_aPosition['zoom'] !== 0) {
            $this->_aPosition['_orig']['zoom'] = (array_key_exists('zoom', $this->_aPosition['_orig']) ? $this->_aPosition['_orig']['zoom'] : $this->_aPosition['zoom']);
            $this->_aPosition['_warnings'][] = 'zoom level is not integer.';
            $this->_aPosition['zoom'] = (int) $this->_aPosition['zoom'];
        }
        // check: zoomlevel exceeds maximum?
        $iMinZoom = $this->getMinZoom();
        if ($this->_aPosition['zoom'] > $iMinZoom) {
            $this->_aPosition['_orig']['zoom'] = (array_key_exists('zoom', $this->_aPosition['_orig']) ? $this->_aPosition['_orig']['zoom'] : $this->_aPosition['zoom']);
            $this->_aPosition['_warnings'][] = 'zoom level ' . $this->_aPosition['_orig']['zoom'] . ' is too large; maximum is ' . $iMinZoom . '.';
            $this->_aPosition['zoom'] = $iMinZoom;
        }

        // cleanup unneeded keys
        if (!count($this->_aPosition['_warnings'])) {
            unset($this->_aPosition['_warnings']);
            unset($this->_aPosition['_orig']);
        }
        return $this->_aPosition;
    }

    /**
     * get minimal zoom level of all providers
     * @return float or int
     */
    public function getMinZoom() {
        $iReturn = false;
        foreach ($this->getProviders() as $sProvider) {
            $iReturn = $iReturn ? min(array($iReturn, $this->_aPatterns[$sProvider]['maxzoom'])) : $this->_aPatterns[$sProvider]['maxzoom']
            ;
        }
        return $iReturn;
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
        $this->_aPosition = false;
        foreach ($this->_aPatterns as $sKey => $aTmp) {
            preg_match_all($aTmp['regex'], $sUrl, $aMatches);
            if (count($aMatches) >= 2 && count($aMatches[2])) {
                $this->_aPosition = array(
                    'source' => $sUrl,
                    'provider' => $sKey,
                    'lat' => $aMatches[$aTmp['lat']][0],
                    'lon' => $aMatches[$aTmp['lon']][0],
                    'zoom' => $aMatches[$aTmp['zoom']][0],
                );
                break;
            }
        }
        return $this->_aPosition;
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
     * @param float   $zoomlevel  zoomlevel
     * @return string
     */
    public function generateUrl($sProvider, $lat = 0, $lon = 0, $zoomlevel = false) {
        $fLat = (float) $lat;
        $fLon = (float) $lon;
        if (!$zoomlevel) {
            $iZoom = $this->_aPatterns[$sProvider]['zoom'];
        } else {
            $iZoom = (float) $zoomlevel;
        }
        if (!$this->_isValidPositionSet($sProvider, $lat, $lon, $iZoom)) {
            return false;
        }
        if (!$this->_isStrictPositionSet($sProvider, $lat, $lon, $iZoom)) {
            return false;
        }
        $sReturn = str_replace(
                array("[lat]", "[lon]", "[zoom]"), array($fLat, $fLon, $iZoom), $this->_aPatterns[$sProvider]['url']
        );

        return $sReturn;
    }

    /**
     * get a list with links to a given position position with all map providers
     * 
     * @param float   $lat        position - latitude
     * @param float   $lon        position - longitude
     * @param float   $zoomlevel  zoomlevel
     * @return array
     */
    public function generateUrls($lat = 0, $lon = 0, $zoomlevel = false) {
        $aReturn = array();
        foreach ($this->getProviders() as $sProvider) {
            $aReturn[$sProvider] = $this->generateUrl($sProvider, $lat, $lon, $zoomlevel);
        }
        return $aReturn;
    }

    /**
     * get a list with links to a given position position with all map providers
     * call this function after method getPos() that fetches the position from
     * an url
     * @see getPos()
     * 
     * @return array
     */
    public function getUrls() {
        $aReturn = array();
        if (!$this->_aPosition) {
            return false;
        }
        foreach ($this->getProviders() as $sProvider) {
            $aReturn[$sProvider] = $this->generateUrl(
                $sProvider, $this->_aPosition['lat'], $this->_aPosition['lon'], $this->_aPosition['zoom']
            );
        }

        return $aReturn;
    }

}
