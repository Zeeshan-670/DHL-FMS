// HAndle Alerts
let alertArray = [];
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
            if (res.success) {
                console.log(res);
                populateOptions(id, res.data, "V_id", "Reg", "Select Vehicle");
            }
        },
        error: function (xhr, status, error) {
            console.error("Error:", error);
        },
    });
}
getVehicle("all", "caVehicleAlert")

// Create Custom Alerts
document.addEventListener("DOMContentLoaded", function () {
    const alertRowsContainer = document.getElementById("alertRows");

    // Add New Alert Row
    document.addEventListener("click", function (event) {
        if (event.target.closest(".add-row")) {
            let alertRowCount = document.querySelectorAll(".alert-row").length;
            if (alertRowCount >= 5) {
                showNotification("error", "You can only add up to 5 alerts.");
                return;
            }

            const newRow = document.createElement("div");
            newRow.classList.add("alert-row", "d-flex", "mb-2");
            newRow.innerHTML = `
                  <select class="form-select me-2 alert-type" required>
                      <option value="">Select Alert Type</option>
                      <option value="Fire Extinguisher">Fire Extinguisher</option>
                      <option value="First Aid Box">First Aid Box</option>
                      <option value="Fitness Certificate">Fitness Certificate</option>
                  </select>
                  <input type="date" class="form-control me-2 alert-date" required>
                  <button type="button" class="btn btn-primary remove-row"><i class="fas fa-minus"></i></button>
              `;
            alertRowsContainer.appendChild(newRow);
        }

        // Remove Alert Row
        if (event.target.closest(".remove-row")) {
            event.target.closest(".alert-row").remove();
        }
    });

    // Form Submission
    document
        .getElementById("customAlertForm")
        .addEventListener("submit", function (event) {
            event.preventDefault(); // Prevent form submission

            let vehicle = document.getElementById("caVehicleAlert").value;
            if (!vehicle) {
                showNotification("error", "Please select a vehicle.");
                return;
            }

            let alertData = {
                vehicle: "12", // Assuming a fixed value or retrieve dynamically
                type: "add",
                alerts: [],
            };
            let valid = true;

            document.querySelectorAll(".alert-row").forEach((row) => {
                let alerttype = row.querySelector(".alert-type").value;
                let date = row.querySelector(".alert-date").value;

                if (!alerttype || !date) {
                    valid = false;
                } else {
                    alertData.alerts.push({ alerttype, date });
                }
            });

            // document.querySelectorAll(".alert-row").forEach(row => {
            //     let alerttype = row.querySelector(".alert-type").value;
            //     let date = row.querySelector(".alert-date").value;

            //     if (!alerttype || !date) {
            //         valid = false;
            //     }
            //     alertData.push('vehicle', vehicle);
            //     alertData.push('type', add);

            //     alertData.push({ alerttype, date });
            // });

            if (!valid) {
                showNotification("error", "All fields are required.");
                return;
            }

            console.log({ vehicle, alerts: alertData });

            $.ajax({
                url: "controller/customalert.php",
                type: "POST",
                contentType: "application/json",
                data: JSON.stringify(alertData),
                beforeSend: function () {
                    handleLoader("flex");
                },
                success: function (response) {
                    console.log(response);
                    if (response.success) {
                        showNotification("success", "Custom Alert add successfully!");
                        document.getElementById("customAlertForm").reset();
                        var myModal = bootstrap.Modal.getInstance(
                            document.getElementById("createCustomAlerts")
                        );
                        myModal.hide();
                        alertRowsContainer.innerHTML = `
              <div class="alert-row d-flex mb-2">
                  <select class="form-select me-2 alert-type" required>
                      <option value="">Select Alert Type</option>
                      <option value="Fire Extinguisher">Fire Extinguisher</option>
                      <option value="First Aid Box">First Aid Box</option>
                      <option value="Fitness Certificate">Fitness Certificate</option>
                  </select>
                  <input type="date" class="form-control me-2 alert-date" required>
                  <button type="button" class="btn btn-success add-row"><i class="fas fa-plus"></i></button>
              </div>
          `;
                    } else {
                        showNotification("error", "Failed to add Custom Alert.");
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

            // alert("Custom Alert Created Successfully!");

            // Close Modal
            // var myModal = bootstrap.Modal.getInstance(document.getElementById('createCustomAlerts'));
            // myModal.hide();

            // Reset Form
        });
});

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
            console.log("License Response:");
            if (res.success) {
                createAlertButtons(res.data)
                console.log(res.data);
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

// Initial Call
getAlert();


function createAlertButtons(data) {
    const buttonsContainer = document.getElementById('buttons-container');
    const modalBody = document.getElementById('modal-body-content');

    if (!buttonsContainer || !modalBody) {
        console.error("Modal body or button container not found.");
        return;
    }

    buttonsContainer.innerHTML = ""; // Clear previous cards

    for (const key in data) {
        if (data.hasOwnProperty(key)) {
            // 🔥 **Set Icons Based on Alert Type** 🔥
            let iconClass = data[key]['icon'];
            // if (key === "License Expiration") {
            //     iconClass = "mdi mdi-license"; // License Icon
            // } else if (key === "First Aid Box") {
            //     iconClass = "fas fa-medkit"; // First Aid Icon
            // } else if (key === "Fire Extinguisher") {
            //     iconClass = "fas fa-fire-extinguisher"; // Fire Extinguisher Icon
            // } else if (key === "Fitness Certificate") {
            //     iconClass = "fas fa-file-medical"; // Certificate Icon
            // } else if (key === "Fitness Certificate") {
            //     iconClass = "fas fa-file-medical"; // Certificate Icon
            // } else {
            //     iconClass = "fas fa-exclamation-circle"; // Default Icon
            // }

            // 🏆 **Create Alert Card**
            const card = document.createElement('div');
            card.className = "col-md-6 col-lg-3";

            console.log(data[key].length);// Responsive grid

            card.innerHTML = `
                <div class="card mini-stats-wid alert-card ${data[key]['data'].length == 0 ? '' : 'blinking-effect'}  mt-4" data-key="${key}">
                    <div class="card-body my-3">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <p class="text-muted fw-bold text-uppercase mb-1">${key}</p>
                                <h4 class="mb-0"><span class="text-primary">${data[key]['data'].length}</span> ${data[key]['data'].length > 1 ? 'upcoming alerts' : 'upcoming alert'}</h4>
                            </div>
                            <div class="flex-shrink-0 align-self-center">
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-light">
                                    <span class="avatar-title">
                                        <i class="${iconClass} ${data[key]['data'].length == 0 ? '' : 'bx-tada'}  font-size-24"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;




            // 📌 **Click Event for Modal**
            card.addEventListener('click', () => {
                modalBody.innerHTML = ''; // Clear previous content

                document.getElementById('modalHeading').innerHTML = ""
                document.getElementById('modalHeading').innerHTML = `
                    <i class="${iconClass} font-size-18 me-1"></i> ${key}`
                // Create list container
                const alertList = document.createElement('ul');
                alertList.className = 'verti-timeline list-unstyled';
                alertList.id = 'modalEventList'; // ID for filtering

                if (data[key]['data'].length === 0) {
                    modalBody.innerHTML = `<p class="no-data text-center"><a href="javascript:void(0);"><i class="fas fa-exclamation-circle"></i> No Alert Found!</a></p>`;
                } else {
                    data[key]['data'].forEach((alert, index) => {
                        let listItem = document.createElement("li");
                        let formattedDate = moment(alert.entrydate).format("DD MMM");
                        listItem.classList.add("event-list");

                        listItem.innerHTML = `
                            <div class="event-timeline-dot">
                                <i class="bx ${index === 0 ? "bxs-right-arrow-circle bx-fade-right" : "bx-right-arrow-circle"} font-size-18"></i>
                            </div>
                            <div class="d-flex" style="border-bottom: 1px solid #5151511c;padding-bottom: 20px;">
                                <div class="flex-shrink-0 me-3">
                                    <h5 class="font-size-14">${formattedDate} <i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></h5>
                                </div>
                                <div class="flex-grow-1">
                                    <div>
                                        <a href="javascript:void(0);">
                                            <i class="${iconClass}"></i> ${key}
                                        </a><br>
                                        <span>${alert.messages}</span>
                                    </div>
                                </div>
                            </div>
                        `;
                        alertList.appendChild(listItem);
                    });
                }

                modalBody.appendChild(alertList);

                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('dataModal'));
                modal.show();
            });

            // Append card to container
            buttonsContainer.appendChild(card);
        }
    }
}

// Filter function for alerts inside the modal
function filterModalAlerts() {
    let input = document.getElementById("modalSearchInput").value.toLowerCase();
    let eventList = document.getElementById("modalEventList");
    let items = eventList ? eventList.getElementsByClassName("event-list") : [];

    let found = false;
    for (let i = 0; i < items.length; i++) {
        let text = items[i].innerText.toLowerCase();
        if (text.includes(input)) {
            items[i].style.display = "";
            found = true;
        } else {
            items[i].style.display = "none";
        }
    }

    document.getElementById("noDataMessage").style.display = found ? "none" : "block";
}


// Filter Alerts

// function filterTimeline() {
//     let input = document.getElementById("searchInput").value.toLowerCase();
//     let listItems = document.querySelectorAll(".event-list");
//     let found = false;

//     listItems.forEach(function (item) {
//         let text = item.textContent.toLowerCase();
//         if (text.includes(input)) {
//             item.style.display = "block";
//             found = true;
//         } else {
//             item.style.display = "none";
//         }
//     });

//     document.getElementById("noDataMessage").style.display = found
//         ? "none"
//         : "block";
// }
