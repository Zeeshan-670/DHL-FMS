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
            if($type == "insert"){
                insert($conn, $arr);
            }else if($type == "add"){
                add($conn, $arr);
            }else if($type == "view"){
                view($conn, $arr);
            }else if($type == "update"){
                update($conn, $arr);
            }else if($type == "delete"){
                delete($conn, $arr);
            }else if($type == "getVendor"){
                getVendor($conn, $arr);
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
    $query = $conn->prepare("
        
SELECT 
            v.vendorid,
            v.vendorname,
            v.vendoremail,
            v.vendorcontact,
            v.vendoraddress,
            u.username,
            u.password,
            c.name AS city,
            s.name AS station,
    COUNT(CASE WHEN j.status IN ('Completed', 'Invoice Approve') THEN 1 END) AS total_jobs
        FROM vendor v
        JOIN user_login u ON v.vendorid = u.vendor_id
        JOIN city c ON v.cityId = c.id
        LEFT JOIN stationdata s ON v.stationId = s.id
        LEFT JOIN job j ON j.vendor_id = v.vendorid 
        WHERE u.STATUS = 1 
        AND u.vendor_id IS NOT NULL
        GROUP BY 
            v.vendorid, v.vendorname, v.vendoremail, v.vendorcontact, 
            v.vendoraddress, u.username, u.password, c.name, s.name
    ");
    
    $query->execute();
    $exe = $query->get_result();
    $result = $exe->fetch_all(MYSQLI_ASSOC);

    if($result === false){
        $arr["message"] = "Error: " . mysqli_error($conn);
        $arr["success"] = false;
    } else {
        if(count($result) > 0){
            foreach ($result as &$vendor) {
                $vendor['avg_rating'] = getAvg($conn, $arr, $vendor['vendorid']);
            }
            $arr["success"] = true;
            $arr["data"] = $result; 
        } else {
            $arr["message"] = "No data";
            $arr["success"] = true;
        }
    }
}

function insert($conn, &$arr){
    $vendorname = $_POST['vendorname'] ?? '';
    $vendorcontact = $_POST['vendorcontact'] ?? '';
    $vendoraddress = $_POST['vendoraddress'] ?? '';
    $vendoremail = $_POST['vendoremail'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $cityId = $_POST['cityId'] ?? '';
    $stationId = $_POST['stationId'] ?? '';

    $errors = [];
    if (empty($vendorname)) {
        $errors['vendorname'] = "vendor name cannot be empty.";
    }
    if (empty($vendorcontact)) {
        $errors['contact'] = "vendor contact cannot be empty.";
    }
    if (empty($vendoraddress)) {
        $errors['address'] = "vendor address cannot be empty.";
    }
    if (empty($vendoremail)) {
        $errors['email'] = "vendor email cannot be empty.";
    }
    if (empty($username)) {
        $errors['username'] = "vendor username cannot be empty.";
    }
    if (empty($password)) {
        $errors['password'] = "vendor password cannot be empty.";
    }
    if (empty($cityId)) {
        $errors['cityId'] = "vendor cityId cannot be empty.";
    }
    if (empty($stationId)) {
        $errors['stationId'] = "vendor stationId cannot be empty.";
    }
    if(empty($errors)){
        $dupCheck = $conn->prepare("select username from user_login where username = ?");
        $dupCheck->bind_param('s', $username);
        $dupCheck->execute();
    
        $result = $dupCheck->get_result();
        $fetch = $result->fetch_assoc(); 

        $response = [];

        if ($fetch > 0) {
                $arr["message"] = "Username already exists";
        }else{
            $creationdate = date("Y-m-d"); 
            $creationby = $_SESSION['user_id'];
            $query = $conn->prepare("INSERT INTO vendor(vendorname,vendorcontact,vendoremail,vendoraddress,cityId,stationID,creationdate, creationby) VALUES (?,?,?,?,?,?,?,?)");
            if (!$query) {
                die("Query preparation failed: " . $conn->error); 
            }else{
                $query->bind_param('ssssiisi',$vendorname,$vendorcontact,$vendoremail,$vendoraddress,$cityId,$stationId, $creationdate, $creationby);
                $exe=$query->execute();

                if($exe === false){
                    $arr["message"] = "Error: " . mysqli_error($conn);
                    $arr["success"]=false;
                }else{
                    $vendor_id = $conn->insert_id;
                    $role_assign = $type = 'vendor';
                    $status = 1;
                    $login = $conn->prepare("insert into user_login(username,password,role_assign,type,status,vendor_id,creationdate,creationby) values(?,?,?,?,?,?,?,?)");
                    if(!$login){
                        die("Login Query preperation failed: " . $conn->error);
                    }else{
                        $login->bind_param('ssssiisi', $username,$password,$role_assign,$type,$status,$vendor_id,$creationdate, $creationby);
                        $login_exe=$login->execute();

                        if($login_exe === false){
                            $arr['message'] = "Error: " . mysqli_error($conn);
                        }else{
                            $arr["success"]=true;
                            $arr["message"] = "Data inserted Successfully!";
                            $arr["data"] = "";
                        }
                    }
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
function add($conn, &$arr){

    $access = $_SESSION['access'];
    if($access == 'Full' || $access == 'full'){
        $cityQuery = $conn->prepare("SELECT id, name, abbrv FROM city ");
        $stationQuery = $conn->prepare("SELECT id, name FROM station");
    
    }else{
        $cityQuery = $conn->prepare("SELECT id, name, abbrv FROM city where name = ?");
        $cityQuery->bind_param('s', $access);
    }
    if (!$cityQuery) {
        $arr['success'] = false;
        $arr['error'][] = "City query preparation failed: " . $conn->error;
        return;
    }
    $cityQuery->execute();
    $results = $cityQuery->get_result()->fetch_all(MYSQLI_ASSOC);
    $accessTitles = array_merge(["Full"], array_column($results, 'name'));

    $cities = $arr['data']['cities'] = $results;
    $arr['data']['access'] = $accessTitles;

    if ($access == 'Full') {
        $stationQuery = $conn->prepare("SELECT id, name FROM station");
    } else {
        $cityAbbrv = $cities[0]['abbrv'];
        $stationQuery = $conn->prepare("SELECT id, name FROM station WHERE name LIKE CONCAT(?, '-%')");
        $stationQuery->bind_param('s', $cityAbbrv);
    }

    if (!$stationQuery) {
        $arr['success'] = false;
        $arr['error'][] = "station query preparation failed: " . $conn->error;
        return;
    }
    $stationQuery->execute();
    $arr['data']['stations'] = $stationQuery->get_result()->fetch_all(MYSQLI_ASSOC); 

    $arr['success'] = true;
}
function delete($conn, &$arr){
    if(isset($_POST['vid'])){
        $vid = $_POST['vid'] ?? '';
        if (empty($vid)) {
            $arr['message'] = "Vendor id cannot be empty.";
        }else{
            $validUid=$conn->prepare("select * from user_login where vendor_id = ?");
            if(!$validUid){
                die("Query preparation failed: " . $conn->error);
            }
            $validUid->bind_param('i' , $vid);
            if (!$validUid->execute()) {
                die("Error inserting user: " . $validUid->error);
            }               
            $fetch = $validUid->get_result()->fetch_all(MYSQLI_ASSOC);
            if (count($fetch) > 0) {
                    $query = $conn->prepare("update user_login set status = 0 where vendor_id = ?");
                    $query->bind_param('i', $vid);
                    if (!$query->execute()) {
                        die("Error deleting from users: " . $query->error);
                    }
                    $arr["success"] = true;
                    $arr["message"] = "Data deleted successfully!";
                    $arr["data"] = array();
            }else {
                $arr['message'] = "Vendor with ID $vid not found.";
            }
        }
    }else{
     $arr['message'] = "Vendor ID is required";   
    }
}
function update($conn, &$arr){
    $vid =  $_SESSION['user_id'];
    $validUid=$conn->prepare("select * from vendor where vendorid = ?");
    if(!$validUid){
        die("Query preparation failed: " . $conn->error);
    }
    $validUid->bind_param('i' , $vid);
    if (!$validUid->execute()) {
        die("Error inserting user: " . $validUid->error);
    }               
    $fetch = $validUid->get_result()->fetch_all(MYSQLI_ASSOC);
    if (count($fetch) > 0) {
        
    $vendorname = $_POST['vendorname'] ?? '';
    $vendorcontact = $_POST['vendorcontact'] ?? '';
    $vendoraddress = $_POST['vendoraddress'] ?? '';
    $vendoremail = $_POST['vendoremail'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $cityId = $_POST['cityId'] ?? '';
    $stationId = $_POST['stationId'] ?? '';

    $errors = [];
    if (empty($vendorname)) {
        $errors['vendorname'] = "vendor name cannot be empty.";
    }
    if (empty($vendorcontact)) {
        $errors['contact'] = "vendor contact cannot be empty.";
    }
    if (empty($vendoraddress)) {
        $errors['address'] = "vendor address cannot be empty.";
    }
    if (empty($vendoremail)) {
        $errors['email'] = "vendor email cannot be empty.";
    }
    if (empty($username)) {
        $errors['username'] = "vendor username cannot be empty.";
    }
    if (empty($password)) {
        $errors['password'] = "vendor password cannot be empty.";
    }
    if (empty($cityId)) {
        $errors['cityId'] = "vendor cityId cannot be empty.";
    }
    if (empty($stationId)) {
        $errors['stationId'] = "vendor stationId cannot be empty.";
    }
    if(empty($errors)){
        $modifydate = date("Y-m-d"); 
        $modifyby = $_SESSION['user_id'];
        $query = $conn->prepare("update vendor set vendorname = ?, vendorcontact = ? , vendoremail = ?, vendoraddress = ?, cityId = ?, stationId = ?, modifydate = ?, modifyby = ?  where vendorid = ?");
        if (!$query) {
            throw new Exception("Query preparation failed: " . $conn->error); 
        }
        $query->bind_param('sssiisii',$vendorname, $vendorcontact,$vendoremail, $vendoraddress, $cityId, $stationId, $modifydate, $modifyby, $vid);
        if (!$query->execute()) {
            throw new Exception("Error inserting user: " . $query->error);
        }
        $loginUpdate = $conn->prepare("update user_login set username = ?, password = ?, modifydate = ?, modifyby = ?  where vendor_id = ?");
        if (!$loginUpdate) {
            throw new Exception("Query preparation failed: " . $conn->error); 
        }
        $loginUpdate->bind_param('sssii',$username, $password, $modifydate, $modifyby, $vid);
        if(!$loginUpdate->execute()){
            throw new Exception("Error inserting user: " . $loginUpdate->error);
        }
        
        $conn->commit();
        $arr["success"] = true;
        $arr["message"] = "Data updated successfully!";
        $arr["data"] = "";     
        }else {
            $errorMessages = "Error: ";
            foreach ($errors as $field => $error) {
                $errorMessages .= "$field: $error; "; 
            }
            $arr["message"] = rtrim($errorMessages, "; ");  
        }
    }else {
        $arr['message'] = "Vendor with ID $vid not found.";
    }
}
function getVendor($conn,&$arr){
    $uid = $_SESSION['user_id'];
    $query = $conn->prepare("SELECT vendorid,vendorname,vendoremail,vendorcontact,vendoraddress,u.username,u.password,c.name AS city, s.stationname AS station 
    FROM vendor JOIN user_login u ON vendorid = vendor_id JOIN city c ON cityId = c.id left JOIN station s ON vendor.stationId = s.stationid WHERE vendorid = ?");
    if(!$query){
        die("Query preparation failed: " . $conn->error);
    }else{
        $query->bind_param('i', $uid);
        if($query->execute()){
            $arr['message'] = "Data feteched successfully";
            $arr['data'] = $query->get_result()->fetch_all(MYSQLI_ASSOC); 
            $arr['success'] = true;    
        }else{
            die("Query execution failed: " . $query->error);
        }
    }
}

function getAvg($conn, &$arr, $vendor_id){
    $query = $conn->prepare("SELECT  ROUND(AVG(rating), 2) AS avg_rating FROM rating WHERE vendor_id = ?");
    $query->bind_param('i', $vendor_id);
    $query->execute();
    $query->bind_result($avg_rating);
    $query->fetch();
    $query->close();
    
    return number_format($avg_rating, 2);;
}

echo json_encode($arr);
?>