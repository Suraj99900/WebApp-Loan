<?php
header('Content-Type: application/json');

require_once "../classes/DB-Connection.php";
require_once "../classes/BorrowerManager.php";
require_once "../classes/LoanManager.php";
require_once "../classes/EMIScheduleManager.php";
include_once "../classes/clientCode.php";
include_once "../classes/sessionManager.php";
include_once "../classes/class.Input.php";

$sFlag = Input::request('sFlag');
$response = array('status' => 'error', 'message' => 'Invalid request');

try {
    switch ($sFlag) {
        case 'addLoanDetails':
            $iBorrowerId = Input::request('borrowerId') ?: '';
            $iPrincipalAmount = Input::request('loanPrincipalAmount') ?: '';
            $iInterestRate = Input::request('interestRate') ?: '';
            $iLoanPeriod = Input::request('loanPeriod') ?: '';
            $dDisbursedDate = Input::request('disbursedDate') ?: '';
            $dClosureDate = Input::request('closureDate') ?: '';

            if ($iBorrowerId && $iPrincipalAmount && $iInterestRate && $iLoanPeriod && $dDisbursedDate && $dClosureDate) {
                // Calculate Monthly Interest
                $monthlyInterest = ($iPrincipalAmount * $iInterestRate) / 100;

                // Calculate Total Closure Amount (Principal + Total Interest over the period)
                $totalInterest = $monthlyInterest * $iLoanPeriod;
                $closure_amount = $iPrincipalAmount + $totalInterest;

                // Prepare Loan Data
                $aData = [
                    'borrower_id' => $iBorrowerId,
                    'principal_amount' => $iPrincipalAmount,
                    'interest_rate' => $iInterestRate,
                    'loan_period' => $iLoanPeriod,
                    'disbursed_date' => $dDisbursedDate,
                    'top_up_payment' => 0,
                    'EMI_amount' => $monthlyInterest, // Storing monthly interest instead of EMI
                    'closure_amount' => $closure_amount,
                    'closure_date' => $dClosureDate,
                    'loan_status' => 'active', // Default status
                    'ending_principal' => $iPrincipalAmount, // Initially, the remaining principal equals the loan amount
                    'repaid_principal' => 0, // No repayments yet
                    'part_payment_status' => 0, // No part payment yet
                    'closer_payment_status' => 0, // Loan is active
                ];

                // Insert Loan Data
                $oResultLastId = (new LoanManager())->addLoan($aData);

                if ($oResultLastId) {
                    // Generate EMI Schedule
                    $emiManager = new EMIScheduleManager();
                    $oEMIData = $emiManager->generateFirstEMI($oResultLastId, $iPrincipalAmount, $iInterestRate, $iLoanPeriod, $dDisbursedDate);
                    $scheduleId = $emiManager->addEMISchedule($oEMIData);
                }

                if ($oResultLastId) {
                    $response['status'] = 'success';
                    $response['message'] = 'Loan details added successfully.';
                } else {
                    $response['status'] = 'error';
                    $response['message'] = 'Failed to add loan details.';
                }
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Missing values.';
            }
            break;


        case 'fetchLoanDetails':
            $iBorrowerId = Input::request('borrowerId') ?: '';
            $iLoanId = Input::request('LoanId') ?: '';

            if ($iLoanId) {
                // Fetch Loan Details
                $loanDetails = (new LoanManager())->getLoanById($iLoanId);

                if ($loanDetails) {
                    $response['status'] = 'success';
                    $response['data'] = $loanDetails;
                } else {
                    $response['status'] = 'error';
                    $response['message'] = 'No loan details found.';
                }
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Missing Loan ID.';
            }
            break;

        case 'updateLoanDetails':
            $iBorrowerId = Input::request('borrowerId') ?: '';
            $iPrincipalAmount = Input::request('principalAmount') ?: '';
            $iInterestRate = Input::request('interestRate') ?: '';
            $iLoanPeriod = Input::request('loanPeriod') ?: '';
            $dDisbursedDate = Input::request('disbursedDate') ?: '';
            $dClosureDate = Input::request('closureDate') ?: '';
            $LoanId = Input::request('LoanId') ?: '';

            // Check for required input values
            if ($iBorrowerId && $iPrincipalAmount && $iInterestRate && $iLoanPeriod && $dDisbursedDate && $dClosureDate) {
                // Calculate EMI amount
                $monthlyInterest = ($iPrincipalAmount * $iInterestRate) / 100;

                // Calculate Total Closure Amount (Principal + Total Interest over the period)
                $totalInterest = $monthlyInterest * $iLoanPeriod;
                $closure_amount = $iPrincipalAmount + $totalInterest;

                $aData = [
                    'borrower_id' => $iBorrowerId,
                    'principal_amount' => $iPrincipalAmount,
                    'interest_rate' => $iInterestRate,
                    'loan_period' => $iLoanPeriod,
                    'disbursed_date' => $dDisbursedDate,
                    'closure_amount' => $closure_amount,
                    'closure_date' => $dClosureDate,
                    'EMI_amount' => $monthlyInterest,
                    'ending_principal' => $iPrincipalAmount,
                    'repaid_principal' => 0, // No repayments yet
                    'part_payment_status' => 0, // No part payment yet
                    'closer_payment_status' => 0, // Loan is active
                    'loan_status' => 'active', 
                ];

                // Update Loan Data
                $loanManager = new LoanManager();
                $oResult = $loanManager->updateLoan($LoanId, $aData);

                if ($oResult) {
                    // Generate the first EMI schedule
                    $emiManager = new EMIScheduleManager();
                    $oEMIData = $emiManager->generateFirstEMI($LoanId, $iPrincipalAmount, $iInterestRate, $iLoanPeriod, $dDisbursedDate);

                    // Update the EMI schedule
                    $emiUpdated = $emiManager->updateEMISchedule($LoanId, $oEMIData);

                    if ($emiUpdated) {
                        $response['status'] = 'success';
                        $response['message'] = 'Loan details and first EMI schedule updated successfully.';
                    } else {
                        $response['status'] = 'error';
                        $response['message'] = 'Loan details updated, but failed to update the EMI schedule.';
                    }
                } else {
                    $response['status'] = 'error';
                    $response['message'] = 'Failed to update loan details.';
                }
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Missing required values.';
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
