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

?>

<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="_css/styles.css">

    <title>PhotoScan | Kevin Ullyott</title>
    
  </head>
  <body>
    <div class="container-fluid">
      <div class="pos-f-t">
      <div class="row">
        
        <div class="col-md-12">
          <div class="collapse" id="navbarToggleExternalContent">
            <div class="bg-dark p-4">
              <h4 class="text-white">Collapsed content</h4>
              <span class="text-muted">Toggleable via the navbar brand.</span>
            </div>
          </div>
          <nav class="navbar navbar-dark bg-dark">
            <a class="navbar-brand" href="#">PhotoScan by KEVIN ULLYOTT</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarToggleExternalContent" aria-controls="navbarToggleExternalContent" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon"></span>
            </button>
          </nav>
        </div>

        <div id="content" class="col-md-6 offset-md-3">
        <?php if (isset($file)) : ?>

          <div id="map"></div>

          <?php if (isset($cleanData['Make'])) : ?>
            <div id="info">
              <?php
                echo '<p>Make of device: ' . ($cleanData['Make'] ? $cleanData['Make'] : 'N/A') . '</p>';
                echo '<p>Model of device: ' . ($cleanData['Model'] ? $cleanData['Model'] : 'N/A') . '</p>';
                echo '<p>Date and time image was taken: ' . ($cleanData['DateTime'] ? $cleanData['DateTime'] : 'N/A') . '</p>';
                echo '<p>Latitude photo was taken: ' . ($cleanData['lat'] ? $cleanData['lat'] : 'N/A') . '</p>';
                echo '<p>Longitude photo was taken: ' . ($cleanData['lng'] ? $cleanData['lng'] : 'N/A') . '</p>';
                echo '<p>Compass degree and direction photo was taken: ' . ($cleanData['degrees'] ? $cleanData['degrees'] . ' | ' . $cleanData['direction'] : 'N/A') . '</p>';
                echo '<a href="/" class="btn btn-primary">Scan Another</a>';
              ?>
            </div>
          <?php else : ?>
            <p>Image has no applicable EXIF data.</p>
            <a href="/" class="btn btn-primary">Scan Another</a>
          <?php endif; ?>

        <?php else : ?>

          <form action="" method="post" enctype="multipart/form-data">
            <div class="form-group">
              <label for="exampleInputFile">Image Upload</label>
              <input type="file" name="photoUpload" class="form-control-file" id="exampleInputFile" aria-describedby="fileHelp">
              <small id="fileHelp" class="form-text text-muted">Upload an image here to scan it's EXIF Data, take a picture with your phone and give it a try!</small>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
          </form>

        <?php endif; ?>
        </div>
      </div>
    </div>
  </div>


    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script>
      function initMap() {
        var uluru = {lat: <?= $location['lat'] ?>, lng: <?= $location['lng'] ?>};
        var map = new google.maps.Map(document.getElementById('map'), {
          zoom: 16,
          center: uluru
        });
        var marker = new google.maps.Marker({
          position: uluru,
          map: map
        });
      }
    </script>
    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBy9_TY5k7RwTYXPH8zSX23tXHyR-zMkw4&callback=initMap">
    </script>
  </body>
</html>