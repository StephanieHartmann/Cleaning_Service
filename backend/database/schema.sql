-- Employee Table
CREATE TABLE Employee (
  employee_id SERIAL primary key,
  name varchar(100) not null
);

-- Service Order Main Table
CREATE TABLE ServiceOrder (
  order_id SERIAL primary key,
  
  -- Fields used in Create Order (orders.php)
  first_name varchar(100) not null,
  last_name varchar(100) not null,
  phone varchar(40),
  email varchar(120),
  address varchar(200), -- Full concatenated address
  cleaning_type varchar(50),
  size_sqm decimal(10,2),
  
  status varchar(50) default 'New', -- New, Scheduled, Completed, Invoiced, Cancelled
  
  -- Scheduling (order.php - action: schedule)
  service_date DATE,
  service_time TIME,
  assigned_to varchar(100), -- Employee Name
  
  -- Completion (order.php - action: complete)
  report_text TEXT,
  hours_worked decimal(5,2),
  
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert Employees (Seed)
DELETE FROM Employee;
INSERT INTO Employee (name) VALUES 
    ('Hans Müller'), 
    ('Petra Schmidt'), 
    ('Klaus Weber'), 
    ('Heidi Weber');