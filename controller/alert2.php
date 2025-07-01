<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json"); 
include("database.php");


$arr = [
    "message" => "", 
    "success" => false,  
    "data" => []  
];

$conn = dbconnection();

if ($conn === false) {
    $arr['message'] = 'DB connecttion failed';
    $arr['success'] = false;
    $arr['error'] = sqlsrv_errors();
    echo json_encode($arr);
    exit;
}else{

    
    if(isset($_POST['type'])){
        $type = $_POST['type'];
        if($type == "view"){
            view($conn, $arr);
        }else{
            $arr['message'] = "Invalid type";
        }
    }else{
        $arr['message'] = "Type required";
    }
}

function view($conn, &$arr) {
    // Initialize an empty array to hold the merged data
    $mergedData = array();

    $query = $conn->prepare("Select alertcategory,alerticon from alertcategory");
    $query->execute();
    $exe = $query->get_result();
    $result = $exe->fetch_all(MYSQLI_ASSOC);

    if ($result === false) {
    $arr["message"] = "Error: " . mysqli_error($conn);
    $arr["success"] = false;
    } else {
    if (count($result) > 0) {
        // Add a title "Driver License" for each item in this query
        foreach ($result as &$row) {

            if (!isset($mergedData[$row['alertcategory']])) {
                $mergedData[$row['alertcategory']]['icon'] = $row['alerticon'];
                $mergedData[$row['alertcategory']]['data'] = array();
            } 
        }
        }
    }

    // Query for driverlicensealert
    $query = $conn->prepare("SELECT entrydate, messages FROM driverlicensealert AS da 
                             JOIN driver d ON d.d_id = da.driverid 
                             WHERE da.entrydate >= CURDATE() AND da.entrydate < CURDATE() + INTERVAL 1 DAY

                             ORDER BY da.entrydate DESC");
    $query->execute();
    $exe = $query->get_result();
    $result = $exe->fetch_all(MYSQLI_ASSOC);

    if ($result === false) {
        $arr["message"] = "Error: " . mysqli_error($conn);
        $arr["success"] = false;
    } else {
        if (count($result) > 0) {
            // Add a title "Driver License" for each item in this query
            foreach ($result as &$row) {
                
                // if (!isset($mergedData['License Expiration'])) {
                //     $mergedData['License Expiration'] = array();
                // }
    
                // Push the row to the array under the 'title' key
                array_push($mergedData['License Expiration']['data'], $row);
            }
            // Merge the data from driverlicensealert into $mergedData
            
            // $mergedData = array_merge($mergedData, $result);
        }
    }

    // Query for vehiclemaintenancealert
    $query = $conn->prepare("SELECT entrydate, messages , vehiclealert FROM vehiclealert 
                             WHERE entrydate >= CURDATE() AND entrydate < CURDATE() + INTERVAL 1 DAY

                             ORDER BY entrydate DESC");
    $query->execute();
    $exe = $query->get_result();
    $result = $exe->fetch_all(MYSQLI_ASSOC);

    if ($result === false) {
        $arr["message"] = "Error: " . mysqli_error($conn);
        $arr["success"] = false;
    } else {
        if (count($result) > 0) {
            // Add a title "Vehicle Maintenance" for each item in this query
            foreach ($result as &$row) {
              
             
                // if (!isset($mergedData[ $row['vehiclealert']])) {
                //     $mergedData[ $row['vehiclealert']] = array();
                // }
    
                // Push the row to the array under the 'title' key
                array_push($mergedData[ $row['vehiclealert']]['data'], $row);
              
            }
            // Merge the data from vehiclemaintenancealert into $mergedData
            // $mergedData = array_merge($mergedData, $result);
        }
    }



    $query = $conn->prepare("SELECT date_of_maturity,Reg,chassis,ENGINE,station_id FROM vehicles 
                JOIN stationdata s ON s.`id` = station_id
                WHERE DATEDIFF(date_of_maturity, CURDATE()) <= 45
                ORDER BY date_of_maturity DESC;
                ");
    $query->execute();
    $exe = $query->get_result();
    $result = $exe->fetch_all(MYSQLI_ASSOC);

    if ($result === false) {
        $arr["message"] = "Error: " . mysqli_error($conn);
        $arr["success"] = false;
    } else {
        if (count($result) > 0) {
            foreach ($result as &$row) {
                array_push($mergedData['Vehicle Maturity']['data'], $row);                
            }
        }
    }


    $query = $conn->prepare("SELECT j.`jobTitle`, r1.STATUS, r1.DATE, DATEDIFF(CURDATE(), r1.DATE) AS days
FROM rfqupdatehistory r1
JOIN job j ON j.id = r1.rfq_id
WHERE r1.rfq_id NOT IN (
    SELECT DISTINCT rfq_id
    FROM rfqupdatehistory
    WHERE STATUS = 'Completed'
)
AND r1.id = (
    SELECT MAX(r2.id)
    FROM rfqupdatehistory r2
    WHERE r2.rfq_id = r1.rfq_id
)
AND DATEDIFF(CURDATE(), r1.DATE) > 1
ORDER BY r1.rfq_id;
                ");
    $query->execute();
    $exe = $query->get_result();
    $result = $exe->fetch_all(MYSQLI_ASSOC);

    if ($result === false) {
        $arr["message"] = "Error: " . mysqli_error($conn);
        $arr["success"] = false;
    } else {
        if (count($result) > 0) {
            foreach ($result as &$row) {
                array_push($mergedData['WorkOrder']['data'], $row);                
            }
        }
    }

    
    $query = $conn->prepare(" SELECT  Reg FROM downtime d
JOIN vehicles v ON v.`V_id` = d.`V_id`
WHERE  d.enddate < CURDATE();");
    $query->execute();
    $exe = $query->get_result();
    $result = $exe->fetch_all(MYSQLI_ASSOC);

    if ($result === false) {
        $arr["message"] = "Error: " . mysqli_error($conn);
        $arr["success"] = false;
    } else {
        if (count($result) > 0) {
            foreach ($result as &$row) {
                array_push($mergedData['Downtime']['data'], $row);                
            }
        }
    }
   


    // Sort the merged data by entrydate in ascending order
    // usort($mergedData, function($a, $b) {
    //     return strtotime($a['entrydate']) - strtotime($b['entrydate']);
    // });



    // Final response
    if (!empty($mergedData)) {
        $arr["success"] = true;
        $arr["data"] = $mergedData;
    } else {
        $arr["success"] = false;
        $arr["message"] = "No data found";
    }
}

echo json_encode($arr);


?>