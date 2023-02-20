<?php  
require_once('auth.session.inc.php');
require_once('fileutil.inc.php');
require_once('constants.inc.php');
require_once('smsutil.inc.php');
require_once('ziputil.inc.php');
require_once('security.inc.php');
require_once('dice_coefficient.inc.php');
require_once('utils.inc.php');

// Set post_max_size and upload_max_filesize to 1000M in php.ini
ini_set('post_max_size', '1000M');
ini_set('upload_max_filesize', '1000M');

// Your database connection details
$host = constant("DATABASE_HOST");
$user = constant("DATABASE_USER");
$pass = constant("DATABASE_PASS");

// Create new mysql connection
$conn = new mysqli($host, $user, $pass);

// If failed to connect, show message
if(!$conn)
    die('Failed to connect to the database!');

// Database name
$dbname = constant("DATABASE_NAME");




// Tables name
$usersTable = 'users';
$announcementsTable = 'announcements';
$schedulesTable = 'schedules';
$reportsTable = 'reports';
$transactionsTable = 'transactions';
$documentationsTable = 'documentations';
$complaintsTable = 'complaints';
$blottersTable = 'blotters';
$cctvReviewsTable = 'cctv_reviews';
$feedbacksTable = 'feedbacks';

// Evidences table name for complaints and blotters
$evidencesTable = 'evidences';

// Attachments table name for announcements
$attachmentsTable = 'attachments';

// UploadedIDs table name for user IDs
$uploadedIDsTable = 'uploaded_ids';





















 /***************[ NOTICE ]*******************\
 * THIS SECTION BELOW ARE FOR AUTOMATICALLY   *
 * CREATING THE TABLES AND DATABASE OF THIS   *
 * SYSTEM. DON'T CHANGE ANYTHING BELOW IF YOU *
 * ARE NOT FAMILIAR WITH WHAT YOU'RE DOING.   *
 \********************************************/


/////////////////////////////////
//  CREATE DATABASE SECTION    //
/////////////////////////////////

$__createDBResult__ = $conn->query("CREATE DATABASE IF NOT EXISTS `$dbname`");
if($__createDBResult__) 
    $conn->select_db($dbname);
else 
    die("Failed to create database for the system! Reason: " . $conn->error);

//////////////////////////////////
// END CREATE DATABASE SECTION  //
//////////////////////////////////





////////////////////////////////////
//  CREATE USERS TABLE SECTION    //
////////////////////////////////////

$__createUsersTableResult__ = $conn->query("CREATE TABLE IF NOT EXISTS `$dbname`.`$usersTable`(
    id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    fullname varchar(255) NOT NULL,
    firstname varchar(255) NOT NULL,
    middlename varchar(255) NOT NULL DEFAULT '',
    lastname varchar(255) NOT NULL,

    gender varchar(255) NOT NULL,
    age varchar(255) NOT NULL,

    contact_number varchar(255) NOT NULL,

    birthday varchar(255) NOT NULL,
    email_address varchar(255) NOT NULL,
    
    job varchar(255) NOT NULL,
    complete_address varchar(255) NOT NULL,

    password varchar(255) NOT NULL,
    role int(11) NOT NULL DEFAULT '0',
    status varchar(255) NOT NULL DEFAULT 'pending',
    created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
)");

if(!$__createUsersTableResult__)
    die('Failed to create users table! Reason: ' . $conn->error);

// Create default super admin account
if(($conn->query("SELECT * FROM $usersTable WHERE role='2'"))->num_rows == 0) {
    $insertSuperAdminResult = $conn->query("INSERT INTO $usersTable(
        fullname,
        firstname,
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
        'Admin, Super',
        'Super',
        'Admin',
        'Male',
        '20',
        '---',
        'June 21, 1998',
        'superadmin@gmail.com',
        'Freelance Programmer',
        'Some full, home address',
        '" . password_hash('superadmin123', PASSWORD_BCRYPT) . "',
        '2',
        'approved'
    )");

    if(!$insertSuperAdminResult)
        die("Failed to create a default super admin account! Reason: " . $conn->error);
}

//////////////////////////////////////
//  END CREATE USERS TABLE SECTION  //
//////////////////////////////////////





////////////////////////////////////////////
//  CREATE ANNOUNCEMENTS TABLE SECTION    //
////////////////////////////////////////////

$__createAnnouncementsTableResult__ = $conn->query("CREATE TABLE IF NOT EXISTS `$dbname`.`$announcementsTable`(
    id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    content varchar(255) NOT NULL,
    datetime varchar(255) NOT NULL,
    created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
)");

if(!$__createAnnouncementsTableResult__)
    die('Failed to create announcements table! Reason: ' . $conn->error);

//////////////////////////////////////////////
//  END CREATE ANNOUNCEMENTS TABLE SECTION  //
//////////////////////////////////////////////





////////////////////////////////////////
//  CREATE SCHEDULES TABLE SECTION    //
////////////////////////////////////////

$__createSchedulesTableResult__ = $conn->query("CREATE TABLE IF NOT EXISTS `$dbname`.`$schedulesTable`(
    id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    owner_id int(11) NOT NULL,
    event varchar(255) NOT NULL,
    start_datetime datetime NOT NULL,
    end_datetime datetime NOT NULL,
    allDay boolean NOT NULL DEFAULT FALSE,
    fromAdmin boolean NOT NULL DEFAULT FALSE,
    location varchar(255) NOT NULL,
    created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
)");

if(!$__createSchedulesTableResult__)
    die('Failed to create schedules table! Reason: ' . $conn->error);

//////////////////////////////////////////
//  END CREATE SCHEDULES TABLE SECTION  //
//////////////////////////////////////////





//////////////////////////////////////
//  CREATE REPORTS TABLE SECTION    //
//////////////////////////////////////

$__createReportsTableResult__ = $conn->query("CREATE TABLE IF NOT EXISTS `$dbname`.`$reportsTable`(
    id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    content varchar(255) NOT NULL,
    datetime varchar(255) NOT NULL,
    created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
)");

if(!$__createReportsTableResult__)
    die('Failed to create reports table! Reason: ' . $conn->error);

////////////////////////////////////////
//  END CREATE REPORTS TABLE SECTION  //
////////////////////////////////////////





///////////////////////////////////////////
//  CREATE TRANSACTIONS TABLE SECTION    //
///////////////////////////////////////////

$__createTransactionsTableResult__ = $conn->query("CREATE TABLE IF NOT EXISTS `$dbname`.`$transactionsTable`(
    id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    user_id int(11) NOT NULL,
    transaction_number varchar(255) NOT NULL,
    transaction_type varchar(255) NOT NULL,
    date_created varchar(255) NOT NULL,
    status varchar(255) NOT NULL,
    created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
)");

if(!$__createTransactionsTableResult__)
    die('Failed to create transactions table! Reason: ' . $conn->error);

/////////////////////////////////////////////
//  END CREATE TRANSACTIONS TABLE SECTION  //
/////////////////////////////////////////////





/////////////////////////////////////////////
//  CREATE DOCUMENTATIONS TABLE SECTION    //
/////////////////////////////////////////////

$__createDocumentationsTableResult__ = $conn->query("CREATE TABLE IF NOT EXISTS `$dbname`.`$documentationsTable`(
    id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    owner_id int(11) NOT NULL,
    date_created varchar(255) NOT NULL,
    type varchar(255) NOT NULL,
    purpose_of_request varchar(255) NOT NULL,
    status varchar(255) NOT NULL,
    transaction_number varchar(255) NOT NULL,
    created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
)");

if(!$__createDocumentationsTableResult__)
    die('Failed to create documentations table! Reason: ' . $conn->error);

///////////////////////////////////////////////
//  END CREATE DOCUMENTATIONS TABLE SECTION  //
///////////////////////////////////////////////





//////////////////////////////////////////////////////
//  CREATE COMPLAINTS AND BLOTTERS TABLE SECTION    //
//////////////////////////////////////////////////////

$__createComplaintsTableResult__ = $conn->query("CREATE TABLE IF NOT EXISTS `$dbname`.`$complaintsTable`(
    id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    user_id int(11) NOT NULL,
    case_title varchar(255) NOT NULL,
    date_filed varchar(255) NOT NULL,
    complaint_title varchar(255) NOT NULL,
    who varchar(255) NOT NULL,
    what varchar(255) NOT NULL,
    `when` varchar(255) NOT NULL,
    `where` varchar(255) NOT NULL,
    how varchar(255) NOT NULL,
    nature_of_complaint varchar(255) NOT NULL,
    status varchar(255) NOT NULL,
    transaction_number varchar(255) NOT NULL,
    created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
)");

if(!$__createComplaintsTableResult__)
    die('Failed to create complaints table! Reason: ' . $conn->error);

$__createBlottersTableResult__ = $conn->query("CREATE TABLE IF NOT EXISTS `$dbname`.`$blottersTable`(
    id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    user_id int(11) NOT NULL,
    case_title varchar(255) NOT NULL,
    date_filed varchar(255) NOT NULL,
    blotter_title varchar(255) NOT NULL,
    who varchar(255) NOT NULL,
    what varchar(255) NOT NULL,
    `when` varchar(255) NOT NULL,
    `where` varchar(255) NOT NULL,
    how varchar(255) NOT NULL,
    nature_of_blotter varchar(255) NOT NULL,
    status varchar(255) NOT NULL,
    transaction_number varchar(255) NOT NULL,
    created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
)");

if(!$__createBlottersTableResult__)
    die('Failed to create blotters table! Reason: ' . $conn->error);

////////////////////////////////////////////////////////
//  END CREATE COMPLAINTS AND BLOTTERS TABLE SECTION  //
////////////////////////////////////////////////////////





///////////////////////////////////////////
//  CREATE CCTV REVIEWS TABLE SECTION    //
///////////////////////////////////////////

$__createCCTVReviewsTableResult__ = $conn->query("CREATE TABLE IF NOT EXISTS `$dbname`.`$cctvReviewsTable`(
    id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    user_id int(11) NOT NULL,
    date varchar(255) NOT NULL,
    time varchar(255) NOT NULL,
    exact_location varchar(255) NOT NULL,
    number_of_cctv varchar(255) NOT NULL,
    purpose_of_request varchar(255) NOT NULL,
    status varchar(255) NOT NULL,
    transaction_number varchar(255) NOT NULL,
    created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
)");

if(!$__createCCTVReviewsTableResult__)
    die('Failed to create cctv reviews table! Reason: ' . $conn->error);

/////////////////////////////////////////////
//  END CREATE CCTV REVIEWS TABLE SECTION  //
/////////////////////////////////////////////





////////////////////////////////////////
//  CREATE FEEDBACKS TABLE SECTION    //
////////////////////////////////////////

$__createFeedbacksTableResult__ = $conn->query("CREATE TABLE IF NOT EXISTS `$dbname`.`$feedbacksTable`(
    id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    content varchar(255) NOT NULL,
    user_id int(11) NOT NULL,
    created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
)");

if(!$__createFeedbacksTableResult__)
    die('Failed to create feedbacks table! Reason: ' . $conn->error);

//////////////////////////////////////////
//  END CREATE FEEDBACKS TABLE SECTION  //
//////////////////////////////////////////





////////////////////////////////////////
//  CREATE EVIDENCES TABLE SECTION    //
////////////////////////////////////////

$__createEvidencesTableResult__ = $conn->query("CREATE TABLE IF NOT EXISTS `$dbname`.`$evidencesTable`(
    id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    complaint_blotter_id int(11) NOT NULL,
    path varchar(255) NOT NULL,
    created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
)");

if(!$__createEvidencesTableResult__)
    die('Failed to create evidences table! Reason: ' . $conn->error);

//////////////////////////////////////////
//  END CREATE EVIDENCES TABLE SECTION  //
//////////////////////////////////////////





//////////////////////////////////////////
//  CREATE ATTACHMENTS TABLE SECTION    //
//////////////////////////////////////////

$__createAttachmentsTableResult__ = $conn->query("CREATE TABLE IF NOT EXISTS `$dbname`.`$attachmentsTable`(
    id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    announcement_id int(11) NOT NULL,
    path varchar(255) NOT NULL,
    created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
)");

if(!$__createAttachmentsTableResult__)
    die('Failed to create attachments table! Reason: ' . $conn->error);

////////////////////////////////////////////
//  END CREATE ATTACHMENTS TABLE SECTION  //
////////////////////////////////////////////





///////////////////////////////////////////
//  CREATE UPLOADED IDS TABLE SECTION    //
///////////////////////////////////////////

$__createUploadedIDsTableResult__ = $conn->query("CREATE TABLE IF NOT EXISTS `$dbname`.`$uploadedIDsTable`(
    id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    user_id int(11) NOT NULL,
    path_front varchar(255) NOT NULL,
    path_back varchar(255) NOT NULL,
    created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
)");

if(!$__createUploadedIDsTableResult__)
    die('Failed to create uploaded ids table! Reason: ' . $conn->error);

/////////////////////////////////////////////
//  END CREATE UPLOADED IDS TABLE SECTION  //
/////////////////////////////////////////////
?>