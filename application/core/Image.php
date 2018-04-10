<?php

use ColorThief\ColorThief;

class Image
{

    public static function getBaseColour($sourceImage)
    {
        return ColorThief::getColor($sourceImage);
    }

    public static function getPalette($sourceImage, $count = 8)
    {
        return ColorThief::getPalette($sourceImage, $count);
    }

    public static function getContentType($source_image)
    {
        $imageData = getimagesize($source_image);
        return $imageData["mime"];
    }

    public static function makeThumbnail($source_image, $destination, $final_width = 44, $final_height = 44, $bgcolour = array(224, 224, 228), $center = true)
    {

        $imageData = getimagesize($source_image);
        $width = $imageData[0];
        $height = $imageData[1];
        $mimeType = $imageData['mime'];

        if (!$width || !$height) {
            return false;
        }

        switch ($mimeType) {
            case 'image/jpeg':$myImage = imagecreatefromjpeg($source_image);
                break;
            case 'image/png':$myImage = imagecreatefrompng($source_image);
                break;
            case 'image/gif':$myImage = imagecreatefromgif($source_image);
                break;
            default:return false;
        }

        // calculating the part of the image to use for thumbnail
        if ($width > $height) {
            $verticalCoordinateOfSource = 0;
            $horizontalCoordinateOfSource = ($width - $height) / 2;
            $smallestSide = $height;
        } else {
            $horizontalCoordinateOfSource = 0;
            $verticalCoordinateOfSource = ($height - $width) / 2;
            $smallestSide = $width;
        }

        if (!$center) {
            $horizontalCoordinateOfSource = 0;
            $verticalCoordinateOfSource = 0;
        }

        // copying the part into thumbnail, maybe edit this for square avatars
        $thumb = imagecreatetruecolor($final_width, $final_height);

        // fill the image with the base colour
        $fill = imagecolorallocate($thumb, $bgcolour[0], $bgcolour[1], $bgcolour[2]);
        imagefill($thumb, 0, 0, $fill);

        // TODO: http://stackoverflow.com/questions/747101/resize-crop-pad-a-picture-to-a-fixed-size

        imagecopyresampled($thumb, $myImage, 0, 0, $horizontalCoordinateOfSource, $verticalCoordinateOfSource, $final_width, $final_height, $smallestSide, $smallestSide);

        // add '.jpg' to file path, save it as a .jpg file with our $destination_filename parameter
        imagejpeg($thumb, $destination . '.jpg', Config::get('AVATAR_JPEG_QUALITY'));
        imagedestroy($thumb);

        if (file_exists($destination)) {
            return true;
        }
        return false;
    }

    public static function urlThumb($source, $destination, $width = 150, $height = true) {

        // file_get_contents needs to skip invalid certificates - prevent verifying peer
        $fgc_options = stream_context_create(array(
            "ssl"=>array(
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            ),
        ));

        // download and create gd image
        $image = ImageCreateFromString(file_get_contents($source, false, $fgc_options));

        // calculate resized ratio
        // Note: if $height is set to TRUE then we automatically calculate the height based on the ratio
        $height = $height === true ? (ImageSY($image) * $width / ImageSX($image)) : $height;

        // create image
        $output = ImageCreateTrueColor($width, $height);
        ImageCopyResampled($output, $image, 0, 0, 0, 0, $width, $height, ImageSX($image), ImageSY($image));

        // save image
        ImageJPEG($output, $destination, 95);

        // return resized image
        return $output; // if you need to use it
    }

}
