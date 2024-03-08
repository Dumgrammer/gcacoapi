<?php 

header("Access-Control-Allow-Origin: *");
 
require_once(__DIR__ . './config/AcoDatabase.php');
require_once(__DIR__ . './services/AcojwtLogin.php');
require_once(__DIR__ . './services/AcojwtRegister.php');
require_once(__DIR__ . './services/AcoMailing.php');
require_once(__DIR__ . './services/AcoFormHandler.php');

$conn = new DatabaseAccess();
$pdo = $conn->connect();

$register = new RegisterUser($pdo);
$login = new Login($pdo);
$mail = new Mailing();
$forms = new FormHandler($pdo);

    if(isset($_REQUEST['request'])){
        
    $request = explode('/', $_REQUEST['request']);

    }
    else{
        
    echo "Not Found";
    http_response_code(404);
    exit;
    }

    switch($_SERVER['REQUEST_METHOD']){
         
        case 'POST':
            
            $data = json_decode(file_get_contents("php://input"));
            switch($request[0]){
                case 'login':
                
                        echo json_encode($login->loginUser($data));

                    break;
                    case 'register':

                            echo json_encode($register->registerUser($data));

                        break;

                case 'addrecords' :
                        
                        echo json_encode($forms->submitFormData($data));

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