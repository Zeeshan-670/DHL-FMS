<?php $title = "RFQ / Work Order History";?>



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
    input:read-only,
    textarea:read-only {
    border: 0;
    box-shadow: none;
    background-color: #ddd !important;
    }
    input:read-only:focus{
    border: 0;
    box-shadow: none;
    background-color: #ddd !important;
    }
    input:disabled ,
    textarea:disabled  {
    border: 0;
    box-shadow: none;
    background-color: #ddd !important;
    }
    input:disabled :focus{
    border: 0;
    box-shadow: none;
    background-color: #ddd !important;
    }
    .rating-container {
        display: flex;
        gap: 10px;
        font-size: 20px;
    }

    .rating-number {
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #ccc;
        cursor: pointer;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 500 !important;
    }

    .rating-number.active {
        background-color: #d40511;
        color: white;
        font-weight: bold;
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
                                <h5 class="my-3">RFQ Detail's</h1>
                                    <div class=" mt-4">
                                        <table class="table table-editable table-nowrap table-edits W-100"
                                            id="rfqDetailTable" style="font-size: 12px;">
                                            <thead>
                                                <tr>
                                                    <th style="max-width:30px;">S.no</th>
                                                    <th>Status</th>
                                                    <th>Date</th>
                                                    <th>Reg No</th>
                                                    <th>Job Title</th>
                                                    <th>Job Description</th>
                                                    <th>Grand Total</th>
                                                    <th>Station</th>
                                                    <th>Reason</th>
                                                    <th>Createdby</th>
                                                    <th>WorkOrder</th>
                                                    <th>Invoice</th>
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

                    <!-- Modal Structure -->
                    <div class="modal fade" id="statusModal" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl modal-dialog-centered">
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
                    <div class="modal fade" id="chainStatusModal" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="chainStatusModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="chainStatusModalLabel"></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body pb-0" id="chainStatusmodalBodyContent">

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="reasonModal" style="    background: #00000059;" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="reasonModalLabel"></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <label>Reason:</label>
                                <textarea class="form-control" id="reasonInput"></textarea>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-primary" id="reasonSubmitButton">Submit</button>
                            </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="recordExpense" style="background: #00000059;" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="recordExpenseLabel">Record Expense & Forward</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <label>Email:</label>
                                <input type="email" id="recordExpenseEmail" class="form-control" value="jawaid.khalid@dhl.com" disabled> 
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-primary" id="recordExpenseBtn" onclick="recordExpense()">Record Expense & Forward</button>
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
     <!-- <script src="assets/js/customjs/workorderHistory.js"></script> -->
    <script src="assets/js/customjs/rfqHistory.js"></script>
</body>

</html>