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


// Variables for error message
$hasError = false;
$message = "";


// Check if the login button has been pressed
if (isset($_POST['btnLogin'])) {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $conn->real_escape_string($_POST['password']);
    $role = $conn->real_escape_string($_POST['type']);

    $result = $conn->query("SELECT * FROM $usersTable WHERE email_address='$email'");

    // Check if the query returned a result
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if ($role != $row['role'] && $row['role'] != 2) {
            $hasError = true;
            $message = "The email address you entered does not exist on the database!";
        } else {
            // Check if password is correct
            if (password_verify($password, $row['password'])) {

                if ($row['status'] == 'pending') {
                    $hasError = true;
                    $message = "Your account is still currently pending and is waiting for admin's approval!";
                } else if ($row['status'] == 'declined') {
                    $hasError = true;
                    $message = "Your account was not approved by the admins!";
                } else {
                    // Create login cookie
                    Auth::createLoginCookie($row['id']);

                    // Redirect the user to its designated home page
                    // based on his account's role
                    if ($row['role'] == '1' || $row['role'] == '2')
                        header('location: views/admin');
                    else
                        header('location: views/client');
                }
            } else {
                $hasError = true;
                $message = "The password you entered is incorrect!";
            }
        }
    } else {
        $hasError = true;
        $message = "The email address you entered does not exist on the database!";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | ASK KAP</title>

    <link rel="stylesheet" href="./assets/bootstrap/css/bootstrap.min.css">

    <!-- Website Logo -->
    <link rel="shortcut icon" href="./assets/images/barangay-logo.png" type="image/png">

    <script src="./assets/tailwind/tailwind.3.1.8.js"></script>

    <style>
        html,
        body {
            height: 100%;
            width: 100%;
            font-size: 100%;
        }
    </style>
</head>

<body>
    <div class="mt-2 col w-100 h-100 d-flex flex-column align-items-center justify-content-center">
        <div class="card bg-[#223D3C] mx-3" style="width: 40%; min-width: 400px; max-width: 450px;">

            <div class="card-header text-light text-center">
                Login to ASK.KAP
            </div>

            <div class="card-body text-light">
                <form method="POST" autocomplete="off" action="<?= $_SERVER['PHP_SELF'] ?>">
                    <?php if ($hasError) { ?>
                        <div class="col">
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <span class="text-danger" id="message"><?= $message ?></span>
                            </div>
                        </div>
                    <?php } ?>

                    <div class="input-group rounded border border-0 border-dark mb-2">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <span class="fa fa-envelope"></span>
                            </div>
                        </div>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Email address" required>
                    </div>

                    <div class="input-group rounded border border-0 border-dark mb-2">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <span class="fa fa-key"></span>
                            </div>
                        </div>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                    </div>

                    <div class="input-group rounded border border-0 border-dark mb-2">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <span class="fa fa-user"></span>
                            </div>
                        </div>
                        <select name="type" id="type" class="form-control" required>
                            <option value="0" selected>User</option>
                            <option value="1">Admin</option>
                        </select>
                    </div>

                    <div class="row mb-2">
                        <div class="col mb-2">
                            <button type="submit" name="btnLogin" class="btn btn-primary btn-block border border-light">Login</button>
                        </div>

                        <div class="col mb-2">
                            <a href="./register.php" class="btn btn-danger btn-block border border-light">Register</a>
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

</html>