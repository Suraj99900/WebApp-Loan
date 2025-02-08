<?php
require_once "../config.php";
require_once "../classes/DB-Connection.php";

$db = new DBConnection();

// Step 1: Generate the database dump
$dumpFilePath = ABS_PATH_TO_PROJECT . 'backup/' . DB_DATABASE . '_dump_' . date('Y-m-d_H-i-s') . '.sql';
$db->dumpDatabase($dumpFilePath);

// Step 2: Create a ZIP backup of the uploads directories
$directoriesToZip = [
    ABS_PATH_TO_PROJECT . 'uploads/borrower_documents',
    ABS_PATH_TO_PROJECT . 'uploads/payment_doc',
    ABS_PATH_TO_PROJECT . 'uploads/referral_document'
];
$zipFilePath = ABS_PATH_TO_PROJECT . 'backup/uploads_backup_' . date('Y-m-d_H-i-s') . '.zip';
$db->createZipBackup($directoriesToZip, $zipFilePath);

// Step 3: Send both the database dump and the ZIP backup via email
$recipientEmail = 'smloan7@gmail.com'; // Replace with the recipient's email
$db->sendBackupByEmail($recipientEmail, $dumpFilePath, $zipFilePath);

// Step 4: Redirect to the previous page (or fallback to index.php)
$previousLocation = $_SERVER['HTTP_REFERER'] ?? 'index.php';
header("Location: $previousLocation?message=Database+dump+and+uploads+backup+sent+successfully");
exit();
