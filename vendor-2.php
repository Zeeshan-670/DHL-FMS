<?php $title = "Vendor Approval";?>
<?php include 'include/header.php';?>

<?php 
$userId = $_SESSION['user_id'];
// var_dump($_SESSION);
// die;
?>


<script>
let vendorIID = <?php echo json_encode($userId); ?>;
</script>

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
    </style>

    <div id="layout-wrapper">
        <!-- Header Include Here -->
        <?php include 'include/menu.php';?>
        <!-- Left Sidebar End -->
        <?php include 'include/sidebar.php';?>
        <div class="main-content" id="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    <!-- include breadcrumb -->
                    <?php include 'include/breadcrumb.php';?>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">

                                <!-- Nav tabs -->
                                    <ul class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist">
                                        <li class="nav-item" onclick="getJobDataForApproval()">
                                            <a class="nav-link active" data-bs-toggle="tab" href="#jobDetailTab"
                                                role="tab">
                                                <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
                                                <span class="">Job Detail</span>
                                            </a>
                                        </li>
                                        <li class="nav-item" onclick="getDataForApproval();">
                                            <a class="nav-link" data-bs-toggle="tab" href="#workorderDetailTab" role="tab">
                                                <span class="d-block d-sm-none"><i class="far fa-user"></i></span>
                                                <span class="">WorkOrder Detail</span>
                                            </a>
                                        </li>

                                    </ul>

                                    <!-- Tab panes -->
                                    <div class="tab-content p-3 text-muted">
                                        <div class="tab-pane active" id="jobDetailTab" role="tabpanel">
                                            <div class=" mt-4">
                                                <table class="table table-editable table-nowrap table-edits W-100"
                                                    id="jobDetailTable" style="font-size: 12px;">
                                                    <thead>
                                                        <tr>
                                                            <th style="max-width:30px;">S.no</th>
                                                            <th>Status</th>
                                                            <th>Date</th>
                                                            <th>Job Title</th>
                                                            <th>Reg No</th>
                                                            <th>Dhl Description</th>
                                                            <th>Reason</th>
                                                            <th>Quotation</th>
                                                            <th>Vendor Description</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>

                                            </div>
                                        </div>
                                        <div class="tab-pane" id="workorderDetailTab" role="tabpanel">
                                            <div class=" mt-4">
                                                <table class="table table-editable table-nowrap table-edits W-100"
                                                    id="workOrderDetailTable" style="font-size: 12px;">
                                                    <thead>
                                                        <tr>
                                                            <th style="max-width:30px;">S.no</th>
                                                            <th>Status</th>
                                                            <th>Date</th>
                                                            <th>Job Title</th>
                                                            <th>Services</th>
                                                            <th>Work Order</th>
                                                            <!-- <th>Station</th>
                                                            <th>Vendor</th> -->


                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Structure -->
                    <div class="modal fade" id="statusModal" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="statusModalLabel">Accept Work Order </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body pb-0 pt-0" id="modalBodyContent">
                                    <!-- <form id="statusForm">
                                        <div class="mb-3">
                                            <label for="invoice" class="form-label">Upload Invoice</label>
                                            <input class="form-control" type="file" id="invoice" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="invoiceAmount" class="form-label">Enter Amount in (Pkr)</label>
                                            <input type="text" class="form-control" id="invoiceAmount"
                                                placeholder="Enter Amount in (Pkr)" required
                                                oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..?)\../g, '$1');">
                                        </div>
                                        <div class="mb-3">
                                            <label for="reasonforReject" class="form-label">Enter Reason for
                                                Reject</label>
                                            <textarea id="reasonforReject" class="form-control" maxlength="225" rows="3"
                                                placeholder="Enter Reason for Reject" style="height: 83px;"></textarea>
                                        </div>

                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </form> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include 'include/footer.php';?>
    </div>
    <!-- end main content-->
    </div>
    <!-- END layout-wrapper -->

    <!-- JAVASCRIPT -->
    <?php include 'include/scripts.php';?>
    <script src="assets/js/customjs/genrateworkorder.js"></script>
    <script src="assets/js/customjs/vendorapproval.js"></script>

</body>

</html>