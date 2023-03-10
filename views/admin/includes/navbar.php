   <!-- Sidebar -->
   <ul class="navbar-nav sidebar sidebar-dark accordion" style="background-color:#223D3C;" id="accordionSidebar">

     <!-- Sidebar - Brand -->
     <a class="sidebar-brand d-flex align-items-center justify-content-center" href="./">
       <div class="sidebar-brand-icon">
         <img src="../../assets/images/barangay-logo.png" width="50">
       </div>
       <div class="sidebar-brand-text mx-3">Hello&nbsp;<?= explode(" ", $adminInfo['firstname'])[0] ?></div>
     </a>

     <!-- Divider -->
     <hr class="sidebar-divider my-0">

     <?php if ($adminInfo['role'] == 2) { ?>
       <li class="nav-item <?= $page == 'admins' ? 'active' : '' ?>">
         <a class="nav-link" href="?page=admins">
           <i class="fa fa-user-secret"></i>
           <span>Admins</span>
         </a>
       </li>
     <?php } ?>

     <li class="nav-item <?= $page == 'users' ? 'active' : '' ?>">
       <a class="nav-link" href="?page=users">
         <i class="fa fa-users"></i>
         <span>Users</span>
       </a>
     </li>

     <li class="nav-item <?= $page == 'bot_messages' ? 'active' : '' ?>">
       <a class="nav-link" href="?page=bot_messages">
         <i class="fa fa-robot"></i>
         <span>ChatBot Messages</span>
       </a>
     </li>

     <li class="nav-item <?= $page == 'documentations' ? 'active' : '' ?>">
       <a class="nav-link" href="?page=documentations">
         <i class="fa fa-bookmark"></i>
         <span>Documentations</span>
       </a>
     </li>

     <li class="nav-item <?= $page == 'complaint_and_blotter' ? 'active' : '' ?>">
       <a class="nav-link" href="?page=complaint_and_blotter">
         <i class="fa fa-bullhorn"></i>
         <span>Complaints / Blotters</span>
       </a>
     </li>

     <li class="nav-item <?= $page == 'cctv_reviews' ? 'active' : '' ?>">
       <a class="nav-link" href="?page=cctv_reviews">
         <i class="fa fa-camera"></i>
         <span>CCTV Reviews</span>
       </a>
     </li>

     <li class="nav-item <?= $page == 'feedbacks' ? 'active' : '' ?>">
       <a class="nav-link" href="?page=feedbacks">
         <i class="fa fa-comment"></i>
         <span>Feedbacks</span>
       </a>
     </li>

     <!-- Divider -->
     <hr class="sidebar-divider d-none d-md-block">

   </ul>
   <!-- End of Sidebar -->

   <!-- Content Wrapper -->
   <div id="content-wrapper" class="d-flex flex-column">

     <!-- Main Content -->
     <div id="content">

       <!-- Topbar -->
       <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

         <!-- Sidebar Toggle (Topbar) -->
         <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3" style="color:#223D3C;">
           <i class="fa fa-bars"></i>
         </button>

         <!-- Topbar Navbar -->
         <ul class="navbar-nav ml-auto">

           <!-- Nav Item - Search Dropdown (Visible Only XS) -->
           <li class="nav-item dropdown d-lg-none">
             <a class="nav-link dropdown-toggle text-dark" href="#" id="mainMenuDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
               Main Menu
             </a>

             <!-- Dropdown -->
             <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in" aria-labelledby="mainMenuDropdown">
               <a href="?page=home" class="nav-link text-dark">
                 <span class="fa fa-home"></span>&nbsp;&nbsp;Home
               </a>

               <a href="?page=schedule" class="nav-link text-dark">
                 <span class="fa fa-calendar"></span>&nbsp;&nbsp;Schedule
               </a>

               <a href="?page=reports" class="nav-link text-dark">
                 <span class="fa fa-bullhorn"></span>&nbsp;&nbsp;Reports
               </a>

               <a href="?page=transactions" class="nav-link text-dark">
                 <span class="fa fa-receipt"></span>&nbsp;&nbsp;Transaction History
               </a>

               <a href="?page=settings" class="nav-link text-dark">
                 <span class="fa fa-cogs"></span>&nbsp;&nbsp;System Settings
               </a>
             </div>
           </li>

           <li class="nav-item">
             <a href="?page=home" class="nav-link">
               <span class="mr-2 d-none d-lg-inline text-dark small">
                 <span class="fa fa-home"></span>&nbsp;&nbsp;Home
               </span>
             </a>
           </li>

           <li class="nav-item">
             <a href="?page=schedule" class="nav-link">
               <span class="mr-2 d-none d-lg-inline text-dark small">
                 <span class="fa fa-calendar"></span>&nbsp;&nbsp;Schedule
               </span>
             </a>
           </li>

           <li class="nav-item">
             <a href="?page=reports" class="nav-link">
               <span class="mr-2 d-none d-lg-inline text-dark small">
                 <span class="fa fa-bullhorn"></span>&nbsp;&nbsp;Reports
               </span>
             </a>
           </li>

           <li class="nav-item">
             <a href="?page=transactions" class="nav-link">
               <span class="mr-2 d-none d-lg-inline text-dark small">
                 <span class="fa fa-receipt"></span>&nbsp;&nbsp;Transaction History
               </span>
             </a>
           </li>

           <li class="nav-item">
             <a href="?page=settings" class="nav-link">
               <span class="mr-2 d-none d-lg-inline text-dark small">
                 <span class="fa fa-cogs"></span>&nbsp;&nbsp;System Settings
               </span>
             </a>
           </li>

           <div class="topbar-divider d-none d-sm-block"></div>

           <li class="nav-item dropdown no-arrow">
             <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
               <span class="mr-2 d-none d-lg-inline text-dark small">
                 <?= $adminInfo['lastname'] . ", " . $adminInfo['firstname'] . (!empty($adminInfo['middlename']) ? " " . substr($adminInfo['middlename'], 0, 1) . "." : '') ?>
               </span>
               <img class="img-profile rounded-circle" src="../../assets/images/barangay-logo.png">
             </a>

             <!-- Dropdown - User Information -->
             <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">

               <a class="dropdown-item" href="#" onClick="openAccountSettingsModal({
                  user_id: '<?= $adminInfo['id'] ?>',
                  first_name: '<?= $adminInfo['firstname'] ?>',
                  middle_name: '<?= $adminInfo['middlename'] ?>',
                  last_name: '<?= $adminInfo['lastname'] ?>',

                  gender: '<?= $adminInfo['gender'] ?>',
                  age: '<?= $adminInfo['age'] ?>',

                  birthday: '<?= date('Y-m-d', strtotime($adminInfo['birthday'])) ?>',
                  email_address: '<?= $adminInfo['email_address'] ?>',

                  job: '<?= $adminInfo['job'] ?>',
                  complete_address: '<?= $adminInfo['complete_address'] ?>'
                })">
                 <i class="fas fa-user fa-sm fa-fw mr-2 text-dark"></i>
                 Account
               </a>

               <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                 <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-dark"></i>
                 Logout
               </a>

             </div>
           </li>

         </ul>

       </nav>
       <!-- End of Topbar -->


       <!-- Scroll to Top Button-->
       <a class="scroll-to-top rounded" href="#page-top">
         <i class="fas fa-angle-up"></i>
       </a>


       <!-- Logout Modal-->
       <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
         <div class="modal-dialog" role="document">
           <div class="modal-content">
             <div class="modal-header">
               <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
               <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                 <span aria-hidden="true">??</span>
               </button>
             </div>
             <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
             <div class="modal-footer">
               <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>

               <form action="../../logout.php" method="POST">

                 <button type="submit" name="logout_btn" class="btn btn-primary">Logout</button>

               </form>


             </div>
           </div>
         </div>
       </div>
       <!-- End Logout Modal -->



       <!-- Account Settings Modal -->
       <div class="modal fade" id="accountSettingsModal" tabindex="-1" role="dialog" aria-labelledby="accountSettingsModalLabel" aria-hidden="true">
         <div class="modal-dialog" role="document">
           <div class="modal-content">
             <div class="modal-header">
               <h5 class="modal-title" id="accountSettingsModalLabel">Your Account</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                 <span aria-hidden="true">&times;</span>
               </button>
             </div>

             <form id="accountSettingsForm" method="POST" autocomplete="off">
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

                 <div class="form-group" id="change-button">
                   <label> Password </label>
                   <button type="button" class="form-control btn btn-info" id="changePasswordBtn">Change password</button>
                 </div>

                 <div class="form-group d-none" id="password-input">
                   <label> Password </label>
                   <div class="row">
                     <div class="col-md-8">
                       <input type="text" name="password" class="form-control w-100" placeholder="Enter new password...">
                     </div>

                     <div class="col-md-4">
                       <button type="button" class="form-control btn btn-danger" id="cancelChangePasswordBtn">Cancel</button>
                     </div>
                   </div>
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
                     <button style="background-color:#223D3C; border-color:#223D3C;" type="submit" name="accountSettingsBtn" class="btn btn-block btn-success" disabled>Update</button>
                   </div>
                 </div>
               </div>
             </form>
           </div>

         </div>
       </div>
       <!-- End Account Settings Modal -->