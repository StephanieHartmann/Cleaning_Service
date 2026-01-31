CREATE TABLE Employee (
  employee_id SERIAL primary key,
  name varchar(100) not null
);

CREATE TABLE ServiceOrder (
  order_id SERIAL primary key,
  customer_name varchar(120) not null,
  phone varchar(40),
  email varchar(120),
  address varchar(200),
  cleaning_type varchar(50),
  size_sqm int,
  status varchar(50) default 'New',
  schedule_at timestamp,
  report_text TEXT,
  hours_worked decimal(5,2),
  total_price decimal(10,2),
  employee_id int references Employee(employee_id)
);

INSERT INTO Employee (name) VALUES ('John Doe'), ('Jane Smith');