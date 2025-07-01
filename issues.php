<?php $title = "Issues";?>
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

                    <h5 class="my-3">Issues</h1>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <form id="createIssue">
                                        <div class="row">
                                            <!-- <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label class="form-label">Select Vehicle</label>
    
                                                    <select class="form-control select2"
                                                    id="vehicleSelect" name="driverCategory[]">
                                                        
                                                    </select>
                                                </div>
                                            </div> -->
                                            <div class="col-md-2">
                                                <div class="mb-3">
                                                    <label class="form-label">Select Date</label>
                                                    <input type="datetime-local" class="form-control" id="selectDate" disabled>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Remarks</label>
                                                    <textarea  id="remarks" class="form-control" placeholder="Enter Remarks" rows="1" maxlength="512"></textarea>
                                                </div>
                                            </div>
                                            <!-- <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label class="form-label">End Date</label>
                                                    <input type="date" class="form-control" id="EndDate">
                                                </div>
                                            </div> -->
                                            <!-- <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label class="form-label">Filter</label>
                                                  <select class="form-control" id="filterBy">
                                                    <option value="">Select Filter By</option>
                                                    <option value="newData">New Data</option>
                                                    <option value="oldData">Old Data</option>
                                                  </select>
                                                </div>
                                            </div> -->
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12 text-end">
                                                    <button type="submit" class="btn btn-primary">Raise Issue</button>
                                            </div>
                                        </div>

                                    
                                </form>

                                <div class="row mt-4" id="historyTableContainer">
                                    <div class=" mt-4">
                                        <table class="table w-100 text-center" id="expenseHistroyTable" style="font-size: 12px;">
                                            <thead>
                                                <tr>
                                                    <th>S.No</th>
                                                    <th>Status</th>
                                                    <th>Creator</th>
                                                    <th>Assign</th>
                                                    <th>Date</th>
                                                    <th>Remarks</th>
                                                </tr>
                                            </thead>
                                            <tbody id="expenseHistroyTbody">
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

        </div>
        <?php include 'include/footer.php';?>
    </div>
    </div>
    <!-- JAVASCRIPT -->
    <?php include 'include/scripts.php';?>

    <script src="assets/js/customjs/issues.js"></script>
</body>

</html>