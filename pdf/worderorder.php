<?php

use Mpdf\Mpdf;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize the input data
    $workOrderData = array_map('htmlspecialchars', $_POST);
    // $expenses = json_decode(htmlspecialchars_decode($workOrderData['expenses'] ?? '[]'), true);
    $expenses = json_decode(htmlspecialchars_decode($workOrderData['expenses'] ?? '[]'), true);

    // Decode expenses JSON properly
    // echo $expenses;
    // die;
    // var_dump($workOrderData);
    // die;
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
    $checkedby	 = $workOrderData['checkedby'] ?? '';
    $signFile = $workOrderData['createdby_url'] ? $workOrderData['createdby_url'] : 'empty.png';
    $chainArray = json_decode(htmlspecialchars_decode($workOrderData['chain'] ?? '[]'), true);

    $circle = '
  <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10">
    <circle cx="5" cy="5" r="5" fill="black"></circle>
  </svg>
';

    // $expenseListHTML = '<ul style="font-size:16px;">';
    // if (is_array($expenses)) {
    //     foreach ($expenses as $expense) {
    //         $expenseTitle = htmlspecialchars($expense['ExpenseTitle'] ?? 'Unknown Expense');
    //         $expenseListHTML .= "<li>{$expenseTitle}</li>";
    //     }
    // } else {
    //     $expenseListHTML .= "<li>Invalid expenses data.</li>";
    // }
    // $expenseListHTML .= '</ul>';


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
} else {
    die('Invalid request method. This endpoint only accepts POST requests.');
}

// Create the HTML content with dynamic data
$html = '
<!DOCTYPE html>
<html lang="en">
<head>  
    <title>'. $jobTitle .'</title>
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
        .main-body div strong.main-heading {
            font-size: 18px;
        }
        .main-body div span.vendorName {
            font-size: 18px;
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
        .completion-table {
            margin-top: 80px;
            font-size: 14px;
            width: 100%;
            border-collapse: collapse;
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
    </style>
</head>
<body>
    <div class="main-body" style="margin:250px 0 0 0;"> 
        <h3><strong class="main-heading">' . htmlspecialchars($jobTitle) . '</strong></h3>
        <span class="addresstext">DHL Pakistan (Pvt) Ltd Survey Number 137 Jinnah International Airport Karachi 75100</span><br>
        <span class="addresstext">Phone # 111 500 000  Fax # 4586292  www.dhl.com.pk</span>
        
        <div style="margin: 20px 0 15px 0;">
            <div><strong class="main-heading">M / S:    </strong> <span class="vendorName"> ' . htmlspecialchars($vendorName) . '</span></h3>
        </div>
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
            <td style="text-align: center; width: 50%;">Check By</td>
            <td style="text-align: center;">Approved By<br></td>
          </tr>
          <tr>
            <td class="label" style="text-align: center; width: 50%;"><strong>' . $chainResult['Quotation Received']['Designation'] . '</strong></td>
            <td class="label" style="text-align: center;"><strong>' . $chainResult['Job in progress']['Designation'] . '</strong></td>
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
$mpdf->Output('Work_Order.pdf', 'I');


// <div style="height:350px; padding:20px 20px;">
// <h2>Please under take vehicle repair & maintenance work.</h2>
// ' . $expenseListHTML . ' 
// </div>
// <table style="margin: 20px 0 15px 0;" class="vehicle-info-table">
// <tr>
//     <td class="label" style="width: 140px">Additional Job:</td>
//     <td colspan="5">' . htmlspecialchars($additionalJob) . '</td>
// </tr>
// </table>
