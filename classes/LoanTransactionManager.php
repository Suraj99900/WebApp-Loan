<?php
require_once "../config.php";
require_once "DB-Connection.php";

final class LoanTransactionManager
{
    private $oConnection;
    private $oQueryBuilder;

    public function __construct()
    {
        $this->oConnection = new DBConnection();
        $this->oQueryBuilder = $this->oConnection->conn->createQueryBuilder();
    }

    // Add a new loan transaction
    public function addLoanTransaction($transactionData)
    {
        $sTableName = "app_loan_transactions";

        try {
            // Build the query
            $this->oQueryBuilder->insert($sTableName)
                ->values([
                    'loan_id' => ':loan_id',
                    'transaction_type' => ':transaction_type',
                    'amount' => ':amount',
                    'transaction_date' => ':transaction_date',
                    'status' => ':status',
                    'deleted' => ':deleted'
                ])
                ->setParameter('loan_id', $transactionData['loan_id'])
                ->setParameter('transaction_type', $transactionData['transaction_type'])
                ->setParameter('amount', $transactionData['amount'])
                ->setParameter('transaction_date', $transactionData['transaction_date'])
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

    // Fetch loan transaction by ID
    public function getLoanTransactionById($transactionId)
    {
        $sTableName = "app_loan_transactions";

        try {
            // Build the query
            $this->oQueryBuilder->select('*')
                ->from($sTableName)
                ->where('transaction_id = :transaction_id')
                ->setParameter('transaction_id', $transactionId);

            // Execute the query
            $oResult = $this->oQueryBuilder->executeQuery();
            $aRow = $oResult->fetchAssociative();

            if ($aRow) {
                return $aRow;  // Return loan transaction data
            } else {
                return [];
            }
        } catch (\Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    // Update loan transaction details
    public function updateLoanTransaction($transactionId, $transactionData)
    {
        $sTableName = "app_loan_transactions";

        try {
            // Build the query
            $this->oQueryBuilder->update($sTableName)
                ->set('transaction_type', ':transaction_type')
                ->set('amount', ':amount')
                ->set('transaction_date', ':transaction_date')
                ->set('status', ':status')
                ->set('deleted', ':deleted')
                ->where('transaction_id = :transaction_id')
                ->setParameter('transaction_type', $transactionData['transaction_type'])
                ->setParameter('amount', $transactionData['amount'])
                ->setParameter('transaction_date', $transactionData['transaction_date'])
                ->setParameter('status', $transactionData['status'])
                ->setParameter('deleted', $transactionData['deleted'])
                ->setParameter('transaction_id', $transactionId);

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

    // Delete loan transaction by ID
    public function deleteLoanTransaction($transactionId)
    {
        $sTableName = "app_loan_transactions";

        try {
            // Build the query
            $this->oQueryBuilder->update($sTableName)
                ->set("deleted", ":deleted")
                ->where('transaction_id = :transaction_id')
                ->setParameter('deleted', 1)
                ->setParameter('transaction_id', $transactionId);

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

    // Get all loan transactions for a specific loan
    public function getAllLoanTransactionsByLoanId($loanId)
    {
        $sTableName = "app_loan_transactions";

        try {
            // Build the query
            $this->oQueryBuilder->select('*')
                ->from($sTableName)
                ->where('loan_id = :loan_id')
                ->andWhere('deleted = 0')  // Only fetch active transactions
                ->setParameter('loan_id', $loanId);

            // Execute the query
            $oResult = $this->oQueryBuilder->executeQuery();
            $aRows = $oResult->fetchAllAssociative();

            if ($aRows) {
                return $aRows;  // Return all loan transactions for the given loan
            } else {
                return [];
            }
        } catch (\Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    // Get all loan transactions globally
    public function getAllLoanTransactionsGlobal()
    {
        $sTableName = "app_loan_transactions";

        try {
            // Build the query
            $this->oQueryBuilder->select('*')
                ->from($sTableName)
                ->where('deleted = 0'); // Only fetch non-deleted records

            // Execute the query
            $oResult = $this->oQueryBuilder->executeQuery();
            $aRows = $oResult->fetchAllAssociative();

            if ($aRows) {
                return $aRows;  // Return all loan transactions
            } else {
                return [];
            }
        } catch (\Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }
}
