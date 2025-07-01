<?php $title = "Job / Work Order";?>
<?php include 'include/header.php';?>


<link rel="stylesheet" href="./assets/css/workorder.css">

<body data-sidebar="dark" data-layout-mode="light"  class="vertical-collpsed">

    <!-- Begin page -->
    <div id="layout-wrapper">
        <!-- Header Include Here -->
        <?php include 'include/menu.php';?>
        <!-- Left Sidebar End -->
        <?php include 'include/sidebar.php';?>

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content" id="main-content" >
            <div class="page-content">
                <div class="container-fluid">
                    <?php include 'include/breadcrumb.php';?>

                    <h5 class="my-3">Create Job</h1>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <form class="veh-search d-lg-block" id="jobSearch">
                                                <div class="position-relative">
                                                    <input type="text" class="form-control" placeholder="Search Vehicle..." id="jobsearchVeh">
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
                                        <form id="serviceJobForm">
                                            <div class="row mb-2">
                                                <div class="col-md-3">
                                                    <label class="form-label" for="ctJobTitle">Job Title</label>
                                                    <input type="text" id="ctJobTitle" class="form-control"
                                                        placeholder="Enter Job Title">
                                                </div>
                                                
                                                <div class="col-md-3">
                                                    <label class="form-label" for="ctregNo">Reg. No.</label>
                                                    <input type="text" id="ctregNo" class="form-control"
                                                        placeholder="Enter Reg. No." readonly>
                                                    <input type="hidden" id="ctvid">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label" for="ctsltVendor">Vendor</label>
                                                    <select class="form-select" id="ctsltVendor">
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label" for="ctDesc">Description</label>
                                                    <input type="text" id="ctDesc" class="form-control"
                                                    placeholder="Enter Description">
                                                </div>
                                            </div>
                                           
                                            <div class="row">
                                                <div class="col-md-12 text-end mt-4">
                                                    <button class="btn btn-primary" id="doneButton"
                                                        type="submit">Create Job</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <h5>Create Work Order</h1>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <form class="veh-search d-lg-block" id="workorderSearch">
                                                <div class="position-relative">
                                                    <input type="text" class="form-control" placeholder="Search Vehicle..." id="workordersearchVeh">
                                                    <div class="autocomplete-suggestions autocomplete-suggestionsworkorder" id="autocompleteList1"></div>
                                                    <span class="fas fa-car-alt"></span>
                                                    <button type="submit">
                                                    <i class="bx bx-search-alt"></i>
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <form id="serviceOrderForm">
                                            <div class="row mb-2">
                                                <div class="col-md-6">
                                                    <label class="form-label" for="jobTitle">Job Title</label>
                                                    <!-- <input type="text"  class="form-control"
                                                        placeholder="Enter Job Title"> -->
                                                        <select class="form-select" id="jobTitle">
                                                    </select>


                                                        
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label" for="station">Station</label>
                                                    <input type="text" id="station" class="form-control" readonly
                                                        placeholder="Enter Station">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label" for="dateField">Date</label>
                                                    <input type="text" id="dateField" class="form-control"
                                                        placeholder="Enter Date" readonly>
                                                </div>
                                            </div>
                                            <div class="row mb-2">
                                                <div class="col-md-3">
                                                    <label class="form-label" for="make">Make</label>
                                                    <input type="text" id="make" class="form-control"
                                                        placeholder="Enter Make" readonly>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label" for="model">Model</label>
                                                    <input type="text" id="model" class="form-control"
                                                        placeholder="Enter Model" readonly>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label" for="regNo">Reg. No.</label>
                                                    <input type="text" id="regNo" class="form-control"
                                                        placeholder="Enter Reg. No." readonly>
                                                    <input type="hidden" id="vid">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label" for="mileage">Mileage</label>
                                                    <input type="text" id="mileage" class="form-control"
                                                        placeholder="Enter Mileage" readonly>
                                                </div>
                                            </div>
                                            <div class="row mb-2">
                                                <div class="col-md-6">
                                                    <label class="form-label" for="sltVendor">Vendor</label>
                                                    <!-- <input type="text"  id="sltVendor" class="form-control" placeholder="Vendor Name" readonly> -->
                                                    <select class="form-select"  id="sltVendor" disabled>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row mb-2">
                                                <div class="col-md-6">
                                                    <label for="addField">Add Service:</label>
                                                    <div class="input-group mb-3">
                                                        <input type="text" id="addField" class="form-control"
                                                            placeholder="Type Service..."
                                                            oninput="this.value = this.value.replace(/[^a-zA-Z0-9 ]/g, '')">
                                                        <div class="input-group-append">
                                                            <button class="btn btn-outline-secondary" type="button"
                                                                id="addButton">Add</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label" for="resultField">Services</label>
                                                    <input type="text" id="resultField" placeholder="Services"
                                                        class="form-control" readonly>
                                                </div>
                                            </div>
                                            <div class="row mb-2">
                                                <div class="col-md-12">
                                                    <label class="form-label" for="additionalJob">Additional Job</label>
                                                    <input type="text" id="additionalJob" class="form-control"
                                                        placeholder="Brake service, Throttle service and Spark plugs service">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12 text-end mt-4">
                                                    <button class="btn btn-primary" id="doneButton"
                                                        type="submit">Create Work Order</button>
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

    <script src="assets/js/customjs/workorder.js"></script>
</body>

</html>