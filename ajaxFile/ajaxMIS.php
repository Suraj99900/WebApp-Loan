<?php
header('Content-Type: application/json');

require_once "../classes/DB-Connection.php";
require_once "../classes/BorrowerManager.php";
require_once "../classes/LoanManager.php";
require_once "../classes/EMIScheduleManager.php";
include_once "../classes/LoanPaymentManager.php";
include_once "../classes/RevenueReportManager.php";
include_once "../classes/MIS.php";
include_once "../classes/sessionManager.php";
include_once "../classes/class.Input.php";

$sFlag = Input::request('sFlag');
$response = array('status' => 'error', 'message' => 'Invalid request');

try {
    switch ($sFlag) {
        case 'fetchLegerReport':
            // Instantiate the MIS class
            $borrowerId = Input::request('borrowerId') ?? '';
            $startDate = Input::request('startDate') ?? '';
            $endDate = Input::request('endDate') ?? '';
            $status = Input::request('status') ?? '';
            $principalAmount = Input::request('principalAmount') ?? '';
            $mis = new MIS();
            
            // Get the revenue report
            $aRevenueReport = $mis->getRevenueReport($borrowerId, $startDate, $endDate, $status, $principalAmount);


            // Check if the report is available
            if (!empty($aRevenueReport)) {
                $response['status'] = 'success';
                $response['message'] = 'Revenue report fetched successfully';
                $response['data'] = $aRevenueReport;
                $response['recordsTotal'] = count($aRevenueReport);
                $response['recordsFiltered'] = count($aRevenueReport);
            } else {
                $response['message'] = 'No data found for the revenue report';
                $response['data'] = [];
                $response['recordsTotal'] = count([]);
                $response['recordsFiltered'] = count([]);
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
