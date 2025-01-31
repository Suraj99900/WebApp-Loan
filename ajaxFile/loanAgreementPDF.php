<?php

// Include the TCPDF library
require_once '../vendor/autoload.php';
include_once "../classes/class.Input.php";
include "../classes/BorrowerManager.php";
include "../classes/DocumentManager.php";
include_once "../classes/LoanManager.php";

// Assuming a POST request with borrower ID
if (isset($_REQUEST['borrower_id'])) {
    $borrowerId = $_REQUEST['borrower_id'];
    $iLoanId = $_REQUEST['loan_id'];

    $aBorrower = (new BorrowerManager())->getBorrowerById($borrowerId);
    if($iLoanId != ''){
        $aLoanData = (new LoanManager())->getAllLoans($borrowerId,$iLoanId);
        $aBorrower['principal_amount'] = $aLoanData[0]['principal_amount'];
        $aBorrower['loan_period'] = $aLoanData[0]['loan_period'];
        $aBorrower['disbursed_date'] = $aLoanData[0]['disbursed_date'];
    }

    if (!$aBorrower) {
        die("Borrower not found.");
    }

    // Initialize TCPDF
    $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('WebApp-Loan');
    $pdf->SetTitle('Loan Agreement');
    $pdf->SetSubject('Loan Agreement Document');
    $pdf->SetKeywords('Loan, Agreement, PDF');

    // Add a page
    $pdf->AddPage();

    // Add Logo
    $pdf->Image('../assets/img/apple-touch-icon.png', 65, 13, 10); // Adjust path and size
    $pdf->SetFont('helvetica', 'B', 20);
    $pdf->Cell(0, 15, 'Loan Agreement', 0, 1, 'C');
    $pdf->Ln(10);

    // Borrower Details Section
    $pdf->SetFont('helvetica', '', 12);
    $html = '
        <p>I, Mr/Ms. <b>' . $aBorrower['name'] . '</b>  <b>(' . $aBorrower['phone_no'] . ')</b>, 
        hereby acknowledge that I have borrowed a sum of INR <b>' . $aBorrower['principal_amount']. '</b>' . '
        from Mr. Sandeep Siddharth More.</p>
    ';
    $pdf->writeHTML($html, true, false, true, false, '');

    // Repayment Terms Section
    $repaymentTerms = '
        <br>
        <h4>Repayment Terms</h4>
        <p>I promise to repay the said amount to Mr. Sandeep S More within a period of <b>' . $aBorrower['loan_period'] . ' months </b>. 
        In case I am unable to repay, my family '.($aBorrower['ref_name'] ? 'or. Witness <b>'.$aBorrower['ref_name'].'</b> <b>('.$aBorrower['ref_phone_number']. ')</b> ':'') .'will repay the amount on my behalf.</p>
        <p>I hereby authorize <b>Mr. Sandeep More</b> to visit my house for recovery in case of delay in paying the borrowed amount, and I will be liable for additional charges including travel expenses.</p>
    ';
    $pdf->writeHTML($repaymentTerms, true, false, true, false, '');

    // Documents Submitted Section
    $pdf->SetFont('helvetica', '', 12);
    $repaymentTerms = '
        <br>
        <h4>Documents Submitted:</h4>
        <p>I have submitted the following documents to Mr. Sandeep S More:</p>
    ';
    $pdf->writeHTML($repaymentTerms, true, false, true, false, '');
    $aDocuments = (new DocumentManager())->getAllDocumentsByBorrowerId($borrowerId);// Assuming documents are stored as comma-separated values
    foreach ($aDocuments as $doc) {
        $sNameParts = explode('.',$doc['document_name'])[0];

        $pdf->Cell(0, 10, '- ' . $sNameParts, 0, 1);
    }
    $pdf->Ln(5);

    // Footer with Date and Signature
    $footer = '
        <br>
        <h4>Agreement Details</h4>
        <p>Date: <b>' . $aBorrower['disbursed_date'] . '</b></p>
        <p>Signature: <b>__________________________</b></p>
        Mr/Ms.'.$aBorrower['name'].'

        <p>Witness Signature: <b>__________________________</b></p>
        Mr/Ms.'.$aBorrower['ref_name'].'

    ';
    $pdf->writeHTML($footer, true, false, true, false, '');

    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="Loan_Agreement.pdf"');
    // Output the PDF
    $pdf->Output('Loan_Agreement.pdf', 'D'); // D for download
}
