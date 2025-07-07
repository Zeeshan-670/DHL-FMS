$(document).ready(function () {
  async function handleVehicleSearch(e, type) {
    e.preventDefault();
    console.log('Submit event triggered for:', type); // Debugging

    const inputField = type === 'job' ? 'jobsearchVeh' : 'workordersearchVeh';
    const vehicleNo = document.getElementById(inputField).value.trim();

    if (!vehicleNo) {
      showNotification('error', 'Please enter a vehicle number.');
      return;
    }

    const formData = new FormData();
    formData.append('vehicle_reg', vehicleNo);
    formData.append('type', type); // Add type to API request

    try {
      handleLoader('flex');
      const response = await fetch(
        'https://mdvr2.itecknologi.com:8080/FMS/controller/search_vehicle.php',
        {
          method: 'POST',
          body: formData,
        }
      );
      if (!response.ok) {
        throw new Error('Network response was not ok');
      }

      const res = await response.json();

      if (res.success) {
        showNotification('success', 'Vehicle Found');
        if (type === 'job') {
          populateJobVehicleData(res.data)
        } else {
          populateWorkOrderVehicleData(res.data.vehicle_details);
        }
      } else {
        showNotification('error', 'Please create a job first to start the work order');

      }
    } catch (error) {
      console.error('Error during Vehicle Searching:', error);
      showNotification(
        'error',
        'An error occurred while searching vehicle details.'
      );
    } finally {
      handleLoader('none');
    }
  }

  initVehicleSearch("#jobsearchVeh", "#jobSearch", handleVehicleSearch, 'job');
  initVehicleSearch("#workordersearchVeh", "#workorderSearch", handleVehicleSearch, 'workorder');
});


function populateVendorOptions(id, data, val, text, defaultText, emailAttr = null) {
  var select = document.getElementById(id);
  select.innerHTML = '';

  // Add default option
  var defaultOption = document.createElement('option');
  defaultOption.value = '';
  defaultOption.selected = true;
  defaultOption.text = defaultText;
  select.appendChild(defaultOption);

  var fragment = document.createDocumentFragment();
  var uniqueValues = {};

  data.forEach(function (item) {
    var optionValue = item[val];
    if (!uniqueValues[optionValue]) {
      uniqueValues[optionValue] = true;

      if (defaultText !== item[text]) {
        var option = document.createElement('option');
        option.value = optionValue;
        option.text = item[text];
        if (emailAttr && item[emailAttr]) {
          option.setAttribute('data-email', item[emailAttr]);
        }

        fragment.appendChild(option);
      }
    }
  });

  select.appendChild(fragment);
}

function populateDataVendor() {
  const formDataforPopulate = new FormData();
  formDataforPopulate.append('type', 'view');

  $.ajax({
    url: `controller/vendor.php`,
    type: 'POST',
    data: formDataforPopulate,
    processData: false,
    contentType: false,
    beforeSend: function () {
      handleLoader('flex');
    },
    success: function (res) {
      if (res.success) {
        populateVendorOptions('sltVendor', res.data, 'vendorid', 'vendorname', 'Select Vendor', 'vendoremail');
        populateVendorOptions('ctsltVendor', res.data, 'vendorid', 'vendorname', 'Select Vendor', 'vendoremail');
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

populateDataVendor()
function populateWorkOrderVehicleData(data) {
  setDataById('station', data['Station']);
  setDataById('dateField', data['Date']);
  setDataById('make', data['Make']);
  setDataById('model', data['Model']);
  setDataById('vid', data['V_id']);
  setDataById('regNo', data['Reg']);
  setDataById('mileage', data['Milage']);
  var select = document.getElementById('jobTitle');
  select.innerHTML = '';
  var defaultOption = document.createElement('option');
  defaultOption.value = '';
  defaultOption.selected = true;
  defaultOption.text = 'Select Job Title';
  select.appendChild(defaultOption);
  var fragment = document.createDocumentFragment();
  var uniqueValues = {};
  data['jobs'].forEach(function (item) {
    var optionValue = item['JobId'];
    if (!uniqueValues[optionValue]) {
      uniqueValues[optionValue] = true;
      if ('Select Job Title' !== item['JobTitle']) {
        var option = document.createElement('option');
        option.value = optionValue;
        option.text = item['JobTitle'];
        option.setAttribute('data-vendor_id', item['vendor_id']);
        fragment.appendChild(option);
      }
    }
  });

  select.appendChild(fragment);
  select.addEventListener('change', function () {
    var selectedOption = select.options[select.selectedIndex];
    var vendorId = selectedOption.getAttribute('data-vendor_id');
    var vendorSelect = document.getElementById('sltVendor');
    console.log(vendorId);
    if (vendorId) {
      vendorSelect.value = vendorId;
    } else {
      vendorSelect.value = ''; // Reset value if no vendor_id is selected
    }
  });
}

function populateJobVehicleData(data) {
  console.log(data[0]);
  setDataById('ctregNo', data[0].Reg);
  setDataById('ctvid', data[0].V_id);

}

function updateButtonText(inputValue, resultArray) {
  const buttonText = resultArray.includes(inputValue) ? 'Remove' : 'Add';
  $('#addButton').text(buttonText);
}

$('#addField').on('input', function () {
  const inputValue = $(this).val().trim();
  const resultArray = $('#resultField')
    .val()
    .split(', ')
    .map((item) => item.trim());
  updateButtonText(inputValue, resultArray);
});
$('#addButton').click(function () {
  const inputValue = $('#addField').val().trim();
  const resultArray = $('#resultField')
    .val()
    .split(', ')
    .map((item) => item.trim())
    .filter(Boolean);

  if (inputValue) {
    if ($(this).text() === 'Add') {
      if (!resultArray.includes(inputValue)) {
        resultArray.push(inputValue);
      }
    } else {
      const index = resultArray.indexOf(inputValue);
      if (index !== -1) resultArray.splice(index, 1);
    }

    $('#resultField').val(resultArray.join(', '));
    $('#addField').val('');
    updateButtonText('', resultArray);
  }
});

$('#addField').on('keydown', function (e) {
  if (e.key === 'Enter') {
    e.preventDefault();
    $('#addButton').click();
  }
});
document
  .getElementById('serviceOrderForm')
  .addEventListener('submit', async function (event) {
    event.preventDefault();


    const getFormData = () => {
      const formData = new FormData();
      const fields = [
        'jobTitle',
        'station',
        'dateField',
        'make',
        'model',
        'regNo',
        'mileage',
        'sltVendor',
        'vid',
        'additionalJob',
        'resultField',
      ];
      fields.forEach((field) =>
        formData.append(field, document.getElementById(field).value)
      );
      formData.set('vendorName', getDataTextById('sltVendor'));
      return formData;
    };

    const formData = getFormData();

    // Validate form data before proceeding
    if (!validateWorkOrderForm(Object.fromEntries(formData.entries()))) {
      handleLoader('none');
      return;
    }

    const workData = new FormData();
    workData.append('V_id', document.getElementById('vid').value);
    workData.append('vendor_id', document.getElementById('sltVendor').value);
    workData.append('service', document.getElementById('resultField').value);
    workData.append('jobtitle', getDataTextById('jobTitle'));
    workData.append('jobid', document.getElementById('jobTitle').value);
    workData.append('additionaljob', document.getElementById('additionalJob').value);
    workData.append('type', 'workorder');

    $.ajax({
      url: 'controller/workorder.php',
      type: 'POST',
      processData: false,
      contentType: false,
      data: workData,
      beforeSend: function () {
        handleLoader('flex');
      },
      success: function (data) {
        if (data.success) {
          showNotification('success', 'Work order request submitted to vendor.');
          document.getElementById('serviceOrderForm').reset();
        } else {
          showNotification('error', 'Failed to Create Work Order.');
        }
      },
      error: function (xhr, status, error) {
        console.error('Error in AJAX request:', error);
      },
      complete: function () {
        handleLoader('none');
      },
    });
  });


function validateJobForm(formData) {

  console.log(formData);
  const requiredFields = [
    { key: 'jobtitle', label: 'Job Title' },
    { key: 'V_id', label: 'Reg. No.' },
    { key: 'vendor_id', label: 'Vendor' },
    { key: 'description', label: 'Description' },
  ];

  for (const field of requiredFields) {
    if (!formData[field.key] || formData[field.key].trim() === '') {
      showNotification(
        'error',
        `Please fill the required field: ${field.label}`
      );
      return false;
    }
  }

  return true;
}

function getSelectedEmail(dropdownId) {
  const selectElement = document.getElementById(dropdownId);
  const selectedOption = selectElement.options[selectElement.selectedIndex];
  return selectedOption.getAttribute('data-email'); // Retrieve the data-email attribute
}

document
  .getElementById('serviceJobForm')
  .addEventListener('submit', async function (event) {
    event.preventDefault();
    const jobData = new FormData();
    jobData.append('jobtitle', document.getElementById('ctJobTitle').value);
    jobData.append('V_id', document.getElementById('ctvid').value);
    jobData.append('vendor_id', document.getElementById('ctsltVendor').value);
    jobData.append('description', document.getElementById('ctDesc').value);
    jobData.append('vendoremail', getSelectedEmail('ctsltVendor'));
    jobData.append('type', 'job');

    if (!validateJobForm(Object.fromEntries(jobData.entries()))) {
      handleLoader('none');
      return;
    }

    $.ajax({
      url: 'controller/workorder.php',
      type: 'POST',
      processData: false,
      contentType: false,
      data: jobData,
      beforeSend: function () {
        handleLoader('flex');
      },
      success: function (data) {
        if (data.success) {
          showNotification('success', 'Job request submitted to vendor.');
          document.getElementById('serviceJobForm').reset();
        } else {
          showNotification('error', 'Failed to Create Job.');
        }
      },
      error: function (xhr, status, error) {
        console.error('Error in AJAX request:', error);
      },
      complete: function () {
        handleLoader('none');
      },
    });
  });


function validateWorkOrderForm(formData) {
  const requiredFields = [
    { key: 'jobTitle', label: 'Job Title' },
    { key: 'station', label: 'Station' },
    { key: 'dateField', label: 'Date' },
    { key: 'make', label: 'Make' },
    { key: 'model', label: 'Model' },
    { key: 'regNo', label: 'Reg. No.' },
    { key: 'mileage', label: 'Mileage' },
    { key: 'sltVendor', label: 'Vendor' },
    { key: 'resultField', label: 'Services' },
  ];

  for (const field of requiredFields) {
    if (!formData[field.key] || formData[field.key].trim() === '') {
      showNotification(
        'error',
        `Please fill the required field: ${field.label}`
      );
      return false;
    }
  }

  return true;
}

