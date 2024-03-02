<?php 
/* Copyright (c) 2024 Mohammad J Hamdar */
/*Add valid days to attend in a ceratin month*/
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
  

form {
    width: 300px;
    margin: 20px auto; /* Center the form horizontally with some top margin */
    padding: 20px;
    border-radius: 4px;
    box-shadow: rgba(0, 0, 0, 0.1) 0px 4px 12px;
    background-color: var(--app-bg);
    border: 1px solid transparent;
}

label {
    display: block;
    color: var(--sidebar-link);
    margin-bottom: 8px;
}

input {
    width: 100%;
    padding: 8px;
    margin-bottom: 16px;
    background-color: lightgray;
    border: 1px solid #ccc;
    border-radius: 4px;
    color: black;
}

button {
    margin: 1%;
    background-color: #2869ff;
    color: #fff;
    border: none;
    border-radius: 4px;
    padding: 10px 16px;
    cursor: pointer;
    width: 100%;
}

button:hover {
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
                    <a href="Home.php">
                        <span>Home</span>
                    </a>
                </li>
                <li class="sidebar-list-item ">
                    <a href="doctors.php">
                        <span>Doctors</span>
                    </a>
                </li>
                <li class="sidebar-list-item">
                    <a href="Statistics.php">
                        <span>Statistics</span>
                    </a>
                </li>
                <li class="sidebar-list-item">
                    <a href="Notification.php">
                        <span>Notifications</span>
                    </a>
                </li>
                <li class="sidebar-list-item active">
                    <a href="addattendancedate.php">
                        <span>Add Attendance Date</span>
                    </a>
                </li>
                <li class="sidebar-list-item">
                    <a href="Logout.php">
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
            <div style=" margin-top: auto;">
                <p style="color: var(--app-content-main-color);">Welcome @<?php echo $username; ?></p>
        </div>
        </div>
        <!-- end  nav     -->
        <?php include('productivity.php') ;   ?>
        <a href="addattendancedate.php">
        <img src="arrow.png" alt="Back to Previous Page" style="width: 50px; height: 50px; margin: 10px;">
    </a>
        <form method="post" action="Daysofattendance.php">
        <label for="month">Month:</label>
        <input type="number" name="month" id="month" placeholder="Enter Month" required>

        <label for="year">Year:</label>
        <input type="number" name="year" id="year" placeholder="Enter Year" required>

        <label for="validdays">Valid Days:</label>
        <input type="number" name="validdays" id="validdays" placeholder="Enter Valid Days" required>

        <button type="submit">Submit</button>
    </form>
    <?php
    function isValidMonth($value) {
        return filter_var($value, FILTER_VALIDATE_INT, array("options" => array("min_range" => 1, "max_range" => 12))) !== false;
    }
    
    function isValidYear($value) {
        return filter_var($value, FILTER_VALIDATE_INT) !== false && ($value >= 1000 && $value <= 9999);
    }

    if(isset($_POST["validdays"]) && isset($_POST["month"]) && isset($_POST["year"])){
        $year = $_POST["year"];
        $month = $_POST["month"];
        $valid = $_POST["validdays"];

        // Validate the year and month
        if (!isValidYear($year) || !isValidMonth($month) || ($valid < 0 || $valid>30) ) {
            echo '<script>alert("Invalid input.")</script>';
        } else {
            // Include your database connection file
            include('sqlconnection.php');
            $sql="SELECT * FROM Days_of_attendance_in_a_month WHERE month=$month and year=$year";
            $result=mysqli_query($conn,$sql);
            if(mysqli_num_rows($result)>0){
            // Prepare the SQL statement with placeholders
            $sql = "UPDATE Days_of_attendance_in_a_month SET valid_days=$valid WHERE month=$month and year=$year";
            $result = mysqli_query($conn, $sql);
            if(!$result) {
                echo '<script>alert("'.mysqli_error($conn).'")</script>';
            }
            else{
                echo '<script>alert("done")</script>';mysqli_close($conn);
            }
            }
            else{
                $sql = "INSERT INTO Days_of_attendance_in_a_month (month,year,valid_days) VALUES($month,$year,$valid) ";
                $result = mysqli_query($conn, $sql);
                if(!$result) {
                    echo '<script>alert("'.mysqli_error($conn).'")</script>';
                }
                else{
                    echo '<script>alert("done")</script>';mysqli_close($conn);
                }
            }
        }
    }
    ?>
</body>

</html>
