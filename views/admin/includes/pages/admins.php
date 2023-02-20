<?php
// Update admin
if (isset($_POST['editAdminBtn'])) {
  $userID = $conn->real_escape_string($_POST['user_id']);
  $firstname = $conn->real_escape_string($_POST['first_name']);
  $middlename = $conn->real_escape_string($_POST['middle_name']);
  $lastname = $conn->real_escape_string($_POST['last_name']);

  $gender = $conn->real_escape_string($_POST['gender']);
  $age = $conn->real_escape_string($_POST['age']);
  $birthday = $conn->real_escape_string($_POST['birthday']);
  $email = $conn->real_escape_string($_POST['email_address']);
  $job = $conn->real_escape_string($_POST['job']);
  $address = $conn->real_escape_string($_POST['complete_address']);

  $checkEmailResult = $conn->query("SELECT * FROM $usersTable WHERE email_address='$email' AND id<>'$userID'");

  if ($checkEmailResult->num_rows == 0) {
    $fullname = "$lastname, $firstname" . (strlen($middlename) > 0 ? " " . substr($middlename, 0, 1) . "." : "");

    $updateQuery = $conn->query("UPDATE $usersTable SET 
      fullname='$fullname',
      firstname='$firstname',
      middlename='$middlename',
      lastname='$lastname',
      gender='$gender', 
      age='$age',
      birthday='$birthday',
      email_address='$email', 
      job='$job', 
      complete_address='$address'
      WHERE id='$userID'
    ");

    if ($updateQuery) {
      $message = "Successfully updated the admin account!";
      $hasError = false;
      $hasSuccess = true;
    } else {
      $message = "Failed to update admin account.\n\n<strong>Reason:\n" . $conn->error . "</strong>";
      $hasError = true;
      $hasSuccess = false;
    }
  } else {
    $message = "Failed to update the admin. The email address <strong>'$email'</strong> is already registered as an admin!";
    $hasError = true;
    $hasSuccess = false;
  }
}

// Add admin
if (isset($_POST['addAdminBtn'])) {
  $firstname = $conn->real_escape_string($_POST['first_name']);
  $middlename = $conn->real_escape_string($_POST['middle_name']);
  $lastname = $conn->real_escape_string($_POST['last_name']);
  $gender = $conn->real_escape_string($_POST['gender']);
  $age = $conn->real_escape_string($_POST['age']);
  $birthday = $conn->real_escape_string($_POST['birthday']);
  $email = $conn->real_escape_string($_POST['email_address']);
  $job = $conn->real_escape_string($_POST['job']);
  $address = $conn->real_escape_string($_POST['complete_address']);
  $password = $conn->real_escape_string($_POST['password']);

  $checkEmailResult = $conn->query("SELECT * FROM $usersTable WHERE email_address='$email'");

  if ($checkEmailResult->num_rows == 0) {
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
      password, 
      role,
      status
    ) VALUES(
      '$fullname',
      '$firstname',
      '$middlename',
      '$lastname',
      '$gender',
      '$age',
      '---',
      '$birthday',
      '$email',
      '$job',
      '$address',
      '$password',
      '1',
      'approved'
    )");

    if ($createQuery) {
      $message = "Successfully created a new admin!";
      $hasError = false;
      $hasSuccess = true;
    } else {
      $message = "Failed to create new admin account.\n\n<strong>Reason:\n" . $conn->error . "</strong>";
      $hasError = true;
      $hasSuccess = false;
    }
  } else {
    $message = "The email address '$email' is already registered as an admin!";
    $hasError = true;
    $hasSuccess = false;
  }
}

// Delete admin
if (isset($_POST['deleteAdminBtn'])) {
  $userID = $conn->real_escape_string($_POST['user_id']);

  $deleteAdminResult = $conn->query("DELETE FROM $usersTable WHERE id='$userID'");

  if ($deleteAdminResult) {
    $message = "Admin account has been successfully deleted!";
    $hasError = false;
    $hasSuccess = true;
  } else {
    $message = "Admin account was not successfully deleted!";
    $hasError = true;
    $hasSuccess = false;
  }
}

// Fetch all admins
$adminsResult = $conn->query("SELECT * FROM $usersTable WHERE role='1' AND id<>'${adminInfo['id']}' ORDER BY created_at DESC");
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
      <h6 class="m-0 font-weight-bold" style="color:#223D3C;"><i class="fa fa-user-secret"></i> Admins &nbsp;&nbsp;&nbsp;
        <button type="button" class="btn btn-primary px-auto" style="background-color:#223D3C; border-color:#223D3C;" data-toggle="modal" data-target="#addAdminModal">
          <i class="fa fa-plus-circle"></i>
          Add new
        </button>
      </h6>
    </div>

    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered align-middle" id="admins" width="100%" cellspacing="0" data-ordering="false">
          <thead>
            <tr>
              <th>ID</th>
              <th>Fullname</th>
              <th>Email Address</th>
              <th>Gender</th>
              <th>Complete Address</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $adminsResult->fetch_assoc()) { ?>
              <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['fullname'] ?></td>
                <td><?= $row['email_address'] ?></td>
                <td><?= $row['gender'] ?></td>
                <td><?= $row['complete_address'] ?></td>
                <td>
                  <a class="btn btn-info px-auto" href="#" onClick="openEditAdminModal({
                    user_id: '<?= $row['id'] ?>',
                    first_name: '<?= $row['firstname'] ?>',
                    middle_name: '<?= $row['middlename'] ?>',
                    last_name: '<?= $row['lastname'] ?>',

                    gender: '<?= $row['gender'] ?>',
                    age: '<?= $row['age'] ?>',

                    birthday: '<?= date('Y-m-d', strtotime($row['birthday'])) ?>',
                    email_address: '<?= $row['email_address'] ?>',

                    job: '<?= $row['job'] ?>',
                    complete_address: '<?= $row['complete_address'] ?>'
                  })">
                    <i class="fa fa-pen"></i>
                  </a>
                  <a class="btn btn-danger px-auto" href="#" onClick="openDeleteAdminModal({
                      user_id: '<?= $row['id'] ?>',
                  })">
                    <i class="fa fa-trash"></i>
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



<!-- Delete Modal-->
<div class="modal fade" id="deleteAdminModal" tabindex="-1" role="dialog" aria-labelledby="deleteAdminModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="deleteAdminModalLabel">Delete account</h5>
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>

      <div class="modal-body">Are you sure you really want to delete this admin account?</div>

      <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>

        <form method="POST" id="deleteAdminForm">
          <input type="hidden" name="user_id" value="">
          <button type="submit" name="deleteAdminBtn" class="btn btn-success">Delete</button>
        </form>

      </div>
    </div>
  </div>
</div>
<!-- End Decline Modal-->



<!-- Add Modal -->
<div class="modal fade" id="addAdminModal" tabindex="-1" role="dialog" aria-labelledby="addAdminModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addAdminModalLabel">Add new admin</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form id="addAdminForm" method="POST" autocomplete="off">
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
                <input type="text" name="middle_name" class="form-control" placeholder="(Optional)">
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
            <label> Password </label>
            <input type="text" name="password" class="form-control" placeholder="Enter password..." required>
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
              <button style="background-color:#223D3C; border-color:#223D3C;" type="submit" name="addAdminBtn" class="btn btn-block btn-success">Add</button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- End Add Modal -->



<!-- Edit Modal -->
<div class="modal fade" id="editAdminModal" tabindex="-1" role="dialog" aria-labelledby="editAdminModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editAdminModalLabel">Edit new admin</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form id="editAdminForm" method="POST" autocomplete="off">
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
                <input type="text" name="middle_name" class="form-control" placeholder="(Optional)">
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

          <input type="hidden" name="user_id">

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
              <button style="background-color:#223D3C; border-color:#223D3C;" type="submit" name="editAdminBtn" class="btn btn-block btn-success" disabled>Update</button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- End Edit Modal -->