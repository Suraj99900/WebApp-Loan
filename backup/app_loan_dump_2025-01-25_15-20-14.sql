

-- Structure of table `app_borrower_document`
CREATE TABLE `app_borrower_document` (
  `id` int NOT NULL AUTO_INCREMENT,
  `borrower_id` int NOT NULL,
  `document_name` varchar(100) DEFAULT NULL,
  `document_path` varchar(255) DEFAULT NULL,
  `added_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint DEFAULT '1',
  `deleted` tinyint DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `borrower_id` (`borrower_id`),
  CONSTRAINT `app_borrower_document_ibfk_1` FOREIGN KEY (`borrower_id`) REFERENCES `app_borrower_master` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
-- Data for table `app_borrower_document`
INSERT INTO `app_borrower_document` (`id`, `borrower_id`, `document_name`, `document_path`, `added_on`, `status`, `deleted`) VALUES ('1', '1', 'Aadhar Card', 'uploads/borrower_documents/2025-01-20_16-17-24_1e8355e5-d77e-469e-be27-3e4b64fca9c1.jpeg', '2025-01-20 16:17:24', '1', '0');
INSERT INTO `app_borrower_document` (`id`, `borrower_id`, `document_name`, `document_path`, `added_on`, `status`, `deleted`) VALUES ('2', '1', 'ATM Card', 'uploads/borrower_documents/2025-01-20_16-17-24_3d14a517-7b18-4fe6-906c-2acfa160bf62.png', '2025-01-20 16:17:24', '1', '0');
INSERT INTO `app_borrower_document` (`id`, `borrower_id`, `document_name`, `document_path`, `added_on`, `status`, `deleted`) VALUES ('3', '2', 'Aadhar Card', 'uploads/borrower_documents/2025-01-20_16-17-48_Loan_Agreement (5).pdf', '2025-01-20 16:17:48', '1', '0');
INSERT INTO `app_borrower_document` (`id`, `borrower_id`, `document_name`, `document_path`, `added_on`, `status`, `deleted`) VALUES ('4', '2', 'ATM Card', 'uploads/borrower_documents/2025-01-20_16-17-48_f83d8064-4add-4ce3-84b5-412acf1415a7.jpeg', '2025-01-20 16:17:48', '1', '0');
INSERT INTO `app_borrower_document` (`id`, `borrower_id`, `document_name`, `document_path`, `added_on`, `status`, `deleted`) VALUES ('5', '5', 'Aadhar Card', 'uploads/borrower_documents/2025-01-21_14-50-07_1e8355e5-d77e-469e-be27-3e4b64fca9c1.jpeg', '2025-01-21 14:50:07', '1', '0');
INSERT INTO `app_borrower_document` (`id`, `borrower_id`, `document_name`, `document_path`, `added_on`, `status`, `deleted`) VALUES ('6', '5', 'ATM Card', 'uploads/borrower_documents/2025-01-21_14-50-07_1e8355e5-d77e-469e-be27-3e4b64fca9c1.jpeg', '2025-01-21 14:50:07', '1', '0');
INSERT INTO `app_borrower_document` (`id`, `borrower_id`, `document_name`, `document_path`, `added_on`, `status`, `deleted`) VALUES ('7', '6', 'Aadhar Card', 'uploads/borrower_documents/2025-01-21_14-56-40_1e8355e5-d77e-469e-be27-3e4b64fca9c1.jpeg', '2025-01-21 14:56:40', '1', '0');
INSERT INTO `app_borrower_document` (`id`, `borrower_id`, `document_name`, `document_path`, `added_on`, `status`, `deleted`) VALUES ('8', '6', 'ATM Card', 'uploads/borrower_documents/2025-01-21_14-56-40_1e8355e5-d77e-469e-be27-3e4b64fca9c1.jpeg', '2025-01-21 14:56:40', '1', '0');
INSERT INTO `app_borrower_document` (`id`, `borrower_id`, `document_name`, `document_path`, `added_on`, `status`, `deleted`) VALUES ('9', '8', 'Aadhar Card', 'uploads/borrower_documents/2025-01-21_16-39-37_1e8355e5-d77e-469e-be27-3e4b64fca9c1.jpeg', '2025-01-21 16:39:37', '1', '0');
INSERT INTO `app_borrower_document` (`id`, `borrower_id`, `document_name`, `document_path`, `added_on`, `status`, `deleted`) VALUES ('10', '9', 'ATM Card', 'uploads/borrower_documents/2025-01-25_12-57-14_1e8355e5-d77e-469e-be27-3e4b64fca9c1.jpeg', '2025-01-25 12:57:14', '1', '0');
INSERT INTO `app_borrower_document` (`id`, `borrower_id`, `document_name`, `document_path`, `added_on`, `status`, `deleted`) VALUES ('11', '9', 'Aadhar Card', 'uploads/borrower_documents/2025-01-25_12-57-14_Borrower_Report_2025-01-24_16-40-52.pdf', '2025-01-25 12:57:14', '1', '0');
INSERT INTO `app_borrower_document` (`id`, `borrower_id`, `document_name`, `document_path`, `added_on`, `status`, `deleted`) VALUES ('12', '10', 'Aadhar Card', 'uploads/borrower_documents/2025-01-25_15-00-22_WhatsApp Image 2025-01-25 at 8.29.25 PM (1).jpeg', '2025-01-25 15:00:22', '1', '0');
INSERT INTO `app_borrower_document` (`id`, `borrower_id`, `document_name`, `document_path`, `added_on`, `status`, `deleted`) VALUES ('13', '10', 'ATM Card', 'uploads/borrower_documents/2025-01-25_15-00-22_WhatsApp Image 2025-01-25 at 8.29.25 PM.jpeg', '2025-01-25 15:00:22', '1', '0');


-- Structure of table `app_borrower_loan_payments`
CREATE TABLE `app_borrower_loan_payments` (
  `payment_id` int NOT NULL AUTO_INCREMENT,
  `loan_id` int DEFAULT NULL,
  `payment_amount` decimal(10,2) DEFAULT NULL,
  `penalty_amount` decimal(10,2) DEFAULT '0.00',
  `referral_share_amount` decimal(10,2) DEFAULT '0.00',
  `final_amount` decimal(10,2) DEFAULT NULL,
  `received_date` date DEFAULT NULL,
  `interest_paid` decimal(10,2) DEFAULT '0.00',
  `principal_paid` decimal(10,2) DEFAULT '0.00',
  `payment_status` varchar(20) DEFAULT 'pending',
  `mode_of_payment` varchar(50) DEFAULT NULL,
  `comments` varchar(255) DEFAULT NULL,
  `interest_date` date DEFAULT NULL,
  `added_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint DEFAULT '1',
  `deleted` tinyint DEFAULT '0',
  PRIMARY KEY (`payment_id`),
  KEY `loan_id` (`loan_id`),
  CONSTRAINT `app_borrower_loan_payments_ibfk_1` FOREIGN KEY (`loan_id`) REFERENCES `app_loan_details` (`loan_id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
-- Data for table `app_borrower_loan_payments`
INSERT INTO `app_borrower_loan_payments` (`payment_id`, `loan_id`, `payment_amount`, `penalty_amount`, `referral_share_amount`, `final_amount`, `received_date`, `interest_paid`, `principal_paid`, `payment_status`, `mode_of_payment`, `comments`, `interest_date`, `added_on`, `status`, `deleted`) VALUES ('1', '1', '10000.00', '1000.00', '200.00', '10800.00', '2025-01-20', '10000.00', '0.00', 'Completed', 'UPI', '', '2025-01-10', '2025-01-20 21:50:46', '1', '0');
INSERT INTO `app_borrower_loan_payments` (`payment_id`, `loan_id`, `payment_amount`, `penalty_amount`, `referral_share_amount`, `final_amount`, `received_date`, `interest_paid`, `principal_paid`, `payment_status`, `mode_of_payment`, `comments`, `interest_date`, `added_on`, `status`, `deleted`) VALUES ('2', '1', '20000.00', '0.00', '0.00', '20000.00', '2025-01-20', '0.00', '20000.00', 'Completed', 'Bank Transfer', '', '2025-06-01', '2025-01-20 21:51:51', '1', '0');
INSERT INTO `app_borrower_loan_payments` (`payment_id`, `loan_id`, `payment_amount`, `penalty_amount`, `referral_share_amount`, `final_amount`, `received_date`, `interest_paid`, `principal_paid`, `payment_status`, `mode_of_payment`, `comments`, `interest_date`, `added_on`, `status`, `deleted`) VALUES ('3', '1', '8000.00', '0.00', '160.00', '7840.00', '2025-01-20', '8000.00', '20000.00', 'Completed', 'Bank Transfer', '', '2025-02-10', '2025-01-20 21:52:39', '1', '0');
INSERT INTO `app_borrower_loan_payments` (`payment_id`, `loan_id`, `payment_amount`, `penalty_amount`, `referral_share_amount`, `final_amount`, `received_date`, `interest_paid`, `principal_paid`, `payment_status`, `mode_of_payment`, `comments`, `interest_date`, `added_on`, `status`, `deleted`) VALUES ('4', '2', '5000.00', '500.00', '0.00', '5500.00', '2025-01-20', '5000.00', '0.00', 'Completed', 'UPI', '', '2025-01-10', '2025-01-20 23:04:56', '1', '0');
INSERT INTO `app_borrower_loan_payments` (`payment_id`, `loan_id`, `payment_amount`, `penalty_amount`, `referral_share_amount`, `final_amount`, `received_date`, `interest_paid`, `principal_paid`, `payment_status`, `mode_of_payment`, `comments`, `interest_date`, `added_on`, `status`, `deleted`) VALUES ('5', '2', '5000.00', '0.00', '0.00', '5000.00', '2025-01-20', '5000.00', '0.00', 'Completed', 'UPI', '', '2025-02-10', '2025-01-20 23:05:06', '1', '0');
INSERT INTO `app_borrower_loan_payments` (`payment_id`, `loan_id`, `payment_amount`, `penalty_amount`, `referral_share_amount`, `final_amount`, `received_date`, `interest_paid`, `principal_paid`, `payment_status`, `mode_of_payment`, `comments`, `interest_date`, `added_on`, `status`, `deleted`) VALUES ('6', '2', '5000.00', '0.00', '0.00', '5000.00', '2025-01-20', '5000.00', '0.00', 'Completed', 'Bank Transfer', '', '2025-03-10', '2025-01-20 23:05:22', '1', '0');
INSERT INTO `app_borrower_loan_payments` (`payment_id`, `loan_id`, `payment_amount`, `penalty_amount`, `referral_share_amount`, `final_amount`, `received_date`, `interest_paid`, `principal_paid`, `payment_status`, `mode_of_payment`, `comments`, `interest_date`, `added_on`, `status`, `deleted`) VALUES ('7', '2', '40000.00', '0.00', '0.00', '40000.00', '2025-01-20', '0.00', '40000.00', 'Completed', 'Cash', '', '2025-12-01', '2025-01-20 23:06:35', '1', '0');
INSERT INTO `app_borrower_loan_payments` (`payment_id`, `loan_id`, `payment_amount`, `penalty_amount`, `referral_share_amount`, `final_amount`, `received_date`, `interest_paid`, `principal_paid`, `payment_status`, `mode_of_payment`, `comments`, `interest_date`, `added_on`, `status`, `deleted`) VALUES ('8', '2', '1000.00', '0.00', '0.00', '1000.00', '2025-01-20', '1000.00', '40000.00', 'Completed', 'UPI', '', '2025-04-10', '2025-01-20 23:08:25', '1', '0');
INSERT INTO `app_borrower_loan_payments` (`payment_id`, `loan_id`, `payment_amount`, `penalty_amount`, `referral_share_amount`, `final_amount`, `received_date`, `interest_paid`, `principal_paid`, `payment_status`, `mode_of_payment`, `comments`, `interest_date`, `added_on`, `status`, `deleted`) VALUES ('9', '2', '1000.00', '0.00', '0.00', '1000.00', '2025-01-20', '1000.00', '0.00', 'Completed', 'Bank Transfer', '', '2025-05-10', '2025-01-20 23:25:40', '1', '0');
INSERT INTO `app_borrower_loan_payments` (`payment_id`, `loan_id`, `payment_amount`, `penalty_amount`, `referral_share_amount`, `final_amount`, `received_date`, `interest_paid`, `principal_paid`, `payment_status`, `mode_of_payment`, `comments`, `interest_date`, `added_on`, `status`, `deleted`) VALUES ('10', '2', '10000.00', '0.00', '0.00', '10000.00', '2025-01-20', '0.00', '10000.00', 'Completed', 'Cheque', '', '2025-12-01', '2025-01-20 23:25:59', '1', '0');
INSERT INTO `app_borrower_loan_payments` (`payment_id`, `loan_id`, `payment_amount`, `penalty_amount`, `referral_share_amount`, `final_amount`, `received_date`, `interest_paid`, `principal_paid`, `payment_status`, `mode_of_payment`, `comments`, `interest_date`, `added_on`, `status`, `deleted`) VALUES ('11', '4', '1000.00', '100.00', '0.00', '1100.00', '2025-01-21', '1000.00', '0.00', 'Completed', 'Cash', '', '2025-01-10', '2025-01-21 20:22:00', '1', '0');
INSERT INTO `app_borrower_loan_payments` (`payment_id`, `loan_id`, `payment_amount`, `penalty_amount`, `referral_share_amount`, `final_amount`, `received_date`, `interest_paid`, `principal_paid`, `payment_status`, `mode_of_payment`, `comments`, `interest_date`, `added_on`, `status`, `deleted`) VALUES ('12', '4', '5000.00', '0.00', '0.00', '5000.00', '2025-01-21', '0.00', '5000.00', 'Completed', 'Bank Transfer', '', '2025-03-01', '2025-01-21 20:22:50', '1', '0');
INSERT INTO `app_borrower_loan_payments` (`payment_id`, `loan_id`, `payment_amount`, `penalty_amount`, `referral_share_amount`, `final_amount`, `received_date`, `interest_paid`, `principal_paid`, `payment_status`, `mode_of_payment`, `comments`, `interest_date`, `added_on`, `status`, `deleted`) VALUES ('13', '4', '500.00', '0.00', '0.00', '500.00', '2025-01-21', '500.00', '5000.00', 'Completed', 'UPI', '', '2025-02-10', '2025-01-21 20:23:40', '1', '0');
INSERT INTO `app_borrower_loan_payments` (`payment_id`, `loan_id`, `payment_amount`, `penalty_amount`, `referral_share_amount`, `final_amount`, `received_date`, `interest_paid`, `principal_paid`, `payment_status`, `mode_of_payment`, `comments`, `interest_date`, `added_on`, `status`, `deleted`) VALUES ('14', '6', '1000.00', '100.00', '20.00', '1080.00', '2025-01-21', '1000.00', '0.00', 'Completed', 'UPI', '', '2025-01-10', '2025-01-21 20:32:33', '1', '0');
INSERT INTO `app_borrower_loan_payments` (`payment_id`, `loan_id`, `payment_amount`, `penalty_amount`, `referral_share_amount`, `final_amount`, `received_date`, `interest_paid`, `principal_paid`, `payment_status`, `mode_of_payment`, `comments`, `interest_date`, `added_on`, `status`, `deleted`) VALUES ('15', '6', '5000.00', '0.00', '0.00', '5000.00', '2025-01-21', '0.00', '5000.00', 'Completed', 'Bank Transfer', '', '2025-12-01', '2025-01-21 20:34:14', '1', '0');
INSERT INTO `app_borrower_loan_payments` (`payment_id`, `loan_id`, `payment_amount`, `penalty_amount`, `referral_share_amount`, `final_amount`, `received_date`, `interest_paid`, `principal_paid`, `payment_status`, `mode_of_payment`, `comments`, `interest_date`, `added_on`, `status`, `deleted`) VALUES ('16', '6', '5000.00', '0.00', '0.00', '5000.00', '2025-01-21', '0.00', '5000.00', 'Completed', 'UPI', '', '2025-12-01', '2025-01-21 20:41:21', '1', '0');
INSERT INTO `app_borrower_loan_payments` (`payment_id`, `loan_id`, `payment_amount`, `penalty_amount`, `referral_share_amount`, `final_amount`, `received_date`, `interest_paid`, `principal_paid`, `payment_status`, `mode_of_payment`, `comments`, `interest_date`, `added_on`, `status`, `deleted`) VALUES ('17', '7', '500.00', '0.00', '10.00', '490.00', '2025-01-21', '500.00', '0.00', 'Completed', 'UPI', '', '2025-02-10', '2025-01-21 20:43:06', '1', '0');
INSERT INTO `app_borrower_loan_payments` (`payment_id`, `loan_id`, `payment_amount`, `penalty_amount`, `referral_share_amount`, `final_amount`, `received_date`, `interest_paid`, `principal_paid`, `payment_status`, `mode_of_payment`, `comments`, `interest_date`, `added_on`, `status`, `deleted`) VALUES ('18', '7', '500.00', '0.00', '10.00', '490.00', '2025-01-21', '500.00', '0.00', 'Completed', 'UPI', '', '2025-03-10', '2025-01-21 20:44:54', '1', '0');
INSERT INTO `app_borrower_loan_payments` (`payment_id`, `loan_id`, `payment_amount`, `penalty_amount`, `referral_share_amount`, `final_amount`, `received_date`, `interest_paid`, `principal_paid`, `payment_status`, `mode_of_payment`, `comments`, `interest_date`, `added_on`, `status`, `deleted`) VALUES ('19', '7', '2000.00', '0.00', '0.00', '2000.00', '2025-01-21', '0.00', '2000.00', 'Completed', 'UPI', '', '2025-04-21', '2025-01-21 20:45:15', '1', '0');
INSERT INTO `app_borrower_loan_payments` (`payment_id`, `loan_id`, `payment_amount`, `penalty_amount`, `referral_share_amount`, `final_amount`, `received_date`, `interest_paid`, `principal_paid`, `payment_status`, `mode_of_payment`, `comments`, `interest_date`, `added_on`, `status`, `deleted`) VALUES ('20', '1', '8000.00', '0.00', '0.00', '8000.00', '2025-01-25', '8000.00', '0.00', 'Completed', 'Cash', '', '2025-03-10', '2025-01-25 09:24:49', '1', '0');
INSERT INTO `app_borrower_loan_payments` (`payment_id`, `loan_id`, `payment_amount`, `penalty_amount`, `referral_share_amount`, `final_amount`, `received_date`, `interest_paid`, `principal_paid`, `payment_status`, `mode_of_payment`, `comments`, `interest_date`, `added_on`, `status`, `deleted`) VALUES ('21', '9', '800.00', '80.00', '0.00', '880.00', '2025-01-25', '800.00', '0.00', 'Completed', 'UPI', '', '2024-12-10', '2025-01-25 09:29:31', '1', '0');
INSERT INTO `app_borrower_loan_payments` (`payment_id`, `loan_id`, `payment_amount`, `penalty_amount`, `referral_share_amount`, `final_amount`, `received_date`, `interest_paid`, `principal_paid`, `payment_status`, `mode_of_payment`, `comments`, `interest_date`, `added_on`, `status`, `deleted`) VALUES ('22', '9', '800.00', '80.00', '0.00', '880.00', '2025-02-25', '800.00', '0.00', 'Completed', 'UPI', '', '2025-01-10', '2025-01-25 10:01:40', '1', '0');
INSERT INTO `app_borrower_loan_payments` (`payment_id`, `loan_id`, `payment_amount`, `penalty_amount`, `referral_share_amount`, `final_amount`, `received_date`, `interest_paid`, `principal_paid`, `payment_status`, `mode_of_payment`, `comments`, `interest_date`, `added_on`, `status`, `deleted`) VALUES ('23', '9', '5000.00', '0.00', '0.00', '5000.00', '2025-02-25', '0.00', '5000.00', 'Completed', 'Bank Transfer', '', '2025-11-01', '2025-01-25 10:02:31', '1', '0');
INSERT INTO `app_borrower_loan_payments` (`payment_id`, `loan_id`, `payment_amount`, `penalty_amount`, `referral_share_amount`, `final_amount`, `received_date`, `interest_paid`, `principal_paid`, `payment_status`, `mode_of_payment`, `comments`, `interest_date`, `added_on`, `status`, `deleted`) VALUES ('24', '1', '8000.00', '0.00', '160.00', '7840.00', '2025-01-25', '8000.00', '0.00', 'Completed', 'UPI', 'test', '2025-04-10', '2025-01-25 18:28:31', '1', '0');
INSERT INTO `app_borrower_loan_payments` (`payment_id`, `loan_id`, `payment_amount`, `penalty_amount`, `referral_share_amount`, `final_amount`, `received_date`, `interest_paid`, `principal_paid`, `payment_status`, `mode_of_payment`, `comments`, `interest_date`, `added_on`, `status`, `deleted`) VALUES ('25', '1', '4000.00', '0.00', '0.00', '4000.00', '2025-01-25', '0.00', '4000.00', 'Completed', 'Cash', 'test', '2025-06-01', '2025-01-25 18:29:08', '1', '0');
INSERT INTO `app_borrower_loan_payments` (`payment_id`, `loan_id`, `payment_amount`, `penalty_amount`, `referral_share_amount`, `final_amount`, `received_date`, `interest_paid`, `principal_paid`, `payment_status`, `mode_of_payment`, `comments`, `interest_date`, `added_on`, `status`, `deleted`) VALUES ('26', '1', '76000.00', '0.00', '0.00', '76000.00', '2025-01-25', '0.00', '76000.00', 'Completed', 'Bank Transfer', 'Done', '2025-06-01', '2025-01-25 18:29:30', '1', '0');
INSERT INTO `app_borrower_loan_payments` (`payment_id`, `loan_id`, `payment_amount`, `penalty_amount`, `referral_share_amount`, `final_amount`, `received_date`, `interest_paid`, `principal_paid`, `payment_status`, `mode_of_payment`, `comments`, `interest_date`, `added_on`, `status`, `deleted`) VALUES ('27', '7', '300.00', '0.00', '6.00', '294.00', '2025-01-25', '300.00', '2000.00', 'Completed', 'UPI', '300 done', '2025-04-10', '2025-01-25 20:10:15', '1', '0');
INSERT INTO `app_borrower_loan_payments` (`payment_id`, `loan_id`, `payment_amount`, `penalty_amount`, `referral_share_amount`, `final_amount`, `received_date`, `interest_paid`, `principal_paid`, `payment_status`, `mode_of_payment`, `comments`, `interest_date`, `added_on`, `status`, `deleted`) VALUES ('28', '10', '1500.00', '0.00', '0.00', '1500.00', '2024-12-27', '1500.00', '0.00', 'Completed', 'UPI', 'Done', '2025-01-10', '2025-01-25 20:35:09', '1', '0');
INSERT INTO `app_borrower_loan_payments` (`payment_id`, `loan_id`, `payment_amount`, `penalty_amount`, `referral_share_amount`, `final_amount`, `received_date`, `interest_paid`, `principal_paid`, `payment_status`, `mode_of_payment`, `comments`, `interest_date`, `added_on`, `status`, `deleted`) VALUES ('29', '10', '5000.00', '0.00', '0.00', '5000.00', '2025-01-25', '0.00', '5000.00', 'Completed', 'UPI', '5000 Done', '2025-12-16', '2025-01-25 20:37:02', '1', '0');
INSERT INTO `app_borrower_loan_payments` (`payment_id`, `loan_id`, `payment_amount`, `penalty_amount`, `referral_share_amount`, `final_amount`, `received_date`, `interest_paid`, `principal_paid`, `payment_status`, `mode_of_payment`, `comments`, `interest_date`, `added_on`, `status`, `deleted`) VALUES ('30', '10', '1000.00', '0.00', '0.00', '1000.00', '2025-01-25', '1000.00', '5000.00', 'Completed', 'Cash', '1000 Done', '2025-02-10', '2025-01-25 20:38:57', '1', '0');
INSERT INTO `app_borrower_loan_payments` (`payment_id`, `loan_id`, `payment_amount`, `penalty_amount`, `referral_share_amount`, `final_amount`, `received_date`, `interest_paid`, `principal_paid`, `payment_status`, `mode_of_payment`, `comments`, `interest_date`, `added_on`, `status`, `deleted`) VALUES ('31', '10', '10000.00', '0.00', '0.00', '10000.00', '2025-01-25', '0.00', '10000.00', 'Completed', 'Bank Transfer', 'Done', '2025-12-16', '2025-01-25 20:41:03', '1', '0');


-- Structure of table `app_borrower_master`
CREATE TABLE `app_borrower_master` (
  `id` int NOT NULL AUTO_INCREMENT,
  `unique_borrower_id` varchar(10) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone_no` varchar(255) NOT NULL,
  `gender` varchar(255) DEFAULT NULL,
  `address` text,
  `email` varchar(100) DEFAULT NULL,
  `added_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint DEFAULT '1',
  `deleted` tinyint DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `phone_no` (`phone_no`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
-- Data for table `app_borrower_master`
INSERT INTO `app_borrower_master` (`id`, `unique_borrower_id`, `name`, `phone_no`, `gender`, `address`, `email`, `added_on`, `status`, `deleted`) VALUES ('1', 'BU-01', 'suraj jaiswal', '7387997294', 'Male', 'pune', 'jaiswaljesus384@gmail.com', '2025-01-20 21:47:24', '1', '0');
INSERT INTO `app_borrower_master` (`id`, `unique_borrower_id`, `name`, `phone_no`, `gender`, `address`, `email`, `added_on`, `status`, `deleted`) VALUES ('2', 'BU-02', 'Akash jaiswal', '7774446662', 'Male', 'pune', 'Akash@gmail.com', '2025-01-20 21:47:48', '1', '0');
INSERT INTO `app_borrower_master` (`id`, `unique_borrower_id`, `name`, `phone_no`, `gender`, `address`, `email`, `added_on`, `status`, `deleted`) VALUES ('5', 'BU-03', 'Ketaki Deshpande', '7714446662', 'Male', 'pune', 'Ketaki@gmail.com', '2025-01-21 20:20:07', '1', '0');
INSERT INTO `app_borrower_master` (`id`, `unique_borrower_id`, `name`, `phone_no`, `gender`, `address`, `email`, `added_on`, `status`, `deleted`) VALUES ('6', 'BU-04', 'Shantanu Gondane', '7774446612', 'Male', 'pune', 'Shantanu.Gondane@gmail.com', '2025-01-21 20:26:40', '1', '0');
INSERT INTO `app_borrower_master` (`id`, `unique_borrower_id`, `name`, `phone_no`, `gender`, `address`, `email`, `added_on`, `status`, `deleted`) VALUES ('8', 'BU-05', 'Niraj ', '7387937294', 'Male', 'pune', 'Niraj@gmail.com', '2025-01-21 22:09:37', '1', '0');
INSERT INTO `app_borrower_master` (`id`, `unique_borrower_id`, `name`, `phone_no`, `gender`, `address`, `email`, `added_on`, `status`, `deleted`) VALUES ('9', 'SM-06', 'Maheshvar', '7774446661', 'Male', 'narhe', 'MaheshJadhav@gmail.com', '2025-01-25 18:27:14', '1', '0');
INSERT INTO `app_borrower_master` (`id`, `unique_borrower_id`, `name`, `phone_no`, `gender`, `address`, `email`, `added_on`, `status`, `deleted`) VALUES ('10', 'SM-07', 'Namrata Kuldeep Shrivastav', '8850685620', 'Female', 'pune', 'test@123', '2025-01-25 20:30:22', '1', '0');


-- Structure of table `app_borrower_ref_map`
CREATE TABLE `app_borrower_ref_map` (
  `id` int NOT NULL AUTO_INCREMENT,
  `reference_Id` int DEFAULT NULL,
  `borrower_id` int DEFAULT NULL,
  `relationship` varchar(50) DEFAULT NULL,
  `added_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint DEFAULT '1',
  `deleted` tinyint DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `reference_Id` (`reference_Id`),
  KEY `borrower_id` (`borrower_id`),
  CONSTRAINT `app_borrower_ref_map_ibfk_1` FOREIGN KEY (`reference_Id`) REFERENCES `app_referral_user` (`reference_Id`),
  CONSTRAINT `app_borrower_ref_map_ibfk_2` FOREIGN KEY (`borrower_id`) REFERENCES `app_borrower_master` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
-- Data for table `app_borrower_ref_map`
INSERT INTO `app_borrower_ref_map` (`id`, `reference_Id`, `borrower_id`, `relationship`, `added_on`, `status`, `deleted`) VALUES ('1', '1', '1', '', '2025-01-20 16:19:28', '1', '0');
INSERT INTO `app_borrower_ref_map` (`id`, `reference_Id`, `borrower_id`, `relationship`, `added_on`, `status`, `deleted`) VALUES ('2', '2', '6', '', '2025-01-21 14:59:06', '1', '0');


-- Structure of table `app_loan_details`
CREATE TABLE `app_loan_details` (
  `loan_id` int NOT NULL AUTO_INCREMENT,
  `borrower_id` int DEFAULT NULL,
  `principal_amount` decimal(10,2) NOT NULL,
  `interest_rate` decimal(5,2) NOT NULL,
  `loan_period` int NOT NULL,
  `disbursed_date` date NOT NULL,
  `top_up_payment` decimal(10,2) DEFAULT '0.00',
  `EMI_amount` decimal(10,2) DEFAULT NULL,
  `closure_amount` decimal(10,2) DEFAULT NULL,
  `closure_date` date DEFAULT NULL,
  `ending_principal` decimal(10,2) DEFAULT '0.00',
  `repaid_principal` decimal(10,2) DEFAULT '0.00',
  `part_payment_status` tinyint DEFAULT '0',
  `closer_payment_status` tinyint DEFAULT '0',
  `loan_status` varchar(20) DEFAULT 'active',
  `added_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint DEFAULT '1',
  `deleted` tinyint DEFAULT '0',
  PRIMARY KEY (`loan_id`),
  KEY `borrower_id` (`borrower_id`),
  CONSTRAINT `app_loan_details_ibfk_1` FOREIGN KEY (`borrower_id`) REFERENCES `app_borrower_master` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
-- Data for table `app_loan_details`
INSERT INTO `app_loan_details` (`loan_id`, `borrower_id`, `principal_amount`, `interest_rate`, `loan_period`, `disbursed_date`, `top_up_payment`, `EMI_amount`, `closure_amount`, `closure_date`, `ending_principal`, `repaid_principal`, `part_payment_status`, `closer_payment_status`, `loan_status`, `added_on`, `status`, `deleted`) VALUES ('1', '1', '100000.00', '10.00', '6', '2024-12-01', '0.00', '10000.00', '160000.00', '2025-06-01', '0.00', '100000.00', '1', '1', 'closed', '2025-01-20 21:48:13', '1', '0');
INSERT INTO `app_loan_details` (`loan_id`, `borrower_id`, `principal_amount`, `interest_rate`, `loan_period`, `disbursed_date`, `top_up_payment`, `EMI_amount`, `closure_amount`, `closure_date`, `ending_principal`, `repaid_principal`, `part_payment_status`, `closer_payment_status`, `loan_status`, `added_on`, `status`, `deleted`) VALUES ('2', '2', '50000.00', '10.00', '12', '2024-12-01', '0.00', '5000.00', '110000.00', '2025-12-01', '0.00', '50000.00', '1', '1', 'closed', '2025-01-20 21:48:37', '1', '0');
INSERT INTO `app_loan_details` (`loan_id`, `borrower_id`, `principal_amount`, `interest_rate`, `loan_period`, `disbursed_date`, `top_up_payment`, `EMI_amount`, `closure_amount`, `closure_date`, `ending_principal`, `repaid_principal`, `part_payment_status`, `closer_payment_status`, `loan_status`, `added_on`, `status`, `deleted`) VALUES ('3', '2', '10000.00', '8.00', '2', '2025-01-20', '0.00', '800.00', '11600.00', '2025-03-20', '10000.00', '0.00', '0', '0', 'active', '2025-01-20 23:09:54', '1', '0');
INSERT INTO `app_loan_details` (`loan_id`, `borrower_id`, `principal_amount`, `interest_rate`, `loan_period`, `disbursed_date`, `top_up_payment`, `EMI_amount`, `closure_amount`, `closure_date`, `ending_principal`, `repaid_principal`, `part_payment_status`, `closer_payment_status`, `loan_status`, `added_on`, `status`, `deleted`) VALUES ('4', '5', '10000.00', '10.00', '3', '2024-12-01', '0.00', '1000.00', '13000.00', '2025-03-01', '5000.00', '5000.00', '1', '0', 'active', '2025-01-21 20:20:58', '1', '0');
INSERT INTO `app_loan_details` (`loan_id`, `borrower_id`, `principal_amount`, `interest_rate`, `loan_period`, `disbursed_date`, `top_up_payment`, `EMI_amount`, `closure_amount`, `closure_date`, `ending_principal`, `repaid_principal`, `part_payment_status`, `closer_payment_status`, `loan_status`, `added_on`, `status`, `deleted`) VALUES ('5', '5', '2000.00', '10.00', '2', '2025-01-21', '0.00', '200.00', '2400.00', '2025-03-21', '2000.00', '0.00', '0', '0', 'active', '2025-01-21 20:24:26', '1', '0');
INSERT INTO `app_loan_details` (`loan_id`, `borrower_id`, `principal_amount`, `interest_rate`, `loan_period`, `disbursed_date`, `top_up_payment`, `EMI_amount`, `closure_amount`, `closure_date`, `ending_principal`, `repaid_principal`, `part_payment_status`, `closer_payment_status`, `loan_status`, `added_on`, `status`, `deleted`) VALUES ('6', '6', '10000.00', '10.00', '12', '2024-12-01', '0.00', '1000.00', '22000.00', '2025-12-01', '0.00', '10000.00', '1', '1', 'closed', '2025-01-21 20:27:37', '1', '0');
INSERT INTO `app_loan_details` (`loan_id`, `borrower_id`, `principal_amount`, `interest_rate`, `loan_period`, `disbursed_date`, `top_up_payment`, `EMI_amount`, `closure_amount`, `closure_date`, `ending_principal`, `repaid_principal`, `part_payment_status`, `closer_payment_status`, `loan_status`, `added_on`, `status`, `deleted`) VALUES ('7', '6', '5000.00', '10.00', '3', '2025-01-21', '0.00', '500.00', '6500.00', '2025-04-21', '3000.00', '2000.00', '1', '0', 'active', '2025-01-21 20:42:13', '1', '0');
INSERT INTO `app_loan_details` (`loan_id`, `borrower_id`, `principal_amount`, `interest_rate`, `loan_period`, `disbursed_date`, `top_up_payment`, `EMI_amount`, `closure_amount`, `closure_date`, `ending_principal`, `repaid_principal`, `part_payment_status`, `closer_payment_status`, `loan_status`, `added_on`, `status`, `deleted`) VALUES ('8', '6', '5000.00', '10.00', '12', '2025-01-21', '0.00', '500.00', '11000.00', '2026-01-21', '5000.00', '0.00', '0', '0', 'active', '2025-01-21 20:43:19', '1', '0');
INSERT INTO `app_loan_details` (`loan_id`, `borrower_id`, `principal_amount`, `interest_rate`, `loan_period`, `disbursed_date`, `top_up_payment`, `EMI_amount`, `closure_amount`, `closure_date`, `ending_principal`, `repaid_principal`, `part_payment_status`, `closer_payment_status`, `loan_status`, `added_on`, `status`, `deleted`) VALUES ('9', '8', '10000.00', '8.00', '12', '2024-11-01', '0.00', '800.00', '19600.00', '2025-11-01', '5000.00', '5000.00', '1', '0', 'active', '2025-01-21 22:10:11', '1', '0');
INSERT INTO `app_loan_details` (`loan_id`, `borrower_id`, `principal_amount`, `interest_rate`, `loan_period`, `disbursed_date`, `top_up_payment`, `EMI_amount`, `closure_amount`, `closure_date`, `ending_principal`, `repaid_principal`, `part_payment_status`, `closer_payment_status`, `loan_status`, `added_on`, `status`, `deleted`) VALUES ('10', '10', '15000.00', '10.00', '12', '2024-12-16', '0.00', '1500.00', '33000.00', '2025-12-16', '0.00', '15000.00', '1', '1', 'closed', '2025-01-25 20:31:28', '1', '0');


-- Structure of table `app_loan_emi_schedule`
CREATE TABLE `app_loan_emi_schedule` (
  `schedule_id` int NOT NULL AUTO_INCREMENT,
  `loan_id` int DEFAULT NULL,
  `month_no` int NOT NULL,
  `beginning_principal` decimal(10,2) NOT NULL,
  `interest_amount` decimal(10,2) NOT NULL,
  `emi_amount` decimal(10,2) NOT NULL,
  `principal_repaid` decimal(10,2) NOT NULL,
  `ending_principal` decimal(10,2) NOT NULL,
  `payment_due_date` date NOT NULL,
  `penalty_amount` decimal(10,2) DEFAULT '0.00',
  `payment_status` varchar(20) DEFAULT 'pending',
  `added_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint DEFAULT '1',
  `deleted` tinyint DEFAULT '0',
  PRIMARY KEY (`schedule_id`),
  KEY `loan_id` (`loan_id`),
  CONSTRAINT `app_loan_emi_schedule_ibfk_1` FOREIGN KEY (`loan_id`) REFERENCES `app_loan_details` (`loan_id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
-- Data for table `app_loan_emi_schedule`
INSERT INTO `app_loan_emi_schedule` (`schedule_id`, `loan_id`, `month_no`, `beginning_principal`, `interest_amount`, `emi_amount`, `principal_repaid`, `ending_principal`, `payment_due_date`, `penalty_amount`, `payment_status`, `added_on`, `status`, `deleted`) VALUES ('1', '1', '1', '100000.00', '10000.00', '10000.00', '0.00', '100000.00', '2025-01-10', '0.00', 'Completed', '2025-01-20 21:48:13', '0', '0');
INSERT INTO `app_loan_emi_schedule` (`schedule_id`, `loan_id`, `month_no`, `beginning_principal`, `interest_amount`, `emi_amount`, `principal_repaid`, `ending_principal`, `payment_due_date`, `penalty_amount`, `payment_status`, `added_on`, `status`, `deleted`) VALUES ('2', '2', '1', '50000.00', '5000.00', '5000.00', '0.00', '50000.00', '2025-01-10', '0.00', 'Completed', '2025-01-20 21:48:37', '0', '0');
INSERT INTO `app_loan_emi_schedule` (`schedule_id`, `loan_id`, `month_no`, `beginning_principal`, `interest_amount`, `emi_amount`, `principal_repaid`, `ending_principal`, `payment_due_date`, `penalty_amount`, `payment_status`, `added_on`, `status`, `deleted`) VALUES ('3', '1', '2', '100000.00', '8000.00', '8000.00', '20000.00', '80000.00', '2025-02-10', '0.00', 'Completed', '2025-01-20 21:50:46', '0', '0');
INSERT INTO `app_loan_emi_schedule` (`schedule_id`, `loan_id`, `month_no`, `beginning_principal`, `interest_amount`, `emi_amount`, `principal_repaid`, `ending_principal`, `payment_due_date`, `penalty_amount`, `payment_status`, `added_on`, `status`, `deleted`) VALUES ('4', '1', '3', '80000.00', '8000.00', '8000.00', '0.00', '80000.00', '2025-03-10', '0.00', 'Completed', '2025-01-20 21:52:39', '0', '0');
INSERT INTO `app_loan_emi_schedule` (`schedule_id`, `loan_id`, `month_no`, `beginning_principal`, `interest_amount`, `emi_amount`, `principal_repaid`, `ending_principal`, `payment_due_date`, `penalty_amount`, `payment_status`, `added_on`, `status`, `deleted`) VALUES ('5', '2', '2', '50000.00', '5000.00', '5000.00', '0.00', '50000.00', '2025-02-10', '0.00', 'Completed', '2025-01-20 23:04:56', '0', '0');
INSERT INTO `app_loan_emi_schedule` (`schedule_id`, `loan_id`, `month_no`, `beginning_principal`, `interest_amount`, `emi_amount`, `principal_repaid`, `ending_principal`, `payment_due_date`, `penalty_amount`, `payment_status`, `added_on`, `status`, `deleted`) VALUES ('6', '2', '3', '50000.00', '5000.00', '5000.00', '0.00', '50000.00', '2025-03-10', '0.00', 'Completed', '2025-01-20 23:05:06', '0', '0');
INSERT INTO `app_loan_emi_schedule` (`schedule_id`, `loan_id`, `month_no`, `beginning_principal`, `interest_amount`, `emi_amount`, `principal_repaid`, `ending_principal`, `payment_due_date`, `penalty_amount`, `payment_status`, `added_on`, `status`, `deleted`) VALUES ('7', '2', '4', '50000.00', '1000.00', '1000.00', '40000.00', '10000.00', '2025-04-10', '0.00', 'Completed', '2025-01-20 23:05:22', '0', '0');
INSERT INTO `app_loan_emi_schedule` (`schedule_id`, `loan_id`, `month_no`, `beginning_principal`, `interest_amount`, `emi_amount`, `principal_repaid`, `ending_principal`, `payment_due_date`, `penalty_amount`, `payment_status`, `added_on`, `status`, `deleted`) VALUES ('8', '2', '5', '10000.00', '1000.00', '1000.00', '0.00', '10000.00', '2025-05-10', '0.00', 'Completed', '2025-01-20 23:08:25', '0', '0');
INSERT INTO `app_loan_emi_schedule` (`schedule_id`, `loan_id`, `month_no`, `beginning_principal`, `interest_amount`, `emi_amount`, `principal_repaid`, `ending_principal`, `payment_due_date`, `penalty_amount`, `payment_status`, `added_on`, `status`, `deleted`) VALUES ('9', '3', '1', '10000.00', '800.00', '800.00', '0.00', '10000.00', '2025-02-10', '0.00', 'pending', '2025-01-20 23:09:54', '1', '0');
INSERT INTO `app_loan_emi_schedule` (`schedule_id`, `loan_id`, `month_no`, `beginning_principal`, `interest_amount`, `emi_amount`, `principal_repaid`, `ending_principal`, `payment_due_date`, `penalty_amount`, `payment_status`, `added_on`, `status`, `deleted`) VALUES ('10', '2', '6', '10000.00', '1000.00', '1000.00', '0.00', '10000.00', '2025-06-10', '0.00', 'pending', '2025-01-20 23:25:40', '0', '1');
INSERT INTO `app_loan_emi_schedule` (`schedule_id`, `loan_id`, `month_no`, `beginning_principal`, `interest_amount`, `emi_amount`, `principal_repaid`, `ending_principal`, `payment_due_date`, `penalty_amount`, `payment_status`, `added_on`, `status`, `deleted`) VALUES ('11', '4', '1', '10000.00', '1000.00', '1000.00', '0.00', '10000.00', '2025-01-10', '0.00', 'Completed', '2025-01-21 20:20:58', '0', '0');
INSERT INTO `app_loan_emi_schedule` (`schedule_id`, `loan_id`, `month_no`, `beginning_principal`, `interest_amount`, `emi_amount`, `principal_repaid`, `ending_principal`, `payment_due_date`, `penalty_amount`, `payment_status`, `added_on`, `status`, `deleted`) VALUES ('12', '4', '2', '10000.00', '500.00', '500.00', '5000.00', '5000.00', '2025-02-10', '0.00', 'Completed', '2025-01-21 20:22:00', '0', '0');
INSERT INTO `app_loan_emi_schedule` (`schedule_id`, `loan_id`, `month_no`, `beginning_principal`, `interest_amount`, `emi_amount`, `principal_repaid`, `ending_principal`, `payment_due_date`, `penalty_amount`, `payment_status`, `added_on`, `status`, `deleted`) VALUES ('13', '4', '3', '5000.00', '500.00', '500.00', '0.00', '5000.00', '2025-03-10', '0.00', 'pending', '2025-01-21 20:23:40', '1', '0');
INSERT INTO `app_loan_emi_schedule` (`schedule_id`, `loan_id`, `month_no`, `beginning_principal`, `interest_amount`, `emi_amount`, `principal_repaid`, `ending_principal`, `payment_due_date`, `penalty_amount`, `payment_status`, `added_on`, `status`, `deleted`) VALUES ('14', '5', '1', '2000.00', '200.00', '200.00', '0.00', '2000.00', '2025-02-10', '0.00', 'pending', '2025-01-21 20:24:26', '1', '0');
INSERT INTO `app_loan_emi_schedule` (`schedule_id`, `loan_id`, `month_no`, `beginning_principal`, `interest_amount`, `emi_amount`, `principal_repaid`, `ending_principal`, `payment_due_date`, `penalty_amount`, `payment_status`, `added_on`, `status`, `deleted`) VALUES ('15', '6', '1', '10000.00', '1000.00', '1000.00', '0.00', '10000.00', '2025-01-10', '0.00', 'Completed', '2025-01-21 20:27:37', '0', '0');
INSERT INTO `app_loan_emi_schedule` (`schedule_id`, `loan_id`, `month_no`, `beginning_principal`, `interest_amount`, `emi_amount`, `principal_repaid`, `ending_principal`, `payment_due_date`, `penalty_amount`, `payment_status`, `added_on`, `status`, `deleted`) VALUES ('16', '6', '2', '10000.00', '500.00', '500.00', '5000.00', '5000.00', '2025-02-10', '0.00', 'pending', '2025-01-21 20:32:33', '0', '1');
INSERT INTO `app_loan_emi_schedule` (`schedule_id`, `loan_id`, `month_no`, `beginning_principal`, `interest_amount`, `emi_amount`, `principal_repaid`, `ending_principal`, `payment_due_date`, `penalty_amount`, `payment_status`, `added_on`, `status`, `deleted`) VALUES ('17', '7', '1', '5000.00', '500.00', '500.00', '0.00', '5000.00', '2025-02-10', '0.00', 'Completed', '2025-01-21 20:42:13', '0', '0');
INSERT INTO `app_loan_emi_schedule` (`schedule_id`, `loan_id`, `month_no`, `beginning_principal`, `interest_amount`, `emi_amount`, `principal_repaid`, `ending_principal`, `payment_due_date`, `penalty_amount`, `payment_status`, `added_on`, `status`, `deleted`) VALUES ('18', '7', '2', '5000.00', '500.00', '500.00', '0.00', '5000.00', '2025-03-10', '0.00', 'Completed', '2025-01-21 20:43:06', '0', '0');
INSERT INTO `app_loan_emi_schedule` (`schedule_id`, `loan_id`, `month_no`, `beginning_principal`, `interest_amount`, `emi_amount`, `principal_repaid`, `ending_principal`, `payment_due_date`, `penalty_amount`, `payment_status`, `added_on`, `status`, `deleted`) VALUES ('19', '8', '1', '5000.00', '500.00', '500.00', '0.00', '5000.00', '2025-02-10', '0.00', 'pending', '2025-01-21 20:43:19', '1', '0');
INSERT INTO `app_loan_emi_schedule` (`schedule_id`, `loan_id`, `month_no`, `beginning_principal`, `interest_amount`, `emi_amount`, `principal_repaid`, `ending_principal`, `payment_due_date`, `penalty_amount`, `payment_status`, `added_on`, `status`, `deleted`) VALUES ('20', '7', '3', '5000.00', '300.00', '300.00', '2000.00', '3000.00', '2025-04-10', '0.00', 'Completed', '2025-01-21 20:44:54', '0', '0');
INSERT INTO `app_loan_emi_schedule` (`schedule_id`, `loan_id`, `month_no`, `beginning_principal`, `interest_amount`, `emi_amount`, `principal_repaid`, `ending_principal`, `payment_due_date`, `penalty_amount`, `payment_status`, `added_on`, `status`, `deleted`) VALUES ('21', '9', '1', '10000.00', '800.00', '800.00', '0.00', '10000.00', '2024-12-10', '0.00', 'Completed', '2025-01-21 22:10:11', '0', '0');
INSERT INTO `app_loan_emi_schedule` (`schedule_id`, `loan_id`, `month_no`, `beginning_principal`, `interest_amount`, `emi_amount`, `principal_repaid`, `ending_principal`, `payment_due_date`, `penalty_amount`, `payment_status`, `added_on`, `status`, `deleted`) VALUES ('22', '1', '4', '80000.00', '8000.00', '8000.00', '0.00', '80000.00', '2025-04-10', '0.00', 'Completed', '2025-01-25 09:24:49', '0', '0');
INSERT INTO `app_loan_emi_schedule` (`schedule_id`, `loan_id`, `month_no`, `beginning_principal`, `interest_amount`, `emi_amount`, `principal_repaid`, `ending_principal`, `payment_due_date`, `penalty_amount`, `payment_status`, `added_on`, `status`, `deleted`) VALUES ('23', '9', '2', '10000.00', '800.00', '800.00', '0.00', '10000.00', '2025-01-10', '80.00', 'Completed', '2025-01-25 09:29:31', '0', '0');
INSERT INTO `app_loan_emi_schedule` (`schedule_id`, `loan_id`, `month_no`, `beginning_principal`, `interest_amount`, `emi_amount`, `principal_repaid`, `ending_principal`, `payment_due_date`, `penalty_amount`, `payment_status`, `added_on`, `status`, `deleted`) VALUES ('24', '9', '3', '10000.00', '400.00', '400.00', '5000.00', '5000.00', '2025-02-10', '0.00', 'pending', '2025-01-25 10:01:40', '1', '0');
INSERT INTO `app_loan_emi_schedule` (`schedule_id`, `loan_id`, `month_no`, `beginning_principal`, `interest_amount`, `emi_amount`, `principal_repaid`, `ending_principal`, `payment_due_date`, `penalty_amount`, `payment_status`, `added_on`, `status`, `deleted`) VALUES ('25', '1', '5', '80000.00', '7600.00', '7600.00', '4000.00', '76000.00', '2025-05-10', '0.00', 'pending', '2025-01-25 18:28:31', '0', '1');
INSERT INTO `app_loan_emi_schedule` (`schedule_id`, `loan_id`, `month_no`, `beginning_principal`, `interest_amount`, `emi_amount`, `principal_repaid`, `ending_principal`, `payment_due_date`, `penalty_amount`, `payment_status`, `added_on`, `status`, `deleted`) VALUES ('26', '7', '4', '3000.00', '300.00', '300.00', '0.00', '3000.00', '2025-05-10', '0.00', 'pending', '2025-01-25 20:10:15', '1', '0');
INSERT INTO `app_loan_emi_schedule` (`schedule_id`, `loan_id`, `month_no`, `beginning_principal`, `interest_amount`, `emi_amount`, `principal_repaid`, `ending_principal`, `payment_due_date`, `penalty_amount`, `payment_status`, `added_on`, `status`, `deleted`) VALUES ('27', '10', '1', '15000.00', '1500.00', '1500.00', '0.00', '15000.00', '2025-01-10', '0.00', 'Completed', '2025-01-25 20:31:28', '0', '0');
INSERT INTO `app_loan_emi_schedule` (`schedule_id`, `loan_id`, `month_no`, `beginning_principal`, `interest_amount`, `emi_amount`, `principal_repaid`, `ending_principal`, `payment_due_date`, `penalty_amount`, `payment_status`, `added_on`, `status`, `deleted`) VALUES ('28', '10', '2', '15000.00', '1000.00', '1000.00', '5000.00', '10000.00', '2025-02-10', '0.00', 'Completed', '2025-01-25 20:35:09', '0', '0');
INSERT INTO `app_loan_emi_schedule` (`schedule_id`, `loan_id`, `month_no`, `beginning_principal`, `interest_amount`, `emi_amount`, `principal_repaid`, `ending_principal`, `payment_due_date`, `penalty_amount`, `payment_status`, `added_on`, `status`, `deleted`) VALUES ('29', '10', '3', '10000.00', '1000.00', '1000.00', '0.00', '10000.00', '2025-03-10', '0.00', 'pending', '2025-01-25 20:38:57', '0', '1');


-- Structure of table `app_loan_transactions`
CREATE TABLE `app_loan_transactions` (
  `transaction_id` int NOT NULL AUTO_INCREMENT,
  `loan_id` int DEFAULT NULL,
  `transaction_type` varchar(50) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `transaction_date` date DEFAULT NULL,
  `added_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint DEFAULT '1',
  `deleted` tinyint DEFAULT '0',
  PRIMARY KEY (`transaction_id`),
  KEY `loan_id` (`loan_id`),
  CONSTRAINT `app_loan_transactions_ibfk_1` FOREIGN KEY (`loan_id`) REFERENCES `app_loan_details` (`loan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
-- Data for table `app_loan_transactions`


-- Structure of table `app_referral_document`
CREATE TABLE `app_referral_document` (
  `id` int NOT NULL AUTO_INCREMENT,
  `referral_id` int NOT NULL,
  `document_name` varchar(100) DEFAULT NULL,
  `document_path` varchar(255) DEFAULT NULL,
  `added_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint DEFAULT '1',
  `deleted` tinyint DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `referral_id` (`referral_id`),
  CONSTRAINT `app_referral_document_ibfk_1` FOREIGN KEY (`referral_id`) REFERENCES `app_referral_user` (`reference_Id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
-- Data for table `app_referral_document`
INSERT INTO `app_referral_document` (`id`, `referral_id`, `document_name`, `document_path`, `added_on`, `status`, `deleted`) VALUES ('1', '1', 'Aadhar Card', 'uploads/referral_document/2025-01-20_16-19-11_3d14a517-7b18-4fe6-906c-2acfa160bf62.png', '2025-01-20 16:19:11', '1', '0');
INSERT INTO `app_referral_document` (`id`, `referral_id`, `document_name`, `document_path`, `added_on`, `status`, `deleted`) VALUES ('2', '1', 'ATM Card', 'uploads/referral_document/2025-01-20_16-19-11_f83d8064-4add-4ce3-84b5-412acf1415a7.jpeg', '2025-01-20 16:19:11', '1', '0');
INSERT INTO `app_referral_document` (`id`, `referral_id`, `document_name`, `document_path`, `added_on`, `status`, `deleted`) VALUES ('3', '2', 'Aadhar Card', 'uploads/referral_document/2025-01-21_14-58-55_1e8355e5-d77e-469e-be27-3e4b64fca9c1.jpeg', '2025-01-21 14:58:55', '1', '0');
INSERT INTO `app_referral_document` (`id`, `referral_id`, `document_name`, `document_path`, `added_on`, `status`, `deleted`) VALUES ('4', '2', 'ATM Card', 'uploads/referral_document/2025-01-21_14-58-55_1e8355e5-d77e-469e-be27-3e4b64fca9c1.jpeg', '2025-01-21 14:58:55', '1', '0');


-- Structure of table `app_referral_user`
CREATE TABLE `app_referral_user` (
  `reference_Id` int NOT NULL AUTO_INCREMENT,
  `ref_name` varchar(100) DEFAULT NULL,
  `ref_phone_number` varchar(15) DEFAULT NULL,
  `ref_percentage` decimal(5,2) DEFAULT NULL,
  `added_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint DEFAULT '1',
  `deleted` tinyint DEFAULT '0',
  PRIMARY KEY (`reference_Id`),
  UNIQUE KEY `ref_phone_number` (`ref_phone_number`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
-- Data for table `app_referral_user`
INSERT INTO `app_referral_user` (`reference_Id`, `ref_name`, `ref_phone_number`, `ref_percentage`, `added_on`, `status`, `deleted`) VALUES ('1', 'Manish Gondane', '8551959323', '2.00', '2025-01-20 16:19:11', '1', '0');
INSERT INTO `app_referral_user` (`reference_Id`, `ref_name`, `ref_phone_number`, `ref_percentage`, `added_on`, `status`, `deleted`) VALUES ('2', 'Suraj Jaiswal', '7387997293', '2.00', '2025-01-21 14:58:55', '1', '0');


-- Structure of table `app_revenue_report`
CREATE TABLE `app_revenue_report` (
  `report_id` int NOT NULL AUTO_INCREMENT,
  `loan_id` int DEFAULT NULL,
  `total_interest` decimal(10,2) DEFAULT NULL,
  `penalty_income` decimal(10,2) DEFAULT NULL,
  `referral_expense` decimal(10,2) DEFAULT NULL,
  `net_revenue` decimal(10,2) DEFAULT NULL,
  `emi_count` int DEFAULT '0',
  `outstanding_principal` decimal(10,2) DEFAULT '0.00',
  `calculated_on` date DEFAULT NULL,
  `added_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint DEFAULT '1',
  `deleted` tinyint DEFAULT '0',
  PRIMARY KEY (`report_id`),
  KEY `loan_id` (`loan_id`),
  CONSTRAINT `app_revenue_report_ibfk_1` FOREIGN KEY (`loan_id`) REFERENCES `app_loan_details` (`loan_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
-- Data for table `app_revenue_report`
INSERT INTO `app_revenue_report` (`report_id`, `loan_id`, `total_interest`, `penalty_income`, `referral_expense`, `net_revenue`, `emi_count`, `outstanding_principal`, `calculated_on`, `added_on`, `status`, `deleted`) VALUES ('1', '1', '34000.00', '1000.00', '520.00', '34480.00', '4', '80000.00', '2025-01-25', '2025-01-20 21:50:46', '1', '0');
INSERT INTO `app_revenue_report` (`report_id`, `loan_id`, `total_interest`, `penalty_income`, `referral_expense`, `net_revenue`, `emi_count`, `outstanding_principal`, `calculated_on`, `added_on`, `status`, `deleted`) VALUES ('2', '2', '17000.00', '500.00', '0.00', '17500.00', '5', '10000.00', '2025-01-20', '2025-01-20 23:04:56', '1', '0');
INSERT INTO `app_revenue_report` (`report_id`, `loan_id`, `total_interest`, `penalty_income`, `referral_expense`, `net_revenue`, `emi_count`, `outstanding_principal`, `calculated_on`, `added_on`, `status`, `deleted`) VALUES ('3', '4', '1500.00', '100.00', '0.00', '1600.00', '2', '5000.00', '2025-01-21', '2025-01-21 20:22:00', '1', '0');
INSERT INTO `app_revenue_report` (`report_id`, `loan_id`, `total_interest`, `penalty_income`, `referral_expense`, `net_revenue`, `emi_count`, `outstanding_principal`, `calculated_on`, `added_on`, `status`, `deleted`) VALUES ('4', '6', '1000.00', '100.00', '20.00', '1080.00', '1', '10000.00', '2025-01-21', '2025-01-21 20:32:33', '1', '0');
INSERT INTO `app_revenue_report` (`report_id`, `loan_id`, `total_interest`, `penalty_income`, `referral_expense`, `net_revenue`, `emi_count`, `outstanding_principal`, `calculated_on`, `added_on`, `status`, `deleted`) VALUES ('5', '7', '1300.00', '0.00', '26.00', '1274.00', '3', '3000.00', '2025-01-25', '2025-01-21 20:43:06', '1', '0');
INSERT INTO `app_revenue_report` (`report_id`, `loan_id`, `total_interest`, `penalty_income`, `referral_expense`, `net_revenue`, `emi_count`, `outstanding_principal`, `calculated_on`, `added_on`, `status`, `deleted`) VALUES ('6', '9', '1600.00', '160.00', '0.00', '1760.00', '2', '10000.00', '2025-01-25', '2025-01-25 09:29:31', '1', '0');
INSERT INTO `app_revenue_report` (`report_id`, `loan_id`, `total_interest`, `penalty_income`, `referral_expense`, `net_revenue`, `emi_count`, `outstanding_principal`, `calculated_on`, `added_on`, `status`, `deleted`) VALUES ('7', '10', '2500.00', '0.00', '0.00', '2500.00', '2', '10000.00', '2025-01-25', '2025-01-25 20:35:09', '1', '0');


-- Structure of table `app_user`
CREATE TABLE `app_user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` varchar(255) NOT NULL,
  `staff_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `user_type` int NOT NULL,
  `added_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` int DEFAULT '1',
  `deleted` int DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `phone` (`phone`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
-- Data for table `app_user`
INSERT INTO `app_user` (`id`, `user_id`, `staff_name`, `email`, `phone`, `password`, `user_type`, `added_on`, `status`, `deleted`) VALUES ('1', 'LN-01', 'suraj jaiswal', 'jaiswaljesus384@gmail.com', '7387997294', '$2y$10$tKtVGO9zNVstGh.GGieKZeRAF0sj0QTiNSsanpvUZ34UQCj5ojhQK', '1', '2025-01-14 00:00:00', '1', '0');
INSERT INTO `app_user` (`id`, `user_id`, `staff_name`, `email`, `phone`, `password`, `user_type`, `added_on`, `status`, `deleted`) VALUES ('2', 'LN-02', 'Akash jaiswal', 'akash@gmail.com', '7774446669', '$2y$10$TXE1jfDPUGZG0nj1PfoM7eYI3gwi/sBx.zUKyDAmoH.Om3654N0dy', '1', '2025-01-16 00:00:00', '1', '0');
INSERT INTO `app_user` (`id`, `user_id`, `staff_name`, `email`, `phone`, `password`, `user_type`, `added_on`, `status`, `deleted`) VALUES ('5', 'LN-03', 'Shantanu', 'Shantanu@gmail.com', '7387997299', '$2y$10$tqvqc4jxyGozHOTiNEPm1OMAksFe6bUTC3cZqYa7.LAzuhizlOkeW', '1', '2025-01-16 00:00:00', '1', '0');
INSERT INTO `app_user` (`id`, `user_id`, `staff_name`, `email`, `phone`, `password`, `user_type`, `added_on`, `status`, `deleted`) VALUES ('6', 'LN-06', 'Ram', 'Ram@gmail.com', '1112223334', '$2y$10$yh0DWNpkjJ8YO1L.S5WnT.Ft8AD5xvlHtmWlNNNdJLGtTqr1HwjVi', '2', '2025-01-18 00:00:00', '1', '0');
INSERT INTO `app_user` (`id`, `user_id`, `staff_name`, `email`, `phone`, `password`, `user_type`, `added_on`, `status`, `deleted`) VALUES ('9', 'LN-07', 'Ramesh Dada', 'Ram123@gmail.com', '1112123334', '$2y$10$Q.pN8hLtufkXbMlLtik7N.dn4Txoum3Th5TcksRBi24qvuxRrTW7G', '2', '2025-01-18 00:00:00', '1', '0');
INSERT INTO `app_user` (`id`, `user_id`, `staff_name`, `email`, `phone`, `password`, `user_type`, `added_on`, `status`, `deleted`) VALUES ('10', 'LN-10', 'vaishnavi', 'mane2@gmail.com', '1112323332', '$2y$10$EAyrBzNA7fuJb1LtPUKx7.lObEZKtPwyBAUq0R/56LEgZ7hDrarTe', '1', '2025-01-18 00:00:00', '1', '0');
INSERT INTO `app_user` (`id`, `user_id`, `staff_name`, `email`, `phone`, `password`, `user_type`, `added_on`, `status`, `deleted`) VALUES ('11', 'LN-11', 'kavitajadhav26', 'Roh2an@gmail.com', '1112223331', '$2y$10$HbJWVSXYdfnU.DLuY5f2PuVWPRcyEMSRRAQUmDx0K3Q33kVpmhtDu', '1', '2025-01-18 00:00:00', '1', '1');
INSERT INTO `app_user` (`id`, `user_id`, `staff_name`, `email`, `phone`, `password`, `user_type`, `added_on`, `status`, `deleted`) VALUES ('12', 'LN-12', 'Sandeep', 'Sandeep@gmail.com', '7387992294', '$2y$10$OawsMYI5Kw0HouFYBn9o2udcTZ.tkf35bFev8pDtYL8CJlY8NZHMi', '1', '2025-01-19 00:00:00', '1', '0');


-- Structure of table `client_code`
CREATE TABLE `client_code` (
  `client_id` varchar(255) DEFAULT NULL,
  `client_key` varchar(255) DEFAULT NULL,
  `client_name` varchar(255) DEFAULT NULL,
  UNIQUE KEY `client_id` (`client_id`),
  UNIQUE KEY `client_key` (`client_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
-- Data for table `client_code`
INSERT INTO `client_code` (`client_id`, `client_key`, `client_name`) VALUES ('99900', '6306e34f4877c2ea7e5d3c09f64d732241dba09afb2cffce295479c75d0b2b49', 'WebApp-Loan');
