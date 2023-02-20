<?php  
// Create new feedback
if(isset($_POST['createFeedbackBtn'])) {
  $message = $conn->real_escape_string($_POST['message']);
  $createdAt = date('Y-m-d h:i:s', time());
  $userID = $userInfo['id'];

  $createFeedbackResult = $conn->query("INSERT INTO $feedbacksTable(
    content,
    user_id,
    created_at
  ) VALUES(
    '$message',
    '$userID',
    '$createdAt'
  )");

  if($createFeedbackResult) {
    $message = "Your feedback has been submitted! Thank you";
    $hasError = false;
    $hasSuccess = true;
  } else {
    $message = "Sorry, your feedback was not successfully submitted. Kindly take a photo of this message and send this to the admin.\n\n<strong>Reason:\n" . $conn->error . "</strong>";
    $hasError = true;
    $hasSuccess = false;
  }
}

$feedbacksResult = $conn->query("SELECT f.*, u.fullname as name
  FROM $feedbacksTable f 
  INNER JOIN $usersTable u
  ON f.user_id=u.id
  WHERE user_id='${userInfo['id']}' 
  ORDER BY created_at DESC
");
?>

<!-- Main Content -->
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
      <h6 class="m-0 font-weight-bold" style="color:#223D3C;"><i class="fa fa-comment"></i> Feedbacks &nbsp;&nbsp;&nbsp;
        <button type="button" class="btn btn-primary px-auto" style="background-color:#223D3C; border-color:#223D3C;" data-toggle="modal" data-target="#createFeedbackModal">
          <i class="fa fa-plus-circle"></i>
          Create new
        </button>
      </h6>
    </div>

    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered" id="feedbacks" width="100%" cellspacing="0">
          <thead>
            <tr>
              <th>Fullname</th>
              <th>Message</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $feedbacksResult->fetch_assoc()) { ?>
              <tr>
                <td><?= $row['name'] ?></td>
                <td><?= $row['content'] ?></td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<!-- End Main Content -->



<!-- Create Modal -->
<div class="modal fade" id="createFeedbackModal" tabindex="-1" role="dialog" aria-labelledby="createFeedback" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="createFeedback">Create new feedback</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form id="createFeedbackForm" method="POST" autocomplete="off">
        <div class="modal-body">

          <div class="form-group">
            <label> Message </label>
            <textarea name="message" class="form-control" maxlength="255" rows="10" placeholder="Write your message..." minlength="10" required></textarea>
          </div>

        </div>

        <div class="row px-2">
          <div class="col">
            <div class="form-group">
              <button type="button" class="btn btn-danger btn-block" data-dismiss="modal" aria-label="Cancel">Cancel</button>
            </div>
          </div>

          <div class="col">
            <div class="form-group">
              <button style="background-color:#223D3C; border-color:#223D3C;" type="submit" name="createFeedbackBtn" class="btn btn-success btn-block">Send</button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- End Create Modal -->