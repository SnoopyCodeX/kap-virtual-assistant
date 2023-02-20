<?php

if (isset($_POST['approveCCTVReviewBtn'])) {
  $datetime = $conn->real_escape_string($_POST['date_and_time']);
  $transactionNumber = $conn->real_escape_string($_POST['transaction_number']);
  $location = $conn->real_escape_string($_POST['exact_location']);
  $userID = $conn->real_escape_string($_POST['user_id']);
  $date = $conn->real_escape_string($_POST['date']);
  $time = $conn->real_escape_string($_POST['time']);
  $name = $conn->real_escape_string($_POST['name']);
  $name = explode(",", trim($name, " "))[0];

  $userInfoResult = $conn->query("SELECT * FROM $usersTable WHERE id='$userID'");
  $userInfo = $userInfoResult->fetch_assoc();
  $contactNumber = $userInfo['contact_number'];

  $updateTransactionTableResult = $conn->query("UPDATE $transactionsTable SET status='approved' WHERE transaction_number='$transactionNumber'");
  $updateCCTVReviewsTableResult = $conn->query("UPDATE $cctvReviewsTable SET status='approved' WHERE transaction_number='$transactionNumber'");

  $smsTextResult = SMSUtil::sendSms(
    $contactNumber,
    "Mr./Mrs. $name, your request for a cctv review has been approved. You may pick it up on " .
      date("F d, Y \\a\\t h:i A", strtotime($datetime)) .
      ".\n\n Your transaction number is: $transactionNumber.\n\n~ASK.Kap"
  );

  if ($updateCCTVReviewsTableResult && $updateTransactionTableResult && $smsTextResult != null) {
    $startDatetime = date("Y-m-d H:i:s", strtotime($datetime, time()));
    $endDatetime = date("Y-m-d H:i:s", strtotime($datetime, time()));

    $addScheduleResult = $conn->query("INSERT INTO $schedulesTable(
      owner_id, 
      event, 
      fromAdmin,
      start_datetime, 
      end_datetime,
      allDay,
      location
    ) VALUES(
      '${userInfo['id']}',
      'Claiming of cctv request by ${userInfo['fullname']}',
      '1',
      '$startDatetime',
      '$endDatetime',
      '0',
      'Baranggay Hall'
    )");

    $hasError = false;
    $hasSuccess = true;
    $message = "The <strong>cctv review</strong> has been approved and <strong>Mr./Mrs. $name</strong> has been notified thru SMS Text.";
  } else if ($updateCCTVReviewsTableResult && $updateTransactionTableResult && $smsTextResult == null) {
    $hasError = false;
    $hasSuccess = true;
    $message = "The <strong>cctv review</strong> has been approved but <strong>Failed to send SMS text to Mr./Mrs. $name.</strong>";
  } else {
    $hasError = true;
    $hasSuccess = false;
    $message = "Something went wrong while updating the database" . ($smsTextResult != null ? " but  <strong>Mr./Mrs. $name</strong> has been notified thru <strong>SMS Text</strong>." : ".");
  }
}



if (isset($_POST['declineCCTVReviewBtn'])) {
  $transactionNumber = $conn->real_escape_string($_POST['transaction_number']);
  $location = $conn->real_escape_string($_POST['exact_location']);
  $reason = $conn->real_escape_string($_POST['reason']);
  $userID = $conn->real_escape_string($_POST['user_id']);
  $date = $conn->real_escape_string($_POST['date']);
  $time = $conn->real_escape_string($_POST['time']);
  $name = $conn->real_escape_string($_POST['name']);
  $name = explode(",", trim($name, " "))[0];

  $userInfoResult = $conn->query("SELECT * FROM $usersTable WHERE id='$userID'");
  $userInfo = $userInfoResult->fetch_assoc();
  $contactNumber = $userInfo['contact_number'];

  $updateTransactionTableResult = $conn->query("UPDATE $transactionsTable SET status='declined' WHERE transaction_number='$transactionNumber'");
  $updateCCTVReviewsTableResult = $conn->query("UPDATE $cctvReviewsTable SET status='declined' WHERE transaction_number='$transactionNumber'");
  $smsTextResult = SMSUtil::sendSms(
    $contactNumber, 
    "Mr./Mrs. $name, your request for a cctv review has been declined.\n\nReason for declining: $reason\n\nCCTV Review Details:\n\nDate: $date\nTime: $time\nLocation: $location\n\n~ASK.Kap"
  );

  if ($updateCCTVReviewsTableResult && $updateTransactionTableResult && $smsTextResult != null) {
    $hasError = false;
    $hasSuccess = true;
    $message = "The <strong>cctv review</strong> has been declined and <strong>Mr./Mrs. $name</strong> has been notified thru SMS Text.";
  } else if ($updateCCTVReviewsTableResult && $updateTransactionTableResult && $smsTextResult == null) {
    $hasError = false;
    $hasSuccess = true;
    $message = "The <strong>cctv review</strong> has been declined but <strong>Failed to send SMS text to Mr./Mrs. $name.</strong>";
  } else {
    $hasError = true;
    $hasSuccess = false;
    $message = "Something went wrong while updating the database" . ($smsTextResult != null ? " but  <strong>Mr./Mrs. $name</strong> has been notified thru <strong>SMS Text</strong>." : ".");
  }
}

$cctvReviewResults = $conn->query("SELECT c.*, u.fullname as name
  FROM $cctvReviewsTable c
  INNER JOIN $usersTable u
  ON c.user_id=u.id
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
      <h6 class="m-0 font-weight-bold" style="color:#223D3C;"><i class="fa fa-camera"></i> CCTV Reviews</h6>
    </div>

    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered" id="cctv-reviews" width="100%" cellspacing="0" data-ordering="false">
          <thead>
            <tr>
              <th>Transaction No.</th>
              <th>Fullname</th>
              <th>Date</th>
              <th>Time</th>
              <th>Exact Location</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $cctvReviewResults->fetch_assoc()) { 
              $time = explode(" to ", $row['time']);
              $fromTime = date("H:i:s", strtotime($time[0], time()));
              $toTime = date("H:i:s", strtotime($time[1], time()));
            ?>
              <tr>
                <td><?= $row['transaction_number'] ?></td>
                <td><?= $row['name'] ?></td>
                <td><?= $row['date'] ?></td>
                <td><?= $row['time'] ?></td>
                <td><?= $row['exact_location'] ?></td>
                <td class="<?= $row['status'] == 'pending' || $row['status'] == 'declined' ? 'table-danger text-danger' : 'table-success text-success' ?>"><?= ucfirst($row['status']) ?></td>
                <td>
                  <a class="btn btn-info px-auto" href="#" title="View" onClick="openViewCCTVReviewModal({
                    date: '<?= $row['date'] ?>', 
                    from_time: '<?= $fromTime ?>', 
                    to_time: '<?= $toTime ?>', 
                    exact_location: '<?= $row['exact_location'] ?>', 
                    number_of_cctv: '<?= $row['number_of_cctv'] ?>', 
                    purpose_of_request: '<?= $row['purpose_of_request'] ?>', 
                    transaction_number: '<?= $row['transaction_number'] ?>'
                  })">
                    <i class="fa fa-eye"></i>
                  </a>
                  <?php if ($row['status'] == 'pending' && $adminInfo['role'] == '1') { ?>
                    <a class="btn btn-success px-auto" href="#" onClick="openApproveCCTVReviewModal({
                      user_id: '<?= $row['user_id'] ?>', 
                      name: '<?= $row['name'] ?>', 
                      date: '<?= $row['date'] ?>', 
                      time: '<?= $row['time'] ?>', 
                      exact_location: '<?= $row['exact_location'] ?>', 
                      transaction_number: '<?= $row['transaction_number'] ?>'
                    })">
                      <i class="fa fa-thumbs-up"></i>
                    </a>
                    <a class="btn btn-danger px-auto" href="#" onClick="openDeclineCCTVReviewModal({
                      user_id: '<?= $row['user_id'] ?>',
                      name: '<?= $row['name'] ?>',  
                      date: '<?= $row['date'] ?>', 
                      time: '<?= $row['time'] ?>', 
                      exact_location: '<?= $row['exact_location'] ?>',
                      transaction_number: '<?= $row['transaction_number'] ?>'
                    })">
                      <i class="fa fa-thumbs-down"></i>
                    </a>
                  <?php } ?>
                </td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<!-- End Main Content -->



<!-- Approve Modal-->
<div class="modal fade" id="approveCCTVReviewModal" tabindex="-1" role="dialog" aria-labelledby="approveModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="approveModalLabel">Approve this CCTV Review?</h5>
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>

      <form method="POST" id="CCTVReviewFormApprove">
        <div class="modal-body">
          <p>Are you sure you really want to approve this CCTV Review?</p>
          <hr>

          <div class="form-group">
            <label class="form-control-label">Date and Time of Pickup</label>
            <input type="datetime-local" name="date_and_time" class="form-control" required>
          </div>

          <input type="hidden" name="transaction_number" value="">
          <input type="hidden" name="exact_location" value="">
          <input type="hidden" name="name" value="">
          <input type="hidden" name="date" value="">
          <input type="hidden" name="time" value="">
          <input type="hidden" name="user_id" value="">
        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
          <button type="submit" name="approveCCTVReviewBtn" class="btn btn-success">Approve</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- End Approve Modal-->



<!-- Decline Modal-->
<div class="modal fade" id="declineCCTVReviewModal" tabindex="-1" role="dialog" aria-labelledby="declineModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="declineModalLabel">Decline this CCTV Review?</h5>
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>

      <form method="POST" id="CCTVReviewFormDecline">
        <div class="modal-body">
          <p>Are you sure you really want to decline this CCTV Review?</p>

          <div class="form-group">
            <label class="form-control-label">Reason <span class="text-bold text-danger">*</span></label>
            <textarea name="reason" id="" cols="30" rows="5" class="form-control" placeholder="Reason for declining..." required></textarea>
          </div>

          <input type="hidden" name="transaction_number" value="">
          <input type="hidden" name="exact_location" value="">
          <input type="hidden" name="name" value="">
          <input type="hidden" name="date" value="">
          <input type="hidden" name="time" value="">
          <input type="hidden" name="user_id" value="">
        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
          <button type="submit" name="declineCCTVReviewBtn" class="btn btn-success">Decline</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- End Decline Modal-->



<!-- View Modal -->
<div class="modal fade" id="viewCCTVReviewRequestModal" tabindex="-1" role="dialog" aria-labelledby="viewCCTVReviewRequest" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="viewCCTVReviewRequest">CCTV Request Info</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form id="viewCCTVReviewRequestForm" method="POST" autocomplete="off">
        <div class="modal-body">

          <div class="form-group">
            <label> Exact Location </label>
            <input type="text" name="exact_location" class="form-control" placeholder="Full address" disabled>
          </div>

          <div class="row">
            <div class="col">
              <div class="form-group">
                <label> Exact Date </label>
                <input type="text" name="exact_date" class="form-control" disabled>
              </div>
            </div>

            <div class="col">
              <div class="form-group">
                <label> Number of CCTV </label>
                <input type="text" name="number_of_cctv" class="form-control" disabled>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col">
              <div class="form-group">
                <label> From time </label>
                <input type="time" name="from_time" class="form-control" disabled>
              </div>
            </div>

            <div class="col">
              <div class="form-group">
                <label> To time </label>
                <input type="time" name="to_time" class="form-control" disabled>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label> Purpose of Request </label>
            <textarea rows="5" name="purpose_of_request" class="form-control" readonly disabled></textarea>
          </div>

        </div>
      </form>
    </div>
  </div>
</div>
<!-- End View Modal -->