<?php
require_once "../config.php";
require_once "DB-Connection.php";

final class DocumentReferral
{
    private $oConnection;
    private $oQueryBuilder;

    public function __construct()
    {
        $this->oConnection = new DBConnection();
        $this->oQueryBuilder = $this->oConnection->conn->createQueryBuilder();
    }

    // Add a new document
    public function addDocument($documentData)
    {
        $sTableName = "app_referral_document";

        try {
            // Build the query
            $this->oQueryBuilder->insert($sTableName)
                ->values([
                    'referral_id'=> ':referral_id',
                    'document_name' => ':document_name',
                    'document_path' => ':document_path',
                    'added_on' => ':added_on',
                    'status' => ':status',
                    'deleted' => ':deleted'
                ])
                ->setParameter('referral_id', $documentData['referral_id'])
                ->setParameter('document_name', $documentData['document_name'])
                ->setParameter('document_path', $documentData['document_path'])
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

    // Fetch document by ID
    public function getDocumentById($documentId)
    {
        $sTableName = "app_referral_document";

        try {
            // Build the query
            $this->oQueryBuilder->select('*')
                ->from($sTableName)
                ->where('id = :document_id')
                ->setParameter('document_id', $documentId);

            // Execute the query
            $oResult = $this->oQueryBuilder->executeQuery();
            $aRow = $oResult->fetchAssociative();

            if ($aRow) {
                return $aRow;  // Return document data
            } else {
                return [];
            }
        } catch (\Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    // Update document details
    public function updateDocument($documentId, $documentData)
    {
        $sTableName = "app_referral_document";

        try {
            // Build the query
            $this->oQueryBuilder->update($sTableName)
                ->set('document_name', ':document_name')
                ->set('document_path', ':document_path')
                ->set('status', ':status')
                ->set('deleted', ':deleted')
                ->where('id = :document_id')
                ->setParameter('document_name', $documentData['document_name'])
                ->setParameter('document_path', $documentData['document_path'])
                ->setParameter('status', $documentData['status'])
                ->setParameter('deleted', $documentData['deleted'])
                ->setParameter('document_id', $documentId);

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

    // Delete document by ID
    public function deleteDocument($documentId)
    {
        $sTableName = "app_referral_document";

        try {
            // Build the query
            $this->oQueryBuilder->update($sTableName)
                ->set("deleted",":deleted")
                ->where('id = :document_id')
                ->setParameter('deleted',1)
                ->setParameter('document_id', $documentId);

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

    // Get all documents for a borrower
    public function getAllDocumentsByReferralId($iReferralId)
    {
        $sTableName = "app_referral_document";

        try {
            // Build the query
            $this->oQueryBuilder->select('*')
                ->from($sTableName)
                ->where('referral_id = :referral_id')
                ->andWhere('deleted = 0')  // Only fetch active documents
                ->setParameter('referral_id', $iReferralId);

            // Execute the query
            $oResult = $this->oQueryBuilder->executeQuery();
            $aRows = $oResult->fetchAllAssociative();

            if ($aRows) {
                return $aRows;  // Return all documents
            } else {
                return [];
            }
        } catch (\Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    // Get all documents (globally)
    public function getAllDocumentsGlobal()
    {
        $sTableName = "app_referral_document";

        try {
            // Build the query
            $this->oQueryBuilder->select('*')
                ->from($sTableName)
                ->where('deleted = 0'); // Only fetch non-deleted documents

            // Execute the query
            $oResult = $this->oQueryBuilder->executeQuery();
            $aRows = $oResult->fetchAllAssociative();

            if ($aRows) {
                return $aRows; // Return all documents
            } else {
                return [];
            }
        } catch (\Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }
}
