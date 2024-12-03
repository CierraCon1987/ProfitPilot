-- Cierra Bailey-Rice (8998948)
-- Harpreet Kaur (8893116)
-- Gurkamal Singh ()

create database ProfitPilot;
use ProfitPilot;

--  Table Creation
CREATE TABLE ID_Counters (
    entity_name VARCHAR(50) PRIMARY KEY,
    last_id INT NOT NULL DEFAULT 0
);

CREATE TABLE Users (
    user_id VARCHAR(20) PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE Projects (
    project_id VARCHAR(20) PRIMARY KEY,
    user_id VARCHAR(20) NOT NULL,
    project_name VARCHAR(255) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE,
    budget DECIMAL(10, 2) NOT NULL,
    status ENUM('Planning', 'In Progress', 'Completed', 'On Hold') NOT NULL DEFAULT 'Planning',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE
);

CREATE TABLE Clients (
    client_id VARCHAR(20) PRIMARY KEY,
    client_name VARCHAR(255) NOT NULL,
    contact_name VARCHAR(100),
    email VARCHAR(255),
    phone_number VARCHAR(50),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE TimeTracking (
    time_entry_id VARCHAR(20) PRIMARY KEY,
    project_id VARCHAR(20) NOT NULL,
    user_id VARCHAR(20) NOT NULL,
    hours DECIMAL(5, 2) NOT NULL,
    date_logged DATE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES Projects(project_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE
);

CREATE TABLE Invoices (
    invoice_id VARCHAR(20) PRIMARY KEY,
    project_id VARCHAR(20) NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    due_date DATE NOT NULL,
    paid BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES Projects(project_id) ON DELETE CASCADE
);

CREATE TABLE Payments (
    payment_id VARCHAR(20) PRIMARY KEY,
    invoice_id VARCHAR(20) NOT NULL,
    amount_paid DECIMAL(10, 2) NOT NULL,
    payment_date DATE NOT NULL,
    payment_method ENUM('Credit Card', 'Bank Transfer', 'Cash', 'Cheque') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (invoice_id) REFERENCES Invoices(invoice_id) ON DELETE CASCADE
);

CREATE TABLE ProjectCosts (
    project_cost_id VARCHAR(20) PRIMARY KEY,
    project_id VARCHAR(20) NOT NULL,
    cost_type ENUM('Labor', 'Materials', 'Travel', 'Other') NOT NULL,
    cost_amount DECIMAL(10, 2) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES Projects(project_id) ON DELETE CASCADE
);

CREATE TABLE Expenses (
    expense_id VARCHAR(20) PRIMARY KEY,
    project_id VARCHAR(20) NOT NULL,
    expense_type ENUM('Supplies', 'Contractor', 'Miscellaneous') NOT NULL,
    expense_amount DECIMAL(10, 2) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES Projects(project_id) ON DELETE CASCADE
);

CREATE TABLE ProjectMilestones (
    milestone_id VARCHAR(20) PRIMARY KEY,
    project_id VARCHAR(20) NOT NULL,
    milestone_name VARCHAR(255) NOT NULL,
    milestone_description TEXT,
    due_date DATE NOT NULL,
    status ENUM('Not Started', 'In Progress', 'Completed') NOT NULL DEFAULT 'Not Started',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES Projects(project_id) ON DELETE CASCADE
);

CREATE TABLE ProjectNotes (
    note_id VARCHAR(20) PRIMARY KEY,
    project_id VARCHAR(20) NOT NULL,
    note_text TEXT NOT NULL,
    created_by VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES Projects(project_id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES Users(user_id) ON DELETE CASCADE
);

CREATE TABLE ProjectTasks (
    task_id VARCHAR(20) PRIMARY KEY,
    project_id VARCHAR(20) NOT NULL,
    task_name VARCHAR(255) NOT NULL,
    assigned_to VARCHAR(20),
    start_date DATE NOT NULL,
    end_date DATE,
    status ENUM('Not Started', 'In Progress', 'Completed') NOT NULL DEFAULT 'Not Started',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES Projects(project_id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_to) REFERENCES Users(user_id) ON DELETE SET NULL
);

alter table users add username VARCHAR(50) NOT NULL UNIQUE; 


select * from users;