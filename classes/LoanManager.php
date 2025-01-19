<?php
require_once "../config.php";
require_once "DB-Connection.php";

final class LoanManager
{
    private $oConnection;
    private $oQueryBuilder;

    public function __construct()
    {
        $this->oConnection = new DBConnection();
        $this->oQueryBuilder = $this->oConnection->conn->createQueryBuilder();
    }

    // Add a new loan
    public function addLoan($loanData)
    {
        $sTableName = "app_loan_details";

        try {
            // Build the query
            $this->oQueryBuilder->insert($sTableName)
                ->values([
                    'borrower_id' => ':borrower_id',
                    'principal_amount' => ':principal_amount',
                    'interest_rate' => ':interest_rate',
                    'loan_period' => ':loan_period',
                    'disbursed_date' => ':disbursed_date',
                    'top_up_payment' => ':top_up_payment',
                    'EMI_amount' => ':EMI_amount',
                    'closure_amount' => ':closure_amount',
                    'closure_date' => ':closure_date',
                    'loan_status' => ':loan_status',
                    'status' => ':status',
                    'deleted' => ':deleted'
                ])
                ->setParameter('borrower_id', $loanData['borrower_id'])
                ->setParameter('principal_amount', $loanData['principal_amount'])
                ->setParameter('interest_rate', $loanData['interest_rate'])
                ->setParameter('loan_period', $loanData['loan_period'])
                ->setParameter('disbursed_date', $loanData['disbursed_date'])
                ->setParameter('top_up_payment', $loanData['top_up_payment'] ?? 0)
                ->setParameter('EMI_amount', $loanData['EMI_amount'])
                ->setParameter('closure_amount', $loanData['closure_amount'])
                ->setParameter('closure_date', $loanData['closure_date'] ?? null)
                ->setParameter('loan_status', $loanData['loan_status'] ?? 'active')
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

    // Fetch loan by ID
    public function getLoanById($loanId)
    {
        $sTableName = "app_loan_details";

        try {
            // Build the query
            $this->oQueryBuilder->select('*')
                ->from($sTableName)
                ->where('loan_id = :loan_id')
                ->andWhere('deleted = 0')
                ->setParameter('loan_id', $loanId);

            // Execute the query
            $oResult = $this->oQueryBuilder->executeQuery();
            $aRow = $oResult->fetchAssociative();

            if ($aRow) {
                return $aRow;  // Return loan data
            } else {
                return [];
            }
        } catch (\Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    // Update loan details
    public function updateLoan($loanId, $loanData)
    {
        $sTableName = "app_loan_details";

        try {
            // Build the query
            $this->oQueryBuilder->update($sTableName)
                ->set('principal_amount', ':principal_amount')
                ->set('interest_rate', ':interest_rate')
                ->set('loan_period', ':loan_period')
                ->set('disbursed_date', ':disbursed_date')
                ->set('top_up_payment', ':top_up_payment')
                ->set('EMI_amount', ':EMI_amount')
                ->set('closure_amount', ':closure_amount')
                ->set('closure_date', ':closure_date')
                ->set('loan_status', ':loan_status')
                ->set('status', ':status')
                ->set('deleted', ':deleted')
                ->where('loan_id = :loan_id')
                ->setParameter('principal_amount', $loanData['principal_amount'])
                ->setParameter('interest_rate', $loanData['interest_rate'])
                ->setParameter('loan_period', $loanData['loan_period'])
                ->setParameter('disbursed_date', $loanData['disbursed_date'])
                ->setParameter('top_up_payment', $loanData['top_up_payment'] ?? 0)
                ->setParameter('EMI_amount', $loanData['EMI_amount'])
                ->setParameter('closure_amount', $loanData['closure_amount'])
                ->setParameter('closure_date', $loanData['closure_date'] ?? null)
                ->setParameter('loan_status', $loanData['loan_status'] ?? 'active')
                ->setParameter('status', $loanData['status'] ?? 1)
                ->setParameter('deleted', $loanData['deleted'] ?? 0)
                ->setParameter('loan_id', $loanId);

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

    public function updateLoanStatus($loanId)
    {
        $sTableName = "app_loan_details";

        try {
            // Build the query
            $this->oQueryBuilder->update($sTableName)
                ->set('loan_status', ':loan_status')
                ->where('loan_id = :loan_id')
                ->setParameter('loan_status', 'Done')
                ->setParameter('loan_id', $loanId);

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

    // Delete loan by ID
    public function invalidLoan($loanId)
    {
        $sTableName = "app_loan_details";

        try {
            // Build the query
            $this->oQueryBuilder->update($sTableName)
                ->set("deleted", ":deleted")
                ->where('loan_id = :loan_id')
                ->setParameters('deleted', 1)
                ->setParameter('loan_id', $loanId);

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

    // Get all loans for a borrower
    public function getAllLoans($borrowerId)
    {
        $sTableName = "app_loan_details";

        try {
            // Build the query
            $this->oQueryBuilder->select('*')
                ->from($sTableName)
                ->where('borrower_id = :borrower_id')
                ->andWhere('deleted = 0')  // Only fetch active loans
                ->setParameter('borrower_id', $borrowerId);

            // Execute the query
            $oResult = $this->oQueryBuilder->executeQuery();
            $aRows = $oResult->fetchAllAssociative();

            if ($aRows) {
                return $aRows;  // Return all loans
            } else {
                return [];
            }
        } catch (\Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    // Get all loans (globally)
    public function getAllLoansGlobal()
    {
        $sTableName = "app_loan_details";

        try {
            // Build the query
            $this->oQueryBuilder->select('*')
                ->from($sTableName)
                ->where('deleted = 0'); // Only fetch non-deleted loans

            // Execute the query
            $oResult = $this->oQueryBuilder->executeQuery();
            $aRows = $oResult->fetchAllAssociative();

            if ($aRows) {
                return $aRows; // Return all loans
            } else {
                return [];
            }
        } catch (\Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }


    public function getLoanDetailsByBorrowerID($iBorrowerId)
    {
        $sBorrowerTable = "app_borrower_master";
        $sLoanDetailTable = "app_loan_details";
        $sEmiScheduleTable = "app_loan_emi_schedule";
        $sRefMapTable = "app_borrower_ref_map";
        $sReferralUserTable = "app_referral_user";
    
        try {
            // Build the query
            $this->oQueryBuilder->select(
                    '*',
                    'A.status AS status_borrower',
                    'A.deleted AS deleted_borrower'
                )
                ->from($sBorrowerTable, 'A')
                ->leftJoin('A', $sLoanDetailTable, 'B', 'A.id = B.borrower_id')
                ->leftJoin('B', $sEmiScheduleTable, 'C', 'B.loan_id = C.loan_id')
                ->leftJoin('A', $sRefMapTable, 'D', 'D.borrower_id = A.id AND D.deleted = 0')
                ->leftJoin('D', $sReferralUserTable, 'E', 'E.reference_Id = D.reference_Id AND E.deleted = 0')
                ->where('A.deleted = 0')
                ->andWhere('A.status = 1')
                ->andWhere('B.deleted = 0')
                ->andWhere('C.status = 1')
                ->andWhere("C.payment_status = 'pending'")
                ->andWhere('A.id = :borrowerId')
                ->setParameter('borrowerId', $iBorrowerId);
            // Execute the query
            $oResult = $this->oQueryBuilder->executeQuery();
            $aRows = $oResult->fetchAllAssociative();
    
            // Process the results
            if (!empty($aRows)) {
                return $aRows; // Return processed data
            } else {
                return [];
            }
        } catch (\Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }
    
}
