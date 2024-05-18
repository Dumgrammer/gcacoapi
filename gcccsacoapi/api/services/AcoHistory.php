<?php

require_once (__DIR__ . '/../utils/Response.php');

class HistoryHandler extends GlobalUtil
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getGCHistory()
    {
        try {
            $tableName = 'gc_history'; 
    
            $sql = "SELECT * FROM $tableName";
            $stmt = $this->pdo->query($sql);
    
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            return $this->sendResponse($result, 200);
        } catch (\PDOException $e) {
            $errmsg = $e->getMessage();
            return $this->sendErrorResponse("Failed to retrieve" . $errmsg, 400);
        }
    }


    public function getEmployed()
        {
            try {
                $history = 'gc_history';
                
               
                $sql = "SELECT COUNT(*) as employed_count FROM $history WHERE employment_status = 'Employed Full-Time'";
                
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
    
    public function getParttime()
        {
            try {
                $history = 'gc_history';
                
               
                $sql = "SELECT COUNT(*) as parttime_count FROM $history WHERE employment_status = 'Employed Part-Time'";
                
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

        
    public function getUnemployed()
        {
            try {
                $history = 'gc_history';
                
               
                $sql = "SELECT COUNT(*) as unemployed_count FROM $history WHERE employment_status = 'Unemployed and not currently looking for work'";
                
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
        
        
    public function selfEmployed()
        {
            try {
                $history = 'gc_history';
                
               
                $sql = "SELECT COUNT(*) as selfemployed_count FROM $history WHERE employment_status = 'Self Employed'";
                
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

    public function lookingforWork()
        {
            try {
                $history = 'gc_history';
                
               
                $sql = "SELECT COUNT(*) as looking_count FROM $history WHERE employment_status = 'Unemployed and looking for work'";
                
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
    
        public function getStudent()
        {
            try {
                $history = 'gc_history';
                
               
                $sql = "SELECT COUNT(*) as student_count FROM $history WHERE employment_status = 'Student'";
                
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

        public function getRetired()
        {
            try {
                $history = 'gc_history';
                
               
                $sql = "SELECT COUNT(*) as retired_count FROM $history WHERE employment_status = 'Retired'";
                
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

        public function mostfrequestJob()
        {
            try {
                $history = 'gc_history';
                
                $sql = "SELECT current_job, COUNT(current_job) AS frequent_job FROM $history WHERE current_job IS NOT NULL GROUP BY current_job ORDER BY frequent_job DESC LIMIT 1";
                
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
        
        public function workingAbroad()
        {
            try {
                $history = 'gc_history';
                
                $sql = "SELECT COUNT(*) as working_abroad FROM $history WHERE working_in_abroad = 'Yes'";
                
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

        public function workingLocally()
        {
            try {
                $history = 'gc_history';
                
                $sql = "SELECT COUNT(*) as working_local FROM $history WHERE working_in_abroad = 'No'";
                
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

        public function workingITindustry()
        {
            try {
                $history = 'gc_history';
                
                $sql = "SELECT COUNT(*) as industry FROM $history WHERE working_in_industry = 'Yes'";
                
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
}
?>