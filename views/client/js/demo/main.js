$(document).ready(function() {

  // Setup datatable of transaction history page
  $('#transactions').DataTable({
    pagingType: "simple_numbers",
    searching: true
  });
  $('#transactions_filter input').attr('placeholder', 'Transaction number');
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
});



// Method for showing the modal for attachments
const viewAttachments = (attachments = []) => {
  let parent = $('#attachments-carousel > .carousel-inner');
  parent.find('.carousel-item').remove();

  let counter = 1;

  for(let attachment of attachments) {
    let path = attachment['path'];
    let type = attachment['type'];

    let carouselItem = $(`<div class="carousel-item${counter == 1 ? ' active' : ''}"></div>`);
    $(carouselItem).append(type.includes('image') 
      ? `<div class="d-flex justify-content-center align-items-center" style="height: 300px;"><img class="h-100 w-auto" src="${path}" alt="Attachment #${counter}"/></div>` 
      : `<div id="trailer" class="section d-flex justify-content-center embed-responsive embed-responsive-21by9">
          <video class="embed-responsive-item" controls loop muted">
            <source src="${path}" type="${type}"/>
            Your browser does not support a video tag
          </video>
        </div>`
    );
    $(carouselItem).append(`<div class="carousel-caption">Attachment #${counter++}</div>`);

    $(parent).append(carouselItem);
  }

  $("#viewAttachments").modal();
};



// Method for opening account settings modal
const processFormSubmit = (event) => {
  let phoneNumber = $("#contact_number").val();

  $("#contact_number").val(phoneNumber.replace(/^0/, '+63'));
  return true;
};

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
  $("#accountSettingsForm .modal-body .form-group input[name='contact_number']").val(data['contact_number'].replace("+63", "0"));

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



// Method for opening view documentation modal
const openViewDocumentationModal = (data = {}) => {
  $("#viewDocumentationForm textarea[name='purpose_of_request']").val(data['purpose_of_request']);
  $("#viewDocumentationForm input[name='document_type']").val(data['type']);

  $("#viewDocumentationModal").modal();
};



// Method for opening cancel documentation modal
const openCancelDocumentationModal = (data = {}) => {
  $("#documentationFormCancel input[name='transaction_number']").val(data['transaction_number']);
  $("#documentationFormCancel input[name='user_id']").val(data['user_id']);

  $("#cancelDocumentationModal").modal();
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

// Method for opening cancel complaint modal
const openCancelComplaintModal = (data = {}) => {
  $("#complaintFormCancel input[name='transaction_number']").val(data['transaction_number']);
  $("#complaintFormCancel input[name='user_id']").val(data['user_id']);

  $("#cancelComplaintModal").modal();
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

// Method for opening cancel blotter modal
const openCancelBlotterModal = (data = {}) => {
  $("#blotterFormCancel input[name='transaction_number']").val(data['transaction_number']);
  $("#blotterFormCancel input[name='user_id']").val(data['user_id']);

  $("#cancelBlotterModal").modal();
};



// Method for opening cancel cctv review modal
const openCancelCCTVReviewModal = (data = {}) => {
  $("#CCTVReviewFormcancel input[name='transaction_number']").val(data['transaction_number']);
  $("#CCTVReviewFormcancel input[name='user_id']").val(data['user_id']);

  $("#cancelCCTVReviewModal").modal();
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