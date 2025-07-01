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
        add($conn, $arr);    
    }else{
        $arr['message'] = "Unauthorized access";
    }
}

function add($conn, &$arr){
    $value = $_POST['value'] ?? '';
  
    if (empty($value)) {
        $arr['message'] = "make name cannot be empty.";
    }else{
        $dupCheck = $conn->prepare("select value from salesTax where value = ?");
        $dupCheck->bind_param('d', $value);
        $dupCheck->execute();
    
        $result = $dupCheck->get_result();
        $fetch = $result->fetch_assoc(); 

        $response = [];

        if ($fetch > 0) {
                $arr["message"] = "Tax value already exists";
        }else{
            $creationdate = date("Y-m-d"); 
            $creationby = $_SESSION['user_id'];
            $query = $conn->prepare("INSERT INTO salesTax(value) VALUES (?)");
            if (!$query) {
                die("Query preparation failed: " . $conn->error); 
            }else{
                $query->bind_param('d',$value);
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