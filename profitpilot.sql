-- Cierra Bailey-Rice (8998948)
-- Harpreet Kaur (8893116)
-- Gurkamal Singh (9001186)

create database ProfitPilot;
use ProfitPilot;

-- Creating the 'users' table first
CREATE TABLE users (
    user_id VARCHAR(13) PRIMARY KEY,  
    username VARCHAR(255) NOT NULL UNIQUE,  
    email VARCHAR(255) NOT NULL UNIQUE, 
    password VARCHAR(255) NOT NULL,  
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,  
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP 
);

-- Creating the 'projects' table after 'users' table
CREATE TABLE projects (
    project_id VARCHAR(13) PRIMARY KEY, 
    user_id VARCHAR(13) NOT NULL,
    project_name VARCHAR(255) NOT NULL,
    client_name VARCHAR(255) NOT NULL,
    start_date DATE,
    end_date DATE,
    status ENUM('Not Started', 'In Progress', 'Completed') DEFAULT 'Not Started',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Creating the 'tasks' table
CREATE TABLE tasks (
    task_id VARCHAR(13) PRIMARY KEY, 
    project_id VARCHAR(13),  
    user_id VARCHAR(13),
    task_name VARCHAR(255) NOT NULL,
    hours_worked DECIMAL(10, 2) NOT NULL,
    hourly_rate DECIMAL(10, 2) NOT NULL,
    total_amount DECIMAL(10, 2) GENERATED ALWAYS AS (hours_worked * hourly_rate) STORED,
    task_description TEXT,
    status ENUM('Not Started', 'In Progress', 'Completed') DEFAULT 'Not Started',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(project_id) ON DELETE CASCADE
);

-- Creating the 'calculations' table


-- Creating the 'provinces' table
CREATE TABLE provinces (
    province_id VARCHAR(13) PRIMARY KEY,
    province_name VARCHAR(255) NOT NULL
);

-- Inserting sample data into 'provinces'
INSERT INTO provinces (province_id, province_name) VALUES
('ON', 'Ontario'),
('QC', 'Quebec'),
('BC', 'British Columbia'),
('AB', 'Alberta'),
('NS', 'Nova Scotia'),
('NB', 'New Brunswick'),
('PE', 'Prince Edward Island'),
('NL', 'Newfoundland and Labrador'),
('MB', 'Manitoba'),
('SK', 'Saskatchewan'),
('YT', 'Yukon'),
('NT', 'Northwest Territories'),
('NU', 'Nunavut');

ALTER TABLE calculations ADD COLUMN task_id VARCHAR(255);
ALTER TABLE calculations ADD COLUMN province VARCHAR(255);
ALTER TABLE calculations ADD COLUMN tax_rate DECIMAL(5,4);

ALTER TABLE calculations
ADD COLUMN total_amount_with_tax DECIMAL(10, 2);


ALTER TABLE provinces ADD COLUMN tax_rate DECIMAL(5, 4) NOT NULL DEFAULT 0;
UPDATE provinces
SET tax_rate = CASE province_id
    WHEN 'AB' THEN 0.05  -- Alberta
    WHEN 'BC' THEN 0.12  -- British Columbia
    WHEN 'MB' THEN 0.13  -- Manitoba
    WHEN 'NB' THEN 0.15  -- New Brunswick
    WHEN 'NL' THEN 0.15  -- Newfoundland and Labrador
    WHEN 'NS' THEN 0.15  -- Nova Scotia
    WHEN 'NT' THEN 0.05  -- Northwest Territories
    WHEN 'NU' THEN 0.05  -- Nunavut
    WHEN 'ON' THEN 0.13  -- Ontario
    WHEN 'PE' THEN 0.15  -- Prince Edward Island
    WHEN 'QC' THEN 0.14975 -- Quebec
    WHEN 'SK' THEN 0.11  -- Saskatchewan
    WHEN 'YT' THEN 0.05  -- Yukon
    ELSE 0 -- Default value if no match
END;
ALTER TABLE provinces MODIFY tax_rate DECIMAL(5,4) DEFAULT 0.05;
