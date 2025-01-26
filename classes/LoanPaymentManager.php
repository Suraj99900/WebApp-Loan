<?php
require_once "../config.php";
require_once "DB-Connection.php";

final class LoanPaymentManager
{
    private $oConnection;
    private $oQueryBuilder;

    public function __construct()
    {
        $this->oConnection = new DBConnection();
        $this->oQueryBuilder = $this->oConnection->conn->createQueryBuilder();
    }

    // Add a new loan payment
    public function addLoanPayment($paymentData)
    {
        $sTableName = "app_borrower_loan_payments";

        try {
            // Build the query
            $this->oQueryBuilder->insert($sTableName)
                ->values([
                    'loan_id' => ':loan_id',
                    'payment_amount' => ':payment_amount',
                    'penalty_amount' => ':penalty_amount',
                    'referral_share_amount' => ':referral_share_amount',
                    'final_amount' => ':final_amount',
                    'received_date' => ':received_date',
                    'interest_paid' => ':interest_paid',
                    'principal_paid' => ':principal_paid',
                    'payment_status' => ':payment_status',
                    'mode_of_payment' => ':mode_of_payment',
                    'comments' => ':comments',
                    'interest_date' => ':interest_date',
                    'status' => ':status',
                    'deleted' => ':deleted'
                ])
                ->setParameter('loan_id', $paymentData['loan_id'])
                ->setParameter('payment_amount', $paymentData['payment_amount'])
                ->setParameter('penalty_amount', $paymentData['penalty_amount'])
                ->setParameter('referral_share_amount', $paymentData['referral_share_amount'])
                ->setParameter('final_amount', $paymentData['final_amount'])
                ->setParameter('received_date', $paymentData['received_date'])
                ->setParameter('interest_paid', $paymentData['interest_paid'])
                ->setParameter('principal_paid', $paymentData['principal_paid'])
                ->setParameter('payment_status', $paymentData['payment_status'])
                ->setParameter('mode_of_payment', $paymentData['mode_of_payment'])
                ->setParameter('interest_date', $paymentData['interest_date'])
                ->setParameter('comments', $paymentData['comments'])
                ->setParameter('status', 1)  // Default status as 1 (active)
                ->setParameter('deleted', 0); // Default deleted as 0 (not deleted)

            // Execute the query
            $oResult = $this->oQueryBuilder->executeQuery();

            if ($oResult) {
                $iLastInsertId = $this->oConnection->conn->lastInsertId(); // Retrieve the last inserted ID
                return $iLastInsertId;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    // Fetch loan payment by ID
    public function getLoanPaymentById($paymentId)
    {
        $sTableName = "app_borrower_loan_payments";

        try {
            // Build the query
            $this->oQueryBuilder->select('*')
                ->from($sTableName)
                ->where('payment_id = :payment_id')
                ->setParameter('payment_id', $paymentId);

            // Execute the query
            $oResult = $this->oQueryBuilder->executeQuery();
            $aRow = $oResult->fetchAssociative();

            if ($aRow) {
                return $aRow;  // Return loan payment data
            } else {
                return [];
            }
        } catch (\Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    // Update loan payment details
    public function updateLoanPayment($paymentId, $paymentData)
    {
        $sTableName = "app_borrower_loan_payments";

        try {
            // Build the query
            $this->oQueryBuilder->update($sTableName)
                ->set('payment_amount', ':payment_amount')
                ->set('penalty_amount', ':penalty_amount')
                ->set('referral_share_amount', ':referral_share_amount')
                ->set('final_amount', ':final_amount')
                ->set('received_date', ':received_date')
                ->set('interest_paid', ':interest_paid')
                ->set('principal_paid', ':principal_paid')
                ->set('payment_status', ':payment_status')
                ->set('mode_of_payment', ':mode_of_payment')
                ->set('interest_date', ':interest_date')
                ->set('status', ':status')
                ->set('deleted', ':deleted')
                ->where('payment_id = :payment_id')
                ->setParameter('payment_amount', $paymentData['payment_amount'])
                ->setParameter('penalty_amount', $paymentData['penalty_amount'])
                ->setParameter('referral_share_amount', $paymentData['referral_share_amount'])
                ->setParameter('final_amount', $paymentData['final_amount'])
                ->setParameter('received_date', $paymentData['received_date'])
                ->setParameter('interest_paid', $paymentData['interest_paid'])
                ->setParameter('principal_paid', $paymentData['principal_paid'])
                ->setParameter('payment_status', $paymentData['payment_status'])
                ->setParameter('mode_of_payment', $paymentData['mode_of_payment'])
                ->setParameter('interest_date', $paymentData['interest_date'])
                ->setParameter('status', $paymentData['status'])
                ->setParameter('deleted', $paymentData['deleted'])
                ->setParameter('payment_id', $paymentId);

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

    // Delete loan payment by ID
    public function deleteLoanPayment($paymentId)
    {
        $sTableName = "app_borrower_loan_payments";

        try {
            // Build the query
            $this->oQueryBuilder->update($sTableName)
                ->set("deleted", ":deleted")
                ->where('payment_id = :payment_id')
                ->setParameters('deleted', 1)
                ->setParameter('payment_id', $paymentId);

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

    // Get all loan payments for a specific loan
    public function getAllLoanPaymentsByLoanId($loanId)
    {
        $sTableName = "app_borrower_loan_payments";

        try {
            // Build the query
            $this->oQueryBuilder->select('*')
                ->from($sTableName)
                ->where('loan_id = :loan_id')
                ->andWhere('deleted = 0')  // Only fetch active payments
                ->setParameter('loan_id', $loanId);

            // Execute the query
            $oResult = $this->oQueryBuilder->executeQuery();
            $aRows = $oResult->fetchAllAssociative();

            if ($aRows) {
                return $aRows;  // Return all loan payments for the given loan
            } else {
                return [];
            }
        } catch (\Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    // Get all loan payments globally
    public function getAllLoanPaymentsGlobal($name = '', $dFromDate = '',$dToDate='', $paymentMode = '')
    {
        $sTableName = "app_borrower_loan_payments";
        $sJoinTableLoanDetails = "app_loan_details";
        $sJoinTableBorrowerMaster = "app_borrower_master";
    
        try {
            // Start building the query
            $this->oQueryBuilder->select('A.*,C.name, C.id AS borrower_id, C.phone_no,C.unique_borrower_id')
                ->from($sTableName, 'A')
                ->leftJoin('A', $sJoinTableLoanDetails, 'B', 'B.loan_id = A.loan_id AND B.deleted = 0')
                ->leftJoin('B', $sJoinTableBorrowerMaster, 'C', 'C.id = B.borrower_id')
                ->where('A.deleted = 0')
                ->orderBy('added_on', 'DESC');;
    
            // Add name filter if provided
            if ($name) {
                $this->oQueryBuilder->andWhere('C.name LIKE :name')
                    ->setParameter('name', '%' . $name . '%');
            }
    
            // Add payment received date filter if provided
            if ($dFromDate != '' && $dToDate != '') {
                $this->oQueryBuilder
                    ->andWhere('A.received_date between :sFromDate and :sToDate')
                    ->setParameter('sFromDate', $dFromDate)
                    ->setParameter('sToDate', $dToDate);
            }
    
            // Add payment mode filter if provided
            if ($paymentMode) {
                $this->oQueryBuilder->andWhere('A.mode_of_payment LIKE :payment_mode')
                    ->setParameter('payment_mode', '%' . $paymentMode . '%');
            }
    
            // Execute the query
            $oResult = $this->oQueryBuilder->executeQuery();
            $aRows = $oResult->fetchAllAssociative();
    
            if ($aRows) {
                return $aRows;  // Return all loan payments matching the filters
            } else {
                return [];  // No records found
            }
        } catch (\Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }
    

    public function getTotalInterestPaidByLoanId($loanId)
{
    // Define the table name
    $sTableName = 'app_borrower_loan_payments';

    try {
        // Build the query to sum the interest paid for the given loan ID
        $result = $this->oQueryBuilder->select('SUM(interest_paid) AS total_interest')
            ->from($sTableName)
            ->where('loan_id = :loan_id')
            ->andWhere('status = 1') // Consider only active (non-deleted) payments
            ->setParameter('loan_id', $loanId);

        // Execute the query
        $oResult = $this->oQueryBuilder->executeQuery();

        // Fetch the result (total interest paid)
        $totalInterest = $oResult->fetchAssociative()['total_interest'];

        // Return the total interest paid, ensuring it's a float or 0 if NULL
        return $totalInterest ? (float)$totalInterest : 0.0;
    } catch (\Exception $e) {
        // Handle any potential errors (e.g., database issues)
        die("Error: " . $e->getMessage());
    }
}

}
