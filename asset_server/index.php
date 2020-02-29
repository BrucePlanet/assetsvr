<?php
/**
 * Created by PhpStorm.
 * User: bruce.tomalin
 * Date: 01/04/2019
 * Time: 15:56
 */

/**
 * Description:
 * Based on the live asset server this is an adaptation to be used in conjunction with the
 * Featherlite project.
 * This employs the use of an API call as opposed to a DB call
 *
 * @author: Bruce.Tomalin
 * @version: 3.0
 *
 * Notes on URL types handled by this code.
 * Imgs URL Example
 * example http://fl-assetsvr.timico.develop/4713392756955/5c829f716445290006365057/300/image.jpg
 * Breakdown of each element
 * URL = http://fl-assetsvr.timico.develop
 * EAN = /4713392756955/
 * Product Id = /5c829f716445290006365057/
 * Image Size = /300/
 * Name - /image.jpg
 * RewriteRule ^(imgs)/([0-9]+)/([0-9]+)/(.*)/([A-Za-z]+)\.([A-Za-z]+)$ index.php?assettype=$1&wmid=$2&assetid=$3&imgsize=$4&format=$6 [L,NC]
 *
 * Images  from Product ID URL Example
 * example http://fl-assetsvr.timico.develop/prodimg/5/12496/300/image.jpg
 * Breakdown of each element
 * URL = http://fl-assetsvr.timico.develop
 * Request Type = /prodimg/
 * Channel = /5/
 * Asset Id = /75288/
 * Image Size = /300/
 * Name - /image.jpg
 * RewriteRule ^(prodimg)/([A-Za-z0-9]+)/([0-9]+)/([0-9]+).([A-Za-z]+)$ index.php?assettype=$1&channel=$2&sourceid=$3&imgsize=$4&format=$5 [L,NC]
 *
 * YouTube (Embed) URL Example
 * example http://fl-assetsvr.timico.develop/embed/34/32344/300x300
 * RewriteRule ^(embed)/([0-9]+)/(.*)/$ index.php?assettype=$1&assetid=$2&dimensions=$3 [L,NC]
 *
 * YouTube (Link Only) URL Example
 * example http://fl-assetsvr.timico.develop/youtube/34/32344/300x300
 * RewriteRule ^(youtube)/([0-9]+)/(.*)/$ index.php?assettype=$1&assetid=$2&dimensions=$3 [L,NC]
 *
 * Document - URL Example
 * example http://fl-assetsvr.timico.develop/doc/5/54443/document.pdf
 * RewriteRule ^(doc)/([A-Za-z0-9]+)/([0-9]+).([A-Za-z]+)$ asset_svr_friendly.php?assettype=$1&channel=$2&assetid=$3&format=$4 [L,NC]
 */



// Get the app config settings
require("app/config/config.php");

// Get the image via the api request
require("app/model/apiRequest.php");

// Process the image
require("app/model/imageProcessor.php");

// Return error image
require("app/model/showErrorImg.php");

// Return error image
require("app/model/cacheAsset.php");

// Return error image
require("app/model/checkCache.php");

$strUrl = (!empty($_SERVER["REQUEST_URI"])) ? $_SERVER["REQUEST_URI"] : HTTP_PROTOCOL . $_SERVER['HTTP_HOST'];

// Remove all illegal characters from a url
$sanitized_url = filter_var($strUrl, FILTER_SANITIZE_URL);

$strUrl = $sanitized_url;

// Explode the URL request into parts
$arrURLparts = explode('/', $strUrl);

$urlCount = count($arrURLparts);

// Give each element and name
$strEAN = $arrURLparts[1];
$strProdID = $arrURLparts[2];
$strImageSize = $arrURLparts[3];
$arrResult['date_created'] = date("Y-m-d H:i:s");
$arrAssetInfo['channel'] = 'image';
$arrResult['wmid'] = 3;

/* Extract the image type tag from the URL */
$arrFormat = explode('.', $arrURLparts[4]);
$strFormat = substr($arrFormat[1], 0, 3) . '';

// Check the request has content
if (empty($strUrl)) {
    $requestURL = ($_SERVER['HTTP_HOST'] . $strUrl);
    // Unable to find specified asset. 404 Them.
    $strErrTxt = 'no-image-available';
    showErrorImg($strErrTxt);
}

// Check to see if the image already exists in the cache, and has not expired !.
$checkCache = checkCache($arrAssetInfo['channel'], $strEAN, $strProdID, $strImageSize, $arrResult['date_created'], $strFormat);

if ($checkCache == true) {
    exit();
}

imageProcessor($strUrl);