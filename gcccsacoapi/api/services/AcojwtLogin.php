<?php

declare(strict_types=1);

use Firebase\JWT\JWT;



require_once(__DIR__ . '/../config/AcoDatabase.php');
require_once(__DIR__ . '/../config/secretKey.php');
require_once(__DIR__ . '/../../vendor/autoload.php');


class Login {
    private $conn;
    private $secretKey;

    public function __construct() {
        $databaseService = new DatabaseAccess();
        $this->conn = $databaseService->connect();

        $keys = new Secret();
        $this->secretKey = $keys->generateSecretKey();
    }

    public function loginUser() {

        $email = '';
        $password = '';

        $rawData = file_get_contents("php://input");
        $data = json_decode($rawData);

        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode(array("message" => "JSON decoding error: " . json_last_error_msg()));
            exit;
        }

        $email = $data->admin_email;
        $password = $data->password;

        $tableName = 'gc_admin';

        $sqlQuery = "SELECT admin_id, faculty_lastname, faculty_firstname, password FROM $tableName WHERE admin_email = :email";

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $num = $stmt->rowCount();

        if ($num > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $id = $row['admin_id'];
            $firstname = $row['faculty_firstname'];
            $lastname = $row['faculty_lastname'];
            $storedPassword = $row['password'];

            if (password_verify($password, $storedPassword)) {
                $token = $this->generateToken($id, $firstname, $lastname, $email);
                http_response_code(200);
                echo json_encode(
                    array(
                        "message" => "Successfully Login!",
                        "jwt" => $token,
                        "email" => $email,
                        "expireAt" => $this->getExpirationTime()
                    )
                );
            } else {
                http_response_code(401);
                echo json_encode(
                    array(
                        "message" => "Login failed",
                        "password" => $password,
                        "storedPassword" => $storedPassword
                    )
                );
            }
        } else {
            http_response_code(401);
            echo json_encode(
                array(
                    "message" => "User not found",
                    "email" => $email
                )
            );
        }
    }

    private function generateToken($id, $firstname, $lastname, $email) {
        $secretKey = $this->secretKey;
        $issuerClaim = "localhost";
        $audienceClaim = "user";
        $issuedAt = time();
        $notBefore = $issuedAt + 10;
        $expiredAt = $issuedAt + 60;

        $token = array(
            "iss" => $issuerClaim,
            "aud" => $audienceClaim,
            "iat" => $issuedAt,
            "nbf" => $notBefore,
            "exp" => $expiredAt,
            "data" => array(
                "id" => $id,
                "firstname" => $firstname,
                "lastname" => $lastname,
                "email" => $email
            )
        );

        $jwt = JWT::encode($token, $secretKey, 'HS512');

        setcookie("jwt", $jwt, [

        'expires' => $expiredAt,
        'path' =>  "/",
        'domain' => "",
        'secure' => false,
        'httponly' => true,
        'samesite' => 'None'
    ]);
        
    $response = array(
        "message" => "Successfully Login!",
        "jwt" => $jwt,
        "email" => $email,
        "expireAt" => $this->getExpirationTime(),
        "redirect" => "/statistics"
    );
    http_response_code(200);
    echo json_encode($response);
    }

    private function getExpirationTime() {
        return time() + 60;
    }

    public function logoutUser() {
        // Unset or clear the JWT cookie
        setcookie("jwt", "", time() - 3600, '/');

        // You may also want to redirect the user to the login page or any other appropriate page after logout
        $response = array(
            "message" => "Successfully logged out"
        );

        http_response_code(200);
        echo json_encode($response);
    }
}
?>
