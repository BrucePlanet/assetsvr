<?php
/**
 * Created by PhpStorm.
 * User: bruce.tomalin
 * Date: 03/04/2019
 * Time: 15:31
 */

/**
 * Description:
 * - Saves the asset specified into the asset cache.
 *
 * @param resource $resAssetData
 * @param int $intAssetId
 * @param int $intAssetSize
 * @param string $strAssetFormat
 * @param int $intWmId
 * @return bool success / fail
 */
function cacheAsset($resAssetData, $intAssetEAN, $intAssetId, $intAssetSize, $strAssetFormat)
{

    switch ($_SERVER["HTTP_HOST"]) {
        case "fl-assetsvr.timico.develop":
            // Create the cache asset filename
            $strCacheFname = MOUNT_FOLDER . $intAssetEAN . "_" . $intAssetId . "_" . $intAssetSize . "x" . $intAssetSize . "." . $strAssetFormat;
            break;
        case "fl-assets-test.kondor.co.uk":
            $strCacheFname = PUBLIC_PATH . IMAGE_CACHE_PATH . $intAssetEAN . "_" . $intAssetId . "_" . $intAssetSize . "x" . $intAssetSize . "." . $strAssetFormat;
            break;
        case "fl-assets.kondor.co.uk":
            $strCacheFname = MOUNT_FOLDER . $intAssetEAN . "_" . $intAssetId . "_" . $intAssetSize . "x" . $intAssetSize . "." . $strAssetFormat;
            break;
    }

    switch (strtolower($strAssetFormat)) {
        case "jpg":
        case "jpeg":
            // Save asset to cache - or send an error if was unable too.
            if (!imagejpeg($resAssetData, $strCacheFname, 100)) {
                sendError("Asset Server Error: unable to cache image " . $strCacheFname, "");
                return FALSE;
            }
            return TRUE;
            break;
        case "png":
            // Save asset to cache - or send an error if was unable too.
            if (!imagepng($resAssetData, $strCacheFname)) {
                sendError("Asset Server Error: unable to cache image " . $strCacheFname, "");
                return FALSE;
            } else {
                return TRUE;
            }
            break;
        case "gif":
            // Save asset to cache - or send an error if was unable too.
            if (!imagegif($resAssetData, $strCacheFname)) {
                sendError("Asset Server Error: unable to cache image " . $strCacheFname, "");
                return FALSE;
            } else {
                return TRUE;
            }
            break;
    }
}