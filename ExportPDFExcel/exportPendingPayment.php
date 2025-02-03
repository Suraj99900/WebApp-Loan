<?php
require_once "../config.php";
require_once "../vendor/autoload.php";
include "../classes/EMIScheduleManager.php";
include_once "../classes/class.Input.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use TCPDF;

if (isset($_GET['export'])) {
    $exportType = $_GET['export'];
    $iBorrowerId = Input::request('borrowerId');
    $dFromDate = Input::request('sFromDate') ?? '';
    $dToDate = Input::request('sToDate') ?? '';
    $sLoanAmount = Input::request('sLoanAmount') ?? '';
    $sOnlyBorrowerId = Input::request('sOnlyBorrowerId') ?? '';
    $bOnlyPending = Input::request('sOnlyPending') ?? false;

    $emiManager = new EMIScheduleManager();
    $oEMISchedules = $emiManager->getAllEMISchedulesByBorrowerId($iBorrowerId, $dFromDate, $dToDate, $sLoanAmount,$bOnlyPending, $sOnlyBorrowerId);

    if($bOnlyPending && $sOnlyBorrowerId){
        foreach ($oEMISchedules as $loan) {
            
            if ($loan['payment_status'] === 'pending') {
                $dueDate = new DateTime($loan['payment_due_date']);
                $currentDate = new DateTime();
                // Check if payment_due_date is more than one month past
                while ($dueDate < $currentDate) {
                    // Increment due date by one month
                    $dueDate->modify('+1 month');
                    $newRow = $loan;
                    $newRow['payment_due_date'] = $dueDate->format('Y-m-d');
                    $oEMISchedules[] = $newRow;

                    
                }
            }
        }
    }

    $filteredBorrowers = formatBorrowerData($oEMISchedules);

    if ($exportType === 'excel') {
        exportToExcel($filteredBorrowers);
    } elseif ($exportType === 'pdf') {
        exportToPDF($filteredBorrowers);
    }
}

function formatBorrowerData($borrowers)
{
    return array_map(function ($borrower, $index) {
        return [
            'Sr. No' => $index + 1,
            'Borrower Name' => $borrower['name'] ? $borrower['name'].' ('. $borrower['unique_borrower_id'].")" : '',
            'Pending Amount (Interest/EMI)' => $borrower['emi_amount'] ?? '',
            'Principal Repaid' => $borrower['principal_repaid'] ?? '',
            'Due Date' => date('M d Y', strtotime($borrower['payment_due_date'])) ?? '',
            'Status' => $borrower['payment_status'] ?? '',
        ];
    }, $borrowers, array_keys($borrowers));
}

function exportToExcel($data)
{
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $bOnlyPending = Input::request('sOnlyPending') ?? false;
    $sOnlyBorrowerId = Input::request('sOnlyBorrowerId') ?? '';

    // Set title
    $sheet->setCellValue('A1', 'Pending Payment')
        ->mergeCells('A1:G1')
        ->getStyle('A1')->getFont()->setBold(true)->setSize(16);
    $sheet->getStyle(cellCoordinate: 'A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    // Add header row
    $headers = array_keys($data[0]);
    $sheet->fromArray($headers, null, 'A2');
    $sheet->getStyle('A2:G2')->getFont()->setBold(true);
    $sheet->getStyle('A2:G2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    // Add data rows
    $row = 3;
    $totalPendingAmount = 0;
    $totalPrincipalRepaid = 0;
    $totalOutstandingPrincipal = 0;

    foreach ($data as $borrower) {
        $sheet->fromArray(array_values($borrower), null, "A$row");

        // Add to totals
        $totalPendingAmount += floatval($borrower['Pending Amount (Interest/EMI)']);
        $totalPrincipalRepaid += floatval($borrower['Principal Repaid']);
        $totalOutstandingPrincipal += floatval($borrower['Outstanding Principal']);

        $row++;
    }

    // Add total row
    $sheet->setCellValue('A' . $row, 'Total')
        ->setCellValue('C' . $row, $totalPendingAmount)
        ->setCellValue('D' . $row, $totalPrincipalRepaid);
        // ->setCellValue('E' . $row, (($bOnlyPending && $sOnlyBorrowerId) ? '' : $totalOutstandingPrincipal));

    $sheet->getStyle("A$row:E$row")->getFont()->setBold(true);
    $sheet->getStyle("A$row:E$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    // Adjust column widths and styles
    foreach (range('A', 'G') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
        $sheet->getStyle($col)->getAlignment()->setWrapText(true);
    }

    $lastRow = $row;
    $sheet->getStyle("A2:G$lastRow")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

    // Output file
    outputExcel($spreadsheet, 'Pending Payment');
}


function outputExcel($spreadsheet, $filename)
{
    $writer = new Xlsx($spreadsheet);
    $filename .= "_" . date("Y-m-d_H-i-s") . ".xlsx";

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=$filename");
    $writer->save("php://output");
    exit;
}

function exportToPDF($data)
{
    $pdf = new TCPDF();
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('SM Loan');
    $pdf->SetTitle('Pending Payment');
    $pdf->SetHeaderData('', 0, 'Pending Payment', 'Generated by SM Loan', [0, 64, 255], [0, 64, 128]);
    $pdf->setFooterData([0, 64, 0], [0, 64, 128]);
    $pdf->SetMargins(10, 20, 10);
    $pdf->SetHeaderMargin(5);
    $pdf->SetFooterMargin(10);
    $pdf->SetAutoPageBreak(true, 20);
    $pdf->AddPage();
    $bOnlyPending = Input::request('sOnlyPending') ?? false;
    $sOnlyBorrowerId = Input::request('sOnlyBorrowerId') ?? '';

    $html = generatePDFHTML($data);

    // Calculate totals
    $totalPendingAmount = 0;
    $totalPrincipalRepaid = 0;
    $totalOutstandingPrincipal = 0;

    foreach ($data as $borrower) {
        $totalPendingAmount += floatval($borrower['Pending Amount (Interest/EMI)']);
        $totalPrincipalRepaid += floatval($borrower['Principal Repaid']);
        $totalOutstandingPrincipal += floatval($borrower['Outstanding Principal']);
    }

    // Add totals to the HTML
    $html .= '<tr style="background-color: #f2f2f2; font-weight: bold; border:1px solid black;">
            <td colspan="2" style="text-align:center; font-weight:bold; border:1px solid black;">Total</td>
            <td style="text-align:center; font-weight:bold; border:1px solid black;">' . $totalPendingAmount . '</td>
            <td style="text-align:center; font-weight:bold; border:1px solid black;">' . $totalPrincipalRepaid . '</td>
            <td colspan="2" style="border:1px solid black;"></td>
          </tr>';


    $html .= '</tbody></table>';
    $pdf->writeHTML($html, true, false, true, false, '');

    $filename = "Pending_Payment_" . date("Y-m-d_H-i-s") . ".pdf";
    $pdf->Output($filename, 'D');
    exit;
}


function generatePDFHTML($data)
{
    $html = '<h2 style="text-align:center; font-size:16px;">Pending Payment</h2>';
    $html .= '<table border="1" cellpadding="5" cellspacing="0" style="width:100%; border-collapse: collapse; font-size:10pt;">
                <thead>
                    <tr style="background-color: #f2f2f2; font-weight: bold;">
                        <th>Sr. No</th>
                        <th>Borrower Name</th>
                        <th>Pending Amount (Interest/EMI)</th>
                        <th>Principal Repaid</th>
                        <th>Due Date</th>
                        <th>Status</th>
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

    $html .= '</tbody></table>';
    return $html;
}
