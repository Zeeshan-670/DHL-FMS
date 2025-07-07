document.addEventListener("DOMContentLoaded", function () {
  const today = new Date();
  const lastMonth = new Date();
  lastMonth.setMonth(lastMonth.getMonth() - 2);
  document.getElementById('fromDate').value = lastMonth.toISOString().split("T")[0];
  document.getElementById('toDate').value = today.toISOString().split("T")[0];
})
function fetchStats(start, end, vehicles) {
  const formData = new FormData();
  formData.append("type", "view");
  formData.append("starttime", `${start} 00:00:00`);
  formData.append("endtime", `${end} 23:59:59`);
  formData.append("platenumber", vehicles);

  $.ajax({
    url: "controller/statistics.php",
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    beforeSend: function () {
      handleLoader('flex');
    },
    success: function (data) {
      console.log(data);
      if (data.success) {
        function formatDate(dateStr) {
          const date = new Date(dateStr);
          const options = { year: 'numeric', month: 'short', day: '2-digit' };
          return date.toLocaleDateString('en-GB', options);  // Format: dd MMM yyyy
        }
        const distanceData = Object.keys(data.data.distance).map(date => {
          return {
            X: formatDate(date),  // Formatted date
            Y: data.data.distance[date]  // Distance
          };
        });
        const fuelData = Object.keys(data.data.fuel).map(date => {
          return {
            X: formatDate(date),  // Formatted date
            Y: data.data.fuel[date]  // Fuel
          };
        });

        const expenseData = Object.keys(data.data.expense).map(date => {
          return {
            X: formatDate(date),  // Formatted date
            Y: data.data.expense[date]  // Fuel
          };
        });
        createAreaChart("#distanceChart", distanceData, 'Distance', 'Km', '#4f5057');
        createAreaChart("#fuelChart", fuelData, 'Fuel', 'Ltr');
        // createAreaChart("#expenseChart", expenseData, 'Expense', 'Pkr');
        createBarChart("#expenseChart", 'Expense');
      }
    },
    complete: function () {
      handleLoader("none");
    },
  });
}
function createAreaChart(containerId, dataFromBackend, chartTitle, units, chartColor = '#4f5057') {
  if (window[containerId]) {
    window[containerId].destroy();
  }
  const seriesData = dataFromBackend.map((data) => {
    return {
      x: new Date(data.X),
      y: data.Y.toFixed()
    };
  });
  seriesData.sort((a, b) => new Date(a.x).getTime() - new Date(b.x).getTime());

  var options = {
    chart: {
      height: 300,
      type: "line", // Use line type to avoid unwanted fill
      id: containerId
    },
    series: [
      {
        name: chartTitle,
        data: seriesData,
        color: chartColor // Explicitly set line color
      }
    ],
    dataLabels: {
      enabled: false
    },
    xaxis: {
      type: "datetime",
      labels: {
        formatter: function (val, timestamp, opts) {
          return opts.dateFormatter(new Date(timestamp), "dd MMM yyyy");
        }
      }
    },
    fill: {
      opacity: 5
    },
    stroke: {
      curve: 'smooth',
      width: 1.5,
      colors: [chartColor],
    },

    tooltip: {
      enabled: true,
      x: {
        format: "dd MMM yyyy HH:mm"
      },
      y: {
        formatter: function (value) {
          return value + ` ${units}`;
        }
      },
      marker: {
        show: true,
        fillColors: [chartColor]
      }
    }
  };
  window[containerId] = new ApexCharts(document.querySelector(containerId), options);
  window[containerId].render();
}

function createBarChart(containerId, chartTitle) {
  if (window[containerId]) {
    window[containerId].destroy();
  }
  function getRandomData() {
    return Array.from({ length: 6 }, () => Math.floor(Math.random() * 100) + 10);
  }

  var categories = ['Week-1', 'Week-2', 'Week-3', 'Week-4', 'Week-5', 'Week-6', 'Week-7', 'Week-8'];
  var seriesData = [
    { name: 'Lubes', data: [44, 55, 41, 67, 22, 43, 27, 44], color: '#4CAF50' },
    { name: 'Documents', data: [13, 23, 20, 8, 13, 27, 23, 20], color: '#5cedbc' },
    { name: 'Services', data: [11, 17, 15, 15, 21, 14, 67, 22], color: '#ffdb69' },
    { name: 'Parts', data: [25, 30, 28, 35, 29, 31, 67, 22,], color: '#f18226' },
    { name: 'Transportation', data: [18, 22, 19, 24, 20, 23, 23, 20,], color: '#3b8ad9' },
    { name: 'Others', data: [32, 40, 36, 45, 38, 41, 23, 20,], color: '#7bc0f7' }
  ];

  var options = {
    series: seriesData.map(item => ({ name: item.name, data: item.data })),
    chart: {
      height: 300,
      type: "bar",
      id: containerId,
      stacked: true,
      stackType: '100%'
    },
    xaxis: {
      categories: categories,
    },
    colors: seriesData.map(item => item.color), // Assigning unique colors
    dataLabels: {
      enabled: false
    },
    tooltip: {
      enabled: true,
      y: {
        formatter: function (value) {
          return `${value} Pkr`;
        }
      }
    },
    plotOptions: {
      bar: {
        borderRadius: 4,
        horizontal: false,
        columnWidth: '50%'
      }
    }
  };

  // Initialize and render the new chart
  window[containerId] = new ApexCharts(document.querySelector(containerId), options);
  window[containerId].render();
}






function populateVehicle(vehicles) {
  const vehicleList = document.getElementById('vehicleList');
  vehicleList.innerHTML = ''; // Clear any previous vehicle list

  vehicles.forEach(vehicle => {
    const div = document.createElement('div');
    div.className = "form-check form-check-danger mx-2 mb-2"; // Match your given class

    const checkbox = document.createElement('input');
    checkbox.type = 'checkbox';
    checkbox.className = 'form-check-input';
    checkbox.name = 'vehicles';
    checkbox.value = vehicle.Reg;
    checkbox.id = `vehicle-${vehicle.V_id}`;
    checkbox.checked = true;

    const label = document.createElement('label');
    label.className = 'form-check-label';
    label.htmlFor = `vehicle-${vehicle.V_id}`;
    label.textContent = vehicle.Reg;

    div.appendChild(checkbox);
    div.appendChild(label);
    vehicleList.appendChild(div);
  });
}

function toggleSelectAll() {
  const checkboxes = document.querySelectorAll('input[name="vehicles"]');
  const selectAllCheckbox = document.getElementById('selectAll');
  const selectAllLabel = document.getElementById('selectAllLabel');

  checkboxes.forEach(checkbox => {
    checkbox.checked = selectAllCheckbox.checked;
  });

  // Update the label text dynamically
  selectAllLabel.textContent = selectAllCheckbox.checked ? "Unselect All" : "Select All";
}

document.getElementById('selectAll').addEventListener('change', toggleSelectAll);

document.addEventListener('change', function (event) {
  if (event.target.name === 'vehicles') {
    const checkboxes = document.querySelectorAll('input[name="vehicles"]');
    const selectAllCheckbox = document.getElementById('selectAll');
    const selectAllLabel = document.getElementById('selectAllLabel');

    selectAllCheckbox.checked = Array.from(checkboxes).every(checkbox => checkbox.checked);

    // Update label based on individual selections
    selectAllLabel.textContent = selectAllCheckbox.checked ? "Unselect All" : "Select All";
  }
});

document.getElementById('searchVehicle').addEventListener('input', function (event) {
  const searchTerm = event.target.value.toLowerCase();

  // Flatten the allVehicleList array if it's a 2D array
  const vehicles = allVehicleList.flat();

  const filteredVehicles = vehicles.filter(vehicle => {
    // Make sure vehicle.Reg exists and is a string before calling toLowerCase
    const reg = vehicle.Reg ? vehicle.Reg.toLowerCase() : '';
    return reg.includes(searchTerm);
  });

  populateVehicle(filteredVehicles);

});

document.getElementById('vehicleForm').addEventListener('submit', function (event) {
  event.preventDefault();
  const selectedVehicles = Array.from(document.querySelectorAll('input[name="vehicles"]:checked'))
    .map(checkbox => checkbox.value);
  fetchStats(document.getElementById('fromDate').value, document.getElementById('toDate').value, selectedVehicles.join())

});

$.ajax({
  url: "controller/getVehicleCount.php",
  type: "get",
  beforeSend: function () { },
  success: function (data) {
    console.log(data);
    if (data.success) {
      // console.log(data.data.TwoWheeler);
      document.getElementById("ttlVehicle").innerHTML = data.data.Total;
      document.getElementById("ttlMBVehicle").innerHTML = data.data.MB;
      document.getElementById("ttlMCVVehicle").innerHTML = data.data.MCV;
      document.getElementById("ttlSCVVehicle").innerHTML = data.data.SCV;
      document.getElementById("ttlLCVVehicle").innerHTML = data.data.LCV;
      document.getElementById("drivercount").innerHTML = data.data.drivercount.Total;
      document.getElementById("drivercountGOPS").innerHTML = data.data.drivercount.GOPS;
      document.getElementById("drivercountGTW").innerHTML = data.data.drivercount.GTW;
      document.getElementById("vendorcount").innerHTML = data.data.vendorcount;
    }
  },
});

// fetchStats();


// Get Vehicle
let allVehicleList = [];
function getVehicle(type, id) {
  const formData = new FormData();
  formData.append("type", "view");
  formData.append("vehicle", type);

  $.ajax({
    url: `controller/getvehicle.php`,
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    success: function (res) {
      handleLoader('flex')
      if (res.success) {
        console.log(res);
        if (type == 'all') {
          allVehicleList.push(res.data);
          populateVehicle(res.data);
          const selectedVehicles = Array.from(document.querySelectorAll('input[name="vehicles"]:checked'))
            .map(checkbox => checkbox.value);
          let plateNumber = selectedVehicles.join();
          fetchStats(document.getElementById('fromDate').value, document.getElementById('toDate').value, plateNumber)
        } else if (type == 'fourvehicle') {
          populateOptions(id, res.data, 'V_id', 'Reg', 'Select Vehicle');
        }
      }
    },
    error: function (xhr, status, error) {
      console.error("Error:", error);
    },
  });
}
getVehicle("fourvehicle", "addVMVehicleList");
getVehicle("all", "caVehicleAlert");

function getService() {
  const formData = new FormData();
  formData.append("type", "view");

  $.ajax({
    url: `controller/services.php`,
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    success: function (res) {
      if (res.success) {
        console.log(res);
        populateOptions(
          "addVMService",
          res.data,
          "servicesid",
          "servicesname",
          "Select Service"
        );
        document.getElementById("serviceTBody").innerHTML = "";
        res.data.map((el, i) => {
          document.getElementById(
            "serviceTBody"
          ).innerHTML += `<tr><td>${++i}</td><td>${el.servicesname}</td><td>${el.creationdate.split(" ")[0]
          }</td></tr>`;
        });
      }
    },
    error: function (xhr, status, error) {
      console.error("Error:", error);
    },
  });
}
// HAndle Alerts
let alertArray = [];
function getAlert() {
  const formData = new FormData();
  formData.append("type", "view");

  $.ajax({
    url: `controller/alert.php`,
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    beforeSend: function () {
      handleLoader("flex");
    },
    success: function (res) {
      if (res.success && res.data.length > 0) {
        let alertArray = res.data;

        let alertList = document.getElementById("eventList");
        alertList.innerHTML = ""; // Clear old alerts

        if (alertArray.length === 0) {
          document.getElementById("noDataMessage").style.display = "block";
          return;
        } else {
          document.getElementById("noDataMessage").style.display = "none";
        }
        alertArray.forEach((alert, index) => {
          let listItem = document.createElement("li");
          let formattedDate = moment(alert.entrydate).format("DD MMM");
          listItem.classList.add("event-list");

          listItem.innerHTML = `
                        <div class="event-timeline-dot">
                            <i class="bx ${index == 0
              ? "bxs-right-arrow-circle bx-fade-right"
              : "bx-right-arrow-circle"
            } font-size-18"></i>
                        </div>
                        <div class="d-flex">
                            <div class="flex-shrink-0 me-3">
                                <h5 class="font-size-14">${formattedDate} <i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></h5>
                            </div>
                            <div class="flex-grow-1">
                                <div>
                                    <a href="javascript:void(0);">
                                        <i class="${alert.title === "License Expiration"
              ? "mdi mdi-license"
              : alert.title ===
                "Vehicle Maintenance"
                ? "fas fa-car-crash"
                : "fas fa-exclamation-triangle"
            }"></i> ${alert.title}</a><br>

                                    <span>${alert.messages}</span>
                                </div>
                            </div>
                        </div>
                    `;
          alertList.appendChild(listItem);
        });
      }
    },
    error: function (xhr, status, error) {
      console.error("Error:", error);
    },
    complete: function () {
      handleLoader("none");
    },
  });
}


function filterTimeline() {
  let input = document.getElementById("searchInput").value.toLowerCase();
  let listItems = document.querySelectorAll(".event-list");
  let found = false;

  listItems.forEach(function (item) {
    let text = item.textContent.toLowerCase();
    if (text.includes(input)) {
      item.style.display = "block";
      found = true;
    } else {
      item.style.display = "none";
    }
  });

  document.getElementById("noDataMessage").style.display = found
    ? "none"
    : "block";
}

// Vehicle Maintenance
let checkVMApiCall = false;
function getVehicleMaintenance() {
  if (checkVMApiCall) return;
  const formData = new FormData();
  formData.append("type", "view");

  $.ajax({
    url: `controller/vehiclemaintenanceschedule.php`,
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    beforeSend: function () {
      handleLoader("flex");
    },
    success: function (res) {
      console.log("vehiclemaintenanceschedule:", res);
      if (res.success) {
        if ($.fn.DataTable.isDataTable("#vmTable")) {
          $("#vmTable").DataTable().destroy();
        }
        $("#vmTbody").empty();
        getService();

        dataObj = res.data;
        dataObj = dataObj.reverse();
        console.log(dataObj);

        // Append rows to the appropriate table based on status
        dataObj.forEach(function (el, i) {
          const rowHtml = `
                      <tr data-id="${i}-${el.vehicleid}">
                        <td data-field="Sno">${++i}</td>
                        <td data-field="vehicleId" style="display:none">${el.vehicleid
            }</td>
                        <td style="width: 100px">
                          <a class="btn btn-outline-success btn-sm" title="Edit" onclick="handleEdit('${el.V_id
            }','${el.platenumber}','${el.servicesname}','${el.setmileages
            }')">
                            <i class="fas fa-pencil-alt"></i>
                          </a>
                          <a class="btn btn-outline-danger btn-sm edit" onclick="handleDelete('${el.V_id
            }', '${el.platenumber}')" title="Delete">
                            <i class="fas fa-trash-alt"></i>
                          </a>
                        </td>
                        <td data-field="Reg">${el.platenumber}</td>
                        <td data-field="runningmileages">${el.runningmileages
            }</td>
                        <td data-field="setmileages">${el.setmileages}</td>
                        <td data-field="services">${el.servicesname}</td>
                        <td data-field="creationdate">${el.creationdate}</td>
                        <td data-field="doi">${el.creationby}</td>
                      </tr>
                    `;

          $("#vmTbody").append(rowHtml);
        });

        // Initialize DataTables for both tables
        $("#vmTable").DataTable({
          scrollX: true,
          scrollCollapse: true,
        });
      }
    },
    error: function (xhr, status, error) {
      console.error("Error:", error);
    },
    complete: function () {
      handleLoader("none");
      checkVMApiCall = true;
    },
  });
}
function openModal(firstModalName, secondModalName) {
  const myModal = new bootstrap.Modal(
    document.getElementById(secondModalName),
    {
      backdrop: "static",
      keyboard: false,
    }
  );
  myModal.show();

  if (secondModalName === 'addVehicleMaintenance') {
    // Ensure Select2 is properly initialized inside the modal
    $(document).ready(function () {
      if ($.fn.select2) {
        // Override Bootstrap's focus enforcement to allow typing in Select2
        $.fn.modal.Constructor.prototype._enforceFocus = function () { };

        $("#addVMVehicleList").select2({
          dropdownParent: $("#addVehicleMaintenance"), // Fixes dropdown positioning inside modal
          placeholder: "Select Vehicle",
          allowClear: false,
          minimumResultsForSearch: 0 // Always show search box
        });

        // Ensure search input is focusable when opening Select2
        $('#addVMVehicleList').on('select2:open', function () {
          setTimeout(() => $('.select2-search__field').focus(), 100);
        });
      } else {
        console.error("Select2 library not loaded.");
      }
    });
  }
}


document
  .getElementById("openAddServiceModal")
  .addEventListener("click", function () {
    openModal("vehicleMaintenance", "addService");
  });
document
  .getElementById("openaddVehicleMaintenance")
  .addEventListener("click", function () {
    openModal("vehicleMaintenance", "addVehicleMaintenance");
  });
document.getElementById("vmButton").addEventListener("click", function () {
  getVehicleMaintenance();
});
// Function to update the label when the range value changes
function updateRangeLabel(rangeId, labelId) {
  const rangeValue = document.getElementById(rangeId).value;
  const label = document.getElementById(labelId);
  label.textContent = "Km: " + rangeValue;
}

// Attach event listeners for both ranges
document
  .getElementById("editcustomRange")
  .addEventListener("input", function () {
    updateRangeLabel("editcustomRange", "editRangeLabel");
  });

document
  .getElementById("ededitcustomRange")
  .addEventListener("input", function () {
    updateRangeLabel("ededitcustomRange", "ededitRangeLabel");
  });

function resetVMFrom() {
  document.getElementById("editRangeLabel").innerHTML = "Km: 00";
  document.getElementById("addVehicleMaintenanceForm").reset();
}

// Function to handle the form submission
document
  .getElementById("addVehicleMaintenanceForm")
  .addEventListener("submit", function (e) {
    e.preventDefault();
    const vehicleSelect = document.getElementById("addVMVehicleList");
    const selectedVehicleValue = vehicleSelect.value.trim();
    const selectedVehicleText =
      vehicleSelect.options[vehicleSelect.selectedIndex].text;
    const serviceSelect = document.getElementById("addVMService");
    const selectedServiceValue = serviceSelect.value;
    const mileageValue = document.getElementById("editcustomRange").value;
    if (!selectedVehicleValue || !selectedServiceValue || mileageValue == 0) {
      showNotification("error", "All fields are required!");
      return;
    }

    const formData = new FormData();
    formData.append("vehicleid", selectedVehicleValue);
    formData.append("platenumber", selectedVehicleText);
    formData.append("servicesid", selectedServiceValue);
    formData.append("mileages", mileageValue);
    formData.append("type", "add");

    $.ajax({
      url: "controller/vehiclemaintenanceschedule.php",
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      beforeSend: function () {
        handleLoader("flex");
      },
      success: function (response) {
        console.log(response);
        if (response.success) {
          showNotification(
            "success",
            "Vehicle maintenance added successfully!"
          );
          resetVMFrom();
          checkVMApiCall = false;
          getVehicleMaintenance();
        } else {
          showNotification("error", "Failed to add vehicle maintenance.");
        }
      },
      error: function (xhr, status, error) {
        console.error("Error:", error);
        showNotification(
          "error",
          "An error occurred while submitting the form."
        );
      },
      complete: function () {
        handleLoader("none");
      },
    });
  });
document
  .getElementById("addServiceForm")
  .addEventListener("submit", function (e) {
    e.preventDefault();

    const serviceName = document.getElementById("serviceName").value.trim();
    if (!serviceName) {
      showNotification("error", "Service Name are required!");
      return;
    }

    const formData = new FormData();
    formData.append("servicesname", serviceName);
    formData.append("type", "add");

    $.ajax({
      url: "controller/services.php",
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      beforeSend: function () {
        handleLoader("flex");
      },
      success: function (response) {
        console.log(response);
        if (response.success) {
          showNotification("success", "Service add successfully!");
          document.getElementById("addServiceForm").reset();
          getService();
        } else {
          showNotification("error", "Failed to add Service.");
        }
      },
      error: function (xhr, status, error) {
        console.error("Error:", error);
        showNotification(
          "error",
          "An error occurred while submitting the form."
        );
      },
      complete: function () {
        handleLoader("none");
      },
    });
  });
document
  .getElementById("editVehicleMaintenanceForm")
  .addEventListener("submit", function (e) {
    e.preventDefault();

    if (document.getElementById("ededitcustomRange").value == 0) {
      showNotification("error", " mileage is require");
      return;
    }

    const formData = new FormData();
    formData.append("type", "update");
    formData.append(
      "vehiclemaintenanceid",
      document.getElementById("edVmVehicleId").value
    );
    formData.append(
      "mileages",
      document.getElementById("ededitcustomRange").value
    );

    $.ajax({
      url: "controller/vehiclemaintenanceschedule.php",
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      beforeSend: function () {
        handleLoader("flex");
      },
      success: function (response) {
        console.log(response);
        if (response.success) {
          showNotification(
            "success",
            "Vehicle maintenance added successfully!"
          );
          document.getElementById("editVehicleMaintenanceForm").reset();
          var myModal = bootstrap.Modal.getInstance(
            document.getElementById("editVehicleMaintenance")
          );
          myModal.hide();
          checkVMApiCall = false;
          getVehicleMaintenance();
        } else {
          showNotification("error", "Failed to add vehicle maintenance.");
        }
      },
      error: function (xhr, status, error) {
        console.error("Error:", error);
        showNotification(
          "error",
          "An error occurred while submitting the form."
        );
      },
      complete: function () {
        handleLoader("none");
      },
    });
  });

function handleEdit(vid, regno, servicename, mileage) {
  openModal("vehicleMaintenance", "editVehicleMaintenance");
  document.getElementById("edVmVehicleId").value = vid;
  document.getElementById("edVmVehicle").value = regno;
  document.getElementById("edVmService").value = servicename;
  document.getElementById("ededitcustomRange").value = mileage;
  document.getElementById("ededitRangeLabel").innerHTML = `Km: ${mileage}`;
}

function handleDelete(vid, reg) {
  Swal.fire({
    title: "Are you sure?",
    text: "You won't be able to revert this!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Yes, delete it!",
  }).then((result) => {
    if (result.isConfirmed) {
      const formData = new FormData();
      formData.append("vehiclemaintenanceid", vid);
      formData.append("type", "delete");

      $.ajax({
        url: `controller/vehiclemaintenanceschedule.php`,
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        beforeSend: function () {
          handleLoader("flex");
        },
        success: function (res) {
          if (res.success) {
            Swal.fire({
              title: "Deleted!",
              text: `Maintenance for ${reg} Deleted Successfully!`,
              icon: "success",
            });
            checkVMApiCall = false;
            getVehicleMaintenance();
          } else {
            Swal.fire({
              title: "Error!",
              text: "Something went wrong, unable to delete.",
              icon: "error",
            });
          }
        },
        error: function (xhr, status, error) {
          console.log("Error during form submission:", xhr.responseText);
          Swal.fire({
            title: "Error!",
            text: "An error occurred while processing your request.",
            icon: "error",
          });
        },
        complete: function () {
          handleLoader("none");
        },
      });
    }
  });
}
