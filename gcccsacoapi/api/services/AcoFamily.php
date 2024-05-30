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
           
            $sql = "
                SELECT 
                    alumni.alumni_id,
                    alumni.alumni_lastname,
                    alumni.alumni_firstname,
                    alumni.alumni_middlename
                FROM 
                    $family AS history
                JOIN 
                    $alumni AS alumni
                ON 
                    history.alumni_id = alumni.alumni_id
                    WHERE alumni_no_of_children != '0'
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