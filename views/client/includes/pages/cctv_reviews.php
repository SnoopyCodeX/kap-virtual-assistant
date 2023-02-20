<?php
// Cancel CCTV Review Request
if (isset($_POST['cancelCCTVReviewBtn'])) {
  $transactionNumber = $conn->real_escape_string($_POST['transaction_number']);
  $userID = $conn->real_escape_string($_POST['user_id']);

  $deleteCCTVRequestResult = $conn->query("DELETE FROM $cctvReviewsTable WHERE transaction_number='$transactionNumber' AND user_id='$userID'");

  if ($deleteCCTVRequestResult) {
    $deleteTransactionResult = $conn->query("DELETE FROM $transactionsTable WHERE transaction_number='$transactionNumber' AND user_id='$userID'");

    if ($deleteTransactionResult) {
      $message = "Your cctv review request has been successfully canceled!";
      $hasError = false;
      $hasSuccess = true;
    } else {
      $message = "Your cctv review request has been successfully canceled, however, the system failed to delete the transaction. Kindly take a photo of this message and report it to the admins.\n\n<strong>Reason:\n" . $conn->error . "</strong>";
      $hasError = true;
      $hasSuccess = false;
    }
  } else {
    $message = "Your cctv review request was not canceled. Kindly take a photo of this message and report it to the admins.\n\n<strong>Reason:\n" . $conn->error . "</strong>";
    $hasError = true;
    $hasSuccess = false;
  }
}

// Create new CCTV Review Request
if (isset($_POST['createNewCCTVReviewRequestBtn'])) {
  $exactLocation = $conn->real_escape_string($_POST['exact_location']);
  $exactDate = $conn->real_escape_string($_POST['exact_date']);
  $numberOfCCTV = $conn->real_escape_string($_POST['number_of_cctv']);
  $purposeOfRequest = $conn->real_escape_string($_POST['purpose_of_request']);
  $fromTime = $conn->real_escape_string($_POST['from_time']);
  $toTime = $conn->real_escape_string($_POST['to_time']);

  $createdAt = date("Y-m-d h:i:s.u", time());
  $dateCreated = date("F d, Y h:i A", time());
  $exactDate = date("F d, Y", strtotime($exactDate, time()));
  $fromTime = date("h:i A", strtotime($fromTime, time()));
  $toTime = date("h:i A", strtotime($toTime, time()));
  $time = "$fromTime to $toTime";

  $checkExistingApprovedRequest = $conn->query("SELECT * FROM $cctvReviewsTable WHERE 
    exact_location='$exactLocation' AND 
    date='$exactDate' AND
    time='$time' AND
    created_at >= '" . date("Y-m-d", strtotime($createdAt)) . "' AND
    created_at < ('" . date("Y-m-d", strtotime($createdAt)) . "' + INTERVAL 1 DAY) AND
    status='approved'
  ");

  $checkExistingPendingRequest = $conn->query("SELECT * FROM $cctvReviewsTable WHERE 
    exact_location='$exactLocation' AND 
    date='$exactDate' AND
    time='$time' AND
    status='pending'
  ");

  if(strtotime($toTime) <= strtotime($fromTime)) {
    $message = "You have entered an invalid range of time! <strong>From time</strong> should be lower than <strong>To time</strong>.";
    $hasError = true;
    $hasSuccess = false;
  } else if ($checkExistingApprovedRequest->num_rows > 0) {
    $message = "You have already requested the same cctv review on this day. You may only request <strong>1 cctv review per day</strong> on the same location and time.";
    $hasError = true;
    $hasSuccess = false;
  } else if ($checkExistingPendingRequest->num_rows > 0) {
    $message = "You already have a pending cctv review request for '<strong>$numberOfCCTV</strong>' at <strong>$exactLocation</strong> from <strong>$fromTime</strong> to <strong>$toTime</strong> on the <strong>" . date("jS", strtotime($exactDate)) . " day</strong> of <strong>" . date("F Y", strtotime($exactDate)) . "</strong>.";
    $hasError = true;
    $hasSuccess = false;
  } else {
    $createCCTVRequestResult = $conn->query("INSERT INTO $cctvReviewsTable(
      user_id, 
      date,
      time,
      exact_location,
      number_of_cctv,
      purpose_of_request,
      status,
      transaction_number,
      created_at
    ) VALUES(
      '${userInfo['id']}',
      '$exactDate',
      '$time',
      '$exactLocation',
      '$numberOfCCTV',
      '$purposeOfRequest',
      'pending',
      'KAP-TEMP-${userInfo['id']}',
      '$createdAt'
    )");

    if ($createCCTVRequestResult) {
      $lastInsertCCTVRequestID = $conn->insert_id;

      $createTransactionResult = $conn->query("INSERT INTO $transactionsTable(
        user_id,
        transaction_number,
        transaction_type,
        date_created,
        status,
        created_at
      ) VALUES(
        '${userInfo['id']}',
        'KAP-TEMP-${userInfo['id']}',
        'CCTV Review Request',
        '$dateCreated',
        'pending',
        '$createdAt'
      )");

      if ($createTransactionResult) {
        $lastInsertTransactionID = $conn->insert_id;
        $transactionNumber = strtoupper("KAP-${userInfo['id']}${lastInsertTransactionID}${lastInsertCCTVRequestID}" . uniqid("", false));

        $updateCCTVRequestsTableResult = $conn->query("UPDATE $cctvReviewsTable SET transaction_number='$transactionNumber' WHERE transaction_number='KAP-TEMP-${userInfo['id']}' AND user_id='${userInfo['id']}'");
        $updateTransactionsTableResult = $conn->query("UPDATE $transactionsTable SET transaction_number='$transactionNumber' WHERE transaction_number='KAP-TEMP-${userInfo['id']}' AND user_id='${userInfo['id']}'");

        if ($updateCCTVRequestsTableResult && $updateTransactionsTableResult) {
          $message = "Your cctv review request has been sent successfully! You will be sent a <strong>text message notification</strong> to your <strong>mobile number (" . str_replace('+63', '0', $userInfo['contact_number']) . ")</strong> regarding the status of your request, thank you!";
          $hasError = false;
          $hasSuccess = true;
        } else {
          $message = "Your cctv review request has been sent successfully! However, the system failed to generate a transaction number for this request. Kindly take a photo of this message and report it to the admins.\n\n<strong>Reason:\n" . $conn->error . "</strong>";
          $hasError = true;
          $hasSuccess = false;
        }
      } else {
        $message = "Your cctv review request has been sent successfully! However, the system failed to generate a transaction number for this request. Kindly take a photo of this message and report it to the admins.\n\n<strong>Reason:\n" . $conn->error . "</strong>";
        $hasError = true;
        $hasSuccess = false;
      }
    } else {
      $message = "Your cctv review request was not sent to the system! Kindly take a photo of this message and report it to the admins.\n\n<strong>Reason:\n" . $conn->error . "</strong>";
      $hasError = true;
      $hasSuccess = false;
    }
  }
}

// Fetch all cctv requests
$cctvReviewResults = $conn->query("SELECT * FROM $cctvReviewsTable WHERE user_id='${userInfo['id']}' ORDER BY created_at DESC");
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
      <h6 class="m-0 font-weight-bold" style="color:#223D3C;"><i class="fa fa-camera"></i> CCTV Reviews &nbsp;&nbsp;&nbsp;
        <button type="button" class="btn btn-primary px-auto" style="background-color:#223D3C; border-color:#223D3C;" data-toggle="modal" data-target="#createCCTVReviewRequestModal">
          <i class="fa fa-plus-circle"></i>
          Request new
        </button>
      </h6>
    </div>

    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered" id="cctv-reviews" width="100%" cellspacing="0">
          <thead>
            <tr>
              <th>Transaction No.</th>
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
                  <?php if ($row['status'] == 'pending') { ?>
                    <a class="btn btn-danger px-auto" href="#" title="Cancel" onClick="openCancelCCTVReviewModal({
                      user_id: '<?= $row['user_id'] ?>',
                      transaction_number: '<?= $row['transaction_number'] ?>'
                    })">
                      <i class="fa fa-ban"></i>
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



<!-- Decline Modal-->
<div class="modal fade" id="cancelCCTVReviewModal" tabindex="-1" role="dialog" aria-labelledby="cancelModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="cancelModalLabel">Decline this CCTV Review?</h5>
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>

      <div class="modal-body">Are you sure you really want to cancel this CCTV Review?</div>

      <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>

        <form method="POST" id="CCTVReviewFormcancel">

          <input type="hidden" name="transaction_number" value="">
          <input type="hidden" name="user_id" value="">
          <button type="submit" name="cancelCCTVReviewBtn" class="btn btn-success">Yes</button>

        </form>

      </div>
    </div>
  </div>
</div>
<!-- End Decline Modal-->



<!-- Create Modal -->
<div class="modal fade" id="createCCTVReviewRequestModal" tabindex="-1" role="dialog" aria-labelledby="createCCTVReviewRequest" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="createCCTVReviewRequest">Request CCTV Review</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form id="createCCTVReviewRequestForm" method="POST" autocomplete="off">
        <div class="modal-body">

          <div class="form-group">
            <label> Exact Location </label>
            <input type="text" name="exact_location" class="form-control" placeholder="Full address" required>
          </div>

          <div class="row">
            <div class="col">
              <div class="form-group">
                <label> Exact Date </label>
                <input type="date" name="exact_date" class="form-control" required>
              </div>
            </div>

            <div class="col">
              <div class="form-group">
                <label> Number of CCTV </label>
                <select name="number_of_cctv" class="form-control" required>
                  <option value="CCTV 1" selected>CCTV 1</option>
                  <option value="CCTV 2">CCTV 2</option>
                  <option value="CCTV 3">CCTV 3</option>
                  <option value="CCTV 4">CCTV 4</option>
                  <option value="CCTV 5">CCTV 5</option>
                  <option value="CCTV 6">CCTV 6</option>
                  <option value="CCTV 7">CCTV 7</option>
                  <option value="CCTV 8">CCTV 8</option>
                  <option value="CCTV 9">CCTV 9</option>
                  <option value="CCTV 10">CCTV 10</option>
                  <option value="CCTV 11">CCTV 11</option>
                  <option value="CCTV 12">CCTV 12</option>
                  <option value="CCTV 13">CCTV 13</option>
                  <option value="CCTV 14">CCTV 14</option>
                  <option value="CCTV 15">CCTV 15</option>
                  <option value="CCTV 16">CCTV 16</option>
                  <option value="CCTV 17">CCTV 17</option>
                  <option value="CCTV 18">CCTV 18</option>
                  <option value="CCTV 19">CCTV 19</option>
                  <option value="CCTV 20">CCTV 20</option>
                  <option value="CCTV 21">CCTV 21</option>
                  <option value="CCTV 22">CCTV 22</option>
                  <option value="CCTV 23">CCTV 23</option>
                  <option value="CCTV 24">CCTV 24</option>
                  <option value="CCTV 25">CCTV 25</option>
                  <option value="CCTV 26">CCTV 26</option>
                </select>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col">
              <div class="form-group">
                <label> From time </label>
                <input type="time" name="from_time" class="form-control" required>
              </div>
            </div>

            <div class="col">
              <div class="form-group">
                <label> To time </label>
                <input type="time" name="to_time" class="form-control" required>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label> Purpose of Request </label>
            <textarea rows="5" name="purpose_of_request" class="form-control" placeholder="Write purpose here..." required></textarea>
          </div>

        </div>

        <hr class="mt-0 mb-3" />
        <div class="px-3 row">
          <div class="col">
            <div class="form-group">
              <button type="button" class="btn btn-danger btn-block" data-dismiss="modal" aria-label="Cancel">Cancel</button>
            </div>
          </div>

          <div class="col">
            <div class="form-group">
              <button style="background-color:#223D3C; border-color:#223D3C;" type="submit" name="createNewCCTVReviewRequestBtn" class="btn btn-success btn-block">Request</button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- End Create Modal -->



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