<?php

date_default_timezone_set('Asia/Manila');
set_time_limit(1000);

define("SERVER", "localhost");
define("DATABASE", "gcccs_aco");
define("USER", "root");
define("PASSWORD", "");
define("DRIVER", "mysql");

class DatabaseAccess {
    private $connectionString;
    private $pdo_options;

    public function __construct() {
        $this->connectionString = DRIVER . ":host=" . SERVER . ";dbname=" . DATABASE . ";charset=utf8mb4";
        $this->pdo_options = [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES => false
        ];
    }

    public function connect() {
        try {
            $pdo = new \PDO($this->connectionString, USER, PASSWORD, $this->pdo_options);
            return $pdo;
        } catch (\PDOException $e) {
            throw new \PDOException("Failed to connect to database: " . $e->getMessage(), (int)$e->getCode());
        }
    }
}

?>
