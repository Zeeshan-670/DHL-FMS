<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

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
$data = '';
if (!empty(file_get_contents("php://input")) && is_json(file_get_contents("php://input"))) {
    $data = json_decode(file_get_contents("php://input"), true);
} elseif (!empty($_POST['json_data'])) {
    $data = json_decode($_POST['json_data'], true);
}
function is_json($string) {
    json_decode($string);
    return (json_last_error() === JSON_ERROR_NONE);
}

if ($conn === false) {
    $arr['message'] = 'DB connecttion failed';
    $arr['success'] = false;
    $arr['error'] = sqlsrv_errors();
    echo json_encode($arr);
    exit;
}else{
    if(isset($_SESSION['user_id'])){
        if(isset($data['type'])){
            if($data['type'] == 'rfq'){
                addrfq($conn, $arr,$data);    
            }else if($data['type'] == 'history'){
                getList($conn,$arr,$data);
            }else if($data['type'] == 'historyDhl'){
                getListDhl($conn,$arr,$data);
            }else if($data['type'] == 'update'){
                update($conn,$arr,$data);
            }else if($data['type'] == 'manager'){
                getManager($conn,$arr,$data['rfq_id']);
            }
        }
    }else{
        $arr['message'] = "Unauthorized access";
    }
}

function sendEmailFromQuery($conn, &$arr, $query, $bindings, $subjectPrefix, $emailBodyTemplate,  $recipients = []) {
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        $arr["success"] = false;
        $arr["message"] = "Data query preparation failed: " . $conn->error;
        return;
    }

    $stmt->bind_param(...$bindings);

    if (!$stmt->execute()) {
        $arr["success"] = false;
        $arr["message"] = "Query execution failed: " . $conn->error;
        return;
    }

    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();

        $subject = $subjectPrefix . ' (Reg: ' . $data['Reg'] . ')';
        $body = str_replace(
            ['{vendorname}', '{Reg}', '{jobTitle}', '{description}'],
            [$data['vendorname'], $data['Reg'], $data['jobTitle'], $data['description']],
            $emailBodyTemplate
        );

        if(!empty($recipients)){
            foreach ($recipients as $recipient) {
                $username = $data['username'] ?? '';
                $subject = $subjectPrefix . ' (Reg: ' . $data['Reg'] . ')';
                $body = str_replace(
                    ['{name}', '{Reg}', '{jobTitle}', '{description}', '{username}'],
                    [$recipient['name'], $data['Reg'], $data['jobTitle'], $data['description'], $username],
                    $emailBodyTemplate
                );
    
                $emailData = [
                    'to' => $recipient['email'],
                    'subject' => $subject,
                    'template' => '../emailtemplate/vendorEmail.html',
                    'placeholders' => [
                        'subject' => $subject,
                        'body' => $body,
                    ],
                    'altBody' => "Quotation Approved - Service Interval for vehicle with registration: " . $data['Reg'],
                ];

                email($conn, $arr, $emailData);
            }
        }
        if(empty($recipients)){
            $emailData = [
                'to' => $data['email'],
                'subject' => $subject,
                'template' => '../emailtemplate/vendorEmail.html',
                'placeholders' => [
                    'subject' => $subject,
                    'body' => $body,
                ],
                'altBody' => $subject,
            ];
    
            email($conn, $arr, $emailData);

        }
    } else {
        $arr["success"] = false;
        $arr["message"] = "No matching record found.";
    }
}

function addrfq($conn,&$arr,$data){
    if($_SESSION['type'] == 'admin' || $_SESSION['type'] == 'User'){
        $V_id = $data['V_id']  ?? '';
        $vendor_id = $data['vendor_id'] ?? '';
        $jobtitle = $data['jobtitle'] ?? '';
        $description = $data['description'] ?? '';
        $department = $data['department'] ?? '';
        $costCenter = '-';
        $expenses = $data['expenses'] ?? null; 

        if (!is_array($expenses)) {
            $expenses = json_decode(file_get_contents('php://input'), true)['expenses'] ?? [];
        }
            $creationdate = date("Y-m-d H:i:s"); 

        // Calculate the GrandTotal
        $total = array_reduce($expenses, function ($sum, $expense) use ($conn){
            $salesTax = getSalesTax($conn, $expense['tax']);        
            $amount = (float)$expense['amount'];
            $tax = (float)$salesTax;
            $amountWithTax = $amount * (1 + ($tax / 100));
        
            return $sum + $amountWithTax;
        }, 0);
       

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
            $creationdate = date("Y-m-d H:i:s"); 
            $creationby = $_SESSION['user_id'];
            $status = "Pending";
            $type = "user";

            $conn->begin_transaction(); 

            $query = $conn->prepare("INSERT INTO job(V_id, vendor_id, jobTitle, description, creationdate, creationby, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
            if (!$query) {
                $arr["success"] = false;
                $arr["message"] = "Error preparing expense entry statement: ".$conn->error;
                exit;
            } else {
                if (!$query->bind_param('iisssis', $V_id, $vendor_id, $jobtitle, $description, $creationdate, $creationby, $status)) {
                    die("Parameter binding failed: " . $query->error);
                }
                if (!$query->execute()) {
                    $arr["success"] = false;
                    $arr["message"] = "Error inserting expense entry: ".$conn->error;
                    exit;
                }else {
                    $arr["success"] = true;
                    $arr["message"] = "Data inserted successfully!";
                    $arr["data"] = "";

                    $inserted_id = $conn->insert_id;
                    $recipients = [];
                    if ($inserted_id > 0) { 
                        $query = "SELECT v.Reg, jobTitle, description, vendorname, vendoremail as email
                            FROM job 
                            JOIN vehicledetails v ON v.V_id = job.V_id 
                            JOIN vendor ON vendorid = vendor_id 
                            WHERE id = ?";

                            $bindings = ['i', $inserted_id]; 

                            $subjectPrefix = 'Request for Quotation - Service Interval';
                            $emailBodyTemplate = '
                                <p>Dear {vendorname},</p>
                                <p>We kindly request a quotation for the service interval work order for the vehicle with registration number
                                    <strong>{Reg}</strong>.
                                </p>
                                <p><strong>Details:</strong></p>
                                <p><strong>Job Title:</strong> {jobTitle}<br>
                                <strong>Reg No:</strong> {Reg}<br>
                                <strong>Description:</strong> {description}</p>
                                <p>Please visit the link below to review the job, approve it, and submit your quotation:</p>
                                <p><a href="https://mdvr2.itecknologi.com:8080/fms" target="_blank">https://mdvr2.itecknologi.com:8080/fms</a></p>
                                <p>Feel free to reach out if you need further details.</p>
                                <p>Thank you,<br><strong>DHL</strong></p>';

                            sendEmailFromQuery($conn, $arr, $query, $bindings, $subjectPrefix, $emailBodyTemplate,$recipients);

                        } else {
                            $arr["message"] = "No data found for inserted ID.";
                            $arr["success"] = false;
                        }

                    } 

                try {

                    $sql = "INSERT INTO rfq_entries (V_Id, GrandTotal,vendor_id, CreatedBy, creationdate, rfq_id) 
                            VALUES (?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);

                    if (!$stmt) {
                        $arr["success"] = false;
                        $arr["message"] = "Error preparing expense entry statement: ".$conn->error;
                        exit;
                    }

                    $stmt->bind_param("idiisi", $V_id, $total,$vendor_id, $creationby, $creationdate, $inserted_id);

                    if (!$stmt->execute()) {
                        $arr["success"] = false;
                        $arr["message"] = "Error inserting expense entry: ".$conn->error;
                        exit;
                    }
                    
                    // $expense_id = $stmt->insert_id;
                    $stmt->close();

                    $detail_sql = "INSERT INTO expense_details (rfq_id, CategoryId, Amount, Description, salesTax) 
                                VALUES (?, ?, ?, ?, ?)";
                    $detail_stmt = $conn->prepare($detail_sql);

                    if (!$detail_stmt) {
                        $arr["success"] = false;
                        $arr["message"] = "Error preparing expense details statement: ".$conn->error;
                        exit;
                    }

                    foreach ($expenses as $expense) {
                        $expenseCategory = $expense['expenseCategory'];
                        $amount = $expense['amount'];
                        $description = $expense['description'];
                        $tax = $expense['tax'];

                        $salesTax = getSalesTax($conn, $tax);        

                        $totalWithTax = number_format(($amount * (1 + ($salesTax / 100))), 2, '.', '');

                        $detail_stmt->bind_param("isdsi", $inserted_id, $expenseCategory, $totalWithTax, $description, $tax);

                        if (!$detail_stmt->execute()) {
                            $arr["success"] = false;
                            $arr["message"] = "Error inserting expense details: ".$conn->error;
                            exit;
                        }
                    }

                    $detail_stmt->close();

                    rfqUpdateHistory($conn,$inserted_id,$creationby,$creationdate,$status,$type);

                    $conn->commit();
                    $arr["success"] = true;
                    $arr["message"] = "RFQ entry created successfully";
                } catch (Exception $e) {
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
    }else{
        $arr['message'] = "Unauthorized access";
    }
}

function rfqUpdateHistory($conn,$id,$by,$date,$status,$type){
    $rfqUpdateHistory = "INSERT INTO rfqUpdateHistory (rfq_id, modifiedby, date, status,modifier_type) 
            VALUES (?, ?, ?, ?, ?)";
    $rfq_stmt = $conn->prepare($rfqUpdateHistory);

    if (!$rfq_stmt) {
        $arr["success"] = false;
        $arr["message"] = "Error preparing expense entry statement: ".$conn->error;
        exit;
    }

    $rfq_stmt->bind_param("iisss",$id,$by,$date,$status,$type);

    if (!$rfq_stmt->execute()) {
        $arr["success"] = false;
        $arr["message"] = "Error inserting expense entry: ".$conn->error;
        exit;
    }

    $rfq_stmt->close();
}

function update($conn, &$arr, $data) {
    $functionMap = [
        'quotation' => 'quotation',
        'quotationRecieved' => 'quotationRecieved',
        'quotationApproved' => 'quotationApproved',
        'jobSatisfaction' => 'jobSatisfaction',
        'invoiceRecieved' => 'invoiceRecieved',
        'jobSatisfactionApprove' => 'jobSatisfactionApprove',
        'higherAuthority' => 'higherAuthority',
        'invoiceApprove' => 'invoiceApprove'
    ];

    if (isset($functionMap[$data['updateType']])) {
        call_user_func_array($functionMap[$data['updateType']], [&$conn, &$arr, &$data]);
    } else {
        throw new Exception("Invalid updateType: " . $data['updateType']);
    }
}

function quotation($conn, &$arr, $data) {
    if($_SESSION['type'] == 'vendor'){
        $creationdate = date("Y-m-d H:i:s");
        $creationby = $_SESSION['user_id'] ?? null;
        $type = "vendor";
        $expenses = $data['expenses'] ?? [];
        $rfq_id = $data['rfq_id'];
            
        $status = "submit quotation";
        $statusResult = checkStatus($conn,$arr,$rfq_id);
        $jobStatus = $statusResult['status'];
        if($jobStatus == 'Pending' || $jobStatus == 'return'){
            if (empty($expenses)) {
                $arr['success'] = false;
                $arr['message'] = "Expenses data is required.";
                return;
            }
            $conn->begin_transaction();
            try {
                foreach ($expenses as $expense) {
                    $expenseCategory = $expense['expenseCategory'];
                    $amount = $expense['amount'];
                    $description = $expense['description'];
                    $tax = $expense['tax'];
                    $ExpenseDetailId = $expense['ExpenseDetailId'] ?? null;

                    if ($rfq_id && $ExpenseDetailId) {
                        $currentQuery = "SELECT Amount, salesTax FROM expense_details WHERE ExpenseDetailId = ? AND rfq_id = ?";
                        $stmt = $conn->prepare($currentQuery);
                        $stmt->bind_param("ii", $ExpenseDetailId, $rfq_id);
                        $stmt->execute();
                        $current = $stmt->get_result()->fetch_assoc();

                        $currentAmount = (float)$current['Amount'];
                        $currentTax = (float)$current['salesTax'];

                        if ((float)$amount !== (float)$currentAmount || (float)$tax !== (float)$currentTax){
                            $salesTax = getSalesTax($conn, $tax);
                            $totalWithTax = number_format(($amount * (1 + ($salesTax / 100))), 2, '.', '');

                            $query1 = "UPDATE expense_details SET Amount = ?, description = ?, salesTax = ? WHERE ExpenseDetailId = ? AND rfq_id = ?";
                            executeUpdate($conn, $query1, "dsiii", $totalWithTax, $description, $tax, $ExpenseDetailId, $rfq_id);
                        }
                    } else {
                        $salesTax = getSalesTax($conn, $tax);        

                        $totalWithTax = number_format(($amount * (1 + ($salesTax / 100))), 2, '.', '');

                        $query2 = "INSERT INTO expense_details (rfq_id, CategoryId, Amount, Description, salesTax) VALUES (?, ?, ?, ?, ?)";
                        $query2_stmt = $conn->prepare($query2);
                        $query2_stmt->bind_param( "iidsi", $rfq_id, $expenseCategory, $totalWithTax, $description, $tax);
                        $query2_stmt->execute();
                    }

                    $query3 = "SELECT SUM(amount) AS grandTotal FROM expense_details WHERE rfq_id = ?";
                    $stmt = $conn->prepare($query3);
                    $stmt->bind_param('i', $rfq_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $row = $result->fetch_assoc();
                    $grandTotal = $row['grandTotal'] ?? 0;

                    // Update grandTotal in rfq_entries
                    $query4 = "UPDATE rfq_entries SET grandTotal = ? WHERE rfq_id = ?";
                    executeUpdate($conn, $query4, "di", $grandTotal, $rfq_id);

                        
                        // Update job status
                        $query5 = "UPDATE job SET status = ? WHERE id = ?";
                        executeUpdate($conn, $query5, "si", $status, $rfq_id);
                        
                    }
                    // Update quotation history
                    rfqUpdateHistory($conn, $rfq_id, $creationby, $creationdate, $status,$type);
                

                $conn->commit();
                $arr['success'] = true;
                $arr['message'] = "Expenses updated/inserted successfully";
                $recipients = [];
                $query = "SELECT v.Reg, jobTitle, description, vendorname, email 
                                            FROM job 
                                            JOIN vehicledetails v ON v.V_id = job.V_id 
                                            JOIN vendor ON vendorid = vendor_id 
                                            CROSS JOIN users u
                                            WHERE job.id = ?  AND u.id = 0";

                $bindings = ['i', $rfq_id]; 

                $subjectPrefix = 'Quotation Submission - Service Interval';
                $emailBodyTemplate = '
                    <p>Dear Mr.Jawaid,</p>
                    <p>We have sent a quotation for the service interval work order for the vehicle with registration number
                        <strong>{Reg}</strong>.
                    </p>
                    <p><strong>Details:</strong></p>
                    <p><strong>Job Title:</strong> {jobTitle}<br>
                    <strong>Reg No:</strong> {Reg}<br>
                    <strong>Description:</strong> {description}</p>
                    <p>Please visit the link below to review the quotation:</p>
                    <p><a href="https://mdvr2.itecknologi.com:8080/fms" target="_blank">https://mdvr2.itecknologi.com:8080/fms</a></p>
                    <p>Thank you,<br><strong>{vendorname}</strong></p>';

                sendEmailFromQuery($conn, $arr, $query, $bindings, $subjectPrefix, $emailBodyTemplate,$recipients);
            } catch (Exception $e) {
                $conn->rollback();
                $arr['success'] = false;
                $arr['message'] = "Error: " . $e->getMessage();
            }
        }else{
            $arr['message'] = "Quotation can only be sent when status is pending or returned";
        }
    }else{
        $arr['message'] = "Unauthorized access";
    }
}

//Mr.jawaid taking action
function quotationRecieved($conn,&$arr,$data){
    if($_SESSION['designation'] == 'Manager Fleet'){
        $rfq_id = $data['rfq_id'];
        $status = $data['status'];
        $reason = $data['reason'] ?? '';
        $creationdate = date("Y-m-d H:i:s"); 
        $creationby = $_SESSION['user_id'];
        $type = "user";

        $statusResult = checkStatus($conn,$arr,$rfq_id);
        $jobStatus = $statusResult['status'];
        if($jobStatus == 'submit quotation'){
            if(in_array($status, ['approve','return','deactivate'])){
                if($status == 'deactivate'){
                    if (empty($reason)) {
                        $arr['message'] = "reason cannot be empty.";
                        return;
                    }
                }
                // Update job status
                $query5 = "UPDATE job SET status = ?, reason = ?, checkby = ? WHERE id = ?";
                executeUpdate($conn, $query5, "ssii", $status, $reason, $creationby, $rfq_id);

                // Update quotation history
                rfqUpdateHistory($conn, $rfq_id, $creationby, $creationdate, $status,$type);
                
            
                $isManager = getManager($conn,$arr,$rfq_id);
                $recipients = [];
                $query = "SELECT v.Reg, jobTitle, description,vendorname, vendoremail as email , name as username
                FROM job 
                JOIN vehicledetails v ON v.V_id = job.V_id 
                JOIN vendor ON vendorid = vendor_id 
                CROSS JOIN users u
                WHERE job.id = ?  AND u.id = ?";

                if (!empty($isManager['data'])) {       
                    foreach ($isManager['data'] as $manager) {
                        $recipients[] = [
                            'email' => $manager['email'], 
                            'name' => $manager['name']    
                        ];

                        $bindings = ['ii', $rfq_id,$manager['id']]; 

                        if($status == 'approve'){
                            $subjectPrefix = 'Quotation Approved - Service Interval';
                            $emailbody = '<p>Dear {username},</p>';
                            $emailBodyTemplate = '
                                <p>The quotation has been approved for the service interval work order for the vehicle with registration number
                                    <strong>{Reg}</strong>.
                                </p>
                                <p><strong>Details:</strong></p>
                                <p><strong>Job Title:</strong> {jobTitle}<br>
                                <strong>Reg No:</strong> {Reg}<br>
                                <strong>Description:</strong> {description}</p>
                                <p>Please visit the link below to review the job:</p>
                                <p><a href="https://mdvr2.itecknologi.com:8080/fms" target="_blank">https://mdvr2.itecknologi.com:8080/fms</a></p>
                                <p>Thank you,<br><strong>Jawaid Khalid</strong></p>';
                                $email = $emailbody . $emailBodyTemplate; 

                            sendEmailFromQuery($conn, $arr, $query, $bindings, $subjectPrefix, $email, $recipients);
                            $emailbody = '<p>Dear {vendorname},</p>';
                            $approve = $emailbody . $emailBodyTemplate; 
                        }else if($status == 'return'){
                            $subjectPrefix = 'Quotation Returned - Service Interval';
                            $emailbody = '<p>Dear {username},</p>';
                            $emailBodyTemplate = '
                                <p>The quotation has been returned for the service interval work order for the vehicle with registration number
                                    <strong>{Reg}</strong>.
                                </p>
                                <p><strong>Details:</strong></p>
                                <p><strong>Job Title:</strong> {jobTitle}<br>
                                <strong>Reg No:</strong> {Reg}<br>
                                <strong>Description:</strong> {description}</p>
                                <p>Please visit the link below to review the job and resubmit the quotation:</p>
                                <p><a href="https://mdvr2.itecknologi.com:8080/fms" target="_blank">https://mdvr2.itecknologi.com:8080/fms</a></p>
                                <p>Thank you,<br><strong>Jawaid Khalid</strong></p>';
                                $email = $emailbody . $emailBodyTemplate; 

                            sendEmailFromQuery($conn, $arr, $query, $bindings, $subjectPrefix, $email, $recipients);
                            $emailbody = '<p>Dear {vendorname},</p>';
                            $return = $emailbody . $emailBodyTemplate; 
                            
                        }else if($status == 'deactivate'){
                            $subjectPrefix = 'Quotation Deactivated - Service Interval';
                            $emailBodyTemplate = '
                                <p>Dear Stakeholders,</p>
                                <p>We have deactivated the quotation for the service interval work order for the vehicle with registration number
                                    <strong>{Reg}</strong>.
                                </p>
                                <p><strong>Details:</strong></p>
                                <p><strong>Job Title:</strong> {jobTitle}<br>
                                <strong>Reg No:</strong> {Reg}<br>
                                <strong>Description:</strong> {description}</p>
                                <p>Please visit the link below to review the job</p>
                                <p><a href="https://mdvr2.itecknologi.com:8080/fms" target="_blank">https://mdvr2.itecknologi.com:8080/fms</a></p>
                                <p>Feel free to reach out if you need further details.</p>
                                <p>Thank you,<br><strong>DHL</strong></p>';

                            sendEmailFromQuery($conn, $arr, $query, $bindings, $subjectPrefix, $emailBodyTemplate,$recipients);
                            $recipients = [];
                            sendEmailFromQuery($conn, $arr, $query, $bindings, $subjectPrefix, $emailBodyTemplate,$recipients);
                        }
                    }
                    $empty = [];

                    if($status == 'approve'){
                        sendEmailFromQuery($conn, $arr, $query, $bindings, $subjectPrefix, $approve,$empty);
                    }else if($status == 'retunr'){
                        sendEmailFromQuery($conn, $arr, $query, $bindings, $subjectPrefix, $return,$empty);
                    }
                }else{
                    $arr['success'] = true;
                    $arr['message'] = "Status updated to ".$status." successfully";
                    $arr['data'] = [];    
                }
                $arr['success'] = true;
                $arr['message'] = "Status updated to ".$status." successfully";
                $arr['data'] = [];
            }else{
                $arr['message'] = "Invalid status";
            }
        }else{
            $arr['message'] = "RFQ action can only be taken after quotation is submitted";
        }
    }else{
        $arr['message'] = "Unauthorized access";
    }
}

//station manager taking action
function quotationApproved($conn,&$arr,$data){
    $rfq_id = $data['rfq_id'];
    $status = $data['status'];
    $by = $_SESSION['user_id'];
    $reason = $data['reason'] ?? '';
    $creationdate = date("Y-m-d H:i:s"); 
    $type = "user";
    $authorized = false;
    
    $stmt = $conn->prepare("select grandTotal from rfq_entries where rfq_id = ?");
    $stmt->bind_param('i',$rfq_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $rfq = $result->fetch_assoc();

    $statusResult = checkStatus($conn,$arr,$rfq_id);
    $jobStatus = $statusResult['status'];
    if($jobStatus == 'approve'){
        if(in_array($status, ['approve','return','deactivate'])){
            $isManager = getManager($conn,$arr,$rfq_id);
            if (!empty($isManager['data'])) {  
                foreach ($isManager['data'] as $manager) {
                    if ($manager['name'] == $_SESSION['name'] && $_SESSION['max'] >= $rfq['grandTotal']) {
                        $authorized = true;
                        $arr['data'] = [];
                        if($status == 'deactivate'){
                            if (empty($reason)) {
                                $arr['message'] = "reason cannot be empty.";
                                return;
                            }
                        }
                        // Update job status
                        $status = $status . ' by manager';

                        $query5 = "UPDATE job SET status = ?, reason = ? WHERE id = ?";
                       
                        $recipients = [];
                        $query = "SELECT v.Reg, jobTitle, description, vendorname, vendoremail as email
                        FROM job 
                        JOIN vehicledetails v ON v.V_id = job.V_id 
                        JOIN vendor ON vendorid = vendor_id 
                        WHERE id = ?";

                        $bindings = ['i', $rfq_id]; 

                        if($status == 'approve' . ' by manager'){
                            $subjectPrefix = 'Quotation Approved - Service Interval';
                            $emailBodyTemplate = '
                            <p>Dear {vendorname},</p>
                                <p>We have approved the quotation for the service interval work order for the vehicle with registration number
                                    <strong>{Reg}</strong>.
                                </p>
                                <p><strong>Details:</strong></p>
                                <p><strong>Job Title:</strong> {jobTitle}<br>
                                <strong>Reg No:</strong> {Reg}<br>
                                <strong>Description:</strong> {description}</p>
                                <p>Please visit the link below to review the job</p>
                                <p><a href="https://mdvr2.itecknologi.com:8080/fms" target="_blank">https://mdvr2.itecknologi.com:8080/fms</a></p>
                                <p>Feel free to reach out if you need further details.</p>
                                <p>Thank you,<br><strong>DHL</strong></p>';
                                sendEmailFromQuery($conn, $arr, $query, $bindings, $subjectPrefix, $emailBodyTemplate, $recipients);
                               
                            }else if($status == 'deactivate' . ' by manager'){
                            $isManager = getManager($conn,$arr,$rfq_id);

                            if (!empty($isManager['data'])) {       
                                foreach ($isManager['data'] as $manager) {
                                    $recipients[] = [
                                        'email' => $manager['email'], 
                                        'name' => $manager['name']    
                                    ];
                                    $subjectPrefix = 'Quotation Deactivated - Service Interval';
                                    $emailBodyTemplate = '
                                        <p>Dear Stakeholders,</p>
                                        <p>We have deactivated the quotation for the service interval work order for the vehicle with registration number
                                            <strong>{Reg}</strong>.
                                        </p>
                                        <p><strong>Details:</strong></p>
                                        <p><strong>Job Title:</strong> {jobTitle}<br>
                                        <strong>Reg No:</strong> {Reg}<br>
                                        <strong>Description:</strong> {description}</p>
                                        <p>Please visit the link below to review the job</p>
                                        <p><a href="https://mdvr2.itecknologi.com:8080/fms" target="_blank">https://mdvr2.itecknologi.com:8080/fms</a></p>
                                        <p>Feel free to reach out if you need further details.</p>
                                        <p>Thank you,<br><strong>DHL</strong></p>';

                                    sendEmailFromQuery($conn, $arr, $query, $bindings, $subjectPrefix, $emailBodyTemplate,$recipients);
                                }
                                
                            $recipients = [];
                            sendEmailFromQuery($conn, $arr, $query, $bindings, $subjectPrefix, $emailBodyTemplate,$recipients);
                            }
                        }
                        $arr['success'] = true;
                        $arr['message'] = "Status updated to ".$status." successfully";
                        $data['data'] = [];
                        break; 
                    }else{
                        $authorized = true;
                        $arr['data'] = [];
                        if($status == 'deactivate'){
                            if (empty($reason)) {
                                $arr['message'] = "reason cannot be empty.";
                                return;
                            }
                        }
                        // Update job status
                        $status = 'isApproved';

                        $query5 = "UPDATE job SET status = ?, reason = ? WHERE id = ?";
                        
                        $recipients = [];
                        $query = "SELECT v.Reg, jobTitle, description, vendorname, vendoremail as email
                        FROM job 
                        JOIN vehicledetails v ON v.V_id = job.V_id 
                        JOIN vendor ON vendorid = vendor_id 
                        WHERE id = ?";

                        $bindings = ['i', $rfq_id]; 

                        $isGops = getGOPS($conn,$arr,$rfq_id);

                        if (!empty($isGops['data'])) {       
                            foreach ($isGops['data'] as $gops) {
                                $recipients[] = [
                                    'email' => $gops['email'], 
                                    'name' => $gops['name']    
                                ];
                            
                                $subjectPrefix = 'Approval Forwarded - Service Interval';
                                $emailBodyTemplate = '
                                    <p>Dear Stakeholders,</p>
                                    <p>We request approval for the quotation for the service interval work order for the vehicle with registration number
                                        <strong>{Reg}</strong>.
                                    </p>
                                    <p><strong>Details:</strong></p>
                                    <p><strong>Job Title:</strong> {jobTitle}<br>
                                    <strong>Reg No:</strong> {Reg}<br>
                                    <strong>Description:</strong> {description}</p>
                                    <p>Please visit the link below to review the job and approve the quotation</p>
                                    <p><a href="https://mdvr2.itecknologi.com:8080/fms" target="_blank">https://mdvr2.itecknologi.com:8080/fms</a></p>
                                    <p>Feel free to reach out if you need further details.</p>
                                    <p>Thank you,<br><strong>DHL</strong></p>';

                                sendEmailFromQuery($conn, $arr, $query, $bindings, $subjectPrefix, $emailBodyTemplate,$recipients);
                            }

                        }
                        
                        $arr['message'] = "Approval forwarded successfully";

                        $arr['success'] = true;
                        $arr['data'] = [];
                    }
                    
                }
                if (!$authorized) {
                    $arr['message'] = "Unauthorized access";
                    $arr['success'] = false;
                    $arr['data'] = [];
                }else{
                    executeUpdate($conn, $query5, "ssi", $status, $reason, $rfq_id);

                    // Update quotation history
                    rfqUpdateHistory($conn, $rfq_id, $by, $creationdate, $status,$type);
                    
                }
            }
        }
    }else{
        $arr['message'] = "Only approval forwarded status can further be approved by final authority";
    }
}

function higherAuthority($conn,&$arr,$data){
    $rfq_id = $data['rfq_id'];
    $status = $data['status'];
    $by = $_SESSION['user_id'];
    $reason = $data['reason'] ?? '';
    $creationdate = date("Y-m-d H:i:s"); 
    $type = "user";
    $authorized = false;

    $stmt = $conn->prepare("select grandTotal from rfq_entries where rfq_id = ?");
    $stmt->bind_param('i',$rfq_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $rfq = $result->fetch_assoc();

    $statusResult = checkStatus($conn,$arr,$rfq_id);
    $jobStatus = $statusResult['status'];
    if($jobStatus == 'isApproved'){
        if(in_array($status, ['approve','deactivate'])){
            $isGOPS = getGOPS($conn,$arr,$rfq_id);
            if (!empty($isGOPS['data'])) {  
                foreach ($isGOPS['data'] as $gops) {
                    if ($gops['name'] == $_SESSION['name']) {
                        $authorized = true;
                        $arr['data'] = [];
                        if($status == 'deactivate'){
                            if (empty($reason)) {
                                $arr['message'] = "reason cannot be empty.";
                                return;
                            }
                        }
                        // Update job status
                        $status = $status . ' by manager';

                        $query5 = "UPDATE job SET status = ?, reason = ? WHERE id = ?";
                        executeUpdate($conn, $query5, "ssi", $status, $reason, $rfq_id);

                        // Update quotation history
                        rfqUpdateHistory($conn, $rfq_id, $by, $creationdate, $status,$type);
                        
                        $recipients = [];
                        $query = "SELECT v.Reg, jobTitle, description, vendorname, vendoremail as email
                        FROM job 
                        JOIN vehicledetails v ON v.V_id = job.V_id 
                        JOIN vendor ON vendorid = vendor_id 
                        WHERE id = ?";

                        $bindings = ['i', $rfq_id]; 

                        if($status == 'approve' . ' by manager'){
                            $subjectPrefix = 'Quotation Approved - Service Interval';
                            $emailBodyTemplate = '
                            <p>Dear {vendorname},</p>
                                <p>We have approved the quotation for the service interval work order for the vehicle with registration number
                                    <strong>{Reg}</strong>.
                                </p>
                                <p><strong>Details:</strong></p>
                                <p><strong>Job Title:</strong> {jobTitle}<br>
                                <strong>Reg No:</strong> {Reg}<br>
                                <strong>Description:</strong> {description}</p>
                                <p>Please visit the link below to review the job</p>
                                <p><a href="https://mdvr2.itecknologi.com:8080/fms" target="_blank">https://mdvr2.itecknologi.com:8080/fms</a></p>
                                <p>Feel free to reach out if you need further details.</p>
                                <p>Thank you,<br><strong>DHL</strong></p>';
                                sendEmailFromQuery($conn, $arr, $query, $bindings, $subjectPrefix, $emailBodyTemplate, $recipients);
                            }else if($status == 'deactivate' . ' by manager'){
                            $isGOPS = getGOPS($conn,$arr,$rfq_id);

                            if (!empty($isGOPS['data'])) {       
                                foreach ($isGOPS['data'] as $gops) {
                                    $recipients[] = [
                                        'email' => $gops['email'], 
                                        'name' => $gops['name']    
                                    ];
                                    $subjectPrefix = 'Quotation Deactivated - Service Interval';
                                    $emailBodyTemplate = '
                                        <p>Dear Stakeholders,</p>
                                        <p>We have deactivated the quotation for the service interval work order for the vehicle with registration number
                                            <strong>{Reg}</strong>.
                                        </p>
                                        <p><strong>Details:</strong></p>
                                        <p><strong>Job Title:</strong> {jobTitle}<br>
                                        <strong>Reg No:</strong> {Reg}<br>
                                        <strong>Description:</strong> {description}</p>
                                        <p>Please visit the link below to review the job</p>
                                        <p><a href="https://mdvr2.itecknologi.com:8080/fms" target="_blank">https://mdvr2.itecknologi.com:8080/fms</a></p>
                                        <p>Feel free to reach out if you need further details.</p>
                                        <p>Thank you,<br><strong>DHL</strong></p>';

                                    sendEmailFromQuery($conn, $arr, $query, $bindings, $subjectPrefix, $emailBodyTemplate,$recipients);
                                }
                                
                            $recipients = [];
                            sendEmailFromQuery($conn, $arr, $query, $bindings, $subjectPrefix, $emailBodyTemplate,$recipients);
                            }
                        }
                        $arr['success'] = true;
                        $arr['message'] = "Status updated to ".$status." successfully";
                        $data['data'] = [];
                        break; 
                    }
                }
                
                if (!$authorized) {
                    $arr['message'] = "Unauthorized access";
                    $arr['success'] = false;
                    $arr['data'] = [];
                }
            }
        }
    }else{
        $arr['message'] = "Only approval forwarded status can further be approved by final authority";
    }
}

function jobSatisfaction($conn,&$arr,$data){
    if($_SESSION['type'] == 'vendor'){
        $creationdate = date("Y-m-d H:i:s");
        $creationby = $_SESSION['user_id'] ?? null;
        $type = "vendor";
        $rfq_id = $data['rfq_id'];
        $status = "job satisfaction requested";

        $statusResult = checkStatus($conn,$arr,$rfq_id);
        $jobStatus = $statusResult['status'];
        if($jobStatus == 'approve by manager'){
             // Update job status
             $query5 = "UPDATE job SET status = ? WHERE id = ?";
             executeUpdate($conn, $query5, "si", $status, $rfq_id);
             
            // Update quotation history
            rfqUpdateHistory($conn, $rfq_id, $creationby, $creationdate, $status,$type);
     

            $recipients = [];
            $query = "SELECT v.Reg, jobTitle, description, vendorname, email 
                                        FROM job 
                                        JOIN vehicledetails v ON v.V_id = job.V_id 
                                        JOIN vendor ON vendorid = vendor_id 
                                        CROSS JOIN users u
                                        WHERE job.id = ?  AND u.id = 0";

            $bindings = ['i', $rfq_id]; 

            $subjectPrefix = 'Job Satisfaction Requested - Service Interval';
            $emailBodyTemplate = '
                <p>Dear Mr.Jawaid,</p>
                <p>We request job satisfaction for the service interval work order for the vehicle with registration number
                    <strong>{Reg}</strong>.
                </p>
                <p><strong>Details:</strong></p>
                <p><strong>Job Title:</strong> {jobTitle}<br>
                <strong>Reg No:</strong> {Reg}<br>
                <strong>Description:</strong> {description}</p>
                <p>Please visit the link below to review the job and accept job satisfaction request:</p>
                <p><a href="https://mdvr2.itecknologi.com:8080/fms" target="_blank">https://mdvr2.itecknologi.com:8080/fms</a></p>
                <p>Thank you,<br><strong>{vendorname}</strong></p>';

            sendEmailFromQuery($conn, $arr, $query, $bindings, $subjectPrefix, $emailBodyTemplate,$recipients);

            
            $arr['success'] = true;
            $arr['message'] = "Job satisfaction requested successfully";
            $arr['data'] = [];
        }else{
            $arr['message'] = "Job satisfaction can only be requested for RFQ's approved by manager";
        }
    }else{
        $arr['message'] = "Unauthorized access";
    }
}

function jobSatisfactionApprove($conn,&$arr,$data){
    if($_SESSION['type'] == 'admin' || $_SESSION['type'] == 'User'){
        $creationdate = date("Y-m-d H:i:s");
        $creationby = $_SESSION['user_id'];
        $type = "user";
        $rfq_id = $data['rfq_id'];
        $status = "job satisfaction approved";

        $statusResult = checkStatus($conn,$arr,$rfq_id);
        $jobStatus = $statusResult['status'];
        $requester = $statusResult['creationby'];

        if($jobStatus == 'job satisfaction requested'){
            $remarks = $data['remarks'] ?? '';
            $completiondate = $data['completiondate'] ?? '';
            $checkbox = $data['checkbox'] ?? '{}';
            $approvedby = $_SESSION['signature_url'];
            $errors = [];
            if (empty($remarks)) {
                $errors['remarks'] = "remarks cannot be empty.";
            }
            if (empty($completiondate)) {
                $errors['completiondate'] = "completiondate cannot be empty.";
            }
            if (empty($errors)) {
                $query = $conn->prepare("UPDATE job SET status = ?, remarks = ?, modifydate = ?, modifyby = ?, completiondate = ?, approvedby_url = ?, approvedby =? WHERE id = ?");
                if (!$query) {
                    die("Query preparation failed: " . $conn->error);
                }
                $query->bind_param('sssissii', $status, $remarks, $creationdate, $creationby,$completiondate, $approvedby,$creationby,  $rfq_id);
                $exe = $query->execute();
                if ($exe === false) {
                    $arr['message'] = "Error: " . $conn->error;
                    $arr['success'] = false;
                } else {
                    $checkboxes = checkboxes($conn);
                    $c['checkbox'] = json_decode($checkbox, true);
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
                        $checkboxQuery = $conn->prepare("INSERT INTO jobSatisfaction (j_id, value_id) VALUES (?, ?)");
                        if (!$checkboxQuery) {
                            die("Query preparation failed: " . $conn->error);
                        }
                        foreach ($validCheckboxes as $checkboxId) {
                            $checkboxQuery->bind_param('ii', $rfq_id, $checkboxId);
                            if (!$checkboxQuery->execute()) {
                                die("Execution failed: " . $checkboxQuery->error); 
                            }
                        }   
                    }
                    // Update quotation history
                    rfqUpdateHistory($conn, $rfq_id, $creationby, $creationdate, $status,$type);
            

                    $recipients = [];
                    $query = "SELECT v.Reg, jobTitle, description, vendorname, email , name as username
                                                FROM job 
                                                JOIN vehicledetails v ON v.V_id = job.V_id 
                                                JOIN vendor ON vendorid = vendor_id 
                                                CROSS JOIN users u
                                                WHERE job.id = ?  AND u.id = ?";

                    $bindings = ['ii', $rfq_id,$requester]; 

                    $subjectPrefix = 'Job Satisfaction Approved - Service Interval';
                    $emailBodyTemplate = '
                        <p>Dear {username}</p>
                        <p>The job satisfaction is approved for the service interval work order for the vehicle with registration number
                            <strong>{Reg}</strong>.
                        </p>
                        <p><strong>Details:</strong></p>
                        <p><strong>Job Title:</strong> {jobTitle}<br>
                        <strong>Reg No:</strong> {Reg}<br>
                        <strong>Description:</strong> {description}</p>
                        <p>Please visit the link below to review the job</p>
                        <p><a href="https://mdvr2.itecknologi.com:8080/fms" target="_blank">https://mdvr2.itecknologi.com:8080/fms</a></p>
                        <p>Thank you,<br><strong>DHL</strong></p>';

                    sendEmailFromQuery($conn, $arr, $query, $bindings, $subjectPrefix, $emailBodyTemplate,$recipients);

                    
                    $arr['success'] = true;
                    $arr['message'] = "Job satisfaction approved successfully";
                    $arr['data'] = [];
                }
            }else{
                $errorMessages = "Error: ";
                foreach ($errors as $field => $error) {
                    $errorMessages .= "$field: $error; "; 
                }
                $errorMessages = rtrim($errorMessages, "; ");
                $arr["message"] = $errorMessages;
            }
        }else{
            $arr['message'] = "Job satisfaction can only be approved after it is requested";            
        }
    }else{
        $arr['message'] = "Unauthorized access";
        $arr['data'] = [];
    }
}

function invoiceRecieved($conn, &$arr, $data) {
    $rfq_id = $data['rfq_id'] ?? '';

    $statusResult = checkStatus($conn,$arr,$rfq_id);
    $jobStatus = $statusResult['status'];
    if($jobStatus == 'job satisfaction approved' || $jobStatus == 'Invoice reject'){
        if ($_SESSION['type'] == 'vendor') {
            $contentFile = $_FILES['contentFile'] ?? null;
            $errors = [];

            if (empty($rfq_id)) {
                $errors['rfq_id'] = "rfq_id cannot be empty.";
            }
            if (empty($contentFile)) {
                $errors['contentFile'] = "contentFile cannot be empty.";
            }

            if (empty($errors)) {
                $creationdate = date("Y-m-d H:i:s");
                $vendor_id = $_SESSION['user_id'];

                if($jobStatus == 'job satisfaction approved'){
                    $query = $conn->prepare("INSERT INTO invoice(rfq_id, vendor_id, creationdate, creationby) VALUES (?, ?, ?, ?)");
                    if (!$query) {
                        $arr["message"] = "Query preparation failed: " . $conn->error;
                        return;
                    }
                    $query->bind_param('iisi', $rfq_id, $vendor_id, $creationdate, $vendor_id);
                    $exe = $query->execute();

                    if ($exe === false) {
                        $arr["message"] = "Error: " . mysqli_error($conn);
                        $arr["success"] = false;
                        return;
                    }
                    $inserted_id = $conn->insert_id;

                }else{
                    $inserted_id = $rfq_id;
                }
                    $filename = $contentFile['name'];
                    $file_extension = pathinfo($filename, PATHINFO_EXTENSION);
                    
                    $pdfurl = $inserted_id . '.' . $file_extension;
                    
                    $folderPath = __DIR__ . '/../invoice/'; 
                    $finalFilePath = $folderPath . $pdfurl;
                    
                    if (!is_dir($folderPath)) {
                        mkdir($folderPath, 0777, true);
                    }
                    
                    $uploadedFilePath = $_FILES['contentFile']['tmp_name'];
                    $uploadedFileType = mime_content_type($uploadedFilePath);
                    
                    // Determine file extension from MIME type
                    switch ($uploadedFileType) {
                        case 'application/pdf':
                            $file_extension = 'pdf';
                            break;
                        case 'image/jpeg':
                            $file_extension = 'jpg';
                            break;
                        case 'image/png':
                            $file_extension = 'png';
                            break;
                        case 'image/gif':
                            $file_extension = 'gif';
                            break;
                        case 'image/webp':
                            $file_extension = 'webp';
                            break;
                        default:
                            throw new Exception('Unsupported file type.');
                    }
                    
                    // Ensure $pdfurl matches correct extension
                    $pdfurl = $inserted_id . '.' . $file_extension;
                    $finalFilePath = $folderPath . $pdfurl;
                    

                    if (move_uploaded_file($uploadedFilePath, $finalFilePath)) {
                        if($jobStatus == 'job satisfaction approved'){
                            $insert = $conn->prepare("UPDATE invoice SET invoice_url = ? WHERE id = ?");
                        }else{
                            $insert = $conn->prepare("UPDATE invoice SET invoice_url = ? WHERE rfq_id = ?");
                        }
                        if (!$insert) {
                            die("Query preparation failed: " . $conn->error);
                        } else {
                            $insert->bind_param('si', $pdfurl, $inserted_id);
                            $execute = $insert->execute();

                            if ($execute === false) {
                                $arr["message"] = "Error: " . mysqli_error($conn);
                                $arr["success"] = false;
                            } else {
                                $status = "invoice recieved";
                                $reason = "";
                                $rfq_id = $data['rfq_id'];
                                $type = 'vendor';

                                // Update job status
                                $query5 = "UPDATE job SET status = ?, reason = ? WHERE id = ?";
                                executeUpdate($conn, $query5, "ssi", $status, $reason, $rfq_id);

                                // Update quotation history
                                rfqUpdateHistory($conn, $rfq_id, $vendor_id, $creationdate, $status,$type);

                                $arr["success"] = true;
                                $arr["message"] = "Data inserted Successfully!";
                            }
                        }
                    } else {
                        $arr["message"] = "Failed to save the file.";
                        $arr["success"] = false;
                    }
                
            } else {
                $arr["success"] = false;
                $arr["message"] = implode("; ", $errors);
            }
        } else {
            $arr['message'] = "Unauthorized access";
        }
    }else{
        $arr['message'] = "Invoice can only be uploaded after job satisfaction or invoice returned";
    }
}

function invoiceApprove($conn,&$arr,$data){
    $rfq_id = $data['rfq_id'];
    $status = $data['status'];
    $reason = $data['reason'] ?? '';
    $creationdate = date("Y-m-d H:i:s"); 
    $creationby = $_SESSION['user_id'];
    $type = "user";

    $statusResult = checkStatus($conn,$arr,$rfq_id);
    $jobStatus = $statusResult['status'];
    $requester = $statusResult['creationby'];
    if($creationby == $requester){
        if($jobStatus == 'invoice recieved'){
            if(in_array($status, ['approve','reject'])){
                if($status == 'reject'){
                    if (empty($reason)) {
                        $arr['message'] = "reason cannot be empty.";
                        return;
                    }
                }
                $status = "Invoice " . $status;
                // Update job status
                $query5 = "UPDATE job SET status = ?, reason = ?, checkby = ? WHERE id = ?";
                executeUpdate($conn, $query5, "ssii", $status, $reason, $creationby, $rfq_id);

                // Update quotation history
                rfqUpdateHistory($conn, $rfq_id, $creationby, $creationdate, $status,$type);

                $recipients = [];
                $query = "SELECT v.Reg, jobTitle, description,vendorname, email , username
                FROM job 
                JOIN vehicledetails v ON v.V_id = job.V_id 
                JOIN vendor ON vendorid = vendor_id 
                CROSS JOIN users u
                WHERE job.id = ?  AND u.id = ?";                
                $bindings = ['ii', $rfq_id,$creationby]; 
                if($status == 'Invoice approve'){
                    $subjectPrefix = 'Invoice Approved - Service Interval';
                    $emailBodyTemplate = '
                        <p>Dear {vendorname},</p>
                        <p>The invocie has been approved for the service interval work order for the vehicle with registration number
                            <strong>{Reg}</strong>.
                        </p>
                        <p><strong>Details:</strong></p>
                        <p><strong>Job Title:</strong> {jobTitle}<br>
                        <strong>Reg No:</strong> {Reg}<br>
                        <strong>Description:</strong> {description}</p>
                        <p>Please visit the link below to review the job:</p>
                        <p><a href="https://mdvr2.itecknologi.com:8080/fms" target="_blank">https://mdvr2.itecknologi.com:8080/fms</a></p>
                        <p>Thank you,<br><strong>{username}</strong></p>';

                    sendEmailFromQuery($conn, $arr, $query, $bindings, $subjectPrefix, $emailBodyTemplate, $recipients);
                }else if($status == 'Invoice reject'){
                    $subjectPrefix = 'Invoice Returned - Service Interval';
                    $emailBodyTemplate = '
                        <p>Dear {vendorname},</p>
                        <p>The Invoice has been returned for the service interval work order for the vehicle with registration number
                            <strong>{Reg}</strong>.
                        </p>
                        <p><strong>Details:</strong></p>
                        <p><strong>Job Title:</strong> {jobTitle}<br>
                        <strong>Reg No:</strong> {Reg}<br>
                        <strong>Description:</strong> {description}</p>
                        <p>Please visit the link below to review the job and resubmit the Invoice:</p>
                        <p><a href="https://mdvr2.itecknologi.com:8080/fms" target="_blank">https://mdvr2.itecknologi.com:8080/fms</a></p>
                        <p>Thank you,<br><strong>{username}</strong></p>';

                    sendEmailFromQuery($conn, $arr, $query, $bindings, $subjectPrefix, $emailBodyTemplate, $recipients);
                }
                $arr['success'] = true;
                $arr['message'] = "Status updated to ".$status." successfully";
                $arr['data'] = [];
            }else{
                $arr['message'] = "Invalid status";
            }
        }else{
            $arr['message'] = "Action can be taken only after invoice is recieved";
        }
    }else{
        $arr['message'] = "Unauthorized access";
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
    $getUser = $conn->prepare("SELECT s.name AS station FROM users JOIN job j  ON j.creationby = users.id JOIN stationdata s ON users.`station_id` = s.id WHERE j.id = ?");
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

    $station = $user['station'];
   
    $query = $conn->prepare("SELECT users.id, users.name, title, access,email, s.name AS station FROM designation 
        JOIN users ON designation_id = designation.id 
        JOIN stationdata s ON s.id = users.station_id 
        WHERE s.name = ? AND  title LIKE '%Manager%'");
    if (!$query) {
        die("Query preparation failed: " . $conn->error);
    }    
    $query->bind_param('s', $station);
    $query->execute();
    $result = $query->get_result();
    $managers = [];
    while ($row = $result->fetch_assoc()) {
        $managers[] = $row; 
    }

    $arr['message'] = "Mangers for the RFQ";
    $arr['success'] = true;
    $arr['data'] = $managers;


    return [
        'success' => true,
        'data' => $managers
    ];
}

function getGOPS($conn,&$arr, $id) {
    $query = $conn->prepare("SELECT users.id, users.name, title, access,email, s.name AS station FROM designation 
                            JOIN users ON designation_id = designation.id 
                            JOIN stationdata s ON s.id = users.station_id 
                            WHERE title LIKE '%GOPS%'");
    if (!$query) {
        die("Query preparation failed: " . $conn->error);
    }    
    $query->execute();
    $result = $query->get_result();
    $gops = [];
    while ($row = $result->fetch_assoc()) {
        $gops[] = $row; 
    }

    $arr['message'] = "GOPS for the RFQ";
    $arr['success'] = true;
    $arr['data'] = $gops;


    return [
        'success' => true,
        'data' => $gops
    ];
}

function getSalesTax($conn, $tax) {
  
    $query = $conn->prepare("select value from salesTax where id = ?");
    if (!$query) {
        die("Query preparation failed: " . $conn->error);
    }    
    $query->bind_param('i', $tax);
    $query->execute();
    $result = $query->get_result();

    $row = $result->fetch_assoc();

    return $row ? (float)$row['value'] : 0;
}

function checkStatus($conn,&$arr,$id){
    $query = $conn->prepare("SELECT status, creationby FROM job WHERE id = ?");
    if (!$query) {
        die("Query preparation failed: " . $conn->error);
    }

    $query->bind_param("i", $id);
    $query->execute();
    $result = $query->get_result();
    $statusResult = $result->fetch_assoc();

  
    if (!$statusResult) {
        $arr['message'] = "RFQ not found";
        $arr['success'] = false;
        return;
    }

    $jobStatus = $statusResult['status'];
    $requester = $statusResult['creationby'];

    return $statusResult;
}

function executeUpdate($conn, $query, $types, ...$params) {
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception("Error preparing statement: " . $conn->error);
    }
    $stmt->bind_param($types, ...$params);
    if (!$stmt->execute()) {
        throw new Exception("Error executing update: " . $stmt->error);
    }
}

//List of workorder for vendors
function getList($conn, &$arr,$data){
    if(isset($data['vendor_id'])){
        $vendor_id = $data['vendor_id'];
        $query = $conn->prepare("SELECT 
            job.id AS job_id,
            job.Status,
            job.creationdate,
            jobTitle,
            v.Reg,
            i.invoice_url AS invoice,
            job.description AS job_description,
            v.ENGINE,
            v.chassis,
            v.makename,
            v.modelname,
            v.stationname,
            categoryId,
            ExpenseDetailId,
            e.Expense AS ExpenseTitle,
            d.amount,
            d.salesTax,
            st.value as salesTaxValue,
            reason,
            d.description AS expense_description,
            vendorname,
            ua.name AS approvedby,
            uc.name AS checkby,
            grandTotal,
            completiondate,
            remarks,
            ucb.name as createdby
        FROM job
        LEFT JOIN vehicledetails v ON v.V_id = job.V_id 
        LEFT JOIN invoice i ON i.rfq_id = job.id
        JOIN rfq_entries r ON r.rfq_id = job.id 
        JOIN expense_details d ON d.rfq_id = r.rfq_id
        JOIN expenseCategory e ON d.categoryId = e.ExpId
        LEFT JOIN users ua ON job.approvedby = ua.id
        LEFT JOIN users uc ON job.checkby = uc.id
        LEFT JOIN users ucb ON r.createdby = ucb.id
        JOIN vendor ON vendor.vendorid = job.vendor_id
        join salestax st on st.id = d.salesTax
        WHERE job.vendor_id =? AND job.creationdate BETWEEN ? and ?
        ORDER BY job.id");
        if(!$query){
            die("Query preparation failed: " . $conn->error); 
        }else{
            if(isset($data['startDate']) && isset($data['endDate'])){
                $startDate = $data['startDate'];
                $endDate = $data['endDate'];
            }else{
                $currentDate = new DateTime();
                $startDate = $currentDate->modify('-1 month')->format('Y-m-d H:i:s');
                $endDate = (new DateTime())->format('Y-m-d H:i:s');
            }
            
        $query->bind_param("iss", $vendor_id, $startDate, $endDate);
        // $query->bind_param("i", $vendor_id);

        $execute = $query->execute();

        if (!$execute) {
            die("Execution failed: " . $query->error);
        }

        $result = $query->get_result();
        $jobs = [];
        $checkboxes = checkboxes($conn);


        while ($row = $result->fetch_assoc()) {
            $job_id = $row['job_id'];
            $statusResult = checkStatus($conn,$arr,$row['job_id']);
            $jobStatus = $statusResult['status'];
            if (!in_array(strtolower($jobStatus), array_map('strtolower', ['job satisfaction requested','isApproved','approve', 'approve by manager', 'submit quotation', 'pending', 'return', 'deactivate', 'return by manager', 'deactivate by manager']))) {
                $satisfaction = true;
            }else{
                $satisfaction = false;
            }
            

            if (!isset($jobs[$job_id])) {
                $jobs[$job_id] = [
                    'job_id' => $row['job_id'],
                    'Status' => $row['Status'],
                    'Reason' => $row['reason'],
                    'creationdate' => $row['creationdate'],
                    'jobTitle' => $row['jobTitle'],
                    'Reg' => $row['Reg'],
                    'invoice' => $row['invoice'],
                    'job_description' => $row['job_description'],
                    'ENGINE' => $row['engine'],
                    'chassis' => $row['chassis'],
                    'makename' => $row['makename'],
                    'modelname' => $row['modelname'],
                    'stationname' => $row['stationname'],    
                    'vendorname' => $row['vendorname'],
                    'approvedby' => $row['approvedby'],
                    'checkedby' => $row['checkby'],
                    'grandTotal' => $row['grandTotal'],
                    'jobSatisfaction' => $satisfaction,
                    'completiondate' => $row['completiondate'],
                    'remarks' => $row['remarks'],
                    'createdby' => $row['createdby']
                ];
                $jobSatisfaction = jobSatisfactionData($conn, $job_id);
                foreach ($checkboxes as $checkbox) {
                    $value = $checkbox['value'];
                    $jobs[$job_id][$value] = isset($jobSatisfaction[$value]) ? true : false;
                }
            }

            $jobs[$job_id]['expenses'][] = [
                'ExpenseDetailId' =>$row['ExpenseDetailId'],
                'CategoryId' => $row['categoryId'],
                'ExpenseTitle' => $row['ExpenseTitle'],
                'amount' => $row['amount'],
                'salesTax' => $row['salesTax'],
                'salesTaxValue' => $row['salesTaxValue'],
                'description' => $row['expense_description']
            ];
        }
    
        $arr['data'] = array_values($jobs);

        if (empty($arr['data'])) {
            $arr['message'] = "No records found";
            $arr['success'] = false;
        } else {
            $arr['message'] = "Data fetched successfully";
            $arr['success'] = true;
        }
            
        }
    }else{
        $arr['message'] = "Vendor id is required";
    }
}

function getListDhl($conn, &$arr,$data){
    $query = $conn->prepare("
    SELECT 
            job.id AS job_id,
            job.Status,
            job.creationdate,
            jobTitle,
            v.Reg,
            i.invoice_url AS invoice,
            job.description AS job_description,
            v.ENGINE,
            v.chassis,
            v.makename,
            v.modelname,
            v.stationname,
            stationid,
            reason,
            vendorname,
            ua.name AS approvedby,
            uc.name AS checkby,
            grandTotal,
            completiondate,
            remarks,
            ucb.name as createdby,
            createdby as createdbyId
        FROM job
        LEFT JOIN vehicledetails v ON v.V_id = job.V_id 
        LEFT JOIN invoice i ON i.rfq_id = job.id
        LEFT JOIN rfq_entries r ON r.rfq_id = job.id
        LEFT JOIN users ua ON job.approvedby = ua.id
        LEFT JOIN users uc ON job.checkby = uc.id
        LEFT JOIN users ucb ON r.createdby = ucb.id
        JOIN vendor ON vendor.vendorid = job.vendor_id
        where  job.creationdate between ? and ?
        ORDER BY job.id");
    if(!$query){
        die("Query preparation failed: " . $conn->error); 
    }else{
        if(isset($data['startDate']) && isset($data['endDate'])){
            $startDate = $data['startDate'];
            $endDate = $data['endDate'];
        }else{
            $currentDate = new DateTime();
            $startDate = $currentDate->modify('-1 month')->format('Y-m-d H:i:s');
            $endDate = (new DateTime())->format('Y-m-d H:i:s');
        }
        
    $query->bind_param("ss", $startDate, $endDate);
    $execute = $query->execute();

    if (!$execute) {
        die("Execution failed: " . $query->error);
    }

    $result = $query->get_result();
    $jobs = [];
    $checkboxes = checkboxes($conn);

    while ($row = $result->fetch_assoc()) {
            $stmt = $conn->prepare("SELECT 
            rm.status as modifiedStatus, rm.date as modificationDate, 
            rm.modifier_type,
            CASE 
                WHEN rm.modifier_type = 'user' THEN u.name
                WHEN rm.modifier_type = 'vendor' THEN v.vendorname
            END AS modifiedby
            FROM rfqupdatehistory rm
            LEFT JOIN users u ON (rm.modifiedby = u.id AND rm.modifier_type = 'user')
            LEFT JOIN vendor v ON (rm.modifiedby = v.vendorid AND rm.modifier_type = 'vendor')
            where rfq_id = ?
            ORDER BY rm.id DESC");
            $stmt->bind_param("i", $row['job_id'] );

            $execute = $stmt->execute();

        if (!$stmt) {
            die("Execution failed: " . $stmt->error);
        }

        $history = $stmt->get_result();

        $stmt = $conn->prepare("
        SELECT categoryId,
            ExpenseDetailId,
            e.Expense AS ExpenseTitle,
            d.amount,
            d.salesTax,
            st.value as salesTaxValue,
            d.description AS expense_description FROM rfq_entries r  
        JOIN expense_details d ON d.rfq_id = r.rfq_id
        JOIN expenseCategory e ON d.categoryId = e.ExpId
        join salestax st on st.id = d.salesTax
        WHERE r.rfq_id = ?");
        $stmt->bind_param("i", $row['job_id'] );

        $execute = $stmt->execute();

    if (!$stmt) {
        die("Execution failed: " . $stmt->error);
    }

    $expense = $stmt->get_result();

        $statusResult = checkStatus($conn,$arr,$row['job_id']);
        $jobStatus = $statusResult['status'];
        if (!in_array(strtolower($jobStatus), array_map('strtolower', ['job satisfaction requested','isApproved','approve', 'approve by manager', 'submit quotation', 'pending', 'return', 'deactivate', 'return by manager', 'deactivate by manager']))) {
            $satisfaction = true;
        }else{
            $satisfaction = false;
        }
        
        $job_id = $row['job_id'];

        if (!isset($jobs[$job_id])) {
            $jobs[$job_id] = [
                'job_id' => $row['job_id'],
                'Status' => $row['Status'],
                'Reason' => $row['reason'],
                'creationdate' => $row['creationdate'],
                'jobTitle' => $row['jobTitle'],
                'Reg' => $row['Reg'],
                'invoice' => $row['invoice'],
                'job_description' => $row['job_description'],
                'ENGINE' => $row['engine'],
                'chassis' => $row['chassis'],
                'makename' => $row['makename'],
                'modelname' => $row['modelname'],
                'stationname' => $row['stationname'],
                'station_id' => $row['stationid'],
                'vendorname' => $row['vendorname'],
                'approvedby' => $row['approvedby'],
                'checkedby' => $row['checkby'],
                'grandTotal' => $row['grandTotal'],
                'jobSatisfaction' => $satisfaction,
                'completiondate' => $row['completiondate'],
                'remarks' => $row['remarks'],
                'createdby' => $row['createdby'],
                'createdbyId' => $row['createdbyId']
            ];
            $jobSatisfaction = jobSatisfactionData($conn, $job_id);
                foreach ($checkboxes as $checkbox) {
                    $value = $checkbox['value'];
                    $jobs[$job_id][$value] = isset($jobSatisfaction[$value]) ? true : false;
                }
        }
        while ($row = $expense->fetch_assoc()) {
            $jobs[$job_id]['expenses'][] = [
                'CategoryId' => $row['categoryId'],
                'ExpenseTitle' => $row['ExpenseTitle'],
                'amount' => $row['amount'],
                'salesTax' => $row['salesTax'],
                'salesTaxValue' => $row['salesTaxValue'],
                'description' => $row['expense_description']
            ];
        }

        while ($row = $history->fetch_assoc()) {

            $jobs[$job_id]['chain'][] = [
                'ModifiedBy' => $row['modifiedby'],
                'ModifiedDate' => $row['modificationDate'],
                'ModifiedStatus' => $row['modifiedStatus'],
            ];
        }

    }
    if (strtolower($_SESSION['access']) == 'full') {
        $arr['success'] = true;
        $arr['data'] = array_values($jobs);
    } else {
        $filteredResults = [];
    
        // Check if designation contains 'manager'
        if (stripos($_SESSION['designation'], 'manager') !== false) {
            foreach ($jobs as $r) {
                if ($_SESSION['station_id'] == $r['station_id']) {
                    $filteredResults[] = $r;
                }
            }
        } else {
            // If not manager, only show jobs created by the user
            foreach ($jobs as $r) {
                if ($_SESSION['user_id'] == $r['createdbyId']) {
                    $filteredResults[] = $r;
                }
            }
        }
    
        $arr['success'] = true;
        $arr['data'] = array_values($filteredResults);
    }
    

    if (empty($arr['data'])) {
        $arr['message'] = "No records found";
        $arr['success'] = false;
    } else {
        $arr['message'] = "Data fetched successfully";
        $arr['success'] = true;
    }
        
    }
}

//get jobSatisfaction checkboxes data
function jobSatisfactionData($conn, $id){
    $jobSatisfaction = $conn->prepare("SELECT j_id, value FROM jobSatisfaction  u join checkboxes c on c.id = u.value_id where u.j_id = ?");
    $jobSatisfaction->bind_param('i', $id);
    $jobSatisfaction->execute();
    $result = $jobSatisfaction->get_result();

    $reviewedValues = []; 
    $response = [];

    while ($row = $result->fetch_assoc()) {
        $reviewedValues[$row['value']] = true;
    }    
   
    
    return $reviewedValues;
}
echo json_encode($arr);
?> 