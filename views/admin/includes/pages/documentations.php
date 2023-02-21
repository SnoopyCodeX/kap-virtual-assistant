<?php

if (isset($_POST['approveDocumentationBtn'])) {
  $transactionNumber = $conn->real_escape_string($_POST['transaction_number']);
  $contactNumber = $conn->real_escape_string($_POST['contact_number']);
  $datetime = $conn->real_escape_string($_POST['date_and_time']);
  $type = $conn->real_escape_string($_POST['type']);
  $fullname = $conn->real_escape_string($_POST['name']);
  $name = explode(",", trim($fullname, " "))[0];

  $updateTransactionTableResult = $conn->query("UPDATE $transactionsTable SET status='approved' WHERE transaction_number='$transactionNumber'");
  $updateDocumentationTableResult = $conn->query("UPDATE $documentationsTable SET status='approved' WHERE transaction_number='$transactionNumber'");
  
  $smsTextResult = SMSUtil::sendSms(
    $contactNumber, 
    "Mr./Mrs. $name, your documentation request of '$type' has been approved. You may pick it up on " .
    date("F d, Y \\a\\t h:i A", strtotime($datetime)) . 
    ".\n\nYour transaction number is: $transactionNumber.\n\n~ASK.Kap"
  );

  if ($updateDocumentationTableResult && $updateTransactionTableResult && $smsTextResult != null) {
    $userResult = $conn->query("SELECT * FROM $usersTable WHERE fullname='$fullname'");
    $userID = $userResult->fetch_assoc()['id'];

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
      '$userID',
      'Claiming of $type request by $fullname',
      '1',
      '$startDatetime',
      '$endDatetime',
      '0',
      'Baranggay Hall'
    )");

    $hasError = false;
    $hasSuccess = true;
    $message = "Documentation request of <strong>$type</strong> has been approved and <strong>Mr./Mrs. $name</strong> has been notified thru SMS Text.";
  } else if ($updateDocumentationTableResult && $updateTransactionTableResult && $smsTextResult == null) {
    $hasError = false;
    $hasSuccess = true;
    $message = "Documentation request of <strong>$type</strong> has been approved but <strong>Failed to send SMS text to Mr./Mrs. $name.</strong>";

    $userResult = $conn->query("SELECT * FROM $usersTable WHERE fullname='$fullname'");
    $userID = $userResult->fetch_assoc()['id'];

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
      '$userID',
      'Claiming of $type request by $fullname',
      '1',
      '$startDatetime',
      '$endDatetime',
      '0',
      'Baranggay Hall'
    )");

    $hasError = false;
    $hasSuccess = true;
    $message = "Documentation request of <strong>$type</strong> has been approved and <strong>Mr./Mrs. $name</strong> has been notified thru SMS Text.";
  } else {
    $hasError = true;
    $hasSuccess = false;
    $message = "Something went wrong while updating the database" . ($smsTextResult != null ? " but  <strong>Mr./Mrs. $name</strong> has been notified thru <strong>SMS Text</strong>." : ".");
  }
}



if (isset($_POST['declineDocumentationBtn'])) {
  $transactionNumber = $conn->real_escape_string($_POST['transaction_number']);
  $contactNumber = $conn->real_escape_string($_POST['contact_number']);
  $reason = $conn->real_escape_string($_POST['reason']);
  $type = $conn->real_escape_string($_POST['type']);
  $name = $conn->real_escape_string($_POST['name']);
  $name = explode(",", trim($name, " "))[0];

  $updateTransactionTableResult = $conn->query("UPDATE $transactionsTable SET status='declined' WHERE transaction_number='$transactionNumber'");
  $updateDocumentationTableResult = $conn->query("UPDATE $documentationsTable SET status='declined' WHERE transaction_number='$transactionNumber'");

  $smsTextResult = SMSUtil::sendSms(
    $contactNumber,
    "Mr./Mrs. $name, your request of '$type' has been declined.\n\nReason: $reason\n\n~ASK.Kap"
  );

  if ($updateDocumentationTableResult && $updateTransactionTableResult && $smsTextResult != null) {
    $hasError = false;
    $hasSuccess = true;
    $message = "Documentation request of <strong>$type</strong> has been declined and <strong>Mr./Mrs. $name</strong> has been notified thru SMS Text.";
  } else if ($updateDocumentationTableResult && $updateTransactionTableResult && $smsTextResult == null) {
    $hasError = false;
    $hasSuccess = true;
    $message = "Documentation request of <strong>$type</strong> has been declined but <strong>Failed to send SMS text to Mr./Mrs. $name</strong>";
  } else {
    $hasError = true;
    $hasSuccess = false;
    $message = "Something went wrong while updating the database" . ($smsTextResult != null ? " but <strong>Mr./Mrs. $name</strong> has been notified thru <strong>SMS Text</strong>." : ".");
  }
}



// Fetch all documentation requests
$documentationsResult = $conn->query("SELECT d.*, u.fullname as name, u.contact_number as contact
  FROM $documentationsTable d 
  INNER JOIN $usersTable u 
  ON d.owner_id=u.id
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
      <h6 class="m-0 font-weight-bold" style="color:#223D3C;"><i class="fa fa-bookmark"></i> Documentations</h6>
    </div>

    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered" id="documentations" width="100%" cellspacing="0" data-ordering="false">
          <thead>
            <tr>
              <th>Transaction No.</th>
              <th>Name</th>
              <th>Contact</th>
              <th>Type</th>
              <th>Date Created</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $documentationsResult->fetch_assoc()) { ?>
              <tr>
                <td><?= $row['transaction_number'] ?></td>
                <td><?= $row['name'] ?></td>
                <td><?= $row['contact'] ?></td>
                <td><?= $row['type'] ?></td>
                <td><?= $row['date_created'] ?></td>
                <td class="<?= $row['status'] == 'pending' || $row['status'] == 'declined' ? 'table-danger text-danger' : 'table-success text-success' ?>"><?= ucfirst($row['status']) ?></td>
                <td>
                  <a class="btn btn-info px-auto" href="#" onClick="openViewDocumentationModal({
                    type: '<?= $row['type'] ?>', 
                    purpose_of_request: '<?= $row['purpose_of_request'] ?>'
                  })">
                    <i class="fa fa-eye"></i>
                  </a>
                  <?php if ($row['status'] == 'pending' && $adminInfo['role'] == '1') { ?>
                    <a class="btn btn-success px-auto" href="#" onClick="openApproveDocumentationModal({
                      name: '<?= $row['name'] ?>', 
                      type: '<?= $row['type'] ?>', 
                      transaction_number: '<?= $row['transaction_number'] ?>', 
                      contact_number: '<?= $row['contact'] ?>'
                    })">
                      <i class="fa fa-thumbs-up"></i>
                    </a>
                    <a class="btn btn-danger px-auto" href="#" onClick="openDeclineDocumentationModal({
                      name: '<?= $row['name'] ?>', 
                      type: '<?= $row['type'] ?>', 
                      transaction_number: '<?= $row['transaction_number'] ?>', 
                      contact_number: '<?= $row['contact'] ?>'
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
<div class="modal fade" id="approveDocumentationModal" tabindex="-1" role="dialog" aria-labelledby="approveModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="approveModalLabel">Approve this request?</h5>
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>

      <form method="POST" id="documentationFormApprove">
        <div class="modal-body">
          <p> Are you sure you really want to approve this documentation request? </p>
          <hr>

          <div class="form-group">
            <label class="form-control-label">Date and Time of Pickup</label>
            <input type="datetime-local" name="date_and_time" class="form-control" required>
          </div>

          <input type="hidden" name="transaction_number" value="">
          <input type="hidden" name="contact_number" value="">
          <input type="hidden" name="type" value="">
          <input type="hidden" name="name" value="">
        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
          <button type="submit" name="approveDocumentationBtn" class="btn btn-success">Approve</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- End Approve Modal-->


<!-- Decline Modal-->
<div class="modal fade" id="declineDocumentationModal" tabindex="-1" role="dialog" aria-labelledby="declineModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="declineModalLabel">Decline this request?</h5>
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>

      <form method="POST" id="documentationFormDecline">
        <div class="modal-body">
          <p>Are you sure you really want to decline this documentation request?</p>

          <div class="form-group">
            <label class="form-control-label">Reason <span class="text-bold text-danger">*</span></label>
            <textarea name="reason" id="" cols="30" rows="5" class="form-control" placeholder="Reason for declining..." required></textarea>
          </div>

          <input type="hidden" name="transaction_number" value="">
          <input type="hidden" name="contact_number" value="">
          <input type="hidden" name="type" value="">
          <input type="hidden" name="name" value="">
        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
          <button type="submit" name="declineDocumentationBtn" class="btn btn-success">Decline</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- End Decline Modal-->

<!-- View Modal -->
<div class="modal fade" id="viewDocumentationModal" tabindex="-1" role="dialog" aria-labelledby="viewDocumentation" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="viewDocumentation">Documentation Request Info</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form id="viewDocumentationForm" method="POST" autocomplete="off">
        <div class="modal-body">

          <div class="form-group">
            <label> Document Type </label>
            <input type="text" name="document_type" class="form-control" disabled>
          </div>

          <div class="form-group">
            <label> Purpose of Request </label>
            <textarea name="purpose_of_request" class="form-control" rows="5" spellcheck="false" readonly disabled></textarea>
          </div>

        </div>
      </form>
    </div>
  </div>
</div>
<!-- End View Modal -->