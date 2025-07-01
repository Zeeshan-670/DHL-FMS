<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include("database.php");

$connMileage = dbmileage();
if ($connMileage === false) {
    $arr['message'] = 'Mileage DB connection failed: ' . mysqli_error($connMileage);
    echo json_encode($arr);
    exit;
}

$arr = [
    "message" => "",
    "success" => false,
    "data" => []
];

// Get the POST parameter (Reg)
$reg = isset($_POST['Reg']) ? $_POST['Reg'] : null;

// Check if Reg is provided
if ($reg === null) {
    $arr["message"] = "Reg parameter is required";
    echo json_encode($arr);
    exit();
}

// Establish the database connection
$conn = dbconnection();

if ($conn) {
    // Define the SQL query with placeholders
    $query = "SELECT V.V_id,V.Reg, V.engine, V.chassis, MK.makename, MD.modelname, C.categoryname, S.stationname, cs.name,date_of_maturity
              FROM vehicles V
              LEFT JOIN MAKE MK ON MK.makeid = V.make_id
              LEFT JOIN MODEL MD ON MD.modelid = V.model_id
              LEFT JOIN CATEGORY C ON C.categoryid = V.category_id
              LEFT JOIN Station S ON S.stationid = V.station_id
              LEFT JOIN city cs ON cs.id = S.Cityid
              WHERE V.Reg = ?";

    // Prepare the statement
    if ($stmt = mysqli_prepare($conn, $query)) {
        // Bind the parameter
        mysqli_stmt_bind_param($stmt, "s", $reg);

        // Execute the statement
        if (mysqli_stmt_execute($stmt)) {
            // Store the result
            $results = mysqli_stmt_get_result($stmt);
            
            // Check if any rows were returned
            if (mysqli_num_rows($results) > 0) {
                // Fetch the data and store it in the array
                $result = mysqli_fetch_all($results, MYSQLI_ASSOC);
                $milage = getMileage($connMileage,$result[0]['Reg']);
                foreach ($result as $row) {
                    if (empty($data)) {
                        $data['vehicle_details'] = [
                            "V_id" => $row['V_id'],
                            "stationname" => $row['stationname'],
                            "makename" => $row['makename'],
                            "modelname" => $row['modelname'],
                            "Reg" => $row['Reg'],
                            "Milage" => $milage,
                            "engine" => $row['engine'],
                            "chassis" => $row['chassis'],
                            "categoryname" => $row['categoryname'],
                            "name" => $row['name'],
                            "date_of_maturity" => $row['date_of_maturity']
                        ];
                    }
                }
                $arr["data"] = $data;
                $arr["message"] = "Vehicle Found";
                $arr["success"] = true;
            } else {
                $arr["message"] = "No records found for the provided Vehicle";
            }
        } else {
            $arr["message"] = "Error executing query: " . mysqli_error($conn);
        }

        // Close the statement
        mysqli_stmt_close($stmt);
    } else {
        $arr["message"] = "Error preparing the query: " . mysqli_error($conn);
    }
} else {
    $arr["message"] = "Failed to connect to the database";
}

//getting mileage
function getMileage($conn, $reg) {
    $mileage = $conn->prepare("select plate_number,mileage from live_status where plate_number = ?");
    if (!$mileage) {
        die("Query preparation failed: " . $conn->error);     
    }
    
    $mileage->bind_param('s', $reg);
    $mileage->execute();
    $exe = $mileage->get_result();
    $results = $exe->fetch_assoc();

    if(!empty($results)){
        return $results['mileage'];         
    }else{
        return null;
    }
}

// Close the database connection
mysqli_close($conn);
echo json_encode($arr);
?>
