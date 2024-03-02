<?php
/* Copyright (c) 2024 Mohammad J Hamdar */
/*  This PHP script calculates and updates doctors' productivity and gained productivity based on their attendance data,
 storing the results in a database. */
// Include the file containing the database connection
include('sqlconnection.php');

// SQL query to retrieve relevant data for productivity calculation
$sql = "SELECT d.doctor_id, d.first_name, d.last_name, m.month, m.year, m.valid_days, m.days_attended
        FROM doctors d
        JOIN monthly_attendance m ON d.doctor_id = m.doctor_id";

// Execute the SQL query
$result = mysqli_query($conn, $sql);

// Check if the query was successful
if ($result) {
    // Loop through each row in the result set
    while ($row = mysqli_fetch_array($result)) {
        // Extract data from the current row
        $id = $row['doctor_id'];
        $month = $row['month'];
        $year = $row['year'];
        $valid = $row['valid_days'];
        $days = $row['days_attended'];

        // Calculate productivity percentage
        $productivity = ($days * 100) / $valid;
        $gainedprod = 0;

        // Determine gained productivity based on predefined conditions
        if ($productivity == 100) {
            $gainedprod = 100;
        } elseif ($productivity < 100 && $productivity >= 80) {
            $gainedprod = 80;
        } elseif ($productivity < 80 && $productivity >= 60) {
            $gainedprod = (int)$productivity;
        } elseif ($productivity < 60) {
            $gainedprod = 0;
        }

        // Check if there is an existing productivity record for the doctor in the specified month and year
        $checkSql = "SELECT productivity FROM productivity WHERE doctor_id=$id AND month=$month AND year=$year";
        $checkResult = mysqli_query($conn, $checkSql);

        // Check if the check query was successful
        if ($checkResult) {
            // Check if there is an existing record
            if (mysqli_num_rows($checkResult) > 0) {
                // Fetch the existing productivity value
                $existingProductivity = mysqli_fetch_array($checkResult)[0];

                // Check if the calculated productivity is different from the existing record
                if ($productivity != $existingProductivity) {
                    // Update the existing productivity record
                    $updateSql = "UPDATE productivity SET productivity=$productivity,gained_productivity=$gainedprod WHERE doctor_id=$id AND month=$month AND year=$year";
                    $updateResult = mysqli_query($conn, $updateSql);

                    // Check if the update query was unsuccessful
                    if (!$updateResult) {
                        echo "Update error: " . mysqli_error($conn);
                    }
                }
            } else {
                // Insert a new productivity record if none exists
                $insertSql = "INSERT INTO productivity (doctor_id, month, year, productivity, gained_productivity) VALUES ($id, $month, $year, $productivity, $gainedprod)";
                $insertResult = mysqli_query($conn, $insertSql);

                // Check if the insert query was unsuccessful
                if (!$insertResult) {
                    echo "Insert error: " . mysqli_error($conn);
                }
            }
        } else {
            // Display an error message if the check query fails
            echo "Check error: " . mysqli_error($conn);
        }
    }
} else {
    // Display an error message if the main query fails
    echo "Select error: " . mysqli_error($conn);
}

// Close the database connection
mysqli_close($conn);
?>
