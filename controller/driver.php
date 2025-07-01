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
    $query= $conn->prepare("SELECT * FROM driver   ORDER BY creationdate DESC  ");
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
    $driverid = $_POST['driverid'] ?? 0;
    $driver_name = $_POST['driver_name'] ?? '';
    $cnic = $_POST['cnic'] ?? '';
    $ltv = $_POST['ltv'] ?? '';
    $license_no = $_POST['license_no'] ?? '';
    $category = $_POST['category'] ?? '';
    $validity = $_POST['validity'] ?? '';
   
    if (empty($driver_name) && empty($cnic)&& empty($ltv)&& empty($license_no)&& empty($category)&& empty($validity)) {
        $arr['message'] = "Field cannot be empty.";
    }else{
        $dupCheck = $conn->prepare("select * from driver where cnic = ?");
        $dupCheck->bind_param('i', $cnic);
        $dupCheck->execute();
    
        $result = $dupCheck->get_result();
        $fetch = $result->fetch_assoc(); 

        $response = [];

        if ($fetch > 0) {
                $arr["message"] = "Driver already exists";
        }else{
            $creationdate = date("Y-m-d H:i:s");  // Correct date format
            $creationby = $_SESSION['user_id'];

            $query = $conn->prepare("INSERT INTO driver(driverid, driver_name, cnic, ltv, license_no, category, validity, is_active, creationdate, creationby) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            if (!$query) {
                die("Query preparation failed: " . $conn->error);  // Error in preparing query
            } else {
                // Ensure correct parameter types and values
                $is_active = 1;  // Default active status
                $query->bind_param('issssssisi', $driverid, $driver_name, $cnic, $ltv, $license_no, $category, $validity, $is_active, $creationdate, $creationby);
                $exe = $query->execute();

                if ($exe === false) {
                    $arr["message"] = "Error: " . sqlsrv_errors();  // Correct error function for SQL Server
                    $arr["success"] = false;
                } else {
                    $arr["success"] = true;
                    $arr["message"] = "Driver info inserted successfully!";
                    $arr["data"] = "";  // You can populate this with more data if needed
                }
            }
        }
    }
}


function update($conn, &$arr){
    
    $driverid = $_POST['driverid'] ?? 0;
    $driver_name = $_POST['driver_name'] ?? '';
    $cnic = $_POST['cnic'] ?? '';
    $ltv = $_POST['ltv'] ?? '';
    $license_no = $_POST['license_no'] ?? '';
    $category = $_POST['category'] ?? '';
    $validity = $_POST['validity'] ?? '';
   
   
    if (empty($driverid) && empty($driver_name) && empty($cnic) && empty($ltv) && empty($license_no) && empty($category) && empty($validity)){
        $arr['message'] = "Field cannot be empty.";
    }else{
       
        $creationdate = date("Y-m-d h:m::s"); 
        $creationby = $_SESSION['user_id'];
        $query = $conn->prepare("Update driver set driver_name = ? ,cnic = ? ,ltv = ?,license_no = ?,category = ? ,validity = ? , modificationdate =?, modificationby=? where driverid = ?");
        if (!$query) {
            die("Query preparation failed: " . $conn->error); 
        }else{
            $query->bind_param('isii',$driver_name,$cnic,$ltv,$license_no,$category,$validity,$creationdate,$creationby,$driverid);
            $exe=$query->execute();

            if($exe === false){
                $arr["message"] = "Error: " . mysqli_error($conn);
                $arr["success"]=false;
            }else{
                $arr["success"]=true;
                $arr["message"] = "Driver  updated Successfully!";
                $arr["data"] = "";
            }
        }
    
    }

}



function delete($conn, &$arr){
    $d_id = $_POST['d_id'] ?? '';
    if (empty($d_id)){
        $arr['message'] = "Field cannot be empty.";
    }else{
       
        $creationdate = date("Y-m-d h:m::s"); 
        $creationby = $_SESSION['user_id'];
        $query = $conn->prepare("Delete from driver where d_id = ?");
        if (!$query) {
            die("Query preparation failed: " . $conn->error); 
        }else{
            $query->bind_param('i',$d_id);
            $exe=$query->execute();

            if($exe === false){
                $arr["message"] = "Error: " . mysqli_error($conn);
                $arr["success"]=false;
            }else{
                $arr["success"]=true;
                $arr["message"] = "Driver Info delete Successfully!";
                $arr["data"] = "";
            }
        }
    
    }
}




echo json_encode($arr);
?>