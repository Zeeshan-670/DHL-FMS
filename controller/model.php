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
    $query= $conn->prepare("select * from model");
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
    $modelname = $_POST['modelname'] ?? '';
  
    if (empty($modelname)) {
        $arr['message'] = "model name cannot be empty.";
    }else{
        $dupCheck = $conn->prepare("select modelname from model where modelname = ?");
        $dupCheck->bind_param('s', $modelname);
        $dupCheck->execute();
    
        $result = $dupCheck->get_result();
        $fetch = $result->fetch_assoc(); 

        $response = [];

        if ($fetch > 0) {
                $arr["message"] = "Model Name already exists";
        }else{
            $creationdate = date("Y-m-d"); 
            $creationby = $_SESSION['user_id'];
            $query = $conn->prepare("INSERT INTO model(modelname,creationdate, creationby) VALUES ( ?, ?, ?)");
            if (!$query) {
                die("Query preparation failed: " . $conn->error); 
            }else{
                $query->bind_param('ssi',$modelname, $creationdate, $creationby);
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