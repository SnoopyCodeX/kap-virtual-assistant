<?php 
session_start();
require_once('includes/auth.session.inc.php');

if(Auth::isAuthenticated()) {
    Auth::clearLoginCookie();
    header('location: /kap');
}
?>