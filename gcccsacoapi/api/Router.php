<?php

include('./config/AcoDatabase.php');
include( './services/AcojwtLogin.php');
include('./services/AcojwtRegister.php');
include('./services/AcoMailing.php');
include('./services/AcoFormHandler.php');
include('./services/AcoEmails.php');
include('./services/AcoHistory.php');
include('./services/AcoFamily.php');
include('./services/AcoStatistics.php');

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

// Your main code logic here


$conn = new DatabaseAccess();
$pdo = $conn->connect();

$register = new RegisterUser($pdo);
$login = new Login($pdo);
$mail = new Mail($pdo);
$forms = new FormHandler($pdo);
$emails = new EmailHandler($pdo);
$history = new HistoryHandler($pdo);
$family = new FamilyHandler($pdo);
$statistics = new StatisticsHandler($pdo);

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
                echo json_encode($login->loginUser($data->email, $data->password));
                break;
            case 'logout':
                echo json_encode($login->logoutUser($data));
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
                echo json_encode($forms->deleteFormData($data->recordIds));
                break;
            case 'mail':
                echo json_encode($mail->sendEmail($data));
                break;
            case 'schedule':
                echo json_encode($mail->scheduledSend($data));
                break;
            case 'mailhistory':
                echo json_encode($emails->mailHistory($data));
                break;
            case  'addrate':
                echo json_encode($statistics->insertRate($data));
                break;
            default:
                echo "This is forbidden";
                http_response_code(403);
                break;
        }
        break;
        case 'GET':
            $data = json_decode(file_get_contents("php://input"));
            switch ($request[0]) {
                case  'alumni':
                    if (count($request) > 1) {
                        echo json_encode($forms->getFormData($request[1]));
                    } else {
                        echo json_encode($forms->getFormData());
                    }
                    break;
                case  'pending':
                    if (count($request) > 1) {
                        echo json_encode($forms->getPending($request[1]));
                    } else {
                        echo json_encode($forms->getPending());
                    }
                    break;
                case  'contact':
                    if (count($request) > 1) {
                        echo json_encode($forms->getFormContact($request[1]));
                    } else {
                        echo json_encode($forms->getFormContact());
                    }
                    break;
                case  'education':
                    if (count($request) > 1) {
                        echo json_encode($forms->getFormCredentials($request[1]));
                    } else {
                        echo json_encode($forms->getFormCredentials());
                    }
                    break;
                case  'archive':
                    if (count($request) > 1) {
                        echo json_encode($forms->getArchiveData($request[1]));
                    } else {
                        echo json_encode($forms->getArchiveData());
                    }
                    break;
                case  'history':
                    if (count($request) > 1) {
                        echo json_encode($emails->getHistory($request[1]));
                    } else {
                        echo json_encode($emails->getHistory());
                    }
                    break;
                case  'gchistory':
                    if (count($request) > 1) {
                        echo json_encode($history->getGCHistory($request[1]));
                    } else {
                        echo json_encode($history->getGCHistory());
                    }
                    break;      
                case  'emails':
                    if (count($request) > 1) {
                        echo json_encode($emails->getEmails($request[1]));
                    } else {
                        echo json_encode($emails->getEmails());
                    }
                    break;
                case  'itemails':
                    if (count($request) > 1) {
                        echo json_encode($emails->getITEmails($request[1]));
                    } else {
                        echo json_encode($emails->getITEmails());
                    }
                    break;
                case  'csemails':
                    if (count($request) > 1) {
                        echo json_encode($emails->getCSEmails($request[1]));
                    } else {
                        echo json_encode($emails->getCSEmails());
                    }
                    break;
                case  'emcemails':
                    if (count($request) > 1) {
                        echo json_encode($emails->getEMCEmails($request[1]));
                    } else {
                        echo json_encode($emails->getEMCEmails());
                    }
                    break;
                case  'actemails':
                    if (count($request) > 1) {
                        echo json_encode($emails->getACTEmails($request[1]));
                    } else {
                        echo json_encode($emails->getACTEmails());
                    }
                    break;
                case  'employed':
                    if (count($request) > 1) {
                        echo json_encode($history->getEmployed($request[1]));
                    } else {
                        echo json_encode($history->getEmployed());
                    }
                    break;
                case  'unemployed':
                    if (count($request) > 1) {
                        echo json_encode($history->getUnemployed($request[1]));
                    } else {
                        echo json_encode($history->getUnemployed());
                    }
                    break;
                case  'selfemployed':
                    if (count($request) > 1) {
                        echo json_encode($history->selfEmployed($request[1]));
                    } else {
                        echo json_encode($history->selfEmployed());
                    }
                    break;
                case  'looking':
                    if (count($request) > 1) {
                        echo json_encode($history->lookingforWork($request[1]));
                    } else {
                        echo json_encode($history->lookingforWork());
                    }
                    break;     
                case  'student':
                    if (count($request) > 1) {
                        echo json_encode($history->getStudent($request[1]));
                    } else {
                        echo json_encode($history->getStudent());
                    }
                    break;
                case  'retired':
                    if (count($request) > 1) {
                        echo json_encode($history->getRetired($request[1]));
                    } else {
                        echo json_encode($history->getRetired());
                    }
                    break;
                case  'parttime':
                    if (count($request) > 1) {
                        echo json_encode($history->getParttime($request[1]));
                    } else {
                        echo json_encode($history->getParttime());
                    }
                    break;
                case  'status':
                    if (count($request) > 1) {
                        echo json_encode($family->getStatus($request[1]));
                    } else {
                        echo json_encode($family->getStatus());
                    }
                    break;
                case  'child':
                    if (count($request) > 1) {
                        echo json_encode($family->alumniChild($request[1]));
                    } else {
                        echo json_encode($family->alumniChild());
                    }
                    break;
                case  'frequent':
                    if (count($request) > 1) {
                        echo json_encode($history->mostfrequestJob($request[1]));
                    } else {
                        echo json_encode($history->mostfrequestJob());
                    }
                    break;
                case  'abroad':
                    if (count($request) > 1) {
                        echo json_encode($history->workingAbroad($request[1]));
                    } else {
                        echo json_encode($history->workingAbroad());
                    }
                    break;
                case  'local':
                    if (count($request) > 1) {
                        echo json_encode($history->workingLocally($request[1]));
                    } else {
                        echo json_encode($history->workingLocally());
                    }
                    break;
                case  'industry':
                    if (count($request) > 1) {
                        echo json_encode($history->workingITindustry($request[1]));
                    } else {
                        echo json_encode($history->workingITindustry());
                    }
                    break;
                case  'gradrate':
                    if (count($request) > 1) {
                        echo json_encode($statistics->getGradrate($request[1]));
                    } else {
                        echo json_encode($statistics->getGradrate());
                    }
                    break;                              
                default:
                    echo "Method not available";
                    http_response_code(404);
                    break;
            }
            break;  // <-- This was missing
            case 'PUT': // Handle PUT requests
                $data = json_decode(file_get_contents("php://input"));
                switch ($request[0]) {
                    case 'editvisibility':
                        // Assuming JSON data is sent in the request body
                        if (isset($data->recordIds) && isset($data->visibilityValue)) {
                            $result = $forms->updateVisibility($data->recordIds, $data->visibilityValue);
                            if ($result['status'] === 'success') {
                                echo json_encode($forms->sendResponse("Records visibility updated successfully", 200));
                            } else {
                                echo json_encode($forms->sendErrorResponse("Failed to update records", 400));
                            }
                        } else {
                            echo json_encode($forms->sendErrorResponse("Invalid Request", 400));
                        }
                        break;
                    case 'getaccepted':
                        // Assuming JSON data is sent in the request body
                        if (isset($data->recordIds) && isset($data->visibilityValue)) {
                            $result = $forms->getAccepted($data->recordIds, $data->visibilityValue);
                            if ($result['status'] === 'success') {
                                echo json_encode($forms->sendResponse("Records visibility updated successfully", 200));
                            } else {
                                echo json_encode($forms->sendErrorResponse("Failed to update records", 400));
                            }
                        } else {
                            echo json_encode($forms->sendErrorResponse("Invalid Request", 400));
                        }
                        break;
                    default:
                        echo "Method not available";
                        http_response_code(404);
                        break;
                        
                }
                break;
        
    default:
        echo "Method not available";
        http_response_code(404);
        break;
}

?>
