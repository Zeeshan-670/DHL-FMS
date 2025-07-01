<?php
require_once __DIR__ . '/../pdf/vendor/autoload.php';
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json"); 
include("database.php");

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

        add($conn, $arr);  
    }else{
        $arr['message'] = "Unauthorized access";
    }  
}
function add($conn, &$arr){
    $work_id = $_POST['work_id']  ?? '';
    $vendor_id = $_POST['vendor_id'] ?? '';
    $amount = $_POST['amount'] ?? '';
    $send_data = $_POST['send_data'] ?? '';
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
    if (empty($send_data)) {
        $errors['send_data'] = "send_data cannot be empty.";
    }
    if(empty($contentFile)){
        $errors['file'] = "File cannot be empty";
    }
    if (empty($errors)) {
        $creationdate = date("Y-m-d H:i:s"); 
        $creationby = $_SESSION['user_id'];
        $query = $conn->prepare("INSERT INTO invoice(work_id,vendor_id,amount,send_data,creationdate, creationby) VALUES (?, ?, ?, ?, ?, ?)");
        if (!$query) {
            die("Query preparation failed: " . $conn->error); 
        }else{
            $query->bind_param('iiissi',$work_id ,$vendor_id, $amount, $send_data,  $creationdate, $creationby);
            $exe=$query->execute();

            if($exe === false){
                $arr["message"] = "Error: " . mysqli_error($conn);
                $arr["success"]=false;
            }else{
                $inserted_id = $conn->insert_id;
                $pdfurl = $inserted_id. '.pdf';
                $insert = $conn->prepare("update invoice set pdf_url = ? where id = ?");
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

function download($conn, &$arr, $pdfurl){
    try {
        $mpdf = new Mpdf();
        $folderPath = __DIR__ . '/../invoice/'; 
        $fileName = $pdfurl; 
        $filePath = $folderPath . $fileName;

        if (!is_dir($folderPath)) {
            mkdir($folderPath, 0777, true);
        }
        $uploadedFilePath = $_FILES['contentFile']['tmp_name'];
        $uploadedFileType = mime_content_type($uploadedFilePath);

        
        if ($uploadedFileType === 'application/pdf') {
            if (move_uploaded_file($uploadedFilePath, $filePath)) {
                $arr['message'] = "PDF uploaded and saved successfully";
                $arr['success'] = true;
            } else {
                $arr['message'] = "Failed to save the uploaded PDF file.";
            }
        } else {
            $content = file_get_contents($uploadedFilePath);

            $mpdf = new Mpdf();
            $mpdf->WriteHTML($content);
            $mpdf->Output($filePath, \Mpdf\Output\Destination::FILE);
            $arr['message'] = "PDF uploaded and saved successfully";
            $arr['success'] = true;
        }
    } catch (\Mpdf\MpdfException $e) {
        $arr['message'] =  "Error generating PDF: " . $e->getMessage();
        $arr['success'] = false;
    }
    
}

echo json_encode($arr);
?> 