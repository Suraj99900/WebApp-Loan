<?php
require_once "../config.php";
require_once "DB-Connection.php";

final class ReferralUserManager
{
    private $oConnection;
    private $oQueryBuilder;

    public function __construct()
    {
        $this->oConnection = new DBConnection();
        $this->oQueryBuilder = $this->oConnection->conn->createQueryBuilder();
    }

    // Add a new referral user
    public function addReferralUser($referralData)
    {
        $sTableName = "app_referral_user";

        try {
            // Build the query
            $this->oQueryBuilder->insert($sTableName)
                ->values([
                    'ref_name' => ':ref_name',
                    'ref_phone_number' => ':ref_phone_number',
                    'ref_percentage' => ':ref_percentage',
                    'added_on' => ':added_on',
                    'status' => ':status',
                    'deleted' => ':deleted'
                ])
                ->setParameter('ref_name', $referralData['ref_name'])
                ->setParameter('ref_phone_number', $referralData['ref_phone_number'])
                ->setParameter('ref_percentage', $referralData['ref_percentage'])
                ->setParameter('added_on', date('Y-m-d H:i:s'))
                ->setParameter('status', 1) 
                ->setParameter('deleted', 0);

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

    // Fetch referral user by ID
    public function getReferralUserById($referralId)
    {
        $sTableName = "app_referral_user";

        try {
            // Build the query
            $this->oQueryBuilder->select('*')
                ->from($sTableName)
                ->where('reference_Id = :reference_id')
                ->setParameter('reference_id', $referralId);

            // Execute the query
            $oResult = $this->oQueryBuilder->executeQuery();
            $aRow = $oResult->fetchAssociative();

            if ($aRow) {
                return $aRow;  // Return referral user data
            } else {
                return [];
            }
        } catch (\Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    // Update referral user details
    public function updateReferralUser($referralId, $referralData)
    {
        $sTableName = "app_referral_user";

        try {
            // Build the query
            $this->oQueryBuilder->update($sTableName)
                ->set('ref_name', ':ref_name')
                ->set('ref_phone_number', ':ref_phone_number')
                ->set('ref_percentage', ':ref_percentage')
                ->set('status', ':status')
                ->set('deleted', ':deleted')
                ->where('reference_Id = :reference_id')
                ->setParameter('ref_name', $referralData['ref_name'])
                ->setParameter('ref_phone_number', $referralData['ref_phone_number'])
                ->setParameter('ref_percentage', $referralData['ref_percentage'])
                ->setParameter('status', 1)
                ->setParameter('deleted', 0)
                ->setParameter('reference_id', $referralId);

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

    // Delete referral user by ID
    public function deleteReferralUser($referralId)
    {
        $sTableName = "app_referral_user";

        try {
            // Build the query
            $this->oQueryBuilder->update($sTableName)
                ->set("deleted", ":deleted")
                ->where('reference_Id = :reference_id')
                ->setParameters('deleted', 1)
                ->setParameter('reference_id', $referralId);

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

    // Get all referral users
    public function getAllReferralUsers($aData = [])
    {
        $sTableName = "app_referral_user";
    
        try {
            // Build the query
            $this->oQueryBuilder->select('*')
                ->from($sTableName)
                ->where('deleted = 0');
    
            // Check if sName is provided and not empty
            if (!empty($aData['sName'])) {
                $this->oQueryBuilder
                    ->andWhere('ref_name like :sName') // Corrected placeholder syntax
                    ->setParameter('sName', "%".$aData['sName']."%");
            }
    
            // Execute the query
            $oResult = $this->oQueryBuilder->executeQuery();
            $aRows = $oResult->fetchAllAssociative();
    
            return $aRows ?: []; // Return all referral users or an empty array
        } catch (\Exception $e) {
            // Handle exceptions gracefully
            die("Error: " . $e->getMessage());
        }
    }
    
}
