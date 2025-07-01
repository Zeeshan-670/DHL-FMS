<?php $title = "User Registration"; ?>
<?php include 'include/header.php'; ?>

<?php
// var_dump($_SESSION);
// die;
?>

<body data-sidebar="dark" data-layout-mode="light"  class="vertical-collpsed"> 

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
                                                <h4 class="card-title mb-0">User & Vendor Details</h4>
                                                <p class="card-title-desc" style="font-weight: 400;font-size: 13px;">
                                                    Switch between user
                                                    and vendor details</p>
                                            </div>
                                        </h4>
                                        <div class="d-flex gap-3">
                                            <button type="button" class="btn btn-primary waves-effect waves-light"
                                                data-bs-toggle="modal" data-bs-target="#createUser">
                                                <i class="bx bx-plus font-size-16 align-middle me-2"></i>
                                                Create User
                                            </button>
                                            <button type="button" class="btn btn-primary waves-effect waves-light"
                                                data-bs-toggle="modal" data-bs-target="#createVendor">
                                                <i class="bx bx-plus font-size-16 align-middle me-2"></i>
                                                Create Vendor
                                            </button>
                                        </div>


                                    </div>



                                    <!-- Nav tabs -->
                                    <ul class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" data-bs-toggle="tab" href="#userDetailTab"
                                                role="tab">
                                                <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
                                                <span class="">User Detail</span>
                                            </a>
                                        </li>
                                        <li class="nav-item" onclick="adjustTable(`vendordetail`)">
                                            <a class="nav-link" data-bs-toggle="tab" href="#vendorDetailTab" role="tab">
                                                <span class="d-block d-sm-none"><i class="far fa-user"></i></span>
                                                <span class="">Vendor Detail</span>
                                            </a>
                                        </li>

                                    </ul>

                                    <!-- Tab panes -->
                                    <div class="tab-content p-3 text-muted">
                                        <div class="tab-pane active" id="userDetailTab" role="tabpanel">
                                            <div class=" mt-4">
                                                <table class="table table-editable table-nowrap table-edits w-100"
                                                    id="userdetail" style="min-width: 100%;font-size: 12px;">
                                                    <thead>
                                                        <tr>
                                                            <th>S.No</th>
                                                            <th style="display: none;">User Id</th>
                                                            <th>Actions</th>
                                                            <th>User Name</th>
                                                            <th>Name</th>
                                                            <th>Email</th>
                                                            <th>Password</th>
                                                            <th>City</th>
                                                            <th>Station</th>
                                                            <th>Designation</th>
                                                            <th>Access</th>
                                                            <th>Signature</th>

                                                        </tr>
                                                    </thead>
                                                    <tbody id="udTbody">
                                                        <!-- Rows will be dynamically populated -->
                                                    </tbody>
                                                </table>

                                            </div>
                                        </div>
                                        <div class="tab-pane" id="vendorDetailTab" role="tabpanel">
                                            <div class=" mt-4">
                                                <table class="table table-editable table-nowrap table-edits w-100"
                                                    id="vendordetail" style="min-width: 100%;font-size: 12px;">
                                                    <thead>
                                                        <tr>
                                                            <th>S.No</th>
                                                            <th style="display: none;">User Id</th>
                                                            <th>Actions</th>
                                                            <th>Vendor Name</th>
                                                            <th>User Name</th>
                                                            <th>Email</th>
                                                            <th>Password</th>
                                                            <th>Completed Jobs</th>
                                                            <th>Rating</th>
                                                            <th>Vendor Contact</th>
                                                            <th>City</th>
                                                            <th>Station</th>
                                                            <th>Vendor Address</th>

                                                        </tr>
                                                    </thead>
                                                    <tbody id="vdTbody">
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



                    <!-- create User -->

                    <div class="modal fade" id="createUser" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" role="dialog" aria-labelledby="createUserLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="createUserLabel">Create User</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">

                                    <form id="userForm" class="row justify-content-center">
                                        <div class="mb-3 col-lg-4 col-md-6">
                                            <label for="username" class="form-label">User Name</label>
                                            <input type="text" class="form-control" id="username"
                                                oninput="this.value = this.value.replace(/\s+/g, '')"
                                                placeholder="Enter Your User Name" required>
                                        </div>

                                        <div class="mb-3 col-lg-4 col-md-6">
                                            <label for="name" class="form-label">Name</label>
                                            <input type="text" class="form-control" id="name"
                                                placeholder="Enter Your Full Name" required>
                                        </div>
                                        <div class="mb-3 col-lg-4 col-md-6">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="text" class="form-control" id="email"
                                                placeholder="Enter Your Email" required>
                                        </div>

                                        <div class="col-lg-4 col-md-6">
                                            <div class="mb-3">
                                                <label for="password" class="form-label">Password</label>
                                                <input class="form-control password-container" type="password"
                                                    id="password" placeholder="Enter Your Password"
                                                    autocomplete="new-password" required
                                                    pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[\W_]).{8,}" title="Password must meet the following requirements:
                                                                - At least one number
                                                                - At least one uppercase letter
                                                                - At least one lowercase letter
                                                                - At least one special character
                                                                - Minimum 8 characters in length">
                                                <!-- Show/Hide Password Icon -->
                                                <span class="toggle-password" id="toggle-password"
                                                    onclick="toggleFunction('password')">
                                                    <i class="fas fa-eye"></i>
                                                </span>
                                            </div>
                                        </div>

                                        <div class="col-lg-4 col-md-6">
                                            <div class="mb-3">
                                                <label for="city" class="form-label">City</label>
                                                <select id="city" class="form-select" required>
                                                    <!-- Add city options here -->
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-lg-4 col-md-6">
                                            <div class="mb-3">
                                                <label for="station" class="form-label">Station</label>
                                                <select id="station" class="form-select" required>
                                                    <!-- Add station options here -->
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-lg-4 col-md-6">
                                            <div class="mb-3">
                                                <label for="designation" class="form-label">Designation</label>
                                                <select id="designation" class="form-select" required>
                                                    <!-- Add designation options here -->
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-lg-4 col-md-6">
                                            <div class="mb-3">
                                                <label for="access" class="form-label">Access</label>
                                                <select id="access" class="form-select" required>
                                                    <!-- Add access options here -->
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-lg-4 col-md-6 mb-3">
                                            <label for="signature" class="form-label">Signature</label>
                                            <input type="file" class="form-control" id="signature" accept="image/png">
                                        </div>
                                        <div class="row justify-content-center mt-3">
                                            <div class="col-md-4">
                                                <button type="submit" class="btn btn-primary w-100">Create
                                                    User</button>

                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="updateUser" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" role="dialog" aria-labelledby="updateUserLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="updateUserLabel">Update User</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">

                                    <form id="userUpdateForm" class="row justify-content-center">
                                        <div class="mb-3 col-lg-4 col-md-6">
                                            <label for="udusername" class="form-label">User Name</label>
                                            <input type="text" class="form-control" id="udusername"
                                                oninput="this.value = this.value.replace(/\s+/g, '')"
                                                placeholder="Enter Your User Name" required>
                                            <input type="hidden" class="form-control" id="udId">
                                        </div>

                                        <div class="mb-3 col-lg-4 col-md-6">
                                            <label for="udname" class="form-label">Name</label>
                                            <input type="text" class="form-control" id="udname"
                                                placeholder="Enter Your Full Name" required>
                                        </div>

                                        <div class="mb-3 col-lg-4 col-md-6">
                                            <label for="ududemail" class="form-label">Email</label>
                                            <input type="text" class="form-control" id="udemail"
                                                placeholder="Enter Your Full Email" required>
                                        </div>


                                        <div class="col-lg-4 col-md-6">
                                            <div class="mb-3">
                                                <label for="udpassword" class="form-label">Password</label>
                                                <input class="form-control password-container" type="password"
                                                    id="udpassword" placeholder="Enter Your Password"
                                                    autocomplete="new-password" required
                                                    pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[\W_]).{8,}" title="Password must meet the following requirements:
                                                                - At least one number
                                                                - At least one uppercase letter
                                                                - At least one lowercase letter
                                                                - At least one special character
                                                                - Minimum 8 characters in length">
                                                <!-- Show/Hide Password Icon -->
                                                <span class="toggle-password" id="toggle-password"
                                                    onclick="toggleFunction('udpassword')">
                                                    <i class="fas fa-eye"></i>
                                                </span>
                                            </div>
                                        </div>

                                        <div class="col-lg-4 col-md-6">
                                            <div class="mb-3">
                                                <label for="udcity" class="form-label">City</label>
                                                <select id="udcity" class="form-select" required>
                                                    <!-- Add city options here -->
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-lg-4 col-md-6">
                                            <div class="mb-3">
                                                <label for="udstation" class="form-label">Station</label>
                                                <select id="udstation" class="form-select" required>
                                                    <!-- Add station options here -->
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-lg-4 col-md-6">
                                            <div class="mb-3">
                                                <label for="uddesignation" class="form-label">Designation</label>
                                                <select id="uddesignation" class="form-select" required>
                                                    <!-- Add designation options here -->
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-lg-4 col-md-6">
                                            <div class="mb-3">
                                                <label for="udaccess" class="form-label">Access</label>
                                                <select id="udaccess" class="form-select" required>
                                                    <!-- Add access options here -->
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-lg-6 col-md-6 mb-3" style="display: flex;gap: 10px;">
                                            <label for="udsignature" class="form-label">Signature</label>

                                            <!-- Signature Image Preview -->
                                            <div class="signature-preview-container mb-2">
                                                <img id="udsignaturePreview" src="" alt="Signature Preview"
                                                    class="img-fluid" style="max-width: 130px; cursor: pointer;">
                                            </div>

                                            <!-- File Input for Uploading New Signature -->
                                            <input type="file" class="form-control" id="udsignature" accept="image/png"
                                                onchange="previewSignature()" style="display: none;">
                                        </div>
                                        <div class="row justify-content-center mt-3">
                                            <div class="col-md-4">
                                                <button type="submit" class="btn btn-primary w-100">Update
                                                    User</button>

                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="modal fade" id="createVendor" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" role="dialog" aria-labelledby="createVendorLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="createVendorLabel">Create Vendor</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="vendorForm" class="row justify-content-center">

                                        <!-- Vendor Name -->
                                        <div class="mb-3 col-lg-4 col-md-6">
                                            <label for="vendorname" class="form-label">Vendor Name</label>
                                            <input type="text" class="form-control" id="vendorname"
                                                placeholder="Enter Vendor Name" required>
                                        </div>



                                        <!-- Vendor Username -->
                                        <div class="mb-3 col-lg-4 col-md-6">
                                            <label for="vendorusername" class="form-label">Username</label>
                                            <input type="text" class="form-control" id="vendorusername"
                                                placeholder="Enter Vendor Username" required
                                                oninput="this.value = this.value.replace(/\s+/g, '')">
                                        </div>

                                        <div class="mb-3 col-lg-4 col-md-6">
                                            <label for="vendoremail" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="vendoremail"
                                                placeholder="Enter Your Email" required>
                                        </div>

                                        <!-- Vendor Password -->
                                        <div class="col-lg-4 col-md-6">
                                            <div class="mb-3">
                                                <label for="vendorpassword" class="form-label">Password</label>
                                                <input class="form-control password-container" type="password"
                                                    id="vendorpassword" placeholder="Enter Vendor Password" required
                                                    pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[\W_]).{8,}" title="Password must meet the following requirements:
                                                                - At least one number
                                                                - At least one uppercase letter
                                                                - At least one lowercase letter
                                                                - At least one special character
                                                                - Minimum 8 characters in length">
                                                <!-- Show/Hide Password Icon -->
                                                <span class="toggle-password" id="toggle-password"
                                                    onclick="toggleFunction('vendorpassword')">
                                                    <i class="fas fa-eye"></i>
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Vendor Contact -->
                                        <div class="mb-3 col-lg-4 col-md-6">
                                            <label for="vendorcontact" class="form-label">Vendor Contact</label>
                                            <input type="text" class="form-control" id="vendorcontact"
                                                placeholder="Enter Vendor Contact" required>
                                        </div>



                                        <!-- City -->
                                        <div class="col-lg-4 col-md-6">
                                            <div class="mb-3">
                                                <label for="vcity" class="form-label">City</label>
                                                <select id="vcity" class="form-select" required>
                                                    <!-- Add city options here -->
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Station -->
                                        <div class="col-lg-4 col-md-6">
                                            <div class="mb-3">
                                                <label for="vstation" class="form-label">Station</label>
                                                <select id="vstation" class="form-select" required>
                                                    <!-- Add station options here -->
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Vendor Address -->
                                        <div class="mb-3 col-lg-8 col-md-12">
                                            <label for="vendoraddress" class="form-label">Vendor Address</label>
                                            <textarea class="form-control" id="vendoraddress"
                                                placeholder="Enter Vendor Address" rows="1" required></textarea>
                                        </div>

                                        <!-- Submit Button -->
                                        <div class="row justify-content-center mt-3">
                                            <div class="col-md-4">
                                                <button type="submit" class="btn btn-primary w-100">Create
                                                    Vendor</button>
                                            </div>
                                        </div>

                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="modal fade" id="updateVendor" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" role="dialog" aria-labelledby="updateVendorLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="updateVendorLabel">Update Vendor</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="vendorUpdateForm" class="row justify-content-center">

                                        <!-- Vendor Name -->
                                        <div class="mb-3 col-lg-4 col-md-6">
                                            <label for="vevendorname" class="form-label">Vendor Name</label>
                                            <input type="text" class="form-control" id="vevendorname"
                                                placeholder="Enter Vendor Name" required>
                                            <input type="hidden" class="form-control" id="vevendorId">
                                        </div>



                                        <!-- Vendor Username -->
                                        <div class="mb-3 col-lg-4 col-md-6">
                                            <label for="vevendorusername" class="form-label">Username</label>
                                            <input type="text" class="form-control" id="vevendorusername"
                                                placeholder="Enter Vendor Username" required>
                                        </div>

                                        <div class="mb-3 col-lg-4 col-md-6">
                                            <label for="vevendoremail" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="vevendoremail"
                                                placeholder="Enter Your Email" required>
                                        </div>

                                        <!-- Vendor Password -->
                                        <div class="col-lg-4 col-md-6">
                                            <div class="mb-3">
                                                <label for="vevendorpassword" class="form-label">Password</label>
                                                <input class="form-control password-container" type="password"
                                                    id="vevendorpassword" placeholder="Enter Vendor Password" required
                                                    pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[\W_]).{8,}" title="Password must meet the following requirements:
                                                                - At least one number
                                                                - At least one uppercase letter
                                                                - At least one lowercase letter
                                                                - At least one special character
                                                                - Minimum 8 characters in length">
                                                <!-- Show/Hide Password Icon -->
                                                <span class="toggle-password" id="toggle-password"
                                                    onclick="toggleFunction('vevendorpassword')">
                                                    <i class="fas fa-eye"></i>
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Vendor Contact -->
                                        <div class="mb-3 col-lg-4 col-md-6">
                                            <label for="vevendorcontact" class="form-label">Vendor Contact</label>
                                            <input type="text" class="form-control" id="vevendorcontact"
                                                placeholder="Enter Vendor Contact" required>
                                        </div>



                                        <!-- City -->
                                        <div class="col-lg-4 col-md-6">
                                            <div class="mb-3">
                                                <label for="vecity" class="form-label">City</label>
                                                <select id="vecity" class="form-select" required>
                                                    <!-- Add city options here -->
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Station -->
                                        <div class="col-lg-4 col-md-6">
                                            <div class="mb-3">
                                                <label for="vestation" class="form-label">Station</label>
                                                <select id="vestation" class="form-select" required>
                                                    <!-- Add station options here -->
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Vendor Address -->
                                        <div class="mb-3 col-lg-8 col-md-12">
                                            <label for="vevendorAddress" class="form-label">Vendor Address</label>
                                            <textarea class="form-control" id="vevendorAddress"
                                                placeholder="Enter Vendor Address" rows="1" required></textarea>
                                        </div>

                                        <!-- Submit Button -->
                                        <div class="row justify-content-center mt-3">
                                            <div class="col-md-4">
                                                <button type="submit" class="btn btn-primary w-100">Update
                                                    Vendor</button>
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
    <script src="assets/js/customjs/userRegistration.js"></script>

</body>

</html>