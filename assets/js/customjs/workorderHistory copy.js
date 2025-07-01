let dataObj; // Global variable for data storage
let rowDataMap = {}; // A map to store row data by unique IDs

function getWorkOrderDataApproval() {
  const formData = new FormData();
  formData.append('type', 'history');

  $.ajax({
    url: `controller/getListWorkorder.php`,
    type: 'Post',
    processData: false, // Important for sending FormData
    contentType: false, // Important for sending FormData
    data: formData,

    beforeSend: function () {
      handleLoader('flex');
    },
    success: function (data) {
      handleLoader('flex');
      try {
        let row = '';
        if (data.success) {
          // showNotification('success', 'Data found');
          dataObj = data.data;
          dataObj = dataObj.reverse();
          data.data.map((el, index) => {
            // Create a unique identifier for each row
            const uniqueId = `row-${index}`;
            rowDataMap[uniqueId] = el; // Store data in the map

            let amount = el.amount || '0';

            // Handle status actions for different statuses
            let statusActions = '';
            switch (el.status) {
              case 'pending':
                statusActions = `<a role="button" title="View Status" onclick="openStatusModal('pending')">
                    <span class="badge badge-pill badge-soft-warning font-size-12">Pending</span>
                  </a>`;
                break;

              case 'Job Completion by vendor':
                statusActions = `
                  <a class="btn btn-action complete" onclick="markJobCompleted('workorder Completion by dhl', '${el.id}','${el.jobTitle}')">
                    <span class="badge badge-pill badge-soft-info font-size-12">Mark Workorder Completed</span>
                  </a>
                `;
                break;


              case 'workorder Completion by dhl':
                statusActions = `
                  <a class="btn btn-action complete" onclick="openStatusModal('Workorder Accepted By DHL')">
                    <span class="badge badge-pill badge-soft-success font-size-12">Workorder Accepted By DHL</span>
                  </a>
                `;
                break;


              // case 'in progress':
              //   statusActions = `<a role="button" title="View Status" onclick="openStatusModal('inProgress')">
              //       <span class="badge badge-pill badge-soft-primary font-size-12">In Progress</span>
              //     </a>`;
              //   break;

              case 'in progress':
                if (amount <= maxAmount) {
                  statusActions = `<a class="btn btn-action accept" title="Accept Workorder" onclick="openStatusModal('underReview','${el.id}','${index}')">
                    <span class="badge badge-pill badge-soft-info font-size-12">Under Review</span>
                  </a>
                  `;
                } else {
                  statusActions = `<a role="button" title="View Status" onclick="openStatusModal('notAccess')">
                    <span class="badge badge-pill badge-soft-info font-size-12">Under Review</span>
                  </a>`;
                }
                break;

              case 'under review':
                statusActions = `<a role="button" title="View Status" onclick="markJobCompleted('confirm','${el.id}','${el.jobTitle}')">
                    <span class="badge badge-pill badge-soft-danger font-size-12">Confirm</span>
                  </a>`;
                break;

              case 'Completed':
                statusActions = `<a role="button" title="View Status" onclick="openStatusModal('Completed')">
                    <span class="badge badge-pill badge-soft-success font-size-12">Completed</span>
                  </a>`;
                break;

              case 'rejected by dhl':
                statusActions = `<a role="button" title="View Status" onclick="openStatusModal('rejected by dhl','${el.id}','${index}','${el.reason}')">
                    <span class="badge badge-pill badge-soft-danger font-size-12">Rejected by DHL</span>
                  </a>`;
                break;

              default:
                statusActions = `<a role="button" title="View Status" onclick="openStatusModal('${el.status}')">
                  <span class="badge badge-pill badge-soft-secondary font-size-12">${capitalizeFirstLetter(el.status)}</span>
                </a>`;
            }

            // Construct the row HTML
            row += `
              <tr id="${uniqueId}">
                <td>${index + 1}</td> <!-- Row number -->
                <td style="width: 100px">${statusActions}</td> <!-- Status actions (custom content) -->
                <td>${el.creationdate || '-'}</td> <!-- Creation date with fallback -->
                <td>${el.Reg || '-'}</td> <!-- Job title with fallback -->
                <td>${el.jobTitle || '-'}</td> <!-- Job title with fallback -->
                <td>${el.service || '-'}</td> <!-- Services with fallback -->
                <td>${el.amount || '-'}</td> <!-- Amount with fallback -->
                <td>${el.reason || '-'}</td> <!-- Reason with fallback -->
                <td>
                  ${amount <= maxAmount
                ? `<a href="#" class="view-workorder" data-row-id="${uniqueId}">View Workorder</a>`
                : `-`}
                </td>
                <td>
                  ${amount <= maxAmount
                ? el.invoice_url
                  ? `<a class='text-success' href="invoice/${el.invoice_url}" target="_blank">View Invoice</a>`
                  : `-`
                : `-`}
                </td>
              </tr>
            `;
          });

          // Initialize DataTable
          initializeDataTable('workOrderHistoryTable', row);
        } else {
          showNotification('error', data.message);
        }
      } catch (error) {
        showNotification('error', error.message);
        console.error('Error in apiCall:', error.message);
      }
    },
    error: function (xhr, status, error) {
      console.error('Error in ajax request:', error);
    },
    complete: function () {
      handleLoader('none');
    },
  });
}



function markJobCompleted(status, id, jobTitle) {
  Swal.fire({
    title: 'Are you sure?',
    html: `
        <p class='custom-msg'>You want to Mark This Job Completed <br><span class="custom-msg-reg">${jobTitle}</span>.</p>
      `,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Confirm',
  }).then((result) => {
    if (result.isConfirmed) {
      const formData = new FormData();


      formData.append('type', 'update');
      formData.append('status', status);
      formData.append('id', id);

      $.ajax({
        url: `controller/getListWorkorder.php`,
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        beforeSend: function () {
          handleLoader('flex');
        },
        success: function (res) {
          if (res.success) {
            let message = '';
            Swal.fire({
              title: 'Success!',
              text: 'Job has been marked as completed.',
              icon: 'success',
            });
            getWorkOrderDataApproval();
          } else {
            Swal.fire({
              title: 'Error!',
              text: 'Something went wrong, unable to update the vehicle status.',
              icon: 'error',
            });
          }
        },
        error: function (xhr, status, error) {
          console.log('Error during form submission:', xhr.responseText);
          Swal.fire({
            title: 'Error!',
            text: 'An error occurred while processing your request.',
            icon: 'error',
          });
        },
        complete: function () {
          handleLoader('none');
        },
      });
    }
  });
}

function getJobDataApproval() {
  const formData = new FormData();
  formData.append('type', 'getJobList');

  $.ajax({
    url: `controller/getlistworkorder.php`,
    type: 'Post',
    processData: false, // Important for sending FormData
    contentType: false, // Important for sending FormData
    data: formData,

    beforeSend: function () {
      handleLoader('flex');
    },
    success: function (data) {
      handleLoader('flex');
      try {
        let row = '';
        if (data.success) {
          // showNotification('success', 'Data found');
          dataObj = data.data;
          dataObj = dataObj.reverse();
          data.data.map((el, index) => {
            // Create a unique identifier for each row
            const uniqueId = `row-${index}`;
            rowDataMap[uniqueId] = el; // Store data in the map

            let amount = el.amount || '0';

            // Handle status actions for different statuses
            let statusActions = '';
            switch (el.status?.toLowerCase()) {
              case 'pending':
                statusActions = `<a role="button" title="View Status" onclick="openStatusModal('Jobpending')">
                    <span class="badge badge-pill badge-soft-warning font-size-12">Pending</span>
                  </a>`;
                break;

              case 'submit quotation':
                statusActions = ` <a class="btn btn-action accept" title="Accept" onclick="submitForm('jobAccept', '${el.id}', '${el.V_id}')">
                    <i class="fas fa-check-circle"></i>
                  </a>
                  <a class="btn btn-action reject" title="Reject" onclick="openStatusModal('jobReject','${el.id}','${el.jobTitle}','${el.V_id}')">
                    <i class="fas fa-times-circle"></i>
                  </a>`;
                break;

              case 'accept':
                statusActions = `<a role="button" title="View Status" onclick="openStatusModal('jobAccepted','${el.id}','${index}','${el.reason}')">
                    <span class="badge badge-pill badge-soft-success font-size-12">Accepted</span>
                  </a>`;
                break;

              case 'reject':
                statusActions = `<a role="button" title="View Status" onclick="openStatusModal('jobRejected','${el.id}','${index}','${el.reason}')">
                    <span class="badge badge-pill badge-soft-danger font-size-12">Rejected</span>
                  </a>`;
                break;

              case 'completed':
                statusActions = `<a role="button" title="View Status" onclick="openStatusModal('completed')">
                    <span class="badge badge-pill badge-soft-success font-size-12">Completed</span>
                  </a>`;
                break;
            }

            // Construct the row HTML
            row += `
              <tr id="${uniqueId}">
                <td>${index + 1}</td> <!-- Row number -->
                <td style="width: 100px">${statusActions}</td>
                <td>${el.creationdate || '-'}</td>
                <td>${el.jobTitle || '-'}</td>
                <td>${el.Reg || '-'}</td>
                <td>${el.quotation || '-'}</td>
                <td>${el.quotationDescription || '-'}</td>
                <td>${capitalizeFirstLetter(el.createdbyName) || '-'}</td>
                <td>${el.description || '-'}</td>
                <td>${el.reason || '-'}</td>
                
            `;
          });

          // Initialize DataTable
          initializeDataTable('jobHistoryTable', row);
        } else {
          showNotification('error', data.message);
        }
      } catch (error) {
        showNotification('error', error.message);
        console.error('Error in apiCall:', error.message);
      }
    },
    error: function (xhr, status, error) {
      console.error('Error in ajax request:', error);
    },
    complete: function () {
      handleLoader('none');
    },
  });
}
// Event delegation: Add event listener to the <tbody> element
$(document).on('click', '#workOrderHistoryTable tbody .view-workorder', function (event) {
  event.preventDefault();
  const uniqueId = $(this).data('row-id'); // Get the unique row ID
  const rowData = rowDataMap[uniqueId]; // Retrieve the row data from the map
  console.log(rowData);
  console.log(rowData['status'])
  // if (rowData['status']) {
  // }
  generatePdf(rowData); // Pass the row data to generate PDF
});




// getWorkOrderDataApproval();
getJobDataApproval();

function openStatusModal(status, id = '', index = '', vendorReason = '') {
  const modalBody = document.getElementById('modalBodyContent');
  modalBody.innerHTML = '';

  let cardContent = '';
  switch (status) {


    // Job Status
    case 'Jobpending':
      cardContent = `
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Job Status: Pending</h5>
            <p class="card-text">The Job is currently in a pending state. Please wait for vendor to approve it.</p>
          </div>
        </div>`;
      break;
    case 'jobAccepted':
      cardContent = `
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Job Status: Accepted</h5>
              <p class="card-text">The job has been accepted. Please proceed to the next steps for vendor approval.</p>
            </div>
          </div>`;
      break;
    case 'jobRejected':
      cardContent = `
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Job Status: Rejected</h5>
              <p class="card-text">The job has been rejected By the DHL.</p>
            </div>
          </div>`;
      break;

    case 'jobReject':
      cardContent = `
        <div class="card">
          <div class="card-body">
            <form id="jobRejectedForm">
              <div class="mb-3">
                <label for="jrReason" class="form-label">Reason To Reject</label>
                <textarea id="jrReason" class="form-control" maxlength="225" rows="3"
                  placeholder="Enter Reason for Reject" required></textarea>
              </div>
              <button type="submit" class="btn btn-primary">Submit</button>
            </form>
          </div>
        </div>`;
      break;

    case 'pending':
      cardContent = `
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Work Order Status: Pending</h5>
            <p class="card-text">The work order is currently in a pending state. Please wait for vendor to approve it.</p>
          </div>
        </div>`;
      break;

    case 'inProgress':
      cardContent = `
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Work Order Status: In Progress</h5>
              <p class="card-text">The work order has been approved by the vendor and is currently being worked on. You can monitor the progress here.</p>
            </div>
          </div>`;
      break;
    case 'underReview':
      const currentDate = new Date().toLocaleDateString('en-GB'); // Format: dd/mm/yyyy

      // Split the currentDate into day, month, and year
      const [day, month, year] = currentDate.split('/');

      // Rearrange to yyyy/mm/dd
      const formattedDate = `${year}/${month}/${day}`;

      console.log(formattedDate); // Output: yyyy/mm/dd
      cardContent = `
          <form id="underReviewForm">
            <div class="card">
                <div class="card-body pb-0">
                    <div class="mb-1">
                        <div class="row">
                            <div class="col-md-9">
                                <h5 class="card-title">Work Order Completion</h5>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="partsReceived" checked='true'>
                                    <label class="form-check-label" for="partsReceived">
                                        The required parts have been received.
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="workCompleted" checked='true'>
                                    <label class="form-check-label" for="workCompleted">
                                        The work on the order has been completed with satisfaction.
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label" for="completionDate">Completion Date</label>
                                <input type="text" id="completionDate" class="form-control" value="${formattedDate}" disabled>
                            </div>
                        </div>
      
                        <div class="row mb-2">
                            <div class="col-md-9">
                                <label class="form-label" for="remarks">Remarks</label>
                                <textarea type="text" id="remarks" required class="form-control"
                                    placeholder="Brake service, Throttle service, and Spark plugs service"></textarea>
                            </div>
                        </div>
      
                        <div style="text-align: end; margin-top: 30px;">
                            <button type="submit" class="btn btn-primary" id="finalDoneButton">Submit</button>
                        </div>
                    </div>
                </div>
            </div>
          </form>`;
      break;

    case 'notAccess':
      cardContent = `
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Work Order Status: Under Review</h5>
              <p class="card-text">The higher approver is authorized to approve this.</p>
            </div>
          </div>`;
      break;
    case 'reject':
      cardContent = `
        <div class="card">
          <div class="card-body">
            <form id="statusForm">
              <div class="mb-3">
                <label for="reasonforReject" class="form-label">Enter Reason for Reject</label>
                <textarea id="reasonforReject" class="form-control" maxlength="225" rows="3"
                          placeholder="Enter Reason for Reject" required></textarea>
              </div>
              <button type="submit" class="btn btn-primary">Submit</button>
            </form>
          </div>
        </div>`;
      break;
    case 'rejected':
      cardContent = `
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Work Order Status: Rejected</h5>
              <p class="card-text">The vendor has rejected this work order. Reason: ${vendorReason}. Please contact support for further details.</p>
            </div>
          </div>`;
      break;
    case 'rejected by dhl':
      cardContent = `
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Work Order Status: Rejected By DHL</h5>
              <p class="card-text">Reason: ${vendorReason}.</p>
            </div>
          </div>`;
      break;

    case 'Completed':
      cardContent = `
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Work Order Status: Completed</h5>
            <p class="card-text">The work order has been successfully completed.</p>
          </div>
        </div>`;
      break;
    default:
      cardContent = `
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Work Order Status: ${status}</h5>
            <p class="card-text">The work order is currently at the status of ${status}.</p>
          </div>
        </div>`;
  }

  modalBody.innerHTML = cardContent;

  const statusModal = new bootstrap.Modal(document.getElementById('statusModal'));
  statusModal.show();

  // Add event listener for form submission
  if (status == 'jobReject') {
    const form = document.getElementById('jobRejectedForm');
    form.onsubmit = function (event) {
      event.preventDefault();
      submitForm(status, id, index);
    };
  }
  else if (status == 'underReview') {
    const form = document.getElementById('underReviewForm');
    form.onsubmit = function (event) {
      event.preventDefault();
      submitForm(status, id, index);
    };
  }
  if (status == 'reject') {
    const form = document.getElementById('statusForm');
    form.onsubmit = function (event) {
      event.preventDefault();
      submitForm(status, id, index);
    };
  }
}


function submitForm(status, id, index) {
  let url;
  if (status == 'jobAccept' || status == 'jobReject') {
    url = 'workorder';
  } else {
    url = 'getListWorkorder';
  }
  const formData = new FormData();
  formData.append('id', id); // Add the ID to the form data

  if (status == 'jobAccept') {
    formData.append('type', 'updatestatus');
    formData.append('status', 'accept');
  } else if (status == 'jobReject') {
    formData.append('type', 'updatestatus');
    formData.append('status', 'reject');
    formData.append('reason', document.getElementById('jrReason').value);
  } else if (status == 'underReview') {
    formData.append('type', 'update');
    formData.append('status', 'under review');
    // Add the values of checkboxes
    const partsReceived = document.getElementById('partsReceived').checked;
    const workCompleted = document.getElementById('workCompleted').checked;
    formData.append('checkbox', JSON.stringify({ partsReceived, workCompleted }));

    // Add remarks
    const remarks = document.getElementById('remarks').value;
    formData.append('remarks', remarks);

    // Add completion date
    const completionDate = document.getElementById('completionDate').value;
    formData.append('completiondate', completionDate);
  } else if (status == 'reject') {
    formData.append('type', 'update');
    const reasonForReject = document.getElementById('reasonforReject').value;

    formData.append('status', 'rejected');
    formData.append('reason', reasonForReject);

  }

  const formDataObj = Object.fromEntries(formData.entries());
  console.log(JSON.stringify(formDataObj, null, 2));

  // generatePdf(dataObj[index], true)


  $.ajax({
    url: `controller/${url}.php`,
    type: 'POST',
    data: formData,
    processData: false,
    contentType: false,
    beforeSend: function () {
      handleLoader('flex');
    },
    success: function (response) {
      handleLoader('none');
      if (response.success) {
        showNotification('success', response.message);
        // if (status == 'under review') {
        // }
        const statusModal = bootstrap.Modal.getInstance(
          document.getElementById('statusModal')
        );
        statusModal?.hide();
        if (status == 'jobAccept' || status == 'jobReject') {
          getJobDataApproval();
        } else {
          generatePdf(response.data, true)
          getWorkOrderDataApproval();
        }
      } else {
        showNotification(
          'error',
          response.message || 'Failed to update status'
        );
      }
    },
    error: function (xhr, status, error) {
      handleLoader('none');
      showNotification('error', 'Error in updating status');
      console.error('Error in ajax request:', error);
    },
  });
}


