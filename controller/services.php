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
            }
            
            else if($type == "update"){
                update($conn, $arr);
            }
            else if($type == "delete"){
                delete($conn, $arr);
            }
            else{
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
    $query= $conn->prepare("select * from services");
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
    $servicesname = $_POST['servicesname'] ?? '';
  
    if (empty($servicesname)) {
        $arr['message'] = "Services name  cannot be empty.";
    }else{
        $dupCheck = $conn->prepare("select * from services where servicesname = ?");
        $dupCheck->bind_param('s', $servicesname);
        $dupCheck->execute();
    
        $result = $dupCheck->get_result();
        $fetch = $result->fetch_assoc(); 

        $response = [];

        if ($fetch > 0) {
                $arr["message"] = "Services name already exists";
        }else{
            $creationdate = date("Y-m-d"); 
            $creationby = $_SESSION['user_id'];
            $query = $conn->prepare("INSERT INTO services(servicesname,creationdate, creationby) VALUES ( ?, ?, ?)");
            if (!$query) {
                die("Query preparation failed: " . $conn->error); 
            }else{
                $query->bind_param('ssi',$servicesname, $creationdate, $creationby);
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



function update($conn, &$arr){
    $servicesid = $_POST['servicesid'] ?? '';
    $servicesname = $_POST['servicesname'] ?? '';
   
    if (empty($servicesid) && empty($servicesname)){
        $arr['message'] = "Field cannot be empty.";
    }else{
       
        $creationdate = date("Y-m-d h:m::s"); 
        $creationby = $_SESSION['user_id'];
        $query = $conn->prepare("Update services set servicesname = ? , modificationdate =?, modificationby=? where servicesid = ?");
        if (!$query) {
            die("Query preparation failed: " . $conn->error); 
        }else{
            $query->bind_param('ssii',$servicesname,$creationdate,$creationby,$servicesid);
            $exe=$query->execute();

            if($exe === false){
                $arr["message"] = "Error: " . mysqli_error($conn);
                $arr["success"]=false;
            }else{
                $arr["success"]=true;
                $arr["message"] = "Services  updated Successfully!";
                $arr["data"] = "";
            }
        }
    
    }
}



function delete($conn, &$arr){
    $servicesid = $_POST['servicesid'] ?? '';
    if (empty($servicesid)){
        $arr['message'] = "Field cannot be empty.";
    }else{
       
      
        $query = $conn->prepare("Delete from services where servicesid = ?");
        if (!$query) {
            die("Query preparation failed: " . $conn->error); 
        }else{
            $query->bind_param('i',$vehiclemaintenanceid);
            $exe=$query->execute();

            if($exe === false){
                $arr["message"] = "Error: " . mysqli_error($conn);
                $arr["success"]=false;
            }else{
                $arr["success"]=true;
                $arr["message"] = "Services  delete Successfully!";
                $arr["data"] = "";
            }
        }
    
    }
}



echo json_encode($arr);
?>