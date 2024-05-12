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



    
    public function loginUser($email, $password) {
        $stmt = $this->conn->prepare("SELECT * FROM gc_admin WHERE admin_email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($password, $user['password'])) {
            return [
                'status' => 401,
                'message' => 'Invalid email or password'
            ];
        }

        $payload = [
            'iss' => 'localhost',
            'aud' => 'localhost',
            'exp' => time() + 3600,
            'data' => [
                'id' => $user['admin_id'],
                'firstname' => $user['faculty_firstname'],
                'lastname' => $user['faculty_lastname'],
                'email' => $user['admin_email'],
                'position'=>$user['admin_pos']
            ],
        ];

        $jwt = JWT::encode($payload, $this->secretKey, 'HS256');

        return [
            'status' => 200,
            'jwt' => $jwt,
            'message' => 'Login Successful'
        ];
    }




    public function logoutUser() {

        setcookie("jwt", "", time() - 3600, '/');


        http_response_code(200);
    }
}
?>
