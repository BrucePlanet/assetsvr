<?php
/**
 * Created by PhpStorm.
 * User: bruce.tomalin
 * Date: 03/04/2019
 * Time: 15:59
 */

/**
 * Description:
 * - Takes the specified request params, and checks to see if the asset exist in the cache, and has not expired.
 *
 * @param string $strAssetType
 * @param int $intAssetCreateTimestamp
 * @param int $intAssetId
 * @param int $intAssetSize
 * @param string $strAssetFormat
 * @param int $intWmId
 * @return string
 */

function checkCache($strAssetType = "", $intAssetEAN, $intAssetId, $intAssetSize = "", $intAssetType = "", $strAssetFormat)
{

    switch ($_SERVER["HTTP_HOST"]) {
        case "fl-assetsvr.timico.develop":
            // Create the cache asset filename
            $strCacheFname = MOUNT_FOLDER . $intAssetEAN . "_" . $intAssetId . "_" . $intAssetSize . "x" . $intAssetSize .  "." . $strAssetFormat;
            break;
        case "fl-assets-test.kondor.co.uk":
            $strCacheFname = PUBLIC_PATH . IMAGE_CACHE_PATH . $intAssetEAN . "_" . $intAssetId . "_" . $intAssetSize . "x" . $intAssetSize .  "." . $strAssetFormat;
            break;
        case "fl-assets.kondor.co.uk":
            $strCacheFname = MOUNT_FOLDER . $intAssetEAN . "_" . $intAssetId . "_" . $intAssetSize . "x" . $intAssetSize .  "." . $strAssetFormat;
            break;
    }

    if (!file_exists($strCacheFname)) {
        $strCacheFname = MOUNT_FOLDER_REM . $intAssetEAN . "_" . $intAssetId . "_" . $intAssetSize . "x" . $intAssetSize .  "." . $strAssetFormat;
        if (!file_exists($strCacheFname)) {
            return FALSE;
        }
    }

    $strCacheContents = @file_get_contents($strCacheFname);

    //** Calculate whether or not the file has expired */
    $lastMod = filemtime($strCacheFname);
    $offset = 3600 * 24 * 7; // 1 Week expiry time.
    $today  = time();
    $expiry = ($today - $offset);

    //** Has a cached file been found? */
    if($strCacheContents == false) {
        return FALSE;
    }

    //** If a file has been found is it out of date? */
    if ($lastMod < $expiry) {
        return FALSE;
    } else {

        $offset = 3600 * 24 * 7; // 1 Week expiry time.
        $today  = time();
        $expiry = ($today - $offset);

        header('Content-Length: ' . filesize($strCacheFname));
        header("Content-Type: image/$strAssetFormat");
        header("Cache-Control: public, must-revalidate, max-age=".$expiry); // Generate cache control header
        header("Etag: 7b29ea78a830245774156847d99f9aeb");
        header("Pragma: public");
        header("Keep-Alive: timeout=5, max=200");
        header('Content-Disposition: inline; filename="' . $intAssetEAN . "_" . $intAssetId . "_" . $intAssetSize . "x" . $intAssetSize .  "." . $strAssetFormat . '"');
        if ($strAssetType === 'download') {
            header("Content-Disposition: attachment; filename=\"".strtolower(str_replace(" ","_",$strAssetType)).".".$intAssetType."\"");
        }
        print($strCacheContents);
        return TRUE;
        exit();
    }
}