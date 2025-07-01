<?php 

session_start();



try{


    $jession = $_SESSION['jsession'];
    $data = json_decode(file_get_contents("http://192.168.20.168/StandardApiAction_queryUserVehicle.action?jsession=".$jession."&language=zh",false),true);
    
    
    if($data['result']==0){

        $stats_data=array();

        $stats=array();
    
        foreach ($data['vehicles'] as $vehicle)
	    {

            $temp=array('pid'=>0,'dl'=>array());
            $temp['pid']  = $vehicle['pid'];
            $temp['dl']  = $vehicle['dl'];
            $stats[$vehicle['nm']] = $temp;


        }

        $_SESSION['vehicle'] = $stats;
        $stats_data['vehicle'] = $stats;


        $stats=array();
    
        foreach ($data['companys'] as $company)
	    {

            $temp=array();
            $temp  = $company;
            $stats[$company['id']] = $temp;


        }

        $_SESSION['company'] = $stats;
        $stats_data['company'] = $stats;



        ob_start();
            header('Content-type: application/json');
            echo json_encode($stats_data);
            header("Connection: close");
            header("Content-length: " . (string)ob_get_length());
            ob_end_flush();
		die;


    }
    else{
        echo $data['message'];
    }
        
}catch (Exception $e) {
    echo "Error";
}

   




















?>