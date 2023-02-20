$(document).ready(function() {
  // Prevent anchor tags with href='#' from adding '#' to the url
  $(() => $("a[href='#']").click((event) => event.preventDefault()));



  // When the add user modal is closed, reset it's form
  $("#addUserModal").on("hidden.bs.modal", () => {
    $(this).find('form').trigger('reset');
  });
  
  
  
  // When the add admin modal is closed, reset it's form
  $("#addAdminModal").on("hidden.bs.modal", () => {
    $(this).find('form').trigger('reset');
  });



  // Setup datatable of admins page
  $('#admins').DataTable({
    pagingType: "simple_numbers",
    searching: true
  });
  // ============================================



  // Setup datatable of users page
  $('#users').DataTable({
    dom: "<'row'<'col-sm-3'l><'col-sm-6'f><'col-sm-3 text-right mt-0 py-0 px-0'B>>" +
         "<'row'<'col-sm-12'tr>>" +
         "<'row'<'col-sm-5'i><'col-sm-7'p>>",
    pagingType: "simple_numbers",
    searching: true,
    buttons: [
      {
        extend: 'print',
        title: "Resident's Data",
        exportOptions: {
          columns: "thead th:not(.exclude)"
        }
      },
      {
        extend: 'csv',
        filename: 'Residents\' Data',
        exportOptions: {
          columns: "thead th:not(.exclude)"
        }
      },
      {
        extend: 'excel',
        filename: 'Residents\' Data',
        exportOptions: {
          columns: "thead th:not(.exclude)"
        }
      },
      {
        extend: 'pdf',
        filename: 'Residents\' Data',
        exportOptions: {
          columns: "thead th:not(.exclude)"
        }
      },
    ]
  });
  // ============================================



  // Setup datatable of transaction history page
  $('#transactions').DataTable({
    pagingType: "simple_numbers",
    searching: true
  });
  $('#transactions_filter input').attr('placeholder', 'Transaction number');
  // ============================================



  // Setup datatable of transaction history page
  $('#detailed_transactions').DataTable({
    pagingType: "simple_numbers",
    searching: true
  });
  $('#detailed_transactions_filter input').attr('placeholder', 'Name of resident');
  // ============================================



  // Setup datatable of schedules page
  $('#schedules').DataTable({
    pagingType: "simple_numbers",
    searching: true
  });
  $('#schedules_filter input').attr('placeholder', 'Event or date');
  // ============================================



  // Setup datatable of reports page
  $('#reports').DataTable({
    pagingType: "simple_numbers",
    searching: true
  });
  // ============================================



  // Setup datatable of documentations page
  $('#documentations').DataTable({
    pagingType: "simple_numbers",
    searching: true
  });
  // ============================================



  // Setup datatable of complaints page
  $('#complaints').DataTable({
    pagingType: "simple_numbers",
    searching: true
  });
  // ============================================



  // Setup datatable of blotters page
  $('#blotters').DataTable({
    pagingType: "simple_numbers",
    searching: true
  });
  // ============================================



  // Setup datatable of cctv reviews page
  $('#cctv-reviews').DataTable({
    pagingType: "simple_numbers",
    searching: true
  });
  // ============================================



  // Setup datatable of feedbacks page
  $('#feedbacks').DataTable({
    pagingType: "simple_numbers",
    searching: true
  });
  $('#feedbacks_filter input').attr('placeholder', 'Fullname');
  // ============================================



  // Setup datatable of bot messages page
  $('#bot-messages').DataTable({
    pagingType: "simple_numbers",
    searching: true
  });
  // ============================================



  // Add max character constraint on the announcement content
  // Just comment the following codes below if you don't
  // want to add a constraint on the announcement content.
  $("#announcement-content-textarea").keyup(() => {
    let currentLength = $("#announcement-content-textarea").val().length;
    let maxLength = 255;

    if(currentLength > maxLength)
      return false;

    $("#characterCounter").html(`Characters: ${currentLength} / 255`);
  });
  // =========================================================



  // =========================================================
  $("#btn-save-gs").on("click", function() {
    $("#form-gs").submit();
  });

  $("#btn-save-dbs").on("click", function() {
    $("#form-dbs").submit();
  });

  $("#btn-save-ts").on("click", function() {
    $("#form-ts").submit();
  });

  let oldGSData = $("#form-gs").serialize();

  $("#form-gs").on('input paste', 'input, select', () => {
    if($("#form-gs").serialize() == oldGSData) {
      $("#btn-save-gs").prop('disabled', true);

      if($("#warning-message").length) {
        $(".container-fluid").find("#warning-message").remove();
      }
    } else {
      $("#btn-save-gs").removeAttr('disabled');

      if(!$("#warning-message").length) {
        $(".container-fluid").prepend(
          `<div class="alert alert-warning alert-dismissible fade show" id="warning-message" role="alert">
            You have an unsaved changes in the settings, please save it first before leaving this page or you will lose all your changes
    
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>`
        );
      } else {
        $("#warning-message").text("You have an unsaved changes in the settings, please save it first before leaving this page or you will lose all your changes");
      }
    }
  });

  let oldDBSData = $("#form-dbs").serialize();

  $("#form-dbs").on('input paste', 'input, select', () => {
    if($("#form-dbs").serialize() == oldDBSData) {
      $("#btn-save-dbs").prop('disabled', true);

      if($("#warning-message").length) {
        $(".container-fluid").find("#warning-message").remove();
      }
    } else {
      $("#btn-save-dbs").removeAttr('disabled');

      if(!$("#warning-message").length) {
        $(".container-fluid").prepend(
          `<div class="alert alert-warning alert-dismissible fade show" id="warning-message" role="alert">
            You have an unsaved changes in the settings, please save it first before leaving this page or you will lose all your changes
    
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>`
        );
      } else {
        $("#warning-message").text("You have an unsaved changes in the settings, please save it first before leaving this page or you will lose all your changes");
      }
    }
  });

  let oldTSData = $("#form-ts").serialize();

  $("#form-ts").on('input paste', 'input, select', () => {
    if($("#form-ts").serialize() == oldTSData) {
      $("#btn-save-ts").prop('disabled', true);

      if($("#warning-message").length) {
        $(".container-fluid").find("#warning-message").remove();
      }
    } else {
      $("#btn-save-ts").removeAttr('disabled');

      if(!$("#warning-message").length) {
        $(".container-fluid").prepend(
          `<div class="alert alert-warning alert-dismissible fade show" id="warning-message" role="alert">
            You have an unsaved changes in the settings, please save it first before leaving this page or you will lose all your changes
    
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>`
        );
      } else {
        $("#warning-message").text("You have an unsaved changes in the settings, please save it first before leaving this page or you will lose all your changes");
      }
    }
  });
  

  $("#change-logo-button").on('click', function(e) {
    e.preventDefault();
    $("#image-picker").click();
  });

  $("#image-picker").change(function(){
    let file = this.files[0];
    let formData = new FormData();
    formData.append('image', file);

    if(!$("#warning-message").length) {
      $(".container-fluid").prepend(
        `<div class="alert alert-warning alert-dismissible fade show" id="warning-message" role="alert">
          You have an unsaved changes in the settings, please save it first before leaving this page or you will lose all your changes
  
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>`
      );
    } else {
      $("#warning-message").text("You have an unsaved changes in the settings, please save it first before leaving this page or you will lose all your changes");
    }

    if($("#btn-save-gs").attr("disabled") !== undefined)
      $("#btn-save-gs").removeAttr("disabled");

    $("#progress-bar").width('0%');
    $("#progress-bar").html("Uploading 0%");

    $("#progress-bar").show();
    $("#change-photo-btn").hide();

    $.ajax({
      xhr: function() {
        let xhr = new window.XMLHttpRequest();

        xhr.upload.addEventListener("progress", function(evt) {
          if (evt.lengthComputable) {
            let percentComplete = evt.loaded / evt.total;
            percentComplete = parseInt(percentComplete * 100);

            $("#progress-bar").width(`${percentComplete}%`);
            $("#progress-bar").html(`Uploading ${percentComplete}%`);
          }
        }, false);

        return xhr;
      },

      type: 'POST',
      url: 'includes/pages/settings.php',
      data: formData,
      processData: false,
      contentType: false,

      success: function(response) {
        setTimeout(() => {
          let objectUrl = URL.createObjectURL(file);

          $("#display-image").attr("src", objectUrl);

          setTimeout(() => {
            URL.revokeObjectURL(objectUrl);
          }, 100);
        }, 100);
      },

      error: function(response) {
        if(!$("#danger-message").length) {
          $(".container-fluid").prepend(
            `<div class="alert alert-danger alert-dismissible fade show" id="danger-message" role="alert">
              Failed to upload the image, reason: ${response}
      
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>`
          );
        } else {
          $("#danger-message").text(`Failed to upload the image, reason: ${response}`);
        }
      },

      complete: function() {
        $("#progress-bar").width('0%');
        $("#progress-bar").html('0%');

        $("#progress-bar").hide();
        $("#change-photo-btn").show();
      }
    });
  });
  // =========================================================
});


// Method for opening edit admin modal
const openEditAdminModal = (data = {}) => {
  $("#editAdminForm .modal-body input[name='user_id']").val(data['user_id']);

  $("#editAdminForm .modal-body .form-group input[name='first_name']").val(data['first_name']);
  $("#editAdminForm .modal-body .form-group input[name='middle_name']").val(data['middle_name']);
  $("#editAdminForm .modal-body .form-group input[name='last_name']").val(data['last_name']);
  
  $("#editAdminForm .modal-body .form-group select[name='gender']").val(data['gender']);
  $("#editAdminForm .modal-body .form-group input[name='age']").val(data['age']);

  $("#editAdminForm .modal-body .form-group input[name='birthday']").val(data['birthday']);
  $("#editAdminForm .modal-body .form-group input[name='email_address']").val(data['email_address']);

  $("#editAdminForm .modal-body .form-group input[name='job']").val(data['job']);
  $("#editAdminForm .modal-body .form-group input[name='complete_address']").val(data['complete_address']);

  let oldData = $("#editAdminForm").serialize();

  $("#editAdminForm").on('input paste', 'input, select', () => {
    if($("#editAdminForm").serialize() == oldData) {
      $("#editAdminForm button[name='editAdminBtn']").prop('disabled', true);
    } else {
      $("#editAdminForm button[name='editAdminBtn']").removeAttr('disabled');
    }
  });

  $("#editAdminModal").modal();
};



// Method for opening edit message modal
const openEditMessageModal = (data = {}) => {
  $("#editMessageForm .modal-body .form-group input[name='question']").val(data['key']);
  $("#editMessageForm .modal-body .form-group textarea[name='response']").val(data['val']);

  let oldData = $("#editMessageForm").serialize();

  $("#editMessageForm").on('input paste', 'input, textarea', () => {
    if($("#editMessageForm").serialize() == oldData) {
      $("#editMessageForm .modal-footer .col button[name='editMessageBtn']").prop('disabled', true);
    } else {
      $("#editMessageForm .modal-footer .col button[name='editMessageBtn']").removeAttr('disabled');
    }
  });

  $("#editMessageModal").modal();
};



// Method for opening delete message modal
const openDeleteMessageModal = (data = {}) => {
  $("#deleteMessageForm input[name='key']").val(data['key']);

  $("#deleteMessageModal").modal();
};



// Method for opening edit user modal
const openEditUserModal = (data = {}) => {
  
  $("#editUserForm .modal-body .form-group input[name='first_name']").val(data['firstname']);
  $("#editUserForm .modal-body .form-group input[name='middle_name']").val(data['middlename']);
  $("#editUserForm .modal-body .form-group input[name='last_name']").val(data['lastname']);
  $("#editUserForm .modal-body .form-group input[name='contact_number']").val(data['contact_number'].replace('+63', '0'));
  $("#editUserForm .modal-body .form-group input[name='email_address']").val(data['email_address']);
  $("#editUserForm .modal-body .form-group select[name='gender']").val(data['gender']);
  $("#editUserForm .modal-body .form-group input[name='complete_address']").val(data['complete_address']);
  $("#editUserForm .modal-body .form-group input[name='birthday']").val(data['birthday']);
  $("#editUserForm .modal-body .form-group input[name='job']").val(data['job']);
  $("#editUserForm .modal-body .form-group input[name='age']").val(data['age']);
  $("#editUserForm .modal-body .form-group input[name='user_id']").val(data['user_id']);

  let oldData = $("#editUserForm").serialize();

  $("#editUserForm").on('input paste', 'input, select', () => {
    if($("#editUserForm").serialize() == oldData) {
      $("#editUserForm button[name='editUserBtn']").prop('disabled', true);
    } else {
      $("#editUserForm button[name='editUserBtn']").removeAttr('disabled');
    }
  });

  $("#editUserModal").modal();
};



// Method for opening delete user modal
const openDeleteUserModal = (data = {}) => {
  $("#deleteUserForm input[name='user_id']").val(data['user_id']);

  $("#deleteUserModal").modal();
};



// Method for opening delete user modal
const openDeclineUserModal = (data = {}) => {
  $("#declineUserForm input[name='user_id']").val(data['user_id']);

  $("#declineUserModal").modal();
};



// Method for opening edit message modal
const openViewIDsModal = (anchor) => {
  let frontIDPath = $(anchor).attr("data-front-src");
  let backIDPath = $(anchor).attr("data-back-src");

  $("#viewIDsForm img[id='front-id']").attr("src", frontIDPath);
  $("#viewIDsForm img[id='back-id']").attr("src", backIDPath);

  console.log(frontIDPath, backIDPath);

  $("#viewIDsModal").modal();
};



// Method for opening delete user modal
const openApproveUserModal = (data = {}) => {
  $("#approveUserForm input[name='user_id']").val(data['user_id']);

  $("#approveUserModal").modal();
};



// Method for opening account settings modal
const openAccountSettingsModal = (data = {}) => {

  $("#accountSettingsForm .modal-body .form-group input[name='first_name']").val(data['first_name']);
  $("#accountSettingsForm .modal-body .form-group input[name='middle_name']").val(data['middle_name']);
  $("#accountSettingsForm .modal-body .form-group input[name='last_name']").val(data['last_name']);
  
  $("#accountSettingsForm .modal-body .form-group select[name='gender']").val(data['gender']);
  $("#accountSettingsForm .modal-body .form-group input[name='age']").val(data['age']);

  $("#accountSettingsForm .modal-body .form-group input[name='birthday']").val(data['birthday']);
  $("#accountSettingsForm .modal-body .form-group input[name='email_address']").val(data['email_address']);

  $("#accountSettingsForm .modal-body .form-group input[name='job']").val(data['job']);
  $("#accountSettingsForm .modal-body .form-group input[name='complete_address']").val(data['complete_address']);

  let oldData = $("#accountSettingsForm").serialize();

  $("#accountSettingsForm").on('input paste', 'input, select', () => {
    if($("#accountSettingsForm").serialize() == oldData) {
      $("#accountSettingsForm button[name='accountSettingsBtn']").prop('disabled', true);
    } else {
      $("#accountSettingsForm button[name='accountSettingsBtn']").removeAttr('disabled');
    }
  });

  $("#accountSettingsForm .modal-body .form-group button[id='changePasswordBtn']").on('click', () => {
    $('#accountSettingsForm .modal-body #password-input .row .col-md-8 input[name="password"]').prop('required', true);
    $('#accountSettingsForm .modal-body #password-input').removeClass('d-none');
    $('#accountSettingsForm .modal-body #change-button').addClass('d-none');
  });

  $("#accountSettingsForm .modal-body #password-input .row .col-md-4 button[id='cancelChangePasswordBtn']").on('click', () => {
    $('#accountSettingsForm .modal-body #password-input .row .col-md-8 input[name="password"]').removeAttr('required');
    $('#accountSettingsForm .modal-body #password-input .row .col-md-8 input[name="password"]').val('');
    $('#accountSettingsForm .modal-body #password-input').addClass('d-none');
    $('#accountSettingsForm .modal-body #change-button').removeClass('d-none');
  });

  $("#accountSettingsModal").modal();
};



// Method for opening delete admin modal
const openDeleteAdminModal = (data = {}) => {
  $("#deleteAdminForm input[name='user_id']").val(data['user_id']);

  $("#deleteAdminModal").modal();
};


// Method for opening approve documentation modal
const openApproveDocumentationModal = (data = {}) => {
  $("#documentationFormApprove").trigger("reset");
  $("#documentationFormApprove input[name='transaction_number']").val(data['transaction_number']);
  $("#documentationFormApprove input[name='contact_number']").val(data['contact_number']);
  $("#documentationFormApprove input[name='type']").val(data['type']);
  $("#documentationFormApprove input[name='name']").val(data['name']);

  $("#approveDocumentationModal").modal();
};



// Method for opening decline documentation modal
const openDeclineDocumentationModal = (data = {}) => {
  $("#documentationFormDecline input[name='transaction_number']").val(data['transaction_number']);
  $("#documentationFormDecline input[name='contact_number']").val(data['contact_number']);
  $("#documentationFormDecline input[name='type']").val(data['type']);
  $("#documentationFormDecline input[name='name']").val(data['name']);

  $("#declineDocumentationModal").modal();
};



// Method for opening view documentation modal
const openViewDocumentationModal = (data = {}) => {
  $("#viewDocumentationForm textarea[name='purpose_of_request']").val(data['purpose_of_request']);
  $("#viewDocumentationForm input[name='document_type']").val(data['type']);

  $("#viewDocumentationModal").modal();
};



// Method for opening approve complaint modal
const openApproveComplaintModal = (data = {}) => {
  $("#complaintFormApprove input[name='transaction_number']").val(data['transaction_number']);
  $("#complaintFormApprove input[name='nature_of_complaint']").val(data['nature_of_complaint']);
  $("#complaintFormApprove input[name='user_id']").val(data['user_id']);

  $("#approveComplaintModal").modal();
};



// Method for opening decline complaint modal
const openDeclineComplaintModal = (data = {}) => {
  $("#complaintFormDecline input[name='transaction_number']").val(data['transaction_number']);
  $("#complaintFormDecline input[name='nature_of_complaint']").val(data['nature_of_complaint']);
  $("#complaintFormDecline input[name='user_id']").val(data['user_id']);

  $("#declineComplaintModal").modal();
};



// Method for opening approve blotter modal
const openApproveBlotterModal = (data = {}) => {
  $("#blotterFormApprove input[name='transaction_number']").val(data['transaction_number']);
  $("#blotterFormApprove input[name='nature_of_blotter']").val(data['nature_of_blotter']);
  $("#blotterFormApprove input[name='user_id']").val(data['user_id']);

  $("#approveBlotterModal").modal();
};



// Method for opening decline blotter modal
const openDeclineBlotterModal = (data = {}) => {
  $("#blotterFormDecline input[name='transaction_number']").val(data['transaction_number']);
  $("#blotterFormDecline input[name='nature_of_blotter']").val(data['nature_of_blotter']);
  $("#blotterFormDecline input[name='user_id']").val(data['user_id']);

  $("#declineBlotterModal").modal();
};



// Method for opening view blotter modal
const openViewBlotterModal = (data = {}) => {
  $("#viewBlotterForm .modal-footer div").removeClass("d-none");
  $("#viewBlotterForm .modal-footer div").removeClass("d-block");
  $("#viewBlotterForm .modal-footer div").addClass("d-none");
  $("#viewBlotterForm .modal-footer button").removeClass("d-none");
  $("#viewBlotterForm .modal-footer button").removeClass("d-block");
  $("#viewBlotterForm .modal-footer button").addClass("d-none");

  $("#viewBlotterForm .modal-body .form-group input[name='who']").val(data['who']);
  $("#viewBlotterForm .modal-body .form-group input[name='what']").val(data['what']);
  $("#viewBlotterForm .modal-body .row .col .form-group input[name='date']").val(data['date']);
  $("#viewBlotterForm .modal-body .row .col .form-group input[name='time']").val(data['time']);
  $("#viewBlotterForm .modal-body .form-group input[name='where']").val(data['where']);
  $("#viewBlotterForm .modal-body .form-group textarea[name='how']").val(data['how']);
  $("#viewBlotterForm .modal-body .form-group input[name='nature_of_blotter']").val(data['nature_of_blotter']);
  $("#viewBlotterForm .modal-body .form-group input[name='complaint_blotter_id']").val(data['complaint_blotter_id']);
  
  if(data['hasEvidence'] === false) {
    $("#viewBlotterForm .modal-footer div").removeClass("d-none");
    $("#viewBlotterForm .modal-footer div").addClass("d-block");
  }else{
    $("#viewBlotterForm .modal-footer button").removeClass("d-none");
    $("#viewBlotterForm .modal-footer button").addClass("d-block");

    $("#viewBlotterForm .modal-footer button").click(() => {
      window.open(`../../download.php?file=${encodeURIComponent(data['download_id'])}`, "_blank");
    });
  }
  
  $("#viewBlotterModal").modal();
};



// Method for opening view complaint modal
const openViewComplaintModal = (data = {}) => {
  $("#viewComplaintForm .modal-footer div").removeClass("d-none");
  $("#viewComplaintForm .modal-footer div").removeClass("d-block");
  $("#viewComplaintForm .modal-footer div").addClass("d-none");
  $("#viewComplaintForm .modal-footer button").removeClass("d-none");
  $("#viewComplaintForm .modal-footer button").removeClass("d-block");
  $("#viewComplaintForm .modal-footer button").addClass("d-none");

  $("#viewComplaintForm .modal-body .form-group input[name='who']").val(data['who']);
  $("#viewComplaintForm .modal-body .form-group input[name='what']").val(data['what']);
  $("#viewComplaintForm .modal-body .row .col .form-group input[name='date']").val(data['date']);
  $("#viewComplaintForm .modal-body .row .col .form-group input[name='time']").val(data['time']);
  $("#viewComplaintForm .modal-body .form-group input[name='where']").val(data['where']);
  $("#viewComplaintForm .modal-body .form-group textarea[name='how']").val(data['how']);
  $("#viewComplaintForm .modal-body .form-group input[name='nature_of_complaint']").val(data['nature_of_complaint']);
  $("#viewComplaintForm .modal-body .form-group input[name='complaint_blotter_id']").val(data['complaint_blotter_id']);
  
  if(data['hasEvidence'] === false) {
    $("#viewComplaintForm .modal-footer div").removeClass("d-none");
    $("#viewComplaintForm .modal-footer div").addClass("d-block");
  } else{
    $("#viewComplaintForm .modal-footer button").removeClass("d-none");
    $("#viewComplaintForm .modal-footer button").addClass("d-block");

    $("#viewComplaintForm .modal-footer button").click(() => {
      window.open(`../../download.php?file=${encodeURIComponent(data['download_id'])}`, "_blank");
    });
  }
  
  $("#viewComplaintModal").modal();
};



// Method for opening approve cctv reviews modal
const openApproveCCTVReviewModal = (data = {}) => {
  $("#CCTVReviewFormApprove input[name='transaction_number']").val(data['transaction_number']);
  $("#CCTVReviewFormApprove input[name='exact_location']").val(data['exact_location']);
  $("#CCTVReviewFormApprove input[name='name']").val(data['name']);
  $("#CCTVReviewFormApprove input[name='date']").val(data['date']);
  $("#CCTVReviewFormApprove input[name='time']").val(data['time']);
  $("#CCTVReviewFormApprove input[name='user_id']").val(data['user_id']);

  $("#approveCCTVReviewModal").modal();
};



// Method for opening decline cctv reviews modal
const openDeclineCCTVReviewModal = (data = {}) => {
  $("#CCTVReviewFormDecline input[name='transaction_number']").val(data['transaction_number']);
  $("#CCTVReviewFormDecline input[name='exact_location']").val(data['exact_location']);
  $("#CCTVReviewFormDecline input[name='name']").val(data['name']);
  $("#CCTVReviewFormDecline input[name='date']").val(data['date']);
  $("#CCTVReviewFormDecline input[name='time']").val(data['time']);
  $("#CCTVReviewFormDecline input[name='user_id']").val(data['user_id']);

  $("#declineCCTVReviewModal").modal();
};



// Method for opening view cctv review modal
const openViewCCTVReviewModal = (data = {}) => {
  $("#viewCCTVReviewRequestForm input[name='transaction_number']").val(data['transaction_number']);
  $("#viewCCTVReviewRequestForm input[name='exact_location']").val(data['exact_location']);
  $("#viewCCTVReviewRequestForm input[name='from_time']").val(data['from_time']);
  $("#viewCCTVReviewRequestForm input[name='to_time']").val(data['to_time']);
  $("#viewCCTVReviewRequestForm input[name='exact_date']").val(data['date']);
  $("#viewCCTVReviewRequestForm input[name='number_of_cctv']").val(data['number_of_cctv']);
  $("#viewCCTVReviewRequestForm textarea[name='purpose_of_request']").val(data['purpose_of_request']);

  $("#viewCCTVReviewRequestModal").modal();
};