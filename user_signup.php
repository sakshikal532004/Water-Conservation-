<?php
include("conn.php");
session_start();

$msg = '';

if (isset($_POST['submit'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $address  = mysqli_real_escape_string($conn, $_POST['address']);
    $password_raw = $_POST['password'];
    
    // Debug information
    error_log("Registration attempt - Email: " . $email);
    error_log("Raw password length: " . strlen($password_raw));
    
    $password = password_hash($password_raw, PASSWORD_DEFAULT);
    error_log("Hashed password: " . $password);

    // Check if email already exists
    $check = "SELECT * FROM `User` WHERE email = ?";
    $stmt = mysqli_prepare($conn, $check);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        error_log("Registration failed - Email already exists: " . $email);
        $msg = "User already exists!";
    } else {
        $insert = "INSERT INTO `User` (`username`, `email`, `address`, `password`) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $insert);
        mysqli_stmt_bind_param($stmt, "ssss", $username, $email, $address, $password);

        if (mysqli_stmt_execute($stmt)) {
            error_log("Registration successful for user: " . $username);
            $_SESSION['User'] = $username;
            header('Location: user_login.php');
            exit();
        } else {
            error_log("Registration failed - Database error: " . mysqli_error($conn));
            $msg = "Error registering user: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
  <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-image: url('img/login.jpg');
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
        }

        .register-container {
            background-color: rgba(0, 0, 0, 0.5);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.8);
            width: 400px;
            text-align: center;
            position: relative;
        }

        .register-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: inherit;
            filter: blur(20px);
            z-index: -1;
            margin: -20px;
        }

       .register-container h2 {
            color: white;
            margin-bottom: 30px;
            text-transform: uppercase;
        }

        
        .register-container input[type="username"],
        .register-container input[type="email"],
        .register-container input[type="address"],
        .register-container input[type="password"] {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 20px;
            border: none;
            border-radius: 5px;
            background-color: rgba(255, 255, 255, 0.8);
        }

        .register-container{
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .register-container button:hover {
            background-color: #0056b3;
        }

        .register-container p {
            color: white;
            margin-top: 20px;
        }

        .register-container a {
            color: #007bff;
            text-decoration: none;
        }

        .register-container a:hover {
            text-decoration: underline;
        }

        .error-msg {
            color: #ff4d4d;
            margin-bottom: 15px;
        }
    </style>

</head>
<body>
    <div class="form">
    <form action="" method="post">
        <h2>Registration</h2>
        <p class="msg"><?= $msg ?></p>

        <div class="form-group">
            <input type="text" name="username" placeholder="Username" required>
        </div>
        <div class="form-group">
            <input type="email" name="email" placeholder="Email" required>
        </div>
        <div class="form-group">
            <textarea name="address" placeholder="Address" rows="4" required></textarea>
        </div>
        <div class="form-group">
            <input type="password" name="password" placeholder="Password" required>
        </div>
        <!-- 🔥 The important fix is here -->
        <div class="form-group">
            <button type="submit" name="submit">Register</button>
        </div>

        <p>Already have an account? <a href="user_login.php">Login</a></p>
    </form>
</div>

</body>
</html>