<?php
/**
 * Created by PhpStorm.
 * User: bruce.tomalin
 * Date: 02/04/2019
 * Time: 08:24
 */

/**
 * Description:
 * - Displays an error image with the specified error code in it - default jpg format, black text on grey background !.
 *
 * @param string $strErrTxt
 * @return
 */
function showErrorImg($strErrTxt)
{

    $strUrl = (!empty($_SERVER["REQUEST_URI"])) ? $_SERVER["REQUEST_URI"] : HTTP_PROTOCOL . $_SERVER['HTTP_HOST'];

    // Remove all illegal characters from a url
    $sanitized_url = filter_var($strUrl, FILTER_SANITIZE_URL);
    $strUrl = $sanitized_url;

    // Explode the URL request into parts
    $arrURLparts = explode('/', $strUrl);

    // Give each element and name
    $strImageSize = $arrURLparts[3];;

    if($strErrTxt === 'no-image-available') {
        /* Attempt to open */
        $im = imagecreatefrompng("public/images/no_image_available.png");

        $newwidth = $strImageSize;
        $newheight = $strImageSize;
        $newImage = imagescale($im, $newwidth, $newheight , $mode = IMG_BILINEAR_FIXED);

        // enables alpha channel
        imagealphablending($newImage, true); // setting alpha blending on
        imagesavealpha($newImage, true); // save alphablending setting (important)

        /* See if it failed */
        if(!$newImage)
        {
            /* Create a blank image */
            header("Content-Type: image/jpg");

            $resImg = @imagecreate(100, 50);
            $resBgColour = imagecolorallocate($resImg, 224, 223, 227);
            $resTextColour = imagecolorallocate($resImg, 0, 0, 0);
            imagestring($resImg, 3, 10, 10, "IMAGE ERROR", $resTextColour);
            imagestring($resImg, 3, 25, 25, $strErrTxt, $resTextColour);
            imagejpeg($resImg);
            imagedestroy($resImg);
            die();
        }

        header('Content-Type: image/png');
        header("HTTP/1.0 404 Not Found");
        imagepng($newImage);
        imagedestroy($newImage);
        die();
    }

    if($strErrTxt === 'error-image') {
        /* Attempt to open */
        $im = imagecreatefrompng("public/images/img_not_available.png");

        $newwidth = $strImageSize;
        $newheight = $strImageSize;
        $newImage = imagescale($im, $newwidth, $newheight , $mode = IMG_BILINEAR_FIXED);

        // enables alpha channel
        imagealphablending($newImage, true); // setting alpha blending on
        imagesavealpha($newImage, true); // save alphablending setting (important)

        /* See if it failed */
        if(!$newImage)
        {
            /* Create a blank image */
            header("Content-Type: image/jpg");

            $resImg = @imagecreate(100, 50);
            $resBgColour = imagecolorallocate($resImg, 224, 223, 227);
            $resTextColour = imagecolorallocate($resImg, 0, 0, 0);
            imagestring($resImg, 3, 10, 10, "IMAGE ERROR", $resTextColour);
            imagestring($resImg, 3, 25, 25, $strErrTxt, $resTextColour);
            imagejpeg($resImg);
            imagedestroy($resImg);
            die();
        }

        header('Content-Type: image/png');
        header("HTTP/1.0 404 Not Found");
        imagepng($newImage);
        imagedestroy($newImage);
        die();

    }

    if($strErrTxt === 'image-creation-failed') {

        /* Attempt to open */
        $im = imagecreatefrompng("public/images/img_creation_failed.png");

        $newwidth = $strImageSize;
        $newheight = $strImageSize;
        $newImage = imagescale($im, $newwidth, $newheight , $mode = IMG_BILINEAR_FIXED);

        // enables alpha channel
        imagealphablending($newImage, true); // setting alpha blending on
        imagesavealpha($newImage, true); // save alphablending setting (important)

        /* See if it failed */
        if(!$newImage)
        {
            /* Create a blank image */
            header("Content-Type: image/jpg");

            $resImg = @imagecreate(100, 50);
            $resBgColour = imagecolorallocate($resImg, 224, 223, 227);
            $resTextColour = imagecolorallocate($resImg, 0, 0, 0);
            imagestring($resImg, 3, 10, 10, "IMAGE ERROR", $resTextColour);
            imagestring($resImg, 3, 25, 25, $strErrTxt, $resTextColour);
            imagejpeg($resImg);
            imagedestroy($resImg);
            die();
        }

        header('Content-Type: image/png');
        header("HTTP/1.0 404 Not Found");
        imagepng($newImage);
        imagedestroy($newImage);
        die();

    }

    if($strErrTxt === 'no-document-found') {

        /* Attempt to open */
        $im = imagecreatefrompng("public/images/pdf_not_available.png");

        $newwidth = $strImageSize;
        $newheight = $strImageSize;
        $newImage = imagescale($im, $newwidth, $newheight , $mode = IMG_BILINEAR_FIXED);

        // enables alpha channel
        imagealphablending($newImage, true); // setting alpha blending on
        imagesavealpha($newImage, true); // save alphablending setting (important)

        /* See if it failed */
        if(!$newImage)
        {
            /* Create a blank image */
            header("Content-Type: image/jpg");

            $resImg = @imagecreate(100, 50);
            $resBgColour = imagecolorallocate($resImg, 224, 223, 227);
            $resTextColour = imagecolorallocate($resImg, 0, 0, 0);
            imagestring($resImg, 3, 10, 10, "IMAGE ERROR", $resTextColour);
            imagestring($resImg, 3, 25, 25, $strErrTxt, $resTextColour);
            imagejpeg($resImg);
            imagedestroy($resImg);
            die();
        }

        header('Content-Type: image/png');
        header("HTTP/1.0 404 Not Found");
        imagepng($newImage);
        imagedestroy($newImage);
        die();

    }
}