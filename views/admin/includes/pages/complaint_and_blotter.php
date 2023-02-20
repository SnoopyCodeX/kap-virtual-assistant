<?php

if (isset($_POST['approveComplaintBtn'])) {
  $userID = $conn->real_escape_string($_POST['user_id']);
  $transactionNumber = $conn->real_escape_string($_POST['transaction_number']);
  $natureOfComplaint = lcfirst($conn->real_escape_string($_POST['nature_of_complaint']));
  $datetime = $conn->real_escape_string($_POST['date_and_time']);

  $userInfoResult = $conn->query("SELECT * FROM $usersTable WHERE id='$userID'");
  $userInfo = $userInfoResult->fetch_assoc();
  $name = explode(",", trim($userInfo['fullname'], " "))[0];
  $contactNumber = $userInfo['contact_number'];

  $updateTransactionTableResult = $conn->query("UPDATE $transactionsTable SET status='approved' WHERE transaction_number='$transactionNumber'");
  $updateComplaintsTableResult = $conn->query("UPDATE $complaintsTable SET status='approved' WHERE transaction_number='$transactionNumber'");
  $smsTextResult = SMSUtil::sendSms(
    $contactNumber, 
    "Mr./Mrs. $name, your complaint for '$natureOfComplaint' has been approved! Check the schedules page for the schedule of the hearing\n\n~ASK.Kap"
  );

  if ($updateComplaintsTableResult && $updateTransactionTableResult && $smsTextResult != null) {
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
      'Complaint request for $natureOfComplaint by ${userInfo['fullname']}',
      '1',
      '$startDatetime',
      '$endDatetime',
      '0',
      'Baranggay Hall'
    )");

    $hasError = false;
    $hasSuccess = true;
    $message = "The <strong>$natureOfComplaint</strong> has been approved and <strong>Mr./Mrs. $name</strong> has been notified thru SMS Text.";
  } else if ($updateComplaintsTableResult && $updateTransactionTableResult && $smsTextResult == null) {
    $hasError = false;
    $hasSuccess = true;
    $message = "The <strong>$natureOfComplaint</strong> has been approved but <strong>Failed to send SMS text to Mr./Mrs. $name.</strong>";
  } else {
    $hasError = true;
    $hasSuccess = false;
    $message = "Something went wrong while updating the database" . ($smsTextResult != null ? " but  <strong>Mr./Mrs. $name</strong> has been notified thru <strong>SMS Text</strong>." : ".");
  }
}



if (isset($_POST['declineComplaintBtn'])) {
  $userID = $conn->real_escape_string($_POST['user_id']);
  $transactionNumber = $conn->real_escape_string($_POST['transaction_number']);
  $natureOfComplaint = lcfirst($conn->real_escape_string($_POST['nature_of_complaint']));
  $reason = $conn->real_escape_string($_POST['reason']);

  $userInfoResult = $conn->query("SELECT * FROM $usersTable WHERE id='$userID'");
  $userInfo = $userInfoResult->fetch_assoc();
  $name = explode(",", trim($userInfo['fullname'], " "))[0];
  $contactNumber = $userInfo['contact_number'];

  $updateTransactionTableResult = $conn->query("UPDATE $transactionsTable SET status='declined' WHERE transaction_number='$transactionNumber'");
  $updateComplaintsTableResult = $conn->query("UPDATE $complaintsTable SET status='declined' WHERE transaction_number='$transactionNumber'");
  $smsTextResult = SMSUtil::sendSms(
    $contactNumber, 
    "Mr./Mrs. $name, your complaint '$natureOfComplaint' has been declined. \nReason: $reason\n\n~ASK.Kap"
  );

  if ($updateComplaintsTableResult && $updateTransactionTableResult && $smsTextResult != null) {
    $hasError = false;
    $hasSuccess = true;
    $message = "The <strong>$natureOfComplaint</strong> has been declined and <strong>Mr./Mrs. $name</strong> has been notified thru SMS Text.";
  } else if ($updateComplaintsTableResult && $updateTransactionTableResult && $smsTextResult == null) {
    $hasError = false;
    $hasSuccess = true;
    $message = "The <strong>$natureOfComplaint</strong> has been declined but <strong>Failed to send SMS text to Mr./Mrs. $name</strong>";
  } else {
    $hasError = true;
    $hasSuccess = false;
    $message = "Something went wrong while updating the database" . ($smsTextResult != null ? " but <strong>Mr./Mrs. $name</strong> has been notified thru <strong>SMS Text</strong>." : ".");
  }
}



if (isset($_POST['approveBlotterBtn'])) {
  $userID = $conn->real_escape_string($_POST['user_id']);
  $transactionNumber = $conn->real_escape_string($_POST['transaction_number']);
  $natureOfBlotter = lcfirst($conn->real_escape_string($_POST['nature_of_blotter']));
  $datetime = $conn->real_escape_string($_POST['date_and_time']);

  $userInfoResult = $conn->query("SELECT * FROM $usersTable WHERE id='$userID'");
  $userInfo = $userInfoResult->fetch_assoc();
  $name = explode(",", trim($userInfo['fullname'], " "))[0];
  $contactNumber = $userInfo['contact_number'];

  $updateTransactionTableResult = $conn->query("UPDATE $transactionsTable SET status='approved' WHERE transaction_number='$transactionNumber'");
  $updateBlottersTableResult = $conn->query("UPDATE $blottersTable SET status='approved' WHERE transaction_number='$transactionNumber'");
  $smsTextResult = SMSUtil::sendSms(
    $contactNumber, 
    "Mr./Mrs. $name, your blotter for '$natureOfBlotter' has been approved! Check the schedules page for the schedule of the hearing\n\n~ASK.Kap"
  );

  if ($updateBlottersTableResult && $updateTransactionTableResult && $smsTextResult != null) {
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
      'Blotter request for $natureOfBlotter by ${userInfo['fullname']}',
      '1',
      '$startDatetime',
      '$endDatetime',
      '0',
      'Baranggay Hall'
    )");

    $hasError = false;
    $hasSuccess = true;
    $message = "The <strong>$natureOfBlotter</strong> has been approved and <strong>Mr./Mrs. $name</strong> has been notified thru SMS Text.";
  } else if ($updateBlottersTableResult && $updateTransactionTableResult && $smsTextResult == null) {
    $hasError = false;
    $hasSuccess = true;
    $message = "The <strong>$natureOfBlotter</strong> has been approved but <strong>Failed to send SMS text to Mr./Mrs. $name.</strong>";
  } else {
    $hasError = true;
    $hasSuccess = false;
    $message = "Something went wrong while updating the database" . ($smsTextResult != null ? " but  <strong>Mr./Mrs. $name</strong> has been notified thru <strong>SMS Text</strong>." : ".");
  }
}



if (isset($_POST['declineBlotterBtn'])) {
  $userID = $conn->real_escape_string($_POST['user_id']);
  $transactionNumber = $conn->real_escape_string($_POST['transaction_number']);
  $natureOfBlotter = lcfirst($conn->real_escape_string($_POST['nature_of_blotter']));
  $reason = $conn->real_escape_string($_POST['reason']);

  $userInfoResult = $conn->query("SELECT * FROM $usersTable WHERE id='$userID'");
  $userInfo = $userInfoResult->fetch_assoc();
  $name = explode(",", trim($userInfo['fullname'], " "))[0];
  $contactNumber = $userInfo['contact_number'];

  $updateTransactionTableResult = $conn->query("UPDATE $transactionsTable SET status='declined' WHERE transaction_number='$transactionNumber'");
  $updateBlottersTableResult = $conn->query("UPDATE $blottersTable SET status='declined' WHERE transaction_number='$transactionNumber'");
  $smsTextResult = SMSUtil::sendSms(
    $contactNumber, 
    "Mr./Mrs. $name, your blotter for '$natureOfBlotter' has been declined. \nReason: $reason\n\n~ASK.Kap"
  );

  if ($updateBlottersTableResult && $updateTransactionTableResult && $smsTextResult != null) {
    $hasError = false;
    $hasSuccess = true;
    $message = "The <strong>$natureOfBlotter</strong> has been declined and <strong>Mr./Mrs. $name</strong> has been notified thru SMS Text.";
  } else if ($updateBlottersTableResult && $updateTransactionTableResult && $smsTextResult == null) {
    $hasError = false;
    $hasSuccess = true;
    $message = "The <strong>$natureOfBlotter</strong> has been declined but <strong>Failed to send SMS text to Mr./Mrs. $name</strong>";
  } else {
    $hasError = true;
    $hasSuccess = false;
    $message = "Something went wrong while updating the database" . ($smsTextResult != null ? " but <strong>Mr./Mrs. $name</strong> has been notified thru <strong>SMS Text</strong>." : ".");
  }
}



// Fetch all complaints and blotters
$complaintsResult = $conn->query("SELECT * FROM $complaintsTable ORDER BY created_at DESC");
$blottersResult = $conn->query("SELECT * FROM $blottersTable ORDER BY created_at DESC");
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
      <h6 class="m-0 font-weight-bold" style="color:#223D3C;"><i class="fa fa-bullhorn"></i> Complaints </h6>
    </div>

    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered align-middle" id="complaints" width="100%" cellspacing="0" data-ordering="false">
          <thead>
            <tr>
              <th>Transaction No.</th>
              <th>Case Title</th>
              <th>Date Filed</th>
              <th>Complaint Title</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $complaintsResult->fetch_assoc()) {
              $checkEvidenceResult = $conn->query("SELECT * FROM $evidencesTable WHERE complaint_blotter_id='${row['id']}'");
              $hasEvidence = $checkEvidenceResult->num_rows > 0;
            ?>
              <tr>
                <td><?= $row['transaction_number'] ?></td>
                <td><?= $row['case_title'] ?></td>
                <td><?= $row['date_filed'] ?></td>
                <td><?= $row['complaint_title'] ?></td>
                <td class="<?= $row['status'] == 'pending' || $row['status'] == 'declined' ? 'table-danger text-danger' : 'table-success text-success' ?>"><?= ucfirst($row['status']) ?></td>
                <td>
                  <a class="btn btn-info px-auto" href="#" onClick="openViewComplaintModal({
                    complaint_blotter_id: '<?= $row['id'] ?>',
                    download_id: '<?= kap_encrypt($row['id']) ?>',
                    nature_of_complaint: '<?= $row['nature_of_complaint'] ?>',
                    who: '<?= $row['who'] ?>',
                    what: '<?= $row['what'] ?>',
                    date: '<?= date('F d, Y' , strtotime(str_replace("at ", "", $row['when']), time())) ?>',
                    time: '<?= date('h:i A' , strtotime(str_replace("at ", "", $row['when']), time())) ?>',
                    where: '<?= $row['where'] ?>',
                    how: '<?= $row['how'] ?>',
                    hasEvidence: <?= $hasEvidence ? 'true' : 'false' ?>
                  })">
                    <i class="fa fa-eye"></i>
                  </a>
                  <?php if ($row['status'] == 'pending' && $adminInfo['role'] == '1') { ?>
                    <a class="btn btn-success px-auto" href="#" onClick="openApproveComplaintModal({
                      user_id: '<?= $row['user_id'] ?>', 
                      nature_of_complaint: '<?= $row['nature_of_complaint'] ?>', 
                      transaction_number: '<?= $row['transaction_number'] ?>'
                    })">
                      <i class="fa fa-thumbs-up"></i>
                    </a>
                    <a class="btn btn-danger px-auto" href="#" onClick="openDeclineComplaintModal({
                      user_id: '<?= $row['user_id'] ?>', 
                      nature_of_complaint: '<?= $row['nature_of_complaint'] ?>', 
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

  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold" style="color:#223D3C;"><i class="fa fa-bullhorn"></i> Blotters </h6>
    </div>

    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered align-middle" id="blotters" width="100%" cellspacing="0">
          <thead>
            <tr>
              <th>Transaction No.</th>
              <th>Case Title</th>
              <th>Date Filed</th>
              <th>Blotter Title</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $blottersResult->fetch_assoc()) {
              $checkEvidenceResult = $conn->query("SELECT * FROM $evidencesTable WHERE complaint_blotter_id='${row['id']}'");
              $hasEvidence = $checkEvidenceResult->num_rows > 0;
            ?>
              <tr>
                <td><?= $row['transaction_number'] ?></td>
                <td><?= $row['case_title'] ?></td>
                <td><?= $row['date_filed'] ?></td>
                <td><?= $row['blotter_title'] ?></td>
                <td class="<?= $row['status'] == 'pending' || $row['status'] == 'declined' ? 'table-danger text-danger' : 'table-success text-success' ?>"><?= ucfirst($row['status']) ?></td>
                <td>
                  <a class="btn btn-info px-auto" href="#" onClick="openViewBlotterModal({
                    complaint_blotter_id: '<?= $row['id'] ?>',
                    download_id: '<?= kap_encrypt($row['id']) ?>',
                    nature_of_blotter: '<?= $row['nature_of_blotter'] ?>',
                    who: '<?= $row['who'] ?>',
                    what: '<?= $row['what'] ?>',
                    date: '<?= date('F d, Y' , strtotime(str_replace("at ", "", $row['when']), time())) ?>',
                    time: '<?= date('h:i A' , strtotime(str_replace("at ", "", $row['when']), time())) ?>',
                    where: '<?= $row['where'] ?>',
                    how: '<?= $row['how'] ?>',
                    hasEvidence: <?= $hasEvidence ? 'true' : 'false' ?>
                  })">
                    <i class="fa fa-eye"></i>
                  </a>
                  <?php if ($row['status'] == 'pending') { ?>
                    <a class="btn btn-success px-auto" href="#" onClick="openApproveBlotterModal({
                      user_id: '<?= $row['user_id'] ?>', 
                      nature_of_blotter: '<?= $row['nature_of_blotter'] ?>', 
                      transaction_number: '<?= $row['transaction_number'] ?>'
                    })">
                      <i class="fa fa-thumbs-up"></i>
                    </a>
                    <a class="btn btn-danger px-auto" href="#" onClick="openDeclineBlotterModal({
                      user_id: '<?= $row['user_id'] ?>', 
                      nature_of_blotter: '<?= $row['nature_of_blotter'] ?>', 
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



<!-- Approve Complaint Modal-->
<div class="modal fade" id="approveComplaintModal" tabindex="-1" role="dialog" aria-labelledby="approveModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="approveModalLabel">Approve this complaint?</h5>
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>

      <form method="POST" id="complaintFormApprove">
        <div class="modal-body">
          <p>Are you sure you really want to approve this complaint?</p>

          <div class="form-group">
            <label class="form-control-label">Date and Time of Hearing</label>
            <input type="datetime-local" name="date_and_time" class="form-control" required>
          </div>

          <input type="hidden" name="transaction_number" value="">
          <input type="hidden" name="nature_of_complaint" value="">
          <input type="hidden" name="user_id" value="">
        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
          <button type="submit" name="approveComplaintBtn" class="btn btn-success">Approve</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- End Approve Modal-->



<!-- Decline Complaint Modal-->
<div class="modal fade" id="declineComplaintModal" tabindex="-1" role="dialog" aria-labelledby="declineModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="declineModalLabel">Decline this complaint?</h5>
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>

      <form method="POST" id="complaintFormDecline">
        <div class="modal-body">
          <p>Are you sure you really want to decline this complaint?</p>

          <div class="form-group">
            <label class="form-control-label">Reason <span class="text-bold text-danger">*</span></label>
            <textarea name="reason" id="" cols="30" rows="5" class="form-control" placeholder="Reason for declining..." required></textarea>
          </div>

          <input type="hidden" name="transaction_number" value="">
          <input type="hidden" name="nature_of_complaint" value="">
          <input type="hidden" name="user_id" value="">
        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
          <button type="submit" name="declineComplaintBtn" class="btn btn-success">Decline</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- End Decline Complaint Modal-->



<!-- Approve Blotter Modal-->
<div class="modal fade" id="approveBlotterModal" tabindex="-1" role="dialog" aria-labelledby="approveModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="approveModalLabel">Approve this blotter?</h5>
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>

      <form method="POST" id="blotterFormApprove">
        <div class="modal-body">
          <p>Are you sure you really want to approve this blotter?</p>

          <div class="form-group">
            <label class="form-control-label">Date and Time of Hearing</label>
            <input type="datetime-local" name="date_and_time" class="form-control" required>
          </div>

          <input type="hidden" name="transaction_number" value="">
          <input type="hidden" name="nature_of_blotter" value="">
          <input type="hidden" name="user_id" value="">
        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
          <button type="submit" name="approveBlotterBtn" class="btn btn-success">Approve</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- End Approve Blotter Modal-->



<!-- Decline Blotter Modal-->
<div class="modal fade" id="declineBlotterModal" tabindex="-1" role="dialog" aria-labelledby="declineModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="declineModalLabel">Decline this blotter?</h5>
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>

      <form method="POST" id="blotterFormDecline">
        <div class="modal-body">
          <p>Are you sure you really want to decline this blotter?</p>

          <div class="form-group">
            <label class="form-control-label">Reason <span class="text-bold text-danger">*</span></label>
            <textarea name="reason" id="" cols="30" rows="5" class="form-control" placeholder="Reason for declining..." required></textarea>
          </div>

          <input type="hidden" name="transaction_number" value="">
          <input type="hidden" name="nature_of_blotter" value="">
          <input type="hidden" name="user_id" value="">
        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
          <button type="submit" name="declineBlotterBtn" class="btn btn-success">Decline</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- End Decline Blotter Modal-->



<!-- View Complaint Modal -->
<div class="modal fade" id="viewComplaintModal" tabindex="-1" role="dialog" aria-labelledby="viewComplaintModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="viewComplaintModalLabel">Complaint Info</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form id="viewComplaintForm" method="POST" novalidate>
        <div class="modal-body">

          <div class="form-group">
            <label> Who </label>
            <input type="text" name="who" class="form-control" disabled>
          </div>

          <div class="form-group">
            <label> What </label>
            <input type="text" name="what" class="form-control" disabled>
          </div>

          <div class="row">
            <div class="col">
              <div class="form-group">
                <label> Date (When it happened) </label>
                <input type="text" name="date" class="form-control" disabled>
              </div>
            </div>

            <div class="col">
              <div class="form-group">
                <label> Time (When it happened) </label>
                <input type="text" name="time" class="form-control" disabled>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label> Where </label>
            <input type="text" name="where" class="form-control" disabled>
          </div>

          <div class="form-group">
            <label> How </label>
            <textarea name="how" class="form-control" rows="5" readonly disabled></textarea>
          </div>

          <div class="form-group">
            <label> Nature Of Complaint </label>
            <input type="text" name="nature_of_complaint" class="form-control" disabled>
          </div>

          <div class="form-group">
            <input type="hidden" name="complaint_blotter_id" class="form-control">
          </div>

        </div>

        <div class="modal-footer d-block">
          <div class="alert alert-danger fade show d-none" id="" role="alert">
            <span class="text-danger text-center" id="message"><i class="fa fa-exclamation-triangle"></i> There is no provided evidence for this complaint</span>
          </div>

          <button type="button" name="downloadEvidenceBtn" class="btn btn-block btn-success d-none"><i class="fa fa-download"></i>&nbsp;&nbsp;Download Provided Evidence</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- End View Complaint Modal -->



<!-- View Blotter Modal -->
<div class="modal fade" id="viewBlotterModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Blotter Info</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form id="viewBlotterForm" method="POST" novalidate>
        <div class="modal-body">

          <div class="form-group">
            <label> Who </label>
            <input type="text" name="who" class="form-control" disabled>
          </div>

          <div class="form-group">
            <label> What </label>
            <input type="text" name="what" class="form-control" disabled>
          </div>

          <div class="row">
            <div class="col">
              <div class="form-group">
                <label> Date (When it happened) </label>
                <input type="text" name="date" class="form-control" disabled>
              </div>
            </div>

            <div class="col">
              <div class="form-group">
                <label> Time (When it happened) </label>
                <input type="text" name="time" class="form-control" disabled>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label> Where </label>
            <input type="text" name="where" class="form-control" disabled>
          </div>

          <div class="form-group">
            <label> How </label>
            <textarea name="how" class="form-control" rows="5" readonly disabled></textarea>
          </div>

          <div class="form-group">
            <label> Nature Of Blotter </label>
            <input type="text" name="nature_of_blotter" class="form-control" disabled>
          </div>

          <div class="form-group">
            <input type="hidden" name="complaint_blotter_id" class="form-control">
          </div>

        </div>

        <div class="modal-footer d-block">
          <div class="alert alert-danger fade show d-none" id="" role="alert">
            <span class="text-danger text-center" id="message"><i class="fa fa-exclamation-triangle"></i> There is no provided evidence for this blotter</span>
          </div>

          <button type="button" name="downloadEvidenceBtn" class="btn btn-block btn-success d-none"><i class="fa fa-download"></i>&nbsp;&nbsp;Download Provided Evidence</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- End View Blotter Modal -->