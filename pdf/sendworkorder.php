<?php

use Mpdf\Mpdf;

// var_dump($_POST);
// die;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Retrieve and sanitize the input data
  $workOrderData = array_map('htmlspecialchars', $_POST);

  // Prepare variables with default fallback values
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
  $signFile = $workOrderData['createdby_url'] ? $workOrderData['createdby_url'] : 'empty.png';
  $approvedsignFile = $workOrderData['approvedby_url'] ? $workOrderData['approvedby_url'] : 'empty.png';

  // Convert services string to an array
  $serviceList = !empty($services) ? explode(',', $services) : [];
} else {
  die('Invalid request method. This endpoint only accepts POST requests.');
}

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
          <td>11-Jul-24</td>
        </tr>
        <tr>
          <td class="checkbox-cell"><input type="checkbox" checked="true" value="2"></td>
          <td>Above mentioned parts have been received.</td>
           <td colspan="2"></td>
        </tr>
        <tr>
          <td class="checkbox-cell">☐</td>
          <td>Above mentioned work has been completed with satisfaction.</td>
          <td colspan="2"></td>
        </tr>
      </table>

      <div style="margin: -40px 80px 0 480px">
        <div style="text-align: center">
          <img src="../signatures/empty.png"/><br>
          Received by<br>(Name and Signature)
        </div>
      </div>

      <!-- Remarks Section -->
      <div class="remarks">
        <h1 style="margin:0px;margin-top:10px;padding:0;font-size:16px;width:80px;position:absolute">Remarks:</h1>
        <div class="remarks-text-editor" style="margin:-31px 0 0 80px;"></div>
        <div class="remarks-text-editor"></div>
      </div>
    </div>
  </body>
</html>';

require_once __DIR__ . '/vendor/autoload.php';
$mpdf = new \Mpdf\Mpdf([
  'margin_top' => 25,
  'margin_left' => 5,
  'margin_right' => 5,
  'default_font' => 'sans-serif',
  'margin_header' => 0,
]);

// You don't set a header, so no $mpdf->SetHTMLHeader() call

$mpdf->AddPage('P');
$mpdf->WriteHTML($html);
$mpdf->Output('Work Order.pdf', 'I');