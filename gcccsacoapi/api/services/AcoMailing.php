<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once(__DIR__ . '/../../PHPMailer/src/Exception.php');
require_once(__DIR__ . '/../../PHPMailer/src/PHPMailer.php');
require_once(__DIR__ . '/../../PHPMailer/src/SMTP.php');
require_once(__DIR__ . '/../../vendor/autoload.php');

class Mail{
    function sendEmail($data){
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'appgradesolutionsgcccsaco@gmail.com';
            $mail->Password = 'kajwsvjaxeugbwzk';
            $mail->Port = 465;
            $mail->SMTPSecure = 'ssl';
            $mail->isHTML(true);
            $mail->setFrom('appgradesolutionsgcccsaco@gmail.com');
            
            // Split multiple email addresses into an array
            $emails = explode(',', $data->email);
            
            foreach ($emails as $email) {
                $mail->addAddress(trim($email)); // Trim to remove any extra whitespace
            }

            $mail->Subject = $data->subject;
            $mail->Body = $data->message;
            $mail->send();
            
            http_response_code(200);
            //echo json_encode(array("message" => "Email sent successfully."));
        } catch(Exception $e){
            http_response_code(500);
            //echo json_encode(array("error" => "Error sending email: " . $e->getMessage()));
        }
    }
}