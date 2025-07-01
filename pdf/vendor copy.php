<?php

use Mpdf\Mpdf;
require __DIR__ . '/vendor/autoload.php';
require './assets/lib/PHPMailer.php';
require './assets/lib/Exception.php';
require './assets/lib/SMTP.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // $inputData = json_decode(file_get_contents('php://input'), true) ?? $_POST;

  $workOrderData = array_map('htmlspecialchars', $_POST);
}

function generatePDF($workOrderData, $mode, $filePath = '')
{
    // Extract work order data with fallback
    $jobTitle = $workOrderData['jobTitle'] ?? 'Unknown Job Title';
    $vendorName = $workOrderData['vendorname'] ?? 'Unknown Vendor';
    $make = $workOrderData['makename'] ?? 'Unknown Make';
    $regNo = $workOrderData['Reg'] ?? 'Unknown Reg No';
    $date = $workOrderData['doi'] ?? 'Unknown Date';
    $model = $workOrderData['modelname'] ?? 'Unknown Model';
    $mileage = $workOrderData['mileage'] ?? 'Unknown Mileage';
    $station = $workOrderData['stationname'] ?? 'Unknown Station';
    $additionalJob = $workOrderData['additionaljob'] ?? 'No additional jobs';
    $services = $workOrderData['service'] ?? '';
    $partsReceived = $workOrderData['partsReceived'] ?? 'false';
    $workCompleted = $workOrderData['workCompleted'] ?? 'false';
    $completiondate = $workOrderData['completiondate'] ?? '';
    $remarks = $workOrderData['remarks'] ?? 'No Remarks';
    $signFile = $workOrderData['createdby_url'] ?? 'empty.png';
    $approvedsignFile = $workOrderData['approvedby_url'] ?? 'empty.png';

    // Convert services string to an array
    $serviceList = !empty($services) ? explode(',', $services) : [];

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
                    <td class="label">Mileage:</td>
                    <td>' . htmlspecialchars($mileage) . '</td>
                    <td class="label">Station:</td>
                    <td>' . htmlspecialchars($station) . '</td>
                </tr>
            </table>
    
            <div style="max-height:240px;height:240px; padding:20px 20px 0px 20px;">
                <ul style="font-size:16px;">
                    ' . implode('', array_map(fn($service) => '<li>' . htmlspecialchars($service) . '</li>', $serviceList)) . '
                </ul>
            </div>
            <table style="margin: 0 0 0 0;" class="vehicle-info-table">
                <tr>
                    <td class="label" style="width: 140px">Additional Job:</td>
                    <td colspan="5">' . htmlspecialchars($additionalJob) . '</td>
                </tr>
            </table>
    
            <!-- Job Completion Section -->
            <table class="completion-table">
                <tr>
                    <td class="label" style="text-align: center; width: 50%;">
                        <img width="120" src="../signatures/' . htmlspecialchars($signFile) . '"/>
                    </td>
                    <td class="label" style="text-align: center;"><img width="120" src="../signatures/' . htmlspecialchars($approvedsignFile) . '"/></td>
                </tr>
                <tr>
                    <td class="label" style="text-align: center; width: 50%;">Check By</td>
                    <td class="label"  style="text-align: center;">Approved By<br></td>
                </tr>
                <tr>
                    <td style="text-align: center; width: 50%;">Coordinator/In-charge</td>
                    <td style="text-align: center;">Manager/In-charge/Coordinator Signature</td>
                </tr>
            </table>
    
            <table class="completion-table-1">
                <tr>
                    <td colspan="2" class="label">JOB COMPLETION</td>
                    <td class="label">Date</td>
                    <td>' . htmlspecialchars($completiondate) . '</td>
                </tr>
                <tr>
                    <td class="checkbox-cell"><input type="checkbox" checked="' . htmlspecialchars($partsReceived) . '" value="2"></td>
                    <td>Above mentioned parts have been received.</td>
                    <td colspan="2"></td>
                </tr>
                <tr>
                    <td class="checkbox-cell"><input type="checkbox" checked="' . htmlspecialchars($partsReceived) . '" value="2"></td>
                    <td>Work Completed</td>
                    <td colspan="2"></td>
                </tr>
            </table>
    
            <div class="remarks">
                <p><strong>Remarks: </strong> ' . htmlspecialchars($remarks) . '</p>
            </div>
        </div>
    </body>
    </html>';

    // Initialize MPDF instance with configuration
    $mpdf = new Mpdf([
        'mode' => 'utf-8',
        'format' => 'A4',
        'tempDir' => __DIR__ . '/tmp_pdf',
        'margin_left' => 15,
        'margin_right' => 15,
        'margin_top' => 10,
        'margin_bottom' => 10,
    ]);

    // Add page and write HTML
    $mpdf->AddPage();
    $mpdf->WriteHTML($html);

    // Save PDF to specified path or output to browser
    if ($mode === 'view') {
        // Output PDF to browser
        $mpdf->Output('WorkOrder.pdf', 'I');
    } else {
        // Save PDF to file path
        $mpdf->Output($filePath, 'F');
    }

    return $mpdf->Output('', 'S');
}

// Email sending function
function sendEmailWithAttachment($pdfContent, $recipientEmail, $regno)
{
    $mail = new PHPMailer(true);

    $response = ['flag' => false, 'message' => 'Email could not be sent.'];

    try {
        $mail->isSMTP();
        $mail->Host = '192.168.20.204'; 
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        $mail->Username = 'broadcast@itecknologi.com';
        $mail->Password = 'Karachi@123';

        $mail->setFrom('broadcast@itecknologi.com');
        $mail->addAddress($recipientEmail);
        $mail->addBCC('toyota.privilege@itecknologi.com');

        $mail->isHTML(true);
        $mail->Subject = 'Toyota Discount Coupon For REG # ' . $regno . ' | in Collaboration with iTecknologi';
        $mail->Body = 'Please find the attached Toyota Coupon.';
        $mail->addStringAttachment($pdfContent, 'Toyota Coupon.pdf');

        $mail->send();

        $response['flag'] = true;
        $response['message'] = 'Email sent successfully!';
    } catch (Exception $e) {
        $response['message'] = 'Coupon Email could not be sent. ' . $e->getMessage();
    }

    return $response;
}