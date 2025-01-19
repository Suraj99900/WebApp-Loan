<?php
require_once "../config.php";
require_once "DB-Connection.php";

final class BorrowerManager
{
    private $oConnection;
    private $oQueryBuilder;

    public function __construct()
    {
        $this->oConnection = new DBConnection();
        $this->oQueryBuilder = $this->oConnection->conn->createQueryBuilder();
    }

    // Add a new borrower
    public function addBorrower($aBorrowerData)
    {
        $sTableName = "app_borrower_master";

        try {
            // Build the query
            $this->oQueryBuilder->insert($sTableName)
                ->values([
                    'name' => ':name',
                    'phone_no' => ':phone_no',
                    'gender' => ':gender',
                    'email' => ':email',
                    'address' => ':address'
                ])
                ->setParameter('name', $aBorrowerData['name'])
                ->setParameter('phone_no', $aBorrowerData['phone_no'])
                ->setParameter('gender', $aBorrowerData['gender'])
                ->setParameter('email', $aBorrowerData['email'])
                ->setParameter('address', $aBorrowerData['address']);

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

    // Fetch borrower by ID
    public function getBorrowerById($borrowerId)
    {
        $sTableName = "app_borrower_master";

        try {

            $this->oQueryBuilder
                ->select(
                    'A.id',
                    'A.name',
                    'A.phone_no',
                    'A.gender',
                    'A.address',
                    'A.email',
                    'A.status AS user_status',
                    'A.deleted AS user_deleted',
                    'C.ref_name',
                    'C.ref_percentage',
                    'C.ref_phone_number',
                    'D.*'
                )
                ->from('app_borrower_master', 'A')
                ->leftJoin('A', 'app_borrower_ref_map', 'B', 'A.id = B.borrower_id and B.deleted = 0')
                ->leftJoin('B', 'app_referral_user', 'C', 'C.reference_Id = B.reference_Id AND C.status = 1 AND C.deleted = 0')
                ->leftJoin('A', 'app_loan_details', 'D', 'D.borrower_id = A.id AND D.status = 1 AND D.deleted = 0')
                ->where('A.status = 1')
                ->andWhere('A.id = :borrower_id')
                ->andWhere('A.deleted = 0')
                ->setParameter('borrower_id', $borrowerId);


            // Execute the query
            $oResult = $this->oQueryBuilder->executeQuery();
            $aRow = $oResult->fetchAssociative();

            if ($aRow) {
                return $aRow;  // Return borrower data
            } else {
                return [];
            }
        } catch (\Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    // Update borrower information
    public function updateBorrower($borrowerId, $borrowerData)
    {
        $sTableName = "app_borrower_master";

        try {
            // Build the query
            $this->oQueryBuilder->update($sTableName)
                ->set('name', ':name')
                ->set('phone_no', ':phone_no')
                ->set('email', ':email')
                ->set('address', ':address')
                ->where('id = :borrower_id')
                ->setParameter('name', $borrowerData['name'])
                ->setParameter('phone_no', $borrowerData['phone_no'])
                ->setParameter('email', $borrowerData['email'])
                ->setParameter('address', $borrowerData['address'])
                ->setParameter('borrower_id', $borrowerId);

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

    // Delete borrower by ID
    public function invalidBorrower($borrowerId)
    {
        $sTableName = "app_borrower_master";

        try {
            // Build the query
            $this->oQueryBuilder->update($sTableName)
                ->set("deleted", ":deleted")
                ->where('id = :borrower_id')
                ->andWhere('status = 1')
                ->andWhere('deleted = 0')
                ->setParameter('deleted', 1)
                ->setParameter('borrower_id', $borrowerId);

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

    // Get all borrowers
    public function getAllBorrowers()
    {
        $sTableName = "app_borrower_master";

        try {
            // Build the query
            $this->oQueryBuilder->select('*')
                ->from($sTableName)
                ->andWhere('status = 1')
                ->where('deleted = 0'); // Only fetch active borrowers

            // Execute the query
            $oResult = $this->oQueryBuilder->executeQuery();
            $aRows = $oResult->fetchAllAssociative();

            if ($aRows) {
                return $aRows; // Return all borrowers
            } else {
                return [];
            }
        } catch (\Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    public function getAllBorrowerDetails($sName='', $amount='', $date='')
    {

        try {
            // Build the query
            $this->oQueryBuilder
                ->select(
                    'A.id',
                    'A.name',
                    'A.phone_no',
                    'A.gender',
                    'A.address',
                    'A.email',
                    'A.status AS user_status',
                    'A.deleted AS user_deleted',
                    'C.ref_name',
                    'C.ref_percentage',
                    'C.ref_phone_number',
                    'D.*'
                )
                ->from('app_borrower_master', 'A')
                ->leftJoin('A', 'app_borrower_ref_map', 'B', 'A.id = B.borrower_id and B.deleted = 0')
                ->leftJoin('B', 'app_referral_user', 'C', 'C.reference_Id = B.reference_Id AND C.status = 1 AND C.deleted = 0')
                ->leftJoin('A', 'app_loan_details', 'D', 'D.borrower_id = A.id AND D.status = 1 AND D.deleted = 0')
                ->where('A.status = 1')
                ->andWhere('A.deleted = 0')
                ->orderBy('D.loan_status', 'ASC');;

            if($sName!=''){
                $this->oQueryBuilder
                    ->andWhere('A.name like :name')
                    ->setParameter('name', "%".$sName."%");
            }
    
            if($amount != ''){
                $this->oQueryBuilder
                    ->andWhere('D.principal_amount = :amount')
                    ->setParameter('amount', $amount);
            }
    
            if($date != ''){
                $this->oQueryBuilder
                    ->andWhere('D.disbursed_date = :date')
                    ->setParameter('date', $date);
            }

            // Execute the query
            $oResult = $this->oQueryBuilder->executeQuery();
            $aRows = $oResult->fetchAllAssociative();

            if ($aRows) {
                return $aRows; // Return all borrower details
            } else {
                return [];
            }
        } catch (\Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }
}
