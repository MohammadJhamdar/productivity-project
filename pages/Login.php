<?php
/* Copyright (c) 2024 Mohammad J Hamdar */
/* Login page that authenticate and authorize */
session_start(); 
include("sqlconnection.php");
if(isset($_POST["username"])&& isset($_POST["password"])){
    $username=$_POST["username"];
    $password=$_POST["password"];
    $sql="SELECT * FROM users where username='$username'";
    $result=mysqli_query($conn,$sql);
    if(mysqli_num_rows($result)> 0){
    $sql2="SELECT * FROM users where username='$username' and password='$password'";
    $res=mysqli_query($conn,$sql2);
    
        if(mysqli_num_rows($res)> 0){
            
            $row=mysqli_fetch_array($res);
            $userRole=$row["2"];
            $_SESSION["username"] = $username;
            $_SESSION['user_role'] = $userRole;
            if($userRole==1)
            {
                header("Location: Home.php");
            }
            else{
                header("Location: doctorhome.php");
            }
            
        }
        else{
            echo '<script>alert("Invalid password.");</script>';
        }
    }
    else{
        echo '<script>alert("Invalid username.");</script>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
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

        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            font-family: "Poppins", sans-serif;
            background-color: var(--app-bg);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        form {
            form {
            width: 300px;
            padding: 20px;
            border-radius: 4px;
            box-shadow: rgba(0, 0, 0, 0.1) 0px 4px 12px;
            background-color: var(--app-bg);
        }
        }

        h2 {
            color: var(--sidebar-link);
            text-align: center;
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
<?php include('productivity.php') ;   ?>
    <form id="loginForm" action="Login.php" method="post">
        <h2>Login</h2>
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Login</button>
    </form>
</body>

</html>
