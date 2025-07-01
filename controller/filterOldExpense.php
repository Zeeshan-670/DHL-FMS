<?php
include("database.php");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json"); 

$conn = dbconnection();

$arr = [
    'message' => "",
    'success' => false
];

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}else{
    if(isset($_POST['filter'])){
        filter($conn,$arr);
    }else{
        $arr['message'] = "Filter is required";
    }
}

function filter($conn,&$arr){
    $filter = $_POST['filter'];
    $reg = isset($_POST['reg']) && $_POST['reg'] == "Select All" ?  null : $_POST['reg'];
    $start = $_POST['start'];
    $end = $_POST['end'];
    
    if($filter == 'old'){
        $query = $conn->prepare("CALL filterOldExpense(?,?,?)");
    }else if($filter == 'new' || $filter == 'all'){
        $query = $conn->prepare("CALL filterNewExpense(?,?,?)");
    }else{
        $arr['message'] = "Invalid filter";
        exit;
    }
    $query->bind_param('sss',$reg,$start,$end);
    
    // if($filter == 'all'){
    //     $query = $conn->prepare("");
    // }
    $query->execute();
    $exe = $query->get_result();
    $result = $exe->fetch_all(MYSQLI_ASSOC);
    

    if($result === false){
        $arr['message'] = "Error: " . mysqli_error($conn);
        $arr['success'] = false;
    }else{
        if(!empty($result)){
            $arr['message'] = "Data found successfully";
            $arr['success'] = true;
            
            if($filter == 'old'){
                $arr['data'] = $result;
            }else if($filter == 'new' || $filter == 'all'){
                foreach ($result as $row) {
                    $rfq_id = $row['rfq_id']; 

                    if (!isset($arr['data'][$rfq_id])) {
                        $arr['data'][$rfq_id] = [
                            'Reg' => $row['reg'],
                            'GrandTotal' => $row['GrandTotal'],
                            'CreationDate' => $row['creationdate'],
                            'StationName' => $row['stationname'],
                            'vendorname' => $row['vendorname'],
                            'details' => [] 
                        ];
                    }

                    $arr['data'][$rfq_id]['details'][] = [
                        'Description' => $row['Description'],
                        'Amount' => $row['Amount'],
                        'CreatedBy' => $row['CreatedBy'],
                        'ExpenseCategory' => $row['expense'],
                        'salesTax' => $row['salesTax'], 
                        'salesTaxValue' => $row['salesTaxValue'], 
                    ];
                }
            }            
        }else{
            $arr['message'] = "No data";
            $arr["success"] = false;
        }
    }
}

echo json_encode($arr);
?>