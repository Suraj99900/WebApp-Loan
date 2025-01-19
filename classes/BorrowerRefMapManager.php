<?php
require_once "../config.php";
require_once "DB-Connection.php";

final class BorrowerRefMapManager
{
    private $oConnection;
    private $oQueryBuilder;

    public function __construct()
    {
        $this->oConnection = new DBConnection();
        $this->oQueryBuilder = $this->oConnection->conn->createQueryBuilder();
    }

    // Add a new borrower referral mapping
    public function addBorrowerRefMap($refMapData)
    {
        $sTableName = "app_borrower_ref_map";

        try {
            // Build the query
            $this->oQueryBuilder->insert($sTableName)
                ->values([
                    'reference_Id' => ':reference_Id',
                    'borrower_id' => ':borrower_id',
                    'added_on' => ':added_on',
                    'status' => ':status',
                    'deleted' => ':deleted'
                ])
                ->setParameter('reference_Id', $refMapData['reference_Id'])
                ->setParameter('borrower_id', $refMapData['borrower_id'])
                ->setParameter('added_on', date('Y-m-d H:i:s'))
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

    // Fetch borrower-referral mapping by ID
    public function getBorrowerRefMapById($refMapId)
    {
        $sTableName = "app_borrower_ref_map";

        try {
            // Build the query
            $this->oQueryBuilder->select('*')
                ->from($sTableName)
                ->where('id = :ref_map_id')
                ->setParameter('ref_map_id', $refMapId);

            // Execute the query
            $oResult = $this->oQueryBuilder->executeQuery();
            $aRow = $oResult->fetchAssociative();

            if ($aRow) {
                return $aRow;  // Return borrower-referral map data
            } else {
                return [];
            }
        } catch (\Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    // Update borrower-referral mapping details
    public function updateBorrowerRefMap($refMapId, $refMapData)
    {
        $sTableName = "app_borrower_ref_map";

        try {
            // Build the query
            $this->oQueryBuilder->update($sTableName)
                ->set('reference_Id', ':reference_Id')
                ->set('borrower_id', ':borrower_id')
                ->set('relationship', ':relationship')
                ->set('status', ':status')
                ->set('deleted', ':deleted')
                ->where('id = :ref_map_id')
                ->setParameter('reference_Id', $refMapData['reference_Id'])
                ->setParameter('borrower_id', $refMapData['borrower_id'])
                ->setParameter('relationship', $refMapData['relationship'])
                ->setParameter('status', $refMapData['status'])
                ->setParameter('deleted', $refMapData['deleted'])
                ->setParameter('ref_map_id', $refMapId);

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

    // Delete borrower-referral mapping by ID
    public function invalidBorrowerRefMap($iBorrowerId)
    {
        $sTableName = "app_borrower_ref_map";

        try {
            // Build the query
            $this->oQueryBuilder->update($sTableName)
                ->set("deleted", ":deleted")
                ->where('borrower_id = :borrower_id')
                ->setParameter('deleted', 1)
                ->setParameter('borrower_id', $iBorrowerId);

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

    // Get all borrower-referral mappings
    public function getAllBorrowerRefMaps()
    {
        $sTableName = "app_borrower_ref_map";

        try {
            // Build the query
            $this->oQueryBuilder->select('*')
                ->from($sTableName)
                ->where('deleted = 0'); // Only fetch non-deleted records

            // Execute the query
            $oResult = $this->oQueryBuilder->executeQuery();
            $aRows = $oResult->fetchAllAssociative();

            if ($aRows) {
                return $aRows;  // Return all borrower-referral mappings
            } else {
                return [];
            }
        } catch (\Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }
}
