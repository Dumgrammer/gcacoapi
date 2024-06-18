<?php
    // //For Debugging
    // ini_set('display_errors', 1);
    // ini_set('display_startup_errors', 1);
    // error_reporting(E_ALL);

    // // Debugging function
    // function debug_echo($message) {
    //     echo $message . "\n";
    // }
    
    // // // Debugging message
    // debug_echo('Script started.');
        
    // // // Debugging message
    // debug_echo('CORS headers set.');
    
    // // // Debugging message
    // debug_echo('Preflight request handled.');
    
    // // // Debugging message
    // debug_echo('Request method: ' . $_SERVER['REQUEST_METHOD']);
    
    // // Debugging message
    // debug_echo('Modules included.');
    
    // // // Debugging message
    // debug_echo('Database connection established.');
    
    // Enable error reporting for debugging
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    
    // Start output buffering to prevent accidental output
    ob_start();
    
    // Allow requests from any origin
    header('Access-Control-Allow-Origin: *');
    
    // Allow specific HTTP methods
    header('Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE');
    
    // Allow specific headers
    header('Access-Control-Allow-Headers: Content-Type, X-Auth-Token, Origin, Authorization');
    
    // Set Content-Type header to application/json for all responses
    header('Content-Type: application/json');
    
    // Handle preflight requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    
        exit(0);
    }
    
    require_once('./config/AcoDatabase.php');
    require_once( './services/AcojwtLogin.php');
    require_once('./services/AcojwtRegister.php');
    require_once('./services/AcoMailing.php');
    require_once('./services/AcoFormHandler.php');
    require_once('./services/AcoEmails.php');
    require_once('./services/AcoHistory.php');
    require_once('./services/AcoFamily.php');
    require_once('./services/AcoStatistics.php');

    
    $con = new DatabaseAccess();
    $pdo = $con->connect();
    
    $register = new RegisterUser($pdo);
    $login = new Login($pdo);
    $forms = new FormHandler($pdo);
    $mail = new Mail($pdo);
    $emails = new EmailHandler($pdo);
    $history = new HistoryHandler($pdo);
    $family = new FamilyHandler($pdo);
    $statistics = new StatisticsHandler($pdo);
    
    // Check if 'request' parameter is set in the request
    if (isset($_REQUEST['request'])) {
        // Split the request into an array based on '/'
        $request = explode('/', $_REQUEST['request']);
    } else {
        // If 'request' parameter is not set, return a 404 response
        echo json_encode(["error" => "Not Found"]);
        http_response_code(404);
        exit();
    }
    
    // Handle requests based on HTTP method
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'POST':
            $data = json_decode(file_get_contents("php://input"));
            switch ($request[0]) {
                case 'login':
                    if (isset($data->email) && isset($data->password)) {
                        echo json_encode($login->loginUser($data->email, $data->password));
                    } else {
                        echo json_encode([
                            'status' => 400,
                            'message' => 'Invalid input data'
                        ]);
                    }
                    break;
                case 'verify':
                    if (isset($data->email)) {
                        echo json_encode($emails->verifyEmail($data->email));
                    } else {
                        echo json_encode([
                            'status' => 400,
                            'message' => 'Invalid input data'
                        ]);
                    }
                    break;
                case 'logout':
                    echo json_encode($login->logoutUser($data));
                    break;
                case 'register':
                    echo json_encode($register->registerUser($data));
                    break;
                case 'importData':
                    echo json_encode($forms->importData($data));
                    break;
                case 'addrecord':
                    echo json_encode($forms->submitFormData($data));
                    break;
                case 'updaterecord':
                    echo json_encode($forms->updateAlumniData($data->formData, $data->id));
                    break;
                case 'addmail':
                    echo json_encode($emails->addMail($data));
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
                    case 'alldata':
                        echo json_encode($forms->getAlumnidata($data));
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
                    case  'alumnidata':
                        if (count($request) > 1) {
                            echo json_encode($forms->getProfiles($request[1]));
                        } else {
                            echo json_encode($forms->getProfiles());
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
                    case  'employeddata':
                        if (count($request) > 1) {
                            echo json_encode($history->getEmployedData($request[1]));
                        } else {
                            echo json_encode($history->getEmployedData());
                        }
                        break;
                    case  'unemployed':
                        if (count($request) > 1) {
                            echo json_encode($history->getUnemployed($request[1]));
                        } else {
                            echo json_encode($history->getUnemployed());
                        }
                        break;
                    case  'unemployeddata':
                        if (count($request) > 1) {
                            echo json_encode($history->getUnemployedData($request[1]));
                        } else {
                            echo json_encode($history->getUnemployedData());
                        }
                        break;
                    case  'selfemployed':
                        if (count($request) > 1) {
                            echo json_encode($history->selfEmployed($request[1]));
                        } else {
                            echo json_encode($history->selfEmployed());
                        }
                        break;
                    case  'selfemployeddata':
                        if (count($request) > 1) {
                            echo json_encode($history->getSelfEmployedData($request[1]));
                        } else {
                            echo json_encode($history->getSelfEmployedData());
                        }
                        break;
                    case  'looking':
                        if (count($request) > 1) {
                            echo json_encode($history->lookingforWork($request[1]));
                        } else {
                            echo json_encode($history->lookingforWork());
                        }
                        break;
                    case  'lookingdata':
                        if (count($request) > 1) {
                            echo json_encode($history->getlookingData($request[1]));
                        } else {
                            echo json_encode($history->getlookingData());
                        }
                        break;      
                    case  'student':
                        if (count($request) > 1) {
                            echo json_encode($history->getStudent($request[1]));
                        } else {
                            echo json_encode($history->getStudent());
                        }
                        break;
                    case  'studentdata':
                        if (count($request) > 1) {
                            echo json_encode($history->getStudentData($request[1]));
                        } else {
                            echo json_encode($history->getStudentData());
                        }
                        break;
                    case  'retired':
                        if (count($request) > 1) {
                            echo json_encode($history->getRetired($request[1]));
                        } else {
                            echo json_encode($history->getRetired());
                        }
                        break;
                    case  'retireddata':
                        if (count($request) > 1) {
                            echo json_encode($history->getRetiredData($request[1]));
                        } else {
                            echo json_encode($history->getRetiredData());
                        }
                        break;
                    case  'parttime':
                        if (count($request) > 1) {
                            echo json_encode($history->getParttime($request[1]));
                        } else {
                            echo json_encode($history->getParttime());
                        }
                        break;
                    case  'parttimedata':
                        if (count($request) > 1) {
                            echo json_encode($history->getPartimeData($request[1]));
                        } else {
                            echo json_encode($history->getPartimeData());
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
                    case  'parentdata':
                        if (count($request) > 1) {
                            echo json_encode($family->getParentData($request[1]));
                        } else {
                            echo json_encode($family->getParentData());
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
                    case  'globaldata':
                        if (count($request) > 1) {
                            echo json_encode($history->getGlobalData($request[1]));
                        } else {
                            echo json_encode($history->getGlobalData());
                        }
                        break;
                    case  'local':
                        if (count($request) > 1) {
                            echo json_encode($history->workingLocally($request[1]));
                        } else {
                            echo json_encode($history->workingLocally());
                        }
                        break;
                    case  'localdata':
                        if (count($request) > 1) {
                            echo json_encode($history->getLocalData($request[1]));
                        } else {
                            echo json_encode($history->getLocalData());
                        }
                        break;
                    case  'industry':
                        if (count($request) > 1) {
                            echo json_encode($history->workingITindustry($request[1]));
                        } else {
                            echo json_encode($history->workingITindustry());
                        }
                        break;
                    case  'industrydata':
                        if (count($request) > 1) {
                            echo json_encode($history->getIndustryData($request[1]));
                        } else {
                            echo json_encode($history->getIndustryData());
                        }
                        break;
                    case  'notindustry':
                        if (count($request) > 1) {
                            echo json_encode($history->notworkingITindustry($request[1]));
                        } else {
                            echo json_encode($history->notworkingITindustry());
                        }
                        break;
                    case  'gradrate':
                        if (count($request) > 1) {
                            echo json_encode($statistics->getGradrate($request[1]));
                        } else {
                            echo json_encode($statistics->getGradrate());
                        }
                        break;
                    case  'getverification':
                        if (count($request) > 1) {
                            echo json_encode($history->getVerification($request[1]));
                        } else {
                            echo json_encode($history->getVerification());
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
                        case 'unsubscribe':
                           
                            if (isset($data->recordIds) && isset($data->visibilityValue)) {
                                $result = $forms->unSubscribe($data->recordIds, $data->visibilityValue);
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
    
    // End output buffering and send output
    ob_end_flush();
?>