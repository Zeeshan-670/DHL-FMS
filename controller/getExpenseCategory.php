<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include("database.php");

$arr = [
    "message" => "",
    "success" => false,
    "data" => []
];

// Establish the database connection
$conn = dbconnection();

if ($conn) {
    // Define the query to fetch data from the expensecategory table
    $query = "SELECT * FROM expensecategory";
    $result = mysqli_query($conn, $query);
    
    if ($result) {
        // Check if any rows are returned
        if (mysqli_num_rows($result) > 0) {
            // Store the results in an array
            $arr["data"] = mysqli_fetch_all($result, MYSQLI_ASSOC);
            $arr["message"] = "Data retrieved successfully";
            $arr["success"] = true;
        } else {
            $arr["message"] = "No records found";
        }
    } else {
        // If there was an error with the query execution
        $arr["message"] = "Error executing query: " . mysqli_error($conn);
    }
} else {
    // If there is an issue with the database connection
    $arr["message"] = "Failed to connect to the database";
}

// Close the database connection
mysqli_close($conn);

// Output the JSON response
echo json_encode($arr);
?>
