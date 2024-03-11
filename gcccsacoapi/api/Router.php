<?php

require_once(__DIR__ . './config/AcoDatabase.php');
require_once(__DIR__ . './services/AcojwtLogin.php');
require_once(__DIR__ . './services/AcojwtRegister.php');
require_once(__DIR__ . './services/AcoMailing.php');
require_once(__DIR__ . './services/AcoFormHandler.php');

if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');
}


if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, PUT,DELETE,OPTIONS");         

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    exit(0);
}
$conn = new DatabaseAccess();
$pdo = $conn->connect();

$register = new RegisterUser($pdo);
$login = new Login($pdo);
$mail = new Mail($pdo);
$forms = new FormHandler($pdo);

if (isset($_REQUEST['request'])) {
    $request = explode('/', $_REQUEST['request']);
} else {
    echo "Not Found";
    http_response_code(404);
    exit;
}

switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        switch ($request[0]) {
            case 'login':
                echo json_encode($login->loginUser($data));
                break;
            case 'register':
                echo json_encode($register->registerUser($data));
                break;
            case 'addrecord':
                echo json_encode($forms->submitFormData($data));
                break;
            case 'editrecord':
                if (isset($request[1])) {
                    echo json_encode($forms->updateFormData($request[1], $data));
                } else {
                    echo json_encode($forms->sendErrorResponse("Invalid Response", 400));
                }
                break;
            case 'deleterecord':
                echo json_encode($forms->deleteFormData($request[1], $data));
                break;
            case 'mail':
                echo json_encode($mail->sendEmail($data));
                break;
            default:
                echo "This is forbidden";
                http_response_code(403);
                break;
        }
        break;
    default:
        echo "Method not available";
        http_response_code(404);
        break;
}

?>
