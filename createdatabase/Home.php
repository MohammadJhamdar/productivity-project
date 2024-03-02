<?php
//Mohammad Jaafar Hamdar (105622)
//Mohammad Salim Farhat (104969)

$servername = "localhost";
$username = "root";
$password = "";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$sql_create_database = "CREATE DATABASE IF NOT EXISTS phpproject";
if ($conn->query($sql_create_database) === TRUE) {
    echo "Database created successfully\n";
} else {
    echo "Error creating database: " . $conn->error . "\n";
}

// Select the database
$conn->select_db("phpproject");

// SQL statements to create tables
$sql_create_doctors = "
CREATE TABLE doctors (
    doctor_id INT,
    first_name VARCHAR(30),
    last_name VARCHAR(30),
    phone_number VARCHAR(30),
    username varchar(50),
    password varchar(50),
    PRIMARY KEY(doctor_id)
)";

$sql_create_attendance = "
CREATE TABLE attendance (
    attendance_id INT AUTO_INCREMENT,
    doctor_id INT,
    attendance_date DATE,
    PRIMARY KEY(attendance_id),
    FOREIGN KEY(doctor_id) REFERENCES doctors(doctor_id) ON DELETE CASCADE
)";

$sql_create_days_of_attendance = "
CREATE TABLE Days_of_attendance_in_a_month (
    id INT AUTO_INCREMENT,
    month INT(2),
    year INT(4),
    valid_days INT,
    primary key(id)
)";

$sql_create_monthly_attendance = "
CREATE TABLE monthly_attendance (
    monthly_attendance_id INT AUTO_INCREMENT,
    doctor_id INT,
    month INT(2),
    year INT(4),
    valid_days INT DEFAULT 24,
    days_attended INT DEFAULT 0,
    PRIMARY KEY(monthly_attendance_id),
    FOREIGN KEY(doctor_id) REFERENCES doctors(doctor_id) ON DELETE CASCADE
)";

$sql_create_admin = "
CREATE TABLE admin (
    admin_id INT,
    first_name VARCHAR(30),
    last_name VARCHAR(30),
    phone_number VARCHAR(30),
    username varchar(50),
    password varchar(50),
    PRIMARY KEY(admin_id)
)";

$sql_create_users = "
CREATE TABLE users (
    username varchar(50),
    password varchar(50),
    role boolean,
    primary key(username)
)";

$sql_create_notifications = "
CREATE TABLE notifications (
    notification_id INT AUTO_INCREMENT,
    doctor_id INT,
    message VARCHAR(255),
    PRIMARY KEY(notification_id),
    FOREIGN KEY(doctor_id) REFERENCES doctors(doctor_id) ON DELETE CASCADE
)";

$sql_create_productivity = "
CREATE TABLE productivity (
    id INT AUTO_INCREMENT,
    doctor_id INT,
    month INT(2),
    year INT(4),
    productivity float,
    gained_productivity int,
    PRIMARY KEY(id),
    FOREIGN KEY(doctor_id) REFERENCES doctors(doctor_id) ON DELETE CASCADE
)";

// Execute SQL statements
if ($conn->query($sql_create_doctors) === TRUE) {
    echo "Table 'doctors' created successfully\n";
} else {
    echo "Error creating table 'doctors': " . $conn->error . "\n";
}

if ($conn->query($sql_create_attendance) === TRUE) {
    echo "Table 'attendance' created successfully\n";
} else {
    echo "Error creating table 'attendance': " . $conn->error . "\n";
}

if ($conn->query($sql_create_days_of_attendance) === TRUE) {
    echo "Table 'Days_of_attendance_in_a_month' created successfully\n";
} else {
    echo "Error creating table 'Days_of_attendance_in_a_month': " . $conn->error . "\n";
}

if ($conn->query($sql_create_monthly_attendance) === TRUE) {
    echo "Table 'monthly_attendance' created successfully\n";
} else {
    echo "Error creating table 'monthly_attendance': " . $conn->error . "\n";
}

if ($conn->query($sql_create_admin) === TRUE) {
    echo "Table 'admin' created successfully\n";
} else {
    echo "Error creating table 'admin': " . $conn->error . "\n";
}

if ($conn->query($sql_create_users) === TRUE) {
    echo "Table 'users' created successfully\n";
} else {
    echo "Error creating table 'users': " . $conn->error . "\n";
}

if ($conn->query($sql_create_notifications) === TRUE) {
    echo "Table 'notifications' created successfully\n";
} else {
    echo "Error creating table 'notifications': " . $conn->error . "\n";
}

if ($conn->query($sql_create_productivity) === TRUE) {
    echo "Table 'productivity' created successfully\n";
} else {
    echo "Error creating table 'productivity': " . $conn->error . "\n";
}

// Close the connection
$conn->close();

$dbname = "phpproject";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Trigger for admin (AFTER INSERT)
$sql = "
CREATE TRIGGER after_insert_admin
AFTER INSERT ON admin
FOR EACH ROW
BEGIN
    INSERT INTO users (username, password, role)
    VALUES (NEW.username, NEW.password, true);
END;

";
$conn->query($sql);

// Trigger for admin (AFTER DELETE)
$sql = "
CREATE TRIGGER after_delete_admin
AFTER DELETE ON admin
FOR EACH ROW
BEGIN
    DELETE FROM users WHERE username = OLD.username;
END;

";
$conn->query($sql);

// Trigger for admin (AFTER UPDATE)
$sql = "
CREATE TRIGGER after_update_admin
AFTER UPDATE ON admin
FOR EACH ROW
BEGIN
    IF NEW.username <> OLD.username THEN
        -- Update username in users table
        UPDATE users
        SET username = NEW.username
        WHERE username = OLD.username;
    END IF;

    IF NEW.password <> OLD.password THEN
        -- Update password in users table
        UPDATE users
        SET password = NEW.password
        WHERE username = NEW.username;
    END IF;
END;
";
$conn->query($sql);

// Trigger for inserting monthly_attendance
$sql = "
CREATE TRIGGER insert_monthly_attendance
AFTER INSERT ON attendance
FOR EACH ROW
BEGIN
    DECLARE attendance_month INT;
    DECLARE attendance_year INT;

    SET attendance_month = MONTH(NEW.attendance_date);
    SET attendance_year = YEAR(NEW.attendance_date);

    IF EXISTS (
        SELECT 1
        FROM monthly_attendance
        WHERE doctor_id = NEW.doctor_id
          AND month = attendance_month
          AND year = attendance_year
    ) THEN
        UPDATE monthly_attendance
        SET days_attended = days_attended + 1
        WHERE doctor_id = NEW.doctor_id
          AND month = attendance_month
          AND year = attendance_year;
    ELSE
        INSERT INTO monthly_attendance (doctor_id, month, year, days_attended)
        VALUES (NEW.doctor_id, attendance_month, attendance_year, 1);
    END IF;
END;
";
$conn->query($sql);

// Trigger for deleting monthly_attendance
$sql = "
CREATE TRIGGER delete_monthly_attendance
BEFORE DELETE ON attendance
FOR EACH ROW
BEGIN
    DECLARE attendance_month INT;
    DECLARE attendance_year INT;

    SET attendance_month = MONTH(OLD.attendance_date);
    SET attendance_year = YEAR(OLD.attendance_date);

    IF EXISTS (
        SELECT 1
        FROM monthly_attendance
        WHERE doctor_id = OLD.doctor_id
          AND month = attendance_month
          AND year = attendance_year
    ) THEN
        UPDATE monthly_attendance
        SET days_attended = GREATEST(days_attended - 1, 0)
        WHERE doctor_id = OLD.doctor_id
          AND month = attendance_month
          AND year = attendance_year;
    END IF;
END;
";
$conn->query($sql);

// Trigger for updating monthly_attendance
$sql = "
CREATE TRIGGER update_monthly_attendance
AFTER UPDATE ON attendance
FOR EACH ROW
BEGIN
    DECLARE old_attendance_month INT;
    DECLARE old_attendance_year INT;
    DECLARE new_attendance_month INT;
    DECLARE new_attendance_year INT;

    SET old_attendance_month = MONTH(OLD.attendance_date);
    SET old_attendance_year = YEAR(OLD.attendance_date);
    SET new_attendance_month = MONTH(NEW.attendance_date);
    SET new_attendance_year = YEAR(NEW.attendance_date);

    IF EXISTS (
        SELECT 1
        FROM monthly_attendance
        WHERE doctor_id = OLD.doctor_id
          AND month = old_attendance_month
          AND year = old_attendance_year
    ) THEN
        UPDATE monthly_attendance
        SET days_attended = GREATEST(days_attended - 1, 0)
        WHERE doctor_id = OLD.doctor_id
          AND month = old_attendance_month
          AND year = old_attendance_year;
    END IF;

    IF EXISTS (
        SELECT 1
        FROM monthly_attendance
        WHERE doctor_id = NEW.doctor_id
          AND month = new_attendance_month
          AND year = new_attendance_year
    ) THEN
        UPDATE monthly_attendance
        SET days_attended = days_attended + 1
        WHERE doctor_id = NEW.doctor_id
          AND month = new_attendance_month
          AND year = new_attendance_year;
    ELSE
        INSERT INTO monthly_attendance (doctor_id, month, year, days_attended)
        VALUES (NEW.doctor_id, new_attendance_month, new_attendance_year, 1);
    END IF;
END;
";
$conn->query($sql);

// Trigger for doctors (AFTER INSERT)
$sql = "
CREATE TRIGGER after_insert_doctor
AFTER INSERT ON doctors
FOR EACH ROW
BEGIN
    INSERT INTO users (username, password, role)
    VALUES (NEW.username, NEW.password, false);
END;
";
$conn->query($sql);

// Trigger for doctors (AFTER DELETE)
$sql = "
-- Trigger for doctors (AFTER DELETE)
CREATE TRIGGER after_delete_doctor
AFTER DELETE ON doctors
FOR EACH ROW
BEGIN
    DELETE FROM users WHERE username = OLD.username;
END;
";
$conn->query($sql);

// Trigger for doctors (AFTER UPDATE)
$sql = "
CREATE TRIGGER after_update_doctor
AFTER UPDATE ON doctors
FOR EACH ROW
BEGIN
    IF NEW.username <> OLD.username THEN
        UPDATE users
        SET username = NEW.username
        WHERE username = OLD.username;
    END IF;

    IF NEW.password <> OLD.password THEN
        UPDATE users
        SET password = NEW.password
        WHERE username = NEW.username;
    END IF;
END;
";
$conn->query($sql);

// Trigger to update valid_days in monthly_attendance after inserting attendance
$sql = "
CREATE TRIGGER update_valid_days
AFTER INSERT ON Days_of_attendance_in_a_month
FOR EACH ROW
BEGIN
    UPDATE monthly_attendance
    SET valid_days = NEW.valid_days
    WHERE month = NEW.month and year=new.year;
END;
";
$conn->query($sql);

$sql = "
CREATE TRIGGER update_valid_days_on_update
AFTER UPDATE ON Days_of_attendance_in_a_month
FOR EACH ROW
BEGIN
    UPDATE monthly_attendance
    SET valid_days = NEW.valid_days
   WHERE month = NEW.month and year=new.year;
END;
";
$conn->query($sql);

$sql = "
CREATE TRIGGER update_valid_days_after_attendance_insert
AFTER INSERT ON attendance
FOR EACH ROW
BEGIN
    DECLARE attendance_month INT;
    DECLARE attendance_year INT;
    DECLARE valid_days_in_month INT;

    -- Extract month and year from the new attendance record
    SET attendance_month = MONTH(NEW.attendance_date);
    SET attendance_year = YEAR(NEW.attendance_date);

    -- Get the valid_days for the specified month and year from Days_of_attendance_in_a_month
    SELECT valid_days INTO valid_days_in_month
    FROM Days_of_attendance_in_a_month
    WHERE month = attendance_month AND year = attendance_year;

    IF valid_days_in_month IS NOT NULL THEN
        -- Update the valid_days in monthly_attendance
        UPDATE monthly_attendance
        SET valid_days = valid_days_in_month
        WHERE doctor_id = NEW.doctor_id
          AND month = attendance_month
          AND year = attendance_year;
    END IF;
END;
";
$conn->query($sql);


// Insert sample doctors
$sql_insert_doctors = "
INSERT INTO doctors (doctor_id, first_name, last_name, phone_number, username, password)
VALUES
    (1, 'John', 'Doe', '123-456-7890', 'john_doe', 'password123'),
    (2, 'Ali', 'Jaber', '0367890', 'ali_jaber', 'ali@jaber'),
    (5, 'Mohammad Jaafar', 'Hamdar', '71964442', 'mhd_ham', '123'),
    (4, 'Mohammad Salim', 'Farhat', '78857132', 'salimous', '456'),
    (3, 'Hussein', 'Matar', '76548432', 'hss_mtr', 'pass'),
    (6, 'Mohammad', 'Sharaf ldeen', '03123456', 'mhm_sharaf', 'idk123');
";

// Insert sample admin
$sql_insert_admin = "
INSERT INTO admin (admin_id, first_name, last_name, phone_number, username, password)
VALUES
    (1, 'Admin', 'Master', '123-456-7890', 'admin_user', 'adminpass');
";

// Insert sample attendance records
$sql_insert_attendance = "
INSERT INTO attendance (doctor_id, attendance_date)
VALUES
-- Doctor 1
(1, '2023-10-01'), (1, '2023-10-02'), (1, '2023-10-24'),(1, '2023-10-03'),(1, '2023-10-04'),(1, '2023-10-05'),
(1, '2023-10-06'),(1, '2023-10-07'),(1, '2023-10-08'),(1, '2023-10-09'),(1, '2023-10-10'),(1, '2023-10-11'),
(1, '2023-10-12'),(1, '2023-10-13'),(1, '2023-10-14'),(1, '2023-10-15'),(1, '2023-10-16'),(1, '2023-10-17'),
(1, '2023-10-18'),(1, '2023-10-19'),(1, '2023-10-20'),(1, '2023-10-21'),(1, '2023-10-22'),    

(1, '2023-11-1'),(1, '2023-11-2'),(1, '2023-11-3'),(1, '2023-11-4'),(1, '2023-11-5'),(1, '2023-11-6'),(1, '2023-11-7'),
(1, '2023-11-8'),(1, '2023-11-8'),(1, '2023-11-9'),(1, '2023-11-10'),(1, '2023-11-11'),(1, '2023-11-12'),(1, '2023-11-13'),
(1, '2023-11-14'),(1, '2023-11-15'),(1, '2023-11-16'),(1, '2023-11-17'),(1, '2023-11-18'),(1, '2023-11-19'),

(1, '2023-12-01'), (1, '2023-12-02'), (1, '2023-12-03'), (1, '2023-12-04'), (1, '2023-12-05'),
(1, '2023-12-06'), (1, '2023-12-07'), (1, '2023-12-08'), (1, '2023-12-09'), (1, '2023-12-10'),
(1, '2023-12-11'), (1, '2023-12-12'), (1, '2023-12-13'), (1, '2023-12-14'), (1, '2023-12-15'),
(1, '2023-12-16'), (1, '2023-12-17'), (1, '2023-12-18'),

(1, '2024-01-03'),(1, '2024-01-04'),(1, '2024-01-05'),(1, '2024-01-06'),(1, '2024-01-08'),(1, '2024-01-09'),
(1, '2024-01-07'),(1, '2024-01-11'),(1, '2024-01-12'),(1, '2024-01-13'),(1, '2024-01-14'),(1, '2024-01-15'),
(1, '2024-01-16'),(1, '2024-01-17'),(1, '2024-01-18'),(1, '2024-01-19'),(1, '2024-01-20'),
-- Doctor 2
(2, '2023-11-1'),(2, '2023-11-2'),(2, '2023-11-3'),(2, '2023-11-4'),(2, '2023-11-5'),(2, '2023-11-6'),(2, '2023-11-7'),(2, '2023-11-8'),(2, '2023-11-8'),(2, '2023-11-9'),(2, '2023-11-10'),(2, '2023-11-11'),(2, '2023-11-12'),(2, '2023-11-13'),(2, '2023-11-14'),(2, '2023-11-15'),(2, '2023-11-16'),(2, '2023-11-17'),(2, '2023-11-18'),(2, '2023-11-19'),
(2, '2023-10-1'),(2, '2023-10-2'),(2, '2023-10-3'),(2, '2023-10-4'),(2, '2023-10-5'),(2, '2023-10-6'),(2, '2023-10-7'),(2, '2023-10-8'),(2, '2023-10-8'),(2, '2023-10-9'),(2, '2023-10-10'),(2, '2023-10-11'),(2, '2023-10-12'),(2, '2023-10-13'),(2, '2023-10-14'),(2, '2023-10-15'),(2, '2023-10-16'),
(2, '2023-12-1'),(2, '2023-12-2'),(2, '2023-12-3'),(2, '2023-12-4'),(2, '2023-12-5'),(2, '2023-12-6'),(2, '2023-12-7'),(2, '2023-12-8'),(2, '2023-12-8'),(2, '2023-12-9'),(2, '2023-12-10'),(2, '2023-12-11'),(2, '2023-12-12'),(2, '2023-12-13'),(2, '2023-12-14'),(2, '2023-12-15'),
(2, '2024-1-1'), (2, '2024-1-2'), (2, '2024-1-3'), (2, '2024-1-4'), (2, '2024-1-5'), (2, '2024-1-6'), (2, '2024-1-7'),
(2, '2024-1-8'), (2, '2024-1-8'), (2, '2024-1-9'), (2, '2024-1-10'), (2, '2024-1-11'), (2, '2024-1-12'),
(2, '2024-1-13'), (2, '2024-1-14'), (2, '2024-1-15'),

-- Doctor 3
(3, '2023-10-03'), (3, '2023-10-04'), (3, '2023-10-05'), (3, '2023-10-06'),
(3, '2023-10-07'), (3, '2023-10-08'), (3, '2023-10-09'), (3, '2023-10-11'),
(3, '2023-10-12'), (3, '2023-10-13'), (3, '2023-10-14'), (3, '2023-10-15'),
(3, '2023-10-16'), (3, '2023-10-17'), (3, '2023-10-18'), (3, '2023-10-19'),

(3, '2023-11-03'), (3, '2023-11-04'), (3, '2023-11-05'), (3, '2023-11-06'),
(3, '2023-11-07'), (3, '2023-11-08'), (3, '2023-11-09'), (3, '2023-11-10'),
(3, '2023-11-11'), (3, '2023-11-12'), (3, '2023-11-13'), (3, '2023-11-14'),
(3, '2023-11-15'), (3, '2023-11-17'), (3, '2023-11-18'), (3, '2023-11-19'),
(3, '2023-11-20'),

(3, '2023-12-03'), (3, '2023-12-04'), (3, '2023-12-05'), (3, '2023-12-06'),
(3, '2023-12-07'), (3, '2023-12-08'), (3, '2023-12-09'),

(3, '2024-01-03'), (3, '2024-01-04'), (3, '2024-01-05'), (3, '2024-01-06'),
(3, '2024-01-07'), (3, '2024-01-08'), (3, '2024-01-09'), (3, '2024-01-10'),
(3, '2024-01-11'),

-- Doctor 4
(4, '2023-10-02'), (4, '2023-10-03'), (4, '2023-10-04'), (4, '2023-10-05'), (4, '2023-10-06'), (4, '2023-10-07'),
(4, '2023-11-02'), (4, '2023-11-03'), (4, '2023-11-04'), (4, '2023-11-05'),
(4, '2023-11-06'), (4, '2023-11-07'), (4, '2023-11-08'), (4, '2023-11-09'), (4, '2023-11-10'),
(4, '2023-12-02'), (4, '2023-12-03'), (4, '2023-12-04'), (4, '2023-12-05'),
(4, '2023-12-06'), (4, '2023-12-07'), (4, '2023-12-08'), (4, '2023-12-09'),
(4, '2023-12-10'), (4, '2023-12-11'), (4, '2023-12-12'), (4, '2023-12-13'),
(4, '2023-12-14'),

(4, '2024-01-02'), (4, '2024-01-03'), (4, '2024-01-04'), (4, '2024-01-05'),
(4, '2024-01-06'), (4, '2024-01-07'), (4, '2024-01-08'), (4, '2024-01-09'),
(4, '2024-01-10'), (4, '2024-01-11'),


-- Doctor5
(5, '2024-01-04'), (5, '2024-01-05'), (5, '2024-01-06'), (5, '2024-01-07'),
(5, '2024-01-08'), (5, '2024-01-09'), (5, '2024-01-10'), (5, '2024-01-11'),
(5, '2024-01-12'), (5, '2024-01-13'), (5, '2024-01-14'), (5, '2024-01-15'),
(5, '2024-01-16'), (5, '2024-01-17'), (5, '2024-01-18'), (5, '2024-01-19'),
(5, '2024-01-20'), (5, '2024-01-21'), (5, '2024-01-22'),

(5, '2023-12-09'), (5, '2023-12-10'), (5, '2023-12-11'), (5, '2023-12-12'),
(5, '2023-12-13'), (5, '2023-12-14'), (5, '2023-12-15'), (5, '2023-12-16'),
(5, '2023-12-17'), (5, '2023-12-18'), (5, '2023-12-19'), (5, '2023-12-20'),
(5, '2023-12-21'), (5, '2023-12-22'), (5, '2023-12-23'), (5, '2023-12-24'),
(5, '2023-12-25'),
(5, '2023-11-09'), (5, '2023-11-10'), (5, '2023-11-11'), (5, '2023-11-12'),
(5, '2023-11-13'), (5, '2023-11-14'), (5, '2023-11-15'), (5, '2023-11-16'),
(5, '2023-11-17'), (5, '2023-11-18'), (5, '2023-11-19'), (5, '2023-11-20'),
(5, '2023-11-21'), (5, '2023-11-22'), (5, '2023-11-23'), (5, '2023-11-24'),
(5, '2023-11-25'), (5, '2023-11-26'), (5, '2023-11-27'), (5, '2023-11-28'),
(5, '2023-11-29'), (5, '2023-11-30'),
(5, '2023-10-09'), (5, '2023-10-10'), (5, '2023-10-11'), (5, '2023-10-12'),
(5, '2023-10-13'), (5, '2023-10-14'), (5, '2023-10-15'), (5, '2023-10-16'),
(5, '2023-10-17'), (5, '2023-10-18'), (5, '2023-10-19'), (5, '2023-10-20'),
(5, '2023-10-21'), (5, '2023-10-22'), (5, '2023-10-23'), (5, '2023-10-24'),
(5, '2023-10-25'), (5, '2023-10-26'), (5, '2023-10-27'), (5, '2023-10-28'),
(5, '2023-10-29'), (5, '2023-10-30'), (5, '2023-10-31'), (5, '2023-11-01'),

-- DOCTOR 6
(6, '2023-10-1'),(6, '2023-10-2'),(6, '2023-10-3'),(6, '2023-10-4'),(6, '2023-10-5'),(6, '2023-10-6'),(6, '2023-10-7'),(6, '2023-10-8'),(6, '2023-10-8'),(6, '2023-10-9'),(6, '2023-10-10'),(6, '2023-10-11'),(6, '2023-10-12'),(6, '2023-10-13'),(6, '2023-10-14'),(6, '2023-10-15'),(6, '2023-10-16'),
(6, '2023-11-1'),(6, '2023-11-2'),(6, '2023-11-3'),(6, '2023-11-4'),(6, '2023-11-5'),(6, '2023-11-6'),(6, '2023-11-7'),(6, '2023-11-8'),(6, '2023-11-8'),(6, '2023-11-9'),(6, '2023-11-10'),(6, '2023-11-11'),(6, '2023-11-12'),(6, '2023-11-13'),(6, '2023-11-14'),(6, '2023-11-15'),(6, '2023-11-16'),(6, '2023-11-17'),(6 ,'2023-11-18'),(6, '2023-11-19'),
(6, '2023-12-1'),(6, '2023-12-2'),(6, '2023-12-3'),(6, '2023-12-4'),(6, '2023-12-5'),(6, '2023-12-6'),(6, '2023-12-7'),(6, '2023-12-8'),(6, '2023-12-8'),(6, '2023-12-9'),(6, '2023-12-10'),(6, '2023-12-11'),(6, '2023-12-12'),(6, '2023-12-13'),(6, '2023-12-14'),(6, '2023-12-15'),
(6, '2024-1-1'),(6, '2024-1-2'),(6, '2024-1-3'),(6, '2024-1-4'),(6, '2024-1-5'),(6, '2024-1-6'),(6, '2024-1-7'),(6, '2024-1-8'),(6, '2024-1-8'),(6, '2024-1-9'),(6, '2024-1-10'),(6, '2024-1-11'),(6, '2024-1-12'),(6, '2024-1-13'),(6, '2024-1-14'),(6, '2024-1-15');
";

$sql_Days_of_attendance_in_a_month = "
INSERT INTO Days_of_attendance_in_a_month (month, year, valid_days)
VALUES
    (10, 2023, 25),
    (11, 2023, 23),
    (12, 2023, 18),
    (1, 2024, 20);
";

$sql_notifications= "
INSERT INTO notifications (doctor_id, message)
VALUES
    (1, 'Notification from Doctor 1'),
    (2, 'Notification from Doctor 2');
";
// Execute queries
if ($conn->multi_query($sql_insert_doctors . $sql_insert_admin . $sql_insert_attendance . $sql_Days_of_attendance_in_a_month . $sql_notifications)) {
    echo "Records inserted successfully.";
} else {
    echo "Error: " . $conn->error;
}

// Close connection
$conn->close();

$message = "DataBase create successfully";
echo '<div style="font-size: 18px; color: #007bff;">' . $message . '</div><br>';

echo '<hr><div style="font-size: 18px; color: #007bff;">' . 
    'Admin account:&nbsp;&nbsp;&nbsp;&nbsp; username: admin_user &nbsp;&nbsp;&nbsp;&nbsp; password: adminpass <br>' . 
    'Doctor account:&nbsp;&nbsp;&nbsp;&nbsp; username: ali_jaber &nbsp;&nbsp;&nbsp;&nbsp; password: ali@jaber' . 
    '</div><hr>';


?>
