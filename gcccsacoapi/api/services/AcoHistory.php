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

    
}
?>