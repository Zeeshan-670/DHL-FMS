<?php $title = "Latest Alerts";?>
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

                    <!-- <h5 class="my-3">Latest Alerts</h1> -->
                   
                    <div class="row">
                        <div class="col-md-12">
                            <!-- <div class="card mini-stats-wid"> -->
                                <!-- <div class="card-body text-end"> -->
                            <div>
                                <div class="text-end">
                                    <button type="button" class="btn btn-primary waves-effect waves-light my-2"
                                        data-bs-toggle="modal" data-bs-target="#createCustomAlerts">
                                        <i class="fas fa-exclamation-triangle font-size-16 align-middle me-2"></i> Create Custom Alert
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <!-- <div class="card">
                                <div class="card-body"> -->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div id="buttons-container" class="row"></div>
                                        </div>
                                    </div>
                                    
                                <!-- </div>
                            </div> -->
                        </div>
                    </div>


                    <!-- Custom Alerts -->
                    <div class="modal fade" id="createCustomAlerts" tabindex="-1" role="dialog" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="createCustomAlertsLabel" aria-hidden="true">
                        <div class="modal-dialog modal-md modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="createCustomAlertsLabel">Create Custom Alerts</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>

                                <div class="modal-body">
                                    <form id="customAlertForm">
                                        <!-- Vehicle Selection -->
                                        <div class="mb-3">
                                            <label for="caVehicleAlert" class="form-label">Select Vehicle</label>
                                            <select class="form-select" id="caVehicleAlert" required>
                                            </select>
                                        </div>

                                        <!-- Dynamic Alert Rows -->
                                        <div id="alertRows">
                                            <div class="alert-row d-flex mb-2">
                                                <select class="form-select me-2 alert-type" required>
                                                    <option value="">Select Alert Type</option>
                                                    <option value="Fire Extinguisher">Fire Extinguisher</option>
                                                    <option value="First Aid Box">First Aid Box</option>
                                                    <option value="Fitness Certificate">Fitness Certificate</option>
                                                </select>
                                                <input type="date" class="form-control me-2 alert-date" required>
                                                <button type="button" class="btn btn-primary add-row"><i class="fas fa-plus"></i></button>
                                            </div>
                                        </div>

                                        <!-- Submit Button -->
                                        <button type="submit" class="btn btn-primary w-100 mt-3">Create Alert</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Bootstrap Modal -->
                    <div class="modal fade" id="dataModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="dataModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="dataModalLabel"><span id='modalHeading'></span> Alert Details</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>

                                <div class="modal-body">
                                    <!-- Search Bar Inside Modal -->
                                    <div class="app-search mb-3">
                                        <div class="position-relative">
                                            <input type="text" class="form-control" id="modalSearchInput" placeholder="Search Alerts..." onkeyup="filterModalAlerts()">
                                            <span class="bx bx-search-alt"></span>
                                        </div>
                                    </div>

                                    <p class="no-data text-center" id="noDataMessage" style="display: none;">
                                        <a href="javascript: void(0);"><i class="fas fa-exclamation-circle"></i> No Alert Found!</a>
                                    </p>

                                    <div id="modal-body-content" style="max-height: 550px; overflow-y: auto;"></div>
                                    
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment.min.js"></script>


    <script src="assets/js/customjs/alert.js"></script>
</body>

</html>