<?php
include("conn.php");

// Create WaterUsage table
$sql = "CREATE TABLE IF NOT EXISTS WaterUsage (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    usage_date DATE NOT NULL,
    total_usage INT NOT NULL,
    morning_usage INT NOT NULL,
    afternoon_usage INT NOT NULL,
    evening_usage INT NOT NULL,
    bathing INT NOT NULL,
    washing_clothes INT NOT NULL,
    washing_utensils INT NOT NULL,
    cooking INT NOT NULL,
    drinking INT NOT NULL,
    cleaning INT NOT NULL,
    goal INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
)";

if (mysqli_query($conn, $sql)) {
    echo "WaterUsage table created successfully";
} else {
    echo "Error creating table: " . mysqli_error($conn);
}
?> 