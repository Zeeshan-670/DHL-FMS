<?php
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
    add($conn,$arr);
}


function add($conn, &$arr){
    $remarks = $_POST['remarks'] ?? '';
    $date = $_POST['date'] ?? '';

    if (empty($remarks)) {
        $arr['message'] = "Remarks cannot be empty.";
    }else{
        $creationdate = date("Y-m-d"); 
        $creationby = $_SESSION['user_id'];
        $query = $conn->prepare("INSERT INTO make(remarks,addDate,creationdate, creationby) VALUES (?, ?, ?, ?)");
        if (!$query) {
            die("Query preparation failed: " . $conn->error); 
        }else{
            $query->bind_param('sssi',$remarks,$date, $creationdate, $creationby);
            $exe=$query->execute();

            if($exe === false){
                $arr["message"] = "Error: " . mysqli_error($conn);
                $arr["success"]=false;
            }else{
                $arr["success"]=true;
                $arr["message"] = "Data inserted Successfully!";
                $arr["data"] = "";
            }
        }
    }
}

echo json_encode($arr);
?>