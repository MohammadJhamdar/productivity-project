<?php 
/* Copyright (c) 2024 Mohammad J Hamdar */
/*This PHP script manages a doctor's productivity information, 
allowing searching based on criteria such as ID, month, and year. */
session_start();

// Check if the username is not set in the session, redirect to login page
if(!isset($_SESSION["username"])){
    header('Location: Login.php');
}

// Retrieve the username from the session
$username=$_SESSION["username"];
?>

<?php
// Include the database connection file
include("sqlconnection.php");

// Function to sanitize input data
function sanitizeInput($input)
{
    return htmlspecialchars(trim($input));
}

// Function to check if a given month is valid
function isValidMonth($month)
{
    return is_numeric($month) && $month >= 1 && $month <= 12;
}

// Function to check if a given year is valid
function isValidYear($year)
{
    // You can adjust the valid range as needed
    return is_numeric($year) && $year >= 1900 && $year <= date("Y") + 10;
}

// Check if the form is submitted using POST method
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize input values
    $searchId = isset($_POST["search_id"]) ? sanitizeInput($_POST["search_id"]) : '';
    $searchmonth = (isset($_POST["search_month"]) && is_numeric($_POST["search_month"])) ? sanitizeInput($_POST["search_month"]) : '';
    $searchyear = (isset($_POST["search_year"]) && is_numeric($_POST["search_year"])) ? sanitizeInput($_POST["search_year"]) : '';

    $conditions = [];

    // Add conditions based on search criteria
    if (!empty($searchId)) {
        $conditions[] = "d.doctor_id = $searchId";
    }

    if (!empty($searchmonth) && isValidMonth($searchmonth)) {
        $conditions[] = "m.month = $searchmonth";
    }

    if (!empty($searchyear) && isValidYear($searchyear)) {
        $conditions[] = "m.year = $searchyear";
    }

    // Build the SQL query based on search conditions
    $whereClause = !empty($conditions) ? "AND " . implode(" AND ", $conditions) : '';
    $sql = "SELECT d.doctor_id, d.first_name, d.last_name, p.month, p.year, m.valid_days, m.days_attended, p.productivity, p.gained_productivity
            FROM doctors d
            JOIN productivity p ON d.doctor_id = p.doctor_id
            JOIN monthly_attendance m ON d.doctor_id = m.doctor_id
            WHERE p.month = m.month AND p.year = m.year $whereClause";
    

} else {
    // If not searching, fetch all doctors
    $sql = "SELECT d.doctor_id,d.first_name,d.last_name,p.month,p.year,m.valid_days,m.days_attended,p.productivity,p.gained_productivity
    FROM
        doctors d JOIN
    productivity p ON d.doctor_id = p.doctor_id
    JOIN
        monthly_attendance m ON d.doctor_id = m.doctor_id
    WHERE
        p.month = m.month
        AND p.year = m.year";
}

// Execute the SQL query
$result = mysqli_query($conn, $sql);



if(isset($_POST['export'])){
    // Sanitize input values for export
    $exportId = isset($_POST["search_id"]) ? sanitizeInput($_POST["search_id"]) : '';
    $exportMonth = isset($_POST["search_month"]) ? sanitizeInput($_POST["search_month"]) : '';
    $exportYear = isset($_POST["search_year"]) ? sanitizeInput($_POST["search_year"]) : '';
    
    // Conditions for export query
    $exportConditions = [];
    
    if (!empty($exportId)) {
        $exportConditions[] = "d.doctor_id = $exportId";
    }

    if (!empty($exportMonth) && isValidMonth($exportMonth)) {
        $exportConditions[] = "m.month = $exportMonth";
    }

    if (!empty($exportYear) && isValidYear($exportYear)) {
        $exportConditions[] = "m.year = $exportYear";
    }

    // Build the SQL query for export
    $exportWhereClause = !empty($exportConditions) ? "AND " . implode(" AND ", $exportConditions) : '';
    $exportSql = "SELECT d.doctor_id, d.first_name, d.last_name, p.month, p.year, m.valid_days, m.days_attended, p.productivity, p.gained_productivity
            FROM doctors d
            JOIN productivity p ON d.doctor_id = p.doctor_id
            JOIN monthly_attendance m ON d.doctor_id = m.doctor_id
            WHERE p.month = m.month AND p.year = m.year $exportWhereClause";
    
    // Fetch the export result set
    $exportResult = mysqli_query($conn, $exportSql);

    // Output headers to make the browser download the file instead of displaying it
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=productivity_report.csv');

    // Create a file handle connected to PHP output stream
    $output = fopen('php://output', 'w');

    // Write the CSV headers
    fputcsv($output, array('ID', 'First Name', 'Last Name', 'Month', 'Year', 'Valid Days', 'Days Attendance', 'Productivity', 'Gained Productivity'));

    // Fetch the export result set
    while ($row = mysqli_fetch_assoc($exportResult)) {
        // Write each row to the CSV file
        fputcsv($output, $row);
    }

    // Close the file handle
    fclose($output);

    // Prevent further execution
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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
    /*overflow-y: auto; /* Add this line to enable vertical scrolling */
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

        .search-form input {
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
            margin-bottom: 2%;
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

       
        </style>

</head>
<body>
    <!--nav     -->
<div class="app-container">
    <div class="sidebar">
        <ul class="sidebar-list">
            <li class="sidebar-list-item active">
                <a href="Home.php" >
                    <span>Home</span>
                </a>
            </li>
            <li class="sidebar-list-item">
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
    <?php 
    //include('productivity.php') ;  
    if (!$result) {
        echo "NOT Found. Please enter the correct inputs.";
    } else {
    ?>
    <!--body-->
    <div class="app-content">
            <div class="app-content-header">
                <h1 class="app-content-headerText">Productivity</h1>
                
            </div>
            <form class="search-form" method="post" action="">
                <label>Search by ID:</label>
                <input type="number" name="search_id">
                <label>Search by Month:</label>
                <input type="text" name="search_month">
                <label>Search by Year:</label>
                <input type="text" name="search_year">
                <button type="submit">Search</button>
                <button type="submit" name="export">Extract To File</button>
            </form>
            <!-----------doctors INFO---------------------->
            <div class="products-area-wrapper tableView">
                <div class="products-header">
                    <div class="product-cell ">Id</div>
                    <div class="product-cell ">FirstName</div>
                    <div class="product-cell ">LastName</div>
                    <div class="product-cell ">Month</div>
                    <div class="product-cell ">Year</div>
                    <div class="product-cell ">Valid Days</div>
                    <div class="product-cell ">Days Attendace</div>
                    <div class="product-cell ">Productivity</div>
                    <div class="product-cell ">Gained Productivity</div>
                </div>
                <?php
                while ($row = mysqli_fetch_array($result)) {
                    $id = $row['doctor_id'];
                    $firstName=$row['first_name'];
                    $lastName=$row['last_name'];
                    $month = $row['month'];
                    $year = $row['year'];
                    $valid = $row['valid_days'];
                    $days = $row['days_attended'];
                    $productivity = $row['productivity'];
                    $gainedprod=$row['gained_productivity'];

                    echo '<div class="products-row">';
                    echo '<div class="product-cell">' . $id . '</div>';
                    echo '<div class="product-cell">' . $firstName . '</div>';
                    echo '<div class="product-cell">' . $lastName . '</div>';
                    echo '<div class="product-cell">' . $month . '</div>';
                    echo '<div class="product-cell">' . $year . '</div>';
                    echo '<div class="product-cell">' . $valid . '</div>';
                    echo '<div class="product-cell">' . $days . '</div>';
                    echo '<div class="product-cell">' . number_format($productivity, 2) . ' %</div>';
                    echo '<div class="product-cell">' . $gainedprod . ' %</div>';
                    echo '</div>';
                    
                }}
                ?>
            </div>
    </div>
    
</body>
</html>