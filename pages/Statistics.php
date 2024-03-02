<?php
/* Copyright (c) 2024 Mohammad J Hamdar */
/* This PHP script manages a statistics page for doctors' productivity, allowing admin to search and display the productivity
 data based on various criteria such as doctor ID, date range, and gained productivity. */
// Start session and check if user is logged in
session_start();
if(!isset($_SESSION["username"])){
    header('Location: Login.php');
}
$username=$_SESSION["username"];
?>

<?php
// Include database connection
include("sqlconnection.php");

// Function to sanitize input
function sanitizeInput($input)
{
    return htmlspecialchars(trim($input));
}

// Process POST data if form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $searchId = isset($_POST["search_id"]) ? sanitizeInput($_POST["search_id"]) : '';
    $searchfd = isset($_POST["search_fromdate"]) ? sanitizeInput($_POST["search_fromdate"]) : '';
    $searchtd = isset($_POST["search_todate"]) ? sanitizeInput($_POST["search_todate"]) : '';
    $searchgp = isset($_POST["search_GP"]) ? sanitizeInput($_POST["search_GP"]) : '';

    $conditions = [];

    // Check if Doctor ID is provided
    if(!empty($searchId))
    {
        $conditions[] = "d.doctor_id = $searchId";
    }

    // Check if both From Date and To Date are provided
    if (!empty($searchfd) && !empty($searchtd)) {
        list($selectedfYear, $selectedfMonth) = explode('-', $searchfd);
        list($selectedtYear, $selectedtMonth) = explode('-', $searchtd);
        
        $conditions[] = "(p.year > $selectedfYear OR (p.year = $selectedfYear AND p.month >= $selectedfMonth))";
        $conditions[] = "(p.year < $selectedtYear OR (p.year = $selectedtYear AND p.month <= $selectedtMonth))";
    }

    // Check if Gained Productivity is provided
    if ($searchgp !== '' || $searchgp === '0') {
        $conditions[] = "gained_productivity = " . (int)$searchgp;
    }

    // Generate the WHERE clause based on conditions
    $whereClause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : '';
    
    // SQL query to retrieve doctors' productivity data
    $sql = "SELECT d.doctor_id, first_name, last_name, month, year, productivity, gained_productivity
            FROM doctors d
            JOIN productivity p ON d.doctor_id = p.doctor_id
            $whereClause";
} else {
    // If not searching, fetch all doctors' productivity data
    $sql = "SELECT d.doctor_id, first_name, last_name, month, year, productivity, gained_productivity
            FROM doctors d
            JOIN productivity p ON d.doctor_id = p.doctor_id";
}

// Execute SQL query
$result = mysqli_query($conn, $sql);
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

        .search-form button {
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
            <li class="sidebar-list-item">
                <a href="Home.php" >
                    <span>Home</span>
                </a>
            </li>
            <li class="sidebar-list-item">
                <a href="doctors.php">
                    <span>Doctors</span>
                </a>
            </li>
            <li class="sidebar-list-item active">
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
    if (!$result) {
        echo "NOT Found. Please enter the correct inputs.";
    } else {
    ?>
    <!--body-->

    <div class="app-content">
            <div class="app-content-header">
                <h1 class="app-content-headerText">Statistics</h1>
                <?php $nb=mysqli_num_rows($result);
                echo "<h2 style=\"color: white;\">There are $nb results</h2>";?>
            </div>
            <form class="search-form" method="post" action="">
                <label>Doctor ID:</label>
                <input type="number" name="search_id">
                <label>From Date:</label>
                <input type="month" name="search_fromdate">
                <label>To Date :</label>
                <input type="month" name="search_todate">
                <label>Search by GP:</label>
                <input type="number" name="search_GP">
                <button type="submit">Search</button>
            </form>
            <!-----------doctors INFO---------------------->
            <div class="products-area-wrapper tableView">
                <div class="products-header">
                    <div class="product-cell ">Id</div>
                    <div class="product-cell ">FirstName</div>
                    <div class="product-cell ">LastName</div>
                    <div class="product-cell ">Month</div>
                    <div class="product-cell ">Year</div>
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
                    $productivity = $row['productivity'];
                    $gainedprod=$row['gained_productivity'];;
                    echo '<div class="products-row">';
                    echo '<div class="product-cell">' . $id . '</div>';
                    echo '<div class="product-cell">' . $firstName . '</div>';
                    echo '<div class="product-cell">' . $lastName . '</div>';
                    echo '<div class="product-cell">' . $month . '</div>';
                    echo '<div class="product-cell">' . $year . '</div>';
                    echo '<div class="product-cell">' . number_format($productivity, 2) . ' %</div>';
                    echo '<div class="product-cell">' . $gainedprod . ' %</div>';
                    echo '</div>';
                    
                }}
                ?>
            </div>
    </div>
    
</body>
</html>