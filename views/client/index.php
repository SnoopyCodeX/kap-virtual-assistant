<?php
require_once("../../assets/twilio/src/Twilio/autoload.php");
require_once('../../includes/db.inc.php');
include_once('includes/header.php'); 
include_once('includes/navbar.php'); 

switch($page) {
    case 'documentations':
        include('includes/pages/documentations.php');
    break;

    case 'complaint_and_blotter':
        include('includes/pages/complaint_and_blotter.php');
    break;

    case 'cctv_reviews':
        include('includes/pages/cctv_reviews.php');
    break;

    case 'schedule':
        include('includes/pages/schedule.php');
    break;

    case 'feedbacks':
        include('includes/pages/feedbacks.php');
    break;

    case 'transactions':
        include('includes/pages/transactions.php');
    break;

    case 'home':
    default:
        include('includes/pages/announcements.php');
    break;
}

include_once('includes/chatui.php');
include_once('includes/scripts.php');
include_once('includes/footer.php');
?>