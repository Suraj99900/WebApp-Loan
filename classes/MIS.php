<?php
require_once "../config.php";
require_once "DB-Connection.php";

final class MIS
{
    private $oConnection;
    private $oQueryBuilder;

    // Constructor to initialize DB connection and query builder
    public function __construct()
    {
        $this->oConnection = new DBConnection();
        $this->oQueryBuilder = $this->oConnection->conn->createQueryBuilder();
    }

    // Function to get the Revenue Report
    public function getRevenueReport($borrowerId = '', $startDate = '', $endDate = '', $status = '', $principalAmount = '')
    {
        try {
            // Start building the query using the DBAL QueryBuilder
            

            // Base query
            $this->oQueryBuilder->select(
                'A.id AS borrower_id',
                'A.name AS borrower_name',
                'A.phone_no AS borrower_phone',
                'A.email AS borrower_email',
                'B.loan_id',
                'B.principal_amount',
                'B.interest_rate',
                'B.loan_period',
                'B.disbursed_date',
                'B.EMI_amount',
                'B.loan_status',
                'SUM(C.payment_amount) AS total_payments',
                'SUM(C.penalty_amount) AS total_penalties',
                'SUM(C.referral_share_amount) AS total_referral_share',
                'SUM(C.final_amount) AS total_final_amount',
                'SUM(C.interest_paid) AS total_interest_paid',
                'SUM(C.principal_paid) AS total_principal_paid',
                'D.total_interest AS total_interest_reported',
                'D.net_revenue AS net_revenue_reported',
                'D.outstanding_principal AS outstanding_principal_reported',
                'D.emi_count AS emi_count_reported',
                'D.calculated_on AS revenue_report_date'
            )
                ->from('app_borrower_master', 'A')
                ->join('A', 'app_loan_details', 'B', 'A.id = B.borrower_id')
                ->leftJoin('B', 'app_borrower_loan_payments', 'C', 'B.loan_id = C.loan_id')
                ->leftJoin('B', 'app_revenue_report', 'D', 'B.loan_id = D.loan_id')
                ->where('A.status = 1')
                ->andWhere('B.status = 1')
                ->groupBy('A.id, B.loan_id, D.report_id')
                ->orderBy('D.calculated_on', 'DESC');

            // Add filters dynamically
            if (!empty($borrowerId)) {
                $this->oQueryBuilder->andWhere('A.id = :borrowerId')->setParameter('borrowerId', $borrowerId);
            }
            if (!empty($startDate) && !empty($endDate)) {
                $this->oQueryBuilder->andWhere('D.calculated_on BETWEEN :startDate AND :endDate')
                    ->setParameter('startDate', $startDate)
                    ->setParameter('endDate', $endDate);
            }
            if (!empty($status)) {
                $this->oQueryBuilder->andWhere('B.loan_status = :status')->setParameter('status', $status);
            }
            if (!empty($principalAmount)) {
                $this->oQueryBuilder->andWhere('B.principal_amount >= :principalAmount')->setParameter('principalAmount', $principalAmount);
            }

            // Execute the query
            $oResult = $this->oQueryBuilder->executeQuery();

            // Fetch results
            $aRows = $oResult->fetchAllAssociative();

            return $aRows ?: [];
        } catch (\Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }



    // Additional methods can be added here based on requirements for MIS

}
