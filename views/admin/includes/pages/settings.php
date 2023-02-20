<?php

if (isset($_POST['baranggay_name']) && isset($_POST['baranggay_address'])) {
    $jsonSettings = json_decode(FileUtil::readFile("../../includes/settings.inc.json", 1024), true);

    $baranggayName = $conn->real_escape_string($_POST['baranggay_name']);
    $baranggayAddress = $conn->real_escape_string($_POST['baranggay_address']);
    $baranggayLogo = $_FILES['image-logo'];

    if (!empty($baranggayLogo['name'])) {
        $filepaths = FileUtil::uploadFiles($baranggayLogo, '../../assets/images', '', 'barangay-logo.png', false);

        if ($filepaths['response']['hasError']) {
            $message = "Failed to upload your photo. <strong>Reason: " . $filepaths['response']['message'] . "</strong>";
            $hasError = true;
            $hasSuccess = false;
        } else {
            $jsonSettings['SYSTEM_SETTINGS']['BARANGGAY_NAME'] = $baranggayName;
            $jsonSettings['SYSTEM_SETTINGS']['BARANGGAY_ADDRESS'] = $baranggayAddress;
            $jsonSettingsUpdated = json_encode($jsonSettings, JSON_PRETTY_PRINT);

            FileUtil::writeFile("../../includes/settings.inc.json", $jsonSettingsUpdated);

            $message = "Successfully updated the general settings!";
            $hasError = false;
            $hasSuccess = true;
        }
    } else {
        $jsonSettings['SYSTEM_SETTINGS']['BARANGGAY_NAME'] = $baranggayName;
        $jsonSettings['SYSTEM_SETTINGS']['BARANGGAY_ADDRESS'] = $baranggayAddress;
        $jsonSettingsUpdated = json_encode($jsonSettings, JSON_PRETTY_PRINT);

        FileUtil::writeFile("../../includes/settings.inc.json", $jsonSettingsUpdated);

        $message = "Successfully updated the general settings!";
        $hasError = false;
        $hasSuccess = true;
    }
}

if(isset($_POST['database_name']) && isset($_POST['database_host']) && isset($_POST['database_username']) && isset($_POST['database_password'])) {
    $jsonSettings = json_decode(FileUtil::readFile("../../includes/settings.inc.json", 1024), true);

    $host = $conn->real_escape_string($_POST['database_host']);
    $name = $conn->real_escape_string($_POST['database_name']);
    $user = $conn->real_escape_string($_POST['database_username']);
    $pass = $conn->real_escape_string($_POST['database_password']);

    $jsonSettings['DATABASE_ACCOUNT']['DATABASE_HOST'] = $host;
    $jsonSettings['DATABASE_ACCOUNT']['DATABASE_NAME'] = $name;
    $jsonSettings['DATABASE_ACCOUNT']['DATABASE_USER'] = $user;
    $jsonSettings['DATABASE_ACCOUNT']['DATABASE_PASS'] = $pass;

    $jsonSettingsUpdated = json_encode($jsonSettings, JSON_PRETTY_PRINT);
    FileUtil::writeFile("../../includes/settings.inc.json", $jsonSettingsUpdated);

    $message = "Successfully updated the database settings!";
    $hasError = false;
    $hasSuccess = true;
}

if(isset($_POST['twilio_auth_token']) && isset($_POST['twilio_sid_token']) && isset($_POST['twilio_messaging_sid_token']) && isset($_POST['twilio_failsafe_login_token'])) {
    $jsonSettings = json_decode(FileUtil::readFile("../../includes/settings.inc.json", 1024), true);

    $tAuthToken = $conn->real_escape_string($_POST['twilio_auth_token']);
    $tSIDToken = $conn->real_escape_string($_POST['twilio_sid_token']);
    $tMSIDToken = $conn->real_escape_string($_POST['twilio_messaging_sid_token']);
    $tFLToken = $conn->real_escape_string($_POST['twilio_failsafe_login_token']);

    $jsonSettings['TWILIO_ACCOUNT']['TWILIO_AUTH_TOKEN'] = $tAuthToken;
    $jsonSettings['TWILIO_ACCOUNT']['TWILIO_SID_TOKEN'] = $tSIDToken;
    $jsonSettings['TWILIO_ACCOUNT']['TWILIO_MESSAGING_SID_TOKEN'] = $tMSIDToken;
    $jsonSettings['TWILIO_ACCOUNT']['TWILIO_FAILSAFE_LOGIN_TOKEN'] = $tFLToken;

    $jsonSettingsUpdated = json_encode($jsonSettings, JSON_PRETTY_PRINT);
    FileUtil::writeFile("../../includes/settings.inc.json", $jsonSettingsUpdated);

    $message = "Successfully updated the twilio account settings!";
    $hasError = false;
    $hasSuccess = true;
}

$jsonSettings = json_decode(FileUtil::readFile("../../includes/settings.inc.json", 1024), true);
?>

<style>
    .row-custom {
        display: flex;
        flex-direction: row;
        justify-content: space-between;
    }
</style>

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
            <h6 class="m-0 font-weight-bold" style="color:#223D3C;">
                <i class="fa fa-cogs"></i> General Settings &nbsp;&nbsp;&nbsp;&nbsp;
                <button type="button" class="btn btn-primary m-0 px-auto" style="background-color:#223D3C; border-color:#223D3C;" id="btn-save-gs" disabled>
                    <i class="fa fa-save"></i>
                    Save
                </button>
            </h6>
        </div>

        <div class="card-body">
            <form method="post" autocomplete="off" enctype="multipart/form-data" id="form-gs">
                <div class="row">
                    <div class="col-md">
                        <div class="form-group">
                            <label for="baranggay_name">Baranggay Name</label>
                            <input type="text" name="baranggay_name" id="baranggay_name" class="form-control" value="<?= $jsonSettings['SYSTEM_SETTINGS']['BARANGGAY_NAME'] ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="baranggay_address">Baranggay Address</label>
                            <input type="text" name="baranggay_address" id="baranggay_address" class="form-control" value="<?= $jsonSettings['SYSTEM_SETTINGS']['BARANGGAY_ADDRESS'] ?>" required>
                        </div>
                    </div>
                    <div class="col-md">
                        <img src="../../assets/images/barangay-logo.png" alt="Baranggay Logo" class="rounded mx-auto d-block" id="display-image" height="150px">
                        <button class="btn btn-block btn-primary my-2" id="change-logo-button" name="btnSaveGS">Change Logo</button>

                        <input type="file" id="image-picker" name="image-logo" style="display:none;" accept="image/*">

                        <div class="progress" style="display: none;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" id="progress-bar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold" style="color:#223D3C;">
                <i class="fa fa-cogs"></i> Database Settings &nbsp;&nbsp;&nbsp;&nbsp;
                <button type="button" class="btn btn-primary m-0 px-auto" style="background-color:#223D3C; border-color:#223D3C;" id="btn-save-dbs" disabled>
                    <i class="fa fa-save"></i>
                    Save
                </button>
            </h6>
        </div>

        <div class="card-body">
            <form method="post" autocomplete="off" id="form-dbs">
                <div class="row">
                    <div class="col-md">
                        <div class="form-group">
                            <label for="database_name">Database Name</label>
                            <input type="text" name="database_name" id="database_name" class="form-control" value="<?= $jsonSettings['DATABASE_ACCOUNT']['DATABASE_NAME'] ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="database_host">Database Host</label>
                            <input type="text" name="database_host" id="database_host" class="form-control" value="<?= $jsonSettings['DATABASE_ACCOUNT']['DATABASE_HOST'] ?>" required>
                        </div>
                    </div>
                    <div class="col-md">
                        <div class="form-group">
                            <label for="database_username">Database Username</label>
                            <input type="text" name="database_username" id="database_username" class="form-control" value="<?= $jsonSettings['DATABASE_ACCOUNT']['DATABASE_USER'] ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="database_password">Database Password</label>
                            <input type="text" name="database_password" id="database_password" class="form-control" value="<?= $jsonSettings['DATABASE_ACCOUNT']['DATABASE_PASS'] ?>" required>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold" style="color:#223D3C;">
                <i class="fa fa-cogs"></i> Twilio SMS API Settings &nbsp;&nbsp;&nbsp;&nbsp;
                <button type="button" class="btn btn-primary m-0 px-auto" style="background-color:#223D3C; border-color:#223D3C;" id="btn-save-ts" disabled>
                    <i class="fa fa-save"></i>
                    Save
                </button>
            </h6>
        </div>

        <div class="card-body">
            <form method="post" autocomplete="off" id="form-ts">
                <div class="row">
                    <div class="col-md">
                        <div class="form-group">
                            <label for="twilio_auth_token">Auth Token</label>
                            <input type="text" name="twilio_auth_token" id="twilio_auth_token" class="form-control" value="<?= $jsonSettings['TWILIO_ACCOUNT']['TWILIO_AUTH_TOKEN'] ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="twilio_test_auth_token">Test Auth Token</label>
                            <input type="text" name="twilio_test_auth_token" id="twilio_test_auth_token" class="form-control" value="<?= $jsonSettings['TWILIO_ACCOUNT']['TWILIO_TEST_AUTH_TOKEN'] ?>" required>
                            <span class="text-small text-warning" style="font-weight: lighter;"><i class="fa fa-info-circle"></i> This token will be used for testing the SMS API of <a class="text-warning" style="font-weight: lighter;" href="https://twilio.com/" target="_blank"><i class="fa fa-link"></i> twilio.com</a>.</span>
                        </div>

                        <div class="form-group">
                            <label for="twilio_sid_token">SID Token</label>
                            <input type="text" name="twilio_sid_token" id="twilio_sid_token" class="form-control" value="<?= $jsonSettings['TWILIO_ACCOUNT']['TWILIO_SID_TOKEN'] ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="twilio_test_sid_token">Test SID Token</label>
                            <input type="text" name="twilio_test_sid_token" id="twilio_test_sid_token" class="form-control" value="<?= $jsonSettings['TWILIO_ACCOUNT']['TWILIO_TEST_SID_TOKEN'] ?>" required>
                            <span class="text-small text-warning" style="font-weight: lighter;"><i class="fa fa-info-circle"></i> This token will be used for testing the SMS API of <a class="text-warning" style="font-weight: lighter;" href="https://twilio.com/" target="_blank"><i class="fa fa-link"></i> twilio.com</a>.</span>
                        </div>
                    </div>
                    <div class="col-md">
                        <div class="form-group">
                            <label for="twilio_messaging_sid_token">Messaging SID Token</label>
                            <input type="text" name="twilio_messaging_sid_token" id="twilio_messaging_sid_token" class="form-control" value="<?= $jsonSettings['TWILIO_ACCOUNT']['TWILIO_MESSAGING_SID_TOKEN'] ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="twilio_failsafe_login_token">Failsafe Login Token</label>
                            <input type="text" name="twilio_failsafe_login_token" id="twilio_failsafe_login_token" class="form-control" value="<?= $jsonSettings['TWILIO_ACCOUNT']['TWILIO_FAILSAFE_LOGIN_TOKEN'] ?>" required>
                            <span class="text-small text-warning" style="font-weight: lighter;"><i class="fa fa-info-circle"></i> This token will be used for login when you forgot your password in <a class="text-warning" style="font-weight: lighter;" href="https://twilio.com/" target="_blank"><i class="fa fa-link"></i> twilio.com</a>.</span>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>
<!-- End Main Content -->