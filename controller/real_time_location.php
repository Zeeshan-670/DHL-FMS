<?php 


session_start();





function gsm_strength($value){
    if ($value == 0){
        return "Non Existence";
    }
    else if($value == 1){
        return "No Signal";
    }
    else if($value == 2){
        return "Weak Signal";
    }
    else if($value == 3){
        return "Normal Signal";
    }
    else if($value == 3){
        return "Strong Signal";
    }
    else if($value == 3){
        return "Extremely Signal";
    }
    else{
        return "NA";
    }
}

function hdd_state($value){
    if ($value == 0){
        return "Not present";
    }
    else if($value == 1){
        return "present";
    }
    else{
        return "N/A";
    }   
}










$vehicle_name = $_GET['vehicle'];
$jession = $_SESSION['jsession'];

if (array_key_exists($vehicle_name,$_SESSION['vehicle'])){


    $dev_id = $_SESSION['vehicle'][$vehicle_name]['dl'][0]['id'];

    $data = json_decode(file_get_contents("http://192.168.20.168/StandardApiAction_getDeviceStatus.action?jsession=".$jession."&devIdno=".$dev_id."&toMap=1&driver=0&language=zh",false),true);

    $driver_id = $data['status'][0]['driJn'];

    $speed =$data['status'][0]['sp'];
    $lat =$data['status'][0]['mlat'];
    $lng =$data['status'][0]['mlng'];
    $channel =  $_SESSION['vehicle'][$vehicle_name]['dl'][0]['cn'];

    if($speed!=0){
        $speed  = $speed/10;
    }


    $fuel = $data['status'][0]['yl'];

    if($fuel!=0){
        $fuel  = $fuel/100;
    }

    $status1 = sprintf('%032b',  $data['status'][0]['s1']); 


    $status1 = strrev($status1);

    $status1 = str_split($status1);


    //  Online / Offline

    $online_status = $data['status'][0]['ol'];

    if($online_status==1){
        $online_status="1";
    }
    else{
        $online_status="0";
    }

    $gps="";
    if($status1[0]=="1"){
        $gps="valid";
    }
    else{
        $gps="Invalid";
    }


    $ign="";
    if($status1[1]=="1"){
        $ign="ON";
    }
    else{
        $ign="OFF";
    }

    $ign="";
    if($status1[1]=="1"){
        $ign="ON";
    }
    else{
        $ign="OFF";
    }



    $seat_belt = $status1[22];



    if($seat_belt=="1" && $ign=="ON"){
        $seat_belt="Yes";
    }
    else if($seat_belt=="0" && $ign=="ON"){
        $seat_belt="No";
    }
    if($seat_belt=="1" && $ign=="OFF"){
        $seat_belt="No";
    }
    else if($seat_belt=="0" && $ign=="OFF"){
        $seat_belt="No";
    }

    $gsm =  $status1[12].$status1[11].$status1[10];
    $value =(int)bindec($gsm);

    $gsm = gsm_strength($value);

    $harddisk =  $status1[9].$status1[8];
    $value =(int)bindec($harddisk);



    $harddisk = hdd_state($value);

    $gps_upload = $data['status'][0]['gt'];

    // $gps_time = date_format($data['status'][0]['gt'], '%Y-%m-%d %H:%M:%S');

    // echo $gps_time;
    date_default_timezone_set('Asia/Karachi');
    $currentdate = date('Y-m-d H:i:s');

    $timestamp1 = strtotime($currentdate);
    $timestamp2 = strtotime($gps_upload);

    $hour = abs($timestamp2 - $timestamp1)/(60*60);

    $hour = (int)$hour;

    $min = abs($timestamp2 - $timestamp1)/60;

    $min = (int)$min;
    $car_icon ="";

    if($hour>=24){
        $car_icon="red";
    }
    else if($status1[0]=="0" && $hour<24){
        $car_icon="pink";
    }
    else if($status1[0]=="1" && $min>15 && $status1[1]=="0" && $speed<5){
        $car_icon="black";
    }
    else if($status1[0]=="1" && $hour<24 && $status1[1]=="1" && $speed<5){
        $car_icon="blue";
    }
    else if($status1[0]=="1" && $hour<24 && $status1[1]=="0" && $speed<5){
        $car_icon="yellow";
    }
    else if($status1[0]=="1" && $hour<24 && $status1[1]=="1" && $speed>=5){
        $car_icon="green";
    }
    else{
        $car_icon="white";
    }

    // echo $car_icon;
    $loc =file_get_contents("http://192.168.20.251/near_by.php?lat=".$lat."&lon=".$lng."",false);


    // echo $loc;
    $date = explode(" ",$currentdate);

    $mileage = json_decode(file_get_contents("http://192.168.20.168:8080/mdv2_report/mileages_service.php?plate_number=".$vehicle_name."&date=".$date[0]."",false),true);

    // print_r($mileage);

    $latest_mileage = $mileage[0];
    if($fuel==0){
        $fuel =(String)$mileage[1];
    }


    $json_string = "Name;" .$vehicle_name. "|Speed;" .(String)$speed." Km/h|ACC;" .$ign."|Online;" .$online_status. "|Today Mileage;".$latest_mileage." Km|Seatbelt;" .$seat_belt. "|Fuel Level;" .$fuel."|GPS Time;" .$gps_upload. "|Coordinates;" .$lat.",".$lng."|GSM;" .$gsm."|GPS;" .$gps."|Hard Disk;".$harddisk."|Channels;".$channel."|Location;".$loc."|Driver ID;".$driver_id."";

    // echo $json_string;

    $stats=array();
    $stats["Json String"] = $json_string;
    $stats["lat"] = $lat;
    $stats["lng"] = $lng;
    $stats["icon"] = $car_icon;
    $stats["Result"] = "Success";

    ob_start();
    header('Content-type: application/json');
    echo json_encode($stats);
    header("Connection: close");
    header("Content-length: " . (string)ob_get_length());
    ob_end_flush();
    die;
}
else{
    $stats=array();
    $stats["Result"] = "No Vehicle Found";
    ob_start();
    header('Content-type: application/json');
    echo json_encode($stats);
    header("Connection: close");
    header("Content-length: " . (string)ob_get_length());
    ob_end_flush();
    die;

}
// echo "http://192.168.20.168:8080/mdv2_report/mileages_service.php?plate_number=".$vehicle_name."&date=".$date[0]."";

?>