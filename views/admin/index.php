<?php
require_once('../../includes/db.inc.php');
require_once('includes/header.php'); 
require_once('includes/navbar.php'); 

switch($page) {
    case 'admins':
        if(isset($adminInfo) && $adminInfo['role'] == 2)
            require_once('includes/pages/admins.php');
        else 
            echo json_encode(['code' => 404, 'message' => "The page '$page' is not found!"], JSON_PRETTY_PRINT);
    break;

    case 'users':
        require_once('includes/pages/users.php');
    break;

    case 'bot_messages':
        require_once('includes/pages/bot_messages.php');
    break;

    case 'documentations':
        require_once('includes/pages/documentations.php');
    break;

    case 'complaint_and_blotter':
        require_once('includes/pages/complaint_and_blotter.php');
    break;

    case 'cctv_reviews':
        require_once('includes/pages/cctv_reviews.php');
    break;

    case 'feedbacks':
        require_once('includes/pages/feedbacks.php');
    break;

    case 'schedule':
        require_once('includes/pages/schedule.php');
    break;

    case 'reports':
        require_once('includes/pages/reports.php');
    break;

    case 'transactions':
        require_once('includes/pages/transactions.php');
    break;
    
    case 'settings':
        require_once('includes/pages/settings.php');
    break;

    case 'home':
    default:
        require_once('includes/pages/announcements.php');
    break;
}

require_once('includes/scripts.php');
require_once('includes/footer.php');
?>