<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
include("database.php");

// Initialize response array
$response = [
    "message" => "",
    "success" => false,
    "data" => []
];

// Establish database connection
$conn = dbconnection();
if (!$conn) {
    $response["message"] = "Database connection failed.";
    echo json_encode($response);
    exit;
}

// SQL Query to count vehicle categories
$sql = "
    SELECT 
        SUM(CASE WHEN category_id IN (2, 5) THEN 1 ELSE 0 END) AS MB, 
        SUM(CASE WHEN category_id IN (1) THEN 1 ELSE 0 END) AS LCV,
        SUM(CASE WHEN category_id IN (3) THEN 1 ELSE 0 END) AS MCV,
        SUM(CASE WHEN category_id IN (4, 6) THEN 1 ELSE 0 END) AS SCV,
        SUM(CASE WHEN category_id IN (1,2,4,3,5,6) THEN 1 ELSE 0 END) AS Total
    FROM fms.vehicles
";


$result = mysqli_query($conn, $sql);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    $response["message"] = "Data retrieved successfully.";
    $response["success"] = true;
    $response["data"] = [
        "MB" => $row["MB"] ?? 0,
        "LCV" => $row["LCV"] ?? 0,
        "MCV" => $row["MCV"] ?? 0,
        "SCV" => $row["SCV"] ?? 0,
        "Total" => $row["Total"] ?? 0
    ];
} else {
    $response["message"] = "Failed to fetch vehicle data: " . mysqli_error($conn);
    echo json_encode($response);
    exit;
}

// Query for driver count
// $sql = "SELECT count(*) as drivercount FROM fms.driver";


$sql = "
SELECT 
        SUM(CASE WHEN Dept IN ('GOPS') THEN 1 ELSE 0 END) AS GOPS, 
        SUM(CASE WHEN Dept IN ('EXP','GTW','IMP') THEN 1 ELSE 0 END) AS GTW,
        SUM(CASE WHEN Dept IN ('EXP','GTW','IMP','GOPS') THEN 1 ELSE 0 END) AS Total
    FROM fms.drivers";
$result = mysqli_query($conn, $sql);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    // $response["data"]['drivercount'] = $row["drivercount"] ?? 0;
    $response["data"]['drivercount'] = [
        "GOPS" => $row["GOPS"] ?? 0,
        "GTW" => $row["GTW"] ?? 0,
        "Total" => $row["Total"] ?? 0
    ];
} else {
    $response["data"]['drivercount'] = "Error fetching driver count: " . mysqli_error($conn);
}

// Query for vendor count
$sql = "SELECT count(*) as vendorcount FROM fms.vendor";
$result = mysqli_query($conn, $sql);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $response["data"]['vendorcount'] = $row["vendorcount"] ?? 0;
} else {
    $response["data"]['vendorcount'] = "Error fetching vendor count: " . mysqli_error($conn);
}

// Close the database connection
mysqli_close($conn);

// Return JSON response
echo json_encode($response);
?>
