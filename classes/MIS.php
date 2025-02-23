<?php
require_once "../config.php";
require_once "DB-Connection.php";

final class MIS
{
    private $oConnection;

    // Constructor to initialize DB connection and query builder
    public function __construct()
    {
        $this->oConnection = new DBConnection();
    }

    // Function to get the Revenue Report
    public function getRevenueReport($borrowerId = '', $startDate = '', $endDate = '', $status = '', $principalAmount = '')
    {
        try {
            // Start building the query using the DBAL QueryBuilder

            $oQueryBuilder = $this->oConnection->conn->createQueryBuilder();
            // Base query
            $oQueryBuilder->select(
                'A.name',
                ' A.id',
                'A.unique_borrower_id',
                'D.total_interest',
                'D.penalty_income',
                'D.referral_expense',
                'D.net_revenue',
                'B.ending_principal as outstanding_principal',
                'D.emi_count',
                '(B.repaid_principal + D.net_revenue) AS total_paid_by_borrower',
                'B.repaid_principal',
                'B.loan_status'
            )
                ->from('app_borrower_master', 'A')
                ->join('A', 'app_loan_details', 'B', 'A.id = B.borrower_id')
                ->leftJoin('B', 'app_borrower_loan_payments', 'C', 'B.loan_id = C.loan_id')
                ->leftJoin('B', 'app_revenue_report', 'D', 'B.loan_id = D.loan_id')
                ->where('A.status = 1')
                ->andWhere("A.deleted = 0")
                ->andWhere("B.deleted = 0")
                ->andWhere('B.status = 1')
                ->groupBy('A.id, B.loan_id, D.report_id')
                ->orderBy('D.calculated_on', 'DESC');

            // Add filters dynamically
            if (!empty($borrowerId)) {
                $oQueryBuilder->andWhere('A.id = :borrowerId')->setParameter('borrowerId', $borrowerId);
            }
            if (!empty($startDate) && !empty($endDate)) {
                $oQueryBuilder->andWhere('D.calculated_on BETWEEN :startDate AND :endDate')
                    ->setParameter('startDate', $startDate)
                    ->setParameter('endDate', $endDate);
            }
            if (!empty($status)) {
                $oQueryBuilder->andWhere('B.loan_status = :status')->setParameter('status', $status);
            }
            if (!empty($principalAmount)) {
                $oQueryBuilder->andWhere('B.principal_amount >= :principalAmount')->setParameter('principalAmount', $principalAmount);
            }

            // Execute the query
            $oResult = $oQueryBuilder->executeQuery();

            // Fetch results
            $aRows = $oResult->fetchAllAssociative();

            return $aRows ?: [];
        } catch (\Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }




    /**
     * Fetch total revenue
     *
     * @return array
     */
    public function fetchTotalRevenue()
    {
        try {
            $oQueryBuilder = $this->oConnection->conn->createQueryBuilder();
            $oQueryBuilder
                ->select(
                    'SUM(D.net_revenue) AS totalRevenue'
                )
                ->from('app_revenue_report', 'D');

            // Execute the query
            $oResult = $oQueryBuilder->executeQuery();

            // Fetch results
            $iTotalRevenue = $oResult->fetchAllAssociative();

            return $iTotalRevenue;
        } catch (\Exception $e) {
            return [
                "success" => false,
                "message" => 'Error fetching total revenue: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Fetch total users
     *
     * @return array
     */
    public function fetchTotalUsers()
    {
        try {
            $oQueryBuilder = $this->oConnection->conn->createQueryBuilder();
            $oQueryBuilder
                ->select('COUNT(DISTINCT B.id) AS totalUsers')
                ->from('app_borrower_master', 'B')
                ->where('B.status = 1');

            // Active users only
            $oResult = $oQueryBuilder->executeQuery();

            // Fetch results
            $iTotalUser = $oResult->fetchAllAssociative();

            return $iTotalUser;
        } catch (\Exception $e) {
            return [
                "success" => false,
                "message" => 'Error fetching total users: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Fetch monthly breakdown (payments, interest, penalties, etc.)
     *
     * @return array
     */
    public function fetchMonthlyBreakdown($sFromDate, $sToDate)
    {
        try {
            $oQueryBuilder = $this->oConnection->conn->createQueryBuilder();
            $oQueryBuilder
                ->select(
                    "DATE_FORMAT(P.received_date, '%Y-%m') AS month", // Month based on payment date
                    '(SUM(P.payment_amount) + SUM(P.penalty_amount) - SUM(P.referral_share_amount)) AS total_Payment', // Total payment
                    'SUM(P.interest_paid) AS total_interest_paid', // Total interest paid
                    '(SUM(P.interest_paid) + SUM(P.penalty_amount) - SUM(P.referral_share_amount)) AS _total_net', // Total net revenue
                    'SUM(P.referral_share_amount) AS total_referral_amount', // Total referral share
                    'SUM(P.penalty_amount) AS total_penalty_amount', // Total penalty amount
                    'COUNT(DISTINCT L.borrower_id) AS total_borrower' // Distinct borrowers
                )
                ->from('app_borrower_loan_payments', 'P') // Payments table
                ->leftJoin('P', 'app_loan_details', 'L', 'P.loan_id = L.loan_id') // Loan details table
                ->where('P.status = 1') // Only active payments
                ->andWhere('P.deleted = 0') // Only non-deleted payments
                ->andWhere('L.status = 1') // Only active loans
                ->andWhere('L.deleted = 0') // Only non-deleted loans
                ->groupBy("DATE_FORMAT(P.received_date, '%Y-%m')") // Group by month
                ->orderBy("DATE_FORMAT(P.received_date, '%Y-%m')", 'DESC');
            if ($sFromDate != '' || $sToDate != '') {
                $oQueryBuilder
                    ->andWhere('P.received_date between :fromDate and :toDate')
                    ->setParameter('fromDate', $sFromDate)
                    ->setParameter('toDate', $sToDate);
            }



            $oQueryBuilder2 = $this->oConnection->conn->createQueryBuilder();
            $oQueryBuilder2
                ->select(
                    "SUM(ending_principal) AS total_pending_principal",
                    "sum(repaid_principal) as total_repaid_principal"
                )
                ->from("app_loan_details")
                ->where('deleted = :deleted')
                ->setParameter('deleted',0)
                ->andWhere('status = :status')
                ->setParameter('status',1);

            // Execute the query
            $oResult = $oQueryBuilder->executeQuery();
            // Fetch results
            $aMonthlyData = $oResult->fetchAllAssociative();

            $oResult2 = $oQueryBuilder2->executeQuery();

            $aMonthlyData2 = $oResult2->fetchAllAssociative();
            
            $aData = [
                "aMonthlyData"=> $aMonthlyData,
                "TotalData" => $aMonthlyData2
            ];
            return $aData;
        } catch (\Exception $e) {
            return [
                "success" => false,
                "message" => 'Error fetching monthly breakdown: ' . $e->getMessage()
            ];
        }
    }

    public function fetchDashboardCounts()
    {
        try {
            // Prepare an array to store the results
            $dashboardData = [];

            // Fetch total borrowers (active users)
            $oQueryBuilder1 = $this->oConnection->conn->createQueryBuilder();
            $oQueryBuilder1
                ->select('COUNT(DISTINCT B.id) AS totalBorrowers')
                ->from('app_borrower_master', 'B')
                ->where('B.status = 1')
                ->andWhere("B.deleted = 0");

            // Execute the query for total borrowers
            $oResult_1 = $oQueryBuilder1->executeQuery();
            $dashboardData['totalBorrowers'] = $oResult_1->fetchOne(); // Single value, no need for fetchAllAssociative

            // Fetch total referral users (assuming referral users are in a separate table or identified by a field)
            $oQueryBuilder2 = $this->oConnection->conn->createQueryBuilder();
            $oQueryBuilder2
                ->select('COUNT(DISTINCT C.reference_Id) AS totalReferralUsers')
                ->from('app_referral_user', 'C')
                ->where('C.status = 1')
                ->andWhere('C.deleted = 0');

            // Execute the query for total referral users
            $oResult_2 = $oQueryBuilder2->executeQuery();
            $dashboardData['totalReferralUsers'] = $oResult_2->fetchOne();

            // Fetch total payments (assuming total payments are in a payment table)
            $oQueryBuilder3 = $this->oConnection->conn->createQueryBuilder();
            $oQueryBuilder3
                ->select('(SUM(P.payment_amount) + SUM(P.penalty_amount) - SUM(P.referral_share_amount)) AS totalPayments')
                ->from('app_borrower_loan_payments', 'P')
                ->where('P.status = 1')  // Only active payments
                ->andWhere('P.deleted = 0'); // Non-deleted payments

            // Execute the query for total payments
            $oResult_3 = $oQueryBuilder3->executeQuery();
            $dashboardData['totalPayments'] = $oResult_3->fetchOne();

            // Fetch total revenue
            $oQueryBuilder4 = $this->oConnection->conn->createQueryBuilder();
            $oQueryBuilder4
                ->select('SUM(D.net_revenue) AS totalRevenue')
                ->from('app_revenue_report', 'D');

            // Execute the query for total revenue
            $oResult_4 = $oQueryBuilder4->executeQuery();
            $dashboardData['totalRevenue'] = $oResult_4->fetchOne();

            // Return the data as a JSON response
            return [
                "success" => true,
                "totalBorrowers" => $dashboardData['totalBorrowers'],
                "totalReferralUsers" => $dashboardData['totalReferralUsers'],
                "totalPayments" => $dashboardData['totalPayments'],
                "totalRevenue" => $dashboardData['totalRevenue']
            ];
        } catch (\Exception $e) {
            return [
                "success" => false,
                "message" => 'Error fetching dashboard data: ' . $e->getMessage()
            ];
        }
    }
}
