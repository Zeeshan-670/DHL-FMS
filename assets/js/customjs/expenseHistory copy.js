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
                console.log(res);
                let row = "";

                // Destroy the existing DataTable instance if it exists
                if ($.fn.DataTable.isDataTable("#expenseHistroyTable")) {
                    $("#expenseHistroyTable").DataTable().destroy();
                }

                // Clear the entire table before adding new data
                $("#expenseHistroyTable").empty();

                let tableHeaders = `<tr>`;
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
                } else {
                    tableHeaders += `
                        <th>S.No</th>
                        <th>Expense Date</th>
                        <th>Reg No</th>
                        <th>Station</th>
                        <th>Amount</th>
                        <th>Expense Description</th>`;
                }
                tableHeaders += `</tr>`;

                // Append new table structure
                $("#expenseHistroyTable").append(`
                    <thead>${tableHeaders}</thead>
                    <tbody id="expenseHistroyTbody"></tbody>
                `);

                if (res.success) {
                    showNotification("success", res.message || "Data Found");
                    document.getElementById("historyTableContainer").style.display = "block";

                    let dataObj = res.data;
                    let count = 0;

                    if (filterBy === "new") {
                        // Iterate through the invoices and their details for new filter data
                        for (const key in dataObj) {
                            const invoice = dataObj[key];
                            const details = invoice.details;
                            let invoiceTotal = 0; // Variable to track total per invoice

                            // Iterate through details for the invoice and render each detail row
                            details.forEach((el, i) => {
                                const amountWithoutTax = calculateAmountWithoutTax(el['Amount'], el['salesTaxValue']); // ✅ Move calculation here 
                                row += `<tr>`;
                                row += `
                                    <td style="font-size:12px;">${++count}</td>
                                    <td style="font-size:12px;">${invoice.CreationDate}</td>
                                    <td style="font-size:12px;">${invoice.Reg}</td>
                                    <td style="font-size:12px;">${invoice.StationName}</td>
                                    <td style="font-size:12px;">${invoice.vendorname || '-'}</td>
                                    <td style="font-size:12px;">${el.ExpenseCategory}</td>
                                    <td style="font-size:12px;">${el.Description}</td>
                                    <td style="font-size:12px;">${amountWithoutTax}</td>
                                    <td style="font-size:12px;">${el.salesTax}%</td>
                                    <td style="font-size:12px;">${el.Amount}</td>
                                    <td style="font-size:12px;">${capitalizeFirstLetter(el.CreatedBy)}</td>
                                `;
                                row += `</tr>`;
                            });

                            //  // Add a row for the invoice data in bold after details
                            // row += `<tr style="font-weight: bold; background-color: #f0f0f0;">`;
                            // row += `
                            //        <th style="font-size:12px;">${++count}</th>
                            //         <th style="font-size:12px;">${invoice.CreationDate}</th>
                            //         <th style="font-size:12px;"> ${invoice.Reg}</th>
                            //         <th style="font-size:12px;">${invoice.StationName}</th>
                            //         <th style="font-size:12px;">${invoice.vendorname || '-'}</th>
                            //         <th style="font-size:12px;"> Total </th>
                            //         <th style="font-size:12px;"> - </th>
                            //         <th style="font-size:12px;">Rs.${invoice.GrandTotal}/-</th>
                            //         <th style="font-size:12px;"> - </th>
                            //         <th style="font-size:12px;">Rs.${invoiceTotal.toFixed(2)}/-</th>
                            //         <th style="font-size:12px;"> - </th>
                            // `;
                            // row += `</tr>`;
                        }
                    } else {
                        dataObj.forEach((el, i) => {
                            row += `<tr>`;
                            row += `
                                <td style="font-size:12px;">${i + 1}</td>
                                <td style="font-size:12px;">${el.expenseDate}</td>
                                <td style="font-size:12px;">${el.reg}</td>
                                <td style="font-size:12px;">${el.stationname}</td>
                                <td style="font-size:12px;">${el.expenseAmount}</td>
                                <td style="font-size:12px;">${el.expenseDescription}</td>
                            `;
                            row += `</tr>`;
                        });
                    }

                    $("#expenseHistroyTbody").html(row);

                    // Reinitialize DataTable with new structure
                    $("#expenseHistroyTable").DataTable({
                        responsive: true,
                        destroy: true,  // Allow reinitialization
                        autoWidth: false,
                        pageLength: 10,
                        scrollX: true,
                        scrollCollapse: true,
                        dom: 'Bfrtip',
                        buttons: [
                            {
                                extend: 'excelHtml5',
                                text: '<i class="bx bx-spreadsheet" style="font-size:15px"></i> Download Excel',
                                className: 'btn btn-primary',
                            },
                        ],

                    });

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


// $(document).ready(function () { $("#datatable").DataTable(), $("#datatable-buttons").DataTable({ lengthChange: !1, buttons: ["copy", "excel", "pdf", "colvis"] }).buttons().container().appendTo("#datatable-buttons_wrapper .col-md-6:eq(0)"), $(".dataTables_length select").addClass("form-select form-select-sm") });