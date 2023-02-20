<?php
// Update user
if (isset($_POST['editUserBtn'])) {
  $userID = $conn->real_escape_string($_POST['user_id']);
  $firstname = $conn->real_escape_string($_POST['first_name']);
  $middlename = $conn->real_escape_string($_POST['middle_name']);
  $lastname = $conn->real_escape_string($_POST['last_name']);
  $gender = $conn->real_escape_string($_POST['gender']);
  $age = $conn->real_escape_string($_POST['age']);
  $contactNumber = $conn->real_escape_string($_POST['contact_number']);
  $birthday = $conn->real_escape_string($_POST['birthday']);
  $email = $conn->real_escape_string($_POST['email_address']);
  $job = $conn->real_escape_string($_POST['job']);
  $address = $conn->real_escape_string($_POST['complete_address']);

  $checkEmailResult = $conn->query("SELECT * FROM $usersTable WHERE email_address='$email' AND id='$userID'");

  if ($checkEmailResult->num_rows > 0) {
    $fullname = "$lastname, $firstname" . (strlen($middlename) > 0 ? " $middlename" : "");

    $updateQuery = $conn->query("UPDATE $usersTable SET 
      fullname='$fullname',
      firstname='$firstname',
      middlename='$middlename',
      lastname='$lastname',
      gender='$gender', 
      age='$age',
      contact_number='$contactNumber',
      birthday='$birthday',
      email_address='$email', 
      job='$job', 
      complete_address='$address'
      WHERE id='$userID'
    ");

    if ($updateQuery) {
      $message = "Successfully updated the user account!";
      $hasError = false;
      $hasSuccess = true;
    } else {
      $message = "Failed to update user account.\n\n<strong>Reason:\n" . $conn->error . "</strong>";
      $hasError = true;
      $hasSuccess = false;
    }
  } else {
    $message = "The email address '$email' is not registered!";
    $hasError = true;
    $hasSuccess = false;
  }
}



// Add user
if (isset($_POST['addUserBtn'])) {
  $firstname = $conn->real_escape_string($_POST['first_name']);
  $middlename = $conn->real_escape_string($_POST['middle_name']);
  $lastname = $conn->real_escape_string($_POST['last_name']);
  $gender = $conn->real_escape_string($_POST['gender']);
  $age = $conn->real_escape_string($_POST['age']);
  $contactNumber = $conn->real_escape_string($_POST['contact_number']);
  $birthday = $conn->real_escape_string($_POST['birthday']);
  $email = $conn->real_escape_string($_POST['email_address']);
  $job = $conn->real_escape_string($_POST['job']);
  $address = $conn->real_escape_string($_POST['complete_address']);
  $password = $conn->real_escape_string($_POST['password']);

  $idFront = $_FILES['upload_id_front'];
  $idBack = $_FILES['upload_id_back'];

  $checkEmailResult = $conn->query("SELECT * FROM $usersTable WHERE email_address='$email'");

  if (!(@StringUtils::contains(strtolower($email), strtolower(str_replace(" ", "", $firstname))) && (@StringUtils::contains(strtolower($email), strtolower(str_replace(" ", "", $middlename))) || @StringUtils::contains(strtolower($email), strtolower(str_replace(" ", "", $lastname)))))) {
    $message = "User's email address must contain his/her <strong>firstname and middlename or lastname</strong>!";
    $hasError = true;
    $hasSuccess = false;
  } else if ($checkEmailResult->num_rows == 0) {
    $fullname = "$lastname, $firstname" . (strlen($middlename) > 0 ? " " . substr($middlename, 0, 1) . "." : "");
    $password = password_hash($password, PASSWORD_BCRYPT);

    $createQuery = $conn->query("INSERT INTO $usersTable(
      fullname,
      firstname,
      middlename,
      lastname,
      gender, 
      age,
      contact_number, 
      birthday,
      email_address, 
      job,
      complete_address, 
      password
    ) VALUES(
      '$fullname',
      '$firstname',
      '$middlename',
      '$lastname',
      '$gender',
      '$age',
      '$contactNumber',
      '$birthday',
      '$email',
      '$job',
      '$address',
      '$password'
    )");

    if ($createQuery) {
      $userIDPrefix = $conn->insert_id;
      $date = date("Y-m-d h-i-s", time());
      $uploadIDFrontResult = FileUtil::uploadFiles($idFront, "../../assets/uploads/id_photos", "FRONT_$date-$userIDPrefix");
      $uploadIDBackResult = FileUtil::uploadFiles($idBack, "../../assets/uploads/id_photos", "BACK_$date-$userIDPrefix");

      if($uploadIDFrontResult['response']['hasError'] || $uploadIDBackResult['response']['hasError']) {
        $message = "User has been successfully created but the IDs were not uploaded successfully! Reason: <strong>" . (($uploadIDFrontResult['response']['hasError'] ? $uploadIDFrontResult['response']['message'] : $uploadIDBackResult['response']['message']) . "</strong>");
        $hasError = true;
        $hasSuccess = false;
      } else {
        $uploadedIDResult = $conn->query("INSERT INTO $uploadedIDsTable(
          user_id,
          path_front,
          path_back
        ) VALUES(
          '$userIDPrefix',
          '" . $uploadIDFrontResult['paths'][0] . "',
          '" . $uploadIDBackResult['paths'][0] . "'
        )");

        $message = "Successfully created a new user!";
        $hasError = false;
        $hasSuccess = true;
      }
    } else {
      $message = "Failed to create new user account.\n\n<strong>Reason:\n" . $conn->error . "</strong>";
      $hasError = true;
      $hasSuccess = false;
    }
  } else {
    $message = "The email address '$email' is already registered!";
    $hasError = true;
    $hasSuccess = false;
  }
}



// Delete user
if (isset($_POST['deleteUserBtn'])) {
  $userID = $conn->real_escape_string($_POST['user_id']);

  $deleteUserResult = $conn->query("DELETE FROM $usersTable WHERE id='$userID'");
  $checkUserIds = $conn->query("SELECT * FROM $uploadedIDsTable WHERE user_id='$userID'");

  if ($deleteUserResult) {
    $failedToDeleteIDs = false;

    if($checkUserIds->num_rows > 0) {

      while($uploadedID = $checkUserIds->fetch_assoc()) {
        if (!StringUtils::startsWith($uploadedID['path_front'], "../../"))
          $uploadedID['path_front'] = "../../" . $uploadedID['path_front'];

        if (!StringUtils::startsWith($uploadedID['path_back'], "../../"))
          $uploadedID['path_back'] = "../../" . $uploadedID['path_back'];

        if(!unlink($uploadedID['path_front']) || !unlink($uploadedID['path_back']))
          $failedToDeleteIDs = true;
      }
      
      $deleteUserIDsResult = $conn->query("DELETE FROM $uploadedIDsTable WHERE user_id='$userID'");
    }

    $message = !$failedToDeleteIDs ? "User account has been successfully deleted!" : "User account has been successfully deleted but failed to delete the uploaded ids!";
    $hasError = $failedToDeleteIDs;
    $hasSuccess = !$failedToDeleteIDs;
  } else {
    $message = "User account was not successfully deleted!";
    $hasError = true;
    $hasSuccess = false;
  }
}



// Approve user
if (isset($_POST['approveUserBtn'])) {
  $userID = $conn->real_escape_string($_POST['user_id']);

  $userDataResult = $conn->query("SELECT * FROM $usersTable WHERE id='$userID'");
  $userData = $userDataResult->fetch_assoc();

  $approveUserResult = $conn->query("UPDATE $usersTable SET status='approved' WHERE id='$userID'");

  if ($approveUserResult) {
    $smsTextResult = @SMSUtil::sendSms(
      $userData['contact_number'],
      "Mr./Mrs. ${userData['lastname']}, your account has been verified and approved. You can now login and use the virtual help desk, thank you for your patience."
    );

    $message = $smsTextResult !== null
      ? "User's account has been successfully approved and has been notified thru his/her contact number!"
      : "User's account has been successfully approved <strong>but has failed to notify the user thru his/her contact number!</strong>";

    $hasError = $smsTextResult === null;
    $hasSuccess = $smsTextResult !== null;
  } else {
    $message = "Failed to approve user's account. <strong>Reason: " . $conn->error . "</strong>";
    $hasError = false;
    $hasSuccess = true;
  }
}



// Decline user
if (isset($_POST['declineUserBtn'])) {
  $userID = $conn->real_escape_string($_POST['user_id']);
  $reason = $conn->real_escape_string($_POST['reason']);

  $userDataResult = $conn->query("SELECT * FROM $usersTable WHERE id='$userID'");
  $userData = $userDataResult->fetch_assoc();

  $declineUserResult = $conn->query("UPDATE $usersTable SET status='declined' WHERE id='$userID'");

  if ($declineUserResult) {
    $smsTextResult = @SMSUtil::sendSms(
      $userData['contact_number'], 
      "Mr./Mrs. ${userData['lastname']}, your account has failed the verification process and has been declined today. You may register your account again and make sure that the credentials that you provided are valid specially your id." . (empty($reason) ? "" : "\n\nAdditional reason:\n\n$reason")
    );

    $message = $smsTextResult !== null
      ? "User's account has been successfully declined and has been notified thru his/her contact number!"
      : "User's account has been successfully declined <strong>but has failed to notify the user thru his/her contact number!</strong>";

    $hasError = $smsTextResult === null;
    $hasSuccess = $smsTextResult !== null;
  } else {
    $message = "Failed to decline user's account. <strong>Reason: " . $conn->error . "</strong>";
    $hasError = false;
    $hasSuccess = true;
  }
}



// Fetch all users
$usersResult = $conn->query("SELECT u.*, i.user_id, i.path_front, i.path_back FROM $usersTable u INNER JOIN $uploadedIDsTable i ON i.user_id=u.id WHERE u.role='0' ORDER BY u.created_at DESC");
?>

<!-- For telephone number input -->
<link rel="stylesheet" href="../../assets/intlTelInput/intlTelInput.css" />
<script src="../../assets/intlTelInput/intlTelInput.min.js"></script>

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
      <h6 class="m-0 font-weight-bold" style="color:#223D3C;"><i class="fa fa-users"></i> Users 
        <?php if($adminInfo['role'] == '1') { ?>
          &nbsp;&nbsp;&nbsp;
          <button type="button" class="btn btn-primary px-auto" style="background-color:#223D3C; border-color:#223D3C;" data-toggle="modal" data-target="#addUserModal">
            <i class="fa fa-plus-circle"></i>
            Add new
          </button>
        <?php } ?>
      </h6>
    </div>

    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered align-middle" id="users" width="100%" cellspacing="0" data-ordering="false">
          <thead>
            <tr>
              <th class="exclude">ID</th>
              <th class="exclude">Fullname</th>
              <th class="d-none">First Name</th>
              <th class="d-none">Middle Name</th>
              <th class="d-none">Last Name</th>
              <th>Contact Number</th>
              <th>Email Address</th>
              <th>Gender</th>
              <th class="d-none">Age</th>
              <th class="d-none">Birthday</th>
              <th class="d-none">Job</th>
              <th>Complete Address</th>
              <th class="exclude">Status</th>
              <th class="exclude">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $usersResult->fetch_assoc()) {
              if(!StringUtils::startsWith($row['path_front'], "../../"))  
                $row['path_front'] = "../../" . $row['path_front'];

              if(!StringUtils::startsWith($row['path_back'], "../../"))  
                $row['path_back'] = "../../" . $row['path_back'];
            ?>
              <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['fullname'] ?></td>
                <td class="d-none"><?= $row['firstname'] ?></td>
                <td class="d-none"><?= empty($row['middlename']) ? "N/A" : $row['middlename'] ?></td>
                <td class="d-none"><?= $row['lastname'] ?></td>
                <td><?= $row['contact_number'] ?></td>
                <td><?= $row['email_address'] ?></td>
                <td><?= $row['gender'] ?></td>
                <td class="d-none"><?= $row['age'] ?></td>
                <td class="d-none"><?= date("Y, M d", strtotime($row['birthday'])) ?></td>
                <td class="d-none"><?= $row['job'] ?></td>
                <td><?= $row['complete_address'] ?></td>
                <td class="<?= $row['status'] == 'pending' || $row['status'] == 'declined' ? 'table-danger text-danger' : 'table-success text-success' ?>"><?= ucfirst($row['status']) ?></td>
                <td>
                  <a class="btn btn-info px-auto" href="#" onClick="openEditUserModal({
                    user_id: '<?= $row['id'] ?>',
                    firstname: '<?= $row['firstname'] ?>',
                    middlename: '<?= $row['middlename'] ?>',
                    lastname: '<?= $row['lastname'] ?>',
                    contact_number: '<?= $row['contact_number'] ?>',
                    email_address: '<?= $row['email_address'] ?>',
                    age: '<?= $row['age'] ?>',
                    birthday: '<?= date('Y-m-d', strtotime($row['birthday'])) ?>',
                    gender: '<?= $row['gender'] ?>',
                    job: '<?= $row['job'] ?>',
                    complete_address: '<?= $row['complete_address'] ?>',
                  })">
                    <i class="fa fa-pen"></i>
                  </a>
                  <?php if ($adminInfo['role'] == '1') { ?>
                    <a class="btn btn-danger px-auto" href="#" onClick="openDeleteUserModal({
                        user_id: '<?= $row['id'] ?>',
                    })">
                      <i class="fa fa-trash"></i>
                    </a>
                    <?php if ($row['status'] == 'pending') { ?>
                      <a class="btn btn-warning px-auto" href="#" onClick="openDeclineUserModal({
                          user_id: '<?= $row['id'] ?>',
                      })">
                        <i class="fa fa-thumbs-down"></i>
                      </a>
                      <a class="btn btn-success px-auto" href="#" onClick="openApproveUserModal({
                          user_id: '<?= $row['id'] ?>',
                      })">
                        <i class="fa fa-thumbs-up"></i>
                      </a>
                    <?php } ?>
                  <?php } ?>
                  <a class="btn btn-info px-auto" href="#" data-front-src="<?= $row['path_front'] ?>" data-back-src="<?= $row['path_back'] ?>" onClick="openViewIDsModal(this)">
                    <i class="fa fa-address-card"></i>
                  </a>
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



<!-- View IDs Modal-->
<div class="modal fade" id="viewIDsModal" tabindex="-1" role="dialog" aria-labelledby="viewIDsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="viewIDsModalLabel">User IDs (Front & Back)</h5>
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>

      <form method="POST" id="viewIDsForm">
        <div class="modal-body">
          <div class="row">
            <div class="col-md">
              <label for="front-id" class="form-control-label">Front ID</label>
              <img id="front-id" alt="Front ID" width="100%">
            </div>
            <div class="col-md">
              <label for="back-id" class="form-control-label">Back ID</label>
              <img id="back-id" alt="Back ID" width="100%">
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- End View IDs Modal-->




<!-- Delete Modal-->
<div class="modal fade" id="deleteUserModal" tabindex="-1" role="dialog" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="deleteUserModalLabel">Delete account</h5>
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>

      <div class="modal-body">Are you sure you really want to delete this user account?</div>

      <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>

        <form method="POST" id="deleteUserForm">
          <input type="hidden" name="user_id" value="">
          <button type="submit" name="deleteUserBtn" class="btn btn-success">Delete</button>
        </form>

      </div>
    </div>
  </div>
</div>
<!-- End Delete Modal-->



<!-- Decline Modal-->
<div class="modal fade" id="declineUserModal" tabindex="-1" role="dialog" aria-labelledby="declineUserModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="declineUserModalLabel">Decline account</h5>
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>

      <form method="POST" id="declineUserForm">
        <div class="modal-body">
          <p>Are you sure you really want to decline this user account?</p>

          <input type="hidden" name="user_id" value="" required>
          <div class="form-group">
            <label class="form-label"> Reason (Optional)</label>
            <textarea class="form-control" name="reason" cols="30" rows="5" placeholder="Specific reason for declining..."></textarea>
          </div>

        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
          <button type="submit" name="declineUserBtn" class="btn btn-danger">Decline</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- End Decline Modal-->



<!-- Approve Modal-->
<div class="modal fade" id="approveUserModal" tabindex="-1" role="dialog" aria-labelledby="approveUserModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="approveUserModalLabel">Approve account</h5>
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>

      <div class="modal-body">Are you sure you really want to approve this user account?</div>

      <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>

        <form method="POST" id="approveUserForm">
          <input type="hidden" name="user_id" value="">
          <button type="submit" name="approveUserBtn" class="btn btn-success">Approve</button>
        </form>

      </div>
    </div>
  </div>
</div>
<!-- End Approve Modal-->



<!-- Add Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addUserModalLabel">Add new user</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form id="addUserForm" method="POST" autocomplete="off" onsubmit="processForm1Submit()" enctype="multipart/form-data">
        <div class="modal-body">

          <div class="row">
            <div class="col-md">
              <div class="form-group">
                <label> First Name </label>
                <input type="text" name="first_name" class="form-control" placeholder="John Luis" required>
              </div>
            </div>

            <div class="col-md">
              <div class="form-group">
                <label> Middle Name </label>
                <input type="text" name="middle_name" pattern="([^\s]+|[a-zA-Z\s]+\.?)" class="form-control" placeholder="(Optional)">
              </div>
            </div>

            <div class="col-md">
              <div class="form-group">
                <label> Last Name </label>
                <input type="text" name="last_name" class="form-control" placeholder="Dela Cruz" required>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-sm">
              <div class="form-group">
                <label> Gender </label>
                <select name="gender" class="form-control" required>
                  <option value="Male" selected>Male</option>
                  <option value="Female">Female</option>
                </select>
              </div>
            </div>

            <div class="col-sm">
              <div class="form-group">
                <label> Age </label>
                <input type="number" name="age" class="form-control" min="1" onchange="JavaScript: (() => parseInt(this.value) <= 0 ? this.value = 1 : this.value)()" required>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-sm">
              <div class="form-group">
                <label> Birthday </label>
                <input type="text" name="birthday" class="form-control" placeholder="mm/dd/yyyy" onfocus="(this.type='date')" onblur="if(this.value == '') {this.type='text'}" required>
              </div>
            </div>

            <div class="col-sm">
              <div class="form-group">
                <label> Email Address </label>
                <input type="email" name="email_address" class="form-control" placeholder="example@gmail.com" required>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-sm">
              <div class="form-group">
                <label> Job </label>
                <input type="text" name="job" placeholder="Enter your job here" class="form-control" required>
              </div>
            </div>

            <div class="col-sm">
              <div class="form-group">
                <label> Complete Address </label>
                <input type="text" name="complete_address" class="form-control" placeholder="Address here..." required>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label> Contact Number </label>
            <input type="tel" name="contact_number" id="contact_number1" class="form-control" required>
          </div>

          <div class="form-group">
            <label> Password </label>
            <input type="text" name="password" class="form-control" placeholder="Enter password..." required>
          </div>

          <div class="row">
            <div class="col-sm">
              <div class="form-group">
                <label> Upload ID (Front) </label>
                <input type="file" name="upload_id_front" class="form-control-file" accept="image/*" required>
              </div>
            </div>

            <div class="col-sm">
              <div class="form-group">
                <label> Upload ID (Back) </label>
                <input type="file" name="upload_id_back" class="form-control-file" accept="image/*" required>
              </div>
            </div>
          </div>

        </div>

        <hr class="mt-0 mb-3" />
        <div class="px-3 row">
          <div class="col-sm">
            <div class="form-group">
              <button type="button" class="btn btn-block btn-danger" data-dismiss="modal" aria-label="Cancel">Cancel</button>
            </div>
          </div>
          <div class="col-sm">
            <div class="form-group">
              <button style="background-color:#223D3C; border-color:#223D3C;" type="submit" name="addUserBtn" class="btn btn-block btn-success">Add</button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- End Add Modal -->



<!-- Edit Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editUserModalLabel">Edit new user</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form id="editUserForm" method="POST" autocomplete="off" onsubmit="processForm2Submit()">
        <div class="modal-body">

          <div class="row">
            <div class="col-md">
              <div class="form-group">
                <label> First Name </label>
                <input type="text" name="first_name" class="form-control" placeholder="John Luis" required>
              </div>
            </div>

            <div class="col-md">
              <div class="form-group">
                <label> Middle Name </label>
                <input type="text" name="middle_name" class="form-control" pattern="([^\s]+|[a-zA-Z]+\.?\s*)" placeholder="(Optional)">
              </div>
            </div>

            <div class="col-md">
              <div class="form-group">
                <label> Last Name </label>
                <input type="text" name="last_name" class="form-control" placeholder="Dela Cruz" required>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-sm">
              <div class="form-group">
                <label> Gender </label>
                <select name="gender" class="form-control" required>
                  <option value="Male" selected>Male</option>
                  <option value="Female">Female</option>
                </select>
              </div>
            </div>

            <div class="col-sm">
              <div class="form-group">
                <label> Age </label>
                <input type="number" name="age" class="form-control" min="1" onchange="JavaScript: (() => parseInt(this.value) <= 0 ? this.value = 1 : this.value)()" required>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-sm">
              <div class="form-group">
                <label> Birthday </label>
                <input type="date" name="birthday" class="form-control" placeholder="mm/dd/yyyy" onfocus="(this.type='date')" onblur="if(this.value == '') {this.type='text'}" required>
              </div>
            </div>

            <div class="col-sm">
              <div class="form-group">
                <label> Email Address </label>
                <input type="email" name="email_address" class="form-control" placeholder="example@gmail.com" required>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-sm">
              <div class="form-group">
                <label> Job </label>
                <input type="text" name="job" placeholder="Enter your job here" class="form-control" required>
              </div>
            </div>

            <div class="col-sm">
              <div class="form-group">
                <label> Complete Address </label>
                <input type="text" name="complete_address" class="form-control" placeholder="Address here..." required>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label> Contact Number </label>
            <input type="tel" name="contact_number" id="contact_number2" class="form-control" required>
          </div>

          <div class="form-group">
            <input type="hidden" name="user_id" class="form-control" required>
          </div>

        </div>

        <hr class="mt-0 mb-3" />
        <div class="px-3 row">
          <div class="col">
            <div class="form-group">
              <button type="button" class="btn btn-block btn-danger" data-dismiss="modal" aria-label="Cancel">Cancel</button>
            </div>
          </div>
          <div class="col">
            <div class="form-group">
              <button style="background-color:#223D3C; border-color:#223D3C;" disabled type="submit" name="editUserBtn" class="btn btn-block btn-success">Update</button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- End Edit Modal -->

<script>
  const phoneInput1Field = document.querySelector("#contact_number1");
  const phoneInput1 = window.intlTelInput(phoneInput1Field, {
    preferredCountries: ['ph'],
    allowDropdown: false,
    utilsScript: "../../assets/intlTelInput/utils.js"
  });

  const phoneInput2Field = document.querySelector("#contact_number2");
  const phoneInput2 = window.intlTelInput(phoneInput2Field, {
    preferredCountries: ['ph'],
    allowDropdown: false,
    utilsScript: "../../assets/intlTelInput/utils.js"
  });

  const processForm1Submit = (event) => {
    phoneInput1Field.value = phoneInput1.getNumber();
    return true;
  };

  const processForm2Submit = (event) => {
    phoneInput2Field.value = phoneInput2.getNumber();
    return true;
  };
</script>