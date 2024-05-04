<?php

require_once (__DIR__ . '/../utils/Response.php');

class FormHandler extends GlobalUtil
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function submitFormData($formData){   

        $tableAttributeMapping = [
            'gc_alumni' => ['alumni_lastname','alumni_firstname','alumni_middlename','alumni_birthday','alumni_age'],
            'gc_alumni_contact' => ['alumni_email','alumni_number','alumni_address'],
            'gc_alumni_education' => ['year_graduated','alumni_program','education_upgrade'],
            'gc_alumni_family' => ['alumni_marital_status','alumni_number_of_children','alumni_spousename','alumni_race','alumni_religion'],
            'gc_history' => ['employment_status','working_in_abroad','working_in_industry','years_of_experience','current_job']
        ];
    
        $formDataArray = (array) $formData;
    
        $inserted = false;
    
        foreach ($tableAttributeMapping as $table => $attributes) {
            // Taga check ng table kung meron
            $matchedAttributes = array_intersect($attributes, array_keys($formDataArray));
            if (!empty($matchedAttributes)) {
                try {
                    // Prepare SQL query
                    $sql = "INSERT INTO $table (" . implode(',', $matchedAttributes) . ") VALUES (" . rtrim(str_repeat('?,', count($matchedAttributes)), ',') . ")";
                    $stmt = $this->pdo->prepare($sql);
    
                    // taga get ng values na paglalagyan ng data
                    $values = array_intersect_key($formDataArray, array_flip($matchedAttributes));
    
                    $stmt->execute(array_values($values));
    
                    $inserted = true;
    
                    // Output success message
                    echo "Data added successfully to $table!";
                } catch (\PDOException $e) {
                    // Handle error: Log or display the error message
                    error_log("Failed to insert data into $table: " . $e->getMessage());
                }
            }
        }
    
        if ($inserted) {
            // Return success response
            return $this->sendResponse("Data added successfully!", 201);
        } else {
            // Return error response if no data was inserted
            return $this->sendErrorResponse("No matching table found for provided attributes", "Failed to add", 400);
        }
    }
    
    
    
    public function getFormData()
    {
        try {
            $tableName = 'gc_alumni'; 

            $sql = "SELECT * FROM $tableName";
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