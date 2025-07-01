<?php
require_once __DIR__ . '/../pdf/vendor/autoload.php';
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json"); 
include("database.php");

use Mpdf\Mpdf;


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
    if(isset($_POST['download'])){
        download($conn, $arr);
    }else{
        add($conn, $arr);
    }
    
}
function add($conn, &$arr){
    $V_id = $_POST['V_id']  ?? '';
    $vendor_id = $_POST['vendor_id'] ?? '';
    $service = $_POST['service'] ?? '';
    $jobtitle = $_POST['jobtitle'] ?? '';
    
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
    if (empty($errors)) {
        $creationdate = date("Y-m-d"); 
        $creationby = 1;
        $status = 'pending';
        $query = $conn->prepare("INSERT INTO workorder(V_id,vendor_id,service,jobtitle,creationdate, creationby,status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if (!$query) {
            die("Query preparation failed: " . $conn->error); 
        }else{
            $query->bind_param('iisssis',$V_id ,$vendor_id, $service, $jobtitle,  $creationdate, $creationby, $status);
            $exe=$query->execute();

            if($exe === false){
                $arr["message"] = "Error: " . mysqli_error($conn);
                $arr["success"]=false;
            }else{
                $inserted_id = $conn->insert_id;
                $pdfurl = $inserted_id. '.pdf';
                $insert = $conn->prepare("update workorder set pdfurl = ? where id = ?");
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

function download($conn, &$arr){
    if(isset($_POST['id'])){
        try {
            $mpdf = new Mpdf();
            $id = $_POST['id'];
            $folderPath = __DIR__ . '/../workorder/'; 
            $fileName = $id. '.pdf'; 
            $filePath = $folderPath . $fileName;

            if (!is_dir($folderPath)) {
                mkdir($folderPath, 0777, true);
            }
            $uploadedFilePath = $_FILES['contentFile']['tmp_name'];
            $uploadedFileType = mime_content_type($uploadedFilePath);

            
            if ($uploadedFileType === 'application/pdf') {
                if (move_uploaded_file($uploadedFilePath, $filePath)) {
                    $arr['message'] = "PDF uploaded and saved successfully to $filePath";
                    $arr['success'] = true;
                } else {
                    $arr['message'] = "Failed to save the uploaded PDF file.";
                }
            } else {
                $content = file_get_contents($uploadedFilePath);

                $mpdf = new Mpdf();
                $mpdf->WriteHTML($content);
                $mpdf->Output($filePath, \Mpdf\Output\Destination::FILE);
                $arr['message'] = "PDF uploaded and saved successfully to $filePath";
                $arr['success'] = true;
            }
        } catch (\Mpdf\MpdfException $e) {
            $arr['message'] =  "Error generating PDF: " . $e->getMessage();
            $arr['success'] = false;
        }
    }
}

echo json_encode($arr);
?> 