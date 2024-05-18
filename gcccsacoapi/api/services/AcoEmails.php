<?php

require_once (__DIR__ . '/../utils/Response.php');

class EmailHandler extends GlobalUtil
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
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