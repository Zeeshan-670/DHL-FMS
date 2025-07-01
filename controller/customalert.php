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
        $payload = json_decode(file_get_contents('php://input'), true);
        // var_dump($payload);
        if(isset($payload['type'])){
            $type = $payload['type'];
            if($type == "add"){
                add($conn, $arr);
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

function add($conn, &$arr) {
    // Start session if not already started
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Decode the payload
    $payload = json_decode(file_get_contents('php://input'), true);

    // Check if the required fields are present
    if (!isset($payload['vehicle']) || !isset($payload['alerts'])) {
        $arr['message'] = "Vehicle and alerts are required";
        echo json_encode($arr);
        return;
    }

    $vehicle = $payload['vehicle']; 
    $alerts = $payload['alerts'];  

    foreach ($alerts as $alert) {
        // Ensure alert has necessary fields
        if (isset($alert['alerttype']) && isset($alert['date'])) {
            // Use proper date format (Y-m-d H:i:s)
            $creationdate = date("Y-m-d H:i:s");
            $creationby = $_SESSION['user_id'];

            // Check if the alerttype and vehicle combination already exists
            $check_query = $conn->prepare("SELECT * FROM customalert WHERE v_id = ? AND alerttype = ?");
            $check_query->bind_param('is', $vehicle, $alert['alerttype']);
            $check_query->execute();
            $result = $check_query->get_result();

            if ($result->num_rows > 0) {
                // If record exists, update the existing record
                $update_query = $conn->prepare("UPDATE customalert SET expiredate = ?, modificationdate = ?, modificationby = ? WHERE v_id = ? AND alerttype = ?");
                $update_query->bind_param('sssis', $alert['date'], $creationdate, $creationby, $vehicle, $alert['alerttype']);
                if (!$update_query->execute()) {
                    $arr["message"] = "Error updating record: " . $conn->error;
                    $arr["success"] = false;
                 
                    return;
                }
            } else {
                // If record doesn't exist, insert new record
                $insert_query = $conn->prepare("INSERT INTO customalert (v_id, alerttype, expiredate, creationdate, creationby) VALUES (?, ?, ?, ?, ?)");
                $insert_query->bind_param('isssi', $vehicle, $alert['alerttype'], $alert['date'], $creationdate, $creationby);
                if (!$insert_query->execute()) {
                    $arr["message"] = "Error inserting record: " . $conn->error;
                    $arr["success"] = false;
                 
                    return;
                }
            }
        } else {
            $arr['message'] = "Missing 'alerttype' or 'date' in alert";
          
            return;
        }
    }

    // Set success message
    $arr['success'] = true;
    $arr['message'] = "Data saved successfully";
    
}



echo json_encode($arr);

?>