<?php $title = "Driver"; ?>
<?php include 'include/header.php'; ?>


<body data-sidebar="dark" data-layout-mode="light" class="vertical-collpsed">

    <style>
    a.btn.btn-action {
        font-size: 22px;
        padding: 2px 4px;
        opacity: 0.750;
        transition: 0.5s;
    }

    a.btn.btn-action.accept {
        color: #368d1a;
    }

    a.btn.btn-action.reject {
        color: #f10b0b;
    }

    a.btn.btn-action:hover {
        opacity: 1;
        background: none;
        border-color: transparent;
    }

    .password-container {
        position: relative;
    }
    
    .toggle-password {
        position: absolute;
        right: 15px;
        top: 58%;
        transform: translateY(-50%);
        cursor: pointer;
        background: white;
        padding: 0 10px;
    }
    .select2-container{
        z-index: 1000000 !important;
        min-width: 100% !important;
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
    background-color: #D40511;
    color: white;
}
input.select2-search__field {
    min-width: 100% !important;
    width: 100% !important;
}
    </style>

    <div id="layout-wrapper">
        <!-- Header Include Here -->
        <?php include 'include/menu.php'; ?>
        <!-- Left Sidebar End -->
        <?php include 'include/sidebar.php'; ?>
        <div class="main-content" id="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    <!-- include breadcrumb -->
                    <?php include 'include/breadcrumb.php'; ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <div
                                        style="display: flex;justify-content: space-between;align-items: center;flex-wrap: wrap;">
                                        <h4 class="card-title">
                                            <div>
                                                <h4 class="card-title mb-0">Driver</h4>
                                               
                                            </div>
                                        </h4>
                                        <div class="d-flex gap-3">
                                            <button type="button" class="btn btn-primary waves-effect waves-light"
                                                data-bs-toggle="modal" data-bs-target="#addDriver">
                                                <i class="bx bx-plus font-size-16 align-middle me-2"></i>
                                                Add Driver
                                            </button>
                                        </div>
                                    </div>
                                    <div class=" mt-4">
                                        <table class="table table-editable table-nowrap table-edits w-100"
                                            id="driverDetail" style="min-width: 100%;font-size: 12px;">
                                            <thead>
                                                <tr>
                                                    <th>S.No</th>
                                                    <th style="display: none;">Driver Id</th>
                                                    <th>Actions</th>
                                                    <th>Driver Name</th>
                                                    <th>CNIC</th>
                                                    <th>LTV</th>
                                                    <th>License NO</th>
                                                    <th>Category</th>
                                                    <th>Validity</th>
                                                </tr>
                                            </thead>
                                            <tbody id="diverTbody">
                                                <!-- Rows will be dynamically populated -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>



                    <!-- create User -->

                    <div class="modal fade" id="addDriver" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" role="dialog" aria-labelledby="addDriverLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addDriverLabel">Add Driver</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">

                                    <form id="driverForm" class="row justify-content-center">
                                        <div class="mb-3 col-lg-4 col-md-6">
                                            <label for="driverid" class="form-label">Driver ID</label>
                                            <input type="text" class="form-control" id="driverid"
                                                oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..?)\../g, '$1');"
                                                placeholder="Enter Driver ID">
                                        </div>

                                        <div class="mb-3 col-lg-4 col-md-6">
                                            <label for="driverName" class="form-label">Driver Name</label>
                                            <input type="text" class="form-control" id="driverName"
                                                placeholder="Enter Driver Name">
                                        </div>
                                        <div class="mb-3 col-lg-4 col-md-6">
                                            <label for="cnic" class="form-label">CNIC</label>
                                            <input type="text" class="form-control" id="cnic"
                                                placeholder="Enter Driver Cnic" data-inputmask="'mask': '99999-9999999-9'"
                                                placeholder="__-__-_">
                                        </div>

                                        <div class="col-lg-4 col-md-6">
                                            <div class="mb-3">
                                                <label for="LicenseType" class="form-label">LTV</label>
                                                <select id="LicenseType" class="form-select">
                                                    <!-- Add city options here -->
                                                     <option value="">--Select LTV--</option>
                                                     <option value="Yes">Yes</option>
                                                     <option value="No">No</option>
                                                     <option value="Learner">Learner</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-lg-4 col-md-6">
                                            <div class="mb-3">
                                                <label for="licenseNo" class="form-label">License No</label>
                                                <input type="text" class="form-control" id="licenseNo"
                                                placeholder="Enter Driver License No">
                                            </div>
                                        </div>

                                        <div class="col-lg-4 col-md-6">
                                            <div class="mb-3">
                                                <label for="driverCategory" class="form-label">Category</label>
                                                <!-- <div class='row'> -->
                                                    <select id="driverCategory" name="driverCategory[]" class='form-control' multiple="multiple">
                                                    <option value="M-CAR">M-CAR</option>
                                                        <option value="M-CYCLE">M-CYCLE</option>
                                                        <option value="M-JEEP">M-JEEP</option>
                                                        <option value="LTV">LTV</option>
                                                        <option value="HTV">HTV</option>
                                                        <option value="Motor Bike">Motor Bike</option>
                                                    </select>
                                                <!-- </div> -->
                                            </div>
                                        </div>

                                        <div class="col-lg-4 col-md-6">
                                            <div class="mb-3">
                                                <label for="validity" class="form-label">Validity</label>
                                               <input type="date" class="form-control"  id="validity">
                                            </div>
                                        </div>

                                        
                                        <div class="row justify-content-center mt-3">
                                            <div class="col-md-4">
                                                <button type="submit" class="btn btn-primary w-100">Create
                                                    Driver</button>

                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                     <div class="modal fade" id="editDriver" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" role="dialog" aria-labelledby="editDriverLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editDriverLabel">Edit Driver</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">

                                    <form id="eddriverForm" class="row justify-content-center">
                                        <div class="mb-3 col-lg-4 col-md-6">
                                            <label for="eddriverid" class="form-label">Driver ID</label>
                                            <input type="text" class="form-control" id="eddriverid"
                                                oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..?)\../g, '$1');"
                                                placeholder="Enter Driver ID">
                                        </div>

                                        <div class="mb-3 col-lg-4 col-md-6">
                                            <label for="eddriverName" class="form-label">Driver Name</label>
                                            <input type="text" class="form-control" id="eddriverName"
                                                placeholder="Enter Driver Name">
                                        </div>
                                        <div class="mb-3 col-lg-4 col-md-6">
                                            <label for="edcnic" class="form-label">CNIC</label>
                                            <input type="text" class="form-control" id="edcnic"
                                                data-inputmask="'mask': '99999-9999999-9'"
                                                placeholder="__-__-_">
                                        </div>

                                        <div class="col-lg-4 col-md-6">
                                            <div class="mb-3">
                                                <label for="edLicenseType" class="form-label">LTV</label>
                                                <select id="edLicenseType" class="form-select">
                                                    <!-- Add city options here -->
                                                     <option value="">--Select LTV--</option>
                                                     <option value="Yes">Yes</option>
                                                     <option value="No">No</option>
                                                     <option value="Learner">Learner</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-lg-4 col-md-6">
                                            <div class="mb-3">
                                                <label for="edlicenseNo" class="form-label">License No</label>
                                                <input type="text" class="form-control" id="edlicenseNo"
                                                placeholder="Enter Driver License No">
                                            </div>
                                        </div>

                                        <div class="col-lg-4 col-md-6">
                                            <div class="mb-3">
                                                <label for="eddriverCategory" class="form-label">Category</label>
                                                <!-- <div class='row'> -->
                                                    <select id="eddriverCategory" name="driverCategory[]" class='form-control' multiple="multiple">
                                                    <option value="M-CAR">M-CAR</option>
                                                        <option value="M-CYCLE">M-CYCLE</option>
                                                        <option value="M-JEEP">M-JEEP</option>
                                                        <option value="LTV">LTV</option>
                                                        <option value="HTV">HTV</option>
                                                        <option value="Motor Bike">Motor Bike</option>
                                                    </select>
                                                <!-- </div> -->
                                            </div>
                                        </div>

                                        <div class="col-lg-4 col-md-6">
                                            <div class="mb-3">
                                                <label for="edvalidity" class="form-label">Validity</label>
                                               <input type="date" class="form-control"  id="edvalidity">
                                            </div>
                                        </div>

                                        
                                        <div class="row justify-content-center mt-3">
                                            <div class="col-md-4">
                                                <button type="submit" class="btn btn-primary w-100">Edit
                                                    Driver</button>

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
        <?php include 'include/footer.php'; ?>
    </div>
    <!-- end main content-->
    </div>
    <!-- END layout-wrapper -->

    <!-- JAVASCRIPT -->
    <?php include 'include/scripts.php'; ?>
    <script src="assets/js/customjs/driver.js"></script>

</body>

</html>