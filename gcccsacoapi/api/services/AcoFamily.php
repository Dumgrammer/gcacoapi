<?php

require_once (__DIR__ . '/../utils/Response.php');

class FamilyHandler extends GlobalUtil
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }


    public function getStatus()
    {
        try {
            $tableName = 'gc_alumni_family'; 
    
            $sql = "SELECT * FROM $tableName";
            $stmt = $this->pdo->query($sql);
    
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            return $this->sendResponse($result, 200);
        } catch (\PDOException $e) {
            $errmsg = $e->getMessage();
            return $this->sendErrorResponse("Failed to retrieve" . $errmsg, 400);
        }
    }

    public function alumniChild()
    {
        try {
            $history = 'gc_alumni_family';
            
           
            $sql = "SELECT COUNT(*) as familied_count FROM $history WHERE alumni_no_of_children != '0'";
            
            $stmt = $this->pdo->query($sql);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $response = [
                'status' => 'success',
                'data' => $result,
                'statusCode' => 200
            ];
            
            return $this->sendResponse($response, 200);
        } catch (\PDOException $e) {
            $errmsg = $e->getMessage();
            return $this->sendErrorResponse("Failed to retrieve: " . $errmsg, 400);
        }
    }

    public function getParentData()
    {
        try {
            $family = 'gc_alumni_family';
            $alumni = 'gc_alumni';
            $contact = 'gc_alumni_contact';
            $education = 'gc_alumni_education';
            $history = 'gc_history';
    
            $sql = "
                SELECT 
                    alumni.alumni_id,
                    alumni.alumni_lastname,
                    alumni.alumni_firstname,
                    alumni.alumni_middlename,
                    alumni.alumni_birthday,
                    alumni.alumni_age,
                    contact.alumni_address, 
                    contact.alumni_number, 
                    contact.alumni_email,
                    family.alumni_race,
                    family.alumni_religion,
                    family.alumni_spousename,
                    family.alumni_no_of_children,
                    family.alumni_marital_status,
                    education.year_graduated,
                    education.alumni_program,
                    education.education_upgrade,
                    history.employment_status,
                    history.working_in_industry,
                    history.working_in_abroad,
                    history.years_of_experience,
                    history.response_date
                FROM 
                    $family AS family
                JOIN 
                    $alumni AS alumni
                ON 
                    family.alumni_id = alumni.alumni_id
                JOIN
                    $contact AS contact
                ON
                    alumni.alumni_id = contact.alumni_id
                JOIN
                    $education AS education
                ON
                    alumni.alumni_id = education.alumni_id
                JOIN
                    $history AS history
                ON
                    alumni.alumni_id = history.alumni_id
                WHERE 
                    family.alumni_no_of_children != '0'
            ";
            
            $stmt = $this->pdo->query($sql);
            
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $response = [
                'status' => 'success',
                'data' => $result,
                'statusCode' => 200
            ];
            
            return $this->sendResponse($response, 200);
        } catch (\PDOException $e) {
            $errmsg = $e->getMessage();
            return $this->sendErrorResponse("Failed to retrieve: " . $errmsg, 400);
        }
    }
    
    
}
?>