<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once(__DIR__ . '/../../PHPMailer/src/Exception.php');
require_once(__DIR__ . '/../../PHPMailer/src/PHPMailer.php');
require_once(__DIR__ . '/../../PHPMailer/src/SMTP.php');

class Mailing {
    public function sendEmail($name, $email, $subject, $message) {
        
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: POST");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
        header("Content-Type: application/json; charset=UTF-8");

        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'bastianlacap55@gmail.com';
            $mail->Password = 'kajw svja xeug bwzk';
            $mail->Port = 465;
            $mail->SMTPSecure = 'ssl';
            $mail->isHTML(true);
            $mail->setFrom($email, $name);
            $mail->addAddress('bastianlacap55@gmail.com');
            $mail->Subject = "$email ($subject)";
            $mail->Body = $message;
            $mail->send();

            http_response_code(200);
            echo json_encode(array("message" => "Email sent successfully."));
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(array("message" => "Error sending email: " . $mail->ErrorInfo));
        }
    }
}
