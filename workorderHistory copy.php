<?php $title = "Job / Work Order History";?>

<?php include 'include/header.php';?>

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


                                <ul class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist">
                                        <li class="nav-item" onclick="getJobDataApproval()">
                                            <a class="nav-link active" data-bs-toggle="tab" href="#jobHistoryTab"
                                                role="tab">
                                                <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
                                                <span class="">Job History</span>
                                            </a>
                                        </li>
                                        <li class="nav-item" onclick="getWorkOrderDataApproval()">
                                            <a class="nav-link" data-bs-toggle="tab" href="#workorderHistoryTab" role="tab">
                                                <span class="d-block d-sm-none"><i class="far fa-user"></i></span>
                                                <span class="">WorkOrder History</span>
                                            </a>
                                        </li>

                                    </ul>

                                    <!-- Tab panes -->
                                    <div class="tab-content p-3 text-muted">
                                        <div class="tab-pane active" id="jobHistoryTab" role="tabpanel">
                                            <div class=" mt-4">
                                                <table class="table table-editable table-nowrap table-edits W-100"
                                                    id="jobHistoryTable" style="font-size: 12px;"> 
                                                    <thead>
                                                        <tr>
                                                            <th style="max-width:30px;">S.no</th>
                                                            <th>Status</th>
                                                            <th>Date</th>
                                                            <th>Job Title</th>
                                                            <th>Reg No</th>
                                                            <th>Amount</th>
                                                            <th>Vendor Description</th>
                                                            <th>Created By</th>
                                                            <th>DHL Description</th>
                                                            <th>Reason</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="jdTbody">
                                                    </tbody>
                                                </table>

                                            </div>
                                        </div>
                                        <div class="tab-pane" id="workorderHistoryTab" role="tabpanel">
                                            <div class=" mt-4">
                                                <table class="table table-editable table-nowrap table-edits W-100"
                                                    id="workOrderHistoryTable" style="font-size: 12px;">
                                                    <thead>
                                                        <tr>
                                                            <th style="max-width:30px;">S.no</th>
                                                            <th>Status</th>
                                                            <th>Date</th>
                                                            <th>Vehicle Reg</th>
                                                            <th>Job Title</th>
                                                            <th>Services</th>
                                                            <th>Amount</th>
                                                            <th>Reason</th>
                                                            <th>Work Order</th>
                                                            <th>Invoice</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="vdTbody">

                                                </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>


                                    <!-- <div class="w-100 mt-4">

                                        <table class="table table-editable table-nowrap table-edits w-100"
                                            id="workorderHistory">
                                            <thead>
                                                <tr>
                                                    <th style="max-width:30px;">S.no</th>
                                                    <th>Status</th>
                                                    <th>Date</th>
                                                    <th>Vehicle Reg</th>
                                                    <th>Job Title</th>
                                                    <th>Services</th>
                                                    <th>Amount</th>
                                                    <th>Reason</th>
                                                    <th>Work Order</th>
                                                    <th>Invoice</th>
                                                </tr>
                                            </thead>
                                            <tbody id="vdTbody">

                                            </tbody>
                                        </table>

                                    </div> -->

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
                                    <h5 class="modal-title" id="statusModalLabel">Job Status</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body pb-0" id="modalBodyContent">

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
    <script src="assets/js/customjs/workorderHistory.js"></script>
</body>

</html>