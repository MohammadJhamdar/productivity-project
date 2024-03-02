<?php 
/* Copyright (c) 2024 Mohammad J Hamdar */
// This PHP script manages search functionality, and options for editing and deleting doctors.
session_start();  // Start the session to manage user login state

// If the user is not logged in, redirect them to the login page
if(!isset($_SESSION["username"])){
    header('Location: Login.php');
}

// Retrieve the username from the session for later use
$username = $_SESSION["username"];
?>

<?php
// Include the file for database connection
include("sqlconnection.php");

// Function to sanitize input data to prevent SQL injection and cross-site scripting
function sanitizeInput($input)
{
    return htmlspecialchars(trim($input));
}

// Check if the form has been submitted using POST method
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve and sanitize search parameters
    $searchId = isset($_POST["search_id"]) ? sanitizeInput($_POST["search_id"]) : '';
    $searchFirstName = isset($_POST["search_first_name"]) ? sanitizeInput($_POST["search_first_name"]) : '';
    $searchLastName = isset($_POST["search_last_name"]) ? sanitizeInput($_POST["search_last_name"]) : '';

    // Prepare conditions for SQL query based on search parameters
    $conditions = [];

    if (!empty($searchId)) {
        $conditions[] = "doctor_id = $searchId";
    }

    if (!empty($searchFirstName)) {
        $conditions[] = "first_name LIKE '%$searchFirstName%'";
    }

    if (!empty($searchLastName)) {
        $conditions[] = "last_name LIKE '%$searchLastName%'";
    }

    // Construct the WHERE clause for SQL query
    $whereClause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : '';
    $sql = "SELECT * FROM doctors $whereClause";
} else {
    // If not searching, fetch all doctors
    $sql = "SELECT * FROM doctors";
}

// Execute the SQL query
$result = mysqli_query($conn, $sql);

// Check for errors in the query execution
if (!$result) {
    echo "Error: " . mysqli_connect_error();
} 

// Process the form data if an action is specified
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["action"])) {
    $action = $_POST["action"];

    if ($action === "edit") {
        // Retrieve doctor information for editing
        $doctor_id = sanitizeInput($_POST["doctor_id"]);
        $sql = "SELECT * FROM doctors WHERE doctor_id = $doctor_id";
        $result = mysqli_query($conn, $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            $doctor = mysqli_fetch_assoc($result);
        } else {
            echo "Doctor not found.";
            exit();
        }
    } elseif ($action === "update") {
        // Retrieve data from the form for updating
        $doctor_id = sanitizeInput($_POST["doctor_id"]);
        $new_first_name = sanitizeInput($_POST["first_name"]);
        $new_last_name = sanitizeInput($_POST["last_name"]);
        $new_username = sanitizeInput($_POST["username"]);
        $new_phone=sanitizeInput($_POST["phone_number"]);

        // Construct and execute the SQL query for updating doctor information
        $update_sql = "UPDATE doctors 
                       SET first_name = '$new_first_name',
                           last_name = '$new_last_name',
                           username = '$new_username',
                           phone_number='$new_phone'
                       WHERE doctor_id = $doctor_id";

        $update_result = mysqli_query($conn, $update_sql);

        // Redirect to the same page after successful update
        if ($update_result) {
            header("Location: {$_SERVER['PHP_SELF']}");
            exit();
        } else {
            echo "Error updating doctor: " . mysqli_error($conn);
        }
    } elseif ($action === "delete") {
        // Handle delete operation
        $doctor_id = sanitizeInput($_POST["doctor_id"]);
        $delete_sql = "DELETE FROM doctors WHERE doctor_id = $doctor_id";
        $delete_result = mysqli_query($conn, $delete_sql);

        // Redirect to the same page after successful delete
        if ($delete_result) {
            header("Location: {$_SERVER['PHP_SELF']}");
            exit();
        } else {
            echo "Error deleting doctor: " . mysqli_error($conn);
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

<!-- Navigation Section -->
<div class="app-container">
    <div class="sidebar">
        <ul class="sidebar-list">
            <!-- Navigation links for different pages -->
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
        <!-- Display logged-in user's username -->
        <div style=" margin-top: auto;">
            <p style="color: var(--app-content-main-color);">Welcome @<?php echo $username; ?></p>
        </div>
    </div>

    <!-- Body Section -->
    <?php
    // Display an error message if the query did not return any result.
    // Main content area displaying doctor information, search form, and buttons for adding doctors.
    if (!$result) {
        echo "NOT Found. Please enter the correct inputs.";
    } else {
    ?>
        <!-- Main content area -->
        <div class="app-content">
            <div class="app-content-header">
                <!-- Header and button to add a new doctor -->
                <h1 class="app-content-headerText">Doctors</h1>
                <a href="adddoctor.php"> <button class="app-content-headerButton">Add Doctor</button></a>
            </div>
            
            
            <!-- Search form for doctors -->
            <form class="search-form" method="post" action="">
                <label>Search by ID:</label>
                <input type="number" name="search_id">
                <label>Search by First Name:</label>
                <input type="text" name="search_first_name">
                <label>Search by Last Name:</label>
                <input type="text" name="search_last_name">
                <button type="submit">Search</button>
            </form>

            <!-- Display doctors in a table -->
            <div class="products-area-wrapper tableView">
                <div class="products-header">
                    <!-- Table header cells -->
                    <div class="product-cell ">Id</div>
                    <div class="product-cell ">FirstName</div>
                    <div class="product-cell ">LastName</div>
                    <div class="product-cell ">Phone number</div>
                    <div class="product-cell ">Username</div>
                    <div class="product-cell ">Password</div>
                    <div class="product-cell ">Edit</div>
                    <div class="product-cell ">Delete</div>
                </div>

                <?php
                // Loop through the query results and display each doctor's information
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<div class="products-row">';
                    echo '<div class="product-cell">' . $row["doctor_id"] . '</div>';
                    echo '<div class="product-cell">' . $row["first_name"] . '</div>';
                    echo '<div class="product-cell">' . $row["last_name"] . '</div>';
                    echo '<div class="product-cell">' . $row["phone_number"] . '</div>';
                    echo '<div class="product-cell">' . $row["username"] . '</div>';
                    echo '<div class="product-cell">' . $row["password"] . '</div>';
                    
                    // Edit button with a form for each doctor
                    echo '<div class="product-cell">' . "<form method='post'><input type='hidden' name='doctor_id' value='{$row["doctor_id"]}'>
              <input type='hidden' name='action' value='edit'>
              <button type='submit'>Edit</button></form>" . '</div>';
                    
                    // Delete button with a confirmation dialog
                    echo '<div class="product-cell">' . "<form method='post' id='deleteForm_{$row["doctor_id"]}' onSubmit='return false;'>
              <input type='hidden' name='doctor_id' value='{$row["doctor_id"]}'>
              <input type='hidden' name='action' value='delete'>
              <button type='button' onclick='confirmDelete({$row["doctor_id"]})'>Delete</button>
              </form>" . '</div>';
                    
                    echo '</div>';
                }
                ?>
            </div>
            
            <!-- Display an edit form for a selected doctor if editing is requested -->
            <?php if (isset($doctor)): ?>
                <a href="doctors.php">
                <img src="arrow.png" alt="Back to Previous Page" style="width: 50px; height: 50px; margin: 10px;">
                </a>
                <div class="edit-form">
                    <form method="post" action="">
                        <center>
                            <!-- Hidden fields for action and doctor_id -->
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="doctor_id" value="<?php echo  $doctor['doctor_id']; ?>">
                            
                            <!-- Form fields for updating doctor information -->
                            <label for="first_name">First Name:</label>
                            <input type="text" id="first_name" name="first_name"
                                   value="<?php echo $doctor['first_name']; ?>" required>

                            <label for="last_name">Last Name:</label>
                            <input type="text" id="last_name" name="last_name"
                                   value="<?php echo $doctor['last_name']; ?>" required>

                            <label for="phone_number">Phone Number:</label>
                            <input type="text" id="phone_number" name="phone_number"
                                   value="<?php echo $doctor['phone_number']; ?>" required>

                            <label for="username">Username:</label>
                            <input type="text" id="username" name="username"
                                   value="<?php echo $doctor['username']; ?>" required>
                        </center>

                        <!-- Submit button for updating doctor information -->
                        <center><button type="submit" class="edit-button">Save Changes</button></center>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    <?php
    }
    ?>
</body>
<script>
// Function to show confirmation dialog before deleting
function confirmDelete(doctorId) {
    var result = confirm("Are you sure you want to delete this doctor?");
    if (result) {
        // If the user clicks "OK," submit the form for deletion
        document.getElementById('deleteForm_' + doctorId).submit();
    }
}
</script>
</html>

                    