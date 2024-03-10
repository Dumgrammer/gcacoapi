<?php

require_once(__DIR__ . '/../config/acodatabase.php');

class RegisterUser {
    private $conn;

    public function __construct() {
        $databaseService = new DatabaseAccess();
        $this->conn = $databaseService->connect();

        header("Access-Control-Allow-Origin: * ");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST");
        header("Access-Control-Max-Age: 3600");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    }

    public function registerUser() {


        $data = json_decode(file_get_contents("php://input"));

        $email = $data->admin_email;
        $password = $data->password;
        $lastName = $data->faculty_lastname;
        $firstName = $data->faculty_firstname;

        $table_name = 'admin';

        $query = "INSERT INTO " . $table_name . "
                    SET admin_email = :email,
                        password = :password,
                        faculty_lastname = :lastname,
                        faculty_firstname = :firstname";

        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':email', $email);

        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        $stmt->bindParam(':password', $password_hash);
        
        $stmt->bindParam(':firstname', $firstName);
        $stmt->bindParam(':lastname', $lastName);



        if ($stmt->execute()) {
            http_response_code(200);
            echo json_encode(array("message" => "User was successfully registered."));
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Unable to register the user."));
        }
    }
}

?>