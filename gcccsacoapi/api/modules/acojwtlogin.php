<?php 

declare(strict_types=1);

use Firebase\JWT\JWT;

require_once(__DIR__ . '/../config/acodatabase.php');
require_once(__DIR__ . '/../config/secretKey.php');
require_once(__DIR__ . '/../../vendor/autoload.php');

header("Access-Control-Allow-Origin: *"); //eto allows the client to use our api asterisk is for all can be specific port tho
header("Access-Control-Max-Age: 3600"); //eto kung gano katagal mai store sa cache
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8"); //eto para sa para translate yung content into JSON encoded format
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With"); //mga pwede gamitin during request

$email = '';
$password = '';

$databaseService = new DatabaseAccess(); //we call the class in the database as an objects
$conn = $databaseService->connect(); //so in that object we call the functions basta ganun yun
$keys =  new Secret();
$keystring = $keys->generateSecretKey();

$rawData = file_get_contents("php://input");
$data = json_decode($rawData);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(array("message" => "JSON decoding error: " . json_last_error_msg()));
    exit;
}

$email = $data->email;
$password = $data->password;

$tableName = 'gcfaculty';

$sqlQuery = "SELECT fac_id, fac_fname, fac_lname, password FROM " .$tableName. " WHERE email = :email"; //taga sigurado na unique to sa table and specifically for 1 record only

$stmt = $conn->prepare($sqlQuery);
$stmt->bindParam(':email',$email);
$stmt->execute();
$num = $stmt->rowCount();


if ($num > 0) {
    // taga kuha ng data
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $id = $row['fac_id'];
    $firstname = $row['fac_fname'];
    $lastname = $row['fac_lname'];
    $passstring = $row['password'];

    // taga verify ng password
    if (password_verify($password, $passstring)) {
        // JWT token creation
        $secretKey = $keystring;
        $issuer_claim = "localhost";
        $audience_claim = "user";
        $issuedat = time();
        $earlierthan = $issuedat + 10;
        $expiredat = $issuedat + 60;

        $token = array(
            "iss" => $issuer_claim,
            "aud" => $audience_claim,
            "iat" => $issuedat,
            "nbf" => $earlierthan,
            "exp" => $expiredat,
            "data" => array(
                "id" => $id,
                "firstname" => $firstname,
                "lastname" => $lastname,
                "email" => $email
            ));

        
        http_response_code(200);
        $jwt = JWT::encode($token, $secretKey, 'HS512');
        echo json_encode(
            array(
                "message" => "Successfully Login!",
                "jwt" => $jwt,
                "email" => $email,
                "expireAt" => $expiredat
            ));
    } else {
        
        http_response_code(401);
        echo json_encode(array(
            "message" => "Login failed",
            "password" => $password,
            "storedPassword" => $passstring
        ));
    }
} else {
    
    http_response_code(401);
    echo json_encode(array(
        "message" => "User not found",
        "email" => $email
    ));
}

?>