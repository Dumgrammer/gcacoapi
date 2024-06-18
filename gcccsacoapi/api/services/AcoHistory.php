<?php

require_once(__DIR__ . '/../utils/Response.php');

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
            $education = 'gc_alumni_education';

            $sql = "
                    SELECT 
                        COUNT(*) as employed_count,
                        education.year_graduated
                    FROM 
                        $history AS history
                    JOIN
                        $education AS education
                    ON 
                        education.alumni_id = history.alumni_id
                    WHERE 
                        employment_status = 'Employed Full-Time'
                    GROUP BY
                        education.year_graduated
                ";

            $stmt = $this->pdo->query($sql);

            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $response = [
                'status' => 'success',
                'data' => $results,
                'statusCode' => 200
            ];

            return $this->sendResponse($response, 200);
        } catch (\PDOException $e) {
            $errmsg = $e->getMessage();
            return $this->sendErrorResponse("Failed to retrieve: " . $errmsg, 400);
        }
    }


    public function getEmployedData()
    {
        try {
            $history = 'gc_history';
            $alumni = 'gc_alumni';
            $contact = 'gc_alumni_contact';
            $family = 'gc_alumni_family';
            $education = 'gc_alumni_education';

            $sql = "
                        SELECT 
                            alumni.alumni_id,
                            alumni.alumni_lastname,
                            alumni.alumni_firstname,
                            alumni.alumni_middlename,
                            alumni.alumni_birthday,
                            alumni.alumni_age,
                            contact.alumni_address, 
                            contact.alumni_number, 
                            contact.alumni_email,
                            family.alumni_race,
                            family.alumni_religion,
                            family.alumni_spousename,
                            family.alumni_no_of_children,
                            family.alumni_marital_status,
                            education.year_graduated,
                            education.alumni_program,
                            education.education_upgrade,
                            history.employment_status,
                            history.working_in_industry,
                            history.working_in_abroad,
                            history.years_of_experience,
                            history.response_date
                        FROM 
                            $history AS history
                        JOIN 
                            $alumni AS alumni
                        ON 
                            history.alumni_id = alumni.alumni_id
                        JOIN
                            $contact AS contact -- Join alumni contact table
                        ON
                            alumni.alumni_id = contact.alumni_id
                        JOIN
                            $family AS family -- Join alumni family table
                        ON
                            alumni.alumni_id = family.alumni_id
                        JOIN
                            $education AS education -- Join alumni education table
                        ON
                            alumni.alumni_id = education.alumni_id
                        WHERE 
                            history.employment_status = 'Employed Full-Time'
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



    // public function getParttime()
    //     {
    //         try {
    //             $history = 'gc_history';


    //             $sql = "SELECT COUNT(*) as parttime_count FROM $history WHERE employment_status = 'Employed Part-Time'";

    //             $stmt = $this->pdo->query($sql);

    //             $result = $stmt->fetch(PDO::FETCH_ASSOC);

    //             $response = [
    //                 'status' => 'success',
    //                 'data' => $result,
    //                 'statusCode' => 200
    //             ];

    //             return $this->sendResponse($response, 200);
    //         } catch (\PDOException $e) {
    //             $errmsg = $e->getMessage();
    //             return $this->sendErrorResponse("Failed to retrieve: " . $errmsg, 400);
    //         }
    //     }


    public function getParttime()
    {
        try {
            $history = 'gc_history';
            $education = 'gc_alumni_education';

            $sql = "
                    SELECT 
                        COUNT(*) as parttime_count,
                        education.year_graduated
                    FROM 
                        $history AS history
                    JOIN
                        $education AS education
                    ON 
                        education.alumni_id = history.alumni_id
                    WHERE 
                        employment_status = 'Employed Part-Time'
                    GROUP BY
                        education.year_graduated
                ";

            $stmt = $this->pdo->query($sql);

            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $response = [
                'status' => 'success',
                'data' => $results,
                'statusCode' => 200
            ];

            return $this->sendResponse($response, 200);
        } catch (\PDOException $e) {
            $errmsg = $e->getMessage();
            return $this->sendErrorResponse("Failed to retrieve: " . $errmsg, 400);
        }
    }

    public function getPartimeData()
    {
        try {
            $history = 'gc_history';
            $alumni = 'gc_alumni';
            $contact = 'gc_alumni_contact';
            $family = 'gc_alumni_family';
            $education = 'gc_alumni_education';

            $sql = "
                    SELECT 
                        alumni.alumni_id,
                        alumni.alumni_lastname,
                        alumni.alumni_firstname,
                        alumni.alumni_middlename,
                        alumni.alumni_birthday,
                        alumni.alumni_age,
                        contact.alumni_address, 
                        contact.alumni_number, 
                        contact.alumni_email,
                        family.alumni_race,
                        family.alumni_religion,
                        family.alumni_spousename,
                        family.alumni_no_of_children,
                        family.alumni_marital_status,
                        education.year_graduated,
                        education.alumni_program,
                        education.education_upgrade,
                        history.employment_status,
                        history.working_in_industry,
                        history.working_in_abroad,
                        history.years_of_experience,
                        history.response_date
                    FROM 
                        $history AS history
                    JOIN 
                        $alumni AS alumni
                    ON 
                        history.alumni_id = alumni.alumni_id
                    JOIN
                        $contact AS contact
                    ON
                        alumni.alumni_id = contact.alumni_id
                    JOIN
                        $family AS family
                    ON
                        alumni.alumni_id = family.alumni_id
                    JOIN
                        $education AS education
                    ON
                        alumni.alumni_id = education.alumni_id
                    WHERE 
                        history.employment_status = 'Employed Part-Time'
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


    // public function getUnemployed()
    //     {
    //         try {
    //             $history = 'gc_history';


    //             $sql = "SELECT COUNT(*) as unemployed_count FROM $history WHERE employment_status = 'Unemployed and not currently looking for work'";

    //             $stmt = $this->pdo->query($sql);

    //             $result = $stmt->fetch(PDO::FETCH_ASSOC);

    //             $response = [
    //                 'status' => 'success',
    //                 'data' => $result,
    //                 'statusCode' => 200
    //             ];

    //             return $this->sendResponse($response, 200);
    //         } catch (\PDOException $e) {
    //             $errmsg = $e->getMessage();
    //             return $this->sendErrorResponse("Failed to retrieve: " . $errmsg, 400);
    //         }
    //     }


    public function getUnemployed()
    {
        try {
            $history = 'gc_history';
            $education = 'gc_alumni_education';

            $sql = "
                    SELECT 
                        COUNT(*) as unemployed_count,
                        education.year_graduated
                    FROM 
                        $history AS history
                    JOIN
                        $education AS education
                    ON 
                        education.alumni_id = history.alumni_id
                    WHERE 
                        employment_status = 'Unemployed and not currently looking for work'
                    GROUP BY
                        education.year_graduated
                ";

            $stmt = $this->pdo->query($sql);

            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $response = [
                'status' => 'success',
                'data' => $results,
                'statusCode' => 200
            ];

            return $this->sendResponse($response, 200);
        } catch (\PDOException $e) {
            $errmsg = $e->getMessage();
            return $this->sendErrorResponse("Failed to retrieve: " . $errmsg, 400);
        }
    }

    public function getUnemployedData()
    {
        try {
            $history = 'gc_history';
            $alumni = 'gc_alumni';
            $contact = 'gc_alumni_contact';
            $family = 'gc_alumni_family';
            $education = 'gc_alumni_education';

            $sql = "
                    SELECT 
                        alumni.alumni_id,
                        alumni.alumni_lastname,
                        alumni.alumni_firstname,
                        alumni.alumni_middlename,
                        alumni.alumni_birthday,
                        alumni.alumni_age,
                        contact.alumni_address, 
                        contact.alumni_number, 
                        contact.alumni_email,
                        family.alumni_race,
                        family.alumni_religion,
                        family.alumni_spousename,
                        family.alumni_no_of_children,
                        family.alumni_marital_status,
                        education.year_graduated,
                        education.alumni_program,
                        education.education_upgrade,
                        history.employment_status,
                        history.working_in_industry,
                        history.working_in_abroad,
                        history.years_of_experience,
                        history.response_date
                    FROM 
                        $history AS history
                    JOIN 
                        $alumni AS alumni
                    ON 
                        history.alumni_id = alumni.alumni_id
                    JOIN
                        $contact AS contact 
                    ON
                        alumni.alumni_id = contact.alumni_id
                    JOIN
                        $family AS family 
                    ON
                        alumni.alumni_id = family.alumni_id
                    JOIN
                        $education AS education 
                    ON
                        alumni.alumni_id = education.alumni_id
                    WHERE 
                        history.employment_status = 'Unemployed and not currently looking for work'
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


    // public function selfEmployed()
    //     {
    //         try {
    //             $history = 'gc_history';


    //             $sql = "SELECT COUNT(*) as selfemployed_count FROM $history WHERE employment_status = 'Self Employed'";

    //             $stmt = $this->pdo->query($sql);

    //             $result = $stmt->fetch(PDO::FETCH_ASSOC);

    //             $response = [
    //                 'status' => 'success',
    //                 'data' => $result,
    //                 'statusCode' => 200
    //             ];

    //             return $this->sendResponse($response, 200);
    //         } catch (\PDOException $e) {
    //             $errmsg = $e->getMessage();
    //             return $this->sendErrorResponse("Failed to retrieve: " . $errmsg, 400);
    //         }
    //     }


    public function selfEmployed()
    {
        try {
            $history = 'gc_history';
            $education = 'gc_alumni_education';

            $sql = "
                    SELECT 
                        COUNT(*) as selfemployed_count,
                        education.year_graduated
                    FROM 
                        $history AS history
                    JOIN
                        $education AS education
                    ON 
                        education.alumni_id = history.alumni_id
                    WHERE 
                        employment_status = 'Self Employed'
                    GROUP BY
                        education.year_graduated
                ";

            $stmt = $this->pdo->query($sql);

            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $response = [
                'status' => 'success',
                'data' => $results,
                'statusCode' => 200
            ];

            return $this->sendResponse($response, 200);
        } catch (\PDOException $e) {
            $errmsg = $e->getMessage();
            return $this->sendErrorResponse("Failed to retrieve: " . $errmsg, 400);
        }
    }

    public function getSelfEmployedData()
    {
        try {
            $history = 'gc_history';
            $alumni = 'gc_alumni';
            $contact = 'gc_alumni_contact';
            $family = 'gc_alumni_family';
            $education = 'gc_alumni_education';

            $sql = "
                    SELECT 
                        alumni.alumni_id,
                        alumni.alumni_lastname,
                        alumni.alumni_firstname,
                        alumni.alumni_middlename,
                        alumni.alumni_birthday,
                        alumni.alumni_age,
                        contact.alumni_address, 
                        contact.alumni_number, 
                        contact.alumni_email,
                        family.alumni_race,
                        family.alumni_religion,
                        family.alumni_spousename,
                        family.alumni_no_of_children,
                        family.alumni_marital_status,
                        education.year_graduated,
                        education.alumni_program,
                        education.education_upgrade,
                        history.employment_status,
                        history.working_in_industry,
                        history.working_in_abroad,
                        history.years_of_experience,
                        history.response_date
                    FROM 
                        $history AS history
                    JOIN 
                        $alumni AS alumni
                    ON 
                        history.alumni_id = alumni.alumni_id
                    JOIN
                        $contact AS contact 
                    ON
                        alumni.alumni_id = contact.alumni_id
                    JOIN
                        $family AS family 
                    ON
                        alumni.alumni_id = family.alumni_id
                    JOIN
                        $education AS education 
                    ON
                        alumni.alumni_id = education.alumni_id
                    WHERE 
                        history.employment_status = 'Self Employed'
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

    // public function lookingforWork()
    //     {
    //         try {
    //             $history = 'gc_history';


    //             $sql = "SELECT COUNT(*) as looking_count FROM $history WHERE employment_status = 'Unemployed and looking for work'";

    //             $stmt = $this->pdo->query($sql);

    //             $result = $stmt->fetch(PDO::FETCH_ASSOC);

    //             $response = [
    //                 'status' => 'success',
    //                 'data' => $result,
    //                 'statusCode' => 200
    //             ];

    //             return $this->sendResponse($response, 200);
    //         } catch (\PDOException $e) {
    //             $errmsg = $e->getMessage();
    //             return $this->sendErrorResponse("Failed to retrieve: " . $errmsg, 400);
    //         }
    //     }

    public function lookingforWork()
    {
        try {
            $history = 'gc_history';
            $education = 'gc_alumni_education';

            $sql = "
                SELECT 
                    COUNT(*) as looking_count,
                    education.year_graduated
                FROM 
                    $history AS history
                JOIN
                    $education AS education
                ON 
                    education.alumni_id = history.alumni_id
                WHERE 
                    employment_status = 'Unemployed and looking for work'
                GROUP BY
                    education.year_graduated
            ";

            $stmt = $this->pdo->query($sql);

            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $response = [
                'status' => 'success',
                'data' => $results,
                'statusCode' => 200
            ];

            return $this->sendResponse($response, 200);
        } catch (\PDOException $e) {
            $errmsg = $e->getMessage();
            return $this->sendErrorResponse("Failed to retrieve: " . $errmsg, 400);
        }
    }

    public function getLookingData()
    {
        try {
            $history = 'gc_history';
            $alumni = 'gc_alumni';
            $contact = 'gc_alumni_contact';
            $family = 'gc_alumni_family';
            $education = 'gc_alumni_education';

            $sql = "
                    SELECT 
                        alumni.alumni_id,
                        alumni.alumni_lastname,
                        alumni.alumni_firstname,
                        alumni.alumni_middlename,
                        alumni.alumni_birthday,
                        alumni.alumni_age,
                        contact.alumni_address, 
                        contact.alumni_number, 
                        contact.alumni_email,
                        family.alumni_race,
                        family.alumni_religion,
                        family.alumni_spousename,
                        family.alumni_no_of_children,
                        family.alumni_marital_status,
                        education.year_graduated,
                        education.alumni_program,
                        education.education_upgrade,
                        history.employment_status,
                        history.working_in_industry,
                        history.working_in_abroad,
                        history.years_of_experience,
                        history.response_date
                    FROM 
                        $history AS history
                    JOIN 
                        $alumni AS alumni
                    ON 
                        history.alumni_id = alumni.alumni_id
                    JOIN
                        $contact AS contact
                    ON
                        alumni.alumni_id = contact.alumni_id
                    JOIN
                        $family AS family
                    ON
                        alumni.alumni_id = family.alumni_id
                    JOIN
                        $education AS education
                    ON
                        alumni.alumni_id = education.alumni_id
                    WHERE 
                        history.employment_status = 'Unemployed and looking for work'
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

    // public function getStudent()
    // {
    //     try {
    //         $history = 'gc_history';


    //         $sql = "SELECT COUNT(*) as student_count FROM $history WHERE employment_status = 'Student'";

    //         $stmt = $this->pdo->query($sql);

    //         $result = $stmt->fetch(PDO::FETCH_ASSOC);

    //         $response = [
    //             'status' => 'success',
    //             'data' => $result,
    //             'statusCode' => 200
    //         ];

    //         return $this->sendResponse($response, 200);
    //     } catch (\PDOException $e) {
    //         $errmsg = $e->getMessage();
    //         return $this->sendErrorResponse("Failed to retrieve: " . $errmsg, 400);
    //     }
    // }


    public function getStudent()
    {
        try {
            $history = 'gc_history';
            $education = 'gc_alumni_education';

            $sql = "
                    SELECT 
                        COUNT(*) as student_count,
                        education.year_graduated
                    FROM 
                        $history AS history
                    JOIN
                        $education AS education
                    ON 
                        education.alumni_id = history.alumni_id
                    WHERE 
                        employment_status = 'Student'
                    GROUP BY
                        education.year_graduated
                ";

            $stmt = $this->pdo->query($sql);

            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $response = [
                'status' => 'success',
                'data' => $results,
                'statusCode' => 200
            ];

            return $this->sendResponse($response, 200);
        } catch (\PDOException $e) {
            $errmsg = $e->getMessage();
            return $this->sendErrorResponse("Failed to retrieve: " . $errmsg, 400);
        }
    }

    public function getStudentData()
    {
        try {
            $history = 'gc_history';
            $alumni = 'gc_alumni';
            $contact = 'gc_alumni_contact';
            $family = 'gc_alumni_family';
            $education = 'gc_alumni_education';

            $sql = "
                    SELECT 
                        alumni.alumni_id,
                        alumni.alumni_lastname,
                        alumni.alumni_firstname,
                        alumni.alumni_middlename,
                        alumni.alumni_birthday,
                        alumni.alumni_age,
                        contact.alumni_address, 
                        contact.alumni_number, 
                        contact.alumni_email,
                        family.alumni_race,
                        family.alumni_religion,
                        family.alumni_spousename,
                        family.alumni_no_of_children,
                        family.alumni_marital_status,
                        education.year_graduated,
                        education.alumni_program,
                        education.education_upgrade,
                        history.employment_status,
                        history.working_in_industry,
                        history.working_in_abroad,
                        history.years_of_experience,
                        history.response_date
                    FROM 
                        $history AS history
                    JOIN 
                        $alumni AS alumni
                    ON 
                        history.alumni_id = alumni.alumni_id
                    JOIN
                        $contact AS contact
                    ON
                        alumni.alumni_id = contact.alumni_id
                    JOIN
                        $family AS family
                    ON
                        alumni.alumni_id = family.alumni_id
                    JOIN
                        $education AS education
                    ON
                        alumni.alumni_id = education.alumni_id
                    WHERE 
                        history.employment_status = 'Student'
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

    // public function getRetired()
    // {
    //     try {
    //         $history = 'gc_history';


    //         $sql = "SELECT COUNT(*) as retired_count FROM $history WHERE employment_status = 'Retired'";

    //         $stmt = $this->pdo->query($sql);

    //         $result = $stmt->fetch(PDO::FETCH_ASSOC);

    //         $response = [
    //             'status' => 'success',
    //             'data' => $result,
    //             'statusCode' => 200
    //         ];

    //         return $this->sendResponse($response, 200);
    //     } catch (\PDOException $e) {
    //         $errmsg = $e->getMessage();
    //         return $this->sendErrorResponse("Failed to retrieve: " . $errmsg, 400);
    //     }
    // }

    public function getRetired()
    {
        try {
            $history = 'gc_history';
            $education = 'gc_alumni_education';

            $sql = "
                    SELECT 
                        COUNT(*) as retired_count,
                        education.year_graduated
                    FROM 
                        $history AS history
                    JOIN
                        $education AS education
                    ON 
                        education.alumni_id = history.alumni_id
                    WHERE 
                        employment_status = 'Retired'
                    GROUP BY
                        education.year_graduated
                ";

            $stmt = $this->pdo->query($sql);

            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $response = [
                'status' => 'success',
                'data' => $results,
                'statusCode' => 200
            ];

            return $this->sendResponse($response, 200);
        } catch (\PDOException $e) {
            $errmsg = $e->getMessage();
            return $this->sendErrorResponse("Failed to retrieve: " . $errmsg, 400);
        }
    }

    public function getRetiredData()
    {
        try {
            $history = 'gc_history';
            $alumni = 'gc_alumni';
            $contact = 'gc_alumni_contact';
            $family = 'gc_alumni_family';
            $education = 'gc_alumni_education';

            $sql = "
                    SELECT 
                        alumni.alumni_id,
                        alumni.alumni_lastname,
                        alumni.alumni_firstname,
                        alumni.alumni_middlename,
                        alumni.alumni_birthday,
                        alumni.alumni_age,
                        contact.alumni_address, 
                        contact.alumni_number, 
                        contact.alumni_email,
                        family.alumni_race,
                        family.alumni_religion,
                        family.alumni_spousename,
                        family.alumni_no_of_children,
                        family.alumni_marital_status,
                        education.year_graduated,
                        education.alumni_program,
                        education.education_upgrade,
                        history.employment_status,
                        history.working_in_industry,
                        history.working_in_abroad,
                        history.years_of_experience,
                        history.response_date
                    FROM 
                        $history AS history
                    JOIN 
                        $alumni AS alumni
                    ON 
                        history.alumni_id = alumni.alumni_id
                    JOIN
                        $contact AS contact
                    ON
                        alumni.alumni_id = contact.alumni_id
                    JOIN
                        $family AS family
                    ON
                        alumni.alumni_id = family.alumni_id
                    JOIN
                        $education AS education
                    ON
                        alumni.alumni_id = education.alumni_id
                    WHERE 
                        history.employment_status = 'Retired'
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

    public function getGlobalData()
    {
        try {
            $history = 'gc_history';
            $alumni = 'gc_alumni';
            $contact = 'gc_alumni_contact';
            $family = 'gc_alumni_family';
            $education = 'gc_alumni_education';

            $sql = "
                    SELECT 
                        alumni.alumni_id,
                        alumni.alumni_lastname,
                        alumni.alumni_firstname,
                        alumni.alumni_middlename,
                        alumni.alumni_birthday,
                        alumni.alumni_age,
                        contact.alumni_address, 
                        contact.alumni_number, 
                        contact.alumni_email,
                        family.alumni_race,
                        family.alumni_religion,
                        family.alumni_spousename,
                        family.alumni_no_of_children,
                        family.alumni_marital_status,
                        education.year_graduated,
                        education.alumni_program,
                        education.education_upgrade,
                        history.employment_status,
                        history.working_in_industry,
                        history.working_in_abroad,
                        history.years_of_experience,
                        history.response_date
                    FROM 
                        $history AS history
                    JOIN 
                        $alumni AS alumni
                    ON 
                        history.alumni_id = alumni.alumni_id
                    JOIN
                        $contact AS contact
                    ON
                        alumni.alumni_id = contact.alumni_id
                    JOIN
                        $family AS family
                    ON
                        alumni.alumni_id = family.alumni_id
                    JOIN
                        $education AS education
                    ON
                        alumni.alumni_id = education.alumni_id
                    WHERE 
                        history.working_in_abroad = 'Yes'
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

    public function getLocalData()
    {
        try {
            $history = 'gc_history';
            $alumni = 'gc_alumni';
            $contact = 'gc_alumni_contact';
            $family = 'gc_alumni_family';
            $education = 'gc_alumni_education';

            $sql = "
                    SELECT 
                        alumni.alumni_id,
                        alumni.alumni_lastname,
                        alumni.alumni_firstname,
                        alumni.alumni_middlename,
                        alumni.alumni_birthday,
                        alumni.alumni_age,
                        contact.alumni_address, 
                        contact.alumni_number, 
                        contact.alumni_email,
                        family.alumni_race,
                        family.alumni_religion,
                        family.alumni_spousename,
                        family.alumni_no_of_children,
                        family.alumni_marital_status,
                        education.year_graduated,
                        education.alumni_program,
                        education.education_upgrade,
                        history.employment_status,
                        history.working_in_industry,
                        history.working_in_abroad,
                        history.years_of_experience,
                        history.response_date
                    FROM 
                        $history AS history
                    JOIN 
                        $alumni AS alumni
                    ON 
                        history.alumni_id = alumni.alumni_id
                    JOIN
                        $contact AS contact
                    ON
                        alumni.alumni_id = contact.alumni_id
                    JOIN
                        $family AS family
                    ON
                        alumni.alumni_id = family.alumni_id
                    JOIN
                        $education AS education
                    ON
                        alumni.alumni_id = education.alumni_id
                    WHERE 
                        history.working_in_abroad = 'No'
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

    public function getIndustryData()
    {
        try {
            $history = 'gc_history';
            $alumni = 'gc_alumni';
            $contact = 'gc_alumni_contact';
            $family = 'gc_alumni_family';
            $education = 'gc_alumni_education';

            $sql = "
            SELECT 
                alumni.alumni_id,
                alumni.alumni_lastname,
                alumni.alumni_firstname,
                alumni.alumni_middlename,
                alumni.alumni_birthday,
                alumni.alumni_age,
                contact.alumni_address, 
                contact.alumni_number, 
                contact.alumni_email,
                family.alumni_race,
                family.alumni_religion,
                family.alumni_spousename,
                family.alumni_no_of_children,
                family.alumni_marital_status,
                education.year_graduated,
                education.alumni_program,
                education.education_upgrade,
                history.employment_status,
                history.working_in_industry,
                history.working_in_abroad,
                history.years_of_experience,
                history.response_date
            FROM 
                $history AS history
            JOIN 
                $alumni AS alumni
            ON 
                history.alumni_id = alumni.alumni_id
            JOIN
                $contact AS contact
            ON
                alumni.alumni_id = contact.alumni_id
            JOIN
                $family AS family
            ON
                alumni.alumni_id = family.alumni_id
            JOIN
                $education AS education
            ON
                alumni.alumni_id = education.alumni_id
            WHERE 
                history.working_in_industry = 'Yes'
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

    public function getVerification()
    {
        try {
            $alumni = 'gc_alumni';
            $contact = 'gc_alumni_contact';
            $family = 'gc_alumni_family';
            $education = 'gc_alumni_education';

            $sql = "
                SELECT 
                    alumni.alumni_id,
                    alumni.alumni_lastname,
                    alumni.alumni_firstname,
                    alumni.alumni_middlename,
                    contact.alumni_email,
                    education.alumni_program,
                    education.year_graduated

                FROM 
                    $alumni AS alumni

                JOIN
                    $contact AS contact
                ON
                    alumni.alumni_id = contact.alumni_id
                JOIN
                    $family AS family
                ON
                    alumni.alumni_id = family.alumni_id
                JOIN
                    $education AS education
                ON
                    alumni.alumni_id = education.alumni_id
                WHERE

                    alumni.isVisible = 2
                    AND alumni.alumni_lastname IS NOT NULL
            ";

            $stmt = $this->pdo->query($sql);

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);


            return $this->sendResponse($result, 200);
        } catch (\PDOException $e) {
            $errmsg = $e->getMessage();
            return $this->sendErrorResponse("Failed to retrieve: " . $errmsg, 400);
        }
    }
}
