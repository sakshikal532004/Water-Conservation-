<?php
include("conn.php");
session_start();

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Debug information
    error_log("Login attempt - Email: " . $email);

    $query = "SELECT * FROM User WHERE email = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
        // Debug information
        error_log("User found in database - Username: " . $user['username']);
        error_log("Stored password hash: " . $user['password']);
        error_log("Attempting to verify password...");

        // Verify hashed password
        if (password_verify($password, $user['password'])) {
            error_log("Password verified successfully");
            $_SESSION['User_id'] = $user['username'];
            $_SESSION['id'] = $user['id'];
            $_SESSION['User'] = 'User';

            // Ensure session is written before redirect
            session_write_close();
            header('Location: user_dashboard.php');
            exit();
        } else {
            error_log("Password verification failed");
            error_log("Input password: " . $password);
            $msg = 'Invalid email or password.';
        }
    } else {
        error_log("No user found with email: " . $email);
        $msg = 'Invalid email or password.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
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

        .login-container {
            background-color: rgba(0, 0, 0, 0.5);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.8);
            width: 400px;
            text-align: center;
            position: relative;
        }

        .login-container::before {
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

        .login-container h2 {
            color: white;
            margin-bottom: 30px;
            text-transform: uppercase;
        }

        .login-container input[type="text"],
        .login-container input[type="password"] {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 20px;
            border: none;
            border-radius: 5px;
            background-color: rgba(255, 255, 255, 0.8);
        }

        .login-container button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .login-container button:hover {
            background-color: #0056b3;
        }

        .login-container p {
            color: white;
            margin-top: 20px;
        }

        .login-container a {
            color: #007bff;
            text-decoration: none;
        }

        .login-container a:hover {
            text-decoration: underline;
        }

        .error-msg {
            color: #ff4d4d;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>LOGIN</h2>
        <?php if ($msg): ?>
            <p class="error-msg"><?php echo $msg; ?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <input type="email" name="email" placeholder="Enter Email" required>
            <input type="password" name="password" placeholder="Enter Password" required>
            <button type="submit">LOGIN</button>
        </form>
        <p>Don't have an account? <a href="user_signup.php">Sign Up</a></p>
    </div>
</body>
</html>
