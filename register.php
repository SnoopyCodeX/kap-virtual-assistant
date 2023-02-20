<?php
session_start();
require_once('./includes/db.inc.php');

// Checks if the user is logged in
if (Auth::isAuthenticated()) {

    // Check if user is an admin and redirect to their respective home pages
    if (Auth::isAdmin())
        header('location: views/admin');
    else
        header('location: views/client');

    return;
}


// Variables for error and success message
$hasError = false;
$hasSuccess = false;
$message = "";


// Check if the register button has been pressed
if (isset($_POST['btnRegister'])) {
    $firstname = $conn->real_escape_string($_POST['firstname']);
    $middlename = $conn->real_escape_string($_POST['middlename']);
    $lastname = $conn->real_escape_string($_POST['lastname']);
    $fullname = "$lastname, $firstname" . (strlen($middlename) > 0 ? " $middlename" : "");
    $gender = $conn->real_escape_string($_POST['gender']);
    $age = $conn->real_escape_string($_POST['age']);
    $birthday = $conn->real_escape_string($_POST['birthday']);
    $email = $conn->real_escape_string($_POST['email_address']);
    $job = $conn->real_escape_string($_POST['job']);
    $address = $conn->real_escape_string($_POST['address']);
    $contactNumber = $conn->real_escape_string($_POST['contact_number']);
    $password = $conn->real_escape_string($_POST['password']);
    $confirmPassword = $conn->real_escape_string($_POST['confirm_password']);

    $idFront = $_FILES['upload_id_front'];
    $idBack = $_FILES['upload_id_back'];

    if (@StringUtils::contains(strtolower($email), strtolower(str_replace(" ", "", $firstname))) && (@StringUtils::contains(strtolower($email), strtolower(str_replace(" ", "", $middlename))) || @StringUtils::contains(strtolower($email), strtolower(str_replace(" ", "", $lastname))))) {

        $result = $conn->query("SELECT * FROM $usersTable WHERE email_address='$email' AND status NOT IN ('pending', 'approved')");

        // Check if the query did not returned a result
        if ($result->num_rows == 0) {

            // If both passwords does not match with each other
            if ($password != $confirmPassword) {
                $message = "The passwords you entered does not match!";
                $hasError = true;
                $hasSuccess = false;
            } else {
                $insertResult = $conn->query("INSERT INTO $usersTable(
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
                    role
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
                    '" . password_hash($password, PASSWORD_BCRYPT) . "',
                    '0'
                )");

                // Check if the query was successful
                if ($insertResult) {
                    $userIDPrefix = $conn->insert_id;
                    $date = date("Y-m-d h-i-s", time());
                    $uploadIDFrontResult = FileUtil::uploadFiles($idFront, "assets/uploads/id_photos", "FRONT_$date-$userIDPrefix");
                    $uploadIDBackResult = FileUtil::uploadFiles($idBack, "assets/uploads/id_photos", "BACK_$date-$userIDPrefix");

                    if ($uploadIDFrontResult['response']['hasError'] || $uploadIDBackResult['response']['hasError']) {
                        $message = "You have been successfully registered <strong>but the IDs were not uploaded</strong> successfully! Please wait for your account to be approved by the admins, a notification will be sent to your mobile number \n\n<strong>Reason: " . (($uploadIDFrontResult['response']['hasError'] ? $uploadIDFrontResult['response']['message'] : $uploadIDBackResult['response']['message']) . "</strong>");
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

                        $message = "You have been successfully registered! Please wait for your account to be approved by the admins, a notification will be sent to your mobile number.";
                        $hasSuccess = true;
                        $hasError = false;
                    }
                } else {
                    $message = "Something went wrong while registering your account";
                    $hasError = true;
                    $hasSuccess = false;
                }
            }
        } else if($result->fetch_assoc()['status'] == 'declined') {
            $updateResult = $conn->query("UPDATE $usersTable SET
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
                complete_address='$address', 
                password='" . password_hash($password, PASSWORD_BCRYPT) . "', 
                role='0',
                status='pending'
                WHERE email_address='$email'
            ");

            // Check if the query was successful
            if ($updateResult) {
                $userIDPrefix = $conn->insert_id;
                $date = date("Y-m-d h-i-s", time());
                $uploadIDFrontResult = FileUtil::uploadFiles($idFront, "assets/uploads/id_photos", "FRONT_$date-$userIDPrefix");
                $uploadIDBackResult = FileUtil::uploadFiles($idBack, "assets/uploads/id_photos", "BACK_$date-$userIDPrefix");

                if ($uploadIDFrontResult['response']['hasError'] || $uploadIDBackResult['response']['hasError']) {
                    $message = "You have been successfully registered <strong>but the IDs were not uploaded</strong> successfully! Please wait for your account to be approved by the admins, a notification will be sent to your mobile number \n\n<strong>Reason: " . (($uploadIDFrontResult['response']['hasError'] ? $uploadIDFrontResult['response']['message'] : $uploadIDBackResult['response']['message']) . "</strong>");
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

                    $message = "You have been successfully registered! Please wait for your account to be approved by the admins, a notification will be sent to your mobile number.";
                    $hasSuccess = true;
                    $hasError = false;
                }
            } else {
                $message = "Something went wrong while registering your account";
                $hasError = true;
                $hasSuccess = false;
            }
        } else {
            $message = "The email address you entered already exists in the database!";
            $hasError = true;
            $hasSuccess = false;
        }
    } else {
        $message = "Your email address must contain your <strong>firstname and middlename or lastname</strong>!";
        $hasError = true;
        $hasSuccess = false;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | ASK KAP</title>

    <link rel="stylesheet" href="./assets/bootstrap/css/bootstrap.min.css">

    <!-- Website Logo -->
    <link rel="shortcut icon" href="./assets/images/barangay-logo.png" type="image/png">

    <!-- TailWind -->
    <script src="./assets/tailwind/tailwind.3.1.8.js"></script>

    <!-- For telephone number input -->
    <link rel="stylesheet" href="./assets/intlTelInput/intlTelInput.css" />
    <script src="./assets/intlTelInput/intlTelInput.min.js"></script>

    <style>
        html,
        body {
            font-size: 100%;
            height: 100vh;
            width: 100vw;
            overflow: auto;
        }
    </style>
</head>

<body>
    <div class="mt-2 col w-100 h-100 d-flex flex-column align-items-center justify-content-center">
        <div class="card bg-[#223D3C] mx-3" style="width: 30%; min-width: 500px; max-width: 550px;">

            <div class="card-header text-light text-center">
                Register to ASK.KAP
            </div>

            <div class="card-body text-light">

                <form class="form d-block" autocomplete="off" method="POST" onsubmit="processFormSubmit(event)" enctype="multipart/form-data">

                    <?php if ($hasError) { ?>
                        <div class="col">
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <span class="text-danger" id="message"><?= $message ?></span>
                            </div>
                        </div>
                    <?php } ?>

                    <?php if ($hasSuccess) { ?>
                        <div class="col">
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <span class="text-success" id="message"><?= $message ?></span>
                            </div>
                        </div>
                    <?php } ?>

                    <div class="input-group rounded border border-0 mb-2">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <span class="fa fa-address-card"></span>
                            </div>
                        </div>
                        <input type="text" class="form-control" id="firstname" name="firstname" placeholder="First name" title="Enter your firstname" required>
                        <input type="text" class="form-control" id="middlename" name="middlename" placeholder="Middle name" title="Enter your middlename">
                        <input type="text" class="form-control" id="lastname" name="lastname" placeholder="Last name" title="Enter your lastname" required>
                    </div>

                    <div class="row">
                        <div class="col-md">
                            <div class="input-group rounded border border-0 mb-2">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <span class="fa fa-venus-mars"></span>
                                    </div>
                                </div>
                                <select name="gender" id="gender" class="form-control" title="Select your gender" required>
                                    <option value="Male" selected>Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md">
                            <div class="input-group rounded border border-0 mb-2">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <span class="fa fa-user"></span>
                                    </div>
                                </div>
                                <input type="number" class="form-control" id="age" name="age" placeholder="Age" min="1" onchange="JavaScript: (() => {parseInt(this.value) <= 0 ? this.value='1' : false})()" title="Enter your age" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm">
                            <div class="input-group rounded border border-0 mb-2">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <span class="fa fa-calendar"></span>
                                    </div>
                                </div>
                                <input type="text" class="form-control" id="birthday" name="birthday" placeholder="Birthday" title="Enter your birthday" onfocus="(this.type='date')" onblur="if(this.value == ''){this.type='text'}" required>
                            </div>
                        </div>

                        <div class="col-sm">
                            <div class="input-group rounded border border-0 mb-2">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <span class="fa fa-envelope"></span>
                                    </div>
                                </div>
                                <input type="email" class="form-control" id="email" name="email_address" placeholder="Email address" title="Enter your email address" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm">
                            <div class="input-group rounded border border-0 mb-2">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <span class="fa fa-wrench"></span>
                                    </div>
                                </div>
                                <input type="text" class="form-control" id="job" name="job" placeholder="Job" title="Enter your job" required>
                            </div>
                        </div>

                        <div class="col-sm">
                            <div class="input-group rounded border border-0 mb-2">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <span class="fa fa-map-location-dot"></span>
                                    </div>
                                </div>
                                <input type="text" class="form-control" id="address" name="address" placeholder="Complete Address" title="Enter your permanent address" required>
                            </div>
                        </div>
                    </div>

                    <div class="input-group rounded border border-0 mb-2">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <span class="fa fa-phone"></span>
                            </div>
                        </div>

                        <input type="tel" class="form-control" id="contact_number" name="contact_number" placeholder="0905 123 4567" title="Enter your contact number" required>
                    </div>

                    <div class="input-group rounded border border-0 mb-2">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <span class="fa fa-key"></span>
                            </div>
                        </div>
                        <input type="text" class="form-control" id="password" placeholder="Password" name="password" title="Enter your password" required>
                        <input type="text" class="form-control" id="confirm_password" placeholder="Confirm password" name="confirm_password" title="Confirm your password" required>
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

                    <div class="row mb-2">
                        <div class="col mb-2">
                            <button type="submit" name="btnRegister" class="btn btn-primary btn-block border border-light">Register</button>
                        </div>

                        <div class="col mb-2">
                            <a href="./login.php" class="btn btn-danger btn-block border border-light">Login</a>
                        </div>
                    </div>

                    <div class="col d-flex justify-content-center">
                        <a class="text-light" href="./index.php">Go Back</a>
                    </div>
                </form>
            </div>

        </div>
    </div>
</body>
<script src="./assets/fontawesome/fontawesome-kit.js"></script>
<script src="./assets/jquery/jquery-3.6.0.min.js"></script>
<script src="./assets/popper/popper-2.9.2.min.js"></script>
<script src="./assets/bootstrap/js/bootstrap.min.js"></script>
<script>
    const processFormSubmit = (event) => {
        let phoneNumber = $("#contact_number").val();

        $("#contact_number").val(phoneNumber.replace(/^0/, '+63'));
        return true;
    };
</script>

</html>