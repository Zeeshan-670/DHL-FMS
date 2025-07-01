<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json"); 
include("database.php");

$arr = [
    "message" => "", 
    "success" => false,  
    "data" => []  
];

$conn = dbconnection();

if ($conn === false) {
    $arr['message'] = 'DB connecttion failed';
    $arr['success'] = false;
    $arr['error'] = sqlsrv_errors();
    echo json_encode($arr);
    exit;
}else{
    if(isset($_SESSION['user_id'])){
        getAvg($conn, $arr, $_POST['vendor_id']);            
    }else{
        $arr['message'] = "Unauthorized access";
    }
}
//get jobSatisfaction checkboxes data
function getAvg($conn, &$arr, $vendor_id){
    $query = $conn->prepare("SELECT  ROUND(AVG(rating), 2) AS avg_rating FROM rating WHERE vendor_id = ?");
    $query->bind_param('i', $vendor_id);
    $query->execute();
    $query->bind_result($avg_rating);
    $query->fetch();
    $query->close();
    
    $arr['message'] = "Success";
    $arr['success'] = true;
    $arr['data'] = number_format($avg_rating, 2);
    
}
echo json_encode($arr);
?> 