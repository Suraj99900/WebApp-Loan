
-- CREATING BORROWER MASTER TABLE

CREATE TABLE app_borrower_master (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone_no VARCHAR(255) NOT NULL UNIQUE,
    gender varchar(255),
    address TEXT,
    email VARCHAR(100),
    added_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status TINYINT DEFAULT 1,
    deleted TINYINT DEFAULT 0
);



-- Table: Document UPLOAD
CREATE TABLE app_borrower_document (
    id INT AUTO_INCREMENT PRIMARY KEY,
    borrower_id int(11) NOT NULL,
    document_name VARCHAR(100),
    document_path VARCHAR(255),
    added_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status TINYINT DEFAULT 1,
    deleted TINYINT DEFAULT 0,
    FOREIGN KEY (borrower_id) REFERENCES app_borrower_master(id)
);




-- Table: LOAN DETAILS
CREATE TABLE app_loan_details (
    loan_id INT AUTO_INCREMENT PRIMARY KEY,
    borrower_id INT,
    principal_amount DECIMAL(10, 2) NOT NULL,
    interest_rate DECIMAL(5, 2) NOT NULL,
    loan_period INT NOT NULL,
    disbursed_date DATE NOT NULL,
    top_up_payment DECIMAL(10, 2) DEFAULT 0,
    EMI_amount DECIMAL(10, 2),
    closure_amount DECIMAL(10, 2),
    closure_date DATE,
    loan_status VARCHAR(20) DEFAULT 'active',
    added_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status TINYINT DEFAULT 1,
    deleted TINYINT DEFAULT 0,
    FOREIGN KEY (borrower_id) REFERENCES app_borrower_master(id)
);


-- Table: Payments Details
CREATE TABLE app_borrower_loan_payments (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    loan_id INT,
    payment_amount DECIMAL(10, 2),
    penalty_amount DECIMAL(10, 2) DEFAULT 0,
    referral_share_amount DECIMAL(10, 2) DEFAULT 0,
    final_amount DECIMAL(10, 2),
    received_date DATE,
    interest_paid DECIMAL(10, 2) DEFAULT 0,
    principal_paid DECIMAL(10, 2) DEFAULT 0,
    payment_status VARCHAR(20) DEFAULT 'pending',
    mode_of_payment VARCHAR(50),
    interest_date DATE,
    status TINYINT DEFAULT 1,
    deleted TINYINT DEFAULT 0,
    FOREIGN KEY (loan_id) REFERENCES app_loan_details(loan_id)
);

-- Table: Referral_User
CREATE TABLE app_referral_user (
    reference_Id INT AUTO_INCREMENT PRIMARY KEY,
    ref_name VARCHAR(100),
    ref_phone_number VARCHAR(15) UNIQUE,
    ref_percentage DECIMAL(5, 2),
    added_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status TINYINT DEFAULT 1,
    deleted TINYINT DEFAULT 0
);


-- Table: Borrower_Ref_Map
CREATE TABLE app_borrower_ref_map (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reference_Id INT,
    borrower_id INT,
    relationship VARCHAR(50),
    added_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status TINYINT DEFAULT 1,
    deleted TINYINT DEFAULT 0,
    FOREIGN KEY (reference_Id) REFERENCES app_referral_user(reference_Id),
    FOREIGN KEY (borrower_id) REFERENCES app_borrower_master(id)
);

-- Table: Loan_Transactions
CREATE TABLE app_loan_transactions (
    transaction_id INT AUTO_INCREMENT PRIMARY KEY,
    loan_id INT,
    transaction_type VARCHAR(50),
    amount DECIMAL(10, 2),
    transaction_date DATE,
    added_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status TINYINT DEFAULT 1,
    deleted TINYINT DEFAULT 0,
    FOREIGN KEY (loan_id) REFERENCES app_loan_details(loan_id)
);


-- Table: EMI Schedule
CREATE TABLE app_loan_emi_schedule (
    schedule_id INT AUTO_INCREMENT PRIMARY KEY,
    loan_id INT,
    month_no INT NOT NULL,
    beginning_principal DECIMAL(10, 2) NOT NULL,
    interest_amount DECIMAL(10, 2) NOT NULL,
    emi_amount DECIMAL(10, 2) NOT NULL,
    principal_repaid DECIMAL(10, 2) NOT NULL,
    ending_principal DECIMAL(10, 2) NOT NULL,
    payment_due_date DATE NOT NULL,
    penalty_amount DECIMAL(10, 2) DEFAULT 0,
    payment_status VARCHAR(20) DEFAULT 'pending',
    added_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status TINYINT DEFAULT 1,
    deleted TINYINT DEFAULT 0,
    FOREIGN KEY (loan_id) REFERENCES app_loan_details(loan_id)
);

-- Table: Revenue_Report
CREATE TABLE app_revenue_report (
    report_id INT AUTO_INCREMENT PRIMARY KEY,
    loan_id INT,
    total_interest DECIMAL(10, 2),
    penalty_income DECIMAL(10, 2),
    referral_expense DECIMAL(10, 2),
    net_revenue DECIMAL(10, 2),
    emi_count INT DEFAULT 0,
    outstanding_principal DECIMAL(10, 2) DEFAULT 0,
    calculated_on DATE,
    added_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status TINYINT DEFAULT 1,
    deleted TINYINT DEFAULT 0,
    FOREIGN KEY (loan_id) REFERENCES app_loan_details(loan_id)
);


-- Table: REFERRAL Document UPLOAD
CREATE TABLE app_referral_document (
    id INT AUTO_INCREMENT PRIMARY KEY,
    referral_id int(11) NOT NULL,
    document_name VARCHAR(100),
    document_path VARCHAR(255),
    added_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status TINYINT DEFAULT 1,
    deleted TINYINT DEFAULT 0,
    FOREIGN KEY (reference_Id) REFERENCES app_referral_user(reference_Id)
);
