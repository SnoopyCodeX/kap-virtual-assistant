<?php 
session_start();
require_once('./includes/db.inc.php');

// Checks if the user is logged in
if(Auth::isAuthenticated()) {

    // Check if user is an admin and redirect to their respective home pages
    if(Auth::isAdmin())
        header('location: views/admin');
    else
        header('location: views/client');

    return;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ASK KAP</title>

    <!-- Website Logo -->
    <link rel="shortcut icon" href="./assets/images/barangay-logo.png" type="image/png">

    <script src="./assets/tailwind/tailwind.3.1.8.js"></script>
</head>
<body>
    <div class="grid grid-cols-1 lg:grid-cols-3 min-h-screen min-w-screen">
        <div class="col-span-2 bg-white flex justify-center items-center flex-col">
            <img src="./assets/images/banner.png" />
            <div class="flex justify-center items-center">
                <span class="text-black text-[1.5rem] md:text-[2rem] text-center">A VIRTUAL BARANGAY FRONT DESK
                    OFFICER</span>
            </div>
            <div class="flex items-center mt-5 w-full justify-center">
                <a class="focus:outline-none bg-[#223D3C] rounded-lg text-white px-10 py-2 font-bold text-[1.5rem] mr-3 md:mr-16"
                    href="./login.php">
                    LOG IN
                </a>
                <a class="focus:outline-none bg-[#223D3C] rounded-lg text-white px-10 py-2 font-bold text-[1.5rem] ml-3 md:ml-16"
                    href="./register.php">
                    REGISTER
                </a>
            </div>
        </div>
        <div class="bg-[#223D3C] flex justify-center items-center px-10 flex-col mt-10 lg:mt-0 pt-10 lg:pt-0">
            <div class="flex flex-row justify-center items-center">
                <img class="w-44 h-44 mr-5" src='./assets/images/barangay-logo.png' />
                <span class="text-[#F2EDDB] font-black text-[2.7rem] text-center leading-tight">
                    <span class="text-[2.1rem]">Brgy. M. Acevida</span> Siniloan Laguna</span>
            </div>
            <div class="flex flex-col justify-center items-center mt-10 w-full ">
                <div class="w-full border-t border-white mb-6"></div>
                <a class="text-[#F2EDDB] font-bold text-[1.8rem] text-center" href="#barangay-profile">Barangay
                    Profile</a>
                <div class="w-full border-t border-white my-6"></div>
                <a class="text-[#F2EDDB] font-bold text-[1.8rem] text-center" href="#history">History</a>
                <div class="w-full border-t border-white my-6"></div>
                <a class="text-[#F2EDDB] font-bold text-[1.8rem] text-center" href="#mission-vision">Mission &
                    Vision</a>
                <div class="w-full border-t border-white my-6"></div>
                <a class="text-[#F2EDDB] font-bold text-[1.8rem] text-center" href="#contact-us">Contact Us</a>
                <div class="w-full border-t border-white my-6"></div>
            </div>
        </div>
    </div>
</body>
</html>