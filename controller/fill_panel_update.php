<?php 
session_start();


$vehicle_name = $_GET['vehicle'];
$jession = $_SESSION['jsession'];
$status=array();
$stats_list=array();
$nr=0;
$moving=0;
$ign=0;
$parked=0;
$offline=0;
$invalid = 0;
$default = 0;

$dev_id = $_SESSION['vehicle'][$vehicle_name]['dl'][0]['id'];
$data = json_decode(file_get_contents("http://192.168.20.168/StandardApiAction_getDeviceStatus.action?jsession=".$jession."&devIdno=".$dev_id."&toMap=1&driver=0&language=zh",false),true);
$gps_upload = $data['status'][0]['gt'];




// $gps_time = date_format($data['status'][0]['gt'], '%Y-%m-%d %H:%M:%S');

// echo $gps_time;

$speed =$data['status'][0]['sp'];

if($speed!=0){
    $speed  = $speed/10;
}

$status1 = sprintf('%032b',  $data['status'][0]['s1']); 


$status1 = strrev($status1);

$status1 = str_split($status1);


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
    $nr+=1;
}
else if($status1[0]=="0" && $hour<24){
    $car_icon="pink";
    $invalid+=1;
}
else if($status1[0]=="1" && $min>15 && $status1[1]=="0" && $speed<5){
    $car_icon="black";
    $offline+=1;
}
else if($status1[0]=="1" && $hour<24 && $status1[1]=="1" && $speed<5){
    $car_icon="blue";
    $ign+=1;
}
else if($status1[0]=="1" && $hour<24 && $status1[1]=="0" && $speed<5){
    $car_icon="yellow";
    $parked+=1;
}
else if($status1[0]=="1" && $hour<24 && $status1[1]=="1" && $speed>=5){
    $car_icon="green";
    $moving+=1;
}
else{
    $car_icon="white";
    $default+=1;
}



$stats_list["icon"]=$car_icon;

print_r($stats_list[$vehicle]);







ob_start();
header('Content-type: application/json');
echo json_encode($status);
header("Connection: close");
header("Content-length: " . (string)ob_get_length());
ob_end_flush();
die;




// for($i=0;i< count($_SESSION['vehicle']);$i++){
//     echo $_SESSION['vehicle']
// }












?>