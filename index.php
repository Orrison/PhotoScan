<html lang="en">
  <head>
    <?php include("_components/head.php"); ?>
  <body>
    <div class="container-fluid">
      <div class="row">
      <?php include("_components/header.php"); ?>

        <div id="content" class="col align-self-center home">

          <form action="scan.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
              <div class="upload-text">
              <p><i class="fas fa-cloud-upload-alt"></i>image <span class="up">up</span>load</p>
              </div>
              <input type="file" name="photoUpload" class="form-control-file" id="exampleInputFile" aria-describedby="fileHelp">
              <small id="fileHelp" class="form-text text-muted">Upload an image here to scan it's EXIF Data, take a picture with your phone and give it a try!</small>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
          </form>

        </div>
      </div>
  </div>

  <?php include("_components/scripts.php"); ?>
  </body>
</html>