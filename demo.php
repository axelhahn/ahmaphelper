<?php
$sTitle = "Tester for map position helper (Php class)";
$aUrls = array(
    "http://www.openstreetmap.org/#map=13/46.9384/7.4585",
    "https://www.yandex.ru/maps/99/munich/?ll=11.555422%2C48.085201&z=10",
    "https://www.google.ch/maps/@46.9457237,7.4302919,14z",
    "https://www.google.com.br/maps/@-22.7405789,-47.3313609,20.25z",
);
require_once 'ahmaphelper.class.php';

// ------------------------------------------------------------------
// FUNCTIONS
// ------------------------------------------------------------------

function getOutput($sUrl = false) {

    if (!$sUrl) {
        return '';
    }
    $oMaphelper = new ahmaphelper();
    $aPos = $oMaphelper->getPos($sUrl);
    $sUrlList = '';
    if ($aPos) {


        $sUrlList.='
                <pre>$oMaphelper-><strong>getUrls</strong>();<br>'
                . print_r($oMaphelper->getUrls(), 1)
                . '</pre>';

        $aFixed = $oMaphelper->fixPosition();
        if (array_key_exists('_orig', $aFixed)) {
            $sUrlList.='<h3>Fix position values</h3>'
                    . 'What you see in the resultset above is: we did not get links to all providers.<br><br>'
                    . 'BUT: There is a '
                    . 'healing function that creates compatible values. '
                    . 'The key _warnings contains info messages, '
                    . 'what was fixed.<br>'
                    . '<pre>$oMaphelper-><strong>fixPosition</strong>();<br>'
                    . print_r($aFixed, 1)
                    . '</pre>'
            ;
            $sUrlList.='<br>'
                    . 'The values were fixed. Let\'s re-run the method getUrls:'
                    . '<pre>$oMaphelper-><strong>getUrls</strong>();<br>'
                    . print_r($oMaphelper->getUrls(), 1)
                    . '</pre>';
        } else {
            $sUrlList.=''
                    . 'OK, we got links of all providers. If not: you should know that there is a '
                    . 'healing function that creates compatible values. '
                    . '<pre>$oMaphelper-><strong>fixPosition</strong>();</pre>'
            ;
        }
        $sUrlList.='<h3>link generation with position data</h3>'
                . '<p>'
                . 'With latitude, longitude and zoom you can generate links too.<br>'
                . 'To get links to all providers:'
                . '</p>'
                . '<pre>$oMaphelper-><strong>generateUrls</strong>(' . $aPos['lat'] . ', ' . $aPos['lon'] . ', ' . $aPos['zoom'] . ')<br>'
                . print_r($oMaphelper->generateUrls($aPos['lat'], $aPos['lon'], $aPos['zoom']), 1)
                . '</pre>'
                . '<p>'
                . '... or just one provider<br>'
                . '</p>'
                . '<pre>$oMaphelper-><strong>generateUrl</strong>("google", ' . $aPos['lat'] . ', ' . $aPos['lon'] . ', ' . $aPos['zoom'] . ')<br>'
                . print_r($oMaphelper->generateUrl("google", $aPos['lat'], $aPos['lon'], $aPos['zoom']), 1)
                . '</pre>'
                . ''
        ;
    }
    return '<h2>Output</h2><div class="output">'
            . '<strong>URL:</strong> <a href="' . $sUrl . '" target="_blank">' . $sUrl . '</a>'
            . '<div style="margin-left: 2em;">'
            . '<br><h3>Get the position</h3>'
            . '<pre>$oMaphelper-><strong>getPos</strong>("' . $sUrl . '")<br>' . print_r($aPos, 1) . '</pre><br>'
            . ($aPos ? '<h3>Urls that point to the same position</h3>' . $sUrlList . '<br>' : 'ERROR: No position was detected. The url cannot be parsed.' )
            . '</div>'
            . '</div>'
    ;
}
?>	
<!DOCTYPE HTML>
<html>
    <head>
        <title><?php echo $sTitle; ?></title>
        <style>
            a{color: #33f;}
            body{background: #f8f8f8; color:#334; font-family: verdana, arial; font-size: 1em;}
            h1{color:#889; font-size: 200%;}
            h2{color:#99a; margin: 1em 0 0;font-size: 300%;}
            h3{color:#aab; margin: 0;font-size: 200%;}
            pre{background: #fff; border: 1px solid #ddd; padding: 0.5em;}
            pre strong{color:#222; background: #def;}
            input{border: 0px solid #ccc; }
            ol>li{
                list-style: none;
                counter-increment: item;                
                margin-bottom: 1em;
            }
            ol>li::before{
                content: counter(item);
                font-size: 200%;
                border: 0px solid #555; border-radius: 50%;
                background: #99a;
                color:#dde;
                margin: 0 0.5em 0.5em 0em;
                padding: 0 0.3em;
            }
            #eUrl{border: 1px solid #ccc; color:#567; padding: 0.5em; font-size: 110%; box-shadow: 0 0 1em #ccc inset ;}
            .content{width: 1000px; margin: 1em auto 3em; padding: 1em; box-shadow: 0 0 4em #ccc; border: 1px solid #fff;}
            .infobox{border: 2px dotted #ccd; padding: 1em; background: #f0f4f8; color:#889;}
            .output{background: #f0f0f0; border: 1px solid #ccc; padding: 1em; margin-bottom: 1em;}
            .footer{background: #f0f0f0; margin-top: 2em; padding: 0.5em; color:#aab;}
            .submit, .reset{
                font-size: 130%;padding: 0.4em;border: 1px solid rgba(0,0,0,0.3); border-radius: 0.3em; 
                color: rgba(0,0,0,0.5); 
            }
            .submit{background: #8c8; }
            .reset{background: #f88; padding-left: 0.6em; padding-right: 0.6em;}
        </style>
    </head>
    <body>
        <div class="content">
            <div class="infobox">
                <h1>Tester for map position helper (Php class)</h1>
                <p>
                    This helper class can parse urls of a map services
                    Google maps, Openstreetmap and Yandex. It fetches
                    the position longitude and latitude from an url.<br>
                    <br>
                    It generates urls with the same position with other
                    map services.
                </p>
            </div>
            <h2>Try it yourself!</h2>
            <ol>
                <li>Open 
                    <a href="http://maps.google.com/" target="_blank">Google maps</a> or
                    <a href="http://www.openstreetmap.org" target="_blank">Open street map</a> or
                    <a href="https://yandex.ru/maps/" target="_blank">Yandex map</a>,</li>
                <li>Zoom in, go to any position and then</li>
                <li>Copy the url and paste it below.</li>
            </ol>

            <?php
            // ------------------------------------------------------------------
            // OUTPUT
            // ------------------------------------------------------------------

            $sUrl = array_key_exists('url', $_GET) && $_GET['url'] ? strip_tags($_GET['url']) : '';

            echo ''
            . '<form method="GET" action="?" style="margin-left: 6em;">
                <!--<label for="eUrl">Paste your url here:</label><br>-->
                <input type="text" id="eUrl" name="url" size="90" value="' . $sUrl . '" />
                <input class="submit" type="submit" value="Go">'
            . ($sUrl ? ' <input type="button" class="reset" onclick="location.href=\'?\';" value=" x "/>' : '')
            . '
              </form>
    
            ' . getOutput($sUrl) . '
              <h2>Examples</h2>
              <p>Click one :-)<br>
            ';
            foreach ($aUrls as $sUrl2) {
                echo '<a href="?url=' . urlencode($sUrl2) . '">' . $sUrl2 . '</a><br>';
                // echo getOutput($sUrl);
            }
            ?>

            <br>
            <strong>Ressources</strong><br>
            Source: <a href="https://github.com/axelhahn/ahmaphelper">Github</a>
            /
            Author: Axel Hahn (<a href="http://www.axel-hahn.de/">www.axel-hahn.de</a>)
        </p>
            <div class="footer">
                www.axel-hahn.de
            </div>
    </div>
</body></html>
