<?php
// Cancel documentation request
if (isset($_POST['cancelDocumentationBtn'])) {
  $transactionNumber = $conn->real_escape_string($_POST['transaction_number']);
  $userID = $conn->real_escape_string($_POST['user_id']);

  $deleteDocumentationRequestResult = $conn->query("DELETE FROM $documentationsTable WHERE transaction_number='$transactionNumber' AND owner_id='${userInfo['id']}'");

  if ($deleteDocumentationRequestResult) {
    $deleteTransactionResult = $conn->query("DELETE FROM $transactionsTable WHERE transaction_number='$transactionNumber' AND user_id='$userID'");

    if ($deleteTransactionResult) {
      $message = "Your documentation request has been successfully canceled!";
      $hasError = false;
      $hasSuccess = true;
    } else {
      $message = "Your documentation request has been successfully canceled, however, the system failed to delete the transaction. Kindly take a photo of this message and report it to the admins.\n\n<strong>Reason:\n" . $conn->error . "</strong>";
      $hasError = true;
      $hasSuccess = false;
    }
  } else {
    $message = "Your documentation request was not canceled. Kindly take a photo of this message and report it to the admins.\n\n<strong>Reason:\n" . $conn->error . "</strong>";
    $hasError = true;
    $hasSuccess = false;
  }
}

// Create documentation request
if (isset($_POST['createDocumentationBtn'])) {
  $type = $conn->real_escape_string($_POST['document_type']);
  $purposeOfRequest = $conn->real_escape_string($_POST['purpose_of_request']);
  $dateCreated = date('F d, Y h:i A', time());
  $contact = $userInfo['contact_number'];
  $createdAt = date('Y-m-d h:i:s.u', time());

  $checkExistingApprovedDocumentationRequest = $conn->query("SELECT * FROM $documentationsTable WHERE 
    owner_id='${userInfo['id']}' AND 
    type='$type' AND 
    created_at >= '" . date("Y-m-d", strtotime($createdAt)) . "' AND
    created_at < ('" . date("Y-m-d", strtotime($createdAt)) . "' + INTERVAL 1 DAY) AND
    status='approved'
  ");

  $checkExistingPendingDocumentationRequest = $conn->query("SELECT * FROM $documentationsTable WHERE 
    owner_id='${userInfo['id']}' AND 
    type='$type' AND
    status='pending'
  ");

  if ($checkExistingApprovedDocumentationRequest->num_rows > 0) {
    $message = "You have already requested for '<strong>$type</strong>' on this day. You may only request <strong>1 type of document per day</strong>.";
    $hasError = true;
    $hasSuccess = false;
  } else if ($checkExistingPendingDocumentationRequest->num_rows > 0) {
    $message = "You already have a pending request for '<strong>$type</strong>'.";
    $hasError = true;
    $hasSuccess = false;
  } else {
    $createDocumentationResult = $conn->query("INSERT INTO $documentationsTable(
      owner_id,
      date_created,
      type,
      purpose_of_request,
      status,
      transaction_number,
      created_at
    ) VALUES(
      '${userInfo['id']}',
      '$dateCreated',
      '$type',
      '$purposeOfRequest',
      'pending',
      'KAP-TEMP-${userInfo['id']}',
      '$createdAt'
    )");

    if ($createDocumentationResult) {
      $lastDocumentationInsertID = $conn->insert_id;

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
        'Documentation Request',
        '$dateCreated',
        'pending',
        '$createdAt'
      )");

      if ($createTransactionResult) {
        $lastTransactionInsertID = $conn->insert_id;
        $transactionNumber = strtoupper("KAP-${userInfo['id']}${lastTransactionInsertID}${lastDocumentationInsertID}" . uniqid("", false));

        $updateDocumentationResult = $conn->query("UPDATE $documentationsTable SET transaction_number='$transactionNumber' WHERE transaction_number='KAP-TEMP-${userInfo['id']}' AND owner_id='${userInfo['id']}'");
        $updateTransactionResult = $conn->query("UPDATE $transactionsTable SET transaction_number='$transactionNumber' WHERE transaction_number='KAP-TEMP-${userInfo['id']}' AND user_id='${userInfo['id']}'");

        if ($updateDocumentationResult && $updateTransactionResult) {
          $message = "Your request for '<strong>$type</strong>' has been successfully filed! You will be sent a <strong>text message notification</strong> to your <strong>mobile number (" . str_replace('+63', '0', $userInfo['contact_number']) . ")</strong> regarding the status of your request.";
          $hasError = false;
          $hasSuccess = true;
        } else {
          $message = "Your request for '<strong>$type</strong>' has been successfully filed! However, the system failed to generate a transaction number for your request. Please take a picture of this message and report it to the admins. \n\n<strong>Reason:\n" . $conn->error . "</strong>";
          $hasError = true;
          $hasSuccess = false;
        }
      } else {
        $message = "Your request for '<strong>$type</strong>' has been successfully filed! However, the system failed to generate a transaction number for your request. Please take a picture of this message and report it to the admins. \n\n<strong>Reason:\n" . $conn->error . "</strong>";
        $hasError = true;
        $hasSuccess = false;
      }
    } else {
      $message = "Your request for '<strong>$type</strong>' was not successfully filed! Please take a picture of this message and report it to the admins. \n\n<strong>Reason:\n" . $conn->error . "</strong>";
      $hasError = true;
      $hasSuccess = false;
    }
  }
}

// Fetch all documentation requests
$documentationsResult = $conn->query("SELECT * FROM $documentationsTable WHERE owner_id='${userInfo['id']}' ORDER BY created_at DESC");
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
      <h6 class="m-0 font-weight-bold" style="color:#223D3C;"><i class="fa fa-bookmark"></i> Documentations &nbsp;&nbsp;&nbsp;
        <button type="button" class="btn btn-primary px-auto" style="background-color:#223D3C; border-color:#223D3C;" data-toggle="modal" data-target="#createDocumentationModal">
          <i class="fa fa-plus-circle"></i>
          Request new
        </button>
      </h6>
    </div>

    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered" id="documentations" width="100%" cellspacing="0">
          <thead>
            <tr>
              <th>Transaction No.</th>
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
                <td><?= $row['type'] ?></td>
                <td><?= $row['date_created'] ?></td>
                <td class="<?= $row['status'] == 'pending' || $row['status'] == 'declined' ? 'table-danger text-danger' : 'table-success text-success' ?>"><?= ucfirst($row['status']) ?></td>
                <td>
                  <a class="btn btn-info px-auto" href="#" title="View" onClick="openViewDocumentationModal({
                      type: '<?= $row['type'] ?>', 
                      purpose_of_request: '<?= $row['purpose_of_request'] ?>'
                  })">
                    <i class="fa fa-eye"></i>
                  </a>
                  <?php if ($row['status'] == 'pending') { ?>
                    <a class="btn btn-danger px-auto" href="#" title="Cancel" onClick="openCancelDocumentationModal({
                      user_id: '<?= $userInfo['id'] ?>', 
                      transaction_number: '<?= $row['transaction_number'] ?>', 
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



<!-- Cancel Modal-->
<div class="modal fade" id="cancelDocumentationModal" tabindex="-1" role="dialog" aria-labelledby="cancelModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="cancelModalLabel">Cancel this request?</h5>
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>

      <div class="modal-body">Are you sure you really want to cancel this documentation request?</div>

      <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>

        <form method="POST" id="documentationFormCancel">

          <input type="hidden" name="transaction_number" value="">
          <input type="hidden" name="user_id" value="">
          <button type="submit" name="cancelDocumentationBtn" class="btn btn-success">Yes</button>

        </form>

      </div>
    </div>
  </div>
</div>
<!-- End Cancel Modal-->



<!-- Create Modal -->
<div class="modal fade" id="createDocumentationModal" tabindex="-1" role="dialog" aria-labelledby="createDocumentation" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="createDocumentation">Request Documentation</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form id="createDocumentationForm" method="POST" autocomplete="off">
        <div class="modal-body">

          <div class="form-group">
            <label> Document Type </label>
            <select name="document_type" class="form-control" required>
              <option value="Sedula" selected>Sedula</option>
              <option value="Brgy. Clearance for Calamity">Brgy. Clearance for Calamity</option>
              <option value="Brgy. Clearance for Funeral Assistance">Brgy. Clearance for Funeral Assistance</option>
              <option value="Brgy. Employment Clearance">Brgy. Employment Clearance</option>
              <option value="Brgy. Fencing Clearance">Brgy. Fencing Clearance</option>
              <option value="Brgy. Business Clearance">Brgy. Business Clearance</option>
              <option value="Brgy. Building Clearance">Brgy. Building Clearance</option>
              <option value="Brgy. Clearance for Tricycle Franchising">Brgy. Clearance for Tricycle Franchising</option>
              <option value="Lupon Clearance for No Derogatory Record">Lupon Clearance for No Derogatory Record</option>
              <option value="Brgy. Clearance for No Derogatory">Brgy. Clearance for No Derogatory</option>
              <option value="Brgy. Clearance for Collection Issurance of Business Tax Receipt">Brgy. Clearance for Collection Issurance of Business Tax Receipt</option>
              <option value="Brgy. Clearance Indigency">Brgy. Clearance Indigency</option>
              <option value="Brgy. Protection Order">Brgy. Protection Order</option>
              <option value="Certificate of Residency">Certificate of Residency</option>
            </select>
          </div>

          <div class="form-group">
            <label> Purpose of Request </label>
            <textarea name="purpose_of_request" class="form-control" rows="5" spellcheck="false" placeholder="Write purpose here..." required></textarea>
          </div>

        </div>

        <hr>
        <div class="px-3 row">
          <div class="col">
            <div class="form-group">
              <button type="button" class="btn btn-danger btn-block" data-dismiss="modal" aria-label="Cancel">Cancel</button>
            </div>
          </div>

          <div class="col">
            <div class="form-group">
              <button style="background-color:#223D3C; border-color:#223D3C;" type="submit" name="createDocumentationBtn" class="btn btn-success btn-block">Request</button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- End Create Modal -->



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