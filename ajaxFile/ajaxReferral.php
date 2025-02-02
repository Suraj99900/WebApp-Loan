<?php
header('Content-Type: application/json');

require_once "../classes/DB-Connection.php";
require_once "../classes/BorrowerManager.php";
require_once "../classes/LoanManager.php";
include_once "../classes/clientCode.php";
include_once "../classes/sessionManager.php";
include_once "../classes/class.Input.php";
include_once "../classes/DocumentReferral.php";
require_once "../classes/ReferralUserManager.php";

$sFlag = Input::request('sFlag');
$response = array('status' => 'error', 'message' => 'Invalid request');

try {
    switch ($sFlag) {
        case 'addReferral':
            $aData['ref_name'] = Input::request('ref_name');
            $aData['ref_phone_number'] = Input::request('ref_phone_number');
            $aData['ref_percentage'] = Input::request('ref_percentage');
            $sDocuments = $_FILES['documents'];  // Files for borrower documents
            $aDocumentName = Input::request('documentName') ?? [];


            $oReferralId = (new ReferralUserManager())->addReferralUser($aData);

            // Handle file upload
            $aDocumentDetail = [];
            if ($sDocuments && $sDocuments['name']) {
                foreach ($sDocuments['name'] as $index => $documentName) {
                    // File path and other validation
                    $targetDir = "../uploads/referral_document/";
                    $targetFile = $targetDir . date("Y-m-d_H-i-s")  . "_" . basename($documentName);
                    if (move_uploaded_file($sDocuments['tmp_name'][$index], $targetFile)) {
                        $aDocumentDetail = [
                            'referral_id' => $oReferralId,
                            'document_name' => $aDocumentName[$index],
                            'document_path' => "uploads/referral_document/" . date("Y-m-d_H-i-s")  . "_" . basename($documentName),
                        ];
                        $oResultDoc = (new DocumentReferral())->addDocument($aDocumentDetail);
                    } else {
                        throw new Exception("Error uploading document: " . $documentName);
                    }
                }
            }

            if ($oReferralId) {
                $response['status'] = 'success';
                $response['message'] = 'Added successfully';
            } else {
                $response['message'] = 'Failed to add referral';
            }
            break;
        case 'fetchAllReferrals':
            $aData['sName'] = Input::request('sName') ? Input::request('sName') : '';
            $oResult = (new ReferralUserManager())->getAllReferralUsers($aData);
            if ($oResult) {
                $response['status'] = 'success';
                $response['message'] = 'successfully';
                $response['data'] = $oResult;
            } else {
                $response['message'] = 'Failed to fetch referral';
            }
            break;

        case 'getReferralDetailsById':
            $iReferralID = isset($_GET['referral_id']) ? intval($_GET['referral_id']) : 0;

            if ($iReferralID > 0) {
                // Call the business logic method to fetch details
                $oReferralDetails = (new ReferralUserManager())->getReferralUserByID($iReferralID);
                $aDocumentData = (new DocumentReferral())->getAllDocumentsByReferralId($iReferralID);
                $aData = [
                    "ReferralDetails" => $oReferralDetails,
                    "uploadedDocuments" => $aDocumentData
                ];

                if ($oReferralDetails) {
                    $response['status'] = 'success';
                    $response['message'] = 'Referral details fetched successfully';
                    $response['data'] = $aData;
                } else {
                    $response['status'] = 'error';
                    $response['message'] = 'Referral not found for the given ID';
                }
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Invalid referral ID';
            }
            break;
        case 'updateReferralDetails':
            $iReferralID = isset($_POST['referral_id']) ? intval($_POST['referral_id']) : 0;
            $sReferralName = isset($_POST['ref_name']) ? trim($_POST['ref_name']) : '';
            $sPhoneNumber = isset($_POST['ref_phone_number']) ? trim($_POST['ref_phone_number']) : '';
            $fReferralPercentage = isset($_POST['ref_percentage']) ? floatval($_POST['ref_percentage']) : 0;
            $sDocuments = $_FILES['new_documents'];  // Files for borrower documents
            $aDocumentName = Input::request('new_document_name') ?? [];

            if ($iReferralID > 0 && $sReferralName && $sPhoneNumber && $fReferralPercentage > 0) {
                $aData = [
                    'ref_name' => $sReferralName,
                    'ref_phone_number' => $sPhoneNumber,
                    'ref_percentage' => $fReferralPercentage
                ];
                // Call the business logic to update the referral details
                $bUpdateResult = (new ReferralUserManager())->updateReferralUser($iReferralID, $aData);

                // Handle file upload
                $aDocumentDetail = [];
                if ($sDocuments && $sDocuments['name']) {
                    foreach ($sDocuments['name'] as $index => $documentName) {
                        // File path and other validation
                        $targetDir = "../uploads/referral_document/";
                        $targetFile = $targetDir . date("Y-m-d_H-i-s")  . "_" . basename($documentName);
                        if (move_uploaded_file($sDocuments['tmp_name'][$index], $targetFile)) {
                            $aDocumentDetail = [
                                'referral_id' => $iReferralID,
                                'document_name' => $aDocumentName[$index],
                                'document_path' => "uploads/referral_document/" . date("Y-m-d_H-i-s")  . "_" . basename($documentName),
                            ];
                            $oResultDoc = (new DocumentReferral())->addDocument($aDocumentDetail);
                        } else {
                            throw new Exception("Error uploading document: " . $documentName);
                        }
                    }
                }

                if ($bUpdateResult) {
                    $response['status'] = 'success';
                    $response['message'] = 'Referral details updated successfully';
                } else {
                    $response['status'] = 'error';
                    $response['message'] = 'Failed to update referral details';
                }
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Invalid input parameters';
            }
            break;
        case 'invalidDocument':
            $iDocumentId = Input::request('id');;
            if ($iDocumentId) {
                $oDeletedDocument = (new DocumentReferral())->deleteDocument($iDocumentId);
                if ($oDeletedDocument) {
                    $response['status'] = 'success';
                    $response['message'] = 'Referral deleted successfully';
                } else {
                    $response['message'] = 'Failed to delete borrower';
                }
            } else {
                $response['message'] = 'Missing required fields';
            }
            break;


        default:
            $response['message'] = 'Unknown request';
            break;
    }
} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

echo json_encode($response);
