<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json"); 
include("database.php");

$arr = [
    "message" => "", 
    "success" => false,  
    "data" => []  
];

$mdvrconn = dbmileage();
$conn = dbconnection();

$starttime = $_POST['starttime'];
$endtime = $_POST['endtime'];
$platenumber = $_POST['platenumber'];

// Split the plate numbers string into an array (assuming it's comma-separated)
$plateNumbersArray = explode(',', $platenumber);

// Create placeholders for each plate number
$placeholders = implode(',', array_fill(0, count($plateNumbersArray), '?'));

// Query to fetch data with proper handling of plate number
$query = $mdvrconn->prepare("SELECT Date, TRAVEL_DISTANCE, FUEL_CONSUMED 
                             FROM vehicle_fuel 
                             WHERE DATE >= ? 
                             AND Date <= ? 
                             AND PLATE_NUMBER IN ($placeholders)");  // Use placeholders for plate numbers

// Merge the starttime, endtime, and the plate numbers array into one array
$params = array_merge([$starttime, $endtime], $plateNumbersArray);

// Bind parameters dynamically
$query->bind_param(str_repeat('s', count($params)), ...$params);

$query->execute();

$exe = $query->get_result();
$result = $exe->fetch_all(MYSQLI_ASSOC);

// Arrays to hold the summed distance and fuel data
$distanceData = [];
$fuelData = [];

if ($result === false) {
    $arr["message"] = "Error: " . mysqli_error($mdvrconn);  // Fix $conn to $mdvrconn
    $arr["success"] = false;
} else {
    if (count($result) > 0) {
        // Loop through each result and populate the data arrays with summed values
        foreach ($result as $row) {
            // Format the date as Y-m-d
            $date = date("Y-m-d", strtotime($row['Date']));  // Format date to Y-m-d
            $distance = $row['TRAVEL_DISTANCE'];  // Distance for the date
            $fuel = $row['FUEL_CONSUMED'];  // Fuel consumed for the date

            // Sum the distance for the same date and round to 2 decimal places
            if (isset($distanceData[$date])) {
                $distanceData[$date] += $distance;  // Add to existing value
            } else {
                $distanceData[$date] = $distance;  // Initialize with the first value
            }

            // Sum the fuel for the same date and round to 2 decimal places
            if (isset($fuelData[$date])) {
                $fuelData[$date] += $fuel;  // Add to existing value
            } else {
                $fuelData[$date] = $fuel;  // Initialize with the first value
            }
        }

        // Round the summed values to 2 decimal places
        foreach ($distanceData as $date => $distance) {
            $distanceData[$date] = round($distance, 2);  // Round distance to 2 decimals
        }

        foreach ($fuelData as $date => $fuel) {
            $fuelData[$date] = round($fuel, 2);  // Round fuel to 2 decimals
        }

        // Set the success flag and prepare the data
        // $arr["success"] = true;
        // $arr["data"] = [
        //     "distance" => $distanceData,
        //     "fuel" => $fuelData
        // ];
    } else {
        $arr["message"] = "Error: No data";
        $arr["success"] = false;
    }
}

$query1 = $conn->prepare("SELECT InvoiceDate, GrandTotal, st.value as salesTax
                          FROM expense_entries e 
                          JOIN expense_details d ON d.ExpenseId = e.ExpenseID 
                          JOIN vehicles v ON v.V_id = e.V_id 
                          JOIN salestax st ON st.id = d.salesTax
                          WHERE InvoiceDate BETWEEN ? AND ? 
                          AND v.reg IN ($placeholders) 
                          GROUP BY e.ExpenseId");

$params1 = array_merge([$starttime, $endtime], $plateNumbersArray);
$query1->bind_param(str_repeat('s', count($params1)), ...$params1);
$query1->execute();
$result1 = $query1->get_result()->fetch_all(MYSQLI_ASSOC);

$expenseData = [];
if ($result1 !== false) {
    foreach ($result1 as $row) {
        $date = date("Y-m-d", strtotime($row['InvoiceDate']));
        $amount = $row['GrandTotal'];
        $tax = $row['salesTax'];
        $expense = number_format((float)$amount * (1 + (float)$tax / 100), 2, '.', '');

        if (isset($expenseData[$date])) {
            $expenseData[$date] += $expense;  // Add to existing value
        } else {
            $expenseData[$date] = $expense;  // Initialize with the first value
        }
    }
     // Generate all dates between starttime and endtime
     $dateRange = [];
     $currentDate = strtotime($starttime);
     $endDate = strtotime($endtime);
 
     while ($currentDate <= $endDate) {
         $dateRange[] = date("Y-m-d", $currentDate);
         $currentDate = strtotime("+1 day", $currentDate);
     }
 
     // Fill missing dates with 0 if no data for that date
     foreach ($dateRange as $date) {
         if (!isset($expenseData[$date])) {
             $expenseData[$date] = 0;  // No data for this date, set to 0
         }
     }
    foreach ($expenseData as $date => $expense) {
        $expenseData[$date] = round($expense, 2);
    }
    ksort($expenseData);
} else {
    $arr["message"] = "Error in fetching expense data: " . mysqli_error($conn);
}

if (!empty($expenseData) || !empty($distanceData) || !empty($fuelData)) {
    $arr["success"] = true;
    $arr["data"] = [
        "expense" => $expenseData,
        "distance" => $distanceData,
        "fuel" => $fuelData
    ];
} else {
    $arr["message"] .= " No data found";
    $arr["success"] = false;
}

// Output the result in JSON format
echo json_encode($arr);
?>
