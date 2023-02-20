<?php 
// Global variables for displaying error messages
$message = ""; // Contains the error message 
$hasError = false; // Will set this to true if we have an error
$hasSuccess = false; // Will set this to true if the operation was successful

// If user is not logged in
if(!Auth::isAuthenticated()) {
  header("location: ../../login.php");
  return;
}

// If user is an admin, redirect user to the admin home
if(Auth::isAdmin()) {
  header('location: ../../views/admin');
  return;
}

// If user wants to update his/her account settings
if(isset($_POST['accountSettingsBtn'])) {
  $firstname = $conn->real_escape_string($_POST['first_name']);
  $middlename = $conn->real_escape_string($_POST['middle_name']);
  $lastname = $conn->real_escape_string($_POST['last_name']);

  $gender = $conn->real_escape_string($_POST['gender']);
  $age = $conn->real_escape_string($_POST['age']);
  $birthday = $conn->real_escape_string($_POST['birthday']);
  $email = $conn->real_escape_string($_POST['email_address']);
  $job = $conn->real_escape_string($_POST['job']);
  $address = $conn->real_escape_string($_POST['complete_address']);
  $contactNumber = $conn->real_escape_string($_POST['contact_number']);
  $password = $conn->real_escape_string($_POST['password']);

  $oldAccountData = ($conn->query("SELECT * FROM $usersTable WHERE id='" . Auth::getLoginCookie() . "'"))->fetch_assoc();

  if(!empty($password) && password_verify($password, $oldAccountData['password'])) {
    $message = "You cannot use your old password as your new password!";
    $hasError = true;
    $hasSuccess = false;
  } else {
    $fullname = "$lastname, $firstname" . (strlen($middlename) > 0 ? " $middlename" : "");
    $updateAccountQueryResult = $conn->query("UPDATE $usersTable SET 
      fullname='$fullname',
      firstname='$firstname',
      middlename='$middlename',
      lastname='$lastname',
      gender='$gender', 
      age='$age',
      birthday='$birthday',
      email_address='$email', 
      job='$job', 
      complete_address='$address',
      contact_number='$contactNumber'" . 
      (!empty($password) ? ", password='" . password_hash($password, PASSWORD_BCRYPT) . "'" : "") .
      " WHERE id='" . Auth::getLoginCookie() . "'"
    );
    
    if($updateAccountQueryResult) {
      $message = "Successfully updated your account!";
      $hasError = false;
      $hasSuccess = true;
    } else {
      $message = "Failed to update your account.\n\n<strong>Reason:\n" . $conn->error . "</strong>";
      $hasError = true;
      $hasSuccess = false;
    }
  }
}

// Fetch user info
$userInfo = ($conn->query("SELECT * FROM $usersTable WHERE id='" . Auth::getLoginCookie() . "' AND role='0'"))->fetch_assoc();

// Get current page
$page = $conn->real_escape_string($_GET['page'] ?? 'home');

// Create dynamic title based on current page
$title = ucfirst(str_replace("_", " ", $page));

// Set default timezone to Asia/Manila
date_default_timezone_set('Asia/Manila');
?>

<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title><?= $title ?> | ASK.Kap</title>

  <!-- Custom fonts for this template-->
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css">
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

  <!-- Custom styles for this template-->
  <link href="css/sb-admin-2.min.css" rel="stylesheet">

  <!-- CHAT UI STYLES -->
  <link rel="stylesheet" href="css/chat/chat.css">
  <link rel="stylesheet" href="css/chat/chat-custom.css">
  <link rel="stylesheet" href="css/chat/typing.css">

  <!-- For telephone number input -->
  <link rel="stylesheet" href="../../assets/intlTelInput/intlTelInput.css"/>
  <script src="../../assets/intlTelInput/intlTelInput.min.js"></script>

  <!-- Website Logo -->
  <link rel="shortcut icon" href="../../assets/images/barangay-logo.png" type="image/png">

  <style>
    .table thead th {
      color: #223D3C;
    }

    th.dt-center, td.dt-center {
      text-align: center;
    }
  </style>
</head>

<body id="page-top">

  <!-- Page Wrapper -->
  <div id="wrapper">