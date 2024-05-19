<?php

require_once (__DIR__ . '/../utils/Response.php');

class StatisticsHandler extends GlobalUtil
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getGradrate()
    {
        try {
            // Correct the table name if needed
            $sql = "
                SELECT 
                    year,
                    bsit_no_of_students,
                    bsit_graduates,
                    bscs_no_of_students,
                    bscs_graduates,
                    bsemc_no_of_students,
                    bsemc_graduates,
                    act_no_of_students,
                    act_graduates
                FROM ccs_grad_rate
                ORDER BY year";
    
            $stmt = $this->pdo->query($sql);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            // Debug: Log the raw query result
            error_log('Query Result: ' . print_r($result, true));
    
            if (empty($result)) {
                return $this->sendErrorResponse("No data found", 404);
            }
    
            // Calculate graduation rates
            foreach ($result as &$row) {
                $row['bsit_rate'] = ($row['bsit_no_of_students'] > 0) ? ($row['bsit_graduates'] / $row['bsit_no_of_students'] * 100) : 0;
                $row['bscs_rate'] = ($row['bscs_no_of_students'] > 0) ? ($row['bscs_graduates'] / $row['bscs_no_of_students'] * 100) : 0;
                $row['bsemc_rate'] = ($row['bsemc_no_of_students'] > 0) ? ($row['bsemc_graduates'] / $row['bsemc_no_of_students'] * 100) : 0;
                $row['act_rate'] = ($row['act_no_of_students'] > 0) ? ($row['act_graduates'] / $row['act_no_of_students'] * 100) : 0;
            }
    
            return $this->sendResponse($result, 200);
        } catch (\PDOException $e) {
            $errmsg = $e->getMessage();
            // Debug: Log the detailed error message
            error_log('PDOException: ' . $errmsg);
            return $this->sendErrorResponse("Failed to retrieve: " . $errmsg, 400);
        }
    }

    public function insertRate($formData)
    {
        $tableName = 'ccs_grad_rate';
    
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
    
}


?>