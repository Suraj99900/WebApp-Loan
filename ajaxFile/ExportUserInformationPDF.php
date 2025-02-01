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

    // Fetch data
    $borrowerManage = new BorrowerManager();
    $aBorrower = $borrowerManage->getBorrowerById($borrowerId);
    $aLoanDetails = (new LoanManager())->getAllLoans($borrowerId);
    $aDocumentData = (new DocumentManager())->getAllDocumentsByBorrowerId($borrowerId);

    if (!$aBorrower) {
        die("Borrower not found.");
    }

    // Initialize TCPDF
    $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('WebApp-Loan');
    $pdf->SetTitle('Borrower Information');
    $pdf->SetSubject('Borrower Information Document');
    $pdf->SetKeywords('Loan, Agreement, PDF');

    // Add a page
    $pdf->AddPage();

    // Add logo
    $pdf->Image('../assets/img/apple-touch-icon.png', 55, 13, 10); // Adjust path and size
    $pdf->SetFont('helvetica', 'B', 20);
    $pdf->Cell(0, 15, 'Borrower Information', 0, 1, 'C');
    $pdf->Ln(10);

    // Borrower Details Section
    $pdf->SetFont('helvetica', '', 12);
    $html = '
        <table cellpadding="5" cellspacing="0" border="0">
            <tr>
                <td><b>Name:</b> ' . $aBorrower['name'] . ' (' . $aBorrower['unique_borrower_id'] . ')</td>
                <td><b>Phone:</b> ' . $aBorrower['phone_no'] . '</td>
                <td><b>Email:</b> ' . $aBorrower['email'] . '</td>
            </tr>
            <tr>
                <td><b>Gender:</b> ' . $aBorrower['gender'] . '</td>
                <td colspan="2"><b>Address:</b> ' . $aBorrower['address'] . '</td>
            </tr>
        </table>
    ';
    $pdf->writeHTML($html, true, false, true, false, '');

    // Loan Information
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->Cell(0, 10, 'Loan Information', 0, 1, 'L');
    $pdf->SetFont('helvetica', '', 12);
    $iTotalLoan = 0;
    if (count($aLoanDetails) > 0) {
        $iTotalLoan = 0; // Initialize total loan amount
        $html = '<table cellpadding="5" cellspacing="0" border="1" style="border-collapse: collapse; width: 100%;">';

        foreach ($aLoanDetails as $loan) {
            $iTotalLoan += $loan['principal_amount'];

            $html .= '
                <tr>
                    <td><b>Principal Amount:</b> ' . number_format($loan['principal_amount'], 2) . '</td>
                    <td><b>Interest Rate:</b> ' . $loan['interest_rate'] . '%</td>
                    <td><b>Interest Amount:</b> ' . number_format($loan['EMI_amount'], 2) . '</td>
                </tr>
                <tr>
                    <td><b>Loan Period:</b> ' . $loan['loan_period'] . ' months</td>
                    <td><b>Disbursed Date:</b> ' . date('d-m-Y', strtotime($loan['disbursed_date'])) . '</td>
                    <td><b>Closure Date:</b> ' . date('d-m-Y', strtotime($loan['closure_date'])) . '</td>
                </tr>
                <tr>
                    <td colspan="3"><b>Loan Status:</b> ' . strtoupper($loan['loan_status']) . '</td>
                </tr>
            ';
        }

        $html .= '
            <tr>
                <td colspan="3" style="text-align: left;"><b>Total Loan Amount:</b> ' . number_format($iTotalLoan, 2) . '</td>
            </tr>
        ';
        $html .= '</table>';
    } else {
        $html = '<p>No Loan Present.</p>';
    }

    $pdf->writeHTML($html, true, false, true, false, '');

    // Referral Information
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->Cell(0, 10, 'Referral Information', 0, 1, 'L');
    $pdf->SetFont('helvetica', '', 12);
    $html = '
        <table cellpadding="5" cellspacing="0" border="0">
            <tr>
                <td><b>Referred By:</b> ' . ($aBorrower['ref_name'] ?: '-') . '</td>
                <td><b>Referral Percentage:</b> ' . ($aBorrower['ref_percentage'] ?: '-') . '%</td>
                <td><b>Referral Phone:</b> ' . ($aBorrower['ref_phone_number'] ?: '-') . '</td>
            </tr>
        </table>
    ';
    $pdf->writeHTML($html, true, false, true, false, '');


    // Output the PDF
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="Loan_Agreement.pdf"');
    $pdf->Output('Loan_Agreement.pdf', 'D'); // 'D' forces download
}
