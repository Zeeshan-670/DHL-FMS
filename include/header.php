<?php
// Start session
session_start();
include('accessControl.php'); 

// var_dump($_SESSION);
// die;

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$maxAmount = isset($_SESSION['max']) ? $_SESSION['max'] : null;
$designation = isset($_SESSION['designation']) ? $_SESSION['designation'] : null;
$userName = isset($_SESSION['name']) ? $_SESSION['name'] : null;

?>

<script>
let maxAmount = <?php echo json_encode($maxAmount); ?>;
let designation = <?php echo json_encode($designation); ?>;
let userName = <?php echo json_encode($userName); ?>;
console.log('maxAmount');
console.log(maxAmount);
console.log(designation);
console.log(userName);
</script>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>DHL (FMS) <?php echo isset($title) ?  '| '.$title : ''; ?></title>
    <link rel="shortcut icon" href="assets/img/favicon.ico" />

    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- Bootstrap Css -->
    <link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <link href="assets/css/custom.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <link href="assets/css/datatableScroll.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">

    <!-- <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css"> -->

    <!-- DataTables -->
    <link href="assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />

    <!-- Responsive datatable examples -->
    <link href="assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" />   
    <link href="assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- <script src="assets/libs/jquery/jquery.min.js"></script> -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>


    <script src="assets/libs/jquery/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/libs/metismenu/metisMenu.min.js"></script>
    <script src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/3/jquery.inputmask.bundle.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/lobibox@1.2.7/dist/css/lobibox.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/lobibox@1.2.7/dist/js/lobibox.min.js"></script>
    <script>
    // document.documentElement.style.setProperty('--current-domain', `'${window.location.hostname} /'`);
    </script>

</head>