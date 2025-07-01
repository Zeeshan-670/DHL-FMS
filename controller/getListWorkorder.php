<?php
require_once __DIR__ . '/../pdf/vendor/autoload.php';
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json"); 
include("database.php");
include("sendemail.php");
use Mpdf\Mpdf;

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
        if(isset($_POST['type'] )){
            if(isset($_POST['status']) && isset($_POST['id']) && $_POST['type'] == 'update'){
                update($conn, $arr,$_POST['status'], $_POST['id'],$connMileage);
            }else if($_POST['type'] == 'vendorList'){
                getList($conn, $arr,$connMileage);
            }else if($_POST['type'] == 'history'){
                getWorkList($conn,$arr,$connMileage);
            }else if($_POST['type'] == 'filter'){
                filter($conn,$arr,$connMileage);
            }else if($_POST['type'] == 'invoice'){
                invoice($conn,$arr,$_POST['status'],$_POST['id']);
            }else if($_POST['type'] == 'manager'){
                getManager($conn,$arr,$_POST['id']);
            }else if($_POST['type'] == 'getJobList'){
                getJobList($conn,$arr);
            }else{
                $arr['message'] = "Invalid type";
            }
        }else{
            $arr['message'] = "Type is required";
        }
    }else{
        $arr['message'] = "Unauthorized access";

    }
}

//List of jobs
function getJoblist($conn,&$arr){
    $query = $conn->prepare("SELECT j.id,j.V_id,j.jobTitle,j.description,j.vendor_id,j.status,j.creationdate,j.creationby,v.Reg,j.quotation,j.quotationDescription,j.reason, u.name AS createdbyName FROM job j 
    LEFT JOIN vehicledetails v ON v.V_id = j.V_id
    JOIN users u ON j.creationby = u.id");
    if(!$query){
        die("Query preparation failed: " . $conn->error); 
    }else{
        $query->execute();
        $exe = $query->get_result();
        $result = $exe->fetch_all(MYSQLI_ASSOC);

        if($result === false){
            $arr['message'] = "Error: " . mysqli_error($conn);
            $arr['success'] = false;
        }else{
            if(!empty($result)){
                $arr['success'] = true;
                $arr['data'] = $result;        
            }else{
                $arr['message'] = "No data";
                $arr["success"] = false;
            }
        }
    }
}

//List of workorder for users
function getWorkList($conn, &$arr,$connMileage){
    $query = $conn->prepare("SELECT w.id,w.V_id,w.service,w.jobTitle,w.additionaljob,w.creationdate,w.creationby,w.modifydate,w.modifyby,w.status,w.reason,w.createdby_url,w.approvedby_url,u.name AS approvedby_name,
    vendorname,vendoremail,i.amount,i.invoice_url,i.send_date,v.Reg,v.makename,v.modelname,v.stationname,v.engine,v.chassis,v.doi,v.dep_years,v.icn,v.device,v.segment,
    v.pudgtw,v.fuel_type,v.payload,v.date_of_maturity,v.categoryname,w.completiondate,w.remarks, s.stationid FROM workorder w 
    LEFT JOIN invoice i ON work_id = w.id 
    LEFT JOIN vehicledetails v ON v.V_id = w.V_id 
    LEFT JOIN station s ON s.stationname = v.stationname 
    LEFT JOIN vendor ON vendorid = w.vendor_id
    LEFT JOIN users u ON w.approvedby_url = u.signature_url
    where  w.creationdate between ? and ?");
    // city.id = ? and
    if(!$query){
        die("Query preparation failed: " . $conn->error); 
    }else{
        if(isset($_POST['startDate']) && isset($_POST['endDate'])){
            $startDate = $_POST['startDate'];
            $endDate = $_POST['endDate'];
        }else{
            $currentDate = new DateTime();
            $startDate = $currentDate->modify('-1 month')->format('Y-m-d H:i:s');
            $endDate = (new DateTime())->format('Y-m-d H:i:s');
        }
        $query->bind_param('ss',$startDate,$endDate);

        // $query->bind_param('iss',$_SESSION['city_id'],$startDate,$endDate);
        $query->execute();
        $exe = $query->get_result();
        $result = $exe->fetch_all(MYSQLI_ASSOC);

        if($result === false){
            $arr['message'] = "Error: " . mysqli_error($conn);
            $arr['success'] = false;
        }else{
            if(!empty($result)){
                $checkboxes = checkboxes($conn);

                foreach ($result as &$work) {
                    $reg = $work['Reg'];
                    $mileageData = getMileage($connMileage, $reg,$arr);
                    $work['mileage'] = $mileageData;
                    
                    
                    $workId = $work['id'];
                    $underReviewData = underReview($conn, $workId);
                    foreach ($checkboxes as $checkbox) {
                        $value = $checkbox['value'];
                        $work[$value] = isset($underReviewData[$value]) ? true : false;
                    }

                    if (isset($work['reason'])) {
                        $work['reason'] = str_replace("\r\n", " ", $work['reason']); // Normalize line breaks
                        $work['reason'] = trim($work['reason']); // Remove extra spaces
                    }
                }

                if($_SESSION['access'] == 'Full' || $_SESSION['access'] == 'full'){
                    $arr['success'] = true;
                    $arr['data'] = $result;
    
                }else{
                    foreach($result as $r){
                        if($_SESSION['station_id'] == $r['stationid']){
                            $arr['success'] = true;
                            $filteredResults[] = $r; 
                        }
                    }
                
                    if (!empty($filteredResults)) {
                        $arr['success'] = true;
                        $arr['data'] = $filteredResults;
                    } else {
                        $arr['success'] = false;
                        $arr['message'] = 'No matching rows found.';
                    }
                }
                return $result;           
            }else{
                $arr['message'] = "No data";
                $arr["success"] = false;
            }
        }
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

//List of workorder for vendors
function getList($conn, &$arr,$connMileage){
    if(isset($_POST['vendor_id'])){
        $vendor_id = $_POST['vendor_id'];
        $query = $conn->prepare("
            SELECT w.id,w.V_id,w.service,w.jobTitle,w.additionaljob,w.creationdate,w.creationby,w.modifydate,w.modifyby,w.status,w.reason,w.createdby_url,w.approvedby_url,u.name as approvedby_name,
            vendorname,i.amount,i.invoice_url,i.send_date,v.Reg,v.makename,v.modelname,v.stationname,v.engine,v.chassis,v.doi,v.dep_years,v.icn,v.device,v.segment,v.pudgtw,
            v.fuel_type,v.payload,v.date_of_maturity,v.categoryname,w.completiondate,w.remarks FROM workorder w 
            LEFT JOIN invoice i ON work_id = w.id 
            LEFT JOIN vehicledetails v ON v.V_id = w.V_id 
            LEFT JOIN vendor ON vendorid = w.vendor_id
            left join users u on w.approvedby_url = u.signature_url
            where w.vendor_id = ? and w.creationdate between ? and ?");

        if(!$query){
            die("Query preparation failed: " . $conn->error); 
        }else{
            if(isset($_POST['startDate']) && isset($_POST['endDate'])){
                $startDate = $_POST['startDate'];
                $endDate = $_POST['endDate'];
            }else{
                $currentDate = new DateTime();
                $startDate = $currentDate->modify('-1 month')->format('Y-m-d H:i:s');
                $endDate = (new DateTime())->format('Y-m-d H:i:s');
            }
            $query->bind_param('iss',$vendor_id,$startDate,$endDate);
            $query->execute();
            $exe = $query->get_result();
            $result = $exe->fetch_all(MYSQLI_ASSOC);
            

            if($result === false){
                $arr['message'] = "Error: " . mysqli_error($conn);
                $arr['success'] = false;
            }else{
                if(!empty($result)){
                    $checkboxes = checkboxes($conn);

                foreach ($result as &$work) {
                    $reg = $work['Reg'];
                    $mileageData = getMileage($connMileage, $reg,$arr);
                    $work['mileage'] = $mileageData;
                    
                    
                    $workId = $work['id'];
                    $underReviewData = underReview($conn, $workId);
                    foreach ($checkboxes as $checkbox) {
                        $value = $checkbox['value'];
                        $work[$value] = isset($underReviewData[$value]) ? true : false;
                    }

                    if (isset($work['reason'])) {
                        $work['reason'] = str_replace("\r\n", " ", $work['reason']); // Normalize line breaks
                        $work['reason'] = trim($work['reason']); // Remove extra spaces
                    }
                }

                    $arr['success'] = true;
                    $arr['data'] = $result;
                    
                }else{
                    $arr['message'] = "No data";
                    $arr["success"] = false;
                }
            }
        }
    }else{
        $arr['message'] = "Vendor id is required";
    }
}

function checkboxes($conn) {
    $checkbox_query = $conn->prepare("SELECT id, value FROM checkboxes");
    $checkbox_query->execute();
    $result = $checkbox_query->get_result();
    $checkboxes = [];
    while ($row = $result->fetch_assoc()) {
        $checkboxes[] = $row; 
    }
    return $checkboxes;
}

function getManager($conn,&$arr, $id) {
    $getUser = $conn->prepare("SELECT users.access FROM users JOIN workorder w ON w.creationby = users.id WHERE w.id = ?");
    if (!$getUser) {
        die("Query preparation failed: " . $conn->error);
    }
    $getUser->bind_param('i', $id);
    $getUser->execute();
    $result = $getUser->get_result();
    $user = $result->fetch_assoc();
    if (!$user) {
        return [
            'success' => false,
            'message' => 'User not found',
            'data' => []
        ];
    }

    $access = $user['access'];
   
    $query = $conn->prepare("SELECT users.name, title, access, s.stationname AS station FROM designation 
        JOIN users ON designation_id = designation.id 
        JOIN station s ON s.stationid = users.station_id 
        WHERE access = ? AND title LIKE '%Manager%'");
    if (!$query) {
        die("Query preparation failed: " . $conn->error);
    }    
    $query->bind_param('s', $access);
    $query->execute();
    $result = $query->get_result();
    $managers = [];
    while ($row = $result->fetch_assoc()) {
        $managers[] = $row; 

    }

    $arr['message'] = "Mangers for the workorder";
    $arr['success'] = true;
    $arr['data'] = $managers;


    return [
        'success' => true,
        'data' => $managers
    ];
}


//workorder status update
// function update($conn, &$arr, $status, $id,$connMileage) {
//     $modifydate = date("Y-m-d");
//     $modifyby = $_SESSION['user_id'];
//     $reason = $_POST['reason'] ?? ''; 
//     $approvedby = "";
//     $query = $conn->prepare("SELECT w.status as workorder, j.creationby, j.status as jobstatus FROM workorder w join job j on j.id = w.job_id WHERE id = ?");
//     if (!$query) {
//         die("Query preparation failed: " . $conn->error);
//     }

//     $query->bind_param("i", $id);
//     $query->execute();
//     $result = $query->get_result();
//     $statusResult = $result->fetch_assoc();

//     $currentWorkOrder = $statusResult['workorder'];
//     $jobStatus = $statusResult['jobstatus'];
//     $requester = $statusResult['creationby'];

//     if (!$currentWorkOrder) {
//         $arr['message'] = "Work order not found";
//         $arr['success'] = false;
//         return;
//     }

//     if ($status == 'under review') {
//         if ($currentWorkOrder['status'] != 'in progress' && $currentWorkOrder['status'] != 'rejected by dhl' ) {
//             $arr['message'] = "Only in progress status can be changed to under review";
//             $arr['success'] = false;
//             return;
//         } else{
//             invoice($conn,$arr,$status,$id,$currentWorkOrder['status']);
//         }
//     }else if ($status == 'accept' || $status == 'reject'){
//         if($jobStatus != 'submit quotation'){
//             $arr['message'] == "Quotation can only be reviewed after quotation is submitted";
//             $arr['success'] == false;
//             return;
//         }else{
//             if($status == 'reject'){
//                 if(isset($_POST['reason'])){
//                     $reason = $_POST['reason'];
//                 }else{
//                     $arr['message'] = "Reason cannot be empty";
//                     return;
//                 }
//             }
//         }
//     }else if ($status == 'approved workorder') {
//         if ($currentWorkOrder['status'] != 'pending') {
//             $arr['message'] = "Only pending status can be changed to approved";
//             $arr['success'] = false;
//             return;
//         } else{
//             $managers = getManager($conn,$arr, $id);

//             if (!$managers['success']) {
//                 $arr['success'] = false;
//                 $arr['message'] = $managers['message'];
//                 return;
//             }
            
//             $isManager = false;
//             foreach ($managers['data'] as $manager) {
//                 if ($manager['name'] === $_SESSION['name']) { 
//                     $isManager = true;
//                     break;
//                 }
//             }
            
//             if (!$isManager) {
//                 $arr['success'] = false;
//                 $arr['message'] = 'Permission denied. You are not authorized to approve this work order.';
//                 return;
//             }
    
//         }
//     }else if ($status == 'approved invoice') {
//         if($_SESSION['name'] == 'Jawaid Khalid'){
//             if ($currentWorkOrder['status'] != 'under review') {
//                 $arr['message'] = "Only under review status can be changed to approved";
//                 $arr['success'] = false;
//                 return;
//             } else{
//                 invoice($conn,$arr,$status,$id,$currentWorkOrder['status']);
//             }
//         }else{
//             $arr['message'] = 'Unauthorized access! Only Jawaid Khalid can approve invoice';
//             $arr['success'] = false;
//             return;
//         }
//     }
//     else if($status == 'in progress'){
//         if($currentWorkOrder['status'] != 'approved workorder'){
//             $arr['message'] = "Only manager approved status can be changed to in progress";
//             $arr['success'] = false;
//             return;
//         }
//     }else if ($status == 'rejected') {
//         if(empty($reason)){
//             die("Reason cannot be empty"); 
//         }else{
//             if($_SESSION['type'] == 'admin' || $_SESSION['type'] == 'User'){
//                 $status .= ' by dhl';
//                 if($currentWorkOrder['status'] != 'under review'){
//                     $arr['message'] = "Only under review status can be rejected";
//                     $arr['success'] = false;
//                     return;
//                 }        
//             }else if($_SESSION['type'] == 'vendor'){
//                 $status .= ' by vendor';
//                 if($currentWorkOrder['status'] != 'pending'){
//                     $arr['message'] = "Only pending status can be rejected";
//                     $arr['success'] = false;
//                     return;
//                 }
//             } 
//         }
//     }else if ($status == 'completed' ) {
//         if($currentWorkOrder['status'] != 'approved invoice'){
//             $arr['message'] = "Only invoice approved status cann be changed to completed";
//             $arr['success'] = false;
//             return;
//         }else {

//             $remarks = $_POST['remarks'] ?? '';
//             $completiondate = $_POST['completiondate'] ?? '';
//             $checkboxData = $_POST['checkbox'] ?? '{}';
//             $approvedby = $_SESSION['signature_url'];
//             $errors = [];
//             if (empty($remarks)) {
//                 $errors['remarks'] = "remarks cannot be empty.";
//             }
//             if (empty($completiondate)) {
//                 $errors['completiondate'] = "completiondate cannot be empty.";
//             }
//             if (empty($errors)) {
//                 $query = $conn->prepare("UPDATE workorder SET status = ?, remarks = ?, modifydate = ?, modifyby = ?, completiondate = ?, approvedby_url = ? WHERE id = ?");
//                 if (!$query) {
//                     die("Query preparation failed: " . $conn->error);
//                 }
//                 $query->bind_param('sssissi', $status, $remarks, $modifydate, $modifyby,$completiondate, $approvedby,  $id);
//                 $exe = $query->execute();
//                 if ($exe === false) {
//                     $arr['message'] = "Error: " . $conn->error;
//                     $arr['success'] = false;
//                 } else {
                  
//                     $checkboxes = checkboxes($conn);
//                     $c['checkbox'] = json_decode($checkboxData, true);
//                     $validCheckboxes = [];
//                     foreach ($c['checkbox'] as $key => $value) {
//                         if ($value === true) {
//                             foreach ($checkboxes as $checkbox) {
//                                 if ($key == $checkbox['value']) {
//                                     $validCheckboxes[] = $checkbox['id'];
//                                 }
//                             }
//                         }
//                     }
//                     if (!empty($validCheckboxes)) {
//                         $id = (int) $id;  
//                         $checkboxQuery = $conn->prepare("INSERT INTO underReview (w_id, value_id) VALUES (?, ?)");
//                         if (!$checkboxQuery) {
//                             die("Query preparation failed: " . $conn->error);
//                         }
//                         foreach ($validCheckboxes as $checkboxId) {
//                             $checkboxQuery->bind_param('ii', $id, $checkboxId);
//                             if (!$checkboxQuery->execute()) {
//                                 die("Execution failed: " . $checkboxQuery->error); 
//                             }
//                         }   
//                     }

//                     $arr['message'] = "Complete Status updated successfully";
//                     $arr['success'] = true;

//                     $results = getWorkList($conn,$arr,$connMileage);
//                     foreach($results as $result){
//                         if($result['id'] == $id){
//                             $arr['data'] = $result;
//                         }
//                     }
                    
                    
//                 }
//                 return;
//             }else{
//                 $errorMessages = "Error: ";
//                 foreach ($errors as $field => $error) {
//                     $errorMessages .= "$field: $error; "; 
//                 }
//                 $errorMessages = rtrim($errorMessages, "; ");
//                 $arr["message"] = $errorMessages;
//             }
//         }
//     }else if (!in_array($status, ['rejected by dhl','rejected by vendor' ,'in progress', 'under review', 'completed','approved workorder','approved invoice','review quotation'])) {
//         $arr['message'] = "Invalid status";
//         $arr['success'] = false;
//         return;
//     }

//     if(in_array($status, ['rejected by dhl','rejected by vendor' ,'in progress', 'under review','approved workorder','approved invoice'])){
//         $query = $conn->prepare("UPDATE workorder SET status = ?, reason = ?, modifydate = ?, modifyby = ?  WHERE id = ?");
//         if (!$query) {
//             die("Update Query preparation failed: " . $conn->error);
//         }
//         $query->bind_param('sssii', $status, $reason, $modifydate, $modifyby, $id);
//         $exe = $query->execute();
//         if ($exe === false) {
//             $arr['message'] = "Error: " . $conn->error;
//             $arr['success'] = false;
//         } else {
//             $arr['message'] = "Status updated successfully";
//             $arr['success'] = true;
//             $arr['data'] = [];
//         }
//     }else if(in_array($status, ['accept', 'reject'])){
//         $query = $conn->prepare("UPDATE job SET status = ?, reason = ? modifydate = ?, modifyby = ?  WHERE id = ?");
//         if (!$query) {
//             die("Update Query preparation failed: " . $conn->error);
//         }
//         $query->bind_param('sssii', $status, $reason, $modifydate, $modifyby, $id);
//         $exe = $query->execute();
//         if ($exe === false) {
//             $arr['message'] = "Error: " . $conn->error;
//             $arr['success'] = false;
//         } else {
//             $arr['message'] = "Status updated to ".$status." successfully";
//             $arr['success'] = true;
//             $arr['data'] = [];
//         }
//     }   
// }

function update($conn, &$arr, $status, $id,$connMileage) {
    $modifydate = date("Y-m-d");
    $modifyby = $_SESSION['user_id'];
    $reason = $_POST['reason'] ?? ''; 
    $approvedby = "";
    $query = $conn->prepare("SELECT w.status as workorder, j.creationby, j.status as jobstatus FROM workorder w join job j on j.id = w.job_id WHERE w.id = ?");
    if (!$query) {
        die("Query preparation failed: " . $conn->error);
    }

    $query->bind_param("i", $id);
    $query->execute();
    $result = $query->get_result();
    $statusResult = $result->fetch_assoc();

  
    if (!$statusResult) {
        $arr['message'] = "Work order not found";
        $arr['success'] = false;
        return;
    }

    $currentWorkOrder = $statusResult['workorder'];
    $jobStatus = $statusResult['jobstatus'];
    $requester = $statusResult['creationby'];

    if ($status == 'Job Completion by vendor') {
        if ($currentWorkOrder != 'pending' && $jobStatus != 'accept') {
            $arr['message'] = "Only accepted job can be marked completed";
            $arr['success'] = false;
            return;
        }
    }else if ($status == 'workorder Completion by dhl') {
        if ($currentWorkOrder != 'Job Completion by vendor' && $jobStatus != 'accept') {
            $arr['message'] = "Only job completed by vendor's workorder can be marked completed'";
            $arr['success'] = false;
            return;
        }
    }else if ($status == 'in progress') {
        if ($currentWorkOrder != 'workorder Completion by dhl' && $jobStatus != 'accept') {
            $arr['message'] = "Invoice can only be added when workorder marked completed";
            $arr['success'] = false;
            return;
        } else{
            invoice($conn, $arr, $status, $id, $currentWorkOrder);
        }
    }else if ($status == 'under review' ) {
        if($currentWorkOrder != 'in progress'){
            $arr['message'] = "Only in progress can be changed to under review";
            $arr['success'] = false;
            return;
        }else {
            $remarks = $_POST['remarks'] ?? '';
            $completiondate = $_POST['completiondate'] ?? '';
            $checkboxData = $_POST['checkbox'] ?? '{}';
            $approvedby = $_SESSION['signature_url'];
            $errors = [];
            if (empty($remarks)) {
                $errors['remarks'] = "remarks cannot be empty.";
            }
            if (empty($completiondate)) {
                $errors['completiondate'] = "completiondate cannot be empty.";
            }
            if (empty($errors)) {
                $query = $conn->prepare("UPDATE workorder SET status = 'Completed', remarks = ?, modifydate = ?, modifyby = ?, completiondate = ?, approvedby_url = ? WHERE id = ?");
                if (!$query) {
                    die("Query preparation failed: " . $conn->error);
                }
                $query->bind_param('ssissi', $remarks, $modifydate, $modifyby,$completiondate, $approvedby,  $id);
                $exe = $query->execute();
                if ($exe === false) {
                    $arr['message'] = "Error: " . $conn->error;
                    $arr['success'] = false;
                } else {
                  
                    $checkboxes = checkboxes($conn);
                    $c['checkbox'] = json_decode($checkboxData, true);
                    $validCheckboxes = [];
                    foreach ($c['checkbox'] as $key => $value) {
                        if ($value === true) {
                            foreach ($checkboxes as $checkbox) {
                                if ($key == $checkbox['value']) {
                                    $validCheckboxes[] = $checkbox['id'];
                                }
                            }
                        }
                    }
                    if (!empty($validCheckboxes)) {
                        $id = (int) $id;  
                        $checkboxQuery = $conn->prepare("INSERT INTO underReview (w_id, value_id) VALUES (?, ?)");
                        if (!$checkboxQuery) {
                            die("Query preparation failed: " . $conn->error);
                        }
                        foreach ($validCheckboxes as $checkboxId) {
                            $checkboxQuery->bind_param('ii', $id, $checkboxId);
                            if (!$checkboxQuery->execute()) {
                                die("Execution failed: " . $checkboxQuery->error); 
                            }
                        }   
                    }

                    $arr['message'] = "Status updated to under review successfully";
                    $arr['success'] = true;

                    $results = getWorkList($conn,$arr,$connMileage);
                    foreach($results as $result){
                        if($result['id'] == $id){
                            $arr['data'] = $result;
                        }
                    }
                }
                return;
            }else{
                $errorMessages = "Error: ";
                foreach ($errors as $field => $error) {
                    $errorMessages .= "$field: $error; "; 
                }
                $errorMessages = rtrim($errorMessages, "; ");
                $arr["message"] = $errorMessages;
            }
        }
    }else if ($status == 'confirm') {
        if($_SESSION['user_id'] == $requester){
            if ($currentWorkOrder != 'under review') {
                $arr['message'] = "Only in progress status can be changed to approved";
                $arr['success'] = false;
                return;
            }
            // $ids = ['2','3'];
            // send($conn,$arr,$ids);
            $dataquery = $conn->prepare("SELECT users.id, name, email, Reg, w.jobTitle,description
                                                FROM users 
                                                LEFT JOIN workorder w ON w.creationby = users.id
                                                LEFT JOIN job ON job.id = w.job_id
                                                LEFT JOIN vehicledetails v ON v.V_id = job.V_id 
                                                LEFT JOIN vendor ON vendorid = w.vendor_id  
                                                WHERE users.id = ? and w.id = ?");
                    if ($dataquery) {
                        $dataquery->bind_param('ii', $requester, $id);
                        $dataquery->execute();
                        $result = $dataquery->get_result();

                        if ($result->num_rows > 0) {
                            $data = $result->fetch_assoc();
                            $data = [
                                'to' => $data['email'],
                                'subject' => 'WorkOrder confirmed (Reg: ' . $data['Reg'] . ')',
                                'template' => '../emailtemplate/vendorEmail.html',
                                'placeholders' => [
                                    'subject' => 'WorkOrder confirmed (Reg: ' . $data['Reg'] . ')',
                                    'body' => '
                                        <p>Dear ' . $data['name'] . ',</p>
                                        <p>This work order for the vehicle with registration number
                                            <strong>' . $data['Reg'] . '</strong> has been confirmed.
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
        }else{
            $arr['message'] = 'Unauthorized access!';
            $arr['success'] = false;
            return;
        }
    }else if (!in_array($status, ['under review', 'completed','in progress','Job Completion by vendor', 'workorder Completion by dhl', 'confirm'])) {
        $arr['message'] = "Invalid status";
        $arr['success'] = false;
        return;
    }



    if(in_array($status, ['in progress','Job Completion by vendor', 'workorder Completion by dhl', 'confirm','under review'])){
        $query = $conn->prepare("UPDATE workorder SET status = ?, reason = ?, modifydate = ?, modifyby = ?  WHERE id = ?");
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
    }   
}


//get under review checkboxes data
function underReview($conn, $id){
    $underReview = $conn->prepare("SELECT w_id, value FROM underReview  u join checkboxes c on c.id = u.value_id where u.w_id = ?");
    $underReview->bind_param('i', $id);
    $underReview->execute();
    $result = $underReview->get_result();

    $reviewedValues = []; 
    $response = [];

    while ($row = $result->fetch_assoc()) {
        $reviewedValues[$row['value']] = true;
    }    
   
    
    return $reviewedValues;
}

//add invoice after "in progress"
function invoice($conn, &$arr,$status, $id,$old){
    $work_id = $_POST['id']  ?? '';
    $vendor_id = $_POST['vendor_id'] ?? '';
    $amount = $_POST['amount'] ?? '';
    $contentFile = $_FILES['contentFile'] ?? '';
    $errors = [];
    if (empty($work_id)) {
        $errors['work_id'] = "work_id cannot be empty.";
    }
    if (empty($vendor_id)) {
        $errors['vendor_id'] = "vendor_id cannot be empty.";
    }
    if (empty($amount)) {
        $errors['amount'] = "amount cannot be empty.";
    }
    if(empty($contentFile)){
        $errors['contentFile'] = "contentFile cannot be empty";
    }
    if (empty($errors)) {
        
        $creationdate = date("Y-m-d"); 
        $creationby = $_SESSION['user_id'];
        $query = $conn->prepare("INSERT INTO invoice(work_id,vendor_id,amount,creationdate, creationby) VALUES (?,?, ?, ?, ?)");
        if(!$query){
            die("Query preparation failed: ". $conn->error);
        }
        $query->bind_param('iiisi',$work_id ,$vendor_id, $amount,  $creationdate, $creationby);
        $exe=$query->execute();

        if($exe === false){
            $arr["message"] = "Error: " . mysqli_error($conn);
            $arr["success"]=false;
        }else{
            $inserted_id = $conn->insert_id; 
            $filename = $_FILES['contentFile']['name']; 
            $file_extension = pathinfo($filename, PATHINFO_EXTENSION);

            $pdfurl = $inserted_id . '.' . $file_extension;

            $insert = $conn->prepare("update invoice set invoice_url = ? where id = ?");
            if (!$insert) {
                die("Query preparation failed: " . $conn->error); 
            }else{
                $insert->bind_param('si', $pdfurl, $inserted_id);
                $execute=$insert->execute();
                if($execute === false){
                    $arr["message"] = "Error: " . mysqli_error($conn);
                    $arr["success"]=false;
                }else{
                    $arr["success"]=true;
                    $arr["message"] = "Data inserted Successfully!";
                    $arr["data"] = "";
                    download($conn, $arr, $pdfurl);
                }
            }
        }
            
    }else {
        $arr["success"]=false;

        $errorMessages = "Error: ";
        foreach ($errors as $field => $error) {
            $errorMessages .= "$field: $error; "; 
        }
        $errorMessages = rtrim($errorMessages, "; ");
        $arr["message"] = $errorMessages;
    }
}

//download invoice pdf
function download($conn, &$arr, $pdfurl) {
    try {
        $folderPath = __DIR__ . '/../invoice/'; 
        $fileExtension = pathinfo($pdfurl, PATHINFO_EXTENSION);
        $finalFilePath = $folderPath . $pdfurl;

        if (!is_dir($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        $uploadedFilePath = $_FILES['contentFile']['tmp_name'];

        // $fileName = $pdfurl; // Use original file name or pass a dynamic name.
        // $filePath = $folderPath . $fileName;

        // if (!is_dir($folderPath)) {
        //     mkdir($folderPath, 0777, true);
        // }

        // $uploadedFilePath = $_FILES['contentFile']['tmp_name'];
        // $uploadedFileType = mime_content_type($uploadedFilePath);

        // // Determine file extension from MIME type
        // $fileExtension = '';
        // switch ($uploadedFileType) {
        //     case 'application/pdf':
        //         $fileExtension = '.pdf';
        //         break;
        //     case 'image/jpeg':
        //         $fileExtension = '.jpg';
        //         break;
        //     case 'image/png':
        //         $fileExtension = '.png';
        //         break;
        //     case 'image/gif':
        //         $fileExtension = '.gif';
        //         break;
        //     case 'image/webp':
        //         $fileExtension = '.webp';
        //         break;
        //     // Add additional MIME types as needed
        //     default:
        //         throw new Exception('Unsupported file type.');
        // }

        // // Generate the final file name with the correct extension
        // $finalFilePath = $folderPath . basename($fileName, ".pdf") . $fileExtension;

        // Move the uploaded file to the desired location with the correct extension
        if (move_uploaded_file($uploadedFilePath, $finalFilePath)) {
            $arr['message'] = ucfirst(pathinfo($finalFilePath, PATHINFO_EXTENSION)) . " uploaded and saved successfully.";
            $arr['success'] = true;
        } else {
            $arr['message'] = "Failed to save the uploaded file.";
            $arr['success'] = false;
        }

        // return $finalFilePath;

    } catch (Exception $e) {
        $arr['message'] = "Error: " . $e->getMessage();
        $arr['success'] = false;
    }

    // var_dump($arr);
    // die;
}


//filter list on workorder status
function filter($conn,&$arr,$connMileage){
    if(isset($_POST['filter'])){
        $filter = $_POST['filter'];
        if(!in_array($filter, ['rejected', 'in progress', 'under review', 'completed','pending'])){
            $arr['message'] = "Invalid filter";
        }else{
            $query = $conn->prepare("SELECT w.id,w.V_id,w.service,w.jobTitle,w.additionaljob,w.creationdate,w.creationby,w.modifydate,w.modifyby,w.status,w.reason,w.createdby_url,
            w.approvedby_url,vendorname,i.amount,i.invoice_url,u.name as approvedby_name,i.send_date,v.Reg,v.makename,v.modelname,v.stationname,v.engine,v.chassis,v.doi,v.dep_years,
            v.icn,v.device,v.segment,v.pudgtw,v.fuel_type,v.payload,v.date_of_maturity,v.categoryname,w.completiondate,w.remarks FROM workorder w 
            LEFT JOIN invoice i ON work_id = w.id 
            LEFT JOIN vehicledetails v ON v.V_id = w.V_id 
            left join city on city.name = stationname 
            LEFT JOIN vendor ON vendorid = w.vendor_id
            left join users u on w.approvedby_url = u.signature_url
            where w.status = ? ");
            if(!$query){
                die("Query preparation failed: " . $conn->error);
            }
            $query->bind_param("s", $filter);
            $query->execute();
            $exe = $query->get_result();
            $result = $exe->fetch_all(MYSQLI_ASSOC);

            if($result === false){
                $arr['message'] = "Error: " . mysqli_error($conn);
                $arr['success'] = false;
            }else{
                if(!empty($result)){
                    $checkboxes = checkboxes($conn);

                foreach ($result as &$work) {
                    $reg = $work['Reg'];
                    $mileageData = getMileage($connMileage, $reg,$arr);
                    $work['mileage'] = $mileageData;
                    
                    
                    $workId = $work['id'];
                    $underReviewData = underReview($conn, $workId);
                    foreach ($checkboxes as $checkbox) {
                        $value = $checkbox['value'];
                        $work[$value] = isset($underReviewData[$value]) ? true : false;
                    }

                    if (isset($work['reason'])) {
                        $work['reason'] = str_replace("\r\n", " ", $work['reason']); // Normalize line breaks
                        $work['reason'] = trim($work['reason']); // Remove extra spaces
                    }
                }

                    $arr['success'] = true;
                    $arr['data'] = $result;
                }else{
                    $arr['message'] = "No data";
                    $arr["success"] = false;
                }
            }        
        }
    }else{
        $arr['message'] = "Filter is required";
    }
}


echo json_encode($arr);
?>