$(document).ready(function () {
  $("#driverCategory").select2({
    placeholder: "Select Category",
    allowClear: true,
    closeOnSelect: false, // Ensures dropdown closes after selection
  });
  $("#eddriverCategory").select2({
    placeholder: "Select Category",
    allowClear: true,
    closeOnSelect: false, // Ensures dropdown closes after selection
  });
});

$("#cnic").inputmask();

function getDriverDetail() {
  const formData = new FormData();
  formData.append("type", "view");

  $.ajax({
    url: `controller/driver.php`,
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    beforeSend: function () {
      handleLoader("flex");
    },
    success: function (res) {
      console.log("diver:", res);
      if (res.success) {
        if ($.fn.DataTable.isDataTable("#driverDetail")) {
          $("#driverDetail").DataTable().destroy();
        }
        $("#diverTbody").empty();

        dataObj = res.data;
        //   dataObj = dataObj.reverse();
        console.log(dataObj);

        dataObj.forEach(function (el, i) {
          const rowHtml = `
                        <tr data-id="${i}-${el.d_id}">
                          <td data-field="Sno">${++i}</td>
                          <td data-field="driverId" style="display:none">${
                            el.d_id
                          }</td>
                          <td style="width: 100px">
                            <a class="btn btn-outline-success btn-sm" title="Edit" 
                                onclick="handleEdit('${el.d_id}','${el.driver_name}','${el.cnic}','${el.ltv}','${el.license_no}','${el.category}','${el.validity}')">
                              <i class="fas fa-pencil-alt"></i>
                            </a>
                            <a class="btn btn-outline-danger btn-sm edit"
                            onclick="handleDelete('${ el.d_id }', '${el.driver_name}')" title="Delete">
                              <i class="fas fa-trash-alt"></i>
                            </a>
                          </td>
                          <td data-field="driverName">${el.driver_name}</td>
                          <td data-field="cnic">${el.cnic}</td>
                          <td data-field="ltv">${el.ltv}</td>
                          <td data-field="licenseNo">${el.license_no}</td>
                          <td data-field="category">${el.category}</td>
                          <td data-field="validity">${el.validity}</td>
                        </tr>
                      `;

          $("#diverTbody").append(rowHtml);
        });

        // Initialize DataTables for both tables
        $("#driverDetail").DataTable({
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
    },
  });
}

getDriverDetail();

document.getElementById("driverForm").addEventListener("submit", function (e) {
  e.preventDefault();

  // Get input values
  const driverId = document.getElementById("driverid").value.trim();
  const driverName = document.getElementById("driverName").value.trim();
  const driverCnic = document.getElementById("cnic").value.trim();
  const driverLicenseType = document.getElementById("LicenseType").value.trim();
  const driverlicenseNo = document.getElementById("licenseNo").value.trim();
  const driverCategory = $("#driverCategory").val(); // Get Select2 multi-value
  const drivervalidity = document.getElementById("validity").value.trim();
  // Validate fields
  let errors = {};

  function validateField(fieldName, condition, errorMessage) {
    if (condition && !errors[fieldName]) {
      errors[fieldName] = errorMessage;
    }
  }

  // Perform validation
  validateField(
    "driverid",
    driverId === "" || isNaN(driverId),
    "Driver ID is required."
  );
  validateField("driverName", driverName === "", "Driver Name is required.");
  validateField(
    "cnic",
    !driverCnic.match(/^\d{5}-\d{7}-\d$/),
    "Enter a valid CNIC (XXXXX-XXXXXXX-X)."
  );
  validateField(
    "LicenseType",
    driverLicenseType === "",
    "Please select an LTV option."
  );
  validateField("licenseNo", driverlicenseNo === "", "License No is required.");
  validateField(
    "driverCategory",
    driverCategory.length === 0,
    "Please select at least one category."
  );
  validateField(
    "validity",
    drivervalidity === "",
    "Please select a validity date."
  );

  // If there are errors, show only the **first** error for each field
  if (Object.keys(errors).length > 0) {
    let firstErrorMessage = Object.values(errors)[0]; // Get the first error
    showNotification("error", firstErrorMessage);
    return;
  }

  // Convert Select2 multiple values into a comma-separated string
  const driverCategoryStr = driverCategory.join(",");

  // Create FormData object
  const formData = new FormData();
  formData.append("type", "add");
  formData.append("driverid", driverId);
  formData.append("driver_name", driverName);
  formData.append("cnic", driverCnic);
  formData.append("ltv", driverLicenseType);
  formData.append("license_no", driverlicenseNo);
  formData.append("category", driverCategoryStr);
  formData.append("validity", drivervalidity);

  // AJAX request
  $.ajax({
    url: "controller/driver.php",
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
        showNotification("success", "Driver added successfully!");
        getDriverDetail();
        $("#driverForm")[0].reset(); // Reset form
        $("#driverCategory").val(null).trigger("change"); // Reset Select2
        var myModal = bootstrap.Modal.getInstance(
          document.getElementById("addDriver")
        );
        myModal.hide();
      } else {
        showNotification("error", "Failed to add driver.");
      }
    },
    error: function (xhr, status, error) {
      console.error("Error:", error);
      showNotification("error", "An error occurred while submitting the form.");
    },
    complete: function () {
      handleLoader("none");
    },
  });
});

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



function handleEdit(did, dname, dcnic, dltv, dlicenseNo, dcategory, dvalidity) {
  const myModal = new bootstrap.Modal(
    document.getElementById('editDriver'),
    {
      backdrop: "static",
      keyboard: false,
    }
  );
  myModal.show();
  // Split categories and set multi-select values
  const dcat = dcategory.split(',');

  // Use jQuery to set values for the inputs and select elements
  $("#eddriverid").val(did);
  $("#eddriverName").val(dname);
  $("#edcnic").val(dcnic);
  $("#edLicenseType").val(dltv);
  $("#edlicenseNo").val(dlicenseNo);
  $("#eddriverCategory").val(dcat).trigger('change'); // Use .trigger('change') to update the select2
  $("#edvalidity").val(dvalidity);
}
