<?php
// Cancel Complaint
if (isset($_POST['cancelComplaintBtn'])) {
  $transactionNumber = $conn->real_escape_string($_POST['transaction_number']);
  $userID = $conn->real_escape_string($_POST['user_id']);

  $complaintResult = $conn->query("SELECT * FROM $complaintsTable WHERE transaction_number='$transactionNumber' AND user_id='$userID'");
  $complaintID = $complaintResult->fetch_assoc()['id'];

  $deleteComplaintResult = $conn->query("DELETE FROM $complaintsTable WHERE transaction_number='$transactionNumber' AND user_id='$userID'");

  if ($deleteComplaintResult) {
    $deleteTransactionResult = $conn->query("DELETE FROM $transactionsTable WHERE transaction_number='$transactionNumber' AND user_id='$userID'");
    $checkEvidencesResult = $conn->query("SELECT * FROM $evidencesTable WHERE complaint_blotter_id='$complaintID'");
    $failedToDeleteEvidencesCount = 0;

    // If there are evidences, delete them as well
    if ($checkEvidencesResult->num_rows > 0) {

      while ($row = $checkEvidencesResult->fetch_assoc()) {
        if (!unlink($row['path']))
          $failedToDeleteEvidencesCount += 1;
      }

      $conn->query("DELETE FROM $evidencesTable WHERE complaint_blotter_id='$complaintID'");
    }

    if ($deleteTransactionResult) {
      $message = $failedToDeleteEvidencesCount > 0 ? "Successfully canceled the complaint but failed to delete some of the attached evidences" : "Successfully canceled the complaint transaction!";
      $hasError = false;
      $hasSuccess = true;
    } else {
      $message = "Failed to cancel the complaint transaction! Reason: <strong>'" . $conn->error . "'</strong>";
      $hasError = true;
      $hasSuccess = false;
    }
  } else {
    $message = "Failed to cancel the complaint transaction! Reason: <strong>'" . $conn->error . "'</strong>";
    $hasError = true;
    $hasSuccess = false;
  }
}

// Create Complaint
if (isset($_POST['createNewComplaintBtn'])) {
  $what = $conn->real_escape_string($_POST['what']);
  $date = $conn->real_escape_string($_POST['date']);
  $time = $conn->real_escape_string($_POST['time']);
  $where = $conn->real_escape_string($_POST['where']);
  $who = $conn->real_escape_string($_POST['who']);
  $how = $conn->real_escape_string($_POST['how']);
  $natureOfComplaint = $conn->real_escape_string($_POST['nature_of_complaint']);
  $evidences = $_FILES['evidences'];

  $when = date("F d, Y \\a\\t h:i A", strtotime("$date $time"));
  $dateFiled = date("F d, Y h:i A", time());
  $createdAt = date("Y-m-d h:i:s.u", time());

  $checkExistingPendingRequest = $conn->query("SELECT * FROM $complaintsTable WHERE 
    who='$who' AND 
    nature_of_complaint='$natureOfComplaint' AND
    created_at >= '" . date("Y-m-d", strtotime($createdAt)) . "' AND
    created_at < ('" . date("Y-m-d", strtotime($createdAt)) . "' + INTERVAL 1 DAY) AND
    status='pending'
  ");

  if ($checkExistingPendingRequest->num_rows > 0) {
    $message = "You already have a pending complaint for <strong>$who</strong> for '<strong>$natureOfComplaint</strong>'.";
    $hasError = true;
    $hasSuccess = false;
  } else {
    $createComplaintResult = $conn->query("INSERT INTO $complaintsTable(
      user_id, 
      case_title, 
      date_filed, 
      complaint_title,
      who,
      what,
      `when`,
      `where`,
      how,
      nature_of_complaint,
      status,
      transaction_number,
      created_at
    ) VALUES (
      '${userInfo['id']}',
      '$what',
      '$dateFiled',
      '$what',
      '$who',
      '$what',
      '$when',
      '$where',
      '$how',
      '$natureOfComplaint',
      'pending',
      'KAP-TEMP-${userInfo['id']}',
      '$createdAt'
    )");

    if ($createComplaintResult) {
      $lastInsertComplaintID = $conn->insert_id;
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
        'Complaint Request',
        '$dateFiled',
        'pending',
        '$createdAt'
      )");

      if ($createTransactionResult) {
        $lastInsertTransactionID = $conn->insert_id;
        $transactionNumber = strtoupper("KAP-${userInfo['id']}${lastInsertTransactionID}${lastInsertComplaintID}" . uniqid("", false));

        $updateComplaintsResult = $conn->query("UPDATE $complaintsTable SET transaction_number='$transactionNumber' WHERE transaction_number='KAP-TEMP-${userInfo['id']}' AND user_id='${userInfo['id']}'");
        $updateTransactionsResult = $conn->query("UPDATE $transactionsTable SET transaction_number='$transactionNumber' WHERE transaction_number='KAP-TEMP-${userInfo['id']}' AND user_id='${userInfo['id']}'");

        if ($updateComplaintsResult && $updateTransactionsResult) {

          // We have to upload the evidences to the database
          if (!empty($evidences['name'][0]) && count($evidences['name']) > 0) {
            $prefix = "EVIDENCE-${transactionNumber}-" . date('Y-m-d h-i-s', time());
            $filepaths = FileUtil::uploadFiles($evidences, '../../assets/uploads/evidences', $prefix);

            if ($filepaths['response']['hasError']) {
              $message = "Your complaint has been successfully filed, however, the attached evidences were not uploaded successfully. Please take a photo of this message and report this to the admins, thank you.\n\n<strong>Reason:\n" . $filepaths['response']['message'] . "</strong>";
              $hasError = true;
              $hasSuccess = false;
            } else {
              // Insert the filepaths to the evidences table
              foreach ($filepaths['paths'] as $_path)
                $conn->query("INSERT INTO $evidencesTable(
                  complaint_blotter_id, 
                  path, 
                  created_at
                ) VALUES(
                  '$lastInsertComplaintID',
                  '$_path',
                  '$createdAt'
                )");

              $message = "Your complaint has been successfully filed! You will be sent a <strong>text message notification</strong> to your <strong>mobile number (" . str_replace('+63', '0', $userInfo['contact_number']) . ")</strong> regarding the status of your complaint.";
              $hasError = false;
              $hasSuccess = true;
            }
          } else {
            $message = "Your complaint has been successfully filed! You will be sent a <strong>text message notification</strong> to your <strong>mobile number (" . str_replace('+63', '0', $userInfo['contact_number']) . ")</strong> regarding the status of your complaint.";
            $hasError = false;
            $hasSuccess = true;
          }
        } else {
          $message = "Your complaint has been successfully filed, however, the system failed to generate a tracking number on it. Please take a photo of this message and kindly report this to the admins, thank you.\n\n<strong>Reason:\n" . mysqli_error($conn) . "</strong>";
          $hasError = true;
          $hasSuccess = false;
        }
      } else {
        $message = "Your complaint has not been filed. Please take a photo of this message and report it to the admins, thank you.\n\n<strong>Reason:\n" . mysqli_error($conn) . "</strong>";
        $hasError = true;
        $hasSuccess = false;
      }
    } else {
      $message = "Your complaint has not been filed. Please take a photo of this message and report it to the admins, thank you.\n\n<strong>Reason:\n" . mysqli_error($conn) . "</strong>";
      $hasError = true;
      $hasSuccess = false;
    }
  }
}

// Cancel Blotter
if (isset($_POST['cancelBlotterBtn'])) {
  $transactionNumber = $conn->real_escape_string($_POST['transaction_number']);
  $userID = $conn->real_escape_string($_POST['user_id']);

  $blotterResult = $conn->query("SELECT * FROM $blottersTable WHERE transaction_number='$transactionNumber' AND user_id='$userID'");
  $blotterID = $blotterResult->fetch_assoc()['id'];

  $deleteBlotterResult = $conn->query("DELETE FROM $blottersTable WHERE transaction_number='$transactionNumber' AND user_id='$userID'");

  if ($deleteBlotterResult) {
    $deleteTransactionResult = $conn->query("DELETE FROM $transactionsTable WHERE transaction_number='$transactionNumber' AND user_id='$userID'");
    $checkEvidencesResult = $conn->query("SELECT * FROM $evidencesTable WHERE complaint_blotter_id='$blotterID'");
    $failedToDeleteEvidencesCount = 0;

    // If there are evidences, delete them as well
    if ($checkEvidencesResult->num_rows > 0) {

      while ($row = $checkEvidencesResult->fetch_assoc()) {
        if (!unlink($row['path']))
          $failedToDeleteEvidencesCount += 1;
      }

      $conn->query("DELETE FROM $evidencesTable WHERE complaint_blotter_id='$blotterID'");
    }

    if ($deleteTransactionResult) {
      $message = $failedToDeleteEvidencesCount > 0 ? "Successfully canceled the blotter but failed to delete some of the attached evidences" : "Successfully canceled the blotter transaction!";
      $hasError = false;
      $hasSuccess = true;
    } else {
      $message = "Failed to cancel the blotter transaction! Reason: <strong>'" . $conn->error . "'</strong>";
      $hasError = true;
      $hasSuccess = false;
    }
  } else {
    $message = "Failed to cancel the blotter transaction! Reason: <strong>'" . $conn->error . "'</strong>";
    $hasError = true;
    $hasSuccess = false;
  }
}

// Create Blotter
if (isset($_POST['createNewBlotterBtn'])) {
  $what = $conn->real_escape_string($_POST['what']);
  $date = $conn->real_escape_string($_POST['date']);
  $time = $conn->real_escape_string($_POST['time']);
  $where = $conn->real_escape_string($_POST['where']);
  $who = $conn->real_escape_string($_POST['who']);
  $how = $conn->real_escape_string($_POST['how']);
  $natureOfBlotter = $conn->real_escape_string($_POST['nature_of_blotter']);
  $evidences = $_FILES['evidences'];

  $when = date("F d, Y \\a\\t h:i A", strtotime("$date $time"));
  $dateFiled = date("F d, Y h:i A", time());
  $createdAt = date("Y-m-d h:i:s.u", time());

  $checkExistingPendingRequest = $conn->query("SELECT * FROM $blottersTable WHERE 
    who='$who' AND 
    nature_of_blotter='$natureOfBlotter' AND
    created_at >= '" . date("Y-m-d", strtotime($createdAt)) . "' AND
    created_at < ('" . date("Y-m-d", strtotime($createdAt)) . "' + INTERVAL 1 DAY) AND
    status='pending'
  ");

  if ($checkExistingPendingRequest->num_rows > 0) {
    $message = "You already have a pending blotter for <strong>$who</strong> for '<strong>$natureOfBlotter</strong>'.";
    $hasError = true;
    $hasSuccess = false;
  } else {
    $createBlotterResult = $conn->query("INSERT INTO $blottersTable(
      user_id, 
      case_title, 
      date_filed, 
      blotter_title,
      who,
      what,
      `when`,
      `where`,
      how,
      nature_of_blotter,
      status,
      transaction_number,
      created_at
    ) VALUES (
      '${userInfo['id']}',
      '$what',
      '$dateFiled',
      '$what',
      '$who',
      '$what',
      '$when',
      '$where',
      '$how',
      '$natureOfBlotter',
      'pending',
      'KAP-TEMP-${userInfo['id']}',
      '$createdAt'
    )");

    if ($createBlotterResult) {
      $lastInsertBlotterID = $conn->insert_id;
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
        'Blotter Request',
        '$dateFiled',
        'pending',
        '$createdAt'
      )");

      if ($createTransactionResult) {
        $lastInsertTransactionID = $conn->insert_id;
        $transactionNumber = strtoupper("KAP-${userInfo['id']}${lastInsertTransactionID}${lastInsertBlotterID}" . uniqid("", false));

        $updateBlottersResult = $conn->query("UPDATE $blottersTable SET transaction_number='$transactionNumber' WHERE transaction_number='KAP-TEMP-${userInfo['id']}' AND user_id='${userInfo['id']}'");
        $updateTransactionsResult = $conn->query("UPDATE $transactionsTable SET transaction_number='$transactionNumber' WHERE transaction_number='KAP-TEMP-${userInfo['id']}' AND user_id='${userInfo['id']}'");

        if ($updateBlottersResult && $updateTransactionsResult) {

          // We have to upload the evidences to the database
          if (!empty($evidences['name'][0]) && count($evidences['name']) > 0) {
            $prefix = "EVIDENCE-${transactionNumber}-" . date('Y-m-d h-i-s', time());
            $filepaths = FileUtil::uploadFiles($evidences, '../../assets/uploads/evidences', $prefix);

            if ($filepaths['response']['hasError']) {
              $message = "Your complaint has been successfully filed, however, the attached evidences were not uploaded successfully. Please take a photo of this message and report this to the admins, thank you.\n\n<strong>Reason:\n" . $filepaths['response']['message'] . "</strong>";
              $hasError = true;
              $hasSuccess = false;
            } else {
              // Insert the filepaths to the evidences table
              foreach ($filepaths['paths'] as $_path)
                $conn->query("INSERT INTO $evidencesTable(
                  complaint_blotter_id, 
                  path, 
                  created_at
                ) VALUES(
                  '$lastInsertBlotterID',
                  '$_path',
                  '$createdAt'
                )");

              $message = "Your blotter has been successfully filed! You will be sent a <strong>text message notification</strong> to your <strong>mobile number (" . str_replace('+63', '0', $userInfo['contact_number']) . ")</strong> regarding the status of your blotter.";
              $hasError = false;
              $hasSuccess = true;
            }
          } else {
            $message = "Your blotter has been successfully filed! You will be sent a <strong>text message notification</strong> to your <strong>mobile number (" . str_replace('+63', '0', $userInfo['contact_number']) . ")</strong> regarding the status of your blotter.";
            $hasError = false;
            $hasSuccess = true;
          }
        } else {
          $message = "Your blotter has been successfully filed, however, the system failed to generate a tracking number on it. Please take a photo of this message and kindly report this to the admins, thank you.\n\n<strong>Reason:\n" . mysqli_error($conn) . "</strong>";
          $hasError = true;
          $hasSuccess = false;
        }
      } else {
        $message = "Your blotter has not been filed. Please take a photo of this message and report it to the admins, thank you.\n\n<strong>Reason:\n" . mysqli_error($conn) . "</strong>";
        $hasError = true;
        $hasSuccess = false;
      }
    } else {
      $message = "Your blotter has not been filed. Please take a photo of this message and report it to the admins, thank you.\n\n<strong>Reason:\n" . mysqli_error($conn) . "</strong>";
      $hasError = true;
      $hasSuccess = false;
    }
  }
}

// Fetch all complaints and blotters
$complaintsResult = $conn->query("SELECT * FROM $complaintsTable WHERE user_id='${userInfo['id']}' ORDER BY created_at DESC");
$blottersResult = $conn->query("SELECT * FROM $blottersTable WHERE user_id='${userInfo['id']}' ORDER BY created_at DESC");
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
      <h6 class="m-0 font-weight-bold" style="color:#223D3C;"><i class="fa fa-bullhorn"></i> Complaints &nbsp;&nbsp;&nbsp;
        <button type="button" class="btn btn-primary px-auto" style="background-color:#223D3C; border-color:#223D3C;" data-toggle="modal" data-target="#createComplaintModal">
          <i class="fa fa-plus-circle"></i>
          Create new
        </button>
      </h6>
    </div>

    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered align-middle" id="complaints" width="100%" cellspacing="0">
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
                  <a class="btn btn-info px-auto" href="#" title="View" onClick="openViewComplaintModal({
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
                    <i class="fa fa-eye"></i> <?= $row['status'] != 'pending' ? 'View' : '' ?>
                  </a>
                  <?php if ($row['status'] == 'pending') { ?>
                    <a class="btn btn-danger px-auto" href="#" title="Cancel" onClick="openCancelComplaintModal({
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

  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold" style="color:#223D3C;"><i class="fa fa-bullhorn"></i> Blotters &nbsp;&nbsp;&nbsp;
        <button type="button" class="btn btn-primary px-auto" style="background-color:#223D3C; border-color:#223D3C;" data-toggle="modal" data-target="#createBlotterModal">
          <i class="fa fa-plus-circle"></i>
          Create new
        </button>
      </h6>
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
                  <a class="btn btn-info px-auto" href="#" title="View" onClick="openViewBlotterModal({
                    blotter_blotter_id: '<?= $row['id'] ?>',
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
                    <i class="fa fa-eye"></i> <?= $row['status'] != 'pending' ? 'View' : '' ?>
                  </a>
                  <?php if ($row['status'] == 'pending') { ?>
                    <a class="btn btn-danger px-auto" href="#" title="Cancel" onClick="openCancelBlotterModal({
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



<!-- Cancel Complaint Modal-->
<div class="modal fade" id="cancelComplaintModal" tabindex="-1" role="dialog" aria-labelledby="cancelModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="cancelModalLabel">Cancel this complaint?</h5>
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>

      <div class="modal-body">Are you sure you really want to cancel this complaint?</div>

      <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>

        <form method="POST" id="complaintFormCancel">

          <input type="hidden" name="transaction_number" value="">
          <input type="hidden" name="user_id" value="">
          <button type="submit" name="cancelComplaintBtn" class="btn btn-success">Yes</button>

        </form>

      </div>
    </div>
  </div>
</div>
<!-- End Cancel Complaint Modal-->



<!-- Cancel Blotter Modal-->
<div class="modal fade" id="cancelBlotterModal" tabindex="-1" role="dialog" aria-labelledby="cancelModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="cancelModalLabel">Cancel this blotter?</h5>
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>

      <div class="modal-body">Are you sure you really want to cancel this blotter?</div>

      <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>

        <form method="POST" id="blotterFormCancel">

          <input type="hidden" name="transaction_number" value="">
          <input type="hidden" name="user_id" value="">
          <button type="submit" name="cancelBlotterBtn" class="btn btn-success">Yes</button>

        </form>

      </div>
    </div>
  </div>
</div>
<!-- End Cancel Blotter Modal-->



<!-- Create Complaint Modal -->
<div class="modal fade" id="createComplaintModal" tabindex="-1" role="dialog" aria-labelledby="createComplaint" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="createComplaint">File a Complaint</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form id="createComplaintForm" method="POST" autocomplete="off" enctype="multipart/form-data">
        <div class="modal-body">

          <div class="form-group">
            <label> Who </label>
            <input type="text" name="who" class="form-control" placeholder="Fullname of the person" required>
          </div>

          <div class="form-group">
            <label> What </label>
            <input type="text" name="what" class="form-control" placeholder="What reason..." required>
          </div>


          <div class="row">
            <div class="col">
              <div class="form-group">
                <label> Date (When it happened) </label>
                <input type="date" name="date" class="form-control" required>
              </div>
            </div>

            <div class="col">
              <div class="form-group">
                <label> Time (When it happened) </label>
                <input type="time" name="time" class="form-control" required>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label> Where </label>
            <input type="text" name="where" class="form-control" placeholder="Complete Address" required>
          </div>

          <div class="form-group">
            <label> How </label>
            <textarea name="how" class="form-control" placeholder="How it happened..." rows="5" required></textarea>
          </div>

          <div class="form-group">
            <label> Nature Of Complaint </label>
            <select name="nature_of_complaint" class="form-control" required>
              <optgroup label="MGA KASONG SIBIL (Civil Cases)">
                <option value="Ejectment" selected>Pagpapalayas (Ejectment)</option>
                <option value="Demand of Payment of some of money">Sapilitang paniningil ng pera (Demand of Payment of some of money)</option>
                <option value="Breach of Contract">'Di pagtupad sa kasunduan (Breach of Contract)</option>
                <option value="Recovery of Personal Property">Pagbawi ng gamit panarili (Recovery of Personal Property)</option>
                <option value="Damages">Pamiminsala (Damages)</option>
                <option value="Demand of Specific Performance">Sapilitang paggawain ng isang bagay (Demand of Specific Performance)</option>
                <option value="Agrarian Cases">May kinalaman sa Lupang Pansakahan (Agrarian Cases)</option>
                <option value="Labor Cases">May kinalaman sa paggawa (Labor Cases)</option>
                <option value="Violation of Price Ordinance">Paglabag sa kautusang pambayad sa baranggay (Violation of Price Ordinance)</option>
                <option value="Violation of Price Control">Paglabag sa pagkokontrol ng presyo (Violation of Price Control)</option>
              </optgroup>
              <optgroup label="MGA KASONG KRIMINAL (Criminal Cases)">
                <option value="Boundary Disputes">May kinalaman sa hangganan (Boundary Disputes)</option>
                <option value="Slight Slander/Oral Defamation">Bahagyang paninirang puri at pagbibintang (Slight Slander/Oral Defamation)</option>
                <option value="Slight Physical Injuries & Mistreatment">Bahagyang pananakit at pang-aapi (Slight Physical Injuries & Mistreatment)</option>
                <option value="Light Threats">Bahagyang Pananakot (Light Threats)</option>
                <option value="Slight Coercion or Unjust Vexation">Bahagyang pamumuwersa or di-makatarungang panunuya (Slight Coercion or Unjust Vexation)</option>
                <option value="Theft of Small Value">Pagnanakaw ng maliit na halaga (Theft of Small Value)</option>
                <option value="Slander By Deed">Paninirang-puri/Pambabastos (Slander By Deed)</option>
                <option value="Malicious Mischief">Panloloko/Estafa (Malicious Mischief)</option>
                <option value="Imprudence & Negligence">Kapabayaan (Imprudence & Negligence)</option>
                <option value="Arson of Property of Small Value">Pagsusunog (Arson of Property of Small Value)</option>
                <option value="Tresspassing">Panghihimasok sa hindi ari-arian (Tresspassing)</option>
                <option value="Alarm & Scandal">Panggugulo (Alarm & Scandal)</option>
                <option value="Deceit">Panloloko (Deceit)</option>
                <option value="Falsification of Public Documents">Paggamit ng huwad na pirma o Pagpapalsika ng dokumentong pampubliko (Falsification of Public Documents)</option>
              </optgroup>
              <optgroup label="IBA PA (Others)">
                <option value="Pautang sa upa ng bahay">Pautang sa upa ng bahay</option>
                <option value="Aksidente sa daan">Aksidente sa daan</option>
                <option value="Nasakop na Lupa">Nasakop na Lupa</option>
                <option value="Reklamo sa inuman">Reklamo sa inuman</option>
                <option value="Ukol sa pagtatalo">Ukol sa pagtatalo</option>
              </optgroup>
            </select>
          </div>

          <div class="form-group">
            <label> Evidences </label>
            <div class="input-group mb-2 rounded">
              <input style="color:#223D3C;" type="file" accept="image/*, video/*" multiple class="form-control-file" name="evidences[]" placeholder="Evidences here">
            </div>
          </div>

        </div>

        <hr class="mt-0 mb-3"/>
        <div class="px-3 row">
          <div class="col">
            <div class="form-group">
              <button type="button" class="btn btn-danger btn-block" data-dismiss="modal" aria-label="Cancel">Cancel</button>
            </div>
          </div>

          <div class="col">
            <div class="form-group">
              <button style="background-color:#223D3C; border-color:#223D3C;" type="submit" name="createNewComplaintBtn" class="btn btn-success btn-block">File Complaint</button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- End Create Complaint Modal -->



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




<!-- Create Blotter Modal -->
<div class="modal fade" id="createBlotterModal" tabindex="-1" role="dialog" aria-labelledby="createBlotter" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="createBlotter">File a Blotter</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form id="createBlotterForm" method="POST" autocomplete="off" enctype="multipart/form-data">
        <div class="modal-body">

          <div class="form-group">
            <label> Who </label>
            <input type="text" name="who" class="form-control" placeholder="Fullname of the person" required>
          </div>

          <div class="form-group">
            <label> What </label>
            <input type="text" name="what" class="form-control" placeholder="What reason..." required>
          </div>


          <div class="row">
            <div class="col">
              <div class="form-group">
                <label> Date (When it happened) </label>
                <input type="date" name="date" class="form-control" required>
              </div>
            </div>

            <div class="col">
              <div class="form-group">
                <label> Time (When it happened) </label>
                <input type="time" name="time" class="form-control" required>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label> Where </label>
            <input type="text" name="where" class="form-control" placeholder="Complete Address" required>
          </div>

          <div class="form-group">
            <label> How </label>
            <textarea name="how" class="form-control" placeholder="How it happened..." rows="5" required></textarea>
          </div>

          <div class="form-group">
            <label> Nature Of Blotter </label>
            <select name="nature_of_blotter" class="form-control" required>
              <optgroup label="MGA KASONG SIBIL (Civil Cases)">
                <option value="Ejectment" selected>Pagpapalayas (Ejectment)</option>
                <option value="Demand of Payment of some of money">Sapilitang paniningil ng pera (Demand of Payment of some of money)</option>
                <option value="Breach of Contract">'Di pagtupad sa kasunduan (Breach of Contract)</option>
                <option value="Recovery of Personal Property">Pagbawi ng gamit panarili (Recovery of Personal Property)</option>
                <option value="Damages">Pamiminsala (Damages)</option>
                <option value="Demand of Specific Performance">Sapilitang paggawain ng isang bagay (Demand of Specific Performance)</option>
                <option value="Agrarian Cases">May kinalaman sa Lupang Pansakahan (Agrarian Cases)</option>
                <option value="Labor Cases">May kinalaman sa paggawa (Labor Cases)</option>
                <option value="Violation of Price Ordinance">Paglabag sa kautusang pambayad sa baranggay (Violation of Price Ordinance)</option>
                <option value="Violation of Price Control">Paglabag sa pagkokontrol ng presyo (Violation of Price Control)</option>
              </optgroup>
              <optgroup label="MGA KASONG KRIMINAL (Criminal Cases)">
                <option value="Boundary Disputes">May kinalaman sa hangganan (Boundary Disputes)</option>
                <option value="Slight Slander/Oral Defamation">Bahagyang paninirang puri at pagbibintang (Slight Slander/Oral Defamation)</option>
                <option value="Slight Physical Injuries & Mistreatment">Bahagyang pananakit at pang-aapi (Slight Physical Injuries & Mistreatment)</option>
                <option value="Light Threats">Bahagyang Pananakot (Light Threats)</option>
                <option value="Slight Coercion or Unjust Vexation">Bahagyang pamumuwersa or di-makatarungang panunuya (Slight Coercion or Unjust Vexation)</option>
                <option value="Theft of Small Value">Pagnanakaw ng maliit na halaga (Theft of Small Value)</option>
                <option value="Slander By Deed">Paninirang-puri/Pambabastos (Slander By Deed)</option>
                <option value="Malicious Mischief">Panloloko/Estafa (Malicious Mischief)</option>
                <option value="Imprudence & Negligence">Kapabayaan (Imprudence & Negligence)</option>
                <option value="Arson of Property of Small Value">Pagsusunog (Arson of Property of Small Value)</option>
                <option value="Tresspassing">Panghihimasok sa hindi ari-arian (Tresspassing)</option>
                <option value="Alarm & Scandal">Panggugulo (Alarm & Scandal)</option>
                <option value="Deceit">Panloloko (Deceit)</option>
                <option value="Falsification of Public Documents">Paggamit ng huwad na pirma o Pagpapalsika ng dokumentong pampubliko (Falsification of Public Documents)</option>
              </optgroup>
              <optgroup label="IBA PA (Others)">
                <option value="Pautang sa upa ng bahay">Pautang sa upa ng bahay</option>
                <option value="Aksidente sa daan">Aksidente sa daan</option>
                <option value="Nasakop na Lupa">Nasakop na Lupa</option>
                <option value="Reklamo sa inuman">Reklamo sa inuman</option>
                <option value="Ukol sa pagtatalo">Ukol sa pagtatalo</option>
              </optgroup>
            </select>
          </div>

          <div class="form-group">
            <label> Evidences </label>
            <div class="input-group mb-2 rounded">
              <input style="color:#223D3C;" type="file" accept="image/*, video/*" multiple class="form-control-file" name="evidences[]" placeholder="Evidences here">
            </div>
          </div>

        </div>

        <hr class="mt-0 mb-3"/>
        <div class="px-3 row">
          <div class="col">
            <div class="form-group">
              <button type="button" class="btn btn-danger btn-block" data-dismiss="modal" aria-label="Cancel">Cancel</button>
            </div>
          </div>

          <div class="col">
            <div class="form-group">
              <button style="background-color:#223D3C; border-color:#223D3C;" type="submit" name="createNewBlotterBtn" class="btn btn-success btn-block">File Blotter</button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- End Create Blotter Modal -->



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