# SM Loan Management System

## Overview

The SM Loan Management System is a comprehensive web application designed to streamline the management of loan applications, approvals, and repayments. Built with PHP 8.3 and utilizing Composer for dependency management, this system offers a robust and scalable solution for financial institutions.

## Prerequisites

Before setting up the project, ensure you have the following installed:

- **PHP 8.3**: The latest version of PHP.
- **Composer**: A dependency management tool for PHP.
- **MySQL**: A relational database management system.

## Installation

Follow these steps to set up the SM Loan Management System:

1. **Clone the Repository**:

   ```bash
   git clone https://github.com/Suraj99900/WebApp-Loan.git
   ```

2. **Navigate to the Project Directory**:

   ```bash
   cd WebApp-Loan
   ```

3. **Install Dependencies**:

   ```bash
   composer install
   ```

4. **Set Up the Database**:

   - Navigate to the `sql` folder in the project directory.
   - Execute the SQL queries to create the necessary database and tables.

5. **Configure Database Connection**:

   Edit the `config.php` file to set your database connection details:

   ```php
   <?php
   // Global Config File
   define('DB_CONNECTION', 'mysql');
   define('DB_HOST', '127.0.0.1');
   define('DB_PORT', '3306');
   define('DB_DATABASE', 'app_loan');
   define('DB_USER', 'root');
   define('DB_PASSWORD', 'your_password_here');
   define('API_URL','http://localhost/WebApp-Loan/');
   define("ABS_PATH_TO_PROJECT","/var/www/html/WebApp-Loan/");
   define('ABS_URL','http://localhost/WebApp-Loan/');
   define('DASHBOARD_INDEX_LOCATION','http://localhost/WebApp-Loan/view/Dashboard.php');

   // Project Name Configuration
   define("ORG_NAME","SM Loan");
   define("DEFAULT_PENALTY","10");
   define("DEFAULT_DAY","10");

   // PortFolio
   define('INDEX_LOCATION','http://localhost/WebApp-Loan/pages-login.php');
   define('INDEX_ENABLE',true);
   ```

   Replace `'your_password_here'` with your actual MySQL password.

## Dependencies

The project utilizes the following Composer packages:

- **doctrine/dbal**: Provides a database abstraction layer.
- **tecnickcom/tcpdf**: A PHP class for generating PDF documents.
- **phpoffice/phpspreadsheet**: A library for reading and writing spreadsheet files.
- **phpmailer/phpmailer**: A full-featured email creation and transfer class for PHP.

## Usage

1. **Start the Development Server**:

   ```bash
   php -S localhost:8000
   ```

2. **Access the Application**:

   Open your browser and navigate to `http://localhost:8000` to access the SM Loan Management System.

## License

This project is licensed under the MIT License.

## Acknowledgments

Special thanks to the contributors and the open-source community for their invaluable support. 
