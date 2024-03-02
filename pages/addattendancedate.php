<?php 
/* Copyright (c) 2024 Mohammad J Hamdar */
/*add days the the doctor attendat in a certain month and year may be more than one date*/
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

        form {
    width: 300px;
    margin: auto; /* Add this line to center the form */
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
    <script>
        function addDateInput() {
            var container = document.getElementById("dateContainer");
            var newInput = document.createElement("div");
            newInput.innerHTML = '<label for="date">Date:</label>' +
                '<input type="date" name="dates[]" required>' +
                '<button type="button" onclick="removeDateInput(this)">Remove</button>';
            container.appendChild(newInput);
        }

        function removeDateInput(button) {
            var container = document.getElementById("dateContainer");
            container.removeChild(button.parentNode);
        }
    </script>

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
        
        <div class="app-content">
            <div class="app-content-header">
                <h1 class="app-content-headerText">Attendance</h1>
                <a href="Daysofattendance.php"> <button class="app-content-headerButton">Days Of Attendance </button></a>
                
            </div>
            <div class="app-content-header" style="justify-content: flex-end; display: flex;">
    <a href="delete.php"><button class="app-content-headerButton">Delete Attendance Day</button></a>
        </div>
    


            <form action="Addattendancedate.php" method="post">
                <div id="dateContainer">
                    <label for="doctorId">Doctor id:</label>
                    <input type="number" name="id" required><br>
                    <label for="date">Date:</label>
                    <input type="date" name="dates[]" required>
                    <button type="button" onclick="removeDateInput(this)">Remove</button>
                </div>

                <button type="button" onclick="addDateInput()">Add Another Date</button>

                <input type="submit" value="Submit">
            </form>
        </div>
    </div>
    <?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the 'dates' array is set in the POST data
    if (isset($_POST['dates']) && isset($_POST['id'])) {
        $dates = $_POST['dates'];
        $id = $_POST['id'];

        // Include your database connection file
        include('sqlconnection.php');
        $sql = "SELECT * FROM doctors WHERE doctor_id=$id";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            foreach ($dates as $index => $date) {
                // Sanitize input (consider using prepared statements for better security)
                $id = mysqli_real_escape_string($conn, $id);
                $date = mysqli_real_escape_string($conn, $date);

                // Check if the date already exists
                $checkSql = "SELECT * FROM attendance WHERE doctor_id=$id AND attendance_date='$date'";
                $checkResult = mysqli_query($conn, $checkSql);

                $month = date('m', strtotime($date));
                $year = date('Y', strtotime($date));

                $sql = "SELECT valid_days, days_attended FROM monthly_attendance WHERE month=$month and year=$year and doctor_id=$id";
                $result = mysqli_query($conn, $sql);
                $row = mysqli_fetch_array($result);

                if (mysqli_num_rows($checkResult) > 0) {
                    echo '<script>alert("This date: ' . $date . ' is already inserted")</script>';
                } elseif ($row && $row[0] < $row[1] + 1) {
                    echo '<script>alert("Exceeded valid days for the date: ' . $date . '")</script>';
                } else {
                    // Insert data into the database
                    $insertSql = "INSERT INTO attendance (doctor_id, attendance_date) VALUES ('$id', '$date')";
                    $insertResult = mysqli_query($conn, $insertSql);

                    if (!$insertResult) {
                        die("Error: " . mysqli_error($conn));
                    }
                    echo '<script>alert("Date added: ' . $date . '")</script>';
                }
            }

            // Include productivity.php outside the loop if needed only once
            include('productivity.php');

            // Close the database connection
            mysqli_close($conn);
        } else {
            echo '<script>alert("Can\'t find doctor id.")</script>';
        }
    } else {
        echo '<script>alert("No dates submitted.")</script>';
    }
}
?>

</body>

</html>
