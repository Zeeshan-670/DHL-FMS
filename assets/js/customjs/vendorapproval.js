'use strict';

let expenseData;
let taxData;
let rowDataMap = {};

function getDataForApproval() {
  let data = {
    type: 'history',
    vendor_id: vendorIID
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
      try {
        let row = '';
        let dataObj;
        if (data.success) {
          dataObj = data.data;
          dataObj = dataObj.reverse();
          dataObj.map((el, index) => {
            const uniqueId = `row-${index}`;
            rowDataMap[el.job_id] = el;
            let statusActions = '';
            switch (el.Status) {
              case 'Pending':
                statusActions = `
                  <a class="btn btn-action complete" onclick="openQuotationModal('Pending','${el.job_id}', '${el.jobTitle}')">
                    <span class="badge badge-pill badge-soft-info font-size-12">Submit Quotation</span>
                  </a>
                `;
                break;
              case 'submit quotation':
                statusActions = `
                  <a class="btn btn-action complete" data-bs-toggle="tooltip" data-bs-placement="top" title="Waiting For DHL To Response">
                    <span class="badge badge-pill badge-soft-warning font-size-12">RFQ Submitted - Awaiting DHL response</span>
                  </a>
                `;
                break;
              case 'approve':
                statusActions = `
                  <a class="btn btn-action complete" data-bs-toggle="tooltip" data-bs-placement="top" title="Waiting For DHL To Response">
                    <span class="badge badge-pill badge-soft-warning font-size-12">RFQ Submitted - Awaiting DHL response</span>
                  </a>
                `;
                break;
              case 'isApproved':
                statusActions = `
                  <a class="btn btn-action complete" data-bs-toggle="tooltip" data-bs-placement="top" title="Waiting For DHL To Response">
                    <span class="badge badge-pill badge-soft-warning font-size-12">RFQ Submitted - Awaiting DHL response</span>
                  </a>
                `;
                break;
              case 'isApprovedByGops':
                statusActions = `
                  <a class="btn btn-action complete" data-bs-toggle="tooltip" data-bs-placement="top" title="Waiting For DHL To Response">
                    <span class="badge badge-pill badge-soft-warning font-size-12">RFQ Submitted - Awaiting DHL response</span>
                  </a>
                `;
                break;
              case 'return':
                statusActions = `
                  <a class="btn btn-action complete" data-bs-toggle="tooltip" data-bs-placement="top" title="Reason: ${el.Reason || '-'}" onclick="openQuotationModal('return','${el.job_id}', '${el.jobTitle}')">
                    <span class="badge badge-pill badge-soft-info font-size-12">Quotation Returned</span>
                  </a>
                `;
                break;
              case 'return by manager':
                statusActions = `
                  <a class="btn btn-action complete" data-bs-toggle="tooltip" data-bs-placement="top" title="Reason: ${el.Reason || '-'}" onclick="openQuotationModal('return','${el.job_id}', '${el.jobTitle}')">
                    <span class="badge badge-pill badge-soft-info font-size-12">Quotation Returned</span>
                  </a>
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
                statusActions = `<a role="button" title="Request job satisfaction" onclick="markJobCompleted('approve by manager','${el.job_id}', '${el.jobTitle}')">
                    <span class="badge badge-pill badge-soft-info font-size-12">Request Job Satisfaction</span>
                  </a>`;
                break;
              case 'isApproved by manager':
                statusActions = `<a role="button" title="Request job satisfaction" onclick="markJobCompleted('approve by manager','${el.job_id}', '${el.jobTitle}')">
                    <span class="badge badge-pill badge-soft-info font-size-12">Request Job Satisfaction</span>
                  </a>`;
                break;
              case 'job satisfaction requested':
                statusActions = `<a role="button" title="Job Satisfaction Requested- Awaiting DHL response">
                    <span class="badge badge-pill badge-soft-warning font-size-12">Job Satisfaction Requested- Awaiting DHL response</span>
                  </a>`;
                break;
              case 'job satisfaction approved':
                statusActions = `<a role="button" title="View Status" onclick="openInvoiceModal('${el.job_id}', '${el.jobTitle}')">
                    <span class="badge badge-pill badge-soft-info font-size-12">Satisfaction Granted - Submit Invoice</span>
                  </a>`;
                break;
              case 'Invoice reject':
                statusActions = `<a role="button" title="View Status" onclick="openInvoiceModal('${el.job_id}', '${el.jobTitle}')">
                    <span class="badge badge-pill badge-soft-info font-size-12">Invoice Rejected - Submit Invoice</span>
                  </a>`;
                break;
              case 'Invoice approve':
                statusActions = `<a role="button" title="View Status">
                    <span class="badge badge-pill badge-soft-success font-size-12">Job Completed</span>
                  </a>`;
                break;
              case 'Completed':
                statusActions = `<a role="button" title="View Status">
                    <span class="badge badge-pill badge-soft-success font-size-12">Job Completed</span>
                  </a>`;
                break;
              case 'invoice recieved':

                statusActions = `
                  <a class="btn btn-action complete" data-bs-toggle="tooltip" data-bs-placement="top" title="Waiting For DHL To Response">
                    <span class="badge badge-pill badge-soft-warning font-size-12">Invoice Submitted - Awaiting DHL response</span>
                  </a>
                `;
                break;
              default:
                statusActions = `<span class="badge badge-pill badge-soft-secondary font-size-12">${capitalizeFirstLetter(
                  el.Status
                )}</span>`;
            }


            let workorderStatus = "";
            let status = el.Status.trim().toLowerCase(); // Normalize status (trim spaces, convert to lowercase)

            if (["pending", "submit quotation", "approve", 'isapproved', 'isapprovedbygops', "deactivate", "return",].includes(status)) {
              workorderStatus = "-";
            } else {
              workorderStatus = `<a href="#" class="view-workorder" data-row-id="${el.job_id}">View Workorder</a>`;
            }
            row += `
              <tr id="${uniqueId}">
                <td>${index + 1}</td> <!-- increment index -->
                <td style="width: 100px">${statusActions}</td>
                <td>${el.creationdate || '-'}</td>
                <td>${el.jobTitle || '-'}</td>
                <td>${el.Reg || '-'}</td>
                <td>${el.Reason || '-'}</td>
                <td>
                 ${workorderStatus}
                </td>
              </tr>
            `;
          });
          initializeDataTable(`rfqDetailTable`, row);
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

getTaxData();
getExpenseCategory();
getDataForApproval();

function calculateAmountWithoutTax(totalWithTax, salesTaxValue) {
  const amountWithoutTax = (Number(totalWithTax) / (1 + Number(salesTaxValue) / 100)).toFixed(2);
  return amountWithoutTax;
}


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
                <div class="col-md-2">
                    <select class="form-select me-2 alert-type category-select" required>
                        <option value="">Select Category</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="number" step="0.01" min="0" class="form-control amount-input" placeholder="Enter Amount" required>
                </div>
                <div class="col-md-2">
                    <div class="input-group">
                        <select class="form-select tax-select" required>
                        </select>
                        <button type="button" class="btn btn-primary docs-datepicker-trigger">%</button>
                    </div>
                </div>
                <div class="col-md-2">
                  <input class="form-control total-amount" value="" placeholder="Total Amount" disabled>
                </div>
                <div class="col-md-3">
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

// $(document).ready(function () {
function updateTotalAmount(row) {
  let amount = parseFloat($(row).find(".amount-input").val()) || 0;
  let taxPercentage = parseFloat($(row).find(".tax-select option:selected").text()) || 0;

  // Calculate total amount with tax
  let totalAmount = amount + (amount * taxPercentage / 100);
  $(row).find(".total-amount").val(totalAmount.toFixed(2));

  updateGrandTotal();
}

function updateGrandTotal() {
  let grandTotal = 0;
  $(".total-amount").each(function () {
    grandTotal += parseFloat($(this).val()) || 0;
  });
  $(".grand-total").text(`Rs.${grandTotal.toFixed(2)}/-`);
}

// Event listener for amount input change
$(document).on("input", ".amount-input", function () {
  let row = $(this).closest(".alert-row");
  updateTotalAmount(row);
});

// Event listener for tax selection change
$(document).on("change", ".tax-select", function () {
  let row = $(this).closest(".alert-row");
  updateTotalAmount(row);
});
// });


function openQuotationModal(status, id, heading) {
  const modalBody = document.getElementById('modalBodyContent');
  modalBody.innerHTML = '';
  let cardContent = '';
  const statusModalLabel = document.getElementById('statusModalLabel');
  statusModalLabel.innerHTML = `${heading}`;
  cardContent = `
      <div class="mt-4">
        ${status == 'return' ? `<div class="alert alert-danger" role="alert">
            <strong>Reason:</strong>  <span>${rowDataMap[id]['Reason']}</span>
          </div>` : ""}
        <form id="rfqForm">
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
              <div>
                  <button type="button" class="btn btn-primary add-row w-100"><i class="fas fa-plus"></i></button>
              </div>
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
                          <input type="number" step="0.01" min="0" class="form-control amount-input" value="${amountWithoutTax}" placeholder="Enter Amount">
                          <input type="hidden" class="job-id" value="${rowDataMap[id]['job_id']}">
                          <input type="hidden" class="ExpenseDetailId" value="${el['ExpenseDetailId']}">
                        </div>
                        <div class="col-md-2">
                          ${i === 0 ? '<label class="form-label">Tax</label>' : ''}
                          <div class="input-group">
                              <select class="form-select alert-type tax-select" id="expenseTax-${i}">
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
                          <input class="form-control total-amount" value="${el['amount']}" placeholder="Total Amount" disabled>
                        </div>
                       <div class="col-md-4">
                          ${i === 0 ? '<label class="form-label">Description</label>' : ''}
                            <input type="text" class="form-control" placeholder="Enter Description" 
                              value="${el['description']} " required  maxlength="512"
                              oninput="if(this.value.indexOf('${el['description']}') !== 0) this.value = '${el['description']}';">
                        </div>
                    </div>
                  `;
  }).join('')}
            </div>
            <div class="row">
              <div class="col-md-12 text-end mt-4">
                  <strong>Grand Total:</strong><span class="grand-total mx-3"> Rs.${(parseFloat(rowDataMap[id]?.grandTotal) || 0).toFixed(2)}/- </span>
              </div>
            </div>
            
            <div class="row">
                <div class="col-md-12 text-end mt-4">
                    <button class="btn btn-primary" id="doneButton"
                        type="submit">Forward</button>
                </div>
            </div>
        </form>
    </div>`;
  modalBody.innerHTML = cardContent;
  // Show modal
  var statusModal = new bootstrap.Modal(document.getElementById('statusModal'));
  statusModal.show();
  const form = document.getElementById('rfqForm');
  if (form) {
    form.onsubmit = function (event) {
      event.preventDefault();
      submitInvoiceForm();
    };
  }
}
function submitInvoiceForm() {
  const expenseRows = document.querySelectorAll(".alert-row");
  const expenses = [];
  let jobID;
  let isValid = true;

  expenseRows.forEach((row, index) => {
    const expenseCategory = row.querySelector(".category-select");
    const amount = row.querySelector("input[type='number']");
    const tax = row.querySelector(".tax-select");
    const rfq_id = row.querySelector(".job-id")?.value;
    const expensesID = row.querySelector(".ExpenseDetailId")?.value || null;
    const description = row.querySelector("input[type='text']");

    if (!expenseCategory.value.trim()) {
      showNotification("error", `Expense Category is required in row ${index + 1}`);
      expenseCategory.focus();
      isValid = false;
      return;
    }
    if (!description.value.trim()) {
      showNotification("error", `Description is required in row ${index + 1}`);
      description.focus();
      isValid = false;
      return;
    }

    // Ensure the amount is not empty and not 0
    const parsedAmount = parseFloat(amount.value);
    if (!amount.value.trim() || isNaN(parsedAmount) || parsedAmount === 0) {
      showNotification("error", `Amount is required in row ${index + 1}`);
      amount.focus();
      isValid = false;
      return;
    }

    const expenseObject = {
      expenseCategory: expenseCategory.value,
      amount: parsedAmount,
      tax: tax.value,
      description: description.value.trim(),
    };

    if (expensesID) {
      expenseObject.ExpenseDetailId = expensesID;
    }
    if (rfq_id) {
      jobID = rfq_id;
    }
    expenseObject.rfq_id = jobID;
    expenses.push(expenseObject);
  });

  if (!isValid) return;
  console.log(expenses);

  const payload = {
    type: "update",
    updateType: "quotation",
    expenses: expenses,
    rfq_id: jobID
  };

  console.log(payload);

  $.ajax({
    url: `controller/rfq.php`,
    type: "POST",
    contentType: "application/json",
    data: JSON.stringify(payload),
    beforeSend: function () { handleLoader("flex"); },
    success: function (res) {
      console.log(res);
      if (res.success) {
        showNotification("success", res.message);
        getDataForApproval();
        var statusModal = bootstrap.Modal.getInstance(
          document.getElementById('statusModal')
        );
        statusModal?.hide();
      } else {
        showNotification("error", res.message);
      }
    },
    complete: function () { handleLoader("none"); },
  });
}



function openInvoiceModal(id, heading) {
  const modalBody = document.getElementById('modalBodyContent');
  modalBody.innerHTML = '';
  let cardContent = '';
  const statusModalLabel = document.getElementById('statusModalLabel');
  statusModalLabel.innerHTML = `Submit Invoice for: ${heading}`;
  cardContent = `
      <div class="card">
        <div class="card-body pb-0">
          <form id="invoiceForm">
            <div class="mb-3">
              <label for="invoice" class="form-label">Upload Invoice</label>
              <input class="form-control" type="file" id="invoice" accept="application/pdf, image/jpeg, image/png, image/gif, image/bmp, image/svg+xml" required>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
          </form>
        </div>
      </div >`;
  modalBody.innerHTML = cardContent;

  // Show modal
  var statusModal = new bootstrap.Modal(document.getElementById('statusModal'));
  statusModal.show();
  const form = document.getElementById('invoiceForm');
  if (form) {
    form.onsubmit = function (event) {
      event.preventDefault();
      const invoiceFile = document.getElementById('invoice').files[0];

      if (!invoiceFile) {
        showNotification("error", "Please upload a file.");
        return;
      }

      let formData = new FormData();
      formData.append("contentFile", invoiceFile, "[PROXY]"); // Append file with "[PROXY]" name
      formData.append("json_data", JSON.stringify({
        "type": "update",
        "updateType": "invoiceRecieved",
        "rfq_id": id
      }));

      $.ajax({
        url: `controller/rfq.php`,
        type: 'POST',
        processData: false, // Prevent jQuery from converting FormData to a query string
        contentType: false, // Ensure correct Content-Type for file upload
        data: formData,
        beforeSend: function () {
          handleLoader('flex');
        },
        success: function (res) {
          if (res.success) {
            showNotification("success", res.message);
            getDataForApproval();
            document.getElementById("invoice").value = "";
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
      let data = {
        type: 'update',
        updateType: 'jobSatisfaction',
        rfq_id: id,
      }
      $.ajax({
        url: `controller/rfq.php`,
        type: 'Post',
        contentType: "application/json",
        data: JSON.stringify(data),
        beforeSend: function () {
          handleLoader('flex');
        },
        success: function (res) {
          if (res.success) {
            let message = '';
            Swal.fire({
              title: 'Success!',
              text: 'Job Satisfaction Request sent.',
              icon: 'success',
            });
            getDataForApproval();
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