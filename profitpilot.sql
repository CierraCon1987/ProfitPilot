-- Cierra Bailey-Rice (8998948)
-- Harpreet Kaur (8893116)
-- Gurkamal Singh (9001186)

create database ProfitPilot;
use ProfitPilot;

CREATE TABLE projects (
    project_id VARCHAR(13) PRIMARY KEY, 
    user_id VARCHAR(13) NOT NULL,
    project_name VARCHAR(255) NOT NULL,
    client_name VARCHAR(255) NOT NULL,
    start_date DATE,
    end_date DATE,
    status ENUM('Not Started', 'In Progress', 'Completed') DEFAULT 'Not Started',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

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

CREATE TABLE calculations (
    calc_id VARCHAR(13) PRIMARY KEY, 
    project_id VARCHAR(13), 
    total_hours DECIMAL(10, 2) NOT NULL,
    total_rate DECIMAL(10, 2) NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(project_id) ON DELETE CASCADE
);

CREATE TABLE users (
    user_id VARCHAR(13) PRIMARY KEY,  
    username VARCHAR(255) NOT NULL UNIQUE,  
    email VARCHAR(255) NOT NULL UNIQUE, 
    password VARCHAR(255) NOT NULL,  
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,  
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP 
);

CREATE TABLE provinces (
    province_id VARCHAR(13) PRIMARY KEY,
    province_name VARCHAR(255) NOT NULL
);

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