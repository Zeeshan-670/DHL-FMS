<?php $title = "Vehicle Detail";?>

<?php include 'include/header.php';?>



<body data-sidebar="dark" data-layout-mode="light"  class="vertical-collpsed">

    <style>
    .dataTables_wrapper .dataTables_scroll div.dataTables_scrollBody>table>thead>tr>th,
    .dataTables_wrapper .dataTables_scroll div.dataTables_scrollBody>table>thead>tr>td,
    .dataTables_wrapper .dataTables_scroll div.dataTables_scrollBody>table>tbody>tr>th,
    .dataTables_wrapper .dataTables_scroll div.dataTables_scrollBody>table>tbody>tr>td {
        vertical-align: baseline !important;
    }

    .dataTables_scrollBody::-webkit-scrollbar {
        width: 10px;
        height: 10px;
        border-radius: 10px;
    }

    .dataTables_scrollBody::-webkit-scrollbar-track {
        box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3);
    }

    .dataTables_scrollBody::-webkit-scrollbar-thumb {
        background-color: darkgrey;
        outline: none;
    }

    .dataTables_scroll {
        text-align: center;
    }
    p.custom-msg {
        font-size: 14px;
    }
    span.custom-msg-reg {
    color: #D40511;
    font-weight: 700;
}
select#statusSelect {
    height: auto;
    padding: 0.25rem .25em;
    font-size: 14px;
    max-width: 175px;
    margin: 0;
}
label.swal2-label {
    font-size: 14px;
    margin-bottom: 0;
    min-width: 135px;
}
div#swal2-html-container {
    font-size: 14px;
}
.swal2-input {
    height: auto;
    padding: 0.25rem .25em;
    font-size: 14px;
    max-width: 175px;
    margin: 0;
}
.swal2-file:focus, .swal2-input:focus, .swal2-textarea:focus {
    border: 1px solid #d9d9d9 !important;
    outline: 0;
    box-shadow: 0 0 0 3px rgba(100, 150, 200, .5);
    box-shadow: none !important;
}
div.swal2-validation-message {
    width: 100% !important;
    background: #f0f0f000 !important;
    padding-top: 23px !important;
}
    </style>
    <!-- Begin page -->
    <div id="layout-wrapper">
        <!-- Header Include Here -->
        <?php include 'include/menu.php';?>
        <!-- Left Sidebar End -->
        <?php include 'include/sidebar.php';?>

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content" id="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    <!-- include breadcrumb -->
                    <?php include 'include/breadcrumb.php';?>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <div
                                        style="display: flex;justify-content: space-between;align-items: center;flex-wrap: wrap;">
                                        <h4 class="card-title">
                                            <div>
                                                <h4 class="card-title mb-0">Vehicle Detail List</h4>
                                                <p class="card-title-desc" style="font-weight: 400;font-size: 13px;">
                                                    Switch between Active & Deactive Vehicle</p>
                                            </div>
                                        </h4>
                                        <div class="d-flex gap-3">

                                            <div class="dropdown">
                                                <button class="btn btn-primary dropdown-toggle" type="button"
                                                    id="dropdownMenuButton" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                    Update Options <i class="mdi mdi-chevron-down"></i>
                                                </button>
                                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                            data-bs-target="#addMakeModal">Add Make</a></li>
                                                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                            data-bs-target="#addModelModal">Add Model</a></li>
                                                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                            data-bs-target="#addStationModal">Add Station</a></li>
                                                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                            data-bs-target="#addCategoryModal">Add Category</a></li>
                                                    <!-- <li><a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                            data-bs-target="#addVehicleFeatModal">Add Vehicle Features</a></li> -->
                                                </ul>
                                            </div>

                                            <button type="button" class="btn btn-primary waves-effect waves-light"
                                                data-bs-toggle="modal" data-bs-target="#AddVehicle">
                                                <i class="bx bx-plus font-size-16 align-middle me-2"></i> Add Vehicle
                                            </button>
                                        </div>


                                    </div>


                                    <ul class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist">
                                        <li class="nav-item" onclick="adjustTable(`activeVehicledetial`)">
                                            <a class="nav-link active" data-bs-toggle="tab" href="#activeVehicleTab"
                                                role="tab">
                                                <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
                                                <span class="">Active Vehicle Details</span>
                                            </a>
                                        </li>
                                        <li class="nav-item" onclick="adjustTable(`deactiveVehicledetial`)">
                                            <a class="nav-link" data-bs-toggle="tab" href="#deactiveVehicleTab" role="tab">
                                                <span class="d-block d-sm-none"><i class="far fa-user"></i></span>
                                                <span class="">Deactive Vehicle Details</span>
                                            </a>
                                        </li>
                                        <li class="nav-item" onclick="adjustTable(`downTimeVehicledetial`)">
                                            <a class="nav-link" data-bs-toggle="tab" href="#downtimeVehicleTab" role="tab">
                                                <span class="d-block d-sm-none"><i class="far fa-user"></i></span>
                                                <span class="">Down Time Vehicle Details</span>
                                            </a>
                                        </li>

                                    </ul>

                                    <!-- Tab panes -->
                                    <div class="tab-content p-3 text-muted">
                                        <div class="tab-pane active" id="activeVehicleTab" role="tabpanel">
                                            <div class=" mt-4">
                                                <table class="table table-editable table-nowrap table-edits text-center" id="activeVehicledetial" style="font-size: 12px;">
                                                    <thead>
                                                        <tr>
                                                            <th>S.No</th>
                                                            <th style="display: none;">Vehicle ID</th>
                                                            <th>Status</th>
                                                            <th>Actions</th>
                                                            <th>Reg</th>
                                                            <th>Make Name</th>
                                                            <th>Model Name</th>
                                                            <th>Station Name</th>
                                                            <th>Engine</th>
                                                            <th>Chassis</th>
                                                            <th>Date of Issue</th>
                                                            <th>Depreciation Years</th>
                                                            <th>ICN</th>
                                                            <th>Device</th>
                                                            <th>Segment</th>
                                                            <th>PUD/GTW</th>
                                                            <th>Fuel Type</th>
                                                            <th>Payload (kg)</th>
                                                            <th>Date of Maturity</th>
                                                            <th>Category Name</th>
                                                            <th>Remarks</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="activeVDTbody">
                                                        <!-- Rows will be dynamically populated -->
                                                    </tbody>
                                                </table>

                                            </div>
                                        </div>
                                        <div class="tab-pane" id="deactiveVehicleTab" role="tabpanel">
                                            <div class=" mt-4">
                                            <table class="table table-editable table-nowrap table-edits text-center" id="deactiveVehicledetial" style="font-size: 12px;">
                                                    <thead>
                                                        <tr>
                                                            <th>S.No</th>
                                                            <th style="display: none;">Vehicle ID</th>
                                                            <th>Status</th>
                                                            <th>Actions</th>
                                                            <th>Reg</th>
                                                            <th>Make Name</th>
                                                            <th>Model Name</th>
                                                            <th>Station Name</th>
                                                            <th>Engine</th>
                                                            <th>Chassis</th>
                                                            <th>Date of Issue</th>
                                                            <th>Depreciation Years</th>
                                                            <th>ICN</th>
                                                            <th>Device</th>
                                                            <th>Segment</th>
                                                            <th>PUD/GTW</th>
                                                            <th>Fuel Type</th>
                                                            <th>Payload (kg)</th>
                                                            <th>Date of Maturity</th>
                                                            <th>Category Name</th>
                                                            <th>Remarks</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="deactiveVDTbody">
                                                        <!-- Rows will be dynamically populated -->
                                                    </tbody>
                                                </table>

                                            </div>
                                        </div>
                                        <div class="tab-pane" id="downtimeVehicleTab" role="tabpanel">
                                            <div class=" mt-4">
                                            <table class="table table-editable table-nowrap table-edits text-center" id="downTimeVehicledetial" style="font-size: 12px;">
                                                    <thead>
                                                        <tr>
                                                            <th>S.No</th>
                                                            <th style="display: none;">Vehicle ID</th>
                                                            <th>Status</th>
                                                            <th>Actions</th>
                                                            <th>Reg</th>
                                                            <th>Make Name</th>
                                                            <th>Model Name</th>
                                                            <th>Station Name</th>
                                                            <th>Engine</th>
                                                            <th>Chassis</th>
                                                            <th>Date of Issue</th>
                                                            <th>Depreciation Years</th>
                                                            <th>ICN</th>
                                                            <th>Device</th>
                                                            <th>Segment</th>
                                                            <th>PUD/GTW</th>
                                                            <th>Fuel Type</th>
                                                            <th>Payload (kg)</th>
                                                            <th>Date of Maturity</th>
                                                            <th>Category Name</th>
                                                            <th>Remarks</th>
                                                            <th>Start Date</th>
                                                            <th>End Date</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="downtimeVDTbody">
                                                        <!-- Rows will be dynamically populated -->
                                                    </tbody>
                                                </table>

                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- end page title -->
                </div>
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->






            <!-- Add Vehicle Modal -->
            <div class="modal fade" id="AddVehicle" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                role="dialog" aria-labelledby="AddVehicleLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="AddVehicleLabel">Add Vehicle</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form class="" id="addVehicleForm">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="vdVehRegNo" class="form-label">Vehicle Reg.
                                                No.</label>
                                            <input type="text" class="form-control" id="vdVehRegNo"
                                                placeholder="Enter Vehicle Reg. No." required>
                                            <div class="invalid-feedback">
                                                Please Enter a Vehicle Reg. No.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="vdMake" class="form-label">Make</label>
                                            <select class="form-select" id="vdMake" required>
                                            </select>
                                            <div class="invalid-feedback">
                                                Please Select Make.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="vdModel" class="form-label">Model</label>
                                            <select class="form-select" id="vdModel" required></select>
                                            <div class="invalid-feedback">
                                                Please select a valid model.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="vdCategory" class="form-label">Category</label>
                                            <select class="form-select" id="vdCategory" required></select>
                                            <div class="invalid-feedback">
                                                Please select a valid category.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="vdEngine" class="form-label">Engine</label>
                                            <input type="text" class="form-control" id="vdEngine"
                                                placeholder="Enter Engine No." required>
                                            <div class="invalid-feedback">
                                                Please Enter Engine No.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="vdChassis" class="form-label">Chassis</label>
                                            <input type="text" class="form-control" id="vdChassis"
                                                placeholder="Enter Chassis No." required>
                                            <div class="invalid-feedback">
                                                Please Enter Chassis No.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="vdRegDate" class="form-label">Reg Date</label>
                                            <input type="date" class="form-control" id="vdRegDate" required>
                                            <div class="invalid-feedback">
                                                Please Enter a Registration Date.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="vdDepYears" class="form-label">Dep. Years</label>
                                            <input type="number" class="form-control" placeholder="Dep. Years"
                                                id="vdDepYears" required>
                                            <div class="invalid-feedback">
                                                Please Enter Depreciation Years.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="vdStation" class="form-label">Station</label>
                                            <select class="form-select" id="vdStation" required>
                                                <option selected disabled value="">Select Station</option>
                                                <!-- <option value="KHI">KHI</option>
                                                <option value="LHE">LHE</option>
                                                <option value="ISB">ISB</option> -->
                                                <!-- Add more options as needed -->
                                            </select>
                                            <div class="invalid-feedback">
                                                Please Select Station.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="vdICN" class="form-label">ICN</label>
                                            <input type="text" class="form-control" id="vdICN" placeholder="Enter ICN"
                                                required>
                                            <div class="invalid-feedback">
                                                Please Enter ICN.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label class="d-block mb-3">PUD/GTW</label>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="vdpudGtw" id="pud"
                                                    value="PUD" required checked>
                                                <label class="form-check-label" for="pud">PUD</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="vdpudGtw" id="pud"
                                                    value="GTW" required>
                                                <label class="form-check-label" for="gtw">GTW</label>
                                            </div>
                                            <div class="invalid-feedback">
                                                Please select PUD or GTW.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label class="d-block mb-3">Leased/Owned</label>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="vdleasedOwned"
                                                    id="leased" value="Leased" required checked>
                                                <label class="form-check-label" for="leased">Leased</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="vdleasedOwned"
                                                    id="owned" value="Owned" required>
                                                <label class="form-check-label" for="owned">Owned</label>
                                            </div>
                                            <div class="invalid-feedback">
                                                Please select Leased or Owned.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label class="d-block mb-3">AI Dash/MDVR</label>
                                            <select class="form-select" id="vdaiDashMDVR" required>
                                                <option value="">Select AI Dash/MDVR</option>
                                                <option value="AI">AI Dash</option>
                                                <option value="MDVR">MDVR</option>
                                                <option value="NONE">NONE</option>
                                            </select>
                                            <!-- <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="vdaiDashMDVR" id="ai"
                                                    value="AI" required checked>
                                                <label class="form-check-label" for="ai">AI Dash</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="vdaiDashMDVR"
                                                    id="mdvr" value="MDVR" required>
                                                <label class="form-check-label" for="mdvr">MDVR</label>
                                            </div> -->
                                            <div class="invalid-feedback">
                                                Please select AI Dash or MDVR.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="vdFuelType" class="form-label">Fuel Type</label>
                                            <select class="form-select" id="vdFuelType" required>
                                                <option selected disabled value="">Select Fuel Type</option>
                                                <option value="Petrol">Petrol</option>
                                                <option value="Diesel">Diesel</option>
                                                <option value="CNG">CNG</option>
                                                <!-- Add more options as needed -->
                                            </select>
                                            <div class="invalid-feedback">
                                                Please Select Fuel Type.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="vdPayload" class="form-label">Payload</label>
                                            <input type="number" class="form-control" id="vdPayload"
                                                placeholder="Enter Payload" required>
                                            <div class="invalid-feedback">
                                                Please Enter Payload.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="vdMaturityDate" class="form-label">Date of
                                                Maturity</label>
                                            <input type="date" class="form-control" id="vdMaturityDate" required>
                                            <div class="invalid-feedback">
                                                Please Enter a Date of Maturity.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Add Vehicle</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Edit Vehicle Modal -->
            <div class="modal fade" id="editVehicleDetail" data-bs-backdrop="static" data-bs-keyboard="false"
                tabindex="-1" role="dialog" aria-labelledby="editVehicleDetailLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editVehicleDetailLabel">Edit Vehicle Detail</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="editVehicleForm">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <input type="hidden" class="form-control" id="edvdVehID">
                                        <div class="mb-3">
                                            <label for="edvdVehRegNo" class="form-label">Vehicle Reg.
                                                No.</label>
                                            <input type="text" class="form-control" id="edvdVehRegNo"
                                                placeholder="Enter Vehicle Reg. No." required>
                                            <div class="invalid-feedback">
                                                Please Enter a Vehicle Reg. No.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="edvdMake" class="form-label">Make</label>
                                            <select class="form-select" id="edvdMake" required>
                                            </select>
                                            <div class="invalid-feedback">
                                                Please Select Make.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="edvdModel" class="form-label">Model</label>
                                            <select class="form-select" id="edvdModel" required>
                                            </select>
                                            <div class="invalid-feedback">
                                                Please select a valid model.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="edvdCategory" class="form-label">Category</label>
                                            <select class="form-select" id="edvdCategory" required>
                                            </select>
                                            <div class="invalid-feedback">
                                                Please select a valid category.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="edvdEngine" class="form-label">Engine</label>
                                            <input type="text" class="form-control" id="edvdEngine"
                                                placeholder="Enter Engine No." required>
                                            <div class="invalid-feedback">
                                                Please Enter Engine No.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="edvdChassis" class="form-label">Chassis</label>
                                            <input type="text" class="form-control" id="edvdChassis"
                                                placeholder="Enter Chassis No." required>
                                            <div class="invalid-feedback">
                                                Please Enter Chassis No.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="edvdRegDate" class="form-label">Reg Date</label>
                                            <input type="date" class="form-control" id="edvdRegDate" required>
                                            <div class="invalid-feedback">
                                                Please Enter a Registration Date.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="edvdDepYears" class="form-label">Dep. Years</label>
                                            <input type="number" class="form-control" placeholder="Dep. Years"
                                                id="edvdDepYears" required>
                                            <div class="invalid-feedback">
                                                Please Enter Depreciation Years.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="edvdStation" class="form-label">Station</label>
                                            <select class="form-select" id="edvdStation" required>
                                                <!-- Add more options as needed -->
                                            </select>
                                            <div class="invalid-feedback">
                                                Please Select Station.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="edvdICN" class="form-label">ICN</label>
                                            <input type="text" class="form-control" id="edvdICN" placeholder="Enter ICN"
                                                required>
                                            <div class="invalid-feedback">
                                                Please Enter ICN.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label class="d-block mb-3">PUD/GTW</label>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="edvdpudGtw" id="pud"
                                                    value="PUD" required>
                                                <label class="form-check-label" for="pud">PUD</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="edvdpudGtw" id="pud"
                                                    value="GTW" required>
                                                <label class="form-check-label" for="gtw">GTW</label>
                                            </div>
                                            <div class="invalid-feedback">
                                                Please select PUD or GTW.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label class="d-block mb-3">Leased/Owned</label>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="edvdleasedOwned"
                                                    id="leased" value="Leased" required>
                                                <label class="form-check-label" for="leased">Leased</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="edvdleasedOwned"
                                                    id="owned" value="Owned" required>
                                                <label class="form-check-label" for="owned">Owned</label>
                                            </div>
                                            <div class="invalid-feedback">
                                                Please select Leased or Owned.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label class="d-block mb-3">AI Dash/MDVR</label>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="edvdaiDashMDVR"
                                                    id="ai" value="AI" required>
                                                <label class="form-check-label" for="ai">AI Dash</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="edvdaiDashMDVR"
                                                    id="mdvr" value="MDVR" required>
                                                <label class="form-check-label" for="mdvr">MDVR</label>
                                            </div>
                                            <div class="invalid-feedback">
                                                Please select AI Dash or MDVR.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="edvdFuelType" class="form-label">Fuel Type</label>
                                            <select class="form-select" id="edvdFuelType" required>
                                                <option selected disabled value="">Select Fuel Type</option>
                                                <option value="Petrol">Petrol</option>
                                                <option value="Diesel">Diesel</option>
                                                <option value="CNG">CNG</option>
                                                <!-- Add more options as needed -->
                                            </select>
                                            <div class="invalid-feedback">
                                                Please Select Fuel Type.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="edvdPayload" class="form-label">Payload</label>
                                            <input type="number" class="form-control" id="edvdPayload"
                                                placeholder="Enter Payload" required>
                                            <div class="invalid-feedback">
                                                Please Enter Payload.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="edvdMaturityDate" class="form-label">Date of
                                                Maturity</label>
                                            <input type="date" class="form-control" id="edvdMaturityDate" required>
                                            <div class="invalid-feedback">
                                                Please Enter a Date of Maturity.
                                            </div>
                                        </div>
                                    </div>

                                </div>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Update</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="addVehicleFeatModal" data-bs-backdrop="static" data-bs-keyboard="false"
            tabindex="-1" role="dialog" aria-labelledby="addVehicleFeatModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addVehicleFeatModalLabel">Add Vehicle Features</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="row mx-4">
                        <div class="col-sm-12 mt-3">
                            <table class="table table-bordered" id='${tableId}' style="min-width:300px;">
                                <thead>
                                   <tr>
                                        <th style="font-size:13px;min-width:35px;max-width:35px">S.NO</th>
                                        <th style="font-size:13px;min-width:130px;max-width:130px">Make</th>
                                        <th style="font-size:13px;min-width:130px;max-width:130px">Model</th>
                                        <th style="font-size:13px;min-width:130px;max-width:130px">Category</th>
                                        <th style="font-size:13px;min-width:130px;max-width:130px">Creation Date</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <form id="vfForm">
                        <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="vfMake" class="form-label">Select Make</label>
                                    <input type="text" class="form-control" placeholder="Enter Make Name"
                                        id="vfMake" required>
                                </div>
                            </div>
                        </div>
                             
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        </div>

        <?php include 'include/footer.php';?>


    </div>
    </div>

    <?php include 'include/scripts.php';?>
    <script src="assets/js/customjs/vehicleDetail.js"></script>
</body>

</html>