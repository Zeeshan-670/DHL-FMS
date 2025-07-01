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
                                                    <th>Job Title</th>
                                                    <th>Reg No</th>
                                                    <th>Reason</th>
                                                    <th>WorkOrder</th>
                                                    <!-- <th>Invoice</th> -->
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
                                    <h5 class="modal-title" id="statusModalLabel"></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body pb-0 pt-0" id="modalBodyContent">
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