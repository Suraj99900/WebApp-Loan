<?php
require_once "../config.php";
require_once "DB-Connection.php";

final class EMIScheduleManager
{
    private $oConnection;
    private $oQueryBuilder;

    public function __construct()
    {
        $this->oConnection = new DBConnection();
        $this->oQueryBuilder = $this->oConnection->conn->createQueryBuilder();
    }

    // Add a new EMI schedule entry
    public function addEMISchedule($emiData)
    {
        $sTableName = "app_loan_emi_schedule";

        try {
            // Build the query
            $this->oQueryBuilder->insert($sTableName)
                ->values([
                    'loan_id' => ':loan_id',
                    'month_no' => ':month_no',
                    'beginning_principal' => ':beginning_principal',
                    'interest_amount' => ':interest_amount',
                    'emi_amount' => ':emi_amount',
                    'principal_repaid' => ':principal_repaid',
                    'ending_principal' => ':ending_principal',
                    'payment_due_date' => ':payment_due_date',
                    'penalty_amount' => ':penalty_amount',
                    'payment_status' => ':payment_status',
                    'status' => ':status',
                    'deleted' => ':deleted'
                ])
                ->setParameter('loan_id', $emiData['loan_id'])
                ->setParameter('month_no', $emiData['month_no'])
                ->setParameter('beginning_principal', $emiData['beginning_principal'])
                ->setParameter('interest_amount', $emiData['interest_amount'])
                ->setParameter('emi_amount', $emiData['emi_amount'])
                ->setParameter('principal_repaid', $emiData['principal_repaid'])
                ->setParameter('ending_principal', $emiData['ending_principal'])
                ->setParameter('payment_due_date', $emiData['payment_due_date'])
                ->setParameter('penalty_amount', $emiData['penalty_amount'] ?? 0)
                ->setParameter('payment_status', $emiData['payment_status'] ?? 'pending')
                ->setParameter('status', 1)  // Default status as 1 (active)
                ->setParameter('deleted', 0); // Default deleted as 0 (not deleted)

            // Execute the query
            $oResult = $this->oQueryBuilder->executeQuery();

            if ($oResult) {
                $lastInsertId = $this->oConnection->conn->lastInsertId(); // Retrieve the last inserted ID
                return $lastInsertId;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    public function generateNewEMIForSameLoan($loanId, $fNewLoanAmount, $fInterestRate, $iLoanTenure, $aLastEMI){
        // Use the ending principal of the previous EMI as the beginning principal
        $fBeginningPrincipal = $aLastEMI['ending_principal'];
        $iNewEndingPrincipal = (float)$aLastEMI['ending_principal'] - (float)$fNewLoanAmount;
        $iNewPrincipalRepaid = (float)$aLastEMI['principal_repaid'] + (float)$fNewLoanAmount;
         // Calculate interest amount using the  formula
        $iInterestAmount = ($iNewEndingPrincipal * $fInterestRate) / 100;

        $aEmiData = [
            'loan_id' => $loanId,
            'month_no' => $aLastEMI['month_no'],
            'beginning_principal' => round($fBeginningPrincipal, 2),
            'interest_amount' => round($iInterestAmount, 2),
            'emi_amount' => round($iInterestAmount, 2),
            'principal_repaid' => $iNewPrincipalRepaid,
            'ending_principal' => $iNewEndingPrincipal,
            'payment_due_date' => $aLastEMI['payment_due_date'],
            'penalty_amount' => $aLastEMI['penalty_amount'],
            'payment_status' => 'pending'
        ];

        return $aEmiData;
    }

    public function generateFirstEMI($loanId, $loanAmount, $interestRate, $loanTenure, $loanStartDate)
    {
        // Calculate EMI
        $emiSchedule = [];
        $remainingPrincipal = $loanAmount;

        // Convert loan start date to DateTime object
        $loanStartDateTime = new DateTime($loanStartDate);
        $iMonthlyInterest = ($remainingPrincipal * $interestRate) / 100;

        $loanStartDateTime = new DateTime($loanStartDate);
        $loanStartDateTime->modify('first day of next month'); // Move to the first day of the next month
        $loanStartDateTime->setDate(
            $loanStartDateTime->format('Y'),
            $loanStartDateTime->format('m'),
            DEFAULT_DAY // Set the day to 10
        );

        // Format the date to 'Y-m-d'
        $paymentDueDate = $loanStartDateTime->format('Y-m-d');

        $emiData = [
            'loan_id' => $loanId,
            'month_no' => 1,
            'beginning_principal' => $loanAmount,
            'interest_amount' => $iMonthlyInterest,
            'emi_amount' => $iMonthlyInterest,
            'principal_repaid' => 0,
            'ending_principal' => $remainingPrincipal,
            'payment_due_date' => $paymentDueDate,
            'penalty_amount' => 0,
            'payment_status' => 'pending',
        ];

        return $emiData;
    }


    // Add this method to handle subsequent EMI generation

    public function generateNextPaymentEMI($loanId, $loanAmount, $interestRate, $loanTenure, $lastEMI)
    {
        // Use the ending principal of the previous EMI as the beginning principal
        $beginningPrincipal = $lastEMI['ending_principal'];

        // Calculate interest amount using the new formula
        $interestAmount = ($beginningPrincipal * $interestRate) / 100;

        // EMI amount is equal to the interest amount (based on your first EMI logic)
        $emiAmount = $interestAmount;



        // Calculate the next due date (one month from the last EMI's due date)
        $nextDueDate = date('Y-m-d', strtotime("+1 month", strtotime($lastEMI['payment_due_date'])));

        // Default penalty amount if the payment is overdue
        $penaltyAmount = 0;
        if (strtotime($nextDueDate) < strtotime(date('Y-m-d'))) {
            $penaltyAmount = ($interestAmount * DEFAULT_PENALTY) / 100; // Apply default penalty if the due date is in the past
        }

        // Check if referral percentage exists and calculate referral share
        $referralShare = isset($lastEMI['ref_percentage']) ? ($emiAmount * $lastEMI['ref_percentage'] / 100) : 0;

        // Prepare EMI data for the next payment
        $emiData = [
            'loan_id' => $loanId,
            'month_no' => $lastEMI['month_no'] + 1,
            'beginning_principal' => round($beginningPrincipal, 2),
            'interest_amount' => round($interestAmount, 2),
            'emi_amount' => round($emiAmount, 2),
            'principal_repaid' => 0,
            'ending_principal' => $beginningPrincipal,
            'payment_due_date' => $nextDueDate,
            'penalty_amount' => $penaltyAmount,
            'payment_status' => 'pending',
            'referral_share' => round($referralShare, 2),
        ];

        return $emiData;
    }


    public function calculateEMI($loanAmount, $interestRate, $loanTenure)
    {
        // Convert the annual interest rate to a monthly interest rate
        $monthlyInterestRate = ($interestRate / 12) / 100;

        // Number of monthly installments
        $numOfInstallments = $loanTenure;

        // EMI Calculation using the formula
        if ($monthlyInterestRate > 0) {
            $emi = ($loanAmount * $monthlyInterestRate * pow(1 + $monthlyInterestRate, $numOfInstallments)) / (pow(1 + $monthlyInterestRate, $numOfInstallments) - 1);
        } else {
            // If interest rate is zero, simply divide the loan by number of installments
            $emi = $loanAmount / $numOfInstallments;
        }

        // Round to 2 decimal places for monetary value
        return round($emi, 2);
    }



    // Fetch EMI schedule by schedule ID
    public function getEMIScheduleById($scheduleId)
    {
        $sTableName = "app_loan_emi_schedule";

        try {
            // Build the query
            $this->oQueryBuilder->select('*')
                ->from($sTableName)
                ->where('schedule_id = :schedule_id')
                ->setParameter('schedule_id', $scheduleId);

            // Execute the query
            $oResult = $this->oQueryBuilder->executeQuery();
            $aRow = $oResult->fetchAssociative();

            if ($aRow) {
                return $aRow;  // Return EMI schedule data
            } else {
                return [];
            }
        } catch (\Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    // Update EMI schedule entry
    public function updateEMISchedule($scheduleId, $emiData)
    {
        $sTableName = "app_loan_emi_schedule";

        try {
            // Build the query
            $this->oQueryBuilder->update($sTableName)
                ->set('month_no', ':month_no')
                ->set('beginning_principal', ':beginning_principal')
                ->set('interest_amount', ':interest_amount')
                ->set('emi_amount', ':emi_amount')
                ->set('principal_repaid', ':principal_repaid')
                ->set('ending_principal', ':ending_principal')
                ->set('payment_due_date', ':payment_due_date')
                ->set('penalty_amount', ':penalty_amount')
                ->set('payment_status', ':payment_status')
                ->set('status', ':status')
                ->set('deleted', ':deleted')
                ->where('loan_id = :loan_id')
                ->andWhere('status = :checkStatus')
                ->andWhere('deleted = :checkDeleted')
                ->setParameter('checkStatus',1)
                ->setParameter('checkDeleted',0)
                ->setParameter('month_no', $emiData['month_no'])
                ->setParameter('beginning_principal', $emiData['beginning_principal'])
                ->setParameter('interest_amount', $emiData['interest_amount'])
                ->setParameter('emi_amount', $emiData['emi_amount'])
                ->setParameter('principal_repaid', $emiData['principal_repaid'])
                ->setParameter('ending_principal', $emiData['ending_principal'])
                ->setParameter('payment_due_date', $emiData['payment_due_date'])
                ->setParameter('penalty_amount', $emiData['penalty_amount'])
                ->setParameter('payment_status', $emiData['payment_status'])
                ->setParameter('status', $emiData['status'] ?? 1)
                ->setParameter('deleted', $emiData['deleted'] ?? 0)
                ->setParameter('loan_id', $emiData['loan_id']);

            $oResult = $this->oQueryBuilder->executeQuery();

            if ($oResult) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    public function updateEMIScheduleStatus($iScheduleId)
    {
        $sTableName = "app_loan_emi_schedule";

        try {
            // Build the query
            $this->oQueryBuilder->update($sTableName)
                ->set('payment_status', ':payment_status')
                ->set('status', ':status')
                ->where('schedule_id = :schedule_id')
                ->setParameter('payment_status', 'Completed')
                ->setParameter('status', 0)
                ->setParameter('schedule_id', $iScheduleId);

            $oResult = $this->oQueryBuilder->executeQuery();

            if ($oResult) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    // Delete EMI schedule entry by schedule ID
    public function deleteEMISchedule($scheduleId)
    {
        $sTableName = "app_loan_emi_schedule";

        try {
            // Build the query
            $this->oQueryBuilder->update($sTableName)
                ->set("deleted", ":deleted")
                ->where('schedule_id = :schedule_id')
                ->setParameters('deleted', 1)
                ->setParameter('schedule_id', $scheduleId);

            // Execute the query
            $oResult = $this->oQueryBuilder->executeQuery();

            if ($oResult) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    // Get all EMI schedules for a loan
    public function getAllEMISchedulesByLoanId($iLoanId)
    {
        $sTableName = "app_loan_emi_schedule";

        try {
            // Build the query
            $this->oQueryBuilder->select('*')
                ->from($sTableName)
                ->where('loan_id = :loan_id')
                ->andWhere('deleted = 0')
                ->andWhere('payment_status = :sPayment_status')
                ->andWhere('status = 1')
                ->setParameter('sPayment_status', 'pending')
                ->setParameter('loan_id', $iLoanId);

            // Execute the query
            $oResult = $this->oQueryBuilder->executeQuery();
            $aRows = $oResult->fetchAllAssociative();

            if ($aRows) {
                return $aRows;  // Return all EMI schedules
            } else {
                return [];
            }
        } catch (\Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    public function getAllEMISchedulesByBorrowerId($iBorrowerId = '')
    {
        try {
            // Build the query with JOINs
            $this->oQueryBuilder->select('B.*, C.*,A.*')
                ->from('app_loan_emi_schedule', 'A')
                ->leftJoin('A', 'app_loan_details', 'B', 'B.loan_id = A.loan_id AND A.deleted = 0')
                ->leftJoin('B', 'app_borrower_master', 'C', 'C.id = B.borrower_id AND C.deleted = 0')
                ->where('A.deleted = 0')
                ->orderBy('A.payment_status','DESC');
                
                
                if($iBorrowerId == ''){
                    $this->oQueryBuilder
                        ->andWhere('A.payment_status = :sPaymentStatus')
                        ->setParameter('sPaymentStatus', "pending");
                }else{
                    $this->oQueryBuilder
                        ->andWhere('C.id = :borrower_id')
                        ->setParameter('borrower_id', $iBorrowerId);
                }
            // Execute the query
            $oResult = $this->oQueryBuilder->executeQuery();
            $aRows = $oResult->fetchAllAssociative();

            if ($aRows) {
                return $aRows;  // Return all EMI schedules
            } else {
                return [];
            }
        } catch (\Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    public function closedActiveEMIScheduleByLoanId($iLoanId)
    {
        $sTableName = "app_loan_emi_schedule";
        try {

            // Build the query
            $this->oQueryBuilder->update($sTableName)
                ->set("deleted", ":deleted")
                ->set("status", ":updateStatus")
                ->where('loan_id = :loan_id')
                ->andWhere('status = :status')
                ->setParameter('status', 1)
                ->setParameter('deleted', 1)
                ->setParameter('updateStatus', 0)
                ->setParameter('loan_id', $iLoanId);

            // Execute the query
            $oResult = $this->oQueryBuilder->executeQuery();

            if ($oResult) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            die("Error :" . $e->getMessage());
        }
    }




    // Get all EMI schedules globally
    public function getAllEMISchedulesGlobal()
    {
        $sTableName = "app_loan_emi_schedule";

        try {
            // Build the query
            $this->oQueryBuilder->select('*')
                ->from($sTableName)
                ->where('deleted = 0'); // Only fetch non-deleted EMI schedules

            // Execute the query
            $oResult = $this->oQueryBuilder->executeQuery();
            $aRows = $oResult->fetchAllAssociative();

            if ($aRows) {
                return $aRows; // Return all EMI schedules
            } else {
                return [];
            }
        } catch (\Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }
}
