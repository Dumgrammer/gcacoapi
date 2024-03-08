<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once(__DIR__ . '/../config/acodatabase.php');
require_once(__DIR__ . 'FormHandler.php');


$rawData = file_get_contents("php://input");
$data = json_decode($rawData);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(array("message" => "JSON decoding error: " . json_last_error_msg()));
    exit;
}

$operation = $data->operation;
try {
    $pdo = new PDO("mysql:host=localhost;dbname=gcccs_aco", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(array("message" => "Database connection error: " . $e->getMessage()));
    exit;
}

if ($operation === 'submit_form') {
    $response = $formHandler->submitFormData($data);
}else if
     ($operation === 'get_formdata'){
        $response = $formHandler->getFormData($data);
    
}
else if 
    ($operation === 'update_form'){
        $response = $formHandler->updateFormData($data);
    }
else if 
    ($operation === 'delete_form'){
        $response = $formHandler->deleteFormData($data);
    }
 
 else {
    http_response_code(400);
    echo json_encode(array("message" => "Invalid"));
    exit;
}

http_response_code($response['status']);
echo json_encode($response);

?>