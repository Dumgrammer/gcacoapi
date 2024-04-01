<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

require_once(__DIR__ . '/../../PHPMailer/src/Exception.php');
require_once(__DIR__ . '/../../PHPMailer/src/PHPMailer.php');
require_once(__DIR__ . '/../../PHPMailer/src/SMTP.php');
require_once(__DIR__ . '/../../vendor/autoload.php');

$dotenv = Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();

class Mail{
    function sendEmail($data){
        try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = $_ENV['SMTP_HOST'];
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['SMTP_USERNAME'];
        $mail->Password = $_ENV['SMTP_PASSWORD'];
        $mail->Port = $_ENV['SMTP_PORT'];
        $mail->SMTPSecure = $_ENV['SMTP_SECURE'];
        $mail->isHTML(true);
        $mail->setFrom($_ENV['SMTP_USERNAME']);

            $emails = explode(',', $data->email);

            foreach($emails as $email){
                $mail->addAddress(trim($email));
            }
    
    $mail->Subject = $data->subject;
    $mail->Body = $data->message;
    $mail->send();

    http_response_code(200);
} catch(Exception $e){
    http_response_code(500);
    //echo json_encode(array("error" => "Error sending email: " . $e->getMessage()));
        }
    }
}
?>