
$(document).ready(function () {
    async function handleVehicleSearch(e) {
        e.preventDefault();
        const vehicleNo = document.getElementById('searchVehicle').value.trim();
        if (!vehicleNo) {
            showNotification('error', 'Please enter a vehicle number.');
            return;
        }
        const formData = new FormData();
        formData.append('Reg', vehicleNo);

        $.ajax({
            url: `controller/searchVehicle.php`,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function () {
                handleLoader("flex");
            },
            success: function (res) {
                console.log(res);
                if (res.success && res.data) {
                    showNotification('success', 'Vehicle found Successfully')
                    document.getElementById('exRegNo').value = res.data['vehicle_details']['Reg'];
                    document.getElementById('exVid').value = res.data['vehicle_details']['V_id'];
                    document.getElementById('exEngine').value = res.data['vehicle_details']['engine'];
                    document.getElementById('exChasis').value = res.data['vehicle_details']['chassis'];
                    document.getElementById('exBrand').value = res.data['vehicle_details']['makename'];
                    document.getElementById('exModel').value = res.data['vehicle_details']['modelname'];
                    document.getElementById('exStation').value = res.data['vehicle_details']['stationname'];
                    document.getElementById('exLocation').value = res.data['vehicle_details']['name'];
                    document.getElementById('exMileage').value = res.data['vehicle_details']['Milage'];
                    document.getElementById('exDepartment').value = 'Operations';
                } else {
                    showNotification('error', 'No vehicle found with the given number.');
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
    initVehicleSearch("#searchVehicle", "#searchVehicleForm", handleVehicleSearch);


});

let expenseData;
let taxData;
function getExpenseCategory() {
    $.ajax({
        url: `controller/getExpenseCategory.php`,
        type: "POST",
        success: function (res) {
            console.log(res);
            if (res.success && res.data) {
                const selectBox = document.getElementById("expenseCategory");
                expenseData = res.data;
                // Populate the select box with the expense data
                res.data.forEach(item => {
                    const option = document.createElement("option");
                    option.value = item.ExpId;
                    option.textContent = item.Expense;
                    selectBox.appendChild(option);
                });
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
                const selectBoxes = document.querySelectorAll(".tax-select"); // Select all elements with class "tax-select"

                taxData = res.data.sort((a, b) => parseFloat(a.value) - parseFloat(b.value));

                selectBoxes.forEach(selectBox => {
                    selectBox.innerHTML = ""; // Clear existing options

                    taxData.forEach(item => {
                        const option = document.createElement("option");
                        option.value = item.id;
                        option.textContent = item.value;
                        selectBox.appendChild(option);
                    });
                });
            }
        },
        error: function (xhr, status, error) {
            console.error("Error:", error);
        },
    });
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
                populateOptions('exSltVendor', res.data, 'vendorid', 'vendorname', 'Select Vendor');
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


populateDataVendor();
getTaxData();
getExpenseCategory();

document.addEventListener("DOMContentLoaded", function () {
    const expenseRowsContainer = document.getElementById("expenseRows");

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
                <div class="col-md-4">
                    <select class="form-select me-2 alert-type category-select" required>
                        <option value="">Select Category</option>
                    </select>
                </div>
                <div class="col-md-2 d-none">
                    <input type="number" step="0.01" min="0" value="0" class="form-control" placeholder="Enter Amount">
                </div>
                <div class="col-md-2 d-none">
                    <div class="input-group">
                        <select class="form-select tax-select">
                        </select>
                        <button type="button" class="btn btn-primary docs-datepicker-trigger">%</button>
                    </div>
                </div>
                <div class="col-md-7">
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

            expenseRowsContainer.appendChild(newRow);
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

document.getElementById("rfqForm").addEventListener("submit", function (event) {
    event.preventDefault();
    function validateField(fieldId, message) {
        const field = document.getElementById(fieldId);
        if (!field.value.trim()) {
            showNotification("error", message);
            field.focus();
            return false;
        }
        return true;
    }

    if (!validateField("exJobTitle", "Job Title is required")) return;
    if (!validateField("exRegNo", "Reg. No. is required")) return;
    if (!validateField("exSltVendor", "Vendor is required")) return;
    if (!validateField("exEngine", "Engine is required")) return;
    if (!validateField("exChasis", "Chasis is required")) return;
    if (!validateField("exBrand", "Brand/Make is required")) return;
    if (!validateField("exModel", "Model is required")) return;
    if (!validateField("exStation", "Station is required")) return;
    if (!validateField("exLocation", "Location is required")) return;
    if (!validateField("exDepartment", "Department is required")) return;

    const expenseRows = document.querySelectorAll(".alert-row");
    if (expenseRows.length === 0) {
        showNotification("error", "At least one expense entry is required.");
        return;
    }

    const expenses = [];
    let isValid = true;

    expenseRows.forEach((row, index) => {
        const expenseCategory = row.querySelector(".category-select");
        const amount = row.querySelector("input[type='number']");
        const tax = row.querySelector(".tax-select");
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
        expenses.push({
            expenseCategory: expenseCategory.value,
            amount: parseFloat(amount.value),
            tax: tax.value, // Add tax value to payload
            description: description.value.trim()
        });
    });

    if (!isValid) return;

    const payload = {
        type: 'rfq',
        V_id: document.getElementById("exVid").value,
        vendor_id: document.getElementById("exSltVendor").value,
        jobtitle: document.getElementById("exJobTitle").value,
        description: document.getElementById("exDesc").value,
        expenses: expenses,
    };

    console.log(payload);


    $.ajax({
        url: `controller/rfq.php`,
        type: "POST",
        contentType: "application/json",
        data: JSON.stringify(payload),
        beforeSend: function () { handleLoader("flex"); },
        success: function (res) {
            if (res.success) {
                showNotification("success", res.message);
                // Clear form fields\

                document.getElementById("searchVehicle").value = "";
                document.getElementById("rfqForm").reset();
                // setTimeout(() => {
                //     // window.location.reload()
                // }, 500);
            } else {
                showNotification("error", res.message);
            }
        },
        complete: function () { handleLoader("none"); },
    });
});
