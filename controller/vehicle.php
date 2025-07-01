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
            }else if($type == "delete"){
                delete($conn, $arr);
            }else if($type == "update"){
                update($conn, $arr);
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

function update($conn, &$arr){
    if(isset($_POST['vid'])){
        $vid = $_POST['vid'] ?? '';
        if (empty($vid)) {
            $arr['message'] = "Vechile id cannot be empty.";
        }else{
            $make_id = $_POST['make_id'] ;
            $model_id = $_POST['model_id'];
            $category_id = $_POST['category_id'] ;
            $engine = $_POST['engine'] ;
            $chassis = $_POST['chassis'] ;
            $doi = $_POST['doi'] ;
            $dep_years = $_POST['dep_years'] ;
            $station_id = $_POST['station_id'] ;
            $icn = $_POST['icn'] ;
            $device = $_POST['device'] ;
            $segment = $_POST['segment'] ;
            $pudgtw = $_POST['pudgtw'] ;
            $fuel_type = $_POST['fuel_type'] ;
            $payload = $_POST['payload'];
            $date_of_maturity = $_POST['date_of_maturity'];
            $modifydate = date("Y-m-d"); 
            $modifyby = $_SESSION['user_id'];
            $query = $conn->prepare("update vehicles set make_id =?,model_id = ?,station_id = ?,engine = ?,chassis = ?,doi = ?,dep_years = ?,icn = ?,device = ?,segment = ?,pudgtw = ?,fuel_type = ?,payload = ?,date_of_maturity = ?,category_id = ?,modifydate = ?, modifyby = ?  where v_id = ?");
            $query->bind_param('iiisssisssssisisii',$make_id, $model_id, $station_id, $engine, $chassis, $doi, $dep_years, $icn, $device, $segment, $pudgtw, $fuel_type, $payload, $date_of_maturity, $category_id, $modifydate, $modifyby, $vid);
            $exe = $query->execute();
            if($exe === true){
                $arr["success"]=true;
                $arr["message"] = "Data updated Successfully!";
                $arr["data"] = array(); 
            }else{
                $arr["message"] = "Error: " . mysqli_error($conn);
                $arr["success"]=false;
            }
        }
    }else{
     $arr['message'] = "Vehicle ID is required" ;  
    }
}
function view($conn, &$arr){
    if($_SESSION['access'] == 'Full' || $_SESSION['access'] == 'full'){
        $query= $conn->prepare("
        SELECT vd.V_id, vd.Reg,makename,modelname,stationname,vd.engine,vd.chassis,vd.doi,vd.dep_years,
        vd.icn,vd.device,vd.segment,vd.pudgtw,vd.fuel_type,vd.payload,vd.date_of_maturity,categoryname,status,v.remarks,
        d.startdate,d.enddate
        FROM vehicledetails vd 
        LEFT JOIN vehicles v ON vd.V_id = v.V_id
                LEFT JOIN downtime d ON vd.V_id = d.V_id AND v.status = 'Downtime'");
    }else{
        $query= $conn->prepare("
        SELECT vd.V_id, vd.Reg,makename,modelname,stationname,vd.engine,vd.chassis,vd.doi,vd.dep_years,
        vd.icn,vd.device,vd.segment,vd.pudgtw,vd.fuel_type,vd.payload,vd.date_of_maturity,categoryname,status,v.remarks,
        d.startdate,d.enddate
        FROM vehicledetails vd 
        LEFT JOIN vehicles v ON vd.V_id = v.V_id 
        LEFT JOIN downtime d ON vd.V_id = d.V_id AND v.status = 'Downtime'
        JOIN stationdata s ON station_id = s.id  
        where s.id =  ?");
        $query->bind_param('s', $_SESSION['station_id']);
    }
    if ($query->execute()) {
        $exe = $query->get_result(); // Fetch result
        if ($exe) {
            $result = $exe->fetch_all(MYSQLI_ASSOC);

            if (count($result) > 0) {
                $arr["success"] = true;
                $arr["data"] = $result;
                // foreach($result as $active){
                    // if($active['status'] == 'active'){
                        // $arr["success"] = true;
                        // $arr["data"] = $active;
                    // }
                    // if ($active['status'] == 'Deactivated'){
                    //     $arr["success"] = true;
                    //     $arr["data"]['deactive'] = $active;
                    // }
                    // if ($active['status'] == 'Downtime'){
                    //     $arr["success"] = true;
                    //     $arr["data"]['downtime'] = $active;
                    // }
                // }
                
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
function delete($conn, &$arr){
    if(isset($_POST['vid'])){
        $vid = $_POST['vid'] ?? '';
        $status = $_POST['status'] ?? '';
        $remarks = $_POST['remarks'] ?? '';
        if (empty($vid)) {
            $arr['message'] = "Vechile id cannot be empty.";
        }else{
            $keycheck = $conn->prepare("SELECT V_id FROM vehicles WHERE V_id = ?");
            $keycheck->bind_param('i', $vid);
            $keycheck->execute();
            $exe = $keycheck->get_result();
            if ($exe === false || $exe->num_rows === 0) {
                $arr["message"] = "Vehicle not found.";
                $arr["success"] = false;
            } else {
                if(!in_array($status , ["Deactivated", "Downtime","Active"])){
                    $arr['message'] = "Invalid status.";
                }else{
                    $query = $conn->prepare("update `vehicles` set status = ?, remarks = ? where v_id = ?");
                    $query->bind_param('ssi', $status,$remarks,$vid);
                    if($status == "Downtime"){
                        $to = $_POST['to'] ?? '';
                        $from = $_POST['from'] ?? '';
                        $DTquery = $conn->prepare("insert into `downtime` (V_id,startdate,enddate) values (?,?,?)");
                        $DTquery->bind_param('iss', $vid,$from,$to);
                        $DTexe = $DTquery->execute();
                        if($DTexe === true){
                            $arr["success"]=true;
                            $arr["message"] = "Data Updated Successfully to Downtime!";
                            $arr["data"] = array(); 
                        }else{
                            $arr["message"] = "Error: " . mysqli_error($conn);
                            $arr["success"]=false;
                        }
                    }
                    if($status == "Active" || $status == "Deactivated"){
                        $DTquery = $conn->prepare("delete from `downtime` where V_id = ?");
                        $DTquery->bind_param('i', $vid);
                        $DTexe = $DTquery->execute();
                        if($DTexe === true){
                            $arr["success"]=true;
                            $arr["message"] = "Data Updated Successfully to active!";
                            $arr["data"] = array(); 
                        }else{
                            $arr["message"] = "Error: " . mysqli_error($conn);
                            $arr["success"]=false;
                        }
                    }
                    $exe = $query->execute();
                    if($exe === true){
                        $arr["success"]=true;
                        $arr["message"] = "Data Updated Successfully!";
                        $arr["data"] = array(); 
                    }else{
                        $arr["message"] = "Error: " . mysqli_error($conn);
                        $arr["success"]=false;
                    }
                }
            }
        }
    }else{
     $arr['message'] = "Vehicle ID is required";   
    }
}
function add($conn, &$arr){
    $Reg = $_POST['Reg']  ?? '';
    $make_id = $_POST['make_id'] ?? '';
    $model_id = $_POST['model_id'] ?? '';
    $category_id = $_POST['category_id'] ?? '';
    $engine = $_POST['engine'] ?? '';
    $chassis = $_POST['chassis'] ?? '';
    $doi = $_POST['doi'] ?? '';
    $dep_years = $_POST['dep_years'] ?? '';
    $station_id = $_POST['station_id'] ?? '';
    $icn = $_POST['icn'] ?? '';
    $device = $_POST['device'] ?? '';
    $segment = $_POST['segment'] ?? '';
    $pudgtw = $_POST['pudgtw'] ?? '';
    $fuel_type = $_POST['fuel_type'] ?? '';
    $payload = $_POST['payload'] ?? '';
    $date_of_maturity = $_POST['date_of_maturity'] ?? '';
  
    $errors = [];
    if (empty($Reg)) {
        $errors['Reg'] = "Reg cannot be empty.";
    }
    if (empty($make_id)) {
        $errors['make_id'] = "make_id cannot be empty.";
    }
    if (empty($model_id)) {
        $errors['model_id'] = "model_id cannot be empty.";
    }
    if (empty($station_id)) {
        $errors['station_id'] = "station_id cannot be empty.";
    }
    if (empty($engine)) {
        $errors['engine'] = "engine cannot be empty.";
    }
    if (empty($chassis)) {
        $errors['chassis'] = "chassis cannot be empty.";
    }
    if (empty($doi)) {
        $errors['doi'] = "doi cannot be empty.";
    }
    if (empty($icn)) {
        $errors['icn'] = "icn cannot be empty.";
    }
    if (empty($device)) {
        $errors['device'] = "device cannot be empty.";
    }
    if (empty($segment)) {
        $errors['segment'] = "segment cannot be empty.";
    }
    if (empty($pudgtw)) {
        $errors['pudgtw'] = "pudgtw cannot be empty.";
    }
    if (empty($fuel_type)) {
        $errors['fuel_type'] = "fuel_type cannot be empty.";
    }
    if (empty($payload)) {
        $errors['payload'] = "payload cannot be empty.";
    }
    if (empty($date_of_maturity)) {
        $errors['date_of_maturity'] = "date_of_maturity cannot be empty.";
    }
    if (empty($category_id)) {
        $errors['category_id'] = "category_id cannot be empty.";
    }
    if (empty($errors)) {
        $dupCheck = $conn->prepare("select Reg from vehicles where Reg = ?");
        $dupCheck->bind_param('s', $Reg);
        $dupCheck->execute();
    
        $result = $dupCheck->get_result();
        $fetch = $result->fetch_assoc(); 

        $response = [];

        if ($fetch > 0) {
                $arr["message"] = "Vehicle already exists";
        }else{
            $creationdate = date("Y-m-d"); 
            $creationby = $_SESSION['user_id'];
            $status = 'active';
            $query = $conn->prepare("INSERT INTO vehicles(Reg,make_id,model_id,station_id,engine,chassis,doi,dep_years,icn,device,segment,pudgtw,fuel_type,payload,date_of_maturity,category_id,creationdate, creationby,status) VALUES (?,?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            if (!$query) {
                die("Query preparation failed: " . $conn->error); 
            }else{
                $query->bind_param('siiisssisssssisisis',$Reg, $make_id, $model_id, $station_id, $engine, $chassis, $doi, $dep_years, $icn, $device, $segment, $pudgtw, $fuel_type, $payload, $date_of_maturity, $category_id, $creationdate, $creationby,$status);
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
    }else {
        $errorMessages = "Error: ";
        foreach ($errors as $field => $error) {
            $errorMessages .= "$field: $error; "; 
        }
        $errorMessages = rtrim($errorMessages, "; ");
        $arr["message"] = $errorMessages;
    }
}


echo json_encode($arr);
?>