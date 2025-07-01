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

$mysqli  = dbconnection();
// Define the path to the signatures folder
$signaturesFolder = '../signatures';
$files = scandir($signaturesFolder);

// Loop through each file in the folder
foreach ($files as $file) {
    // Skip "." and ".." entries
    if ($file === '.' || $file === '..') {
        continue;
    }

    // Extract the user ID from the file name (e.g., "fullname-id.jpg")
    if (preg_match('/-(\d+)\.jpg$/', $file, $matches)) {
        $userId = $matches[1];

        // Check if a user with this ID exists in the database
        $stmt = $mysqli->prepare("SELECT id FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Insert the signature file name into the database
            $insertStmt = $mysqli->prepare("update users set signature_url = ? where id = ?");
            $insertStmt->bind_param("si", $file,$userId);
            $insertStmt->execute();
            $insertStmt->close();

            echo "Inserted signature for user ID {$userId}: {$file}\n";
        } else {
            echo "No user found for user ID {$userId}\n";
        }

        $stmt->close();
    } else {
        echo "File {$file} does not match the required pattern.\n";
    }
}

// Close the database connection
$mysqli->close();

?>