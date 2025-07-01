<?php $title = "Dashboard"; ?>
<?php include 'include/header.php'; ?>



<body data-sidebar="dark" data-layout-mode="light" class="vertical-collpsed">
    <style>
        .cust-form-label {
            margin-bottom: 0;
            font-size: 11px;
            color: #a29c9c;
        }

        .cust-form-control {
            font-size: 11px;
            padding: 0.2rem 0.25rem;
            color: #a29c9c;
            border: 1px solid #a29c9c;
            border-radius: 5px;

        }

        .no-data {
            display: none;
            text-align: center;
            margin-top: 10px;
        }
        .form-check .form-check-input {
            float: left;
            margin-left: -1.5em;
            background: #b0000445;
        }
        .form-check-danger .form-check-input:checked {
            background-color: #b00004;
            border-color: #b00004;
        }

        .apexcharts-canvas .apexcharts-zoom-icon.apexcharts-selected svg, .apexcharts-canvas .apexcharts-selection-icon.apexcharts-selected svg, .apexcharts-canvas .apexcharts-reset-zoom-icon.apexcharts-selected svg {
            fill: #b81a1e;
        }
        .apexcharts-pan-icon.apexcharts-selected svg {
    stroke: #b00004;
}
    
    /* #editcustomRange::-webkit-slider-thumb {
        background: #b00004;
    }

    #editcustomRange::-moz-range-thumb {
        background: #b00004;
    } */
    
    </style>
    <!-- Begin page -->
    <div id="layout-wrapper">
        <!-- Header Include Here -->
        <?php include 'include/menu.php'; ?>
        <!-- Left Sidebar End -->
        <?php include 'include/sidebar.php'; ?>

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    <!-- include breadcrumb -->
                    <?php include 'include/breadcrumb.php'; ?>

                    <div class="row">
                        <div class="col-md-4 col-lg-2">
                            <div class="card">
                                <div class="card-body">
                                    <form id="vehicleForm">
                                        <div class="row align-items-center">
                                            <label for="fromDate" class="cust-form-label col-lg-4 col-md-12">Form</label>
                                            <div class="col-lg-8 col-md-12">
                                                <input type="date" class="form-control" id="fromDate">
                                            </div>
                                        </div>
                                        <div class="my-3 row">
                                            <label for="toDate" class="cust-form-label col-lg-4 col-md-12">To</label>
                                            <div class="col-lg-8 col-md-12">
                                                <input type="date" class="form-control" id="toDate">
                                            </div>
                                        </div>

                                        <div class="text-center">
                                            <button class="btn btn-primary">Fetch Record</button>
                                        </div>
                                        <!-- <div>

                                        </div> -->
                                        <!-- <div>Vehicle List</div> -->
                                        <div class="app-search ">
                                            <div class="position-relative">
                                                <input type="text" class="form-control" id="searchVehicle" placeholder="Search Vehicle..." style="    border-radius: 6px;">
                                                <span class="bx bx-search-alt"></span>
                                            </div>
                                        </div>
                                        <div style="max-height: 400px;overflow: auto;">
                                            <div class="form-check form-check-danger mb-2 mx-2">
                                                <input class="form-check-input" type="checkbox" id="selectAll" checked>
                                                <label class="form-check-label" for="selectAll">
                                                <strong id="selectAllLabel">Unselect All</strong>
                                                </label>
                                            </div>
                                            <div id="vehicleList"></div>
                                        </div>
                                        <!-- <button type="submit">Submit</button> -->
                                    </form>
                                </div>

                            </div>
                        </div>

                        <div class="col-md-8 col-lg-10">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card mini-stats-wid">
                                        <div class="card-body text-end">
                                                <button type="button" class="btn btn-primary waves-effect waves-light mx-3" id="vmButton"
                                                    data-bs-toggle="modal" data-bs-target="#vehicleMaintenance">
                                                    <i class="fas fa-car-crash font-size-16 align-middle me-2"></i> Vehicle Maintenance
                                                </button>
                                                <a href="issues.php" type="button" class="btn btn-primary waves-effect waves-light">
                                                    <i class="fas fa-exclamation-circle font-size-16 align-middle me-2"></i> Issues
                                                </a>
                                                <a href="alert.php" type="button" class="btn btn-primary waves-effect waves-light alert-btn mx-3">
                                                    <i class="bx bx-bell bx-tada font-size-16 align-middle me-2 bx-tada"></i> Lastest Alert
                                                </a>
                                                
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 col-md-12">
                                    <div class="row">
                                        <div class="col-md-6 col-lg-3">
                                            <div class="card mini-stats-wid">
                                                <div class="card-body">
                                                    <div class="d-flex">
                                                        <div class="flex-grow-1">
                                                            <p class="text-muted fw-medium">Two Wheelers</p>
                                                            <h4 class="mb-0" id="twoWheeler">00</h4>
                                                        </div>

                                                        <div class="flex-shrink-0 align-self-center">
                                                            <div class="mini-stat-icon avatar-sm rounded-circle ">
                                                                <span class="avatar-title">
                                                                    <i class="mdi mdi-motorbike font-size-24"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-3">
                                            <div class="card mini-stats-wid">
                                                <div class="card-body">
                                                    <div class="d-flex">
                                                        <div class="flex-grow-1">
                                                            <p class="text-muted fw-medium">Four Wheelers</p>
                                                            <h4 class="mb-0" id="fourWheeler">00</h4>
                                                        </div>

                                                        <div class="flex-shrink-0 align-self-center ">
                                                            <div class="avatar-sm rounded-circle  mini-stat-icon">
                                                                <span class="avatar-title rounded-circle ">
                                                                    <i class="bx bx-car font-size-24"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-3">
                                            <div class="card mini-stats-wid">
                                                <div class="card-body">
                                                    <div class="d-flex">
                                                        <div class="flex-grow-1">
                                                            <p class="text-muted fw-medium">Total Drivers</p>
                                                            <h4 class="mb-0" id="drivercount">00</h4>

                                                        </div>

                                                        <div class="flex-shrink-0 align-self-center">
                                                            <div class="avatar-sm rounded-circle  mini-stat-icon">
                                                                <span class="avatar-title rounded-circle ">
                                                                    <i class="fa-solid fa-people-group font-size-24"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-3">
                                            <div class="card mini-stats-wid">
                                                <div class="card-body">
                                                    <div class="d-flex">
                                                        <div class="flex-grow-1">
                                                            <p class="text-muted fw-medium">Total Vendors</p>
                                                            <h4 class="mb-0" id="vendorcount">00</h4>
                                                        </div>

                                                        <div class="flex-shrink-0 align-self-center">
                                                            <div class="mini-stat-icon avatar-sm rounded-circle ">
                                                                <span class="avatar-title">
                                                                    <i class="fas fa-users font-size-24"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                                <!-- <div class="row"> -->
                            <div class="col-lg-12">   
                                <div class="row">
                                    <div class="col-xl-12">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-lg-6">
                                                        <h4 class="card-title mb-4">Fuel</h4>
                                                    </div>
                                                    <!-- <div class="col-lg-3">
                                                        <label for="fromDate" class="cust-form-label">Form</label>
                                                        <input type="date" class="cust-form-control" id="fromDate">
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <label for="toDate" class="cust-form-label">To</label>
                                                        <input type="date" class="cust-form-control" id="toDate">
                                                    </div> -->
                                                </div>
                                                <div id="fuelChart"
                                                    class="apex-charts" dir="ltr"></div>
                                            </div>
    
    
                                        </div>
                                    </div>
                                    <!--end card-->
    
                                    <div class="col-xl-12">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-lg-6">
                                                        <h4 class="card-title mb-4">Distance</h4>
                                                    </div>
                                                    <!-- <div class="col-lg-3">
                                                        <label for="fromDate" class="cust-form-label">Form</label>
                                                        <input type="date" class="cust-form-control" id="fromDate">
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <label for="toDate" class="cust-form-label">To</label>
                                                        <input type="date" class="cust-form-control" id="toDate">
                                                    </div> -->
                                                </div>
    
                                                <div id="distanceChart"
                                                    class="apex-charts" dir="ltr"></div>
                                            </div>
                                        </div>
                                        <!--end card-->
                                    </div>
                                    <div class="col-xl-12">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-lg-6">
                                                        <h4 class="card-title mb-4">Expense</h4>
                                                    </div>
                                                    <!-- <div class="col-lg-3">
                                                        <label for="fromDate" class="cust-form-label">Form</label>
                                                        <input type="date" class="cust-form-control" id="fromDate">
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <label for="toDate" class="cust-form-label">To</label>
                                                        <input type="date" class="cust-form-control" id="toDate">
                                                    </div> -->
                                                </div>
    
                                                <div id="expenseChart"
                                                    class="apex-charts" dir="ltr"></div>
                                            </div>
                                        </div>
                                        <!--end card-->
                                    </div>
                                </div> <!-- end row -->
                                <!-- <div class="row">
                                    <div class="col-xl-6">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-lg-6">
                                                        <h4 class="card-title mb-4">Expense</h4>
                                                    </div>
                                                </div>
    
                                                <div id="column_chart3"
                                                    data-colors='["--bs-success","--bs-primary", "--bs-danger"]'
                                                    class="apex-charts" dir="ltr"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-lg-6">
                                                        <h4 class="card-title mb-4">Driving License</h4>
                                                    </div>
                                                </div>
    
    
                                                <div id="column_chart4"
                                                    data-colors='["--bs-success","--bs-primary", "--bs-danger"]'
                                                    class="apex-charts" dir="ltr"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>  -->
                            </div>
                                <!-- </div> -->

                        </div>
                        <!-- <div class=" col-lg-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="mb-4 d-flex flex-column justify-content-end">
                                        <button type="button" class="btn btn-primary waves-effect waves-light my-2"
                                            data-bs-toggle="modal" data-bs-target="#createCustomAlerts">
                                            <i class="fas fa-exclamation-triangle font-size-16 align-middle me-2"></i> Create Custom Alert
                                        </button>
                                        <button type="button" class="btn btn-primary waves-effect waves-light" id="vmButton"
                                            data-bs-toggle="modal" data-bs-target="#vehicleMaintenance">
                                            <i class="fas fa-car-crash font-size-16 align-middle me-2"></i> Vehicle Maintenance
                                        </button>
                                    </div>
                                    <div class="d-flex justify-content-between  mb-3 align-items-center">
                                        <h4 class="card-title mb-0">Alerts</h4>
                                        <div class="app-search ">
                                            <div class="position-relative">
                                                <input type="text" class="form-control" id="searchInput" placeholder="Search Alerts..." onkeyup="filterTimeline()">
                                                <span class="bx bx-search-alt"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="no-data" id="noDataMessage"><a href="javascript: void(0);"><i class="fas fa-exclamation-circle"></i> No Alert Found!</a></p>
                                    <ul class="verti-timeline list-unstyled" id="eventList">
                                        

                                    </ul>




                                    </ul>
                                </div>
                            </div>
                        </div> -->
                    </div>

                    <!-- end page title -->
                </div>
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->


            
            <div class="modal fade" id="vehicleMaintenance"  tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
                role="dialog" aria-labelledby="vehicleMaintenanceLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="vehicleMaintenanceLabel">Vehicle Maintenance</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                        <div>
                            <div class="d-flex gap-3 justify-content-end mb-3">
                                <button type="button" class="btn btn-primary waves-effect waves-light" id="openAddServiceModal">
                                    <i class="bx bx-plus font-size-16 align-middle me-2"></i> Add Service
                                </button>
                                <button type="button" class="btn btn-primary waves-effect waves-light" id="openaddVehicleMaintenance">
                                    <i class="bx bx-plus font-size-16 align-middle me-2"></i> Add Vehicle Maintenance
                                </button>
                            </div>
                                <table class="table table-editable table-nowrap table-edits text-center w-100" id="vmTable" style="font-size: 12px;">
                                    <thead>
                                        <tr>
                                            <th>S.No</th>
                                            <th style="display: none;">Vehicle ID</th>
                                            <th>Action</th>
                                            <th>Reg No</th>
                                            <th>Running Mileage</th>
                                            <th>Set Mileage</th>
                                            <th>Service</th>
                                            <th>Creation Date</th>
                                            <th>Created By</th>
                                        </tr>
                                    </thead>
                                    <tbody id="vmTbody">
                                        <!-- Rows will be dynamically populated -->
                                    </tbody>
                                </table>

                            </div>
                        </div>
                       
                    </div>
                </div>
            </div>
            <div class="modal fade" id="addService" style="z-index: 1000000;background: #1c1c1c66;" tabindex="-1" role="dialog" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="addServiceLabel" aria-hidden="true">
                <div class="modal-dialog modal-md modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addServiceLabel">Add Service</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                            <div class="row">
                                <div class="col-sm-12 mt-3">
                                    <table class="table table-bordered" id='serviceTable' style="min-width:100%;font-size:11px">
                                        <thead>
                                            <tr>
                                                <th>S.NO</th>
                                                <th>Service Name</th>
                                                <th>Creation Date</th>
                                            </tr>
                                        </thead>
                                        <tbody id="serviceTBody"></tbody>
                                    </table>
                                </div>
                            </div>
                            <form id="addServiceForm">
                                <!-- Vehicle Selection -->
                                <div class="mb-3">
                                    <label for="serviceName" class="form-label">Service Name</label>
                                    <input class="form-control" type="text" id="serviceName" />
                                </div>
                                <!-- Submit Button -->
                                <button type="submit" class="btn btn-primary w-100 mt-3">Add Service</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="addVehicleMaintenance" style="z-index: 1000000;background: #1c1c1c66;" tabindex="-1" role="dialog" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="addVehicleMaintenanceLabel" aria-hidden="true">
                <div class="modal-dialog modal-md modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addVehicleMaintenanceLabel">Add Vehicle Maintenance</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="resetVMFrom()"></button>
                        </div>

                        <div class="modal-body">
                            <form id="addVehicleMaintenanceForm">
                                <!-- Vehicle Selection -->
                                <div class="mb-3">
                                    <label for="addVMVehicleList" class="form-label">Select Vehicle</label>
                                    <select class="form-select" id="addVMVehicleList" required>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="addVMService" class="form-label">Select Service</label>
                                    <select class="form-select" id="addVMService" required>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="addVMService" class="form-label">Set Mileage</label>
                                    <input type="range" style="width: 100%; padding: 15px 0;accent-color: #b00004"  value="0" min="0" max="50000" step="5000" id="editcustomRange" required="true" >
                                    <label for="editcustomRange" class="form-label rangeLabel" id="editRangeLabel">Km: 00</label>
                                </div>
                                <!-- Submit Button -->
                                 <div class="d-flex gap-3">
                                     <button onclick="resetVMFrom()" type="button" class="btn btn-primary w-100 mt-3">Reset</button>
                                     <button type="submit" class="btn btn-primary w-100 mt-3">Add Vehicle Maintenance</button>
                                 </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="editVehicleMaintenance" style="z-index: 1000000;background: #1c1c1c66;" tabindex="-1" role="dialog" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="editVehicleMaintenanceLabel" aria-hidden="true">
                <div class="modal-dialog modal-md modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addVehicleMaintenanceLabel">Add Vehicle Maintenance</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="resetVMFrom()"></button>
                        </div>

                        <div class="modal-body">
                            <form id="editVehicleMaintenanceForm">
                                <!-- Vehicle Selection -->
                                <div class="mb-3">
                                    <label for="edVmVehicle" class="form-label">Vehicle</label>
                                    <input type='text' class="form-control" id='edVmVehicle' readonly/>
                                    <input type='hidden' class="form-control" id='edVmVehicleId' />
                                </div>
                                <div class="mb-3">
                                    <label for="addVMService" class="form-label">Service</label>
                                    <input type='text' class="form-control" id='edVmService' readonly />
                                </div>
                                <div class="mb-3">
                                    <label for="addVMService" class="form-label">Set Mileage</label>
                                    <input type="range" style="width: 100%; padding: 15px 0;accent-color: #b00004" min="0" max="50000" step="5000" id="ededitcustomRange" required="true" >
                                    <label for="editcustomRange" class="form-label rangeLabel" id="ededitRangeLabel">Km: 00</label>
                                </div>
                                <!-- Submit Button -->
                                 <div class="d-flex gap-3">
                                     <button type="submit" class="btn btn-primary w-100 mt-3">Edit Vehicle Maintenance</button>
                                 </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
           

            <!-- subscribeModal -->
            <div class="modal fade" id="subscribeModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="subscribeModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header border-bottom-0">
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="text-center mb-4">
                                <div class="avatar-md mx-auto mb-4">
                                    <div class="avatar-title bg-light rounded-circle text-primary h1">
                                        <i class="mdi mdi-email-open"></i>
                                    </div>
                                </div>

                                <div class="row justify-content-center">
                                    <div class="col-xl-10">
                                        <h4 class="text-primary">Subscribe !</h4>
                                        <p class="text-muted font-size-14 mb-4">
                                            Subscribe our newletter and get notification to stay
                                            update.
                                        </p>

                                        <div class="input-group bg-light rounded">
                                            <input type="email" class="form-control bg-transparent border-0"
                                                placeholder="Enter Email address" aria-label="Recipient's username"
                                                aria-describedby="button-addon2" />

                                            <button class="btn btn-primary" type="button" id="button-addon2">
                                                <i class="bx bxs-paper-plane"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end modal -->
            <!--  -->
            <?php include 'include/footer.php'; ?>


        </div>
        <!-- end main content-->
    </div>
    <!-- END layout-wrapper -->

    <!-- JAVASCRIPT -->

    <!-- apexcharts -->
    <script src="assets/libs/apexcharts/apexcharts.min.js"></script>
    <!-- apexcharts init -->
    <!-- <script src="assets/js/pages/apexcharts.init.js"></script> -->
    <!-- dashboard init -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment.min.js"></script>
    <script src="assets/js/pages/dashboard.init.js"></script>
    <?php include 'include/scripts.php'; ?>

    <script src="assets/js/customjs/dashboard.js"></script>


</body>

</html>