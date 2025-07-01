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
    if(isset($_SESSION['user_id'])){
        if(isset($_POST['type'])){
            $type = $_POST['type'];
            if($type == "add"){
            add($conn, $arr);
            }else if($type == "view"){
                view($conn, $arr);
            }else{
                $arr['message'] = "Invalid type";
            }
        }else{
            $arr['message'] = "Type required";
        }
    }else{
        $arr['message'] = "Unauthorized access";
    }
}


function view($conn, &$arr){
    if($_SESSION['access'] == 'Full' || $_SESSION['access'] == 'full'){
        $query= $conn->prepare("select * from station");
    }else{
        $query= $conn->prepare("select * from station where stationname =  ?");
        $query->bind_param('s', $_SESSION['access']);

    }
    // $query= $conn->prepare("select * from station");
    $query->execute();
    $exe = $query->get_result();
    $result = $exe->fetch_all(MYSQLI_ASSOC);

    if($result === false){
        $arr["message"] = "Error: " . mysqli_error($conn);
        $arr["success"]=false;

    }else{
        if($result > 0){
            $arr["success"]=true;
            $arr["data"] = $result; 
    }else{
        $arr["message"] = "Error: No data";
        $arr["success"]=false;
    }
    }
}

function add($conn, &$arr){
    $stationname = $_POST['stationname'] ?? '';
  
    if (empty($stationname)) {
        $arr['message'] = "station name cannot be empty.";
    }else{
        $dupCheck = $conn->prepare("select stationname from station where stationname = ?");
        $dupCheck->bind_param('s', $stationname);
        $dupCheck->execute();
    
        $result = $dupCheck->get_result();
        $fetch = $result->fetch_assoc(); 

        $response = [];

        if ($fetch > 0) {
                $arr["message"] = "station Name already exists";
        }else{
            $creationdate = date("Y-m-d"); 
            $creationby = $_SESSION['user_id'];
            $query = $conn->prepare("INSERT INTO station(stationname,creationdate, creationby) VALUES ( ?, ?, ?)");
            if (!$query) {
                die("Query preparation failed: " . $conn->error); 
            }else{
                $query->bind_param('ssi',$stationname, $creationdate, $creationby);
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
}

echo json_encode($arr);
?>