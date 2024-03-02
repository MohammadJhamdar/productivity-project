<?php 
/* Copyright (c) 2024 Mohammad J Hamdar */
/*This PHP script for adding new doctors to a system*/
session_start();

if(!isset($_SESSION["username"])){
    header('Location: Login.php');
}
$username=$_SESSION["username"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AddDoctor</title>
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

        /* AddDoctor Form Styling */
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

form input[type="number"],
form input[type="text"],
form input[type="password"] {
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
            <li class="sidebar-list-item">
                <a href="Home.php" >
                    <span>Home</span>
                </a>
            </li>
            <li class="sidebar-list-item active">
                <a href="doctors.php">
                    <span>Doctors</span>
                </a>
            </li>
            <li class="sidebar-list-item">
                <a href="Statistics.php" >
                    <span>Statistics</span>
                </a>
            </li>
            <li class="sidebar-list-item">
                <a href="Notification.php" >
                    <span>Notifications</span>
                </a>
            </li>
            <li class="sidebar-list-item">
                <a href="addattendancedate.php" >
                    <span>Add Attendance Date</span>
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
    <a href="doctors.php">
        <img src="arrow.png" alt="Back to Previous Page" style="width: 50px; height: 50px; margin: 10px;">
    </a>
    <!-- end  nav     -->
        <form method="post" action="adddoctor.php">
            <center>
            <label for="id">id:</label>
            <input type="number" id="id" name="id" required>
            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name" required>

            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name" required>

            <label for="phone_number">Phone Number:</label>
            <input type="text" id="phone_number" name="phone_number" required>

            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required></center>

            <center><button type="submit">Add Doctor</button></center>
        </form>
        <?php
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Include your database connection file
    include("sqlconnection.php");

    // Function to sanitize input
    function sanitizeInput($input) {
        return htmlspecialchars(trim($input));
    }

    // Sanitize input data
    $id = sanitizeInput($_POST["id"]);
    $firstName = sanitizeInput($_POST["first_name"]);
    $lastName = sanitizeInput($_POST["last_name"]);
    $phoneNumber = sanitizeInput($_POST["phone_number"]);
    $username = sanitizeInput($_POST["username"]);
    $password = $_POST["password"];

    // Check if the username is already taken
    $checkSql = "SELECT * FROM users WHERE username='$username'";
    $checkResult = mysqli_query($conn, $checkSql);

    if (mysqli_num_rows($checkResult) > 0) {
        // Redirect to the registration page with an error message
        echo '<script>alert("Username is already taken. Please choose another username.");</script>';
        exit();
    }

    // Insert data into the database
    $sql = "INSERT INTO doctors (doctor_id, first_name, last_name, phone_number, username, password)
            VALUES ($id, '$firstName', '$lastName', '$phoneNumber', '$username', '$password')";

    if (mysqli_query($conn, $sql)) {
        echo '<script>alert("added.");</script>';
    } else {
        echo '<script>alert("An error occurred.")</script>';
    }

    mysqli_close($conn);
}
?>

<!-- In your adddoctor.php file, you can display the error message like this -->


</body>
</html>