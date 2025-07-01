<?php 


ob_start();
session_start();



$type=$_GET['type'];


if($type=="login"){

    $username=$_GET['username'];
    $pass=$_GET['password'];

    $data = json_decode(file_get_contents("http://192.168.20.168/StandardApiAction_login.action?account=".$username."&password=".$pass,false),true);

   

    if($data['result']==0){
        $_SESSION['valid'] = true;
	    $_SESSION['timeout'] = time();
        $_SESSION['username'] = $username;
        $_SESSION['password'] = $pass;
        $_SESSION['jsession'] = $data['jsession'];

        echo "Ok";
    }
    else{
        echo $data['message'];
    }


}
else{
    $jession = $_SESSION['jsession'];
    $data = json_decode(file_get_contents("http://192.168.20.168/StandardApiAction_logout.action?jsession=".$jession."",false),true);
   
    if($data['result']==0){
        unset($_SESSION["username"]);
        unset($_SESSION["password"]);
        unset($_SESSION["jsession"]);
    
        echo "Ok";
    }
    else{
        echo $data['message'];
    }



}





















?>