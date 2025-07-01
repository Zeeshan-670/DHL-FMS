<?php
session_start();

use Mpdf\Mpdf;

require __DIR__ . '/vendor/autoload.php';
require './assets/lib/PHPMailer.php';
require './assets/lib/Exception.php';
require './assets/lib/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function dbconnection()
{
  $username = "root";
  $password = "cmsserverv6";
  $database = "fms";
  $port = '3311';

  // Opens a connection to a MySQL server
  $connection = mysqli_connect('localhost:3311', $username, $password, $database);
  return $connection;
}

$conn = dbconnection();


// Suppress warnings for GD issues
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);


// Check if the request is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // $inputData = json_decode(file_get_contents('php://input'), true) ?? $_POST;

  $workOrderData = array_map('htmlspecialchars', $_POST);
  $expenses = json_decode(htmlspecialchars_decode($workOrderData['expenses'] ?? '[]'), true);

  // var_dump($workOrderData);
  // var_dump($_SESSION);
  // die;
  // Determine the mode: view or email
  $mode = $workOrderData['mode'] ?? 'view'; // Default to 'view'
  $email = 'muneerzeeshan670@gmail.com';
  // Prepare variables with default fallback values
  $jobTitle = $workOrderData['jobTitle'] ?? 'Unknown Job Title';
  $vendorName = $workOrderData['vendorname'] ?? 'Unknown Vendor';
  $make = $workOrderData['makename'] ?? 'Unknown Make';
  $regNo = $workOrderData['Reg'] ?? 'Unknown Reg No';
  $date = $workOrderData['creationdate'] ?? 'Unknown Date';
  $model = $workOrderData['modelname'] ?? 'Unknown Model';
  $mileage = $workOrderData['mileage'] ?? 'Unknown Mileage';
  $station = $workOrderData['stationname'] ?? 'Unknown Station';
  $additionalJob = $workOrderData['additionaljob'] ?? 'No additional jobs';
  $services = $workOrderData['service'] ?? '';
  $createdby = $workOrderData['createdby'] ?? 'Unknown User';
  $approvedby = $workOrderData['approvedby'] ?? '';
  $checkedby   = $workOrderData['checkedby'] ?? '';
  $partsReceived = $workOrderData['partsReceived'] ?? 'false';
  $workCompleted = $workOrderData['workCompleted'] ?? 'false';
  $completiondate = $workOrderData['completiondate'] ?? '';
  $job_id = $workOrderData['job_id'] ?? '';
  $user_id = $_SESSION['user_id'] ?? '';
  $chain = $workOrderData['chain'] ?? '';
  $remarks = $workOrderData['remarks'] ?? 'No Remarks';
  $invoice_url = '../invoice/' . ($workOrderData['invoice'] ?? '');
  $chainArray = json_decode(htmlspecialchars_decode($workOrderData['chain'] ?? '[]'), true);

  // $signFile = $workOrderData['createdby_url'] ? $workOrderData['createdby_url'] : 'empty.png';
  // $approvedsignFile = $workOrderData['approvedby_url'] ? $workOrderData['approvedby_url'] : 'empty.png';

  $circle = '
  <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10">
    <circle cx="5" cy="5" r="5" fill="black"></circle>
  </svg>
';

$chainResult = [];

if (is_array($chainArray)) {
    foreach ($chainArray as $entry) {
        if (in_array($entry['ModifiedStatus'], ['Quotation Received', 'Job in progress', 'Pending']) 
            && $entry['Designation'] !== "-") {
            $chainResult[$entry['ModifiedStatus']] = [
                'ModifiedBy' => $entry['ModifiedBy'],
                'Designation' => $entry['Designation']
            ];
        }
    }
}

function capitalizeFirstLetter($string) {
  return ucwords(strtolower($string));
}

// var_dump($chainResult);
// die;



// var_dump($chainResult['Quotation Received']);
// echo '___________________';

// Output chainResult
// var_dump($chain);
// echo $chainArray;
// echo json_encode($chainResult, JSON_PRETTY_PRINT);
// die;


  $expenseListHTML = '<div style="font-size:16px;">';
  $grandTotal = 0; // Initialize grand total

  if (is_array($expenses)) {
    foreach ($expenses as $expense) {
      $expenseTitle = htmlspecialchars($expense['ExpenseTitle'] ?? 'Unknown Expense');
      $expenseDesc = htmlspecialchars($expense['description'] ?? 'Unknown Description');
      $expenseAmount = floatval($expense['amount'] ?? 0); // Convert to number
      $expenseSalesTax = floatval($expense['salesTaxValue'] ?? 0); // Convert to number (percentage)

      // Calculate total amount including sales tax
      // $totalWithTax = $expenseAmount * (1 + $expenseSalesTax / 100);
      $totalWithTax = $expenseAmount;
      $grandTotal += $totalWithTax; // Add to grand total

      $expenseListHTML .= '<div style="padding: 0 10px;">
              <table style="margin: 2px 0 2px 0;" class="expense-info-table">
                  <tr>
                      <td style="width: 220px;"><strong>' . $circle . ' &nbsp; ' . $expenseTitle . '</strong></td>
                      <td style="width: 320px">' . $expenseDesc . '</td>
                      <td colspan="5" style="text-align: right;"><strong>Rs.' . number_format($totalWithTax, 2) . '/-</strong></td>
                  </tr>
              </table>
          </div>';
    }

    // Add Grand Total row at the end
    $expenseListHTML .= '<div style="background-color:rgb(226, 226, 226);margin-top:30px;padding: 0 10px;">
          <table style="margin: 7.5px 0 7.5px 0;padding: 7.5px 0 7.5px 0;font-weight: bold;" class="expense-info-table">
              <tr>
                  <td style="width: 250px;">Grand Total:</td>
                  <td colspan="5" style="text-align: right;">Rs.' . number_format($grandTotal, 2) . '/-</td>
              </tr>
          </table>
      </div>';
  } else {
    $expenseListHTML .= '<li>Invalid expenses data.</li>';
  }

  $expenseListHTML .= '</div>';



  $check = '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
  <path d="M16.0303 10.0303C16.3232 9.73744 16.3232 9.26256 16.0303 8.96967C15.7374 8.67678 15.2626 8.67678 14.9697 8.96967L10.5 13.4393L9.03033 11.9697C8.73744 11.6768 8.26256 11.6768 7.96967 11.9697C7.67678 12.2626 7.67678 12.7374 7.96967 13.0303L9.96967 15.0303C10.2626 15.3232 10.7374 15.3232 11.0303 15.0303L16.0303 10.0303Z" fill="#1C274C"/>
  <path fill-rule="evenodd" clip-rule="evenodd" d="M12.0574 1.25H11.9426C9.63424 1.24999 7.82519 1.24998 6.41371 1.43975C4.96897 1.63399 3.82895 2.03933 2.93414 2.93414C2.03933 3.82895 1.63399 4.96897 1.43975 6.41371C1.24998 7.82519 1.24999 9.63422 1.25 11.9426V12.0574C1.24999 14.3658 1.24998 16.1748 1.43975 17.5863C1.63399 19.031 2.03933 20.1711 2.93414 21.0659C3.82895 21.9607 4.96897 22.366 6.41371 22.5603C7.82519 22.75 9.63423 22.75 11.9426 22.75H12.0574C14.3658 22.75 16.1748 22.75 17.5863 22.5603C19.031 22.366 20.1711 21.9607 21.0659 21.0659C21.9607 20.1711 22.366 19.031 22.5603 17.5863C22.75 16.1748 22.75 14.3658 22.75 12.0574V11.9426C22.75 9.63423 22.75 7.82519 22.5603 6.41371C22.366 4.96897 21.9607 3.82895 21.0659 2.93414C20.1711 2.03933 19.031 1.63399 17.5863 1.43975C16.1748 1.24998 14.3658 1.24999 12.0574 1.25ZM3.9948 3.9948C4.56445 3.42514 5.33517 3.09825 6.61358 2.92637C7.91356 2.75159 9.62177 2.75 12 2.75C14.3782 2.75 16.0864 2.75159 17.3864 2.92637C18.6648 3.09825 19.4355 3.42514 20.0052 3.9948C20.5749 4.56445 20.9018 5.33517 21.0736 6.61358C21.2484 7.91356 21.25 9.62177 21.25 12C21.25 14.3782 21.2484 16.0864 21.0736 17.3864C20.9018 18.6648 20.5749 19.4355 20.0052 20.0052C19.4355 20.5749 18.6648 20.9018 17.3864 21.0736C16.0864 21.2484 14.3782 21.25 12 21.25C9.62177 21.25 7.91356 21.2484 6.61358 21.0736C5.33517 20.9018 4.56445 20.5749 3.9948 20.0052C3.42514 19.4355 3.09825 18.6648 2.92637 17.3864C2.75159 16.0864 2.75 14.3782 2.75 12C2.75 9.62177 2.75159 7.91356 2.92637 6.61358C3.09825 5.33517 3.42514 4.56445 3.9948 3.9948Z" fill="#1C274C"/>
  </svg>';

  $uncheck = '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
  <path fill-rule="evenodd" clip-rule="evenodd" d="M12.0574 1.25H11.9426C9.63424 1.24999 7.82519 1.24998 6.41371 1.43975C4.96897 1.63399 3.82895 2.03933 2.93414 2.93414C2.03933 3.82895 1.63399 4.96897 1.43975 6.41371C1.24998 7.82519 1.24999 9.63422 1.25 11.9426V12.0574C1.24999 14.3658 1.24998 16.1748 1.43975 17.5863C1.63399 19.031 2.03933 20.1711 2.93414 21.0659C3.82895 21.9607 4.96897 22.366 6.41371 22.5603C7.82519 22.75 9.63423 22.75 11.9426 22.75H12.0574C14.3658 22.75 16.1748 22.75 17.5863 22.5603C19.031 22.366 20.1711 21.9607 21.0659 21.0659C21.9607 20.1711 22.366 19.031 22.5603 17.5863C22.75 16.1748 22.75 14.3658 22.75 12.0574V11.9426C22.75 9.63423 22.75 7.82519 22.5603 6.41371C22.366 4.96897 21.9607 3.82895 21.0659 2.93414C20.1711 2.03933 19.031 1.63399 17.5863 1.43975C16.1748 1.24998 14.3658 1.24999 12.0574 1.25ZM3.9948 3.9948C4.56445 3.42514 5.33517 3.09825 6.61358 2.92637C7.91356 2.75159 9.62177 2.75 12 2.75C14.3782 2.75 16.0864 2.75159 17.3864 2.92637C18.6648 3.09825 19.4355 3.42514 20.0052 3.9948C20.5749 4.56445 20.9018 5.33517 21.0736 6.61358C21.2484 7.91356 21.25 9.62177 21.25 12C21.25 14.3782 21.2484 16.0864 21.0736 17.3864C20.9018 18.6648 20.5749 19.4355 20.0052 20.0052C19.4355 20.5749 18.6648 20.9018 17.3864 21.0736C16.0864 21.2484 14.3782 21.25 12 21.25C9.62177 21.25 7.91356 21.2484 6.61358 21.0736C5.33517 20.9018 4.56445 20.5749 3.9948 20.0052C3.42514 19.4355 3.09825 18.6648 2.92637 17.3864C2.75159 16.0864 2.75 14.3782 2.75 12C2.75 9.62177 2.75159 7.91356 2.92637 6.61358C3.09825 5.33517 3.42514 4.56445 3.9948 3.9948Z" fill="#1C274C"/>
  </svg>';
  // Convert services string to an array
  // $serviceList = !empty($services) ? explode(',', $services) : [];

  // Generate the HTML for the PDF
  $html = '
  <!DOCTYPE html>
  <html lang="en">
    <head>  
      <title>Work Order Service Interval</title>
      <style>
        * {
          margin: 0;
          padding: 0;
          box-sizing: border-box;
          font-family: sans-serif;
        }
        body {
          font-size: 10px;
          background-color: #fff;
        }
        .header-container{
          }
          .header {
          padding: 0 10px;
          position: absolute;
          top: 10px;
          right:20px;
          left:20px;
        }
        .expenseList{
          width: 250px;
        }
        .header h1 {
          font-size: 26px;
        }
        .main-body {
          padding: 0 10px;
        }
        .main-body h3 {
          margin: 0 0 12px 0;
        }
        .main-body h3 strong.main-heading {
          font-size: 20px;
          border-bottom: 2px solid black;
        }
        .addresstext {
          font-size: 16px;
        }
        .vehicle-info-table {
          margin-top: 10px;
          font-size: 14px;
          width: 100%;
          border-collapse: collapse;
        }
        .vehicle-info-table td {
          padding: 8px 0px;
          vertical-align: top;
        }
        .vehicle-info-table .label {
          font-weight: bold;
        }
          .expense-info-table {
          margin-top: 2px;
          font-size: 14px;
          width: 100%;
          border-collapse: collapse;
        }
        .expense-info-table td {
          padding: 2px 0px;
          vertical-align: top;
        }
        .expense-info-table .label {
          font-weight: bold;
        }
        .label {
          font-weight: bold;
        }
        .completion-table {
          margin-top: 10px;
          font-size: 14px;
          width: 100%;
          border-collapse: collapse;
        }
        .completion-table-1 {
          margin-top: 40px;
          font-size: 14px;
          width: 100%;
          border-collapse: collapse;
        }
        .completion-table td {
          padding: 2px;
          vertical-align: top;
        }
        .completion-table-1 td {
          padding: 0 0 6px 0 ;
          vertical-align: top;
        }
        .checkbox-cell {
          width: 20px;
          text-align: center;
        }
        .remarks {
          margin-top: 10px;
          padding-top: 5px;
          position: relative;
        }
        .remarks-text-editor{
          border-bottom: 1px solid black;
          height:30px;
        }
      </style>
    </head>
    <body>
      <div class="header">
        <img src="./assets/images/dhl-logo.png" width="150" height="30" style="margin: 20px 0 10px 620px;" alt="" />
        <h1 style="margin: -35px 0 0 0px;font-size:25px;">DHL Pakistan (Pvt.) Ltd.</h1>
        <div style="border:1px solid black;margin: 10px 0 0 0;"></div>
      </div>
      <div class="main-body" style="margin:-200px 0 0 0;"> 
        <h3><strong class="main-heading">' . htmlspecialchars($jobTitle) . '</strong></h3>
        <span class="addresstext">DHL Pakistan (Pvt) Ltd Survey Number 137 Jinnah International Airport Karachi 75100</span><br>
        <span class="addresstext">Phone # 111 500 000  Fax # 4586292  www.dhl.com.pk</span>
        
        <!-- Vehicle Information Table -->
        <table style="margin: 20px 0 15px 0;" class="vehicle-info-table">
          <tr>
            <td class="label" style="width: 140px">Vendor Name:</td>
            <td colspan="5">' . htmlspecialchars($vendorName) . '</td>
          </tr>
        </table>
        <table class="vehicle-info-table">
            <tr>
                <td class="label">Make:</td>
                <td>' . htmlspecialchars($make) . '</td>
                <td class="label">Reg. No.:</td>
                <td>' . htmlspecialchars($regNo) . '</td>
                <td class="label">Date:</td>
                <td>' . htmlspecialchars($date) . '</td>
            </tr>
            <tr>
                <td class="label">Model:</td>
                <td>' . htmlspecialchars($model) . '</td>
                <td class="label">Station:</td>
                <td>' . htmlspecialchars($station) . '</td>
                <td class="label"></td>
                <td></td>
            </tr>
        </table>

        <div style="max-height:300px;height:300px; padding:20px 20px 0px 20px;">
          <h2>Please under take vehicle repair & maintenance work.</h2>
            ' . $expenseListHTML . '
        </div>

        <!-- Job Completion Section -->
        <table class="completion-table">
          <tr>
            <td class="label" style="text-align: center; width: 50%;">
                <h5>' . $chainResult['Quotation Received']['ModifiedBy'] . '</h5>
            </td>
            <td class="label" style="text-align: center;"><h5>' . $chainResult['Job in progress']['ModifiedBy'] . '</h5></td>
          </tr>
          <tr>
            <td style="text-align: center; width: 50%;">Checked By</td>
            <td style="text-align: center;">Approved By<br></td>
          </tr>
          <tr>
            <td class="label" style="text-align: center; width: 50%;">' . $chainResult['Quotation Received']['Designation'] . '</td>
            <td class="label" style="text-align: center;">' . $chainResult['Job in progress']['Designation'] . '</td>
          </tr>
        </table>

        <table class="completion-table-1">
          <tr>
            <td colspan="2" class="label">JOB COMPLETION</td>
            <td class="label">Date</td>
            <td>' . htmlspecialchars($completiondate) . '</td>
          </tr>
          <tr>
          
            <td class="checkbox-cell" style="margin-right:15px"> ' . (htmlspecialchars($partsReceived) == 'true' ? $check  : $uncheck) . '
  <td>Above mentioned parts have been received.</td>
  <td colspan="2"></td>
  </tr>
  <tr>
      <td class="checkbox-cell" style="margin-right:15px"> ' . (htmlspecialchars($workCompleted) == 'true' ? $check : $uncheck) . ' </td>
      <td>Above mentioned work has been completed with satisfaction.</td>
      <td colspan="2"></td>
  </tr>
  </table>

  <div style="margin: -20px 80px 0 480px">
      <div style="text-align: center">
          <strong>' . capitalizeFirstLetter($chainResult['Pending']['ModifiedBy']) . '</strong><br>
          Received by<br>
      </div>
  </div>

  <!-- Remarks Section -->
  <div class="remarks">
      <h1 style="margin:0px;margin-top:10px;padding:0;font-size:18px;position:absolute;font-weight: 100;"><span
              style="font-weight: 900 !important;">Remarks: </span> <span
              style="margin-left:40px;font-size:12px;font-weight: 100 !important;">' . htmlspecialchars($remarks) . '
          </span></h1>

  </div>
  </div>
  </body>

  </html>';

  // Initialize Mpdf
  // $mpdf = new Mpdf(['margin_top' => 15, 'margin_left' => 10, 'margin_right' => 10]);
  // Initialize MPDF configuration
  $mpdfConfig = [
    'margin_top' => 25,
    'margin_left' => 5,
    'margin_right' => 5,
    'default_font' => 'sans-serif',
    'margin_header' => 0,
  ];

  // Adjust MPDF configuration for email mode
  if ($mode === 'email' && $email) {
    $mpdfConfig['default_font'] = 'serif';
    $mpdfConfig['tempDir'] = __DIR__ . '\temp';
  }

  // Initialize MPDF
  $mpdf = new \Mpdf\Mpdf($mpdfConfig);

  if ($mode === 'email' && $email) {
    // Check if the file is an image or a PDF
    if (file_exists($invoice_url)) {
      // Check if the file is an image or a PDF
      $file_extension = pathinfo($invoice_url, PATHINFO_EXTENSION);
      if (in_array(strtolower($file_extension), ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg'])) {
        // If the file is an image, embed it
        $mpdf->AddPage();
        $mpdf->Image($invoice_url, 10, 10, 190);  // Adjust as needed
      } else {
        // If it's a PDF, try to handle it without FPDI error
        try {
          $pageCount = $mpdf->SetSourceFile($invoice_url);
          for ($i = 1; $i <= $pageCount; $i++) {
            $importedPage = $mpdf->ImportPage($i);
            $mpdf->AddPage();
            $mpdf->UseTemplate($importedPage);
          }
        } catch (Exception $e) {
          // Handle the error gracefully
          $mpdf->AddPage();
          $mpdf->WriteHTML('<p style="color:red; text-align:center;">Could not process the referenced PDF. It may be compressed in an unsupported format.</p>');
        }
      }
    }

    // Add the HTML content after the invoice
    $mpdf->AddPage(); // Optionally, add a new page before the HTML
    $mpdf->WriteHTML($html);

    $timestamp = time();
    $filePath = __DIR__ . "/temp/WorkOrder_{$timestamp}.pdf";
    $mpdf->Output($filePath, 'F');
    $mail = new PHPMailer(true);

    try {
      $mail->isSMTP();
      $mail->Host = '192.168.20.204';
      $mail->SMTPAuth = true;
      $mail->SMTPSecure = 'tls';
      $mail->Port = 587;
      $mail->Username = "broadcast@itecknologi.com";
      $mail->Password = "Karachi@123";
      $mail->setFrom('broadcast@itecknologi.com', 'iTecknologi');
      $mail->SMTPOptions = array('ssl' => array('verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' =>
      true));
      // $mail->addAddress($email);
      $mail->addAddress('jawaid.khalid@dhl.com');
      $mail->addCC($email); 
      $mail->addCC('hamza.khan@itecknologi.com'); 
      // $mail->addAddress('omar.ahmed@itecknologi.com');

      $mail->isHTML(true);
      $mail->Subject = 'Work Order PDF';
      $mail->Body = '<p>Find the attached Work Order PDF.</p>';
      $mail->addAttachment($filePath, 'WorkOrder.pdf');

      $mail->send();
      echo json_encode(['success' => true, 'message' => 'Email sent successfully.']);
      status($conn, $job_id, $user_id);
    } catch (Exception $e) {
      echo json_encode(['success' => false, 'message' => 'Error sending email: ' . $e->getMessage()]);
    } finally {
      if (file_exists($filePath)) {
        unlink($filePath);
      }
    }
  } else {
    // Add the HTML content after the invoice
    // Check if the file is an image or a PDF
    if (file_exists($invoice_url)) {
      // Check if the file is an image or a PDF
      $file_extension = pathinfo($invoice_url, PATHINFO_EXTENSION);
      if (in_array(strtolower($file_extension), ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg'])) {
        // If the file is an image, embed it
        $mpdf->AddPage();
        $mpdf->Image($invoice_url, 10, 10, 190);  // Adjust as needed
      } else {
        // If it's a PDF, try to handle it without FPDI error
        try {
          $pageCount = $mpdf->SetSourceFile($invoice_url);
          for ($i = 1; $i <= $pageCount; $i++) {
            $importedPage = $mpdf->ImportPage($i);
            $mpdf->AddPage();
            $mpdf->UseTemplate($importedPage);
          }
        } catch (Exception $e) {
          // Handle the error gracefully
          $mpdf->AddPage();
          $mpdf->WriteHTML('<p style="color:red; text-align:center;">Could not process the referenced PDF. It may be compressed in an unsupported format.</p>');
        }
      }
    }

    // Add the HTML content after the invoice
    $mpdf->AddPage(); // Optionally, add a new page before the HTML
    $mpdf->WriteHTML($html);

    // Output PDF for viewing
    $mpdf->Output('WorkOrder.pdf', 'I');
  }
} else {
  echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}


function status($conn, $job_id, $user_id)
{
  $reason = '';
  $creationdate = date("Y-m-d");
  $type = "user";
  $status = "Completed";
  // Update job status
  $query = "UPDATE job SET status = ?, reason = ?, checkby = ? WHERE id = ?";
  $stmt = $conn->prepare($query);
  if (!$stmt) {
    throw new Exception("Error preparing statement: " . $conn->error);
  }
  $stmt->bind_param("ssii", $status, $reason, $user_id, $job_id);
  if (!$stmt->execute()) {
    throw new Exception("Error executing update: " . $stmt->error);
  }

  // Update quotation history
  $rfqUpdateHistory = "INSERT INTO rfqUpdateHistory (rfq_id, modifiedby, date, status,modifier_type) 
  VALUES (?, ?, ?, ?, ?)";
  $rfq_stmt = $conn->prepare($rfqUpdateHistory);

  if (!$rfq_stmt) {
    $arr["success"] = false;
    $arr["message"] = "Error preparing expense entry statement: " . $conn->error;
    exit;
  }

  $rfq_stmt->bind_param("iisss", $job_id, $user_id, $creationdate, $status, $type);

  if (!$rfq_stmt->execute()) {
    $arr["success"] = false;
    $arr["message"] = "Error inserting expense entry: " . $conn->error;
    exit;
  }

  $rfq_stmt->close();
}
