'use strict';
let rowDataMap = {};

function getDataForApproval() {
  const formData = new FormData();
  formData.append('vendor_id', vendorIID);
  formData.append('type', 'vendorList');

  $.ajax({
    url: `controller/getListWorkorder.php`,
    type: 'Post',
    processData: false,
    contentType: false,
    data: formData,

    beforeSend: function () {
      handleLoader('flex');
    },
    success: function (data) {
      handleLoader('flex');
      try {
        let row = '';
        let dataObj;
        if (data.success) {
          showNotification('success', 'Data found');
          dataObj = data.data;
          dataObj = dataObj.reverse();
          dataObj.map((el, index) => {
            // Create a unique identifier for each row
            const uniqueId = `row-${index}`;
            rowDataMap[uniqueId] = el; // Store data in the map

            // Handle status actions based on specific status
            let statusActions = '';
            switch (el.status) {
              case 'pending':
                statusActions = `
                  <a class="btn btn-action complete" onclick="markJobCompleted('Job Completion by vendor', '${el.id}','${el.jobTitle}')">
                    <span class="badge badge-pill badge-soft-info font-size-12">Mark Job Completed</span>
                  </a>
                `;
                break;
              case 'workorder Completion by dhl':
                statusActions = `
                <a class="btn btn-action complete" onclick="openStatusModal('inprogress', '${el.id}','${el.jobTitle}','${el.V_id}')">
                  <span class="badge badge-pill badge-soft-info font-size-12">Submit Invoice</span>
                </a>
              `;
                break;
              case 'Job Completion by vendor':
                statusActions = `
                <a class="btn btn-action complete" onclick="openStatusModal('awaitingForDhl', '${el.id}','${el.jobTitle}','${el.V_id}')">
                  <span class="badge badge-pill badge-soft-warning font-size-12">Awaiting For DHL</span>
                </a>
              `;
                break;
              case 'in progress':
                statusActions = `
                <a class="btn btn-action complete" onclick="openStatusModal('awaitingForDhl', '${el.id}','${el.jobTitle}','${el.V_id}')">
                  <span class="badge badge-pill badge-soft-warning font-size-12">Awaiting For DHL</span>
                </a>
              `;
                break;
              case 'under review':
                statusActions = `<span class="badge badge-pill badge-soft-info font-size-12">Under Review</span>`;
                break;

              case 'Completed':
                statusActions = `<a role="button" title="View Status" onclick="openStatusModal('Completed','${el.id}','${el.jobTitle}')">
                      <span class="badge badge-pill badge-soft-success font-size-12">Completed</span>
                    </a>`;
                break;
              case 'rejected':
                statusActions = `<span class="badge badge-pill badge-soft-danger font-size-12">Rejected</span>`;
                break;
              case 'rejected by dhl':
                statusActions = `<a role="button" title="View Status" 
                onclick="openStatusModal('rejected by dhl','${el.id}','${el.jobTitle}','${el.V_id}','${el.reason}')">
                      <span class="badge badge-pill badge-soft-danger font-size-12">Rejected by DHL</span>
                    </a>`;
                break;
              default:
                statusActions = `<span class="badge badge-pill badge-soft-secondary font-size-12">${capitalizeFirstLetter(
                  el.status
                )}</span>`;
            }

            // Construct the row HTML
            row += `
              <tr id="${uniqueId}">
                <td>${index + 1}</td> <!-- increment index -->
                <td style="width: 100px">${statusActions}</td>
                <td>${el.creationdate || '-'}</td>
                <td>${el.jobTitle || '-'}</td>
                <td>${el.service || '-'}</td>
                <td>
                  <!-- Add a unique data-row-id for each work order -->
                  <a href="#" class="view-workorder" data-row-id="${uniqueId}">
                    View Workorder
                  </a>
                </td>
              </tr>
            `;
          });

          // Initialize the DataTable
          initializeDataTable(`workOrderDetailTable`, row);

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


function getJobDataForApproval() {
  const formData = new FormData();
  formData.append('vendor_id', vendorIID);
  formData.append('type', 'getJobList');

  $.ajax({
    url: `controller/getlistworkorder.php`,
    type: 'Post',
    processData: false,
    contentType: false,
    data: formData,

    beforeSend: function () {
      handleLoader('flex');
    },
    success: function (data) {
      handleLoader('flex');
      try {
        let row = '';
        let dataObj;
        if (data.success) {

          console.log('data');
          console.log(data);
          dataObj = data.data;
          dataObj = dataObj.reverse();
          dataObj.map((el, index) => {
            // Create a unique identifier for each row
            const uniqueId = `row-${index}`;
            rowDataMap[uniqueId] = el; // Store data in the map

            // Handle status actions based on specific status
            let statusActions = '';
            if (el.status?.toLowerCase() == 'pending') {
              statusActions = `
                <a class="btn btn-action complete" onclick="openStatusModal('submitquotation', '${el.id}','${el.jobTitle}','${el.V_id}')">
                  <span class="badge badge-pill badge-soft-info font-size-12">Submit Quotation</span>
                </a>
              `;
            } else if (el.status?.toLowerCase() == 'submit quotation') {
              statusActions = `
                <a class="btn btn-action complete" onclick="openStatusModal('awaiting', '${el.id}','${el.jobTitle}','${el.V_id}')">
                  <span class="badge badge-pill badge-soft-warning font-size-12">Awaiting For DHL</span>
                </a>
              `;
            }
            else if (el.status?.toLowerCase() == 'accept') {
              statusActions = `
                <a class="btn btn-action complete" onclick="openStatusModal('jobAcceptByDHL', '${el.id}','${el.jobTitle}','${el.V_id}')">
                  <span class="badge badge-pill badge-soft-success font-size-12">Accepted By DHL</span>
                </a>
              `;
            } else if (el.status?.toLowerCase() == 'reject') {
              statusActions = `
                <a class="btn btn-action complete" onclick="openStatusModal('jobRejectByDHL', '${el.id}','${el.jobTitle}','${el.V_id}','${el.reason}')">
                  <span class="badge badge-pill badge-soft-danger font-size-12">Reject By DHL</span>
                </a>
              `;
            }

            // Construct the row HTML
            row += `
              <tr id="${uniqueId}">
                <td>${index + 1}</td> <!-- increment index -->
                <td style="width: 100px">${statusActions}</td>
                <td>${el.creationdate || '-'}</td>
                <td>${el.jobTitle || '-'}</td>
                <td>${el.Reg || '-'}</td>
                <td>${el.description || '-'}</td>
                <td>${el.reason || '-'}</td>
                <td>${el.quotation || '-'}</td>
                <td>${el.quotationDescription || '-'}</td>
              </tr>
            `;
          });

          // Initialize the DataTable
          initializeDataTable(`jobDetailTable`, row);

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
            getDataForApproval()
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

getJobDataForApproval()
// getDataForApproval();

// Event delegation: Add event listener to the <tbody> element
$(document).on('click', '#workOrderDetailTable tbody .view-workorder', function (event) {
  event.preventDefault();
  const uniqueId = $(this).data('row-id'); // Get the unique row ID
  const rowData = rowDataMap[uniqueId]; // Retrieve the row data from the map
  generatePdf(rowData); // Pass the row data to generate PDF
});




function openStatusModal(status, id, heading, vendorId, reason = '') {
  const modalBody = document.getElementById('modalBodyContent');
  modalBody.innerHTML = '';
  let cardContent = '';
  const statusModalLabel = document.getElementById('statusModalLabel');

  if (status === 'submitquotation') {
    statusModalLabel.innerHTML = `Submit Quotation For - ${heading}`;
    cardContent = `
      <div class="card">
        <div class="card-body">
          <form id="statusForm">
            <div class="mb-3">
              <label for="quotation" class="form-label">Enter Amount in (Pkr)</label>
              <input type="Number" class="form-control" id="quotation" placeholder="Enter Amount in (Pkr)" required>
            </div>
            <div class="mb-3">
              <label for="quotationDescription" class="form-label">Description</label>
              <textarea id="quotationDescription" class="form-control" maxlength="225" rows="3"
                placeholder="Enter Quotation Description" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
          </form>
        </div>
      </div>`;
  } if (status === 'awaiting') {
    statusModalLabel.innerHTML = `Awaiting For DHL - ${heading}`;
    cardContent = `
     <div class="card">
            <div class="card-body">
              <h5 class="card-title">Awaiting For DHL</h5>
              <p class="card-text">${heading} is currently under review by DHL. Please wait for their response.</p>
            </div>
          </div>`;
  } else if (status === 'jobAcceptByDHL') {
    statusModalLabel.innerHTML = `Accepted By DHL - ${heading}`;
    cardContent = `
     <div class="card">
            <div class="card-body">
              <h5 class="card-title">Job Accepted By DHL</h5>
              <p class="card-text">${heading} has been accepted by DHL. Please proceed to the next steps.</p>
            </div>
          </div>`;
  } else if (status === 'jobRejectByDHL') {
    statusModalLabel.innerHTML = `Rejected By DHL - ${heading}`;
    cardContent = `
     <div class="card">
            <div class="card-body">
              <h5 class="card-title">Job Rejected By DHL</h5>
              <p class="card-text">${heading} has been rejected by DHL. <strong>Reason: ${reason}.</strong></p>
            </div>
          </div>`;
  } else if (status === 'awaitingForDhl') {
    statusModalLabel.innerHTML = `Awaiting For DHL - ${heading}`;
    cardContent = `
     <div class="card">
            <div class="card-body">
              <h5 class="card-title">Awaiting For DHL</h5>
              <p class="card-text">${heading} is currently under review by DHL. Please wait for their response.</p>
            </div>
          </div>`;
  }

  else if (status === 'inprogress') {
    statusModalLabel.innerHTML = `Accept ${heading} `;
    cardContent = `
      <div class="card">
        <div class="card-body pb-0">
          <form id="statusForm">
            <div class="mb-3">
              <label for="sltFacility" class="form-label">Select Facility</label>
              <select id="sltFacility" class="form-select" required>
                <option value="SVC">SVC</option>
                <option value="GTW">GTW</option>
              </select>
            </div>
            <div class="mb-3">
              <label for="invoice" class="form-label">Upload Invoice</label>
              <input class="form-control" type="file" accept="application/pdf" id="invoice" accept="application/pdf, image/jpeg, image/png, image/gif, image/bmp, image/svg+xml" required>
            </div>
            <div class="mb-3">
              <label for="invoiceAmount" class="form-label">Enter Amount in (Pkr)</label>
              <input type="Number" class="form-control" id="invoiceAmount" placeholder="Enter Amount in (Pkr)" required>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
          </form>
        </div>
      </div > `;
  }
  else if (status === 'Completed') {
    statusModalLabel.innerHTML = `Completed - ${heading} `;

    cardContent = `
      <div class="card">
          <div class="card-body">
            <h5 class="card-title">Work Order Status: Completed</h5>
            <p class="card-text">The work order has been successfully completed.</p>
          </div>
      </div > `;

  }
  else if (status === 'rejected') {
    statusModalLabel.innerHTML = `Reject ${heading} `;
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
      </div > `;
  } else if (status === 'rejected by dhl') {
    statusModalLabel.innerHTML = `Reject ${heading} `;
    cardContent = `
      < div class="card" >
        <div class="card-body">
          <h5 class="card-title">Work Order Status: Rejected By DHL</h5>
          <p class="card-text">Reason: ${reason}.</p>
          <h6 class='text-danger mt-3'>Want to Resubmit the Invoice and Amount</h6>
          <form id="statusForm" class='mt-3'>
            <div class="mb-3">
              <label for="invoice" class="form-label">Upload Invoice</label>
              <input class="form-control" type="file" id="invoice" accept="application/pdf" required>
            </div>
            <div class="mb-3">
              <label for="invoiceAmount" class="form-label">Enter Amount in (Pkr)</label>
              <input type="Number" class="form-control" id="invoiceAmount" placeholder="Enter Amount in (Pkr)" required>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
          </form>
        </div>
          </div > `;
  }

  modalBody.innerHTML = cardContent;

  // Show modal
  var statusModal = new bootstrap.Modal(document.getElementById('statusModal'));
  statusModal.show();

  // Add event listener for form submission
  const form = document.getElementById('statusForm');
  if (form) {
    form.onsubmit = function (event) {
      event.preventDefault();
      submitForm(status, id, vendorId);
    };
  }
}

function submitForm(status, id, vendorId,) {
  const formData = new FormData();
  formData.append('id', id); // Add the ID to the form data
  if (status == 'submitquotation') {
    formData.append('type', 'quotation');
    formData.append('quotation', document.getElementById('quotation').value);
    formData.append('quotationDescription', document.getElementById('quotationDescription').value);
  } else {
    formData.append('type', 'update');
    formData.append('vendor_id', vendorId);
    if (status === 'inprogress' || status === 'rejected by dhl') {
      const invoiceFile = document.getElementById('invoice').files[0];
      const invoiceAmount = document.getElementById('invoiceAmount').value;
      const sltFacility = document.getElementById('sltFacility').value;
      formData.append('status', 'in progress');
      formData.append('facility', sltFacility);
      formData.append('contentFile', invoiceFile);
      formData.append('amount', invoiceAmount);
    } else if (status === 'rejected') {
      const reasonForReject = document.getElementById('reasonforReject').value;

      formData.append('status', 'rejected');
      formData.append('reason', reasonForReject);
    }
  }

  const formDataObj = Object.fromEntries(formData.entries());
  console.log(JSON.stringify(formDataObj, null, 2));
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
        var statusModal = bootstrap.Modal.getInstance(
          document.getElementById('statusModal')
        );
        statusModal?.hide();
        if (status == 'under review') {
          var statusModal = bootstrap.Modal.getInstance(
            document.getElementById('statusModal')
          );
          statusModal.hide();
        }
        if (status == 'submitquotation') {
          var statusModal = bootstrap.Modal.getInstance(
            document.getElementById('statusModal')
          );
          statusModal.hide();
          getJobDataForApproval()
        } else {
          getDataForApproval();
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

