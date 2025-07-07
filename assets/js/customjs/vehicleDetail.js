$('#activeVehicledetial').DataTable({
  scrollX: true,
});
getVehicleData();


function adjustTable(tableId) {
  const table = $(`#${tableId}`).DataTable();
  table.columns.adjust().draw(); // Adjust column widths and redraw
  if (table.responsive) {
    table.responsive.recalc(); // Recalculate if responsive extension is used
  }
}

function createUpdateModals(modalId, heading, formId, tableId) {
  const modal = document.getElementById('main-content');
  modal.insertAdjacentHTML(
    'afterend',
    `<div class="modal fade" id="${modalId}" data-bs-backdrop="static" data-bs-keyboard="false"
            tabindex="-1" role="dialog" aria-labelledby="${modalId}Label" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="${modalId}Label">Add ${heading}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="row mx-4">
                        <div class="col-sm-12 mt-3">
                            <table class="table table-bordered" id='${tableId}' style="min-width:300px;">
                                <thead>
                                   <tr>
                                        <th style="font-size:13px;min-width:35px;max-width:35px">S.NO</th>
                                        <th style="font-size:13px;min-width:130px;max-width:130px">${heading}</th>
                                        <th style="font-size:13px;min-width:130px;max-width:130px">Creation Date</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <form id="${formId}">
                        <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="add${formId}" class="form-label">Enter ${heading} Name</label>
                                    <input type="text" class="form-control" placeholder="Enter ${heading} Name"
                                        id="add${formId}" required>
                                </div>
                            </div>
                        </div>
                             
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>`
  );
  // Add form submission handling
  document.getElementById(formId).addEventListener('submit', function (e) {
    e.preventDefault();
    const formData = getFormData(formId);
    sendAjaxRequest(heading.toLowerCase(), formData, this.id, modalId);
  });
}

createUpdateModals('addMakeModal', 'Make', 'addMakeForm', 'makeTable');
createUpdateModals('addModelModal', 'Model', 'addModelForm', 'modelTable');
createUpdateModals(
  'addStationModal',
  'Station',
  'addStationForm',
  'stationTable'
);
createUpdateModals(
  'addCategoryModal',
  'Category',
  'addCategoryForm',
  'categoryTable'
);

function getFormData(formId) {
  const formData = new FormData();
  const inputField = document.getElementById(`add${formId}`);
  let apiKey;
  if (formId === 'addMakeForm') {
    apiKey = 'makename';
  } else if (formId === 'addModelForm') {
    apiKey = 'modelname';
  } else if (formId === 'addStationForm') {
    apiKey = 'stationname';
  } else if (formId === 'addCategoryForm') {
    apiKey = 'categoryname';
  }
  formData.append(apiKey, inputField.value.trim());
  formData.append('type', 'add');
  return formData;
}

function populateDataFromApi(apiUrl, val, key, id, defaultText) {
  const formDataforPopulate = new FormData();
  formDataforPopulate.append('type', 'view');

  $.ajax({
    url: `controller/${apiUrl}.php`,
    type: 'POST',
    data: formDataforPopulate,
    processData: false,
    contentType: false,
    beforeSend: function () {
      handleLoader('flex');
    },
    success: function (res) {
      let row = '';
      let dataObj;
      if (res.success) {
        populateOptions(id, res.data, val, key, defaultText);
        populateOptions(`ed${id}`, res.data, val, key, defaultText);
        dataObj = res.data;
        dataObj = dataObj.reverse();
        dataObj.map((el, i) => {
          row += `<tr>
                        <td style="font-size:12px;min-width:30px;max-width:30px">${++i}</td>
                        <td style="font-size:12px;min-width:130px;max-width:130px">${el[`${apiUrl}name`]
            }</td>
                        <td style="font-size:12px;min-width:130px;max-width:130px">${el['creationdate']
            }</td>
                    </tr>`;
        });
        initializeDataTable(`${apiUrl}Table`, row);
        adjustTable(`${apiUrl}Table`);
      }
    },
    error: function (xhr, status, error) {
      console.error('Error:', error);
    },
    complete: function () {
      handleLoader('none');
    },
  });
}

populateDataFromApi('make', 'makeid', 'makename', 'vdMake', 'Select Make');
populateDataFromApi('model', 'modelid', 'modelname', 'vdModel', 'Select Model');
populateDataFromApi(
  'category',
  'categoryid',
  'categoryname',
  'vdCategory',
  'Select Category'
);
populateDataFromApi(
  'station',
  'stationid',
  'stationname',
  'vdStation',
  'Select Station'
);

function handleEdit(button) {
  let row = $(button).closest('tr');
  let vehId = row.find('td[data-field="vehicleId"]').text();
  let regNo = row.find('td[data-field="Reg"]').text();
  let make = row.find('td[data-field="makename"]').text();
  let model = row.find('td[data-field="modelname"]').text();
  let category = row.find('td[data-field="categoryname"]').text();
  let engine = row.find('td[data-field="engine"]').text();
  let chassis = row.find('td[data-field="chassis"]').text();
  let regDate = row.find('td[data-field="doi"]').text();
  let depYears = row.find('td[data-field="dep_years"]').text();
  let station = row.find('td[data-field="stationname"]').text();
  let icn = row.find('td[data-field="icn"]').text();
  let pudGtw = row.find('td[data-field="pudgtw"]').text();
  let leasedOwned = row.find('td[data-field="device"]').text();
  let aiDashMDVR = row.find('td[data-field="segment"]').text();
  let fuelType = row.find('td[data-field="fuel_type"]').text();
  let payload = row.find('td[data-field="payload"]').text();
  let maturityDate = row.find('td[data-field="date_of_maturity"]').text();
  $('#edvdVehID').val(vehId);
  $('#edvdVehRegNo').val(regNo);

  $('#edvdStation option')
    .filter(function () {
      return $(this).text() === station;
    })
    .prop('selected', true)
    .change();
  $('#edvdMake option')
    .filter(function () {
      return $(this).text() === make;
    })
    .prop('selected', true)
    .change();
  $('#edvdModel option')
    .filter(function () {
      return $(this).text() === model;
    })
    .prop('selected', true)
    .change();
  $('#edvdCategory option')
    .filter(function () {
      return $(this).text() === category;
    })
    .prop('selected', true)
    .change();

  $('#edvdFuelType').val(fuelType).change();

  // Populate text fields
  $('#edvdEngine').val(engine);
  $('#edvdChassis').val(chassis);
  $('#edvdRegDate').val(regDate);
  $('#edvdDepYears').val(depYears);
  $('#edvdICN').val(icn);
  $('#edvdPayload').val(payload);
  $('#edvdMaturityDate').val(maturityDate);
  $('input[name="edvdpudGtw"][value="' + pudGtw + '"]').prop('checked', true);
  $('input[name="edvdleasedOwned"][value="' + leasedOwned + '"]').prop(
    'checked',
    true
  );
  $('input[name="edvdaiDashMDVR"][value="' + aiDashMDVR + '"]').prop(
    'checked',
    true
  );

  $('#editVehicleDetail').modal('show');
}

function handleDelete(vehId, vehicle, status) {
  Swal.fire({
    title: 'Are you sure?',
    html: `
      <p class='custom-msg'>You want to change the status of <span class="custom-msg-reg">${vehicle}</span>.</p>
      <div class="d-flex justify-content-center align-items-center my-3" style="gap: 20px;">
        <label for="statusSelect" class="swal2-label">Select an action:</label>
        <select id="statusSelect" class="swal2-input" onchange="handleStatusChange()">
          ${status !== 'Active' ? '<option value="Active">Active</option>' : ''}
          ${status !== 'Deactivated' ? '<option value="Deactivated">Deactivated</option>' : ''}
          ${status !== 'Downtime' ? '<option value="Downtime">Down Time</option>' : ''}
        </select>
      </div>
      <div class="d-flex justify-content-center align-items-center my-3" style="gap: 20px;">
        <label for="statusremarks" class="swal2-label">Remarks:</label>
        <textarea id="statusremarks" name="remarks" maxlength="512" style="height: auto;" class="swal2-input" rows="3" placeholder="Enter your remarks here (optional)"></textarea>
      </div>
      <div id="dateFields" style="display: none;">
        <div class="d-flex justify-content-center align-items-center my-3" style="gap: 20px;">
          <label for="startDate" class="swal2-label">Start Date:</label>
          <input type="date" id="startDate" class="swal2-input">
        </div>
        <div class="d-flex justify-content-center align-items-center my-3" style="gap: 20px;">
          <label for="endDate" class="swal2-label">End Date:</label>
          <input type="date" id="endDate" class="swal2-input">
        </div>
      </div>
    `,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Confirm',
    didOpen: () => {
      document.getElementById('statusSelect').addEventListener('change', handleStatusChange);
      document.getElementById('startDate').value = new Date().toISOString().split('T')[0];
    },
    preConfirm: () => {
      const selectedAction = document.getElementById('statusSelect').value;
      const startDate = document.getElementById('startDate').value.trim();
      const remarks = document.getElementById('statusremarks').value.trim();
      const endDate = document.getElementById('endDate') ? document.getElementById('endDate').value : null;

      if (selectedAction === 'Deactivated' && !remarks) {
        Swal.showValidationMessage('Remarks are required for deactivating a vehicle.');
        return false;
      }
      if (selectedAction === 'Downtime') {
        if (!remarks) {
          Swal.showValidationMessage('Remarks are required for downtime status.');
          return false;
        }
        if (!endDate) {
          Swal.showValidationMessage('End date is required for downtime status.');
          return false;
        }
        if (new Date(endDate) < new Date()) {
          Swal.showValidationMessage('End date Should be greater than Start Date.');
          return false;
        }
      }

      return { selectedAction, remarks, endDate, startDate };
    }
  }).then((result) => {
    if (result.isConfirmed) {
      const { selectedAction, remarks, endDate, startDate } = result.value;
      const formData = new FormData();
      formData.append('vid', vehId);
      formData.append('type', 'delete');
      formData.append('status', selectedAction);
      formData.append('remarks', remarks);
      if (selectedAction === 'Downtime') formData.append('from', startDate);
      if (selectedAction === 'Downtime') formData.append('to', endDate);

      $.ajax({
        url: `controller/vehicle.php`,
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
            if (selectedAction === 'Downtime') {
              message = 'The vehicle status has been updated to downtime.';
            } else if (selectedAction === 'Deactivated') {
              message = 'The vehicle has been deactivated.';
            } else if (selectedAction === 'Active') {
              message = 'The vehicle has been activated.';
            }
            Swal.fire({
              title: 'Success!',
              text: message,
              icon: 'success',
            });
            getVehicleData();
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

function handleStatusChange() {
  const status = document.getElementById('statusSelect').value;
  const dateFields = document.getElementById('dateFields');
  const remarksField = document.getElementById('statusremarks');

  if (status === 'Downtime') {
    dateFields.style.display = 'block';
    remarksField.placeholder = 'Remarks are required for downtime...';
  } else {
    dateFields.style.display = 'none';
  }

  if (status === 'Deactivated') {
    remarksField.placeholder = 'Remarks are required for deactivation...';
  } else if (status === 'Active') {
    remarksField.placeholder = 'Enter your remarks here (optional)...';
  }
}

function handleFormSubmission(formId, modalId, type, isEdit) {
  document.getElementById(formId).addEventListener('submit', function (e) {
    e.preventDefault();
    var prefix = isEdit ? 'ed' : '';
    var form = new FormData();
    if (isEdit) {
      form.append('vid', $(`#${prefix}vdVehID`).val());
    }
    form.append('Reg', $(`#${prefix}vdVehRegNo`).val());
    form.append('make_id', $(`#${prefix}vdMake`).val());
    form.append('model_id', $(`#${prefix}vdModel`).val());
    form.append('category_id', $(`#${prefix}vdCategory`).val());
    form.append('engine', $(`#${prefix}vdEngine`).val());
    form.append('chassis', $(`#${prefix}vdChassis`).val());
    form.append('doi', $(`#${prefix}vdRegDate`).val());
    form.append('dep_years', $(`#${prefix}vdDepYears`).val());
    form.append('station_id', $(`#${prefix}vdStation`).val());
    form.append('icn', $(`#${prefix}vdICN`).val());
    form.append(
      'device',
      $(`input[name='${prefix}vdleasedOwned']:checked`).val()
    );
    form.append(
      'segment',
      $(`input[name='${prefix}vdaiDashMDVR']:checked`).val()
    );
    form.append('pudgtw', $(`input[name='${prefix}vdpudGtw']:checked`).val());
    form.append('fuel_type', $(`#${prefix}vdFuelType`).val());
    form.append('payload', $(`#${prefix}vdPayload`).val());
    form.append('date_of_maturity', $(`#${prefix}vdMaturityDate`).val());
    form.append('type', type);
    sendAjaxRequest('vehicle', form, this.id, modalId);
  });
}

function sendAjaxRequest(url, formData, formId, modalId = '') {
  $.ajax({
    url: `controller/${url}.php`,
    type: 'POST',
    data: formData,
    contentType: false,
    processData: false,
    beforeSend: function () {
      handleLoader('flex');
    },
    success: function (res) {
      if (res.success) {
        showNotification('success', res.message);
        if (modalId === 'AddVehicle' || modalId === 'editVehicleDetail') {
          getVehicleData();
        }
        if (modalId === 'addMakeModal') {
          populateDataFromApi(
            'make',
            'makeid',
            'makename',
            'vdMake',
            'Select Make'
          );
        } else if (modalId === 'addModelModal') {
          populateDataFromApi(
            'model',
            'modelid',
            'modelname',
            'vdModel',
            'Select Model'
          );
        } else if (modalId === 'addStationModal') {
          populateDataFromApi(
            'station',
            'stationid',
            'stationname',
            'vdStation',
            'Select Station'
          );
        } else if (modalId === 'addCategoryModal') {
          populateDataFromApi(
            'category',
            'categoryid',
            'categoryname',
            'vdCategory',
            'Select Category'
          );
        }
        $(`#${modalId}`).modal('hide');

        clearForm(formId);
      }
    },
    error: function (xhr, status, error) {
      console.log('Error during form submission:', xhr.responseText);
      alert('An error occurred while updating vehicle details.');
    },
    complete: function () {
      handleLoader('none');
    },
  });
}
handleFormSubmission('editVehicleForm', 'editVehicleDetail', 'update', true);
handleFormSubmission('addVehicleForm', 'AddVehicle', 'add', false);

function getVehicleData() {
  const formData = new FormData();
  formData.append('type', 'view');

  $.ajax({
    url: 'controller/vehicle.php',
    type: 'POST',
    processData: false,
    contentType: false,
    data: formData,
    beforeSend: function () {
      handleLoader('flex');
    },
    success: function (data) {
      let dataObj;
      if (data.success) {
        // Clear the table bodies
        if ($.fn.DataTable.isDataTable('#activeVehicledetial')) {
          $('#activeVehicledetial').DataTable().destroy();
        }
        if ($.fn.DataTable.isDataTable('#deactiveVehicledetial')) {
          $('#deactiveVehicledetial').DataTable().destroy();
        }
        if ($.fn.DataTable.isDataTable('#downTimeVehicledetial')) {
          $('#downTimeVehicledetial').DataTable().destroy();
        }
        $('#activeVDTbody').empty();
        $('#deactiveVDTbody').empty();
        $('#downtimeVDTbody').empty();

        dataObj = data.data;
        dataObj = dataObj.reverse();
        console.log(dataObj);

        // Append rows to the appropriate table based on status
        dataObj.forEach(function (el, i) {
          let startDateHtml = el.status.toLowerCase() === 'downtime' ? `<td data-field="startDate">${el.startdate || '-'}</td>` : '';
          let endDateHtml = el.status.toLowerCase() === 'downtime' ? `<td data-field="endDate">${el.enddate || '-'}</td>` : '';
          const rowHtml = `
            <tr data-id="${el.V_id}">
              <td data-field="Sno">${++i}</td>
              <td data-field="vehicleId" style="display:none">${el.V_id}</td>
              <td data-field="status">
                <a role="button" title="Status">
                  <span class="badge badge-pill badge-soft-${el.status.toLowerCase() === 'active' ? 'success' : el.status.toLowerCase() === 'maintenance' ? 'warning' : 'danger'}   font-size-12">
                    ${capitalizeFirstLetter(el.status)}
                  </span>

                </a>
              </td>
              <td style="width: 100px">
                <a class="btn btn-outline-success btn-sm" title="Edit" onclick="handleEdit(this)">
                  <i class="fas fa-pencil-alt"></i>
                </a>
                <a class="btn btn-outline-danger btn-sm edit" onclick="handleDelete('${el.V_id}', '${el.Reg}','${el.status}')" title="Delete">
                  <i class="fas fa-toggle-on"></i>
                </a>
              </td>
              <td data-field="Reg">${el.Reg || '-'}</td>
              <td data-field="makename">${el.makename || '-'}</td>
              <td data-field="modelname">${el.modelname || '-'}</td>
              <td data-field="stationname">${el.stationname || '-'}</td>
              <td data-field="engine">${el.engine || '-'}</td>
              <td data-field="chassis">${el.chassis || '-'}</td>
              <td data-field="doi">${el.doi || '-'}</td>
              <td data-field="dep_years">${el.dep_years || '-'}</td>
              <td data-field="icn">${el.icn || '-'}</td>
              <td data-field="device">${el.device || '-'}</td>
              <td data-field="segment">${el.segment || '-'}</td>
              <td data-field="pudgtw">${el.pudgtw || '-'}</td>
              <td data-field="fuel_type">${el.fuel_type || '-'}</td>
              <td data-field="payload">${el.payload || '-'}</td>
              <td data-field="date_of_maturity">${el.date_of_maturity || '-'}</td>
              <td data-field="date_of_maturity">${el.categoryname || '-'}</td>
              <td data-field="date_of_maturity">${el.remarks || '-'} </td>
              ${startDateHtml}
              ${endDateHtml}
            </tr>
          `;

          if (el.status === 'Active') {
            $('#activeVDTbody').append(rowHtml);
          } else if (el.status === 'Downtime') {
            $('#downtimeVDTbody').append(rowHtml);
          } else {
            $('#deactiveVDTbody').append(rowHtml);
          }
        });

        // Initialize DataTables for both tables
        $('#activeVehicledetial').DataTable({
          scrollX: true,
          scrollCollapse: true,
        });
        $('#deactiveVehicledetial').DataTable({
          scrollX: true,
          scrollCollapse: true,
        });
        $('#downTimeVehicledetial').DataTable({
          scrollX: true,
          scrollCollapse: true,
        });
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

