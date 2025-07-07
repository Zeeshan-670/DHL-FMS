<?php $title = "Expense History";?>
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

                    <h5 class="my-3">Expense History</h1>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <form id="getHistory">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label class="form-label">Select Vehicle</label>
    
                                                    <select class="form-control select2"
                                                    id="vehicleSelect" name="driverCategory[]">
                                                        
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label class="form-label">Start Date</label>
                                                    <input type="date" class="form-control" id="startDate">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label class="form-label">End Date</label>
                                                    <input type="date" class="form-control" id="EndDate">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label class="form-label">Filter</label>
                                                  <select class="form-control" id="filterBy">
                                                    <option value="">Select Filter By</option>
                                                    <option value="new">New Data</option>
                                                    <option value="old">Old Data</option>
                                                  </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12 text-end">
                                                    <button type="submit" class="btn btn-primary">Get Expense Data</button>
                                            </div>
                                        </div>

                                    
                                </form>

                                <div class="row mt-4" id="historyTableContainer" style="display: none;">
                                    <div class=" mt-4">
                                        <table class="table w-100" id="expenseHistroyTable" style="font-size: 12px;">
                                            <thead>
                                                <tr>
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

    <script src="assets/js/customjs/expenseHistory.js"></script>
</body>

</html>