<?php

require __DIR__ . '/vendor/autoload.php';

use Mpdf\Mpdf;

$invoice_url = '../invoice/11.pdf' ?? ''; // URL of the file to include
// $invoice_url = '../signatures/Abbas Bin Rashid-5.jpg' ?? ''; // URL of the file to include

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
      .header {
        position: relative;
        padding: 10px;
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
        margin-top: 40px;
        font-size: 14px;
        width: 100%;
        border-collapse: collapse;
      }
      .completion-table-1 {
        margin-top: 20px;
        font-size: 14px;
        width: 100%;
        border-collapse: collapse;
      }
      .completion-table td {
        padding: 2px;
        vertical-align: top;
      }
      .completion-table-1 td {
        padding: 6px;
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
    <div class="main-body" style="margin:250px 0 0 0;"> 
      <h3><strong class="main-heading">WORK ORDER SERVICE INTERVAL <span>55k</span></strong></h3>
      <span class="addresstext">DHL Pakistan (Pvt) Ltd Survey Number 137 Jinnah International Airport Karachi 75100</span><br>
      <span class="addresstext">Phone # 111 500 000  Fax # 4586292  www.dhl.com.pk</span>
      
      <!-- Vehicle Information Table -->
      <table style="margin: 20px 0 15px 0;" class="vehicle-info-table">
        <tr>
          <td class="label" style="width: 140px">Vendor Name:</td>
          <td colspan="5">Suzuki Mandviwalla Motors</td>
        </tr>
      </table>
      <table class="vehicle-info-table">
        <tr>
          <td class="label">Make:</td>
          <td>Suzuki</td>
          <td class="label">Reg. No.:</td>
          <td>LA-3788</td>
          <td class="label">Date:</td>
          <td>11-Jul-24</td>
        </tr>
        <tr>
          <td class="label">Model:</td>
          <td>Cargo Van</td>
          <td class="label">Mileage:</td>
          <td>54,896</td>
          <td class="label">Station:</td>
          <td>KHI</td>
        </tr>
      </table>

      <div style="height:240px; padding:20px 20px;">
      
        <ul style="font-size:16px;">
          <li>Engine Oil</li>
          <li>Air Filter</li>
          <li>Fuel Filter</li>
        </ul>
      </div>
      <table style="margin: 20px 0 15px 0;" class="vehicle-info-table">
        <tr>
          <td class="label" style="width: 140px">Additional Job:</td>
          <td colspan="5">Brake service, Throttle service, and Spark plugs service</td>
        </tr>
      </table>

      <!-- Job Completion Section -->
      <table class="completion-table">
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
          <img src="./assets/images/sign.png"/><br>
          Received by<br>(Name and Signature)
        </div>
      </div>

      <!-- Remarks Section -->
      <div class="remarks">
        <h1 style="margin:0px;margin-top:20px;padding:0;font-size:16px;width:80px;position:absolute">Remarks:</h1>
        <div class="remarks-text-editor" style="margin:-31px 0 0 80px;"></div>
        <div class="remarks-text-editor"></div>
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

$mpdf->SetHTMLHeader('
    <div class="header">
      <img src="./assets/images/dhl-logo.png" width="150" height="30" style="margin: 30px 0 0 620px;" alt="" />
      <h1 style="margin: -35px 0 0 0px;">DHL Pakistan (Pvt.) Ltd.</h1>
      <div style="border:1px solid black;margin: 10px 0 0 0;"></div>
    </div>
');

$mpdf->AddPage('P');
$mpdf->WriteHTML($html);

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
} else {
  // Placeholder in case file doesn't exist
  $mpdf->AddPage();
  $mpdf->WriteHTML('<p style="color:red; text-align:center;">Referenced file not found!</p>');
}


$mpdf->Output('Work Order.pdf', 'I');