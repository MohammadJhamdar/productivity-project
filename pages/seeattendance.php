<?php
/* Copyright (c) 2024 Mohammad J Hamdar */
/*This PHP script handles session authentication, retrieves a doctor's ID, 
and displays their attendance dates from a MySQL database in a responsive web page*/
// Start the session
session_start();

// Redirect to login page if username is not set
if (!isset($_SESSION["username"])) {
    header('Location: Login.php');
}

// Include database connection
include("sqlconnection.php");

// Retrieve username and user role from session
$username = $_SESSION["username"];
$role = $_SESSION["user_role"];

// Query to get doctor_id based on username
$sql_doctor_id = "SELECT doctor_id FROM doctors WHERE username = '$username'";
$result_doctor_id = mysqli_query($conn, $sql_doctor_id);

// Check for errors in retrieving doctor information
if (!$result_doctor_id) {
    echo "Error retrieving doctor information: " . mysqli_error($conn);
    exit();
}

// Fetch the doctor_id
$row_doctor_id = mysqli_fetch_assoc($result_doctor_id);

// Check if doctor_id is found
if ($row_doctor_id) {
    $doctor_id = $row_doctor_id['doctor_id'];

    // Query to get attendance information based on doctor_id
    $sql_attendance = "SELECT * FROM attendance WHERE doctor_id = $doctor_id";
    $result_attendance = mysqli_query($conn, $sql_attendance);

    // Check for errors in retrieving attendance information
    if (!$result_attendance) {
        echo "Error retrieving attendance information: " . mysqli_error($conn);
        exit();
    }
} else {
    echo "Error: Doctor ID not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Information</title>
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

        /*body part*/

        .app-content {
            padding: 16px;
            background-color: var(--app-bg);
            height: 100%;
            flex: 1;
            max-height: 100%;
            display: flex;
            flex-direction: column;
        }

        .app-content-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 4px;
        }

        .app-content-headerText {
            color: var(--app-content-main-color);
            font-size: 24px;
            line-height: 32px;
            margin: 0;
        }

        .app-content-headerButton {
            background-color: var(--action-color);
            color: #fff;
            font-size: 14px;
            line-height: 24px;
            border: none;
            border-radius: 4px;
            height: 32px;
            padding: 0 16px;
            transition: 0.2s;
            cursor: pointer;
        }

        .app-content-headerButton:hover {
            background-color: var(--action-color-hover);
        }

        .search-form {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .search-form label {
            font-weight: bold;
            margin-right: 10px;
            color:var(--sidebar-link); 
        }

        .search-form input[type="text"] {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin: 2%;
        }

        .search-form button[type="submit"] {
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 8px 16px;
            margin-left: 10px;
            cursor: pointer;
        }

        .products-area-wrapper {
            width: 100%;
            max-height: 100%;
            overflow: auto;
            padding: 0 4px;
        }

        .tableView .products-header {
            display: flex;
            align-items: center;
            border-radius: 4px;
            background-color: var(--app-content-secondary-color);
            position: sticky;
            top: 0;
        }

        .tableView .products-row {
            display: flex;
            align-items: center;
            border-radius: 4px;
            margin: 2%;
        }

        .tableView .products-row:hover {
            box-shadow: var(--filter-shadow);
            background-color: var(--app-content-secondary-color);
        }

        .tableView .product-cell {
            flex: 1;
            padding: 8px 16px;
            color: var(--app-content-main-color);
            font-size: 14px;
            display: flex;
            align-items: center;
        }

        /* Edit Form */

        .edit-form {
          margin:auto;
          width: 60%;
          background-color: var(--app-bg);
            
            padding: 20px;
            border-radius: 4px;
        }

        .edit-form label {
          margin: 1%;
          color:var(--sidebar-link) ;
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
            
        }
        
        .edit-form input[type="text"] { 
          background-color: lightgray;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            width: 60%;
        }
        
        .edit-button {
          margin: 1%;
          background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 8px 16px;
            margin-left: 10px;
            cursor: pointer;
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
                <li class="sidebar-list-item active">
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
        <div style=" margin-top: auto;">
                <p style="color: var(--app-content-main-color);">Welcome @<?php echo $username; ?></p>
        </div>
    </div>
    
    <!-- end  nav     -->

    
    <!-- end  nav     -->
        <a href="Attendance.php">
        <img src="arrow.png" alt="Back to Previous Page" style="width: 50px; height: 50px; margin: 10px;">
        </a>
        <!-- massege form -->
        <div class="app-content">

            <div class="products-area-wrapper tableView">
                <div class="products-header">
                    <div class="product-cell">Attendance Dates</div>
                </div>
                <?php
                while ($row_attendance = mysqli_fetch_assoc($result_attendance)) {
                    echo '<div class="products-row">';
                    echo '<div class="product-cell">' . $row_attendance["attendance_date"] . '</div>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </div>
    <!-- end massege form -->
</body>

</html>
