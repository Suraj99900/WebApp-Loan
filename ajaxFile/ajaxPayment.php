<?php
header('Content-Type: application/json');

require_once "../classes/DB-Connection.php";
require_once "../classes/BorrowerManager.php";
require_once "../classes/LoanManager.php";
require_once "../classes/EMIScheduleManager.php";
include_once "../classes/LoanPaymentManager.php";
include_once "../classes/RevenueReportManager.php";
include_once "../classes/sessionManager.php";
include_once "../classes/class.Input.php";

$sFlag = Input::request('sFlag');
$response = array('status' => 'error', 'message' => 'Invalid request');

try {
    switch ($sFlag) {
        case 'fetchPaymentDetails':
            $iBorrowerId = Input::request('borrower_id') ? Input::request('borrower_id') : '';
            if (!$iBorrowerId) {
                $response['message'] = 'Borrower ID is required.';
                break;
            }

            $oLoanManager = new LoanManager();
            $oLoanData = $oLoanManager->getLoanDetailsByBorrowerID($iBorrowerId);

            if (!empty($oLoanData)) {
                $aPendingPayments = [];
                $currentDate = date('Y-m-d'); // Get the current date
                $currentMonth = date('m');
                $currentYear = date('Y');
                $defaultDay = DEFAULT_DAY;
                $iDefaultPenalty = DEFAULT_PENALTY;

                foreach ($oLoanData as $aLoan) {
                    // Get the payment due date for the loan
                    $paymentDueDate = $aLoan['payment_due_date'];

                    // Apply penalty only if the payment due date is less than the current date 
                    // and today's date is greater than the penalty date (which is the due date + penalty period)
                    if ($paymentDueDate < $currentDate) {
                        // Compare the current date with the due date + penalty date logic
                        if (date('d') > $defaultDay) {
                            // Apply the penalty
                            $aLoan['penalty_amount'] = $iDefaultPenalty;
                        }
                    } else {
                        $aLoan['penalty_amount'] = 0; // No penalty if the payment due date is not passed
                    }

                    // Calculate referral share
                    $iRefPercentage = isset($aLoan['ref_percentage']) ? $aLoan['ref_percentage'] : 0;
                    $aLoan['refShare'] = ($aLoan['emi_amount']) * ($iRefPercentage / 100);

                    $aPendingPayments[] = $aLoan;
                }

                $response = [
                    'status' => 'success',
                    'message' => 'Pending payments fetched successfully.',
                    'data' => $aPendingPayments,
                ];
            } else {
                $response['message'] = 'No loans found for the given borrower ID.';
            }
            break;


        case 'addPayment':
            $iBorrowerId = Input::request('borrower_id') ? Input::request('borrower_id') : '';
            $iLoanId = Input::request('loan_id') ? Input::request('loan_id') : '';
            $fPaymentAmount = Input::request('payment_amount') ? Input::request('payment_amount') : 0;
            $fPenaltyAmount = Input::request('penalty_amount') ? Input::request('penalty_amount') : 0;
            $fInterest_amount = Input::request('interest_amount') ? Input::request('interest_amount') : 0;
            $fPrincipal_repaid = Input::request('principal_repaid') ? Input::request('principal_repaid') : 0;
            $fReferralShare = Input::request('referral_share') ? Input::request('referral_share') : 0;
            $sPaymentMode = Input::request('payment_mode') ? Input::request('payment_mode') : "";
            $dReceivedDate = Input::request('received_date') ? Input::request('received_date') : "";
            $dPaymentDueDate = Input::request('payment_due_date') ? Input::request('payment_due_date') : "";

            if (!$iBorrowerId || !$iLoanId || !$fPaymentAmount) {
                $response['message'] = 'Missing required fields.';
                break;
            }
            $aPaymentData = [
                "loan_id" => $iLoanId,
                "payment_amount" => $fPaymentAmount,
                "penalty_amount" => ($fPenaltyAmount ? $fPenaltyAmount : 0),
                "referral_share_amount" => $fReferralShare,
                "final_amount" => ((float)$fPaymentAmount + (float)$fPenaltyAmount) - (float)$fReferralShare,
                "received_date" => $dReceivedDate,
                "interest_paid" => $fInterest_amount,
                "principal_paid" => $fPrincipal_repaid,
                "payment_status" => 'Completed',
                "mode_of_payment" => $sPaymentMode,
                "interest_date" => $dPaymentDueDate
            ];

            // Process Payment
            $oLoanPaymentManager = new LoanPaymentManager();
            $iLastInsertId = $oLoanPaymentManager->addLoanPayment($aPaymentData);

            if ($iLastInsertId) {
                // Get Next EMI Details
                $oEMIScheduleManager = new EMIScheduleManager();
                $oLoanDetails = (new LoanManager)->getLoanById($iLoanId);
                $oLastEMIScheduleResult = $oEMIScheduleManager->getAllEMISchedulesByLoanId($iLoanId);
                $oEMIScheduleManager->updateEMIScheduleStatus($oLastEMIScheduleResult[0]['schedule_id']);


                if ((int) $oLastEMIScheduleResult[0]['ending_principal'] == 0) {
                    $oLoanManager = new LoanManager();
                    $oLoanManager->updateLoanStatus($iLoanId); // Mark loan as Done
                } else {
                    $oNextEMI = $oEMIScheduleManager->generateNextPaymentEMI($iLoanId, $oLoanDetails['principal_amount'], $oLoanDetails['interest_rate'], $oLoanDetails['loan_period'], $oLastEMIScheduleResult[0]);
                    $iScheduleId = $oEMIScheduleManager->addEMISchedule($oNextEMI);
                }

                // Calculate Revenue Report Data
                $oRevenueReport = new RevenueReportManager();
                $existingReport = $oRevenueReport->getAllRevenueReportsByLoanId($iLoanId);
                if (count($existingReport) > 0) {
                    $penaltyIncome = (float)$existingReport[0]['penalty_income'] + (float)$fPenaltyAmount;
                    $referralExpense = (float)$existingReport[0]['referral_expense'] + (float)$fReferralShare;
                    $outstandingPrincipal = $existingReport[0]['outstanding_principal'] - $fPrincipal_repaid;
                }else{
                    $penaltyIncome = $fPenaltyAmount;
                    $referralExpense = $fReferralShare;
                    $outstandingPrincipal = $oLoanDetails['principal_amount'] - $fPrincipal_repaid;
                }

                if($outstandingPrincipal < 1){
                    $outstandingPrincipal = 0;
                }

                $totalInterest = $oLoanPaymentManager->getTotalInterestPaidByLoanId($iLoanId);
                
                
                
                $netRevenue = $totalInterest + $penaltyIncome - $referralExpense;
                $emiCount = $oLastEMIScheduleResult[0]['month_no'];


                // Prepare Report Data
                $reportData = [
                    "loan_id" => $iLoanId,
                    "total_interest" => $totalInterest,
                    "penalty_income" => $penaltyIncome,
                    "referral_expense" => $referralExpense,
                    "net_revenue" => $netRevenue,
                    "emi_count" => $emiCount,
                    "outstanding_principal" => $outstandingPrincipal,
                    "calculated_on" => date('Y-m-d'),
                ];
                

                if (count($existingReport) > 0) {
                    $oRevenueReport->updateRevenueReport($existingReport[0]['report_id'], $reportData);
                } else {
                    // Add a new revenue report
                    $oRevenueReport->addRevenueReport($reportData);
                }

                $response = [
                    'status' => 'success',
                    'message' => 'Payment added successfully.',
                    'next_emi' => $oNextEMI,
                ];
            } else {
                $response['message'] = 'Failed to add payment.';
            }
            break;
        case 'fetchPaymentData':
            $iLoanId = Input::request('loan_id') ? Input::request('loan_id') : '';
            $name = Input::request('name') ?? '';
            $paymentReceivedDate = Input::request('paymentReceivedDate') ?? '';
            $paymentMode = Input::request('paymentMode') ?? '';

            // Fetch Payment Data
            $oLoanPaymentManager = new LoanPaymentManager();
            $paymentData = $oLoanPaymentManager->getAllLoanPaymentsGlobal($name, $paymentReceivedDate, $paymentMode);

            // Prepare response data
            $response['status'] = 'success';
            $response['recordsTotal'] = count($paymentData); // Total number of records
            $response['recordsFiltered'] = count($paymentData); // Number of records after filtering (if applicable)
            $response['data'] = $paymentData;
            break;


        default:
            $response['message'] = 'Unknown request';
            break;
    }
} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

echo json_encode($response);
