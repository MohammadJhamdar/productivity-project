<?php
/* Copyright (c) 2024 Mohammad J Hamdar */
/*displays information about the logged-in doctor, including personal details, monthly attendance overview, and recent notifications.*/
session_start();

if (!isset($_SESSION["username"])) {
    header('Location: Login.php');
    exit();
}

if ($_SESSION["user_role"] == true) {
    header('Location: Login.php');
    exit();
}

include("sqlconnection.php");

$username = $_SESSION["username"];
$role = $_SESSION["user_role"];

$sql_doctor_info = "SELECT * FROM doctors WHERE username = '$username'";
$result_doctor_info = mysqli_query($conn, $sql_doctor_info);

if (!$result_doctor_info) {
    echo "Error retrieving doctor information: " . mysqli_error($conn);
    exit();
}

$doctor_info = mysqli_fetch_assoc($result_doctor_info);
$_SESSION["id"] = $doctor_info['doctor_id'];

// Get the current year
$current_year = date('Y');

$sql_monthly_attendance = "SELECT * FROM monthly_attendance WHERE doctor_id = " . $doctor_info['doctor_id'] . " AND year = $current_year";
$result_monthly_attendance = mysqli_query($conn, $sql_monthly_attendance);

if (!$result_monthly_attendance) {
    echo "Error retrieving monthly attendance information: " . mysqli_error($conn);
    exit();
}

$monthly_attendance = mysqli_fetch_assoc($result_monthly_attendance);

$sql_notifications = "SELECT * FROM notifications WHERE doctor_id = " . $doctor_info['doctor_id'] . " ORDER BY notification_id DESC LIMIT 5";
$result_notifications = mysqli_query($conn, $sql_notifications);

if (!$result_notifications) {
    echo "Error retrieving notifications: " . mysqli_error($conn);
    exit();
}
$notifications = mysqli_fetch_all($result_notifications, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <style>
        :root {
            --app-bg: #101827;
            --sidebar: rgba(21, 30, 47,1);
            --sidebar-main-color: #fff;
            --table-border: #1a2131;
            --table-header: #1a2131;
            --app-content-main-color: #fff;
            --sidebar-link: #fff;
            --sidebar-active-link: #1d283c;
            --sidebar-hover-link: #1a2539;
            --action-color: #2869ff;
            --action-color-hover: #6291fd;
            --app-content-secondary-color: #1d283c;
            --filter-reset: #2c394f;
            --filter-shadow: rgba(16, 24, 39, 0.8) 0px 6px 12px -2px, rgba(0, 0, 0, 0.3) 0px 3px 7px -3px;
        }

        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            width: 100%;
        }

        body {
    /* Remove overflow: hidden; */
    font-family: "Poppins", sans-serif;
    background-color: var(--app-bg);
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    margin: 0; /* Add margin: 0; to ensure no default margin */
}

.app-container {
    border-radius: 4px;
    width: 100%;
    height: 100%;
    max-height: 100%;
    max-width: 1280px;
    display: flex;
    overflow: hidden;
    box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;
    max-width: 2000px;
    margin: 0 auto;
    
}

        .sidebar {
            flex-basis: 200px;
            max-width: 200px;
            flex-shrink: 0;
            background-color: var(--sidebar);
            display: flex;
            flex-direction: column;
        }

        .sidebar-list {
            list-style-type: none;
            padding: 0;
        }

        .sidebar-list-item {
            position: relative;
            margin-bottom: 4px;
        }

        .sidebar-list-item a {
            display: flex;
            align-items: center;
            width: 100%;
            padding: 10px 16px;
            color: var(--sidebar-link);
            text-decoration: none;
            font-size: 14px;
            line-height: 24px;
        }

        .sidebar-list-item:hover {
            background-color: var(--sidebar-hover-link);
        }

        .sidebar-list-item.active {
            background-color: var(--sidebar-active-link);
        }

        .sidebar-list-item.active:before {
            content: "";
            position: absolute;
            right: 0;
            background-color: var(--action-color);
            height: 100%;
            width: 4px;
        }
        .content{
            margin-left: 100px;
            color: #fff;
        }
        </style>

</head>
<body>
    <!--nav-->
    <div class="app-container">
        <div class="sidebar">
            <ul class="sidebar-list">
                <li class="sidebar-list-item active">
                    <a href="doctorhome.php" >
                        <span>Home</span>
                    </a>
                </li>
                <li class="sidebar-list-item">
                    <a href="Attendance.php">
                        <span>Attendance</span>
                    </a>
                </li>
                <li class="sidebar-list-item">
                    <a href="doctorNotification.php" >
                        <span>Write Notifications</span>
                    </a>
                </li>
                <li class="sidebar-list-item">
                    <a href="doctorsproductivity.php" >
                        <span>Productivity</span>
                    </a>
                </li>
                <li class="sidebar-list-item">
                    <a href="Logout.php" >
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
            <div style="margin-top: auto;">
                <p style="color: var(--app-content-main-color);">Welcome @<?php echo $username; ?></p>
            </div>
        </div>
        <!-- end  nav -->
        <!-- Display doctor information -->
        <div class="content">
            <h2>Doctor Information</h2>
            <ul>
                <li><p>ID: <?php echo $doctor_info['doctor_id']; ?></p></li>
                <li><p>Name: <?php echo $doctor_info['first_name'] . ' ' . $doctor_info['last_name']; ?></p></li>
                <li><p>Phone Number: <?php echo $doctor_info['phone_number']; ?></p></li>
                <li><p>Username: <?php echo $doctor_info['username']; ?></p></li>
                <!-- Add more information as needed -->
            </ul>
            <!-- Include this section below the doctor's profile information -->
            <h2>Monthly Attendance Overview for <?php echo $current_year; ?></h2>
            <ul>
                <li><p>Month: <?php echo date('F'); ?></p></li>
                <li><p>Days Attended: <?php echo $monthly_attendance['days_attended']; ?></p></li>
                <li><p>Total Valid Days: <?php echo $monthly_attendance['valid_days']; ?></p></li>
            </ul>
            <!-- Include this section below the monthly attendance overview -->
            <h2>Recent Notifications</h2>
            <ul>
                <?php foreach ($notifications as $notification): ?>
                    <li><p><?php echo $notification['message']; ?></p></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</body>
</html>