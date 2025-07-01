<?php $title = "My Details";?>
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
        <div class="main-content" id="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    <?php include 'include/breadcrumb.php';?>
                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-body">


                                    <div class="mt-4">
                                        <div id="serviceOrderForm">
                                            <div class="row mb-2">
                                                <div class="col-md-6 my-3">
                                                    <label class="form-label" for="name">Name</label>
                                                    <input type="text" id="name" class="form-control" disabled
                                                        placeholder="Name">
                                                </div>
                                                <div class="col-md-6 my-3">
                                                    <label class="form-label" for="username">User Name</label>
                                                    <input type="text" id="username" class="form-control" disabled
                                                        placeholder="User Name">
                                                </div>
                                                <!-- <div class="col-md-6 my-3">
                                                    <label class="form-label" for="password">Password</label>
                                                    <input type="text" id="password" class="form-control" disabled>
                                                </div> -->
                                                <div class="col-md-6 my-3">
                                                    <label class="form-label" for="city">City</label>
                                                    <input type="text" id="city" class="form-control" disabled
                                                        placeholder="City Name">
                                                </div>
                                                <div class="col-md-6 my-3">
                                                    <label class="form-label" for="station">Station</label>
                                                    <input type="text" id="station" class="form-control" disabled
                                                        placeholder="Station Name">
                                                </div>
                                                <div class="col-md-6 my-3">
                                                    <label class="form-label" for="designation">Designation</label>
                                                    <input type="text" id="designation" class="form-control" disabled
                                                        placeholder="Designation">
                                                </div>
                                                <div class="col-md-6 my-3">
                                                    <label class="form-label" for="access">Access</label>
                                                    <input type="text" id="access" class="form-control" disabled
                                                        placeholder="Access">
                                                </div>

                                            </div>

                                            <div class="row">
                                                <div class="col-md-12 text-end my-4">
                                                    <button class="btn btn-primary" data-bs-toggle="modal"
                                                        data-bs-target="#exampleModal">Change
                                                        Password</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <form id="updatePassword">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="exampleModalLabel">Update Password</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label for="curPassword" class="form-label">Current Password</label>
                                                    <input class="form-control password-container" type="password"
                                                        id="curPassword" name="curPassword"
                                                        placeholder="Enter Your Password" autocomplete="new-password"
                                                        required>
                                                    <!-- Show/Hide Password Icon -->
                                                    <span class="toggle-password"
                                                        onclick="toggleFunction('curPassword')">
                                                        <i class="fas fa-eye"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label for="newPassword" class="form-label">New Password</label>
                                                    <input class="form-control password-container" type="password"
                                                        id="newPassword" name="newPassword"
                                                        placeholder="Enter Your Password" autocomplete="new-password"
                                                        required
                                                        pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[\W_]).{8,}" title="Password must meet the following requirements:
                                    - At least one number
                                    - At least one uppercase letter
                                    - At least one lowercase letter
                                    - At least one special character
                                    - Minimum 8 characters in length">
                                                    <!-- Show/Hide Password Icon -->
                                                    <span class="toggle-password"
                                                        onclick="toggleFunction('newPassword')">
                                                        <i class="fas fa-eye"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Save Password</button>
                                    </div>
                                </form>
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

    <script src="assets/js/customjs/mydetail.js"></script>
</body>

</html>