<html>
    <head>
        <title>Tester for map position helper (Php class)</title>
        <style>
            a{color: #33f;}
            body{background: #f8f8f8; color:#334; font-family: verdana, arial; font-size: 1em;}
            h1{color:#889;}
            h2{color:#99a; margin: 2em 0 0;}
            pre{background: #fff; border: 1px solid #ddd; padding: 0.5em;}
            pre strong{color:#222; background: #fee;}
            input{border: 0px solid #ccc; }
            #eUrl{border: 1px solid #ccc; color:#567; padding: 0.5em; font-size: 110%; box-shadow: 0 0 1em #ccc inset ;}
            .content{width: 1000px; margin: 1em auto 3em; padding: 1em; box-shadow: 0 0 4em #ccc; border: 1px solid #fff;}
            .output{background: #f0f0f0; border: 1px solid #ccc; padding: 1em; margin-bottom: 1em;}
            .footer{background: #f0f0f0; margin-top: 2em; padding: 0.5em; color:#aab;}

            .submit{background: #8f8; border: 1px solid; border-radius: 0.3em; padding: 0.3em;}
            .reset{background: #f88; border: 1px solid; border-radius: 0.3em; padding: 0.3em;}
        </style>
    </head>
    <body>
        <div class="content">

            <h1>Tester for map position helper (Php class)</h1>
            <ol>
                <li>This helper class can parse urls of a map services
                    Google maps, Openstreetmap and Yandex. It fetches
                    the position longitude and latitude from an url.<br>
                </li>
                <li>
                    It generates urls with the same position with other
                    map services.
                </li>
            </ol>
            <h2>Try it yourself!</h2>
            <p>
                (1) Open 
                <a href="http://maps.google.com/" target="_blank">Google maps</a> /
                <a href="http://www.openstreetmap.org" target="_blank">Open street map</a> /
                <a href="https://yandex.ru/maps/" target="_blank">Yandex map</a>,
                (2) zoom in, go to any position and then (3) copy the
                url and paste it below.
            </p>

            <?php
            $aUrls = array(
                "http://www.openstreetmap.org/#map=13/46.9384/7.4585",
                "https://www.yandex.ru/maps/99/munich/?ll=11.555422%2C48.085201&z=10",
                "https://www.google.ch/maps/@46.9457237,7.4302919,14z"
            );
            require_once 'ahmaphelper.class.php';

            // ------------------------------------------------------------------
            // FUNCTIONS
            // ------------------------------------------------------------------

            function getOutput($sUrl = false) {

                if (!$sUrl) {
                    return 'Nothing so far. Enter an url first.';
                }
                $oMaphelper = new ahmaphelper();
                $aPos = $oMaphelper->getPos($sUrl);
                $sUrlList = '';
                if ($aPos) {
                    $sUrlList.='<pre>$oMaphelper-><strong>generateUrls</strong>(' . $aPos['lat'] . ', ' . $aPos['lon'] . ', ' . $aPos['zoom'] . ')</pre>';
                    foreach ($oMaphelper->generateUrls($aPos['lat'], $aPos['lon'], $aPos['zoom']) as $sKey => $sUrl2) {
                        $sUrlList.=$sKey . ': <a href="' . $sUrl2 . '" target="_blank">' . $sUrl2 . '</a><br>';
                    }
                }
                return '<div class="output">'
                        . '<strong>URL:</strong> <a href="' . $sUrl . '" target="_blank">' . $sUrl . '</a>'
                        . '<div style="margin-left: 2em;">'
                            . '<br>Get the position:'
                            . '<pre>$oMaphelper-><strong>getPos</strong>("' . $sUrl . '")<br>' . print_r($aPos, 1) . '</pre><br>'
                            . ($aPos ? 'Urls that point to the same position:<br>' . $sUrlList . '<br>' : 'ERROR: No position was detected. The url cannot be parsed.' )
                        . '</div>'
                        . '</div>'
                ;
            }

            // ------------------------------------------------------------------
            // OUTPUT
            // ------------------------------------------------------------------

            $sUrl = array_key_exists('url', $_GET) && $_GET['url'] ? strip_tags($_GET['url']) : '';

            echo ''
            . '<form method="GET" action="?">
                <label for="eUrl">Paste your url here:</label><br>
                <input type="text" id="eUrl" name="url" size="100" value="' . $sUrl . '" />
                <input class="submit" type="submit" value="Go">'
                    . ($sUrl ? ' <input type="button" class="reset" onclick="location.href=\'?\';" value=" x "/>' : '')
                    . '
              </form>
    
              <h2>Output</h2>
                  ' . getOutput($sUrl) . '
              <h2>Examples</h2>
              <p>Click one :-)</p>
            ';
            foreach ($aUrls as $sUrl2) {
                echo '<a href="?url=' . urlencode($sUrl2) . '">' . $sUrl2 . '</a><br>';
                // echo getOutput($sUrl);
            }
            ?>

            <div class="footer">
                www.axel-hahn.de
            </div>
        </div>
    </body></html>
