let dataObj;
let expenseData;
let taxData;
let rowDataMap = {};

function getRfqData() {
  let data = {
    type: 'historyDhl',
  }

  $.ajax({
    url: `controller/rfq.php`,
    type: 'Post',
    contentType: "application/json",
    data: JSON.stringify(data),
    beforeSend: function () {
      handleLoader('flex');
    },
    success: function (data) {
      handleLoader('flex');
      console.log(data);
      try {
        let row = '';
        if (data.success) {
          // showNotification('success', 'Data found');
          dataObj = data.data;
          dataObj = dataObj.reverse();
          data.data.map((el, index) => {
            // Create a unique identifier for each row
            const uniqueId = `row-${index}`;
            rowDataMap[el.job_id] = el;// Store data in the map

            let amount = el.amount || '0';

            // Handle status actions for different statuses
            let statusActions = '';
            switch (el.Status) {
              case 'Pending':
                statusActions = `<a role="button" title="Pending"  onclick="openQuotationModal('Pending','${el.job_id}', '${el.jobTitle}')">
                    <span class="badge badge-pill badge-soft-warning font-size-12">Pending</span>
                  </a>`;
                break;
              case 'submit quotation':
                statusActions = `<a role="button" title="Quotation Received" onclick="openQuotationModal('submit quotation','${el.job_id}', '${el.jobTitle}')">
                    <span class="badge badge-pill badge-soft-info font-size-12">Quotation Received</span>
                  </a>`;
                break;
              case 'approve':
                statusActions = `                
                    ${designation == 'Manager' ?
                    `<a class="btn btn-action complete" 
                          onclick="openQuotationModal('Approve', '${el.job_id}', '${el.jobTitle.replace(/'/g, "\\'")}')"
                          data-bs-toggle="tooltip"  
                          data-bs-placement="top" 
                          title="Quotation Received">
                          <span class="badge badge-pill badge-soft-info font-size-12">Quotation Received</span>
                      </a>` :
                    `<a class="btn btn-action complete" 
                          onclick="openQuotationModal('waiting', '${el.job_id}', '${el.jobTitle.replace(/'/g, "\\'")}')"
                          data-bs-toggle="tooltip" 
                          data-bs-placement="top" 
                          title="Waiting for Upper Authority">
                          <span class="badge badge-pill badge-soft-warning font-size-12">Forwarded for Further Approval</span>
                      </a>`}
                `;
                break;
              case 'isApproved':
                statusActions = `                
                    ${designation == 'Director GOPS' ?
                    `<a class="btn btn-action complete" 
                          onclick="openQuotationModal('isApproved', '${el.job_id}', '${el.jobTitle.replace(/'/g, "\\'")}')"
                          data-bs-toggle="tooltip"  
                          data-bs-placement="top" 
                          title="Quotation Received">
                          <span class="badge badge-pill badge-soft-info font-size-12">Quotation Received</span>
                      </a>` :
                    `<a class="btn btn-action complete" 
                          onclick="openQuotationModal('waiting', '${el.job_id}', '${el.jobTitle.replace(/'/g, "\\'")}')"
                          data-bs-toggle="tooltip" 
                          data-bs-placement="top" 
                          title="Waiting for Upper Authority">
                          <span class="badge badge-pill badge-soft-warning font-size-12">Forwarded for Further Approval</span>
                      </a>`}
                `;
                break;
              case 'isApproved':
                statusActions = `                
                    ${designation == 'Director GOPS' ?
                    `<a class="btn btn-action complete" 
                          onclick="openQuotationModal('isApproved', '${el.job_id}', '${el.jobTitle.replace(/'/g, "\\'")}')"
                          data-bs-toggle="tooltip"  
                          data-bs-placement="top" 
                          title="Quotation Received">
                          <span class="badge badge-pill badge-soft-info font-size-12">Quotation Received</span>
                      </a>` :
                    `<a class="btn btn-action complete" 
                          onclick="openQuotationModal('waiting', '${el.job_id}', '${el.jobTitle.replace(/'/g, "\\'")}')"
                          data-bs-toggle="tooltip" 
                          data-bs-placement="top" 
                          title="Waiting for Upper Authority">
                          <span class="badge badge-pill badge-soft-warning font-size-12">Forwarded for Further Approval</span>
                      </a>`}
                `;
                break;
              case 'isApprovedByGops':
                statusActions = `                
                    ${designation == 'VP GOPS' ?
                    `<a class="btn btn-action complete" 
                          onclick="openQuotationModal('isApprovedByGops', '${el.job_id}', '${el.jobTitle.replace(/'/g, "\\'")}')"
                          data-bs-toggle="tooltip"  
                          data-bs-placement="top" 
                          title="Quotation Received">
                          <span class="badge badge-pill badge-soft-info font-size-12">Quotation Received</span>
                      </a>` :
                    `<a class="btn btn-action complete" 
                          onclick="openQuotationModal('waiting', '${el.job_id}', '${el.jobTitle.replace(/'/g, "\\'")}')"
                          data-bs-toggle="tooltip" 
                          data-bs-placement="top" 
                          title="Waiting for Upper Authority">
                          <span class="badge badge-pill badge-soft-warning font-size-12">Forwarded for Further Approval</span>
                      </a>`}
                `;
                break;
              case 'deactivate':
                statusActions = `
                  <a class="btn btn-action complete" data-bs-toggle="tooltip" data-bs-placement="top" title="Reason: ${el.Reason || '-'}">
                    <span class="badge badge-pill badge-soft-secondary font-size-12 text-bg-danger">Job Declined</span>
                  </a>
                `;
                break;
              case 'approve by manager':
                statusActions = `<a role="button" title="Job in progress" onclick="openQuotationModal('approve by manager','${el.job_id}', '${el.jobTitle}')">
                    <span class="badge badge-pill badge-soft-warning font-size-12">Job in progress</span>
                  </a>`;
                break;
              case 'isApproved by manager':
                statusActions = `<a role="button" title="Job in progress" onclick="openQuotationModal('approve by manager','${el.job_id}', '${el.jobTitle}')">
                    <span class="badge badge-pill badge-soft-warning font-size-12">Job in progress</span>
                  </a>`;
                break;
              case 'job satisfaction requested':
                statusActions = `<a role="button" title="Job satisfaction requested" onclick="openJobSatisfactionModal('job satisfaction requested', '${el.job_id}', '${el.jobTitle.replace(/'/g, "\\'")}')">
                    <span class="badge badge-pill badge-soft-info font-size-12">Job Satisfaction Requested</span>
                  </a>`;
                break;
              case 'job satisfaction approved':
                statusActions = `<a role="button" title="Invoice Pending" onclick="openQuotationModal('job satisfaction approved','${el.job_id}', '${el.jobTitle}')">
                    <span class="badge badge-pill badge-soft-warning font-size-12">Invoice Pending</span>
                  </a>`;
                break;
              case 'invoice recieved':
                statusActions = `<a role="button" title="Invoice Received" onclick="openQuotationModal('invoice recieved','${el.job_id}', '${el.jobTitle}')">
                    <span class="badge badge-pill badge-soft-info font-size-12">Invoice Received</span>
                  </a>`;
                break;
              case 'Invoice reject':
                statusActions = `<a role="button" title="Invoice Rejected"  onclick="openQuotationModal('Invoice Rejected','${el.job_id}', '${el.jobTitle}')">
                    <span class="badge badge-pill badge-soft-warning font-size-12">Invoice Rejected</span>
                  </a>`;
                break;
              case 'Invoice approve':
                statusActions = `<a role="button" title="Record Expense & Forward" onclick="openRecordExpense('${el.job_id}')">
                    <span class="badge badge-pill badge-soft-info font-size-12">Record Expense & Forward</span>
                  </a>`;
                break;
              case 'Completed':
                statusActions = `<a role="button" title="Job Completed">
                    <span class="badge badge-pill badge-soft-success font-size-12">Job Completed</span>
                  </a>`;
                break;
              case 'return':
                statusActions = `<a role="button" title="Returned">
                  <span class="badge badge-pill badge-soft-warning font-size-12">Returned</span>
                </a>`;
                break;
              case 'return by manager':
                statusActions = `<a role="button" title="Returned">
                  <span class="badge badge-pill badge-soft-warning font-size-12">Returned</span>
                </a>`;
                break;
              default:
                statusActions = `<a role="button" title="View Status">
                <span class="badge badge-pill badge-soft-secondary font-size-12">${capitalizeFirstLetter(el.Status)}</span>
              </a>`;
            }
            let workorderStatus = "";
            let status = el.Status.trim().toLowerCase(); // Normalize status (trim spaces, convert to lowercase)

            if (["pending", "submit quotation", "approve", 'isapproved', 'isapprovedbygops', "deactivate", "return",].includes(status)) {
              workorderStatus = "-";
            } else {
              workorderStatus = `<a href="#" class="view-workorder" data-row-id="${el.job_id}">View Workorder</a>`;
            }
            // Construct the row HTML
            row += `
              <tr id="${uniqueId}">
                <td><button class="btn btn-outline-primary me-1 px-1 py-1" ${'chain' in el ? "" : "disabled"}  onclick="openChainModal('${el.job_id}', '${el.jobTitle.replace(/'/g, "\\'")}')"><i class="fa-solid fa-link"></i></button> ${index + 1}</td>
                <td style="width: 100px">${statusActions}</td>
                <td>${el.creationdate || '-'}</td>
                <td>${el.Reg || '-'}</td>
                <td>${el.jobTitle || '-'}</td> 
                <td>${el.job_description || '-'}</td> 
                <td>${el.grandTotal || '-'}</td> 
                <td>${el.stationname || '-'}</td> 
                <td>${el.Reason || '-'}</td>
                <td>${capitalizeFirstLetter(el.createdby) || '-'}</td> 
                <td>
                 ${workorderStatus}
                </td>
                <td>
                  ${el.invoice
                ? `<a class='text-success' href="invoice/${el.invoice}" target="_blank">View Invoice</a>`
                : `-`}
                </td>
              </tr>
            `;
          });

          // Initialize DataTable
          initializeDataTable('rfqDetailTable', row);
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
$(document).on('click', '#rfqDetailTable tbody .view-workorder', function (event) {
  event.preventDefault();
  const uniqueId = $(this).data('row-id'); // Get the unique row ID
  const rowData = rowDataMap[uniqueId]; // Retrieve the row data from the map
  console.log(rowData);
  console.log(rowData['status'])
  // if (rowData['status']) {
  // }
  generatePdf(rowData); // Pass the row data to generate PDF
});

function getExpenseCategory() {
  $.ajax({
    url: `controller/getExpenseCategory.php`,
    type: "POST",
    success: function (res) {
      console.log(res);
      if (res.success && res.data) {
        expenseData = res.data;
      }
    },
    error: function (xhr, status, error) {
      console.error("Error:", error);
    },
  });
}

function getTaxData() {
  $.ajax({
    url: `controller/getSalesTax.php`,
    type: "POST",
    success: function (res) {
      console.log(res);
      if (res.success && res.data) {
        taxData = res.data.sort((a, b) => parseFloat(a.value) - parseFloat(b.value));
      }
    },
    error: function (xhr, status, error) {
      console.error("Error:", error);
    },
  });
}
function calculateAmountWithoutTax(totalWithTax, salesTaxValue) {
  const amountWithoutTax = (Number(totalWithTax) / (1 + Number(salesTaxValue) / 100)).toFixed(2);
  return amountWithoutTax;
}
getExpenseCategory();
getTaxData();
getRfqData();

document.addEventListener("DOMContentLoaded", function () {

  document.addEventListener("click", function (event) {
    if (event.target.closest(".add-row")) {
      let alertRowCount = document.querySelectorAll(".alert-row").length;
      if (alertRowCount >= 8) {
        showNotification("error", "You can only add up to 8 alerts.");
        return;
      }

      const newRow = document.createElement("div");
      newRow.classList.add("alert-row", "row", "mb-2");
      newRow.innerHTML = `
                <div class="col-md-3">
                    <select class="form-select me-2 alert-type category-select" required>
                        <option value="">Select Category</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="number" step="0.01" min="0" class="form-control" placeholder="Enter Amount">
                </div>
                <div class="col-md-2">
                    <div class="input-group">
                        <select class="form-select tax-select">
                        </select>
                        <button type="button" class="btn btn-primary docs-datepicker-trigger">%</button>
                    </div>
                </div>
                <div class="col-md-4">
                    <input type="text" class="form-control" placeholder="Enter Description" required>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-primary remove-row w-100"><i class="fas fa-minus"></i></button>
                </div>
            `;

      const selectBox = newRow.querySelector(".category-select");
      expenseData.forEach(item => {
        const option = document.createElement("option");
        option.value = item.ExpId;
        option.textContent = item.Expense;
        selectBox.appendChild(option);
      });
      const selectBox1 = newRow.querySelector(".tax-select");
      taxData.forEach(item => {
        const option = document.createElement("option");
        option.value = item.id;
        option.textContent = item.value;
        selectBox1.appendChild(option);
      });

      document.getElementById("expenseRows").appendChild(newRow);
      updateCategoryOptions();
    }

    if (event.target.closest(".remove-row")) {
      event.target.closest(".alert-row").remove();
      updateCategoryOptions();
    }
  });

  document.addEventListener("change", function (event) {
    if (event.target.classList.contains("category-select")) {
      updateCategoryOptions();
    }
  });

  function updateCategoryOptions() {
    const selectedCategories = Array.from(document.querySelectorAll(".category-select"))
      .map(select => select.value)
      .filter(value => value);

    document.querySelectorAll(".category-select").forEach(select => {
      const currentSelection = select.value;
      select.innerHTML = '<option value="">Select Category</option>';

      expenseData.forEach(item => {
        if (!selectedCategories.includes(item.ExpId) || item.ExpId === currentSelection) {
          const option = document.createElement("option");
          option.value = item.ExpId;
          option.textContent = item.Expense;
          if (item.ExpId === currentSelection) {
            option.selected = true;
          }
          select.appendChild(option);
        }
      });
    });
  }
});





function openQuotationModal(status, id, heading) {
  const modalBody = document.getElementById('modalBodyContent');
  modalBody.innerHTML = '';
  let cardContent = '';
  const statusModalLabel = document.getElementById('statusModalLabel');
  statusModalLabel.innerHTML = `${heading}`;


  cardContent = `
      <div class="my-4">
        <div class="row mb-2 g-3">
            <div class="col-md-3">
                <label class="form-label" for="exJobTitle">Job Title</label>
                <input type="text" id="exJobTitle" class="form-control"
                    placeholder="Enter Job Title" value="${rowDataMap[id]['jobTitle']}" readonly>
            </div>  
            <div class="col-md-3">
                <label class="form-label" for="exDesc">Description</label>
                <input type="text" id="exDesc" class="form-control" readonly
                placeholder="Enter Description" value="${rowDataMap[id]['job_description']}" maxlength="512">
            </div>
            <div class="col-md-3">
                <label class="form-label" for="exRegNo">Reg. No.</label>
                <input type="text" id="exRegNo" class="form-control"
                    placeholder="Enter Reg. No." value="${rowDataMap[id]['Reg']}" readonly>
                <input type="hidden" id="exVid">
            </div>
            <div class="col-md-3">
                <label class="form-label" for="exEngine">Engine</label>
                <input type="text" id="exEngine" class="form-control"
                    placeholder="Enter Engine" value="${rowDataMap[id]['ENGINE']}" readonly>
            </div>
            <div class="col-md-3">
                <label class="form-label" for="exChasis">Chasis</label>
                <input type="text" id="exChasis" class="form-control"
                    placeholder="Enter Chasis" value="${rowDataMap[id]['chassis']}" readonly>
            </div>
            <div class="col-md-3">
                <label class="form-label" for="exBrand">Brand/ Make</label>
                <input type="text" id="exBrand" class="form-control"
                    placeholder="Enter Brand" value="${rowDataMap[id]['makename']}" readonly>
            </div>
            <div class="col-md-3">
                <label class="form-label" for="exModel">Model</label>
                <input type="text" id="exModel" class="form-control"
                    placeholder="Enter Model" value="${rowDataMap[id]['modelname']}" readonly>
            </div>
            <div class="col-md-3">
                <label class="form-label" for="exStation">Station</label>
                <input type="text" id="exStation" class="form-control"
                    placeholder="Enter Station" value="${rowDataMap[id]['stationname']}" readonly>
            </div>
        </div>

        <div class="d-flex mt-4 align-items-center justify-content-between">
          <h5 class="">Services Required</h5> 
        </div>
        <div id="expenseRows" class="mt-4">
          ${rowDataMap[id]['expenses'].map((el, i) => {
    const amountWithoutTax = calculateAmountWithoutTax(el['amount'], el['salesTaxValue']); // ✅ Move calculation here
    return `
                <div class="alert-row row mb-2">
                    <div class="col-md-2">
                        ${i === 0 ? '<label class="form-label">Expense Category</label>' : ''}
                        <select class="form-select me-2 alert-type category-select" required id="expenseCategory-${i}" disabled>
                          <option value="">Select Category</option>
                          ${expenseData.map(item => `
                              <option value="${item.ExpId}" ${item.ExpId == el['CategoryId'] ? 'selected' : ''}>
                                  ${item.Expense}
                              </option>
                          `).join('')}
                      </select>
                    </div>
                    <div class="col-md-2">
                      ${i === 0 ? '<label class="form-label">Amount</label>' : ''}
                      <input type="number" step="0.01" min="0" class="form-control" value="${amountWithoutTax}" placeholder="Enter Amount" disabled>
                      <input type="hidden" class="job-id" value="${rowDataMap[id]['job_id']}">
                      <input type="hidden" class="ExpenseDetailId" value="${el['ExpenseDetailId']}">
                    </div>
                    <div class="col-md-2">
                      ${i === 0 ? '<label class="form-label">Tax</label>' : ''}
                      <div class="input-group">
                          <select class="form-select alert-type tax-select" id="expenseTax-${i}" disabled>
                              <option value="">Select Tax</option>
                              ${taxData.map(item => `
                                  <option value="${item.id}" ${item.id == el['salesTax'] ? 'selected' : ''}>
                                      ${item.value} 
                                  </option>
                              `).join('')}
                          </select>
                          <button type="button" class="btn btn-primary docs-datepicker-trigger">%</button>
                      </div>
                    </div>
                    <div class="col-md-2">
                      ${i === 0 ? '<label class="form-label">Total Amount</label>' : ''}
                      <input type="number" step="0.01" min="0" class="form-control" value="${el['amount']}" placeholder="Total Amount" disabled>
                    </div>
                    <div class="col-md-4">
                      ${i === 0 ? '<label class="form-label">Description</label>' : ''}
                        <input type="text" class="form-control" placeholder="Enter Description" 
                          value="${el['description']}" required  maxlength="512" disabled
                          oninput="if(this.value.indexOf('${el['description']}') !== 0) this.value = '${el['description']}';">
                    </div>
                </div>
              `;
  }).join('')}
        </div>
        <div class="row">
          <div class="col-md-12 text-end mt-4">
              <strong>Grand Total:</strong> Rs. ${(parseFloat(rowDataMap[id]?.grandTotal) || 0).toFixed(2)}/-
          </div>
        </div>



        
        <div class="row">
        
          <div class="col-md-12 text-end mt-4">
            ${designation === "Manager" && status === "Approve" ? `
              <button class="btn btn-success" id="approveButton" type="button">Approve</button>
              <button class="btn btn-primary" id="returnButton" type="button">Return</button>  
              <button class="btn btn-secondary" id="deactivateButton" type="button">Decline</button>
            ` : ''}
            
            ${status === "submit quotation" && designation === "Manager Fleet" ? `
              <button class="btn btn-success" id="approveButton" type="button">Approve</button>
             <button class="btn btn-primary" id="returnButton" type="button">Return</button>  
              <button class="btn btn-secondary" id="deactivateButton" type="button">Decline</button>
            ` : ''}
            
            ${status === "invoice recieved" && userName === rowDataMap[id]['createdby'] ? `
              <button class="btn btn-success" id="invoiceapproveButton" type="button">Approve</button>
              <button class="btn btn-primary" id="invoiceRejectButton" type="button">Reject</button>
            ` : ''}
            
            ${status === "isApproved" && designation == 'Director GOPS' ? `
              <button class="btn btn-success" id="approveButton" type="button">Approve</button>
              <button class="btn btn-primary" id="returnButton" type="button">Return</button>  
              <button class="btn btn-secondary" id="deactivateButton" type="button">Decline</button>
            ` : ''}

            ${status === "isApprovedByGops" && designation == 'VP GOPS' ? `
              <button class="btn btn-success" id="approveButton" type="button">Approve</button>
              <button class="btn btn-primary" id="returnButton" type="button">Return</button>  
              <button class="btn btn-secondary" id="deactivateButton" type="button">Decline</button>
            ` : ''}

            </div>
            </div>
            </div>`;
  modalBody.innerHTML = cardContent;

  // Show modal
  var statusModal = new bootstrap.Modal(document.getElementById('statusModal'));
  statusModal.show();



  document.getElementById("invoiceapproveButton")?.addEventListener("click", function () {
    handleQuotationRequest('approve', 'invoiceApprove', id);
  });
  document.getElementById("approveButton")?.addEventListener("click", function () {
    handleQuotationRequest(status, status, id);
  });

  function openReasonModal(actionType) {
    document.getElementById('reasonModalLabel').innerText = `${actionType} Reason`;
    document.getElementById('reasonSubmitButton').setAttribute("data-action", actionType);
    document.getElementById('reasonSubmitButton').setAttribute("data-prevactionType", status);
    document.getElementById('reasonSubmitButton').setAttribute("data-id", id); // Store ID in button
    var reasonModal = new bootstrap.Modal(document.getElementById('reasonModal'));
    reasonModal.show();
  }

  document.getElementById("returnButton")?.addEventListener("click", function () {
    openReasonModal("Return");
  });

  document.getElementById("deactivateButton")?.addEventListener("click", function () {
    openReasonModal("Deactivate");
  });
  document.getElementById("invoiceRejectButton")?.addEventListener("click", function () {
    openReasonModal("reject");
  });

  // Remove previous event listeners before adding a new one
  const reasonSubmitButton = document.getElementById("reasonSubmitButton");
  reasonSubmitButton?.removeEventListener("click", handleReasonSubmit);
  reasonSubmitButton?.addEventListener("click", handleReasonSubmit);
}

function handleReasonSubmit(e) {
  e.preventDefault();
  const reason = document.getElementById("reasonInput").value.trim();
  const actionType = document.getElementById('reasonSubmitButton').getAttribute("data-action");
  const prevactionType = document.getElementById('reasonSubmitButton').getAttribute("data-prevactionType");
  const id = document.getElementById('reasonSubmitButton').getAttribute("data-id"); // Retrieve ID
  if (reason === "") {
    showNotification('error', "Reason is required.");
    return;
  }

  handleQuotationRequest(actionType, prevactionType, id, reason);
  bootstrap.Modal.getInstance(document.getElementById('reasonModal')).hide();
}

function openChainModal(id, heading) {
  const modalBody = document.getElementById('chainStatusmodalBodyContent');
  modalBody.innerHTML = '';  // Clear previous content

  const statusModalLabel = document.getElementById('chainStatusModalLabel');
  statusModalLabel.innerHTML = `Action History Of: ${heading}`;

  let chainData = rowDataMap[id]?.chain || [];
  chainData = chainData.reverse();
  let row = "";
  row = chainData.length > 0
    ? chainData.map((el, i) => `
          <tr>
              <td>${i + 1}</td>
              <td>${capitalizeFirstLetter(el.ModifiedStatus)}</td>
              <td>${capitalizeFirstLetter(el.ModifiedBy)}</td>
              <td>${el.ModifiedDate}</td>
          </tr>
      `).join('')
    : `<tr><td colspan="4" class="text-center">No history available.</td></tr>`;

  let cardContent = `
      <div class="my-2">
          <table class="table" style="font-size: 12px;" id="chainTable">
              <thead>
                  <tr>
                      <th style="max-width:30px;">S.no</th>
                      <th>Modified Status</th>
                      <th>Modified By</th>
                      <th>Modified Date</th>
                  </tr>
              </thead>
              <tbody>${row}</tbody>
          </table>
      </div>`;

  modalBody.innerHTML = cardContent;

  if ($.fn.DataTable.isDataTable(`#chainTable`)) {
    $(`#chainTable`).DataTable().clear().destroy();
  }
  $(`#chainTable`).DataTable();

  // Show modal
  var statusModal = new bootstrap.Modal(document.getElementById('chainStatusModal'));
  statusModal.show();
}



function openJobSatisfactionModal(status, id, heading) {
  const currentDate = new Date().toLocaleDateString('en-GB');
  const [day, month, year] = currentDate.split('/');
  const formattedDate = `${year}/${month}/${day}`;
  const modalBody = document.getElementById('modalBodyContent');
  modalBody.innerHTML = '';
  let cardContent = '';
  const statusModalLabel = document.getElementById('statusModalLabel');
  statusModalLabel.innerHTML = `Job satisfaction requested For: ${heading}`;
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
                                    Above mentioned parts have been received.
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="workCompleted" checked='true'>
                                <label class="form-check-label" for="workCompleted">
                                    Above mentioned work has been completed.
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
                    <div class="row mb-2 mt-3">
                        <div class="col-md-9">
                            <label class="form-label" for="remarks">Job Rating</label>
                            <div class="rating-container" id="ratingContainer"></div>
                            <span class='d-none' id="selectedRating">5</span>
                        </div>
                    </div>

                    
  
                    <div style="text-align: end; margin-top: 30px;">
                        <button type="submit" class="btn btn-primary" id="finalDoneButton">Submit</button>
                    </div>
                </div>
            </div>
        </div>
      </form>`;
  modalBody.innerHTML = cardContent;


  const ratingContainer = document.getElementById('ratingContainer');
  const selectedRating = document.getElementById('selectedRating');

  function updateRating(value) {
    document.querySelectorAll('.rating-number').forEach(el => el.classList.remove('active'));
    for (let j = 0; j < value; j++) {
      document.querySelectorAll('.rating-number')[j].classList.add('active');
    }
    selectedRating.textContent = value;
  }

  for (let i = 1; i <= 10; i++) {
    const div = document.createElement('div');
    div.classList.add('rating-number');
    div.textContent = i;
    if (i <= 5) {
      div.classList.add('active');
    }
    div.addEventListener('click', function () {
      updateRating(i);
    });
    ratingContainer.appendChild(div);
  }

  function resetRating() {
    updateRating(5);
  }

  // Show modal
  var statusModal = new bootstrap.Modal(document.getElementById('statusModal'));
  statusModal.show();
  const form = document.getElementById('underReviewForm');
  if (form) {
    form.onsubmit = function (event) {
      event.preventDefault();
      const partsReceived = document.getElementById('partsReceived').checked;
      const workCompleted = document.getElementById('workCompleted').checked;
      const remarks = document.getElementById('remarks').value;
      const completionDate = document.getElementById('completionDate').value;
      const vendorRating = document.getElementById('selectedRating').textContent;
      let data = {
        "type": "update",
        "updateType": "jobSatisfactionApprove",
        "rfq_id": id,
        "checkbox": JSON.stringify({ partsReceived, workCompleted }),
        "remarks": remarks,
        "completiondate": completionDate,
        "rating": vendorRating
      };
      console.log(data);

      $.ajax({
        url: `controller/rfq.php`,
        type: 'POST',
        contentType: "application/json",
        data: JSON.stringify(data),
        beforeSend: function () {
          handleLoader('flex');
        },
        success: function (res) {
          if (res.success) {
            showNotification("success", res.message);
            getRfqData();
            document.getElementById("remarks").value = "";
            var statusModal = bootstrap.Modal.getInstance(document.getElementById('statusModal'));
            statusModal?.hide();
          } else {
            showNotification("error", res.message);
          }
        },
        error: function (xhr, status, error) {
          console.log('Error during form submission:', xhr.responseText);
        },
        complete: function () {
          handleLoader('none');
        },
      });
    };
  }

}

function openRecordExpense(jobId) {
  // Show modal
  document.getElementById('recordExpenseBtn').setAttribute("data-jobId", jobId);
  var statusModal = new bootstrap.Modal(document.getElementById('recordExpense'));
  statusModal.show();

}

function recordExpense() {
  console.log('object');
  const jobId = document.getElementById('recordExpenseBtn').getAttribute("data-jobId");
  console.log(rowDataMap[jobId]);
  let jobData = rowDataMap[jobId];
  generatePdf(jobData, true)
}

function handleQuotationRequest(action, previousAction, id, reason = "") {
  console.log('action');
  console.log(previousAction);
  let data = {
    "type": "update",
    "updateType": (previousAction === "isApproved")
      ? "higherAuthority"
      : (designation === "Manager Fleet")
        ? "quotationRecieved"
        : "quotationApproved",
    "rfq_id": id,
  };


  if (action == "reject") {
    data.status = "reject";
    data.updateType = "invoiceApprove";
    data.reason = reason;
  }
  if (previousAction == "invoiceApprove") {
    data.status = "approve";
    data.updateType = "invoiceApprove";
  }
  if (action == 'isApprovedByGops') {
    data.updateType = "higherAuthority";
  }

  if (action == 'submit quotation' || action == 'Approve' || action == 'isApproved' || action == 'isApprovedByGops') {
    data.status = "approve"

  } else if (action == 'Return') {
    if (previousAction != 'submit quotation') {
      data.updateType = "reject";
    }
    data.status = "return";
    data.reason = reason;
  } else if (action == 'Deactivate') {
    data.status = "deactivate";
    data.reason = reason;
  }


  console.log(data);
  $.ajax({
    url: `controller/rfq.php`,
    type: 'POST',
    contentType: "application/json",
    data: JSON.stringify(data),
    beforeSend: function () {
      handleLoader('flex');
    },
    success: function (res) {
      if (res.success) {
        showNotification("success", res.message);
        getRfqData();
        document.getElementById("reasonInput").value = "";
        var statusModal = bootstrap.Modal.getInstance(document.getElementById('statusModal'));
        statusModal?.hide();
      } else {
        showNotification("error", res.message);
      }
    },
    error: function (xhr, status, error) {
      console.log('Error during form submission:', xhr.responseText);
    },
    complete: function () {
      handleLoader('none');
    },
  });
}
