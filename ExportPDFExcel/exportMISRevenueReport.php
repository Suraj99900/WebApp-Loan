<?php
require_once "../config.php";
require_once "../vendor/autoload.php";
include "../classes/MIS.php";
include_once "../classes/class.Input.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use TCPDF;

if (isset($_GET['export'])) {
    $exportType = $_GET['export'];
    $borrowerId = Input::request('borrowerId') ?? '';
    $startDate = Input::request('sFromDate') ?? '';
    $endDate = Input::request('sToDate') ?? '';
    $sLoanAmount = Input::request('sLoanAmount') ?? '';
    $principalAmount = Input::request('principalAmount') ?? '';
    $mis = new MIS();

    $aRevenueReport = $mis->getRevenueReport($borrowerId, $startDate, $endDate, $sLoanAmount, $principalAmount);

    $filteredBorrowers = formatBorrowerData($aRevenueReport);

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
            'Borrower Name' => $borrower['name'] ?? '',
            'Interest Collected' => formatCurrency($borrower['total_interest'] ?? 0.00),
            'Penalties Collected' => formatCurrency($borrower['penalty_income'] ?? 0.00),
            'Referral Collected' => formatCurrency($borrower['referral_expense'] ?? 0.00),
            'Net Revenue' => formatCurrency($borrower['net_revenue'] ?? 0.00),
            'EMI COUNT' => $borrower['emi_count'] ?? 0,
            'Outstanding Principal' => formatCurrency($borrower['outstanding_principal'] ?? 0.00),
            'Principal Repaid' => formatCurrency($borrower['repaid_principal'] ?? 0.00),
            'Total Payment Done' => formatCurrency($borrower['total_paid_by_borrower'] ?? 0.00),
            'Status' => $borrower['loan_status'] ?? '',
        ];
    }, $borrowers, array_keys($borrowers));
}

function calculateTotals($data)
{
    $totals = [
        'Total Interest Collected' => 0,
        'Total Penalties Collected' => 0,
        'Total Referral Collected' => 0,
        'Total Net Revenue' => 0,
        'Total EMI Count' => 0,
        'Total Outstanding Principal' => 0,
        'Total Principal Repaid' => 0,
        'Total Payment Done' => 0,
    ];

    foreach ($data as $row) {
        $totals['Total Interest Collected'] += floatval(str_replace(',', '', $row['Interest Collected']));
        $totals['Total Penalties Collected'] += floatval(str_replace(',', '', $row['Penalties Collected']));
        $totals['Total Referral Collected'] += floatval(str_replace(',', '', $row['Referral Collected']));
        $totals['Total Net Revenue'] += floatval(str_replace(',', '', $row['Net Revenue']));
        $totals['Total EMI Count'] += intval($row['EMI COUNT']);
        $totals['Total Outstanding Principal'] += floatval(str_replace(',', '', $row['Outstanding Principal']));
        $totals['Total Principal Repaid'] += floatval(str_replace(',', '', $row['Principal Repaid']));
        $totals['Total Payment Done'] += floatval(str_replace(',', '', $row['Total Payment Done']));
    }

    // Format totals
    $totals['Total Interest Collected'] = formatCurrency($totals['Total Interest Collected']);
    $totals['Total Penalties Collected'] = formatCurrency($totals['Total Penalties Collected']);
    $totals['Total Referral Collected'] = formatCurrency($totals['Total Referral Collected']);
    $totals['Total Net Revenue'] = formatCurrency($totals['Total Net Revenue']);
    $totals['Total Outstanding Principal'] = formatCurrency($totals['Total Outstanding Principal']);
    $totals['Total Principal Repaid'] = formatCurrency($totals['Total Principal Repaid']);
    $totals['Total Payment Done'] = formatCurrency($totals['Total Payment Done']);

    return $totals;
}

function exportToExcel($data)
{
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Set title
    $sheet->setCellValue('A1', 'Revenue Report')
        ->mergeCells('A1:J1')
        ->getStyle('A1')->getFont()->setBold(true)->setSize(16);
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    // Add header row
    $headers = array_keys($data[0]);
    $sheet->fromArray($headers, null, 'A2');
    $sheet->getStyle('A2:J2')->getFont()->setBold(true);
    $sheet->getStyle('A2:J2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    // Add data rows
    $row = 3;
    foreach ($data as $borrower) {
        $sheet->fromArray(array_values($borrower), null, "A$row");
        $row++;
    }

    // Add totals row
    $totals = calculateTotals($data);
    $sheet->setCellValue("A$row", 'Total:')->getStyle("A$row")->getFont()->setBold(true);
    $sheet->setCellValue("C$row", $totals['Total Interest Collected'])->getStyle("C$row")->getFont()->setBold(true);
    $sheet->setCellValue("D$row", $totals['Total Penalties Collected'])->getStyle("D$row")->getFont()->setBold(true);
    $sheet->setCellValue("E$row", $totals['Total Referral Collected'])->getStyle("E$row")->getFont()->setBold(true);
    $sheet->setCellValue("F$row", $totals['Total Net Revenue'])->getStyle("F$row")->getFont()->setBold(true);
    $sheet->setCellValue("H$row", $totals['Total Outstanding Principal'])->getStyle("H$row")->getFont()->setBold(true);
    $sheet->setCellValue("I$row", $totals['Total Principal Repaid'])->getStyle("H$row")->getFont()->setBold(true);
    $sheet->setCellValue("J$row", $totals['Total Payment Done'])->getStyle("J$row")->getFont()->setBold(true);

    // Adjust column widths and styles
    foreach (range('A', 'J') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
        $sheet->getStyle($col)->getAlignment()->setWrapText(true);
    }

    $lastRow = $row;
    $sheet->getStyle("A2:J$lastRow")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

    // Output file
    outputExcel($spreadsheet, 'Revenue Report');
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
    $pdf->SetTitle('Revenue Report');
    $pdf->SetHeaderData('', 0, 'Revenue Report', 'Generated by SM Loan', [0, 64, 255], [0, 64, 128]);
    $pdf->setFooterData([0, 64, 0], [0, 64, 128]);
    $pdf->SetMargins(10, 20, 10);
    $pdf->SetHeaderMargin(5);
    $pdf->SetFooterMargin(10);
    $pdf->SetAutoPageBreak(true, 20);
    $pdf->AddPage();

    $html = generatePDFHTML($data);
    $pdf->writeHTML($html, true, false, true, false, '');

    $filename = "Revenue_Report_" . date("Y-m-d_H-i-s") . ".pdf";
    $pdf->Output($filename, 'D');
    exit;
}

function generatePDFHTML($data)
{
    $totals = calculateTotals($data);

    $html = '<h2 style="text-align:center; font-size:16px;">Revenue Report</h2>';
    $html .= '<table border="1" cellpadding="5" cellspacing="0" style="width:100%; border-collapse: collapse; font-size:10pt;">
                <thead>
                    <tr style="background-color: #f2f2f2; font-weight: bold;">
                        <th>Sr. No</th>
                        <th>Borrower Name</th>
                        <th>Interest Collected</th>
                        <th>Penalties Collected</th>
                        <th>Referral Collected</th>
                        <th>Net Revenue</th>
                        <th>EMI COUNT</th>
                        <th>Outstanding Principal</th>
                        <th>Principal Repaid</th>
                        <th>Total Payment Done</th>
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

    $html .= '<tr style="font-weight: bold;">
                <td colspan="1" style="text-align: right;">Total:</td>
                <td style="text-align: center;"></td>
                <td>' . $totals['Total Interest Collected'] . '</td>
                <td>' . $totals['Total Penalties Collected'] . '</td>
                <td>' . $totals['Total Referral Collected'] . '</td>
                <td style="text-align: center;">' . $totals['Total Payment Done'] . '</td>
                <td>' . $totals['Total EMI Count'] . '</td>
                <td>' . $totals['Total Outstanding Principal'] . '</td>
                <td>' . $totals['Total Principal Repaid'] . '</td>
                <td>' . $totals['Total Net Revenue'] . '</td>
              </tr>';

    $html .= '</tbody></table>';
    return $html;
}

function formatCurrency($amount)
{
    return number_format((float)$amount, 2, '.', ',');
}
