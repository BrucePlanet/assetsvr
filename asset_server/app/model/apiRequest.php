<?php
/**
 * Created by PhpStorm.
 * User: bruce.tomalin
 * Date: 01/04/2019
 * Time: 16:48
 */

function apiRequest($strGetURL)
{
    // this is where the API request will be processed

//******************************* Curl request to API ************************************//

    $options = array(
        CURLOPT_RETURNTRANSFER => true,   // return web page
        CURLOPT_HEADER => false,  // don't return headers
        CURLOPT_FOLLOWLOCATION => true,   // follow redirects
        CURLOPT_MAXREDIRS => 10,     // stop after 10 redirects
        CURLOPT_ENCODING => "",     // handle compressed
        CURLOPT_AUTOREFERER => true,   // set referrer on redirect
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_SSL_VERIFYHOST => 0
    );

    $ch = curl_init($strGetURL);
    curl_setopt_array($ch, $options);
    $resCurlHandle = curl_exec($ch);
    $arrResult = json_decode($resCurlHandle, true);
    curl_close($ch);

    if ($arrResult['http_code'] == 200) {
        error_log("API has been called get an image", 0);
        return $arrResult;

    } else {

        // No Matching asset id
        $requestURL = ($_SERVER['HTTP_HOST'] . $arrResult);
        // Unable to find specified asset. 404 Them.
        $strErrTxt = 'no-image-available';
        showErrorImg($strErrTxt);

    }

    return $arrResult;
}