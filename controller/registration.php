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
            if($_POST['type'] == 'add'){
                add($conn,$arr);
            }else if ($_POST['type'] == 'insert'){
                insert($conn, $arr);
            }else if($_POST['type'] == 'update'){
                update($conn,$arr);
            } else if($_POST['type'] == 'view'){
                view($conn,$arr);
            }else if($_POST['type'] == 'delete'){
                delete($conn,$arr);
            }else if($_POST['type'] == 'getUser'){
                getUser($conn,$arr);
            }else if($_POST['type'] == 'getSignature'){
                getSignature($conn,$arr);
            }else{
                $arr['message'] = "Invalid request";
            }
        }else{
            $arr['message'] = "Type is required";
        }
    }else{
        $arr['message'] = "Unauthorized access";
    }
}

function add($conn, &$arr){

    $access = $_SESSION['access'];
    if($access == 'Full'){
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

    $designationQuery = $conn->prepare("SELECT id, title FROM designation");
    if (!$designationQuery) {
        $arr['success'] = false;
        $arr['error'][] = "Designation query preparation failed: " . $conn->error;
        return;
    }
    $designationQuery->execute();
    $arr['data']['designations'] = $designationQuery->get_result()->fetch_all(MYSQLI_ASSOC); 

    if ($access == 'Full') {
        $stationQuery = $conn->prepare("SELECT id, name FROM stationdata");
    } else {
        $cityAbbrv = $cities[0]['abbrv'];
        $stationQuery = $conn->prepare("SELECT id, name FROM stationdata WHERE name LIKE CONCAT(?, '-%')");
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
function insert($conn, &$arr){
    $access = $_POST['access'] ?? '';
    $name = $_POST['name']  ?? '';
    $station_id = $_POST['station_id'] ?? '';
    $city_id = $_POST['city_id'] ?? '';
    $designation_id = $_POST['designation_id'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
  
    $errors = [];
    if (empty($name)) {
        $errors['name'] = "name cannot be empty.";
    }
    if (empty($name)) {
        $errors['access'] = "access cannot be empty.";
    }
    if (empty($station_id)) {
        $errors['station_id'] = "station_id cannot be empty.";
    }
    if (empty($city_id)) {
        $errors['city_id'] = "city_id cannot be empty.";
    }
    if (empty($designation_id)) {
        $errors['designation_id'] = "designation_id cannot be empty.";
    }
    if (empty($username)) {
        $errors['username'] = "username cannot be empty.";
    }
    if (empty($password)) {
        $errors['password'] = "Password cannot be empty.";
    }
     
    if (empty($errors)) {
        $dupCheck = $conn->prepare("select username from user_login where username = ?");
        $dupCheck->bind_param('s', $username);
        $dupCheck->execute();
    
        $result = $dupCheck->get_result();
        $fetch = $result->fetch_assoc(); 

        $response = [];

        if ($fetch > 0) {
                $arr["message"] = "Username already exists";
        }else{
            $conn->begin_transaction();
            try {
                $query = $conn->prepare("INSERT INTO users(name,station_id,city_id,designation_id,access,creationdate, creationby) VALUES (?,?, ?, ?, ?, ?, ?)");
                if (!$query) {
                    throw new Exception("Query preparation failed: " . $conn->error); 
                }
                $creationdate = date("Y-m-d H:i:s"); 
                $creationby = $_SESSION['user_id'];
                $query->bind_param('siiissi',$name, $station_id, $city_id,  $designation_id, $access, $creationdate, $creationby);
                if (!$query->execute()) {
                    throw new Exception("Error inserting user: " . $query->error);
                }
                $newId = $query->insert_id;
                $query->close();
                $type = "User";
                $login = $conn->prepare("insert into user_login (username,user_id,password,type,status,creationdate, creationby) values (?,?,?,?,?,?,?)");
                if (!$login) {
                    throw new Exception("Query preparation failed: " . $conn->error); 
                }
                $status = 1;
                // $password = encodePassword($_POST['password']);
                $login->bind_param('sissisi',$username, $newId, $password,$type,$status,$creationdate, $creationby);
                if (!$login->execute()) {
                    throw new Exception("Error inserting user login: " . $login->error);
                }
                $login->close();
                    
                if (isset($_FILES['signature_url'])){
                    if($_FILES['signature_url']['error'] == UPLOAD_ERR_OK) {
                        $fileTmpPath = $_FILES['signature_url']['tmp_name'];
                        $fileExtension = pathinfo($_FILES['signature_url']['name'], PATHINFO_EXTENSION);

                        $cleanName = preg_replace("/[^a-zA-Z0-9]+/", " ", strtolower($name));
                        $newFileName = $cleanName . '-' . $newId . '.' . $fileExtension;

                        $uploadFileDir = '../signatures/';
                        $dest_path = $uploadFileDir . $newFileName;
                        
                        if (!move_uploaded_file($fileTmpPath, $dest_path)) {
                            throw new Exception("Error moving the uploaded file.");
                        }
                        $updateQuery = $conn->prepare("UPDATE users SET signature_url = ? WHERE id = ?");
                        if (!$updateQuery) {
                            throw new Exception("Query preparation failed: " . $conn->error);
                        }
                        $updateQuery->bind_param('si', $newFileName, $newId);
                        if (!$updateQuery->execute()) {
                            throw new Exception("Error updating signature_url: " . $updateQuery->error);
                        }
                        $updateQuery->close();
                    } else {
                        throw new Exception("There was an upload error in Signature file .");
                    }
                }
                $conn->commit();
                $arr["success"] = true;
                $arr["message"] = "Data inserted successfully!";
                $arr["data"] = "";
            }catch (Exception $e) {
                $conn->rollback();
                $arr["success"] = false;
                $arr["message"] = $e->getMessage();
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
function update($conn, &$arr){
    $uid = $_POST['uid'];
    $validUid=$conn->prepare("select * from users where id = ?");
    if(!$validUid){
        die("Query preparation failed: " . $conn->error);
    }
    $validUid->bind_param('i' , $uid);
    if (!$validUid->execute()) {
        die("Error inserting user: " . $validUid->error);
    }               
    $fetch = $validUid->get_result()->fetch_all(MYSQLI_ASSOC);
    if (count($fetch) > 0) {
        
        $access = $_POST['access'] ?? '';
        $name = $_POST['name']  ?? '';
        $station_id = $_POST['station_id'] ?? '';
        $city_id = $_POST['city_id'] ?? '';
        $designation_id = $_POST['designation_id'] ?? '';
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        $errors = [];
        if (empty($name)) {
            $errors['name'] = "name cannot be empty.";
        }
        if (empty($name)) {
            $errors['access'] = "access cannot be empty.";
        }
        if (empty($station_id)) {
            $errors['station_id'] = "station_id cannot be empty.";
        }
        if (empty($city_id)) {
            $errors['city_id'] = "city_id cannot be empty.";
        }
        if (empty($designation_id)) {
            $errors['designation_id'] = "designation_id cannot be empty.";
        }
        if (empty($username)) {
            $errors['username'] = "username cannot be empty.";
        }
        if (empty($password)) {
            $errors['password'] = "Password must conatin at least one capital, one small , one digit and one number.";
        }
        if(empty($errors)){
            $conn->begin_transaction();
            
            try {
                $modifydate = date("Y-m-d H:i:s"); 
                $modifyby = $_SESSION['user_id'];
                $query = $conn->prepare("update users set name = ?, city_id = ?, station_id = ?, designation_id = ?, access = ?, modifydate = ?, modifyby = ?  where id = ?");
                if (!$query) {
                    throw new Exception("Query preparation failed: " . $conn->error); 
                }
                $query->bind_param('siiissii',$name, $city_id, $station_id, $designation_id, $access, $modifydate, $modifyby, $uid);
                if (!$query->execute()) {
                    throw new Exception("Error inserting user: " . $query->error);
                }
                $loginUpdate = $conn->prepare("update user_login set username = ?, password = ?, modifydate = ?, modifyby = ?  where user_id = ?");
                if (!$loginUpdate) {
                    throw new Exception("Query preparation failed: " . $conn->error); 
                }
                // $password = decodePassword($password);
                $loginUpdate->bind_param('sssii',$username, $password, $modifydate, $modifyby, $uid);
                if(!$loginUpdate->execute()){
                    throw new Exception("Error inserting user: " . $loginUpdate->error);
                }
                if (isset($_FILES['signature_url'])){
                    if($_FILES['signature_url']['error'] == UPLOAD_ERR_OK) {
                        $fileTmpPath = $_FILES['signature_url']['tmp_name'];
                        $fileExtension = pathinfo($_FILES['signature_url']['name'], PATHINFO_EXTENSION);
    
                        $cleanName = preg_replace("/[^a-zA-Z0-9]+/", " ", strtolower($name));
                        $newFileName = $cleanName . '-' . $uid . '.' . $fileExtension;
    
                        $uploadFileDir = '../signatures/';
                        $dest_path = $uploadFileDir . $newFileName;
    
                        if (move_uploaded_file($fileTmpPath, $dest_path)) {
                            $updateQuery = $conn->prepare("UPDATE users SET signature_url = ? WHERE id = ?");
                            if (!$updateQuery) {
                                throw new Exception("Query preparation failed: " . $conn->error);
                            }
                            $updateQuery->bind_param('si', $newFileName, $uid);
                            if (!$updateQuery->execute()) {
                                throw new Exception("Error updating signature_url: " . $updateQuery->error);
                            }
                            $updateQuery->close();
                        } else {
                            throw new Exception("Error moving the uploaded file.");
                        }
                    } 
                }
                $conn->commit();
                $arr["success"] = true;
                $arr["message"] = "Data updated successfully!";
                $arr["data"] = "";

            } catch (Exception $e) {
                $conn->rollback();
                $arr["success"] = false;
                $arr["message"] = $e->getMessage();
            }          
        }else {
            $errorMessages = "Error: ";
            foreach ($errors as $field => $error) {
                $errorMessages .= "$field: $error; "; 
            }
            $arr["message"] = rtrim($errorMessages, "; ");  
        }
    }else {
        $arr['message'] = "User with ID $uid not found.";
    }    
}
function view($conn, &$arr){
    $query= $conn->prepare("SELECT u.id,u.name,u.signature_url,d.title AS designation,u.access, d.min, d.max, c.name AS city, s.name AS station, ul.username, ul.password
    FROM users u JOIN designation d ON designation_id = d.id 
    JOIN city c ON city_id = c.id 
    JOIN stationdata s ON station_id = s.id 
    LEFT JOIN user_login ul ON u.id = ul.user_id 
    WHERE STATUS = 1 AND user_id IS NOT NULL");
    if ($query->execute()) {
        $exe = $query->get_result(); 
        if ($exe) {
            $result = $exe->fetch_all(MYSQLI_ASSOC);

            if (count($result) > 0) {
                $arr["success"] = true;
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
function delete($conn, &$arr){
    if(isset($_POST['uid'])){
        $uid = $_POST['uid'] ?? '';
        if (empty($uid)) {
            $arr['message'] = "User id cannot be empty.";
        }else{
            $validUid=$conn->prepare("select * from users where id = ?");
            if(!$validUid){
                die("Query preparation failed: " . $conn->error);
            }
            $validUid->bind_param('i' , $uid);
            if (!$validUid->execute()) {
                die("Error inserting user: " . $validUid->error);
            }               
            $fetch = $validUid->get_result()->fetch_all(MYSQLI_ASSOC);
            if (count($fetch) > 0) {
                $query = $conn->prepare("update user_login set status = 0 where user_id = ?");
                $query->bind_param('i', $uid);
                if (!$query->execute()) {
                    die("Error deleting from users: " . $query->error);
                }
                $arr["success"] = true;
                $arr["message"] = "Data deleted successfully!";
                $arr["data"] = array();
            }else {
                $arr['message'] = "User with ID $uid not found.";
            }
        }
    }else{
     $arr['message'] = "User ID is required";   
    }
}
function getUser($conn,&$arr){
    $uid = $_SESSION['user_id'];
    $query = $conn->prepare("SELECT u.id,u.name,u.signature_url,d.title AS designation,u.access, d.min, d.max, c.name AS city, s.stationname AS station, ul.username, ul.password 
    FROM users u JOIN designation d ON designation_id = d.id 
    JOIN city c ON city_id = c.id 
    JOIN station s ON station_id = s.stationid 
    JOIN user_login ul ON u.id = ul.user_id 
    where u.id = ?");
    if(!$query){
        $arr['message'] = "Query preparation failed: " . $conn->error;
    }else{
        $query->bind_param('i', $uid);
        if($query->execute()){
            $arr['data'] = $query->get_result()->fetch_all(MYSQLI_ASSOC); 
            $arr['success'] = true;    
        }else{
            $arr['message'] = "Query execution failed: " . $query->error;
        }
    }
}
function getSignature($conn,&$arr){
    $uid = $_SESSION['user_id'];
    $query = $conn->prepare("SELECT signature_url FROM users where id = ?");
    if(!$query){
        $arr['message'] = "Query preparation failed: " . $conn->error;
    }else{
        $query->bind_param('i', $uid);
        if($query->execute()){
            $arr['data'] = $query->get_result()->fetch_all(MYSQLI_ASSOC); 
            $arr['success'] = true;    
        }else{
            $arr['message'] = "Query execution failed: " . $query->error;
        }
    }
}

function encodePassword($password) {
    return base64_encode($password);  
}
function decodePassword($encodedPassword) {
    return base64_decode($encodedPassword);  
}

echo json_encode($arr);
?>