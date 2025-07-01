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

function add($conn,&$arr){
    $make_id = $_POST['make_id'] ?? '';
    $model_id = $_POST['model_id'] ?? '';
    $category_id = $_POST['category_id'] ?? '';
  
    $errors = [];
    if (empty($make_id)) {
        $errors['make_id'] = "make_id cannot be empty.";
    }
    if (empty($model_id)) {
        $errors['model_id'] = "model_id cannot be empty.";
    }
    if (empty($category_id)) {
        $errors['category_id'] = "category_id cannot be empty.";
    }
    
    if (empty($errors)) {
        $creationdate = date("Y-m-d H:i:s"); 
        $creationby = $_SESSION['user_id'];
        $query = $conn->prepare("INSERT INTO feature(make_id,model_id,category_id, creationdate, creationby) VALUES (?, ?, ?, ?, ?)");
        if (!$query) {
            die("Query preparation failed: " . $conn->error); 
        }else{
            $query->bind_param('iiisi',$make_id, $model_id, $category_id, $creationdate, $creationby);
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
    }else {
        $errorMessages = "Error: ";
        foreach ($errors as $field => $error) {
            $errorMessages .= "$field: $error; "; 
        }
        $errorMessages = rtrim($errorMessages, "; ");
        $arr["message"] = $errorMessages;
    }
}

function view($conn,&$arr){
    $query= $conn->prepare("SELECT make_id,makename,model_id,modelname,category_id,categoryname FROM feature
    LEFT JOIN make ON make_id = makeid
    LEFT JOIN model ON model_id = modelid 
    LEFT JOIN category ON category_id = categoryid ");
    if ($query->execute()) {
        $exe = $query->get_result(); 
        if ($exe) {
            $result = $exe->fetch_all(MYSQLI_ASSOC);
            if (count($result) > 0) {
                $data = array_map('array_values', $result);

                $arr["success"] = true;
                $arr["data"] = $data;
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