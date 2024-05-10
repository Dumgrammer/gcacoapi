<?php

require_once (__DIR__ . '/../utils/Response.php');

class FormHandler extends GlobalUtil
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function submitFormData($formData) {
        $tableAttributeMapping = [
            'gc_alumni' => ['alumni_lastname', 'alumni_firstname', 'alumni_middlename', 'alumni_birthday', 'alumni_age'],
            'gc_alumni_contact' => ['alumni_email', 'alumni_number', 'alumni_address'],
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
    
            // Insert data into other related tables using the retrieved alumni_id
            foreach ($tableAttributeMapping as $table => $attributes) {
                if ($table !== 'gc_alumni') {
                    $tableData = array_intersect_key((array) $formData, array_flip($attributes));
                    $placeholders = rtrim(str_repeat('?,', count($tableData)), ',');
                    $tableSql = "INSERT INTO $table (alumni_id," . implode(',', array_keys($tableData)) . ") VALUES (?, $placeholders)";
                    $tableStmt = $this->pdo->prepare($tableSql);
                    $tableValues = array_merge([$alumniId], array_values($tableData));
                    $tableStmt->execute($tableValues);
                }
            }
    
            return $this->sendResponse("Data added successfully!", 201);
        } catch (\PDOException $e) {
            error_log("Failed to insert data: " . $e->getMessage());
            return $this->sendErrorResponse("Failed to add data", 500);
        }
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

    public function getFormData()
    {
        try {
            $tableName = 'gc_alumni'; 
    
            $sql = "SELECT * FROM $tableName WHERE isVisible = 1";
            $stmt = $this->pdo->query($sql);
    
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            return $this->sendResponse($result, 200);
        } catch (\PDOException $e) {
            $errmsg = $e->getMessage();
            return $this->sendErrorResponse("Failed to retrieve" . $errmsg, 400);
        }
    }
    
    public function getArchiveData()
    {
        try {
            $tableName = 'gc_alumni'; 
    
            $sql = "SELECT * FROM $tableName WHERE isVisible = 0";
            $stmt = $this->pdo->query($sql);
    
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            return $this->sendResponse($result, 200);
        } catch (\PDOException $e) {
            $errmsg = $e->getMessage();
            return $this->sendErrorResponse("Failed to retrieve" . $errmsg, 400);
        }
    }

    public function getFormContact()
    {
        try {
            $tableName = 'gc_alumni_contact'; 

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

                $sql = "SELECT alumni_email FROM $tableName";
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
                WHERE s.alumni_program = 'BSIT'";
                
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
                WHERE s.alumni_program = 'BSCS'";
                
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
                WHERE s.alumni_program = 'BSEMC'";
                
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
    public function getFormCredentials()
    {
        try {
            $tableName = 'gc_alumni_education'; 

            $sql = "SELECT * FROM $tableName";
            $stmt = $this->pdo->query($sql);

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $this->sendResponse($result, 200);
        } catch (\PDOException $e) {
            $errmsg = $e->getMessage();
            return $this->sendErrorResponse("Failed to retrieve" . $errmsg, 400);
        }
    }

    public function updateFormData($id, $formData){
        $tableName = 'gc_alumni'; 
    
        $attrs = array_keys((array) $formData);
        $updateStatements = array_map(function ($attr) {
            return "$attr = ?"; 
        }, $attrs);
    
        $sql = "UPDATE $tableName SET " . implode(', ', $updateStatements) . " WHERE alumni_id = ?";
    
        try {
            $stmt = $this->pdo->prepare($sql);
    
            $values = array_values((array) $formData);
            $values[] = (int) $id;
    
            $stmt->execute($values);
    
            return $this->sendResponse("Form data updated", 200);
        } catch (\PDOException $e) {
            $errmsg = $e->getMessage();
            return $this->sendErrorResponse("Failed to update: " . $errmsg, 400);
        }
    }
    

    public function deleteFormData($id)
    {
        $tableName = 'gc_alumni';
        $sql = "DELETE FROM $tableName WHERE alumni_id = ?";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);

            return $this->sendResponse("Form Deleted", 200);
        } catch (\PDOException $e) {
            $errmsg = $e->getMessage();
            return $this->sendErrorResponse("Failed to delete" . $errmsg, 400);
        }
    }
}
?>