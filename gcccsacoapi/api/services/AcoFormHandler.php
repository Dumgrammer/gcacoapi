<?php

require_once (__DIR__ . '/../utils/Response.php');

class FormHandler extends GlobalUtil
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function submitFormData($formData)
    {
        $tableName = 'alumni'; 

            $attrs = array_keys((array) $formData);
            $quest = array_fill(0, count($attrs), '?');

            $sql = "INSERT INTO formtable (" . implode(',', $attrs) . ") VALUES(" . implode(',', $quest) . ")";
        try {
            $stmt = $this->pdo->prepare($sql);

            $values = array_values((array) $formData);
            $stmt->execute($values);

            return $this->sendResponse("Form data added", 201);
        } catch (\PDOException $e) {
            $errmsg = $e->getMessage();
            return $this->sendErrorResponse($errmsg, "Failed to add" , 400);
        }
    }
    
    public function getFormData()
    {
        try {
            $tableName = 'table'; 

            $sql = "SELECT * FROM formtable";
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