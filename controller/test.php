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
            $creationdate = date("Y-m-d"); 

        // Calculate the GrandTotal
        $totalWithTax = array_reduce($expenses, function ($sum, $expense) {
            $amount = (float)$expense['amount'];
            $tax = (float)$expense['tax'];
            $amountWithTax = $amount * (1 + ($tax / 100));
            return $sum + $amountWithTax;
        }, 0);
        
        $arr['data'] = $totalWithTax;
        

        var_dump($expenses);

        
        $creationdate = date("Y-m-d"); 
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
            // if (!$query->execute()) {
            //     $arr["success"] = false;
            //     $arr["message"] = "Error inserting expense entry: ".$conn->error;
            //     exit;
            // }else {
            //     $arr["success"] = true;
            //     $arr["message"] = "Data inserted successfully!";
            //     $arr["data"] = "";
            // }
            try {

                $sql = "INSERT INTO rfq_entries (V_Id, GrandTotal,vendor_id, CreatedBy, creationdate, rfq_id) 
                        VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);

                if (!$stmt) {
                    $arr["success"] = false;
                    $arr["message"] = "Error preparing expense entry statement: ".$conn->error;
                    exit;
                }

                $stmt->bind_param("idiisi", $V_id, $totalWithTax,$vendor_id, $creationby, $creationdate, $inserted_id);

                // if (!$stmt->execute()) {
                //     $arr["success"] = false;
                //     $arr["message"] = "Error inserting expense entry: ".$conn->error;
                //     exit;
                // }
                
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

                    $totalWithTax = number_format(($amount * (1 + ($tax / 100))), 2, '.', '');

                    $detail_stmt->bind_param("isdsi", $inserted_id, $expenseCategory, $totalWithTax, $description, $tax);

                    // if (!$detail_stmt->execute()) {
                    //     $arr["success"] = false;
                    //     $arr["message"] = "Error inserting expense details: ".$conn->error;
                    //     exit;
                    // }
                }

                $detail_stmt->close();


                $conn->commit();
                $arr["success"] = true;
                $arr["message"] = "RFQ entry created successfully";
            } catch (Exception $e) {
                $conn->rollback(); 
                $arr["success"] = false;
                $arr["message"] = $e->getMessage();
            }
        }
        
    }else{
        $arr['message'] = "Unauthorized access";
    }
}


echo json_encode($arr);
?> 