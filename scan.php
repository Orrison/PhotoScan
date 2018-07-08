<?php include("_components/functions.php"); ?>
<html lang="en">

<head>
    <?php include("_components/head.php"); ?>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <?php include("_components/header.php"); ?>

            <div id="content" class="col align-self-center">
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
                  echo '<p>Compass degree and direction photo was taken: ' . ($cleanData['degrees'] ? $cleanData['degrees'] . ' |            ' . $cleanData['direction'] : 'N/A') . '</p>';
                  echo '<a href="/" class="btn btn-primary">Scan Another</a>';
                ?>
                </div>
                <?php else : ?>
                <p>Image has no applicable EXIF data.</p>
                <a href="/" class="btn btn-primary">Scan Another</a>
                <?php endif; ?>

                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include("_components/scripts.php"); ?>
    <script>
        function initMap() {
            var uluru = {
                lat: <?= $location['lat'] ?>,
                lng: <?= $location['lng'] ?>
            };
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
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBy9_TY5k7RwTYXPH8zSX23tXHyR-zMkw4&callback=initMap">
    </script>
</body>

</html>