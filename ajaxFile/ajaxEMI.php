<?php
header('Content-Type: application/json');

require_once "../classes/DB-Connection.php";
require_once "../classes/BorrowerManager.php";
require_once "../classes/EMIScheduleManager.php";
include_once "../classes/sessionManager.php";
include_once "../classes/class.Input.php";
include_once "../classes/LoanManager.php";

$sFlag = Input::request('sFlag');
$response = array('status' => 'error', 'message' => 'Invalid request');

try {
    $borrowerManage = new BorrowerManager();
    switch ($sFlag) {
        case 'fetchEMIListing':
            $iBorrowerId = Input::request('borrowerId'); // Get the loan ID from the request, if provided
            $dFromDate = Input::request('sFromDate') ?? '';
            $dToDate = Input::request('sToDate') ?? '';
            $dToDate = Input::request('sToDate') ?? '';
            $sLoanAmount = Input::request('sLoanAmount') ?? '';
            $sOnlyBorrowerId = Input::request('sOnlyBorrowerId') ?? '';
            $bOnlyPending = Input::request('sOnlyPending') ?? false;
            $emiManager = new EMIScheduleManager();

            // Fetch EMI schedules for a specific loan ID
            $oEMISchedules = $emiManager->getAllEMISchedulesByBorrowerId($iBorrowerId, $dFromDate, $dToDate, $sLoanAmount, $bOnlyPending, $sOnlyBorrowerId);
            
            if($bOnlyPending && $sOnlyBorrowerId){
                foreach ($oEMISchedules as $loan) {
                    
                    if ($loan['payment_status'] === 'pending') {
                        $dueDate = new DateTime($loan['payment_due_date']);
                        $currentDate = new DateTime();
                        // Check if payment_due_date is more than one month past
                        while ($dueDate < $currentDate) {
                            // Increment due date by one month
                            $dueDate->modify('+1 month');
                            $newRow = $loan;
                            $newRow['payment_due_date'] = $dueDate->format('Y-m-d');
                            $oEMISchedules[] = $newRow;

                            
                        }
                    }
                }
            }

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
        case 'invalidData':
            $iLoanId = Input::request('iLoanId');
            $emiManager = new EMIScheduleManager();

            // Fetch EMI schedules for a specific loan ID
            $oInvalidPending = $emiManager->invalidActiveLoan($iLoanId);

            $oLoanObj = new LoanManager();
            $oLoanDeleted = $oLoanObj->invalidLoan($iLoanId);

            if ($oLoanDeleted) {
                $response['status'] = 'success';
                $response['message'] = "Deleted";
                $response['data'] = true;
            } else {
                $response['status'] = 'error';
                $response['message'] = "error";
                $response['data'] = false;
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
