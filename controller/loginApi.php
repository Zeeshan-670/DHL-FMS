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
    login($conn,$arr);
}


function login($conn, &$arr) {
    if(isset($_POST['username']) && isset($_POST['password'])){
        $username = $_POST['username'];
        $password = $_POST['password'];

        $query=$conn->prepare("select * from user_login left JOIN users ON user_id = users.`id` left join vendor on vendorid = user_login.vendor_id left join designation on designation_id = designation.id where username = ? and status = 1 ");
        if(!$query){
            die("Query execusion failed: ". $conn->error);
        }else{
            $query->bind_param('s', $username);
            $query->execute();
            $exe = $query->get_result();
            $result = $exe->fetch_assoc();
            if($result===false){
                $arr['message'] = "Error: ".mysqli_error($conn);
                $arr['success'] = false;
            }else{
                if(!empty($result)){

                    if($password == $result['password']){
                        $arr['message'] = "Login successful";
                        $arr['success'] = true;
                        $arr['type'] = $result['type'];
                        if($result['type'] == 'vendor'){
                            $_SESSION['user_id'] = $result['vendor_id'];
                            $_SESSION['city_id'] = $result['cityId'];
                            $_SESSION['name'] = $result['vendorname'];
                            $_SESSION['username'] = $result['username'];
                            $_SESSION['isManager'] = false;
                        }else if($result['type'] == 'admin' || $result['type'] == 'User'){
                            $_SESSION['user_id'] = $result['user_id'];
                            $_SESSION['access'] = $result['access'];
                            $_SESSION['station_id'] = $result['station_id'];
                            $_SESSION['designation'] = $result['title'];
                            $_SESSION['city_id'] = $result['city_id'];
                            $_SESSION['name'] = $result['name'];
                            $_SESSION['username'] = $result['username'];
                            $_SESSION['min'] = $result['min'];
                            $_SESSION['max'] = $result['max'];
                            $_SESSION['signature_url'] = $result['signature_url'];
                            if($result['title'] == 'Manager'){
                                $_SESSION['isManager'] = true;
                            }else{
                                $_SESSION['isManager'] = false;
                            }
                        }
                        $_SESSION['type'] = $result['type'];

                        $arr['data'] = $_SESSION;
                    }else{
                        $arr['message'] = "Incorrect password";
                    }
                }else{
                    $arr['message'] = "User doesnt exists";
                    $arr['success'] = false;
                }
            }
        }
    }else{
        $arr['message'] = "Username and password required";
    }
}




function decodePassword($encodedPassword) {
    return base64_decode($encodedPassword); 
}

function encodePassword($password) {
    return base64_encode($password);  
}

echo json_encode($arr);
?>