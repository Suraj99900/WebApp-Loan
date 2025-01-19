<?php
// Global Config File
define('DB_CONNECTION', 'mysql');
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_DATABASE', 'app_loan');
define('DB_USER', 'root');
define('DB_PASSWORD', '99900');
define('API_URL','http://localhost/WebApp-Loan/');
define("ABS_PATH_TO_PROJECT","/var/www/html/WebApp-Loan/");
define('ABS_URL','http://localhost/WebApp-Loan/');
define('DASHBOARD_INDEX_LOCATION','http://localhost/WebApp-Loan/view/Dashboard.php');


// Project Name Configuration
define("ORG_NAME","WebApp Loan");
define("DEFAULT_PENALTY","100");
define("DEFAULT_DAY","10");

// PortFolio
define('INDEX_LOCATION','http://localhost/WebApp-Loan/pages-login.php');
define('INDEX_ENABLE',true);