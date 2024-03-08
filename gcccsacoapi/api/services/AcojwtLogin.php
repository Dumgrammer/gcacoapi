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
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Max-Age: 3600");
        header("Access-Control-Allow-Methods: POST");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

        $email = '';
        $password = '';

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

        $sqlQuery = "SELECT fac_id, fac_fname, fac_lname, password FROM " . $tableName . " WHERE email = :email";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $num = $stmt->rowCount();

        if ($num > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $id = $row['fac_id'];
            $firstname = $row['fac_fname'];
            $lastname = $row['fac_lname'];
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

        return JWT::encode($token, $secretKey, 'HS512');
    }

    private function getExpirationTime() {
        return time() + 60; // Adjust this as needed
    }
}
?>
