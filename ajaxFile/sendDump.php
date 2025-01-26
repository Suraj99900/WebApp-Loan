<?php
require_once "../config.php";
require_once "../classes/DB-Connection.php";

$db = new DBConnection();

// Step 1: Generate the database dump
$dumpFilePath = ABS_PATH_TO_PROJECT.'backup/' . DB_DATABASE . '_dump_' . date('Y-m-d_H-i-s') . '.sql';
$db->dumpDatabase($dumpFilePath);

// Step 2: Send the dump via email
$recipientEmail = 'smloan7@gmail.com'; // Replace with the recipient's email
$db->sendDumpByEmail($recipientEmail, $dumpFilePath);

// Step 3: Redirect to the previous page (original location)
$previousLocation = $_SERVER['HTTP_REFERER'] ?? 'index.php'; // Fallback to index.php if HTTP_REFERER is not set
header("Location: $previousLocation?message=Database+dump+sent+successfully");
exit();
