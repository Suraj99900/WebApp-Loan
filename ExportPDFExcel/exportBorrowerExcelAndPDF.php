<?php
require_once "../config.php";
require_once "../vendor/autoload.php";
include "../classes/BorrowerManager.php";
include_once "../classes/class.Input.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use TCPDF;

if (isset($_GET['export'])) {
    $exportType = $_GET['export'];
    $sName = $_REQUEST['name']?$_REQUEST['name']:'';
    $sAmount = $_REQUEST['amount'] ? $_REQUEST['amount'] :'';
    $dFromDate = Input::request('sFromDate')??'';
    $dToDate = Input::request('sToDate')??'';

    // Fetch borrower details
    $borrowers = (new BorrowerManager())->getAllBorrowerDetails($sName,$sAmount,$dFromDate,$dToDate);

    // Map and filter required columns
    $filteredBorrowers = array_map(function ($borrower, $index) {
        return [
            'Sr. No'         => $index + 1,
            'Borrower Name'  => $borrower['name'] ?? '',
            'Phone Number'   => $borrower['phone_no'] ?? '',
            'Email'          => $borrower['email'] ?? '',
            'Loan Amount'    => $borrower['total_principal'] ?? '',
            'Disbursed Date' => $borrower['disbursed_date'] ?? '',
            'Closure Date'   => $borrower['closure_date'] ?? '',
            'Loan Status'    => $borrower['loan_status'] ?? ''
        ];
    }, $borrowers, array_keys($borrowers));

    if ($exportType === 'excel') {
        exportToExcel($filteredBorrowers);
    } elseif ($exportType === 'pdf') {
        exportToPDF($filteredBorrowers);
    }
}

function exportToExcel($data) {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Set title
    $title = "Borrower Report";
    $sheet->setCellValue('A1', $title);
    $sheet->mergeCells('A1:H1');
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    // Add header row
    $headers = array_keys($data[0]);
    $sheet->fromArray($headers, null, 'A2');
    $sheet->getStyle('A2:H2')->getFont()->setBold(true);
    $sheet->getStyle('A2:H2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    // Add data rows
    $row = 3;
    foreach ($data as $borrower) {
        $sheet->fromArray(array_values($borrower), null, "A$row");
        $row++;
    }

    // Auto-wrap text and adjust column widths
    foreach (range('A', 'H') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
        $sheet->getStyle("$col")->getAlignment()->setWrapText(true);
    }

    // Add borders to all cells
    $lastRow = $row - 1;
    $sheet->getStyle("A2:H$lastRow")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

    // Write file
    $writer = new Xlsx($spreadsheet);
    $filename = "Borrower_Report_" . date("Y-m-d_H-i-s") . ".xlsx";

    // Output to browser
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=$filename");
    $writer->save("php://output");
    exit;
}

function exportToPDF($data) {
    $pdf = new TCPDF();
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Loan Management System');
    $pdf->SetTitle('Borrower Report');
    $pdf->SetHeaderData('', 0, 'Borrower Report', 'Generated by Loan Management System', [0, 64, 255], [0, 64, 128]);
    $pdf->setFooterData([0, 64, 0], [0, 64, 128]);
    $pdf->SetMargins(10, 20, 10);
    $pdf->SetHeaderMargin(5);
    $pdf->SetFooterMargin(10);
    $pdf->SetAutoPageBreak(true, 20);
    $pdf->AddPage();

    // Generate table HTML
    $html = '<h2 style="text-align:center; font-size:16px;">Borrower Report</h2>';
    $html .= '<table border="1" cellpadding="5" cellspacing="0" style="width:100%; border-collapse: collapse; font-size:10pt;">
                <thead>
                    <tr style="background-color: #f2f2f2; font-weight: bold;">
                        <th scope="col">Sr. No</th>
                        <th scope="col">Borrower Name</th>
                        <th scope="col">Phone Number</th>
                        <th scope="col">Email</th>
                        <th scope="col">Loan Amount</th>
                        <th scope="col">Disbursed Date</th>
                        <th scope="col">Closure Date</th>
                        <th scope="col">Loan Status</th>
                    </tr>
                </thead>
                <tbody>';
    foreach ($data as $row) {
        $html .= '<tr>';
        foreach ($row as $cell) {
            $html .= "<td style='text-align: center;'>$cell</td>";
        }
        $html .= '</tr>';
    }
    $html .= '</tbody>
            </table>';

    // Add HTML to PDF
    $pdf->writeHTML($html, true, false, true, false, '');
    $filename = "Borrower_Report_" . date("Y-m-d_H-i-s") . ".pdf";

    // Output to browser
    $pdf->Output($filename, 'D');
    exit;
}
?>