<?php
require_once __DIR__ . '/../pdf/vendor/autoload.php';
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json"); 
include("database.php");
include("sendemail.php");

use Mpdf\Mpdf;

// var_dump($_POST);
// var_dump($_FILES);
// die;
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
            if($_POST['type'] == 'job'){
                addJob($conn, $arr);    
            }else if ($_POST['type'] == 'workorder'){
                add($conn, $arr);    
            }else if ($_POST['type'] == 'quotation'){
                quotation($conn, $arr);    
            }else if($_POST['type'] == 'updatestatus'){
                updateStatus($conn,$arr,$_POST['status'],$_POST['id']);
            }else if($_POST['type'] == 'updateJob'){
                updateJob($conn,$arr);
            }
        }
    }else{
        $arr['message'] = "Unauthorized access";
    }
}

function updateStatus($conn,&$arr,$status, $id){
    $modifydate = date("Y-m-d");
    $modifyby = $_SESSION['user_id'];
    $reason = $_POST['reason'] ?? ''; 
    $query = $conn->prepare("SELECT creationby, status from job WHERE id = ?");
    if (!$query) {
        die("Query preparation failed: " . $conn->error);
    }

    $query->bind_param("i", $id);
    $query->execute();
    $result = $query->get_result();
    $statusResult = $result->fetch_assoc();

    $jobStatus = $statusResult['status'];
    $requester = $statusResult['creationby'];

    if (!$statusResult) {
        $arr['message'] = "Job not found";
        $arr['success'] = false;
        return;
    }
    if ($status == 'accept' || $status == 'reject'){
        if($jobStatus != 'submit quotation'){
            $arr['message'] = "Quotation can only be reviewed after quotation is submitted";
            $arr['success'] = false;
            return;
        }else{
            if($status == 'reject'){
                if(isset($_POST['reason']) && !empty($_POST['reason'])){
                    $reason = $_POST['reason'];
                }else{
                    $arr['message'] = "Reason cannot be empty";
                    return;
                }
            }
        }
    }
    if(in_array($status, ['accept', 'reject'])){
        $query = $conn->prepare("UPDATE job SET status = ?, reason = ?, modifydate = ?, modifyby = ?  WHERE id = ?");
        if (!$query) {
            die("Update Query preparation failed: " . $conn->error);
        }
        $query->bind_param('sssii', $status, $reason, $modifydate, $modifyby, $id);
        $exe = $query->execute();
        if ($exe === false) {
            $arr['message'] = "Error: " . $conn->error;
            $arr['success'] = false;
        } else {
            $arr['message'] = "Status updated to ".$status." successfully";
            $arr['success'] = true;
            $arr['data'] = [];
        }
    }else{
        $arr['message'] = "Invalid status";
    }
}

function quotation($conn,&$arr){
    if(isset($_POST['quotation'])){
        $quotation = $_POST['quotation'];
        $quotationDescription = $_POST['quotationDescription'] ?? '';
        $id = $_POST['id'];
        $query = $conn->prepare("update job set quotation = ?, quotationDescription = ?, status = ? where id = ? ");
        if (!$query) {
            die("Query preparation failed: " . $conn->error); 
        }else{
            $status = "submit quotation";
            $query->bind_param('sssi',$quotation,$quotationDescription,$status,$id);
            $exe=$query->execute();

            if($exe === false){
                $arr["message"] = "Error: " . mysqli_error($conn);
                $arr["success"]=false;
            }else{
                $dataquery = $conn->prepare("SELECT users.id, name, email, Reg, w.jobTitle,description
                                                FROM users 
                                                LEFT JOIN workorder w ON w.creationby = users.id
                                                LEFT JOIN job ON job.id = w.job_id
                                                LEFT JOIN vehicledetails v ON v.V_id = job.V_id 
                                                LEFT JOIN vendor ON vendorid = w.vendor_id  
                                                WHERE job.id = ?");
                    if ($dataquery) {
                        $dataquery->bind_param('i', $id);
                        $dataquery->execute();
                        $result = $dataquery->get_result();

                        if ($result->num_rows > 0) {
                            $data = $result->fetch_assoc();
                            $data = [
                                'to' => $data['email'],
                                'subject' => 'Quotation added (Reg: ' . $data['Reg'] . ')',
                                'template' => '../emailtemplate/vendorEmail.html',
                                'placeholders' => [
                                    'subject' => 'Quotation added (Reg: ' . $data['Reg'] . ')',
                                    'body' => '
                                        <p>Dear ' . $data['name'] . ',</p>
                                        <p>Quotation is added for the vehicle with registration number
                                            <strong>' . $data['Reg'] . '</strong>.
                                        </p>
                                        <p><strong>Details:</strong></p>
                                        <p><strong>Job Title:</strong> ' . $data['jobTitle'] . '<br>
                                        <strong>Reg No:</strong> ' . $data['Reg'] . '<br>
                                        <strong>Description:</strong> ' . $data['description'] . '</p>
                                        <p>Please visit the link below to review the workorder and approve it:</p>
                                        <p><a href="https://mdvr2.itecknologi.com:8080/fms" target="_blank">https://mdvr2.itecknologi.com:8080/fms</a></p>
                                        <p>Thank you,<br><strong>DHL</strong></p>',
                                ],
                                'altBody' => 'We kindly request confirmation for this work order.',
                            ];
                            $result = email($conn, $arr, $data);

                            if (!$result) {
                                return;
                            }
                        }
                    }else {
                        $arr["message"] = "Data query preparation failed: " . $conn->error;
                        $arr["success"] = false;
                        return;
                    }
                $arr["success"]=true;
                $arr["message"] = "Quotation Updated Successfully!";
                $arr["data"] = "";
            }
        }
    }else{
        $arr['message'] = "Quotation cannot be empty";
    }
}

function addJob($conn,&$arr){
    $V_id = $_POST['V_id']  ?? '';
    $vendor_id = $_POST['vendor_id'] ?? '';
    $jobtitle = $_POST['jobtitle'] ?? '';
    $description = $_POST['description'] ?? '';
    $errors = [];
    if (empty($V_id)) {
        $errors['V_id'] = "V_id cannot be empty.";
    }
    if (empty($vendor_id)) {
        $errors['vendor_id'] = "vendor_id cannot be empty.";
    }
    if (empty($description)) {
        $errors['description'] = "description cannot be empty.";
    }
    if (empty($jobtitle)) {
        $errors['jobtitle'] = "jobtitle cannot be empty.";
    }
    
    if (empty($errors)) {
        $creationdate = date("Y-m-d"); 
        $creationby = $_SESSION['user_id'];
        $status = "Pending";
        $query = $conn->prepare("INSERT INTO job(V_id, vendor_id, jobTitle, description, creationdate, creationby, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if (!$query) {
            die("Query preparation failed: " . $conn->error); 
        } else {
            if (!$query->bind_param('iisssis', $V_id, $vendor_id, $jobtitle, $description, $creationdate, $creationby, $status)) {
                die("Parameter binding failed: " . $query->error);
            }
            
            $executeResult = $query->execute();
            if ($executeResult === false) {
                $arr["message"] = "Error: " . $conn->error;
                $arr["success"] = false;
            } else {
                $arr["success"] = true;
                $arr["message"] = "Data inserted successfully!";
                $arr["data"] = "";

                $inserted_id = $conn->insert_id;
                if ($inserted_id > 0) { 
                    $dataquery = $conn->prepare("SELECT v.Reg, jobTitle, description, vendorname, vendoremail
                                                FROM job 
                                                JOIN vehicledetails v ON v.V_id = job.V_id 
                                                JOIN vendor ON vendorid = vendor_id 
                                                WHERE id = ?");
                    if ($dataquery) {
                        $dataquery->bind_param('i', $inserted_id);
                        $dataquery->execute();
                        $result = $dataquery->get_result();

                        if ($result->num_rows > 0) {
                            $data = $result->fetch_assoc();
                            $data = [
                                'to' => $data['vendoremail'],
                                'subject' => 'Request for Quotation - Service Interval (Reg: ' . $data['Reg'] . ')',
                                'template' => '../emailtemplate/vendorEmail.html',
                                'placeholders' => [
                                    'subject' => 'Request for Quotation - Service Interval (Reg: ' . $data['Reg'] . ')',
                                    'body' => '
                                        <p>Dear ' . $data['vendorname'] . ',</p>
                                        <p>We kindly request a quotation for the service interval work order for the vehicle with registration number
                                            <strong>' . $data['Reg'] . '</strong>.
                                        </p>
                                        <p><strong>Details:</strong></p>
                                        <p><strong>Job Title:</strong> ' . $data['jobTitle'] . '<br>
                                        <strong>Reg No:</strong> ' . $data['Reg'] . '<br>
                                        <strong>Description:</strong> ' . $data['description'] . '</p>
                                        <p>Please visit the link below to review the job, approve it, and submit your quotation:</p>
                                        <p><a href="https://mdvr2.itecknologi.com:8080/fms" target="_blank">https://mdvr2.itecknologi.com:8080/fms</a></p>
                                        <p>Feel free to reach out if you need further details.</p>
                                        <p>Thank you,<br><strong>DHL</strong></p>',
                                ],
                                'altBody' => 'We kindly request a quotation for the service interval work order.',
                            ];
                            

                            email($conn, $arr, $data); 
                        } else {
                            $arr["message"] = "No data found for inserted ID.";
                            $arr["success"] = false;
                        }

                        $dataquery->close(); 
                    } else {
                        $arr["message"] = "Data query preparation failed: " . $conn->error;
                        $arr["success"] = false;
                    }
                } else {
                    $arr["message"] = "Invalid inserted ID.";
                    $arr["success"] = false;
                }
            }

            $query->close(); 
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


function updateJob($conn,&$arr){
    if(isset($_POST['id'])){
        $id = $_POST['id'];
        $jobtitle = $_POST['jobtitle'] ?? '';
        $description = $_POST['description'] ?? '';
        $errors = [];
        if (empty($description)) {
            $errors['description'] = "description cannot be empty.";
        }
        if (empty($jobtitle)) {
            $errors['jobtitle'] = "jobtitle cannot be empty.";
        }
        if (empty($errors)) {
            $modifydate = date("Y-m-d"); 
            $modifyby = $_SESSION['user_id'];
            $query = $conn->prepare("update job set jobTitle = ?, description = ?, modifydate = ?, modifyby = ? where id = ?");
            if (!$query) {
                die("Query preparation failed: " . $conn->error); 
            } else {
                if (!$query->bind_param('sssii', $jobtitle, $description, $modifydate, $modifyby, $id)) {
                    die("Parameter binding failed: " . $query->error);
                }
                
                $query->execute();
                if ($query->affected_rows > 0) {
                    $arr["success"] = true;
                    $arr["message"] = "Data updated successfully!";
                    $arr["data"] = "";
                } else {
                    $arr["success"] = false;
                    $checkQuery = $conn->prepare("SELECT id FROM job WHERE id = ?");
                    $checkQuery->bind_param('i', $id);
                    $checkQuery->execute();
                    $checkQuery->store_result();
    
                    if ($checkQuery->num_rows === 0) {
                        $arr["message"] = "Error: Job not found for ID $id.";
                    } else {
                        $arr["message"] = "Error: No changes were made.";
                    }
                }
    
                $query->close(); 
            }
        }else {
            $errorMessages = "Error: ";
            foreach ($errors as $field => $error) {
                $errorMessages .= "$field: $error; "; 
            }
            $errorMessages = rtrim($errorMessages, "; ");
            $arr["message"] = $errorMessages;
        }    
    }else{
        $arr['message'] = "Id is required.";
    }
}

function add($conn, &$arr){
    $V_id = $_POST['V_id']  ?? '';
    $vendor_id = $_POST['vendor_id'] ?? '';
    $service = $_POST['service'] ?? '';
    $jobtitle = $_POST['jobtitle'] ?? '';
    $additionaljob = $_POST['additionaljob'] ?? '';
    $createdby_url = $_SESSION['signature_url'];
    $jobid = $_POST['jobid'];
    $errors = [];
    if (empty($V_id)) {
        $errors['V_id'] = "V_id cannot be empty.";
    }
    if (empty($vendor_id)) {
        $errors['vendor_id'] = "vendor_id cannot be empty.";
    }
    if (empty($service)) {
        $errors['service'] = "service cannot be empty.";
    }
    if (empty($jobtitle)) {
        $errors['jobtitle'] = "jobtitle cannot be empty.";
    }
    if (empty($jobid)) {
        $errors['jobid'] = "jobid cannot be empty.";
    }
    
    if (empty($errors)) {
        $creationdate = date("Y-m-d"); 
        $creationby = $_SESSION['user_id'];
        $status = 'pending';
        $query = $conn->prepare("INSERT INTO workorder(V_id,vendor_id,service,jobtitle,creationdate, creationby,status,additionaljob,createdby_url,job_id) VALUES (?,?,?,?, ?, ?, ?, ?, ?, ?)");
        if (!$query) {
            die("Query preparation failed: " . $conn->error); 
        }else{
            $query->bind_param('iisssisssi',$V_id ,$vendor_id, $service, $jobtitle,  $creationdate, $creationby, $status, $additionaljob,$createdby_url,$jobid);
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


echo json_encode($arr);
?> 