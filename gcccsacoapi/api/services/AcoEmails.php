<?php

require_once (__DIR__ . '/../utils/Response.php');
require_once(__DIR__ . '/../config/AcoDatabase.php');
require_once(__DIR__ . '/../config/secretKey.php');
require_once(__DIR__ . '/../vendor/autoload.php');

use Firebase\JWT\JWT;

class EmailHandler extends GlobalUtil
{
    private $pdo;
    private $conn;
    private $secretKey;

    public function __construct($pdo)
    {
        $databaseService = new DatabaseAccess();
        $this->conn = $databaseService->connect();

        $keys = new Secret();
        $this->secretKey = $keys->generateSecretKey();
        $this->pdo = $pdo;
    }


    public function verifyEmail($email) {
        $stmt = $this->conn->prepare("SELECT * FROM gc_alumni_contact WHERE alumni_email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if (!$user) {
            return [
                'status' => 401,
                'message' => 'Your email is not in the database'
            ];
        }
        
        // Check if number and address fields are null
        $number = $user['alumni_number'] !== null ? $user['alumni_number'] : null;
        $address = $user['alumni_address'] !== null ? $user['alumni_address'] : null;
    
        $payload = [
            'iss' => 'localhost',
            'aud' => 'localhost',
            'exp' => time() + 3600,
            'data' => [
                'id' => $user['alumni_id'],
                'email' => $user['alumni_email'],
                'number' => $number,
                'address' => $address
            ],
        ];
    
        $jwt = JWT::encode($payload, $this->secretKey, 'HS256');
    
        return [
            'status' => 200,
            'jwt' => $jwt,  
            'message' => 'Login Successful',
            'id' => $user['alumni_id'],
            'email' => $user['alumni_email'],
            'number' => $number,
            'address' => $address
        ];
    }
    


    public function mailHistory($formData)
    {
        $tableName = 'gc_mailing_history';
    
        // Convert object to array
        $formDataArray = (array) $formData;
    
        // Include only scalar values in $attrs
        $attrs = array_keys(array_filter($formDataArray, 'is_scalar'));
        $quest = array_fill(0, count($attrs), '?');
    
        $sql = "INSERT INTO $tableName (" . implode(',', $attrs) . ") VALUES(" . implode(',', $quest) . ")";
    
        try {
            $stmt = $this->pdo->prepare($sql);
    
            $values = array_values(array_filter($formDataArray, 'is_scalar'));
            $stmt->execute($values);

    
            return $this->sendResponse("Form data added", 201);
        } catch (\PDOException $e) {
            $errmsg = $e->getMessage();
            return $this->sendErrorResponse($errmsg, "Failed to add", 400);
        }
    }

    private function logMessage($message) {
        file_put_contents('debug.log', date('Y-m-d H:i:s') . " - " . $message . PHP_EOL, FILE_APPEND);
    }

    public function addMail($formData) {
        $tableAttributeMapping = [
            'gc_alumni' => ['alumni_lastname', 'alumni_firstname', 'alumni_middlename', 'alumni_birthday', 'alumni_age'],
            'gc_alumni_contact' => ['alumni_email', 'alumni_number', 'alumni_address', 'alumni_notify'],
            'gc_alumni_education' => ['year_graduated', 'alumni_program', 'education_upgrade'],
            'gc_alumni_family' => ['alumni_marital_status', 'alumni_no_of_children', 'alumni_spousename', 'alumni_race', 'alumni_religion'],
            'gc_history' => ['employment_status', 'working_in_abroad', 'working_in_industry', 'years_of_experience', 'current_job']
        ];
    
        try {
            // Insert data into gc_alumni table
            $alumniAttributes = array_intersect_key((array) $formData, array_flip($tableAttributeMapping['gc_alumni']));
            $alumniSql = "INSERT INTO gc_alumni (" . implode(',', array_keys($alumniAttributes)) . ") VALUES (" . rtrim(str_repeat('?,', count($alumniAttributes)), ',') . ")";
            $alumniStmt = $this->pdo->prepare($alumniSql);
            $alumniStmt->execute(array_values($alumniAttributes));
            $alumniId = $this->pdo->lastInsertId();
    
            // Insert data into gc_alumni_contact table
            $contactAttributes = array_intersect_key((array) $formData, array_flip($tableAttributeMapping['gc_alumni_contact']));
            $contactAttributes['alumni_id'] = $alumniId; // Add alumni_id
            $contactSql = "INSERT INTO gc_alumni_contact (" . implode(',', array_keys($contactAttributes)) . ") VALUES (" . rtrim(str_repeat('?,', count($contactAttributes)), ',') . ")";
            $contactStmt = $this->pdo->prepare($contactSql);
            $contactStmt->execute(array_values($contactAttributes));
    
            // Insert data into gc_alumni_education table
            $educationAttributes = array_intersect_key((array) $formData, array_flip($tableAttributeMapping['gc_alumni_education']));
            $educationAttributes['alumni_id'] = $alumniId; // Add alumni_id
            $educationSql = "INSERT INTO gc_alumni_education (" . implode(',', array_keys($educationAttributes)) . ") VALUES (" . rtrim(str_repeat('?,', count($educationAttributes)), ',') . ")";
            $educationStmt = $this->pdo->prepare($educationSql);
            $educationStmt->execute(array_values($educationAttributes));
    
            // Insert data into gc_alumni_family table
            $familyAttributes = array_intersect_key((array) $formData, array_flip($tableAttributeMapping['gc_alumni_family']));
            $familyAttributes['alumni_id'] = $alumniId; // Add alumni_id
            $familySql = "INSERT INTO gc_alumni_family (" . implode(',', array_keys($familyAttributes)) . ") VALUES (" . rtrim(str_repeat('?,', count($familyAttributes)), ',') . ")";
            $familyStmt = $this->pdo->prepare($familySql);
            $familyStmt->execute(array_values($familyAttributes));
    
            // Insert data into gc_history table
            $historyAttributes = array_intersect_key((array) $formData, array_flip($tableAttributeMapping['gc_history']));
            $historyAttributes['alumni_id'] = $alumniId; // Add alumni_id
            $historySql = "INSERT INTO gc_history (" . implode(',', array_keys($historyAttributes)) . ") VALUES (" . rtrim(str_repeat('?,', count($historyAttributes)), ',') . ")";
            $historyStmt = $this->pdo->prepare($historySql);
            $historyStmt->execute(array_values($historyAttributes));
    
            return $this->sendResponse("Data added successfully!", 201);
        } catch (\PDOException $e) {
            error_log("Failed to insert data: " . $e->getMessage());
            return $this->sendErrorResponse("Failed to add data", 500);
        }
    }
    
    public function getHistory()
    {
        try {
            $tableName = 'gc_mailing_history'; 
    
            $sql = "SELECT * FROM $tableName";
            $stmt = $this->pdo->query($sql);
    
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            return $this->sendResponse($result, 200);
        } catch (\PDOException $e) {
            $errmsg = $e->getMessage();
            return $this->sendErrorResponse("Failed to retrieve" . $errmsg, 400);
        }
    }

    public function getEmails()
        {
            try {
                $tableName = 'gc_alumni_contact'; 

                $sql = "SELECT alumni_email FROM $tableName WHERE alumni_notify = '1'";
                $stmt = $this->pdo->query($sql);

                $result = $stmt->fetchAll(PDO::FETCH_COLUMN);

                return $this->sendResponse($result, 200);
            } catch (\PDOException $e) {
                $errmsg = $e->getMessage();
                return $this->sendErrorResponse("Failed to retrieve: " . $errmsg, 400);
            }
        }

    public function getITEmails()
        {
            try {
                $contactTableName = 'gc_alumni_contact';
                $siblingTableName = 'gc_alumni_education';
        
                $sql = "SELECT c.alumni_email 
                FROM $contactTableName c
                INNER JOIN $siblingTableName s ON c.alumni_id = s.alumni_id
                WHERE s.alumni_program = 'BSIT' AND c.alumni_notify = '1'";
                
                $stmt = $this->pdo->query($sql);
        
                $result = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
                return $this->sendResponse($result, 200);
            } catch (\PDOException $e) {
                $errmsg = $e->getMessage();
                return $this->sendErrorResponse("Failed to retrieve: " . $errmsg, 400);
            }
        }
    public function getCSEmails()
        {
            try {
                $contactTableName = 'gc_alumni_contact';
                $siblingTableName = 'gc_alumni_education';
        
                $sql = "SELECT c.alumni_email 
                FROM $contactTableName c
                INNER JOIN $siblingTableName s ON c.alumni_id = s.alumni_id
                WHERE s.alumni_program = 'BSCS' AND c.alumni_notify = '1'";
                
                $stmt = $this->pdo->query($sql);
        
                $result = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
                return $this->sendResponse($result, 200);
            } catch (\PDOException $e) {
                $errmsg = $e->getMessage();
                return $this->sendErrorResponse("Failed to retrieve: " . $errmsg, 400);
            }
        }    
    public function getEMCEmails()
        {
            try {
                $contactTableName = 'gc_alumni_contact';
                $siblingTableName = 'gc_alumni_education';
        
                $sql = "SELECT c.alumni_email 
                FROM $contactTableName c
                INNER JOIN $siblingTableName s ON c.alumni_id = s.alumni_id
                WHERE s.alumni_program = 'BSEMC' AND c.alumni_notify = '1'";
                
                $stmt = $this->pdo->query($sql);
        
                $result = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
                return $this->sendResponse($result, 200);
            } catch (\PDOException $e) {
                $errmsg = $e->getMessage();
                return $this->sendErrorResponse("Failed to retrieve: " . $errmsg, 400);
            }
        }


    public function getACTEmails()
        {
            try {
                $contactTableName = 'gc_alumni_contact';
                $siblingTableName = 'gc_alumni_education';
        
                $sql = "SELECT c.alumni_email 
                FROM $contactTableName c
                INNER JOIN $siblingTableName s ON c.alumni_id = s.alumni_id
                WHERE s.alumni_program = 'ACT'";
                
                $stmt = $this->pdo->query($sql);
        
                $result = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
                return $this->sendResponse($result, 200);
            } catch (\PDOException $e) {
                $errmsg = $e->getMessage();
                return $this->sendErrorResponse("Failed to retrieve: " . $errmsg, 400);
            }
        }   
    
}
?>