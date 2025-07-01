<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json"); 
include("database.php");


$arr = [
    "message" => "", 
    "success" => false,  
    "data" => []  
];

$connMileage = dbmileage();
if ($connMileage === false) {
    $arr['message'] = 'Mileage DB connection failed: ' . mysqli_error($connMileage);
    echo json_encode($arr);
    exit;
}
$conn = dbconnection();

if ($conn === false) {
    $arr['message'] = 'DB connecttion failed';
    $arr['success'] = false;
    $arr['error'] = sqlsrv_errors();
    echo json_encode($arr);
    exit;
}else{
    if(isset($_SESSION['user_id'])){
        vehicledetails($conn,$arr,$connMileage);
    }else{
        $arr['message'] = "Unauthorized access";
    }
}


function vehicledetails($conn,&$arr,$connMileage){
    if(isset($_POST['vehicle_reg'])){ 
        $reg = $_POST['vehicle_reg'];   
        if($_SESSION['access'] == 'Full' || $_SESSION['access'] == 'full'){
            if(isset($_POST['type'])){
                if($_POST['type'] == 'job'){
                    $query= $conn->prepare("SELECT * FROM vehicledetails v  WHERE Reg = ?");
                    $query->bind_param('s',$reg);
                }else if($_POST['type'] == 'workorder'){
                    $query= $conn->prepare("SELECT * FROM job j 
                    JOIN vehicledetails v ON v.V_id = j.V_id 
                    JOIN station s ON s.stationname = v.stationname 
                    JOIN vendor ON vendorid = vendor_id 
                    WHERE Reg = ? && status = 'accept'");
                    $query->bind_param('s',$reg);
                }
            }
            
        }else{
            if(isset($_POST['type'])){
                if($_POST['type'] == 'job'){
                    $query= $conn->prepare("SELECT * FROM vehicledetails v  JOIN station s ON s.stationname = v.stationname WHERE Reg = ? && stationid = ?");
                    $query->bind_param('ss',$reg, $_SESSION['station_id']);
                }else if($_POST['type'] == 'workorder'){
                    $query= $conn->prepare("SELECT * FROM job j 
                    JOIN vehicledetails v ON v.V_id = j.V_id 
                    JOIN station s ON s.stationname = v.stationname 
                    JOIN vendor ON vendorid = vendor_id  
                    WHERE Reg = ? && s.stationid = ? && status = 'accept'");
                    $query->bind_param('ss',$reg, $_SESSION['station_id']);
                }
            }
        }  
        if (!$query) {
            die("Query preparation failed: " . $conn->error); 
        }else{
            $query->execute(); 
            $exe_query = $query->get_result();
            if($exe_query === false){
                $arr["message"] = "Error: " . mysqli_error($conn);
                $arr["success"]=false;
                }else{
                    if ($exe_query->num_rows > 0) {
                    $arr["success"]=true;
                    $result = $exe_query->fetch_all(MYSQLI_ASSOC);  
                    if($_POST['type'] == 'job'){
                        $arr['data'] = $result;
                    }else if($_POST['type'] == 'workorder'){
                        $milage = getMileage($connMileage,$result[0]['Reg']);
                        foreach ($result as $row) {
                            if (empty($data)) {
                                $data['vehicle_details'] = [
                                    "V_id" => $row['V_id'],
                                    "Station" => $row['stationname'],
                                    "Make" => $row['makename'],
                                    "Model" => $row['modelname'],
                                    "Reg" => $row['Reg'],
                                    "Milage" => $milage,
                                    "Date" => $row['date_of_maturity']
                                ];
                            }
                            $jobs[] = [
                                "JobId" => $row['id'],
                                "JobTitle" => $row['jobTitle'],
                                "vendor_id" => $row['vendor_id'],
                                "vendorname" => $row['vendorname'],
                            ];
                        }
                    
                        $data['vehicle_details']['jobs'] = $jobs;
                        $arr["data"] = $data;
                    }
                    
                }else{
                    $arr['message'] = "No record found";
                }
            }
        }
    }else{
        $arr['message'] = "Vehicle registration is required";
    }
}

//getting mileage
function getMileage($conn, $reg) {
    $mileage = $conn->prepare("select plate_number,mileage from live_status where plate_number = ?");
    if (!$mileage) {
        die("Query preparation failed: " . $conn->error);     
    }
    
    $mileage->bind_param('s', $reg);
    $mileage->execute();
    $exe = $mileage->get_result();
    $results = $exe->fetch_assoc();

    if(!empty($results)){
        return $results['mileage'];         
    }else{
        return null;
    }
}

echo json_encode($arr);

?>