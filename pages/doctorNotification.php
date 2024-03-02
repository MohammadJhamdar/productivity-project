<?php
/* Copyright (c) 2024 Mohammad J Hamdar */
//The code is a PHP script  allows a logged-in doctor to write notifications.

// Start a new session to manage user login state
session_start();

// Check if the user is not logged in, redirect to the login page
if (!isset($_SESSION["username"])) {
    header('Location: Login.php');
    exit();
}

// Include the SQL connection file
include("sqlconnection.php");

// Fetch user information from the session
$username = $_SESSION["username"];
$role = $_SESSION["user_role"];

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve doctor_id based on the username
    $sql_doctor_id = "SELECT doctor_id FROM doctors WHERE username = '$username'";
    $result_doctor_id = mysqli_query($conn, $sql_doctor_id);

    // Check for errors in the query
    if (!$result_doctor_id) {
        echo "Error retrieving doctor information: " . mysqli_error($conn);
        exit();
    }

    $row_doctor_id = mysqli_fetch_assoc($result_doctor_id);

    // If doctor_id is found, proceed to add notification
    if ($row_doctor_id) {
        $doctor_id = $row_doctor_id['doctor_id'];

        // Get the notification message from the form
        $notification_message = mysqli_real_escape_string($conn, $_POST['notification_message']);

        // Insert the notification into the database
        $sql_insert_notification = "INSERT INTO notifications (doctor_id, message) VALUES ($doctor_id, '$notification_message')";
        $result_insert_notification = mysqli_query($conn, $sql_insert_notification);

        // Check for successful notification insertion
        if ($result_insert_notification) {
            echo "";
        } else {
            echo "Error adding notification: " . mysqli_error($conn);
        }
    } else {
        // Handle the case where doctor_id is not found
        echo "Error: Doctor ID not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Write Notification</title>
    <style>
        /* AddDoctor Form Styling */
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
            font-family: "Poppins", sans-serif;
            background-color: var(--app-bg);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
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

        form {
            width: 40%;
            height: 70%;
            margin: 20px auto;
            background-color: var(--app-bg);
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 4px;
            box-shadow: rgba(0, 0, 0, 0.1) 0px 4px 12px;
        }

        form label {
            font-weight: bold;
            display: block;
            color:var(--sidebar-link) ;
            margin-bottom: 5px;
        }

        form textarea {
            background-color: lightgray;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            width: 50%;
            margin-bottom: 10px;
        }

        form button[type="submit"] {
            background-color: #2869ff;
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 10px 16px;
            cursor: pointer;
        }

        form button[type="submit"]:hover {
            background-color: #6291fd;
        }
    </style>
</head>
<body>
 <!--nav     -->
 <div class="app-container">
    <div class="sidebar">
    <ul class="sidebar-list">
                <li class="sidebar-list-item ">
                    <a href="doctorhome.php" >
                        <span>Home</span>
                    </a>
                </li>
                <li class="sidebar-list-item">
                    <a href="Attendance.php">
                        <span>Attendance</span>
                    </a>
                </li>
                <li class="sidebar-list-item active">
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
        <div style=" margin-top: auto;">
                <p style="color: var(--app-content-main-color);">Welcome @<?php echo $username; ?></p>
        </div>
    </div>
    
    <!-- end  nav     -->
        
        <!-- Your form for writing notifications -->
        <form action="doctorNotification.php" method="post">
            <center>
                <label for="notification_message">Notification Message:</label>
                <textarea name="notification_message" id="notification_message" rows="4" cols="50"></textarea>
                <br>
                <center><button type="submit">Add Notification</button></center>
            </center>
        </form>
    </div>
</body>

</html>
