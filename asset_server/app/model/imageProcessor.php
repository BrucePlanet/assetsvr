<?php
/**
 * Created by PhpStorm.
 * User: bruce.tomalin
 * Date: 01/04/2019
 * Time: 18:24
 */

/**
 * Description:
 * - Processes the request as an image type.
 *
 * @param array $arrRequest
 * @param array $arrAssetInfo
 * @return
 */

function imageProcessor($strUrl)
{
    // this is where the API request will be processed
    $arrURLparts = explode('/', $strUrl);

    $urlCount = count($arrURLparts);

    $strEAN = $arrURLparts[1];
    $strProdID = $arrURLparts[2];
    $strImageSize = $arrURLparts[3];

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

    $arrResult['EAN'] = $strEAN;
    $arrResult['prod_id'] = $strProdID;
    $arrResult['imgsize'] = $strImageSize;
    $arrResult['format'] = $strFormat;
    $arrResult['date_created'] = date("Y-m-d H:i:s");
    $arrAssetInfo['channel'] = 'image';
    $arrResult['wmid'] = 3;

    /** Build the Get request URL */
    $strHTTPProtocol = HTTP_PROTOCOL;

//    $strHTTPProtocol = 'http://';

    switch ($_SERVER["HTTP_HOST"]) {
        case "fl-assetsvr.timico.develop":
            $strFIEDomain = FIE_DEV;
            break;
        case "fl-assets-test.kondor.co.uk":
            $strFIEDomain = FIE_TEST;
            break;
        case "fl-assets.kondor.co.uk":
            $strFIEDomain = FIE;
            break;
    }

    $strRequestString = CALL;

    $strGetURL = $strFIEDomain . $strRequestString . $strEAN . '/' . $strProdID;

    $apiResult = apiRequest($strGetURL);

    $strMime_type = $apiResult['details']['mime_type'];
    $strTitle = $apiResult['details']['title'];
    $strAlt_title = $apiResult['details']['alt_title'];
    $strProduct_id = $apiResult['details']['product_id'];
    $arrImageBlob = base64_decode($apiResult['details']['image']);

    ini_set('memory_limit', '215M');

    if (!$arrImageBlob) {

        mail(EMAIL_ADDRESS, ENV . ": ERROR - DB returns no blob for this asset_id requested from - ", print_r($_SERVER['REMOTE_HOST'], true), print_r($_SERVER['HTTP_HOST'], true), print_r($arrAssetInfo, true), "from:noreply@kondor.co.uk");
        $strErrTxt = 'error-image';
        showErrImg($strErrTxt);
    }

    /** Try creating the image if this fails send an error image */
    try {

        $resMainImg = imagecreatefromstring($arrImageBlob);

    } catch (Exception $e) {

        echo 'failed to create image failed: ' . $e->getMessage();
        mail(EMAIL_ADDRESS, ENV . " : ERROR - Image creation has failed ", print_r($_SERVER['REMOTE_HOST'], true), print_r($_SERVER['HTTP_HOST'], true), print_r($arrAssetInfo, true), "from:noreply@kondor.co.uk");
        $strErrTxt = 'image-creation-failed';
        showErrImg($strErrTxt);
    }

    // Calculate the x & y for the water mark to sit in the main image center.
    $intMainImgX = imagesx($resMainImg);
    $intMainImgY = imagesy($resMainImg);

    // TL:: fork between legacy functionality (1:1 ratio only) and new variable ratio
    $intDimensions = explode("x", $arrResult['imgsize']);

    if (count($intDimensions) == 1) { // We don't want images with one dimension any more, so rebuild with both dimensions matching...
        if ($arrResult['imgsize'] == 0 || $arrResult['imgsize'] == "0x0") {
            $arrResult['imgsize'] = $intMainImgX . "x" . $intMainImgY;
        } else {
            if ($intMainImgX >= $arrResult['imgsize']) {
                $arrResult['imgsize'] = $intDimensions[0] . "x" . $intDimensions[0];
            } else {
                $arrResult['imgsize'] = $intMainImgX . "x" . $intMainImgY;
            }
        }
        $intDimensions = explode("x", $arrResult['imgsize']);
    }

    $intNewX = $intDimensions[0];
    $intNewY = $intDimensions[1];
    $intNewResizeX = $intNewX;
    $intNewResizeY = $intNewY;

    if (count($intDimensions) == 1) {
        // If the image size requested = 0 then set image size requested to the actual image size !.
        if ($arrResult['imgsize'] == 0) {
            // Image size specified as 0, or no scaling required.
            $arrResult['imgsize'] = $intMainImgX;
        }
        // Check to see if this image needs to be resized - will NOT increase image size !. If so use the
        if ($arrResult['imgsize'] < $intMainImgX) {
            // Image requested is < than the stored image in the DB. Create a new image with equal x & y based on the size provided in the request.
            $resMainImg2 = imagecreatetruecolor($arrResult['imgsize'], $arrResult['imgsize']);
            imagesavealpha($resMainImg2, true);
            imagefill($resMainImg2, 0, 0, imagecolorallocatealpha($resMainImg2, 0, 0, 0, 127));
            imagecopyresampled($resMainImg2, $resMainImg, 0, 0, 0, 0, $arrResult['imgsize'], $arrResult['imgsize'], $intMainImgX, $intMainImgY);
        } else {
            // No rescaling required (requested image same as stored image), OR requested image size > stored image, so create the final image the same as the original we created earlier.
            $resMainImg2 = imagecreatetruecolor($intMainImgX, $intMainImgY);
            imagesavealpha($resMainImg2, true);
            imagefill($resMainImg2, 0, 0, imagecolorallocatealpha($resMainImg2, 0, 0, 0, 127));
            imagecopy($resMainImg2, $resMainImg, 0, 0, 0, 0, $intMainImgX, $intMainImgY);
        }
    } else {
        // Image dimensions are <width>x<height>, or so we assume.
        $intOffsetX = 0;
        $intOffsetY = 0;

        if ($intNewX == 0) {
            $intNewX = imagesx($resMainImg) / (imagesy($resMainImg) / $intNewY);
        }

        if ($intNewY == 0) {
            $intNewY = imagesy($resMainImg) / (imagesx($resMainImg) / $intNewX);
        }

        if ($intNewX != 0 && $intNewY != 0) {
            $intOldX = imagesx($resMainImg);
            $intOldY = imagesy($resMainImg);

            if ($intOldX > $intOldY) {
                /*
                 * X is bigger Old Image is a different shape to the one being requested
                 */
                $intPercent = ($intNewX / ($intOldX / 100));
                $intNewResizeY = ($intOldY / 100) * $intPercent;

                if ($intNewResizeY > $intNewY) {
                    $intPercent = ($intNewY / ($intOldY / 100));
                    $intNewResizeX = ($intOldX / 100) * $intPercent;
                    $intOffsetX = ($intNewX / 2) - ($intNewResizeX / 2);
                    $intNewResizeY = $intNewY;
                } else {
                    $intOffsetY = ($intNewY / 2) - ($intNewResizeY / 2);
                    $intNewResizeX = $intNewX;
                }
            } else {
                $intPercent = ($intNewY / ($intOldY / 100));
                $intNewResizeX = ($intOldX / 100) * $intPercent;

                if ($intNewResizeX > $intNewX) {
                    $intPercent = ($intNewX / ($intOldX / 100));
                    $intNewResizeY = ($intOldY / 100) * $intPercent;
                    $intOffsetY = ($intNewY / 2) - ($intNewResizeY / 2);
                    $intNewResizeX = $intNewX;
                } else {
                    $intOffsetX = ($intNewX / 2) - ($intNewResizeX / 2);
                    $intNewResizeY = $intNewY;
                }
            }
        }

        // Calculate the new dimensions.
        $resMainImg2 = imagecreatetruecolor($intNewX, $intNewY);

        switch (strtolower($arrResult['format'])) {
            case "jpg":
            case "jpeg":

                $white = imagecolorallocate($resMainImg2, 255, 255, 255);
                $black = imagecolorallocate ($resMainImg2,0,0,0);
                imagefill($resMainImg2, 0, 0, $white );

                break;

            case "png":

                imagesavealpha($resMainImg2, true);
                $color = imagecolorallocatealpha($resMainImg2, 0,0,0, 127);
                imagefill($resMainImg2, 0, 0, $color);

                break;

        }

        imagecopyresampled($resMainImg2, $resMainImg, $intOffsetX, $intOffsetY, 0, 0, $intNewResizeX, $intNewResizeY, $intMainImgX, $intMainImgY);

    }

    // Stream the image back to the requester.
    switch (strtolower($arrResult['format'])) {
        case "jpg":
        case "jpeg":
            // Store the asset in the cache
            cacheAsset($resMainImg2, $arrResult['EAN'], $arrResult['prod_id'], $strImageSize, $arrResult['format'], $arrResult['wmid']);
            ob_start();
            imagejpeg($resMainImg2, NULL, 100);

            $strData = ob_get_contents();
            ob_end_clean();
            $_SERVER['CONTENT_TYPE'] = 'image/jpeg';

            if ($arrAssetInfo['channel'] == 'download') {

                header("Content-Disposition: attachment; filename=\"" . strtolower(str_replace(" ", "_", $arrResult['title'])) . "." . $arrResult['format'] . "\"");

            } else {
                header('Content-Disposition: inline; filename="' . $strMime_type . "." . $arrResult['format'] . '"');
            }

            header("Content-Type: image/" . $arrResult['format']);

            print $strData;
            break;
        case "png":
            // Store the asset in the cache
            cacheAsset($resMainImg2, $arrResult['EAN'], $arrResult['prod_id'], $strImageSize, $arrResult['format'], $arrResult['wmid']);
            ob_start();
            imagepng($resMainImg2);
            $strData = ob_get_contents();
            ob_end_clean();
            $_SERVER['CONTENT_TYPE'] = 'image/png';
            if ($arrAssetInfo['channel'] == 'download') {
                header("Content-Disposition: attachment; filename=\"" . strtolower(str_replace(" ", "_", $arrResult['title'])) . "." . $arrResult['format'] . "\"");
            } else {
                header('Content-Disposition: inline; filename="' . $strMime_type . "." . $arrResult['format'] . '"');
            }
            header("Content-Type: image/" . $arrResult['format']);
            print $strData;
            break;
        case "gif":
            // Store the asset in the cache
            cacheAsset($resMainImg2, $arrResult['EAN'], $arrResult['prod_id'], $strImageSize, $arrResult['format'], $arrResult['wmid']);
            ob_start();
            imagegif($resMainImg2);
            $strData = ob_get_contents();
            ob_end_clean();
            $_SERVER['CONTENT_TYPE'] = 'image/gif';
            if ($arrAssetInfo['channel'] == 'download') {
                header("Content-Disposition: attachment; filename=\"" . strtolower(str_replace(" ", "_", $arrResult['title'])) . "." . $arrResult['format'] . "\"");
            } else {
                header('Content-Disposition: inline; filename="' . $strMime_type . "." . $arrResult['format'] . '"');
            }
            header("Content-Type: image/" . $arrResult['format']);
            print $strData;
            break;
    }

    // Free any memory resources.
    // imagedestroy($resWm);
    imagedestroy($resMainImg);
    imagedestroy($resMainImg2);
}