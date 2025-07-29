<?php
session_start();
include("conn.php");

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    error_log("User not logged in. Session data: " . print_r($_SESSION, true));
    die(json_encode(['success' => false, 'message' => 'Please login first']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['id'];
    
    // Debug POST data
    error_log("POST data received: " . print_r($_POST, true));
    
    // Get time-based usage
    $morning_usage = intval($_POST['morning']);
    $afternoon_usage = intval($_POST['afternoon']);
    $evening_usage = intval($_POST['evening']);
    
    // Get activity-based usage
    $bathing = intval($_POST['bathing']);
    $clothes = intval($_POST['clothes']);
    $utensils = intval($_POST['utensils']);
    $cooking = intval($_POST['cooking']);
    $drinking = intval($_POST['drinking']);
    $cleaning = intval($_POST['cleaning']);
    
    // Calculate total usage
    $total_usage = $bathing + $clothes + $utensils + $cooking + $drinking + $cleaning;
    
    $usage_date = date('Y-m-d');
    $goal = 150; // Default goal value

    // Debug information
    error_log("Saving usage data for user: " . $user_id);
    error_log("Total usage: " . $total_usage);
    error_log("Usage date: " . $usage_date);

    // Check if table exists
    $table_check = mysqli_query($conn, "SHOW TABLES LIKE 'WaterUsage'");
    if (mysqli_num_rows($table_check) == 0) {
        error_log("WaterUsage table does not exist");
        die(json_encode(['success' => false, 'message' => 'Database table not found. Please contact administrator.']));
    }

    // Prepare the SQL statement
    $query = "INSERT INTO WaterUsage (
        user_id, 
        usage_date, 
        total_usage,
        morning_usage,
        afternoon_usage,
        evening_usage,
        bathing,
        washing_clothes,
        washing_utensils,
        cooking,
        drinking,
        cleaning,
        goal
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        error_log("Prepare failed: " . mysqli_error($conn));
        die(json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($conn)]));
    }

    mysqli_stmt_bind_param($stmt, "isiiiiiiiiiii", 
        $user_id, 
        $usage_date, 
        $total_usage,
        $morning_usage,
        $afternoon_usage,
        $evening_usage,
        $bathing,
        $clothes,
        $utensils,
        $cooking,
        $drinking,
        $cleaning,
        $goal
    );

    if (mysqli_stmt_execute($stmt)) {
        error_log("Usage data saved successfully");
        echo json_encode(['success' => true, 'message' => 'Water usage data saved successfully']);
    } else {
        error_log("Error saving usage data: " . mysqli_error($conn));
        echo json_encode(['success' => false, 'message' => 'Error saving water usage data: ' . mysqli_error($conn)]);
    }

    mysqli_stmt_close($stmt);
} else {
    error_log("Invalid request method: " . $_SERVER['REQUEST_METHOD']);
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?> 