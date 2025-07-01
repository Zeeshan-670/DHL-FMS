<?php
require_once __DIR__ . '/../pdf/vendor/autoload.php';
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json"); 
require_once '../pdf/assets/lib/PHPMailer.php';
require_once '../pdf/assets/lib/SMTP.php';
require_once '../pdf/assets/lib/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function email($conn,$arr, $data){
    $templatePath = $data['template'];
    if (!file_exists($templatePath)) {
        die("Email template not found.");
    }

    $templateContent = file_get_contents($templatePath);

    foreach ($data['placeholders'] as $key => $value) {
        $templateContent = str_replace('{{' . $key . '}}', $value, $templateContent);
    }

    $to = $data['to'];
    $subject = $data['subject'];
    $body = $templateContent;
    $altBody = $data['altBody'];

   
    $mail = new PHPMailer(true); 

    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host = '192.168.20.204';
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        $mail->Username = "broadcast@itecknologi.com";
        $mail->Password = "Karachi@123";
        $mail->setFrom('broadcast@itecknologi.com', 'iTecknologi');
        $mail->SMTPOptions = array('ssl' => array('verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' =>
            true));

        $mail->addAddress($to);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AltBody = $altBody;
        $mail->isHTML(true);
        if ($mail->send()) {
            $arr['message'] = 'Email sent successfully.';
            $arr['success'] = true;
            return true; 
        } else {
            $arr['message'] = 'Email not sent.';
            $arr['success'] = false;
            return false; 
        }
    } catch (Exception $e) {
        $arr['message'] = 'Error sending email: ' . $e->getMessage();
        $arr['success'] = false;
        return false; 
    }
}
?>