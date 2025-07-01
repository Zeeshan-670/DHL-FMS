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
   
    $query = $conn->prepare("SELECT date_of_maturity,Reg,chassis,ENGINE,name as station FROM vehicles 
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
                $mergedData['Vehicle Maturity']['data'][] = [
                    'Date of Maturity' => $row['date_of_maturity'],
                    'messages' => "Vehicle {$row['Reg']}, Station {$row['station']} alert for the date of maturity expired on {$row['date_of_maturity']}",
                    'vehiclealert' => 'Vehicle Maturity'
                ];
            }
            
        }
    }


    $query = $conn->prepare("SELECT j.`jobTitle`,date_of_maturity, r1.STATUS, r1.DATE, DATEDIFF(CURDATE(), r1.DATE) AS days,reg,s.name,u.name AS createdby
FROM rfqupdatehistory r1
JOIN job j ON j.id = r1.rfq_id
JOIN vehicles v ON j.`V_id` = v.V_id
JOIN stationdata s ON s.`id` = v.`station_id`
JOIN users u ON u.`id` = j.creationby

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
ORDER BY r1.rfq_id;
                ");
    $query->execute();
    $exe = $query->get_result();
    $result = $exe->fetch_all(MYSQLI_ASSOC);

    if ($result === false) {
        $arr["message"] = "Error: " . mysqli_error($conn);
        $arr["success"] = false;
    } else {
        $value = '';

        if (count($result) > 0) {
            foreach ($result as &$row) {
                                
                // switch ($row['STATUS']) {
                //     case 'Pending':
                //         $value = 'Pending';
                //         break;
                //     case 'submit quotation':
                //     case 'approve':
                //         $value = 'Quotation Received';
                //         break;
                //     case 'isApproved':
                //         $value = 'Forwarded for Further Approval';
                //         break;
                //     case 'deactivate':
                //         $value = 'Job Deactivated';
                //         break;
                //     case 'approve by manager':
                //         $value = 'Job in progress';
                //         break;
                //     case 'job satisfaction requested':
                //         $value = 'Job Satisfaction Requested';
                //         break;
                //     case 'job satisfaction approved':
                //         $value = 'Invoice Pending';
                //         break;
                //     case 'invoice recieved':
                //         $value = 'Invoice Received';
                //         break;
                //     case 'Invoice reject':
                //         $value = 'Invoice Rejected';
                //         break;
                //     case 'Invoice approve':
                //         $value = 'Record Expense & Forward';
                //         break;
                //     case 'Completed':
                //         $value = 'Job Completed';
                //         break;
                //     case 'return':
                //         $value = 'Returned';
                //         break;
                //     default:
                //         $value = 'Unknown Status';
                //         break;
                // }
                // if($row['STATUS'] == 'invoice recieved'){
                //     $mergedData['Invoice']['data'][] = [
                //         'Date of Maturity' => $row['date_of_maturity'],
                //         'messages' => "<div class='mt-3'><b>Job Title:</b> {$row['jobTitle']},</div>
                //                     <div><b>Created by:</b> {$row['createdby']},&nbsp;&nbsp; <b>Status:</b> {$value},</div> 
                //                     <div><b>Station:</b> {$row['name']},&nbsp;&nbsp; <b>Vehilce:</b> {$row['reg']},&nbsp;&nbsp; <b>Days:</b> {$row['days']}</div>
                //                     <a href='rfqHistory.php' type='button' target='_blank' class='btn btn-primary mt-3'>Go to Job History</a>",
                //         'vehiclealert' => 'WorkOrder'
                //     ];
                // }
                $mergedData['WorkOrder']['data'][] = [
                    'Date of Maturity' => $row['date_of_maturity'],
                    'messages' => "<div class='mt-3'><b>Job Title:</b> {$row['jobTitle']},</div>
                                <div><b>Created by:</b> {$row['createdby']},&nbsp;&nbsp; <b>Status:</b> {$row['STATUS']},</div> 
                                <div><b>Station:</b> {$row['name']},&nbsp;&nbsp; <b>Vehilce:</b> {$row['reg']},&nbsp;&nbsp; <b>Days:</b> {$row['days']}</div>
                                <a href='rfqHistory.php' type='button' target='_blank' class='btn btn-primary mt-3'>Go to Job History</a>",
                    'vehiclealert' => 'WorkOrder'
                ];
            }
        }
    }

    
    $query = $conn->prepare("
SELECT j.`jobTitle`, date_of_maturity, r1.STATUS, r1.DATE,invoice_url ,
       DATEDIFF(CURDATE(), r1.DATE) AS days, reg, s.name, 
       u.name AS createdby
FROM rfqupdatehistory r1
JOIN job j ON j.id = r1.rfq_id
JOIN vehicles v ON j.`V_id` = v.V_id
JOIN stationdata s ON s.`id` = v.`station_id`
JOIN users u ON u.`id` = j.creationby
join invoice i on i.rfq_id = j.id
WHERE r1.STATUS = 'invoice recieved'
AND r1.id = (
    SELECT MAX(r2.id)
    FROM rfqupdatehistory r2
    WHERE r2.rfq_id = r1.rfq_id
)
ORDER BY r1.rfq_id;");
    $query->execute();
    $exe = $query->get_result();
    $result = $exe->fetch_all(MYSQLI_ASSOC);

    if ($result === false) {
        $arr["message"] = "Error: " . mysqli_error($conn);
        $arr["success"] = false;
    } else {
        $value = '';

        if (count($result) > 0) {
            foreach ($result as &$row) {
                  
                $mergedData['Invoice']['data'][] = [
                    'Date of Maturity' => $row['date_of_maturity'],
                    'messages' => "<div class='mt-3'><b>Job Title:</b> {$row['jobTitle']},</div>
                                <div><b>Created by:</b> {$row['createdby']},&nbsp;&nbsp; <b>Status:</b> {$row['STATUS']},</div> 
                                <div><b>Invoice:</b> {$row['invoice_url']},</div>
                                <div><b>Station:</b> {$row['name']},&nbsp;&nbsp; <b>Vehilce:</b> {$row['reg']},&nbsp;&nbsp; <b>Days:</b> {$row['days']}</div>
                                <a href='rfqHistory.php' type='button' target='_blank' class='btn btn-primary mt-3'>Go to Job History</a>",
                    'vehiclealert' => 'WorkOrder'
                ];
            
            }
        }
    }
      
    $query = $conn->prepare(" SELECT  Reg, enddate,date_of_maturity FROM downtime d
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
                $mergedData['Downtime']['data'][] = [
                    'Date of Maturity' => $row['date_of_maturity'],
                    'messages' => "Downtime {$row['Reg']} alert for end date expired on {$row['enddate']}",
                    'vehiclealert' => 'Downtime'
                ];
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