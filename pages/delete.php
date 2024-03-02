<?php 
/* Copyright (c) 2024 Mohammad J Hamdar */
/*This PHP code manages user sessions, handles attendance search and deletion */
// Start session to persist user login status
session_start();

// Redirect to login page if user is not logged in
if(!isset($_SESSION["username"])){
    header('Location: Login.php');
}
$username=$_SESSION["username"];
?>

<?php
// Include the file with the database connection
include("sqlconnection.php");

// Function to sanitize user input
function sanitizeInput($input)
{
    return htmlspecialchars(trim($input));
}

// Check if the form is submitted using POST method
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize and get search parameters
    $searchId = isset($_POST["search_id"]) ? sanitizeInput($_POST["search_id"]) : '';
    $date = isset($_POST["date"]) ? sanitizeInput($_POST["date"]) : '';

    $conditions = [];

    // Add conditions based on search parameters
    if (!empty($searchId)) {
        $conditions[] = "doctor_id = $searchId";
    }

    if (!empty($date)) {
        // Add single quotes around the date value
        $conditions[] = "attendance_date = '$date'";
    }

    // Build WHERE clause for the SQL query
    $whereClause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : '';
    $sql = "SELECT * FROM attendance $whereClause";
} else {
    // If not searching, fetch all doctors
    $sql = "SELECT * FROM attendance";
}

// Execute the SQL query
$result = mysqli_query($conn, $sql);

// Process form submissions for delete action
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["action"])) {
    $action = $_POST["action"];

    if ($action === "delete") {
        // Handle delete operation
        $doctor_id = sanitizeInput($_POST["doctor_id"]);
        $attendance_date = sanitizeInput($_POST["attendance_date"]);
        
        // Check if the entry exists before attempting to delete
        $checkSql = "SELECT * FROM attendance WHERE doctor_id = $doctor_id AND attendance_date = '$attendance_date'";
        $checkResult = mysqli_query($conn, $checkSql);

        if (mysqli_num_rows($checkResult) > 0) {
            $delete_sql = "DELETE FROM attendance WHERE doctor_id = $doctor_id AND attendance_date = '$attendance_date'";
            $delete_result = mysqli_query($conn, $delete_sql);

            if ($delete_result) {
                // Redirect to the same page after successful delete
                header("Location: {$_SERVER['PHP_SELF']}");
                exit();
            } else {
                echo "Error deleting attendance entry: " . mysqli_error($conn);
            }
        } else {
            echo "Attendance entry not found for the selected ID and date.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Doctors</title>
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
            <li class="sidebar-list-item ">
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
            <li class="sidebar-list-item active">
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
        <a href="addattendancedate.php">
        <img src="arrow.png" alt="Back to Previous Page" style="width: 50px; height: 50px; margin: 10px;"></a>
        <div class="app-content">
            
            <div class="app-content-header">
                <h1 class="app-content-headerText">Delete date</h1>
            </div>
            <form class="search-form" method="post" action="">
                <label>Search by ID:</label>
                <input type="number" name="search_id">

                <label>Search by Date:</label>
                <input type="date" name="date">
                <button type="submit">Search</button>
            </form>
            <!-----------doctors---------------------->
            <div class="products-area-wrapper tableView">
                <div class="products-header">
                    <div class="product-cell ">Id</div>
                    <div class="product-cell ">Date</div>
                    <div class="product-cell ">Delete</div>
                </div>
                <?php
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<div class="products-row">';
                    echo '<div class="product-cell">' . $row["doctor_id"] . '</div>';
                    echo '<div class="product-cell">' . $row["attendance_date"] . '</div>';
                    echo '<div class="product-cell">' . "<form method='post' id='deleteForm_{$row["doctor_id"]}' onSubmit='return false;'>
                    <input type='hidden' name='doctor_id' value='{$row["doctor_id"]}'>
                    <input type='hidden' name='attendance_date' value='{$row["attendance_date"]}'>
                    <input type='hidden' name='action' value='delete'>
                    <button type='button' onclick='confirmDelete({$row["doctor_id"]}, \"{$row["attendance_date"]}\")'>Delete</button>
                    </form>" . '</div>';
                    echo '</div>';
                }}
                ?>
            </div>
        <!--End body-->
      

</body>
<script>
    // Function to show confirmation dialog before deleting
    function confirmDelete(doctorId, attendanceDate) {
        var result = confirm("Are you sure you want to delete this attendance entry?");
        if (result) {
            // If the user clicks "OK," submit the form for deletion
            document.getElementById('deleteForm_' + doctorId).submit();
        }
    }
</script>
</html>

                    