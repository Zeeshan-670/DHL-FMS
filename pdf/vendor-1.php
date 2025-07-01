<?php

use Mpdf\Mpdf;

// Get data from the POST request
$data = json_decode(file_get_contents('php://input'), true);



// Prepare the HTML for the PDF
$html = '
<!DOCTYPE html>
<html lang="en">
  <head>  
    <title>Vendor Approval</title>
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
      .completion-table {
        margin-top: 80px;
        font-size: 14px;
        width: 100%;
        border-collapse: collapse;
      }
    </style>
  </head>
  <body>
    <div class="main-body" style="margin:250px 0 0 0;"> 
      <h3><strong class="main-heading">' . htmlspecialchars($data['vendor']) . '</strong></h3>
      <span class="addresstext">DHL Pakistan (Pvt) Ltd Survey Number 137 Jinnah International Airport Karachi 75100</span><br>
      <span class="addresstext">Phone # 111 500 000  Fax # 4586292  www.dhl.com.pk</span>
      
      <!-- Vehicle Information Table -->
      <table style="margin: 20px 0 15px 0;" class="vehicle-info-table">
        <tr>
          <td class="label" style="width: 140px">Vendor Name:</td>
          <td colspan="5">' . htmlspecialchars($data['vendor']) . '</td>
        </tr>
      </table>
      <table class="vehicle-info-table">
        <tr>
          <td class="label">Make:</td>
          <td>' . htmlspecialchars($data['make']) . '</td>
          <td class="label">Reg. No.:</td>
          <td>' . htmlspecialchars($data['regNo']) . '</td>
          <td class="label">Date:</td>
          <td>' . htmlspecialchars($data['date']) . '</td>
        </tr>
        <tr>
          <td class="label">Model:</td>
          <td>' . htmlspecialchars($data['model']) . '</td>
          <td class="label">Mileage:</td>
          <td>' . htmlspecialchars($data['mileage']) . '</td>
          <td class="label">Station:</td>
          <td>' . htmlspecialchars($data['station']) . '</td>
        </tr>
      </table>

      <div style="height:440px; padding:20px 20px;">
        <ul style="font-size:16px;">' . 
        implode('', array_map(function($service) {
            return '<li>' . htmlspecialchars($service) . '</li>';
        }, explode(', ', $data['services']))) . '
        </ul>
      </div>
      <table style="margin: 20px 0 15px 0;" class="vehicle-info-table">
        <tr>
          <td class="label" style="width: 140px">Additional Job:</td>
          <td colspan="5">' . htmlspecialchars($data['additionalJob']) . '</td>
        </tr>
      </table>

      <!-- Job Completion Section -->
      <table class="completion-table">
        <tr>
          <td class="label" style="text-align: center; width: 50%;">Check By</td>
          <td class="label" style="text-align: center;">Approved By<br></td>
        </tr>
        <tr>
          <td style="text-align: center; width: 50%;">Coordinator/In-charge</td>
          <td style="text-align: center;">Manager/In-charge/Coordinator Signature</td>
        </tr>
      </table>

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
$mpdf->Output('Work Order.pdf', 'I');