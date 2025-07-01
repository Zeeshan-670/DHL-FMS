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
           if($type == "view"){
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
    $vehicle = $_POST['vehicle'] ?? '';
    if($_SESSION['access'] == 'Full' || $_SESSION['access'] == 'full'){
        if($vehicle=='all'){
            $query= $conn->prepare("SELECT vd.V_id, vd.Reg FROM vehicledetails vd LEFT JOIN vehicles v ON vd.V_id = v.V_id where status='active'");
        }
        else{
            $query= $conn->prepare("SELECT vd.V_id, vd.Reg FROM vehicledetails vd LEFT JOIN vehicles v ON vd.V_id = v.V_id where status='active' and category_id IN (1, 3, 4, 6)");
        }
        
    }else{

        if($vehicle=='all'){
            $query= $conn->prepare("SELECT vd.V_id, vd.Reg FROM vehicledetails vd LEFT JOIN vehicles v ON vd.V_id = v.V_id JOIN stationdata s ON station_id = s.id  where s.id =  ? and status='active'");
        }
        else{
            $query= $conn->prepare("SELECT vd.V_id, vd.Reg FROM vehicledetails vd LEFT JOIN vehicles v ON vd.V_id = v.V_id JOIN stationdata s ON station_id = s.id  where s.id =  ? and status='active' and category_id IN (1, 3, 4, 6)");
      
        }
        $query->bind_param('s', $_SESSION['station_id']);
    }
    if ($query->execute()) {
        $exe = $query->get_result(); // Fetch result
        if ($exe) {
            $result = $exe->fetch_all(MYSQLI_ASSOC);

            if (count($result) > 0) {
                $arr["success"]=true;
                $arr["data"] = $result; 
               
                
            } else {
                $arr["message"] = "Error: No data";
                $arr["success"] = false;
            }
        } else {
            $arr["message"] = "Error: " . $conn->error;
            $arr["success"] = false;
        }
    }
}


echo json_encode($arr);
?>