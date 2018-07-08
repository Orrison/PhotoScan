<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
/**
 * Returns an array of latitude and longitude from the Image file
 * @param image $file
 * @return multitype:number |boolean
 */
function read_gps_location($file)
{
    if (is_file($file)) {
        $info = exif_read_data($file);
        if (isset($info['GPSLatitude']) && isset($info['GPSLongitude']) &&
            isset($info['GPSLatitudeRef']) && isset($info['GPSLongitudeRef']) &&
            in_array($info['GPSLatitudeRef'], array('E','W','N','S')) && in_array($info['GPSLongitudeRef'], array('E','W','N','S'))) {
            $GPSLatitudeRef  = strtolower(trim($info['GPSLatitudeRef']));
            $GPSLongitudeRef = strtolower(trim($info['GPSLongitudeRef']));

            $lat_degrees_a = explode('/', $info['GPSLatitude'][0]);
            $lat_minutes_a = explode('/', $info['GPSLatitude'][1]);
            $lat_seconds_a = explode('/', $info['GPSLatitude'][2]);
            $lng_degrees_a = explode('/', $info['GPSLongitude'][0]);
            $lng_minutes_a = explode('/', $info['GPSLongitude'][1]);
            $lng_seconds_a = explode('/', $info['GPSLongitude'][2]);

            $lat_degrees = $lat_degrees_a[0] / $lat_degrees_a[1];
            $lat_minutes = $lat_minutes_a[0] / $lat_minutes_a[1];
            $lat_seconds = $lat_seconds_a[0] / $lat_seconds_a[1];
            $lng_degrees = $lng_degrees_a[0] / $lng_degrees_a[1];
            $lng_minutes = $lng_minutes_a[0] / $lng_minutes_a[1];
            $lng_seconds = $lng_seconds_a[0] / $lng_seconds_a[1];

            $lat = (float) $lat_degrees+((($lat_minutes*60)+($lat_seconds))/3600);
            $lng = (float) $lng_degrees+((($lng_minutes*60)+($lng_seconds))/3600);

            //If the latitude is South, make it negative.
            //If the longitude is west, make it negative
            $GPSLatitudeRef  == 's' ? $lat *= -1 : '';
            $GPSLongitudeRef == 'w' ? $lng *= -1 : '';

            return array(
                'lat' => $lat,
                'lng' => $lng
            );
        }
    }
    return false;
}

function convertToDecimal($fraction)
{
    $numbers=explode("/", $fraction);
    return round($numbers[0]/$numbers[1], 6);
}

if ($_FILES) {
    $file = $_FILES["photoUpload"]["tmp_name"];

    $exifData = exif_read_data($file);

    $dateTime = date_format($exifData['DateTimeOriginal'], 'n/j/Y g:i:s A');

    $location = read_gps_location($file);

    $degrees = convertToDecimal($exifData['GPSImgDirection']);

    $direction = '';

    if ($degrees == 0) {
        $direction = 'North';
    } elseif ($degrees > 0 && $degrees < 90) {
        $direction = 'North East';
    } elseif ($degrees == 90) {
        $direction = 'East';
    } elseif ($degrees > 90 && $degrees < 180) {
        $direction = 'South East';
    } elseif ($degrees == 180) {
        $direction = 'South';
    } elseif ($degrees > 180 && $degrees < 270) {
        $direction = 'South West';
    } elseif ($degrees == 270) {
        $direction = 'West';
    } elseif ($degrees > 270 && $degrees < 360) {
        $direction = 'North West';
    }

    $cleanData = array('Make' => $exifData['Make'], 'Model' => $exifData['Model'], 'DateTime' => $exifData['DateTimeOriginal'], 'lat' => $location['lat'], 'lng' => $location['lng'], 'degrees' => $degrees, 'direction' => $direction);
}
