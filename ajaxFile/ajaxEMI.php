<?php
header('Content-Type: application/json');

require_once "../classes/DB-Connection.php";
require_once "../classes/BorrowerManager.php";
require_once "../classes/EMIScheduleManager.php";
include_once "../classes/sessionManager.php";
include_once "../classes/class.Input.php";

$sFlag = Input::request('sFlag');
$response = array('status' => 'error', 'message' => 'Invalid request');

try {
    $borrowerManage = new BorrowerManager();
    switch ($sFlag) {
        case 'fetchEMIListing':
            $iBorrowerId = Input::request('borrowerId'); // Get the loan ID from the request, if provided
            $emiManager = new EMIScheduleManager();

            // Fetch EMI schedules for a specific loan ID
            $oEMISchedules = $emiManager->getAllEMISchedulesByBorrowerId($iBorrowerId);

            if (!empty($oEMISchedules)) {
                $response['status'] = 'success';
                $response['data'] = $oEMISchedules;
                $response['recordsTotal'] = count($oEMISchedules); // Total number of records
                $response['recordsFiltered'] = count($oEMISchedules); // Number of records after filtering (if applicable)
            } else {
                $response['status'] = 'success';
                $response['message'] = 'No EMI schedules found.';
                $response['data'] = [];
                $response['recordsTotal'] = count([]); // Total number of records
                $response['recordsFiltered'] = count([]); // Number of records after filtering (if applicable)
            }
            break;

        default:
            $response['message'] = 'Unknown request';
            break;
    }
} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

echo json_encode($response);