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
        updatePassword($conn,$arr);
    }else{
        $arr['message'] = "Unauthorized access";
    }
}

function updatePassword($conn, &$arr){
    $uid = $_SESSION['user_id'];
    $modifydate = date("Y-m-d"); 
    if(isset($_POST['oldpass']) && isset($_POST['newpass'])){
        $oldpass = $_POST['oldpass'];
        $newpass = $_POST['newpass'];
        if($_SESSION['type'] == 'vendor'){
            $query = $conn->prepare("select * from user_login where vendor_id = ?");
        }else if($_SESSION['type'] == 'user' || $_SESSION['type'] == 'admin'){
            $query = $conn->prepare("select * from user_login where user_id = ?");
        }
        if(!$query){
            die("Query preparation failed: ". $conn->error);
        }else{
            $query->bind_param('i',$uid);
            if(!$query->execute()){
                die("Query execution failed: ". $query->error);
            }else{
                if (!$res = $query->get_result()->fetch_all(MYSQLI_ASSOC)) {
                    die("Fetching result failed: " . $query->error);
                }
                if($oldpass != $res[0]['password']){
                    $arr['message'] = "Inncorect password";
                }else{
                    if($_SESSION['type'] == 'vendor'){
                        $update = $conn->prepare("update user_login set password = ?, modifydate = ? , modifyby = ? where vendor_id = ?");
                    }else if($_SESSION['type'] == 'user' || $_SESSION['type'] == 'admin' ){
                        $update = $conn->prepare("update user_login set password = ?, modifydate = ? , modifyby = ? where user_id = ?");
                    }
                    if(!$update){
                        die("Update query preparation failed: " . $conn->error);
                    }else{
                        $update->bind_param('ssii', $newpass,$modifydate, $uid, $uid);
                        if(!$update->execute()){
                            die("Update query execution failed: " . $update->error);
                        }else{
                            $arr['message'] = "Password Updated successfully";
                            $arr['success'] = true;
                        }
                    }                        
                }
            }
        }
    }else{
        $arr['message'] = "Old and new password are required";
    }
}


echo json_encode($arr);
?>