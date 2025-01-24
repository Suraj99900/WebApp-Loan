<?php
header('Content-Type: application/json');

require_once "../classes/DB-Connection.php";
require_once "../classes/BorrowerManager.php";
require_once "../classes/DocumentManager.php";
require_once "../classes/ReferralUserManager.php";
require_once "../classes/BorrowerRefMapManager.php";
require_once "../classes/LoanPaymentManager.php";
include_once "../classes/clientCode.php";
include_once "../classes/sessionManager.php";
include_once "../classes/class.Input.php";
include_once "../classes/LoanManager.php";

$sFlag = Input::request('sFlag');
$response = array('status' => 'error', 'message' => 'Invalid request');

try {
    $borrowerManage = new BorrowerManager();

    switch ($sFlag) {
        case 'addBorrower':
            // Gather Borrower Information
            $sName = Input::request('name');
            $sEmail = Input::request('email');
            $sPhone = Input::request('phone');
            $sAddress = Input::request('address');
            $sGender = Input::request('gender');
            $sDocuments = $_FILES['documents'];  // Files for borrower documents
            $aDocumentName = Input::request('documentName') ?? [] ;

            if ($sName && $sEmail && $sPhone && $sAddress && $sGender) {

                $aBorrowerData = [
                    "name" => $sName,
                    "email" => $sEmail,
                    "phone_no" => $sPhone,
                    "address" => $sAddress,
                    "gender" => $sGender
                ];
                // Add Borrower
                $iBorrowerId = $borrowerManage->addBorrower($aBorrowerData);

                // Handle file upload
                $aDocumentDetail = [];
                if ($sDocuments && $sDocuments['name']) {
                    foreach ($sDocuments['name'] as $index => $documentName) {
                        // File path and other validation
                        $targetDir = "../uploads/borrower_documents/";
                        $targetFile = $targetDir . date("Y-m-d_H-i-s")  . "_" . basename($documentName);
                        if (move_uploaded_file($sDocuments['tmp_name'][$index], $targetFile)) {
                            $aDocumentDetail = [
                                'borrower_id' => $iBorrowerId,
                                'document_name' => $aDocumentName[$index],
                                'document_path' => "uploads/borrower_documents/" . date("Y-m-d_H-i-s")  . "_" . basename($documentName),
                            ];
                            $oResultDoc = (new DocumentManager())->addDocument($aDocumentDetail);
                        } else {
                            throw new Exception("Error uploading document: " . $documentName);
                        }
                    }
                }

                if ($iBorrowerId) {
                    $response['status'] = 'success';
                    $response['message'] = 'Borrower added successfully';
                    $response['data'] = ['borrower_id' => $iBorrowerId];
                } else {
                    $response['message'] = 'Failed to add borrower';
                }
            } else {
                $response['message'] = 'Missing required fields';
            }
            break;

        case 'fetchAllBorrowers':
            $iLimit = Input::request('iLimit');
            $sName = Input::request('name')??'';
            $sAmount = Input::request('amount')??'';
            $dFromDate = Input::request('sFromDate')??'';
            $dToDate = Input::request('sToDate')??'';


            $aBorrowers = $borrowerManage->getAllBorrowerDetails($sName,$sAmount,$dFromDate,$dToDate);
            $aData = array();
            foreach ($aBorrowers as $key => $iEle) {
                $totalInterest =( new LoanPaymentManager())->getTotalInterestPaidByLoanId($iEle['loan_id']);
                if($totalInterest > 0){
                    $iEle['bIsPaid'] = 1;
                }else{
                    $iEle['bIsPaid'] = 0;
                }
                $aData[] = $iEle;
            }

            
            $response['status'] = 'success';
            $response['message'] = 'Borrower data fetched successfully';
            $response['data'] = $aData;
            break;

        case 'fetchBorrowerById':
            $borrowerId = Input::request('id');
            if ($borrowerId) {
                $aBorrower = $borrowerManage->getBorrowerById($borrowerId);
                $aLoanDetails = (new LoanManager())->getAllLoans($borrowerId);
                $aDocumentData = (new DocumentManager())->getAllDocumentsByBorrowerId($aBorrower['id']);
                $aData = [
                    "borrowerDetails" => $aBorrower,
                    "uploadedDocuments" => $aDocumentData,
                    "loanDetails" => $aLoanDetails
                ];
                if ($aBorrower) {
                    $response['status'] = 'success';
                    $response['message'] = 'Borrower data fetched successfully';
                    $response['data'] = $aData;
                } else {
                    $response['message'] = 'Borrower not found';
                }
            } else {
                $response['message'] = 'Missing required fields';
            }
            break;

        case 'updateBorrower':
            $iBorrowerId = Input::request('id');
            $sName = Input::request('name');
            $sEmail = Input::request('email');
            $sPhone = Input::request('phone_no');
            $sAddress = Input::request('address');
            $sDocuments = $_FILES['documents'];
            $aDocumentName = Input::request('documentName') ?? [] ;

            if ($iBorrowerId && $sName && $sEmail && $sPhone && $sAddress) {
                // Handle file upload if documents are provided

                $aBorrowerData = [
                    "name" => $sName,
                    "email" => $sEmail,
                    "phone_no" => $sPhone,
                    "address" => $sAddress,
                ];
                // Update Borrower
                $updated = $borrowerManage->updateBorrower($iBorrowerId, $aBorrowerData);

                // Handle file upload
                $aDocumentDetail = [];
                if ($sDocuments && $sDocuments['name']) {
                    foreach ($sDocuments['name'] as $index => $documentName) {
                        // File path and other validation
                        if ($documentName != "") {
                            $targetDir = "../uploads/borrower_documents/";
                            $targetFile = $targetDir . date("Y-m-d_H-i-s")  . "_" . basename($documentName);
                            if (move_uploaded_file($sDocuments['tmp_name'][$index], $targetFile)) {
                                $aDocumentDetail = [
                                    'borrower_id' => $iBorrowerId,
                                    'document_name' => $aDocumentName[$index],
                                    'document_path' => "uploads/borrower_documents/" . date("Y-m-d_H-i-s")  . "_" . basename($documentName),
                                ];
                                $oResultDoc = (new DocumentManager())->addDocument($aDocumentDetail);
                            } else {
                                throw new Exception("Error uploading document: " . $documentName);
                            }
                        }
                    }
                }

                if ($updated) {
                    $response['status'] = 'success';
                    $response['message'] = 'Borrower updated successfully';
                    $response['data'] = ['borrower_id' => $borrowerId, 'documents' => $documentPaths];
                } else {
                    $response['message'] = 'Failed to update borrower';
                }
            } else {
                $response['message'] = 'Missing required fields';
            }
            break;

        case 'deleteBorrower':
            $borrowerId = Input::request('id');
            if ($borrowerId) {
                $deleted = $borrowerManage->invalidBorrower($borrowerId);
                if ($deleted) {
                    $response['status'] = 'success';
                    $response['message'] = 'Borrower deleted successfully';
                } else {
                    $response['message'] = 'Failed to delete borrower';
                }
            } else {
                $response['message'] = 'Missing required fields';
            }
            break;

        case 'invalidDocument':
            $iDocumentId = Input::request('id');
            if ($iDocumentId) {
                $oDeletedDocument = (new DocumentManager)->deleteDocument($iDocumentId);
                if ($oDeletedDocument) {
                    $response['status'] = 'success';
                    $response['message'] = 'Borrower deleted successfully';
                } else {
                    $response['message'] = 'Failed to delete borrower';
                }
            } else {
                $response['message'] = 'Missing required fields';
            }
            break;
        case 'mapReferralToBorrower':
            $iBorrowerId = Input::request('borrowerId') ? Input::request('borrowerId') : '';
            $iReferralId = Input::request('referralId') ? Input::request('referralId') : '';
            $aData = [
                'borrower_id' => $iBorrowerId,
                'reference_Id' => $iReferralId
            ];
            $iInsertedId = (new BorrowerRefMapManager())->addBorrowerRefMap($aData);
            if ($iInsertedId) {
                $response['status'] = 'success';
                $response['message'] = 'Map successfully';
                $response['data'] = $iInsertedId;
            } else {
                $response['message'] = 'Failed to map referral';
            }
            break;
        case 'updateMapReferralToBorrower':
            $iBorrowerId = Input::request('borrowerId') ? Input::request('borrowerId') : '';
            $iReferralId = Input::request('referralUpdateId') ? Input::request('referralUpdateId') : '';
            $aData = [
                'borrower_id' => $iBorrowerId,
                'reference_Id' => $iReferralId
            ];

            $oResult = (new BorrowerRefMapManager())->invalidBorrowerRefMap($iBorrowerId);
            if ($oResult) {
                $iInsertedId = (new BorrowerRefMapManager())->addBorrowerRefMap($aData);
            }
            if ($oResult) {
                $response['status'] = 'success';
                $response['message'] = 'Map successfully';
                $response['data'] = $iInsertedId;
            } else {
                $response['message'] = 'Failed to map referral';
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
