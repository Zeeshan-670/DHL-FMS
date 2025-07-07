function getDataById(id, placeHolder = '-') {
  const element = document.getElementById(id);
  if (element) {
    const value = element.value.trim();
    return value !== '' ? value : placeHolder;
  } else {
    return placeHolder;
  }
}
//  Set Data by ID
function setDataById(id, val, placeHolder = '-') {
  const element = document.getElementById(id);
  return element ? (element.value = val || placeHolder) : placeHolder;
}

function getDataTextById(id) {
  const element = document.getElementById(id);
  return element ? element.options[element.selectedIndex].text : '';
}

function getSelectedValue(elementId) {
  var sel = document.getElementById(elementId);
  if (sel != null) {
    if (typeof sel.options[sel.selectedIndex] != 'undefined') {
      var value = sel.options[sel.selectedIndex].value;
      return value;
    } else {
      return '';
    }
  }
  return '';
}

function encodePassword(password) {
  return btoa(password);
}

function decodePassword(encodedPassword) {
  return atob(encodedPassword);
}

function toggleFunction(id) {
  var x = document.getElementById(id);
  x.type = x.type === 'password' ? 'text' : 'password';
}


function validatePassword(passwordInput) {
  const password = passwordInput.value;
  let errorMessage = "";

  if (!/(?=.*\d)/.test(password)) {
    errorMessage += "- At least one number\n";
  }
  if (!/(?=.*[a-z])/.test(password)) {
    errorMessage += "- At least one lowercase letter\n";
  }
  if (!/(?=.*[A-Z])/.test(password)) {
    errorMessage += "- At least one uppercase letter\n";
  }
  if (!/(?=.*[\W_])/.test(password)) {
    errorMessage += "- At least one special character\n";
  }
  if (password.length < 8) {
    errorMessage += "- Minimum 8 characters in length\n";
  }

  if (errorMessage === "") {
    passwordInput.title = "Password is strong.";
  } else {
    passwordInput.title = errorMessage.trim();
  }
}



function handleLoader(des) {
  const loader = document.getElementById('loader-container');

  if (!loader) {
    console.error("Loader container not found!");
    return;
  }

  loader.style.display = des;

  if (des === 'flex') {
    loader.style.position = 'fixed';
    loader.style.top = '0';
    loader.style.left = '0';
    loader.style.width = '100vw';
    loader.style.height = '100vh';
    loader.style.background = 'rgba(0, 0, 0, 0.7)'; // Dark transparent overlay
    loader.style.justifyContent = 'center';
    loader.style.alignItems = 'center';
    loader.style.zIndex = '99990000'; // Ensure it's on top
    document.body.style.overflow = 'hidden';
  } else {
    document.body.style.overflow = 'auto';
  }
}


function clearForm(formID) {
  $(`#${formID}`)
    .find(':input')
    .each(function () {
      switch (this.tagName.toLowerCase()) {
        case 'input':
          switch (this.type) {
            case 'password':
            case 'text':
            case 'file':
            case 'date':
            case 'datetime-local':
            case 'datetime':
            case 'number':
            case 'tel':
            case 'email':
              jQuery(this).val('');
              break;
            case 'checkbox':
            case 'radio':
              this.checked = false;
              break;
          }
          break;
        case 'textarea':
          jQuery(this).val('');
          break;
        case 'select':
          // console.log(this);
          jQuery(this).val('0');
          break;
      }
    });
}

function populateDataFromArray(id, data) {
  let selectBox = document.getElementById(id); // Get the select element itself
  selectBox.innerHTML = ''; // Clear any existing options
  selectBox.innerHTML = '<option value="">-- Select Access--</option>'; // Add the default option

  data.forEach((el) => {
    selectBox.innerHTML += `<option value="${el}">${el}</option>`;
  });
}
function populateOptions(id, data, val, text, defaultText) {
  var select = document.getElementById(id);
  select.innerHTML = '';
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
        fragment.appendChild(option);
      }
    }
  });
  select.appendChild(fragment);
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
      if (res.success) {
        populateOptions(id, res.data, val, key, defaultText);
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



function redirectToPdf(url) {
  window.open(`pdf/${url}.php`, '_blank');
}

function populateOptions(id, data, val, text, defaultText) {
  var select = document.getElementById(id);
  select.innerHTML = '';
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
        fragment.appendChild(option);
      }
    }
  });
  select.appendChild(fragment);
}
function showNotification(type, message) {
  Lobibox.notify(type, {
    pauseDelayOnHover: true,
    size: 'mini',
    lines: 5,
    rounded: true,
    icon: type === 'success' ? 'mdi mdi-check-circle' : 'mdi mdi-close-circle',
    delayIndicator: true,
    // delay: 10000000,
    continueDelayOnInactiveTab: false,
    position: 'top right',
    msg: message,
    sound: false,
  });
}
function capitalizeFirstLetter(str) {
  if (str && str.length > 0) {
    return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
  }
  return str;
}

document.addEventListener("DOMContentLoaded", () => {
  document.addEventListener("keydown", (event) => {
    const target = event.target;
    if ((target.tagName === "INPUT" || target.tagName === "TEXTAREA") && event.key === "Enter") {
      event.preventDefault();
    }
  });
});

function adjustTable(tableId) {
  const table = $(`#${tableId}`).DataTable();
  table.columns.adjust().draw(); // Adjust column widths and redraw
  if (table.responsive) {
    table.responsive.recalc(); // Recalculate if responsive extension is used
  }
}
function initializeDataTable(tableId, data, excel = false) {
  // Check if DataTable is already initialized, and destroy it before reinitializing
  if ($.fn.DataTable.isDataTable(`#${tableId}`)) {
    $(`#${tableId}`).DataTable().clear().destroy();
  }

  // Clear any existing rows and insert the new data into the table body
  let tableBody = document.getElementById(`${tableId}`).querySelector('tbody');
  tableBody.innerHTML = ''; // Clear existing rows to prevent stacking
  tableBody.insertAdjacentHTML('beforeend', data);

  // Initialize DataTable with options
  let tableOptions = {
    scrollY: '500px',
    scrollX: true,
    scrollCollapse: true,
    pageLength: 10,
  };

  // If Excel export is required, add the buttons configuration
  if (excel) {
    tableOptions.dom = 'Bfrtip';  // Add buttons to the DataTable toolbar
    tableOptions.buttons = [
      {
        extend: 'excelHtml5',
        text: '<i class="bx bx-spreadsheet" style="font-size:15px"></i> Download Excel',
        title: 'Expense Histroy',
        className: 'btn btn-primary',
      },
    ];
  }

  // Initialize the DataTable with the prepared options
  $(`#${tableId}`).DataTable(tableOptions);

  // Adjust table layout on sidebar toggle and window resize
  $('#sidebarToogle').click(function () {
    adjustTable(tableId);
  });
  $(window).resize(function () {
    adjustTable(tableId);
  });
}


