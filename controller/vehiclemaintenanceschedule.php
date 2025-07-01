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

    
    if(isset($_POST['type'])){
        $type = $_POST['type'];
        if($type == "view"){
            view($conn, $arr);
        
        }
        else if($type == "add"){
            add($conn, $arr);
        
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
}



function view($conn, &$arr){
    $query= $conn->prepare("SELECT * FROM vehiclemaintenanceschedule  vm JOIN services s ON s.servicesid = vm.servicesid  ORDER BY vm.creationdate DESC  ");
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
    $vehicleid = $_POST['vehicleid'] ?? '';
    $platenumber = $_POST['platenumber'] ?? '';
    $mileages = $_POST['mileages'] ?? '';
    $servicesid = $_POST['servicesid'] ?? 0;
   
    if (empty($vehicleid) && empty($platenumber) && empty($mileages)&& empty($servicesid)) {
        $arr['message'] = "Field cannot be empty.";
    }else{
        $dupCheck = $conn->prepare("select * from vehiclemaintenanceschedule where vehicleid = ?");
        $dupCheck->bind_param('i', $vehicleid);
        $dupCheck->execute();
    
        $result = $dupCheck->get_result();
        $fetch = $result->fetch_assoc(); 

        $response = [];

        if ($fetch > 0) {
                $arr["message"] = "Vehicle  already schedule";
        }else{
            $creationdate = date("Y-m-d h:m::s"); 
            $creationby = $_SESSION['user_id'];
            $query = $conn->prepare("INSERT INTO vehiclemaintenanceschedule(vehicleid,platenumber,setmileages,servicesid,creationdate,creationby) VALUES (?, ?, ?,?, ?,?)");
            if (!$query) {
                die("Query preparation failed: " . $conn->error); 
            }else{
                $query->bind_param('isiisi',$vehicleid,$platenumber,$mileages,$servicesid, $creationdate,$creationby);
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
    $vehiclemaintenanceid = $_POST['vehiclemaintenanceid'] ?? '';
    $mileages = $_POST['mileages'] ?? '';
   
    if (empty($vehiclemaintenanceid) && empty($mileages)){
        $arr['message'] = "Field cannot be empty.";
    }else{
       
        $creationdate = date("Y-m-d h:m::s"); 
        $creationby = $_SESSION['user_id'];
        $query = $conn->prepare("Update vehiclemaintenanceschedule set setmileages = ? , modificationdate =?, modificationby=? where V_id = ?");
        if (!$query) {
            die("Query preparation failed: " . $conn->error); 
        }else{
            $query->bind_param('isii',$mileages,$creationdate,$creationby,$vehiclemaintenanceid);
            $exe=$query->execute();

            if($exe === false){
                $arr["message"] = "Error: " . mysqli_error($conn);
                $arr["success"]=false;
            }else{
                $arr["success"]=true;
                $arr["message"] = "Vehicle Maintenance  updated Successfully!";
                $arr["data"] = "";
            }
        }
    
    }
}



function delete($conn, &$arr){
    $vehiclemaintenanceid = $_POST['vehiclemaintenanceid'] ?? '';
    if (empty($vehiclemaintenanceid)){
        $arr['message'] = "Field cannot be empty.";
    }else{
       
        $creationdate = date("Y-m-d h:m::s"); 
        $creationby = $_SESSION['user_id'];
        $query = $conn->prepare("Delete from vehiclemaintenanceschedule where V_id = ?");
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
                $arr["message"] = "Vehicle Maintenance  delete Successfully!";
                $arr["data"] = "";
            }
        }
    
    }
}




echo json_encode($arr);
?>