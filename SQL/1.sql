CREATE TABLE app_user (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id VARCHAR(255) NOT NULL UNIQUE,            -- User's unique ID (LN-01, LN-02, ...)
    staff_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,              -- User's email
    phone VARCHAR(15) UNIQUE,                        -- Optional phone number
    password VARCHAR(255) NOT NULL,                  -- Hashed password
    user_type INT(11) NOT NULL,                      -- Restricted user roles
    added_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP,    -- Record creation timestamp
    status INT(1) DEFAULT 1,                         -- Account status
    deleted INT(1) DEFAULT 0                          -- Account Deleted
);


-- genrate client ---
CREATE TABLE client_code (
    client_id VARCHAR(255) UNIQUE,
    client_key VARCHAR(255) UNIQUE,
    client_name VARCHAR(255)
);

-- insert client value
insert into client_code (client_id,client_key,client_name) value("99900","6306e34f4877c2ea7e5d3c09f64d732241dba09afb2cffce295479c75d0b2b49","suraj jaiswal");