<?php
require_once "../config.php";
require_once "DB-Connection.php";

final class RevenueReportManager
{
    private $oConnection;
    private $oQueryBuilder;

    public function __construct()
    {
        $this->oConnection = new DBConnection();
        $this->oQueryBuilder = $this->oConnection->conn->createQueryBuilder();
    }

    // Add a new revenue report
    public function addRevenueReport($reportData)
    {
        $sTableName = "app_revenue_report";

        try {
            // Build the query
            $this->oQueryBuilder->insert($sTableName)
                ->values([
                    'loan_id' => ':loan_id',
                    'total_interest' => ':total_interest',
                    'penalty_income' => ':penalty_income',
                    'referral_expense' => ':referral_expense',
                    'net_revenue' => ':net_revenue',
                    'emi_count' => ':emi_count',
                    'outstanding_principal' => ':outstanding_principal',
                    'calculated_on' => ':calculated_on',
                    'status' => ':status',
                    'deleted' => ':deleted'
                ])
                ->setParameter('loan_id', $reportData['loan_id'])
                ->setParameter('total_interest', $reportData['total_interest'])
                ->setParameter('penalty_income', $reportData['penalty_income'])
                ->setParameter('referral_expense', $reportData['referral_expense'])
                ->setParameter('net_revenue', $reportData['net_revenue'])
                ->setParameter('emi_count', $reportData['emi_count'])
                ->setParameter('outstanding_principal', $reportData['outstanding_principal'])
                ->setParameter('calculated_on', $reportData['calculated_on'])
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

    // Fetch revenue report by ID
    public function getRevenueReportById($reportId)
    {
        $sTableName = "app_revenue_report";

        try {
            // Build the query
            $this->oQueryBuilder->select('*')
                ->from($sTableName)
                ->where('report_id = :report_id')
                ->setParameter('report_id', $reportId);

            // Execute the query
            $oResult = $this->oQueryBuilder->executeQuery();
            $aRow = $oResult->fetchAssociative();

            if ($aRow) {
                return $aRow;  // Return revenue report data
            } else {
                return [];
            }
        } catch (\Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    // Update revenue report details
    public function updateRevenueReport($reportId, $reportData)
    {
        
        $sTableName = "app_revenue_report";

        try {
            // Build the query
            $this->oQueryBuilder->update($sTableName)
                ->set('total_interest', ':total_interest')
                ->set('penalty_income', ':penalty_income')
                ->set('referral_expense', ':referral_expense')
                ->set('net_revenue', ':net_revenue')
                ->set('emi_count', ':emi_count')
                ->set('outstanding_principal', ':outstanding_principal')
                ->set('calculated_on', ':calculated_on')
                ->set('status', ':status')
                ->set('deleted', ':deleted')
                ->where('report_id = :report_id')
                ->setParameter('total_interest', $reportData['total_interest'])
                ->setParameter('penalty_income', $reportData['penalty_income'])
                ->setParameter('referral_expense', $reportData['referral_expense'])
                ->setParameter('net_revenue', $reportData['net_revenue'])
                ->setParameter('emi_count', $reportData['emi_count'])
                ->setParameter('outstanding_principal', $reportData['outstanding_principal'])
                ->setParameter('calculated_on', $reportData['calculated_on'])
                ->setParameter('status', 1)
                ->setParameter('deleted', 0)
                ->setParameter('report_id', $reportId);

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
    

    // Delete revenue report by ID
    public function deleteRevenueReport($reportId)
    {
        $sTableName = "app_revenue_report";

        try {
            // Build the query
            $this->oQueryBuilder->update($sTableName)
                ->set("deleted", ":deleted")
                ->where('report_id = :report_id')
                ->setParameter('deleted', 1)
                ->setParameter('report_id', $reportId);

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

    // Get all revenue reports for a specific loan
    public function getAllRevenueReportsByLoanId($loanId)
    {
        $sTableName = "app_revenue_report";

        try {
            // Build the query
            $this->oQueryBuilder->select('*')
                ->from($sTableName)
                ->where('loan_id = :loan_id')
                ->andWhere('deleted = 0')  // Only fetch active revenue reports
                ->setParameter('loan_id', $loanId);

            // Execute the query
            $oResult = $this->oQueryBuilder->executeQuery();
            $aRows = $oResult->fetchAllAssociative();

            if ($aRows) {
                return $aRows;  // Return all revenue reports for the given loan
            } else {
                return [];
            }
        } catch (\Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    // Get all revenue reports globally
    public function getAllRevenueReportsGlobal()
    {
        $sTableName = "app_revenue_report";

        try {
            // Build the query
            $this->oQueryBuilder->select('*')
                ->from($sTableName)
                ->where('deleted = 0'); // Only fetch non-deleted records

            // Execute the query
            $oResult = $this->oQueryBuilder->executeQuery();
            $aRows = $oResult->fetchAllAssociative();

            if ($aRows) {
                return $aRows;  // Return all revenue reports
            } else {
                return [];
            }
        } catch (\Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }
}
