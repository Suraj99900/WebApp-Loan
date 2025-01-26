<?php
require_once '../config.php';
require_once '../vendor/autoload.php'; // Include Composer's autoloader
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class DBConnection
{
    private $sServername = DB_HOST;
    private $sUsername = DB_USER;
    private $sPassword = DB_PASSWORD;
    private $sDatabase = DB_DATABASE;
    public $conn;

    function __construct()
    {
        $this->connect();
    }

    private function connect()
    {
        try {
            $config = new Configuration();
            $connectionParams = [
                'dbname' => $this->sDatabase,
                'user' => $this->sUsername,
                'password' => $this->sPassword,
                'host' => $this->sServername,
                'driver' => 'pdo_mysql', // Change this based on your database type
            ];
            $this->conn = DriverManager::getConnection($connectionParams, $config);
        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    public function dumpDatabase($outputFile)
    {
        try {
            // Ensure the backup directory exists
            $backupDir = dirname($outputFile);
            if (!is_dir($backupDir)) {
                mkdir($backupDir, 0777, true);  // Create the directory if it doesn't exist
            }

            $tables = $this->conn->fetchAllAssociative('SHOW TABLES');
            $dumpContent = '';

            foreach ($tables as $tableRow) {
                $tableName = array_values($tableRow)[0];

                // Get CREATE TABLE statement for structure
                $createTable = $this->conn->fetchAssociative("SHOW CREATE TABLE `$tableName`");
                if ($createTable) {
                    $dumpContent .= "\n\n-- Structure of table `$tableName`\n";
                    $dumpContent .= $createTable['Create Table'] . ";\n"; // Access 'Create Table' key to get the SQL statement
                } else {
                    $dumpContent .= "\n\n-- Structure of table `$tableName` could not be retrieved\n";
                }

                // Fetch data from the table (INSERT statements)
                $rows = $this->conn->fetchAllAssociative("SELECT * FROM `$tableName`");
                $dumpContent .= "-- Data for table `$tableName`\n";
                foreach ($rows as $row) {
                    $columns = implode('`, `', array_keys($row));
                    // Ensure null values are handled by replacing them with empty strings
                    $values = implode("', '", array_map(function ($value) {
                        return $value === null ? '' : $value;  // Replace null with empty string
                    }, array_values($row)));

                    // Add INSERT INTO statement
                    $dumpContent .= "INSERT INTO `$tableName` (`$columns`) VALUES ('$values');\n";
                }
            }

            // Write the dump content to a file
            file_put_contents($outputFile, $dumpContent);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function sendDumpByEmail($toEmail, $dumpFilePath)
    {
        try {
            $mail = new PHPMailer(true);

            // SMTP configuration
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'smloan7@gmail.com';
            $mail->Password = 'eokc rksp smsi nnxd';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Email settings
            $mail->setFrom('smloan7@gmail.com', 'Database Dump Service'); // Sender
            $mail->addAddress($toEmail); // Recipient
            $mail->Subject = 'Database Dump File';
            $mail->Body = 'Please find the attached database dump file.';
            $mail->addAttachment($dumpFilePath); // Attach the dump file

            // Send email
            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
