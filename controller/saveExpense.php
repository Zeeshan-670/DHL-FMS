<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include("database.php");

$arr = [
    "message" => "",
    "success" => false
];

// Establish the database connection
$conn = dbconnection();

if (!$conn) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

// Get the JSON input from the client
$data = json_decode(file_get_contents("php://input"), true);

// Validate the input
if (!isset($data['entryDate'], $data['vid'], $data['InvoiceNumber'], $data['InvoiceDate'], $data['expenses']) || !is_array($data['expenses'])) {
    echo json_encode(["status" => "error", "message" => "Invalid input data"]);
    exit;
}

// Extract data from request
$entryDate = $data['entryDate'];
$vid = $data['vid'];
$InvoiceNumber = $data['InvoiceNumber'];
$department = $data['department'];
$costCenter = '-';
$InvoiceDate = $data['InvoiceDate'];
$expenses = $data['expenses'];
$vendor_id = $data['vendor_id'];
$creationby = $_SESSION['user_id'];
// $costCenter = $data['costCenter'];

// Calculate the GrandTotal
$grandTotal = array_reduce($expenses, function ($sum, $expense) {
    return $sum + $expense['amount'];
}, 0);

$dupCheck = $conn->prepare("select InvoiceNumber from `expense_entries` where InvoiceNumber = ?");
$dupCheck->bind_param("s", $InvoiceNumber);

        $dupCheck->execute();
    
        $result = $dupCheck->get_result();
        $fetch = $result->fetch_assoc(); 

        $response = [];

        if ($fetch ) {

            if ((string)$fetch['InvoiceNumber'] === (string)$InvoiceNumber ) {
                echo json_encode(["status" => "error", "message" => "Invoice already exists"]);
                exit;
            }
        }else{

            $conn->begin_transaction(); 

            try {
                // Insert data into expense_entries table using prepared statement
                $sql = "INSERT INTO expense_entries (V_Id, EntryDate, InvoiceDate, InvoiceNumber, Department, CostCenter, GrandTotal,vendor_id, CreatedBy) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);

                if (!$stmt) {
                    echo json_encode(["status" => "error", "message" => "Error preparing expense entry statement: ".$conn->error]);
                    exit;
                }

                $stmt->bind_param("isssssdii", $vid, $entryDate, $InvoiceDate, $InvoiceNumber, $department, $costCenter, $grandTotal,$vendor_id, $creationby);

                if (!$stmt->execute()) {
                    echo json_encode(["status" => "error", "message" => "Error inserting expense entry: ".$conn->error]);
                    exit;
                }

                // Get the last inserted expense_id (to link with expense details)
                $expense_id = $stmt->insert_id;
                $stmt->close();

                // Prepare SQL statement for inserting expense details
                $detail_sql = "INSERT INTO expense_details (ExpenseId, CategoryId, Amount, Description, salesTax) 
                            VALUES (?, ?, ?, ?, ?)";
                $detail_stmt = $conn->prepare($detail_sql);

                if (!$detail_stmt) {
                    echo json_encode(["status" => "error", "message" => "Error preparing expense details statement: ".$conn->error]);
                    exit;
                }

                foreach ($expenses as $expense) {
                    $expenseCategory = $expense['expenseCategory'];
                    $amount = $expense['amount'];
                    $description = $expense['description'];
                    $tax = $expense['tax'];

                    $detail_stmt->bind_param("isdsi", $expense_id, $expenseCategory, $amount, $description, $tax);

                    if (!$detail_stmt->execute()) {
                        echo json_encode(["status" => "error", "message" => "Error inserting expense details: ".$conn->error]);
                        exit;
                    }
                }

                $detail_stmt->close();

                $conn->commit();

                echo json_encode(["status" => "success", "message" => "Expense entry created successfully"]);
            } catch (Exception $e) {
                $conn->rollback(); 
                echo json_encode(["status" => "error", "message" => $e->getMessage()]);
            }
        }
// Close the database connection
$conn->close();
?>
