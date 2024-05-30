<?php

require_once (__DIR__ . '/../utils/Response.php');
require_once(__DIR__ . '/../config/AcoDatabase.php');
require_once(__DIR__ . '/../config/secretKey.php');
require_once(__DIR__ . '/../vendor/autoload.php');

class FormHandler extends GlobalUtil
{
    private $pdo;
    private $conn;

    public function __construct($pdo)
    {
        $databaseService = new DatabaseAccess();
        $this->conn = $databaseService->connect();
        $this->pdo = $pdo;

    }

    public function submitFormData($formData) {
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

    public function updateAlumniData($formData, $alumniId) {
        $tableAttributeMapping = [
            'gc_alumni' => ['alumni_lastname', 'alumni_firstname', 'alumni_middlename', 'alumni_birthday', 'alumni_age'],
            'gc_alumni_contact' => ['alumni_email', 'alumni_number', 'alumni_address', 'alumni_notify'],
            'gc_alumni_education' => ['year_graduated', 'alumni_program', 'education_upgrade'],
            'gc_alumni_family' => ['alumni_marital_status', 'alumni_no_of_children', 'alumni_spousename', 'alumni_race', 'alumni_religion'],
            'gc_history' => ['employment_status', 'working_in_abroad', 'working_in_industry', 'years_of_experience', 'current_job']
        ];
    
        try {
            // Update data in gc_alumni table
            $alumniAttributes = array_intersect_key((array) $formData, array_flip($tableAttributeMapping['gc_alumni']));
            $alumniSet = implode('=?, ', array_keys($alumniAttributes)) . '=?';
            $alumniSql = "UPDATE gc_alumni SET $alumniSet WHERE alumni_id=?";
            $alumniStmt = $this->pdo->prepare($alumniSql);
            $alumniValues = array_merge(array_values($alumniAttributes), [$alumniId]);
            $alumniStmt->execute($alumniValues);
    
            // Update data in other related tables
            foreach ($tableAttributeMapping as $table => $attributes) {
                if ($table !== 'gc_alumni') {
                    $tableData = array_intersect_key((array) $formData, array_flip($attributes));
                    $setClause = implode('=?, ', array_keys($tableData)) . '=?';
                    $tableSql = "UPDATE $table SET $setClause WHERE alumni_id=?";
                    $tableStmt = $this->pdo->prepare($tableSql);
                    $tableValues = array_merge(array_values($tableData), [$alumniId]);
                    $tableStmt->execute($tableValues);
                }
            }
    
            return $this->sendResponse("Data updated successfully!", 200);
        } catch (\PDOException $e) {
            error_log("Failed to update data: " . $e->getMessage());
            return $this->sendErrorResponse("Failed to update data", 500);
        }
    }
    
    
    public function importData($formDataArray)
    {
        $tableAttributeMapping = [
            'gc_alumni' => ['alumni_lastname', 'alumni_firstname', 'alumni_middlename'],
            'gc_alumni_contact' => ['alumni_email'],
            'gc_alumni_education' => ['year_graduated', 'alumni_program'],
        ];
        
        try {
            foreach ($formDataArray as $formData) {
                $formData = (array)$formData; // Ensure $formData is an array
    
                // Check if the record already exists in gc_alumni table
                $existingAlumniSql = "SELECT alumni_id FROM gc_alumni WHERE alumni_lastname = ? AND alumni_firstname = ? AND alumni_middlename = ?";
                $existingAlumniStmt = $this->pdo->prepare($existingAlumniSql);
                $existingAlumniStmt->execute([$formData['alumni_lastname'], $formData['alumni_firstname'], $formData['alumni_middlename']]);
                $existingAlumniId = $existingAlumniStmt->fetchColumn();
    
                if ($existingAlumniId) {
                    // Record exists, update isVisible to 1
                    $updateVisibilitySql = "UPDATE gc_alumni SET isVisible = 1 WHERE alumni_id = ?";
                    $updateVisibilityStmt = $this->pdo->prepare($updateVisibilitySql);
                    $updateVisibilityStmt->execute([$existingAlumniId]);
                    
                    // No need to insert a new record or update other tables, as we only update visibility
                    continue;
                }
    
                // Insert data into gc_alumni table
                $alumniAttributes = array_intersect_key($formData, array_flip($tableAttributeMapping['gc_alumni']));
                $alumniSql = "INSERT INTO gc_alumni (" . implode(',', array_keys($alumniAttributes)) . ") VALUES (" . rtrim(str_repeat('?,', count($alumniAttributes)), ',') . ")";
                $alumniStmt = $this->pdo->prepare($alumniSql);
                $alumniStmt->execute(array_values($alumniAttributes));
                $alumniId = $this->pdo->lastInsertId();
    
                // Insert data into gc_alumni_contact table
                $contactAttributes = array_intersect_key($formData, array_flip($tableAttributeMapping['gc_alumni_contact']));
                if (!empty($contactAttributes)) {
                    $contactSql = "INSERT INTO gc_alumni_contact (alumni_id, " . implode(',', array_keys($contactAttributes)) . ") VALUES (?, " . rtrim(str_repeat('?,', count($contactAttributes)), ',') . ")";
                    $contactStmt = $this->pdo->prepare($contactSql);
                    if (!$contactStmt->execute(array_merge([$alumniId], array_values($contactAttributes)))) {
                        error_log("Failed to insert into gc_alumni_contact: " . implode(', ', $contactStmt->errorInfo()));
                    }
                } else {
                    error_log("No data found for gc_alumni_contact");
                }
    
                // Insert data into gc_alumni_education table
                $educationAttributes = array_intersect_key($formData, array_flip($tableAttributeMapping['gc_alumni_education']));
                if (!empty($educationAttributes)) {
                    $educationSql = "INSERT INTO gc_alumni_education (alumni_id, " . implode(',', array_keys($educationAttributes)) . ") VALUES (?, " . rtrim(str_repeat('?,', count($educationAttributes)), ',') . ")";
                    $educationStmt = $this->pdo->prepare($educationSql);
                    if (!$educationStmt->execute(array_merge([$alumniId], array_values($educationAttributes)))) {
                        error_log("Failed to insert into gc_alumni_education: " . implode(', ', $educationStmt->errorInfo()));
                    }
                } else {
                    error_log("No data found for gc_alumni_education");
                }
            }
    
            return $this->sendResponse("Data imported successfully!", 201);
        } catch (\PDOException $e) {
            error_log("Failed to import data: " . $e->getMessage());
            return $this->sendErrorResponse("Failed to import data", 500);
        }
    }

    public function getAlumnidata($alumni_id) {
        try {
            $sql = "SELECT * FROM gc_alumni WHERE alumni_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$alumni_id]);
            $alumni_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
 
            $sql = "SELECT * FROM gc_alumni_contact WHERE alumni_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$alumni_id]);
            $contact_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            $sql = "SELECT * FROM gc_alumni_education WHERE alumni_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$alumni_id]);
            $education_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $sql = "SELECT * FROM gc_alumni_family WHERE alumni_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$alumni_id]);
            $family_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $sql = "SELECT * FROM gc_history WHERE alumni_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$alumni_id]);
            $history_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $result = [
                'alumni_data' => $alumni_data,
                'contact_data' => $contact_data,
                'education_data' => $education_data,
                'family_data' => $family_data,
                'history_data' => $history_data
            ];
    
            // Send the combined data as response
            return $this->sendResponse($result, 200);
        } catch (\PDOException $e) {
            $errmsg = $e->getMessage();
            return $this->sendErrorResponse("Failed to retrieve: " . $errmsg, 400);
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
    

    public function getPending()
    {
        try {
            $tableName = 'gc_alumni'; 
    
            $sql = "SELECT * FROM $tableName WHERE isVisible = 2";
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
            
            $alumniTable = 'gc_alumni';
            $contactTableName = 'gc_alumni_contact';
            $educationTableName = 'gc_alumni_education';
    
            $sql = "SELECT a.alumni_id, a.alumni_lastname, a.alumni_firstname, a.alumni_middlename, 
                           c.alumni_email, e.alumni_program, e.year_graduated
                    FROM $alumniTable a
                    INNER JOIN $contactTableName c ON a.alumni_id = c.alumni_id
                    INNER JOIN $educationTableName e ON a.alumni_id = e.alumni_id
                    WHERE a.isVisible = 0";
            
            $stmt = $this->pdo->query($sql);
    
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            return $this->sendResponse($result, 200);
        } catch (\PDOException $e) {
            $errmsg = $e->getMessage();
            return $this->sendErrorResponse("Failed to retrieve: " . $errmsg, 400);
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

    public function updateFormData($id, $formData) {
        $tableName = 'gc_admin'; 
    
        // Map frontend field names to backend table column names
        $fieldMappings = [
            'firstname' => 'faculty_firstname',
            'lastname' => 'faculty_lastname',
            'email' => 'admin_email',
            'position' => 'admin_pos'
            // Add mappings for other fields as needed
        ];
    
        // Modify formData keys according to the mappings
        $mappedFormData = [];
        foreach ($formData as $key => $value) {
            if (isset($fieldMappings[$key])) {
                $mappedFormData[$fieldMappings[$key]] = $value;
            }
        }
    
        // Construct SQL UPDATE statement
        $updateStatements = array_map(function ($attr) {
            return "$attr = ?"; 
        }, array_keys($mappedFormData));
        $sql = "UPDATE $tableName SET " . implode(', ', $updateStatements) . " WHERE admin_id = ?";
    
        try {
            $stmt = $this->pdo->prepare($sql);
            $values = array_values($mappedFormData);
            $values[] = (int) $id;
            $stmt->execute($values);
    
            return $this->sendResponse("Form data updated", 200);
        } catch (\PDOException $e) {
            $errmsg = $e->getMessage();
            error_log("Failed to update: " . $errmsg); // Add logging here
            return $this->sendErrorResponse("Failed to update: " . $errmsg, 400);
        }
    }
    

    public function updateVisibility($recordIds, $isVisible) {
        $tableName = 'gc_alumni'; 
    
        $isVisible = is_numeric($isVisible) ? intval($isVisible) : 0;
    
        $placeholders = rtrim(str_repeat('?,', count($recordIds)), ',');
        $sql = "UPDATE $tableName SET isVisible = ? WHERE alumni_id IN ($placeholders)";
    
        try {
            $stmt = $this->pdo->prepare($sql);
            
            // Combine the visibility value and record IDs into a single array
            $params = array_merge([$isVisible], $recordIds);
    
            $stmt->execute($params);
    
            return $this->sendResponse("Visibility updated", 200);
        } catch (\PDOException $e) {
            $errmsg = $e->getMessage();
            return $this->sendErrorResponse("Failed to update visibility: " . $errmsg, 400);
        }
    }
        
    public function getAccepted($recordIds, $isVisible) {
        $tableName = 'gc_alumni'; 
    
        $isVisible = is_numeric($isVisible) ? intval($isVisible) : 1;
    
        $placeholders = rtrim(str_repeat('?,', count($recordIds)), ',');
        $sql = "UPDATE $tableName SET isVisible = ? WHERE alumni_id IN ($placeholders)";
    
        try {
            $stmt = $this->pdo->prepare($sql);
            
            // Combine the visibility value and record IDs into a single array
            $params = array_merge([$isVisible], $recordIds);
    
            $stmt->execute($params);
    
            return $this->sendResponse("Visibility updated", 200);
        } catch (\PDOException $e) {
            $errmsg = $e->getMessage();
            return $this->sendErrorResponse("Failed to update visibility: " . $errmsg, 400);
        }
    }

    public function deleteFormData($recordIds)
    {
        $tableName = 'gc_alumni';
        $sql = "DELETE FROM $tableName WHERE alumni_id = ?";
        try {
            $stmt = $this->pdo->prepare($sql);
            
            // Loop over each record ID and execute the statement for each ID
            foreach ($recordIds as $id) {
                $stmt->execute([$id]);
            }
    
            return $this->sendResponse("Form Deleted", 200);
        } catch (\PDOException $e) {
            $errmsg = $e->getMessage();
            return $this->sendErrorResponse("Failed to delete" . $errmsg, 400);
        }
    }

}


?>