$(document).ready(function () {
    $("#vehicleSelect").select2({
        placeholder: "Select Vehicle",
        allowClear: false,
    });


    function getVehicleAll() {
        const formData = new FormData();
        formData.append("type", "view");
        formData.append("vehicle", "all");

        $.ajax({
            url: `controller/getvehicle.php`,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function () {
                handleLoader("flex");
            },
            success: function (res) {
                if (res.success && res.data) {
                    res.data.unshift({
                        "V_id": "all",
                        "Reg": "Select All"
                    });
                    document.getElementById('vehicleSelect').value = ""
                    populateOptions('vehicleSelect', res.data, 'Reg', 'Reg', 'Select Vehicle')
                    // res.data.map((el, i) => {
                    //     $("#vehicleSelect").append(`<option value="${el.Reg}">${el.Reg}</option>`);
                    // })
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

    function calculateAmountWithoutTax(totalWithTax, salesTaxValue) {
        const amountWithoutTax = (Number(totalWithTax) / (1 + Number(salesTaxValue) / 100)).toFixed(2);
        return amountWithoutTax;
    }

    getVehicleAll();


    // Function to handle form submission
    document.getElementById("getHistory").addEventListener("submit", function (e) {
        e.preventDefault(); // Prevent default form submission

        const selectedVehicles = $("#vehicleSelect").val();
        const startDate = document.getElementById("startDate").value;
        const endDate = document.getElementById("EndDate").value;
        const filterBy = document.getElementById("filterBy").value;

        // Validate fields
        if (!selectedVehicles || selectedVehicles.length === 0) {
            showNotification("error", "Please select vehicle.");
            return;
        }
        if (!startDate) {
            showNotification("error", "Start date is required.");
            return;
        }
        if (!endDate) {
            showNotification("error", "End date is required.");
            return;
        }
        if (!filterBy) {
            showNotification("error", "Please select a filter.");
            return;
        }

        // Prepare form data
        const formData = new FormData();
        formData.append("reg", selectedVehicles);
        formData.append("start", startDate);
        formData.append("end", endDate);
        formData.append("filter", filterBy);

        $.ajax({
            url: `controller/filterOldExpense.php`,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function () {
                handleLoader("flex");
            },
            success: function (res) {
                let row = "";
                let grandTotal = 0;

                if ($.fn.DataTable.isDataTable("#expenseHistroyTable")) {
                    $("#expenseHistroyTable").DataTable().destroy();
                }

                $("#expenseHistroyTable").empty();

                let tableHeaders = `<tr>`;
                let totalColumnIndex = 4; // Default column index for total amount

                if (filterBy === "new") {
                    tableHeaders += `
                    <th>S.NO</th>
                    <th>Date</th>
                    <th>Reg No</th>
                    <th>Station</th>
                    <th>Vendor</th>
                    <th>Expense</th>
                    <th>Expense Description</th>
                    <th>Amount</th>
                    <th>Sales Tax</th>
                    <th>Total Amount</th>
                    <th>Created by</th>`;
                    totalColumnIndex = 9;
                } else {
                    tableHeaders += `
                    <th>S.No</th>
                    <th style="min-width: 90px;">Expense Date</th>
                    <th style="min-width: 90px;">Reg No</th>
                    <th>Station</th>
                    <th>Amount</th>
                    <th>Expense Description</th>`;
                    totalColumnIndex = 4;
                }
                tableHeaders += `</tr>`;

                $("#expenseHistroyTable").append(`
                    <thead>${tableHeaders}</thead>
                    <tbody id="expenseHistroyTbody"></tbody>
                    <tfoot><tr><td colspan="${totalColumnIndex}" style="font-weight:bold;">Grand Total:</td><td id="grandTotal" style="font-weight:bold;">0</td><td></td></tr></tfoot>
                `);

                if (res.success) {
                    showNotification("success", res.message || "Data Found");
                    document.getElementById("historyTableContainer").style.display = "block";
                    let dataObj = res.data;
                    let count = 0;

                    if (filterBy === "new") {
                        for (const key in dataObj) {
                            const invoice = dataObj[key];
                            const details = invoice.details;

                            details.forEach((el, i) => {
                                const amountWithoutTax = calculateAmountWithoutTax(el['Amount'], el['salesTaxValue']);
                                row += `<tr>`;
                                row += `
                                <td>${++count}</td>
                                <td>${invoice.CreationDate}</td>
                                <td>${invoice.Reg}</td>
                                <td>${invoice.StationName}</td>
                                <td>${invoice.vendorname || '-'}</td>
                                <td>${el.ExpenseCategory}</td>
                                <td>${el.Description}</td>
                                <td>${amountWithoutTax}</td>
                                <td>${el.salesTax}%</td>
                                <td>${el.Amount}</td>
                                <td>${capitalizeFirstLetter(el.CreatedBy)}</td>`;
                                row += `</tr>`;
                                grandTotal += parseFloat(el.Amount) || 0;
                            });
                        }
                    } else {
                        dataObj.forEach((el, i) => {
                            row += `<tr>`;
                            row += `
                            <td>${i + 1}</td>
                            <td>${el.expenseDate}</td>
                            <td>${el.reg}</td>
                            <td>${el.stationname}</td>
                            <td>${el.expenseAmount}</td>
                            <td>${el.expenseDescription}</td>`;
                            row += `</tr>`;
                            grandTotal += parseFloat(el.expenseAmount) || 0;
                        });
                    }

                    $("#expenseHistroyTbody").html(row);
                    $("#grandTotal").text(`Rs.${grandTotal.toFixed(2)}/-`);

                    setTimeout(() => {
                        $("#expenseHistroyTable").DataTable({
                            responsive: true,
                            destroy: true,
                            pageLength: 20,
                            scrollCollapse: true,
                            dom: 'Bfrtip',
                            buttons: [
                                {
                                    extend: 'excelHtml5',
                                    text: '<i class="bx bx-spreadsheet" style="font-size:15px"></i> Download Excel',
                                    className: 'btn btn-primary',
                                },
                            ],
                            footerCallback: function (row, data, start, end, display) {
                                let api = this.api();
                                if (api.column(totalColumnIndex).data().length) {
                                    let total = api.column(totalColumnIndex, { page: 'current' }).data()
                                        .reduce((a, b) => parseFloat(a) + parseFloat(b), 0);
                                    $(api.column(totalColumnIndex).footer()).html(`Rs.${total.toFixed(2)}/-`);
                                }
                            }
                        });
                    }, 500);

                    adjustTable("expenseHistroyTable");
                } else {
                    document.getElementById("historyTableContainer").style.display = "none";
                    showNotification("error", res.message);
                }
            },
            error: function (xhr, status, error) {
                console.error("Error:", error);
            },
            complete: function () {
                handleLoader("none");
            },
        });
    });
});