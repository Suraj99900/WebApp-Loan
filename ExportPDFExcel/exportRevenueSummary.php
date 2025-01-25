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
    $startDate = Input::request('sFromDate') ?? '';
    $endDate = Input::request('sToDate') ?? '';
    $mis = new MIS();

    // Fetch monthly breakdown data
    $monthlyBreakdown = $mis->fetchMonthlyBreakdown($startDate, $endDate);
    $monthlyBreakdown = formatMonthlyBreakdownData($monthlyBreakdown);
    if ($exportType === 'excel') {
        exportToExcel($monthlyBreakdown);
    } elseif ($exportType === 'pdf') {
        exportToPDF($monthlyBreakdown);
    }
}

// Function to format monthly breakdown data
function formatMonthlyBreakdownData($data)
{
    return array_map(function ($item, $index) {
        return [
            'Sr. No' => $index + 1,
            'Month' => $item['month'] ?? '',
            'Total Payment' => formatCurrency($item['total_Payment'] ?? 0.00),
            'Total Interest Paid' => formatCurrency($item['total_interest_paid'] ?? 0.00),
            'Net Revenue' => formatCurrency($item['_total_net'] ?? 0.00),
            'Total Referral Amount' => formatCurrency($item['total_referral_amount'] ?? 0.00),
            'Total Penalty Amount' => formatCurrency($item['total_penalty_amount'] ?? 0.00),
            'Borrowers' => $item['total_borrower'] ?? 0,
        ];
    }, $data, array_keys($data));
}

function calculateMonthlyTotals($data)
{
    $totals = [
        'Total Payment' => 0,
        'Total Interest Paid' => 0,
        'Net Revenue' => 0,
        'Total Referral Amount' => 0,
        'Total Penalty Amount' => 0,
    ];

    foreach ($data as $row) {
        $totals['Total Payment'] += floatval(str_replace(',', '', $row['Total Payment']));
        $totals['Total Interest Paid'] += floatval(str_replace(',', '', $row['Total Interest Paid']));
        $totals['Net Revenue'] += floatval(str_replace(',', '', $row['Net Revenue']));
        $totals['Total Referral Amount'] += floatval(str_replace(',', '', $row['Total Referral Amount']));
        $totals['Total Penalty Amount'] += floatval(str_replace(',', '', $row['Total Penalty Amount']));
    }

    $totals['Total Payment'] = formatCurrency($totals['Total Payment']);
    $totals['Total Interest Paid'] = formatCurrency($totals['Total Interest Paid']);
    $totals['Net Revenue'] = formatCurrency($totals['Net Revenue']);
    $totals['Total Referral Amount'] = formatCurrency($totals['Total Referral Amount']);
    $totals['Total Penalty Amount'] = formatCurrency($totals['Total Penalty Amount']);

    return $totals;
}

// Export to Excel
function exportToExcel($data)
{
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Set title
    $sheet->setCellValue('A1', 'Monthly Revenue Breakdown')
        ->mergeCells('A1:H1')
        ->getStyle('A1')->getFont()->setBold(true)->setSize(16);
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('A1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('4CAF50');

    // Add header row
    $headers = array_keys($data[0]);
    $sheet->fromArray($headers, null, 'A2');
    $sheet->getStyle('A2:H2')->getFont()->setBold(true);
    $sheet->getStyle('A2:H2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('A2:H2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('DCE775');

    // Add data rows
    $row = 3;
    foreach ($data as $monthData) {
        $sheet->fromArray(array_values($monthData), null, "A$row");
        $row++;
    }

    // Add totals row
    $totals = calculateMonthlyTotals(data: $data);

    $sheet->setCellValue("B$row", 'Total:')->getStyle("E$row")->getFont()->setBold(true);
    $sheet->setCellValue("C$row", $totals['Total Payment'])->getStyle("F$row")->getFont()->setBold(true);
    $sheet->setCellValue("D$row", $totals['Total Interest Paid'])->getStyle("G$row")->getFont()->setBold(true);
    $sheet->setCellValue("E$row", $totals['Net Revenue'])->getStyle("H$row")->getFont()->setBold(true);
    $sheet->setCellValue("F$row", $totals['Total Referral Amount'])->getStyle("H$row")->getFont()->setBold(true);
    $sheet->setCellValue("G$row", $totals['Total Penalty Amount'])->getStyle("H$row")->getFont()->setBold(true);

    // // Adjust column widths and styles
    // foreach (range('A', 'H') as $col) {

    //     $sheet->getColumnDimension($col)->setAutoSize(true);
    //     $sheet->getStyle($col)->getAlignment()->setWrapText(true);
    //     $sheet->getStyle($col)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    // }

    $lastRow = $row;
    // Adjust column widths, wrap text, and apply borders for each cell
    foreach (range('A', 'H') as $col) {
        // Set AutoSize for each column individually
        $sheet->getColumnDimension($col)->setAutoSize(true);

        // Apply wrap text and border styles to each column range
        $sheet->getStyle($col . '2:' . $col . $lastRow)
            ->getAlignment()->setWrapText(true);  // Wrap text in each column

        $sheet->getStyle($col . '2:' . $col . $lastRow)
            ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN); // Add borders
    }

    // Output file
    outputExcel($spreadsheet, 'Monthly_Revenue_Report');
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

// Export to PDF
function exportToPDF($data)
{
    $pdf = new TCPDF();
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Web App Loan');
    $pdf->SetTitle('Monthly Revenue Breakdown');
    $pdf->SetHeaderData('', 0, 'Monthly Revenue Breakdown', 'Generated by Web App Loan', [0, 64, 255], [0, 64, 128]);
    $pdf->setFooterData([0, 64, 0], [0, 64, 128]);
    $pdf->SetMargins(10, 20, 10);
    $pdf->SetHeaderMargin(5);
    $pdf->SetFooterMargin(10);
    $pdf->SetAutoPageBreak(true, 20);
    $pdf->AddPage();

    $html = generatePDFHTML($data);
    $pdf->writeHTML($html, true, false, true, false, '');

    $filename = "Monthly_Revenue_Report_" . date("Y-m-d_H-i-s") . ".pdf";
    $pdf->Output($filename, 'D');
    exit;
}

function generatePDFHTML($data)
{
    $totals = calculateMonthlyTotals($data);

    $html = '<h2 style="text-align:center; font-size:16px;">Monthly Revenue Breakdown</h2>';
    $html .= '<table border="1" cellpadding="5" cellspacing="0" style="width:100%; border-collapse: collapse; font-size:10pt;">
                <thead>
                    <tr style="background-color: #4CAF50; color: white; font-weight: bold;">
                        <th>Sr. No</th>
                        <th>Month</th>
                        <th>Total Payment</th>
                        <th>Total Interest Paid</th>
                        <th>Net Revenue</th>
                        <th>Total Referral Amount</th>
                        <th>Total Penalty Amount</th>
                        <th>Borrowers</th>
                    </tr>
                </thead>
                <tbody>';

    foreach ($data as $row) {
        $html .= '<tr>';
        foreach ($row as $cell) {
            $html .= "<td style='text-align: center;'>" . htmlspecialchars($cell) . "</td>";
        }
        $html .= '</tr>';
    }

    $html .= '<tr style="font-weight: bold;">
                <td colspan="2" style="text-align: right;">Total:</td>
                <td style="text-align: center;">' . $totals['Total Payment'] . '</td>
                <td style="text-align: center;">' . $totals['Total Interest Paid'] . '</td>
                <td style="text-align: center;">' . $totals['Net Revenue'] . '</td>
                <td style="text-align: center;">' . $totals['Total Referral Amount'] . '</td>
                <td style="text-align: center;">' . $totals['Total Penalty Amount'] . '</td>
                <td></td>
              </tr>';

    $html .= '</tbody></table>';
    return $html;
}

function formatCurrency($amount)
{
    return number_format((float)$amount, 2, '.', ',');
}
