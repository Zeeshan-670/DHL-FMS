<?php $title = "RFQ / Work Order";?>
<?php include 'include/header.php';?>


<link rel="stylesheet" href="./assets/css/workorder.css">

<body data-sidebar="dark" data-layout-mode="light"  class="vertical-collpsed">

    <!-- Begin page -->
    <div id="layout-wrapper">
        <!-- Header Include Here -->
        <?php include 'include/menu.php';?>
        <!-- Left Sidebar End -->
        <?php include 'include/sidebar.php';?>

        <div class="main-content" id="main-content" >
            <div class="page-content">
                <div class="container-fluid">
                    <?php include 'include/breadcrumb.php';?>

                    <h5 class="my-3">Create RFQ</h1>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <form class="veh-search d-lg-block" id="searchVehicleForm">
                                                <div class="position-relative">
                                                    <input type="text" class="form-control" placeholder="Search Vehicle..." id="searchVehicle">
                                                    <div class="autocomplete-suggestions autocomplete-suggestionsjob" id="autocompleteList1"></div>
                                                    <span class="fas fa-car-alt"></span>
                                                    <button type="submit">
                                                        <i class="bx bx-search-alt"></i>
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <form id="rfqForm">
                                            <div class="row mb-2 g-3">
                                                <div class="col-md-3">
                                                    <label class="form-label" for="exJobTitle">Job Title</label>
                                                    <input type="text" id="exJobTitle" class="form-control"
                                                        placeholder="Enter Job Title" required>
                                                </div>
                                                
                                                <div class="col-md-3">
                                                    <label class="form-label" for="exRegNo">Reg. No.</label>
                                                    <input type="text" id="exRegNo" class="form-control"
                                                        placeholder="Enter Reg. No." readonly>
                                                    <input type="hidden" id="exVid">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label" for="exSltVendor">Vendor</label>
                                                    <select class="form-select" id="exSltVendor" required>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label" for="exDesc">Description</label>
                                                    <select class="form-select" id="exDesc" required>
                                                        <option value="Your Recommendation is Required">Your Recommendation is Requested</option>
                                                        <option value="-">-</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                        <label class="form-label" for="exEngine">Engine</label>
                                                        <input type="text" id="exEngine" class="form-control"
                                                            placeholder="Enter Engine" readonly required>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label" for="exChasis">Chasis</label>
                                                        <input type="text" id="exChasis" class="form-control"
                                                            placeholder="Enter Chasis" readonly required>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label" for="exBrand">Brand/ Make</label>
                                                        <input type="text" id="exBrand" class="form-control"
                                                            placeholder="Enter Brand" readonly required>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label class="form-label" for="exModel">Model</label>
                                                        <input type="text" id="exModel" class="form-control"
                                                            placeholder="Enter Model" readonly required>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label" for="exStation">Station</label>
                                                        <input type="text" id="exStation" class="form-control"
                                                            placeholder="Enter Station" readonly required>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label" for="exMileage">Mileage</label>
                                                        <input type="text" id="exMileage" class="form-control"
                                                            placeholder="Enter Mileage" readonly required>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label" for="exLocation">Location</label>
                                                        <input type="text" id="exLocation" class="form-control"
                                                            placeholder="Enter Location" readonly required>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label" for="exDepartment">Department</label>
                                                        <input type="text" id="exDepartment" class="form-control"
                                                            placeholder="Enter Department" required readonly>
                                                    </div>
                                            </div>

                                            <div id="expenseRows" class="mt-4">
                                                <div class="alert-row row mb-2">
                                                    <div class="col-md-4">
                                                        <label class="form-label">Expense Category</label>
                                                        <select class="form-select me-2 alert-type category-select" required id="expenseCategory">
                                                            <option value=""> Select Category</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2 d-none">
                                                        <label class="form-label">Amount</label>
                                                        <!-- <div class="input-group"> -->
                                                            <input type="number" step="0.01"  value="0" min="0" class="form-control" placeholder="Enter Amount">
                                                        <!-- </div> -->
                                                    </div>
                                                    <div class="col-md-2 d-none">
                                                        <label class="form-label">Tax</label>
                                                        <div class="input-group">
                                                            <!-- <input type="number" class="form-control" placeholder="Enter Amount" required> -->
                                                            <select class="form-select alert-type tax-select" id="expenseTax">
                                                            </select>
                                                            <button type="button" class="btn btn-primary docs-datepicker-trigger">
                                                                %
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-7">
                                                        <label class="form-label">Description</label>
                                                        <input type="text" class="form-control" placeholder="Enter Description" required>
                                                    </div>
                                                    <div class="col-md-1 d-flex align-items-end">
                                                        <button type="button" class="btn btn-primary add-row w-100"><i class="fas fa-plus"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                           
                                            <div class="row">
                                                <div class="col-md-12 text-end mt-4">
                                                    <button class="btn btn-primary" id="doneButton"
                                                        type="submit">Forward</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                 
                </div>
            </div>

        </div>
        <?php include 'include/footer.php';?>
    </div>
    </div>
    <!-- JAVASCRIPT -->
    <?php include 'include/scripts.php';?>
    <script src="assets/js/initVehicleSearch.js"></script>
    
    <script src="assets/js/customjs/rfq.js"></script>
</body>

</html>