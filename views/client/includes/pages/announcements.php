<?php
// Fetch all the announcements ordered from most recent
$announcementsResult = $conn->query("SELECT * FROM $announcementsTable ORDER BY id DESC");
?>

<!-- MAIN CONTENT -->

<div class="container-fluid">

  <?php if ($hasError) { ?>
    <div class="col mb-2">
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <span class="text-danger" id="message"><?= $message ?></span>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    </div>
  <?php } ?>

  <?php if ($hasSuccess) { ?>
    <div class="col mb-2">
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <span class="text-success" id="message"><?= $message ?></span>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    </div>
  <?php } ?>

  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold" style="color:#223D3C;"><i class="fa fa-bell"></i> Announcements</h6>
    </div>
  </div>

  <?php if ($announcementsResult->num_rows == 0) { ?>
    <div class="col mb-2">
      <div class="alert alert-danger show">
        <span class="text-danger" id="message">
          There's currently no announcements to show as of <strong><?= date("F d, Y") ?></strong> at <strong><?= date("h:i A") ?></strong>.
        </span>
      </div>
    </div>
  <?php } ?>

  <?php while ($announcement = $announcementsResult->fetch_assoc()) { 
    $attachmentsCountResult = $conn->query("SELECT * FROM $attachmentsTable WHERE announcement_id='${announcement['id']}'");
    $_attachmentPaths = "";

    while($row = $attachmentsCountResult->fetch_assoc())
      $_attachmentPaths .= "${row['path']},";
    $_attachmentPaths = substr($_attachmentPaths, 0, strlen($_attachmentPaths) - 1);

    $attachmentPaths = explode(",", $_attachmentPaths);
    $attachmentsCount = $attachmentPaths[0] == "" ? 0 : count($attachmentPaths);
  ?>
    <div class="card shadow mb-4">
      <div class="card-header pb-0" style="background-color:#223D3C;">
        <h5 class="font-weight-bold" style="color:#fff;">
          <div class="col px-0 py-0">
            <span>KAPITAN</span><br>
            <h6>
              <span class='text-gray-500 small'><i class="fa fa-clock"></i> <?= $announcement['datetime'] ?></span>
            </h6>
          </div>
        </h5>
      </div>

      <div class="card-body text-dark">
        <p class="text-dark"><?= $announcement['content'] ?></p>

        <?php if ($attachmentsCount > 0) { ?>
          <!-- Carousel wrapper -->
          <div id="attachmentsCarousel<?= $announcement['id'] ?>" class="carousel slide carousel-fade" data-interval="false">
            <!-- Carousel Indicators -->
            <ol class="carousel-indicators">
              <?php for($i = 0; $i < $attachmentsCount; $i++) { ?>
                <li data-target="#attachmentsCarousel<?= $announcement['id'] ?>" data-slide-to="<?= $i ?>" <?= $i == 0 ? 'class="active"' : '' ?> ></li>
              <?php } ?>
            </ol> 

            <!-- Inner -->
            <div class="carousel-inner" style="height: 500px;">
              <!-- Carousel item -->
              <?php for ($i = 0; $i < $attachmentsCount; $i++) {
                $mimeType = mime_content_type($attachmentPaths[$i]);
              ?>
                <?php if(StringUtils::contains($mimeType, "image")) { ?>
                  <div class="carousel-item <?= $i == 0 ? 'active' : '' ?>">
                    <img src="<?= $attachmentPaths[$i] ?>" class="d-block w-100" height="500px" alt="Attachment #<?= $i + 1 ?>" />
                    <div class="carousel-caption d-none d-md-block">
                      <h5>ATTACHMENT #<?= $i + 1 ?></h5>
                      <p>
                        This is the attachment #<?= $i + 1 ?> uploaded by the admin
                      </p>
                    </div>
                  </div>
                <?php } ?>

                <?php if(StringUtils::contains($mimeType, "video")) { ?>
                  <div class="carousel-item <?= $i == 0 ? 'active' : '' ?>">
                    <video class="img-fluid" height="500px" autoplay loop>
                      <source src="<?= $attachmentPaths[$i] ?>" type="<?= $mimeType ?>" />
                    </video>
                    <div class="carousel-caption d-none d-md-block">
                      <h5>ATTACHMENT #<?= $i + 1 ?></h5>
                      <p>
                        This is the attachment #<?= $i + 1 ?> uploaded by the admin
                      </p>
                    </div>
                  </div>
                <?php } ?>

              <?php } ?>
            </div>
            <!-- Inner -->

            <!-- Controls -->
            <a class="carousel-control-prev" href="#attachmentsCarousel<?= $announcement['id'] ?>" role="button" data-slide="prev">
              <span class="carousel-control-prev-icon" aria-hidden="true"></span>
              <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#attachmentsCarousel<?= $announcement['id'] ?>" role="button" data-slide="next">
              <span class="carousel-control-next-icon" aria-hidden="true"></span>
              <span class="sr-only">Next</span>
            </a>
          </div>
          <!-- Carousel wrapper -->
        <?php } ?>
      </div>
    </div>
  <?php } ?>

</div>

<!-- END OF MAIN CONTENT -->



<!-- MODAL FOR VIEWING ATTACHMENTS -->

<div class="modal fade" id="viewAttachments" tabindex="-1" role="dialog" aria-labelledby="viewAttachmentsLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" style="color:#223D3C; font-weight: bold;" id="viewAttachmentsLabel">Attachments</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <div id="attachments-carousel" class="carousel slide" data-ride="carousel">
          
          <div class="carousel-inner"></div>

          <a href="#attachments-carousel" class="carousel-control-prev" style="color:#223D3C;" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" style="color:#223D3C;"></span>
            <span class="sr-only" style="color:#223D3C;">Previous</span>
          </a>
          
          <a href="#attachments-carousel" class="carousel-control-next" style="color:#223D3C;" role="button" data-slide="next">
            <span class="carousel-control-next-icon" style="color:#223D3C;"></span>
            <span class="sr-only" style="color:#223D3C;">Next</span>
          </a>

        </div>
      </div>
    </div>
  </div>
</div>

<!-- END MODAL FOR VIEWING ATTACHMENTS -->