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
                    'ending_principal' => ':ending_principal',
                    'repaid_principal' => ':repaid_principal',
                    'part_payment_status' => ':part_payment_status',
                    'closer_payment_status' => ':closer_payment_status',
                    'loan_status' => ':loan_status',
                    'status' => ':status',
                    'deleted' => ':deleted'
                ])
                ->setParameters([
                    'borrower_id' => $loanData['borrower_id'],
                    'principal_amount' => $loanData['principal_amount'],
                    'interest_rate' => $loanData['interest_rate'],
                    'loan_period' => $loanData['loan_period'],
                    'disbursed_date' => $loanData['disbursed_date'],
                    'top_up_payment' => $loanData['top_up_payment'] ?? 0,
                    'EMI_amount' => $loanData['EMI_amount'],
                    'closure_amount' => $loanData['closure_amount'],
                    'closure_date' => $loanData['closure_date'] ?? null,
                    'ending_principal' => $loanData['ending_principal'] ?? 0,
                    'repaid_principal' => $loanData['repaid_principal'] ?? 0,
                    'part_payment_status' => $loanData['part_payment_status'] ?? 0,
                    'closer_payment_status' => $loanData['closer_payment_status'] ?? 0,
                    'loan_status' => $loanData['loan_status'] ?? 'active',
                    'status' => 1,
                    'deleted' => 0
                ]);

            $oResult = $this->oQueryBuilder->executeQuery();

            if ($oResult) {
                return $this->oConnection->conn->lastInsertId();
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
            $this->oQueryBuilder->update($sTableName)
                ->set('principal_amount', ':principal_amount')
                ->set('interest_rate', ':interest_rate')
                ->set('loan_period', ':loan_period')
                ->set('disbursed_date', ':disbursed_date')
                ->set('top_up_payment', ':top_up_payment')
                ->set('EMI_amount', ':EMI_amount')
                ->set('closure_amount', ':closure_amount')
                ->set('closure_date', ':closure_date')
                ->set('ending_principal', ':ending_principal')
                ->set('repaid_principal', ':repaid_principal')
                ->set('part_payment_status', ':part_payment_status')
                ->set('closer_payment_status', ':closer_payment_status')
                ->set('loan_status', ':loan_status')
                ->set('status', ':status')
                ->set('deleted', ':deleted')
                ->where('loan_id = :loan_id')
                ->setParameters([
                    'principal_amount' => $loanData['principal_amount'],
                    'interest_rate' => $loanData['interest_rate'],
                    'loan_period' => $loanData['loan_period'],
                    'disbursed_date' => $loanData['disbursed_date'],
                    'top_up_payment' => $loanData['top_up_payment'] ?? 0,
                    'EMI_amount' => $loanData['EMI_amount'],
                    'closure_amount' => $loanData['closure_amount'],
                    'closure_date' => $loanData['closure_date'] ?? null,
                    'ending_principal' => $loanData['ending_principal'] ?? 0,
                    'repaid_principal' => $loanData['repaid_principal'] ?? 0,
                    'part_payment_status' => $loanData['part_payment_status'] ?? 0,
                    'closer_payment_status' => $loanData['closer_payment_status'] ?? 0,
                    'loan_status' => $loanData['loan_status'] ?? 'active',
                    'status' => $loanData['status'] ?? 1,
                    'deleted' => $loanData['deleted'] ?? 0,
                    'loan_id' => $loanId
                ]);

            return $this->oQueryBuilder->executeQuery() ? true : false;
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
                ->setParameter('loan_status', 'closed')
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

    public function makePayment($loanId, $paymentAmount)
    {
        $sTableName = "app_loan_details";

        try {
            // Fetch current loan details
            $query = $this->oQueryBuilder->select('*')
                ->from($sTableName)
                ->where('loan_id = :loan_id')
                ->setParameter('loan_id', $loanId);

            $loanDetails = $query->fetchAssociative();

            if (!$loanDetails) {
                throw new \Exception("Loan not found for ID: $loanId");
            }

            // Extract relevant fields
            $repaidPrincipal = $loanDetails['repaid_principal'];
            $endingPrincipal = $loanDetails['ending_principal'];
            $closureAmount = $loanDetails['closure_amount'];
            $currentStatus = $loanDetails['closer_payment_status'];

            // Validate the payment
            if ($currentStatus == 1) {
                throw new \Exception("Loan already closed for ID: $loanId");
            }

            // Update `repaid_principal` and `ending_principal`
            $newRepaidPrincipal = $repaidPrincipal + $paymentAmount;
            $newEndingPrincipal = max(0, $endingPrincipal - $paymentAmount);

            // Check if this payment closes the loan
            $isLoanClosed = ($newEndingPrincipal == 0);

            // Update loan details in the database
            $this->oQueryBuilder->update($sTableName)
                ->set('repaid_principal', ':repaid_principal')
                ->set('ending_principal', ':ending_principal')
                ->set('closer_payment_status', ':closer_payment_status')
                ->set('loan_status', ':loan_status')
                ->where('loan_id = :loan_id')
                ->setParameter('repaid_principal', $newRepaidPrincipal)
                ->setParameter('ending_principal', $newEndingPrincipal)
                ->setParameter('closer_payment_status', $isLoanClosed ? 1 : 0)
                ->setParameter('loan_status', $isLoanClosed ? 'closed' : 'active')
                ->setParameter('loan_id', $loanId);

            $oResult = $this->oQueryBuilder->executeQuery();

            // Return success if updated
            if ($oResult) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }

    public function updateLoanPrincipalRepaidAndOutstanding($loanId, $repaidPrincipal, $outstandingPrincipal)
    {
        $sTableName = "app_loan_details";

        try {
            $this->oQueryBuilder->update($sTableName)
                ->set('repaid_principal', ':repaid_principal')
                ->set('ending_principal', ':ending_principal')
                ->set('part_payment_status', ':part_payment_status')
                ->where('loan_id = :loan_id')
                ->setParameter('repaid_principal', $repaidPrincipal)
                ->setParameter('ending_principal', $outstandingPrincipal)
                ->setParameter('part_payment_status', 1)
                ->setParameter('loan_id', $loanId);

            $oResult = $this->oQueryBuilder->executeQuery();

            return $oResult ? true : false;
        } catch (\Exception $e) {
            throw new \Exception("Error updating loan details: " . $e->getMessage());
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
    public function getAllLoans($borrowerId,$iLoanId='')
    {
        $sTableName = "app_loan_details";

        try {
            // Build the query
            $this->oQueryBuilder->select('*')
                ->from($sTableName)
                ->where('borrower_id = :borrower_id')
                ->andWhere('deleted = 0')
                ->setParameter('borrower_id', $borrowerId);

            if($iLoanId != ""){
                $this->oQueryBuilder
                    ->andWhere("loan_id = :iLoanID")
                    ->setParameter("iLoanID", $iLoanId);
            }

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


    public function getLoanDetailsByBorrowerID($iBorrowerId,$iLoanId = '')
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

            if($iLoanId != '') {
                $this->oQueryBuilder
                    ->andWhere('B.loan_id = :iLoanId')
                    ->setParameter('iLoanId', $iLoanId);
            }
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
