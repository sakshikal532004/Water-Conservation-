<?php
session_start();
include("conn.php");

if (!isset($_SESSION['User_id'])) {
    header('Location: user_login.php');
    exit();
}

$username = $_SESSION['User_id'];
$user_id = $_SESSION['id'];

// Debug information
error_log("User ID: " . $user_id);

// Fetch latest water usage
$latest_query = "SELECT * FROM WaterUsage WHERE user_id = ? ORDER BY usage_date DESC LIMIT 1";
$stmt = mysqli_prepare($conn, $latest_query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$latest_result = mysqli_stmt_get_result($stmt);
$latest_usage = mysqli_fetch_assoc($latest_result);

// Debug information
error_log("Latest Usage: " . print_r($latest_usage, true));

// Fetch last 3 days usage history
$history_query = "SELECT * FROM WaterUsage WHERE user_id = ? ORDER BY usage_date DESC LIMIT 3";
$stmt = mysqli_prepare($conn, $history_query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$history_result = mysqli_stmt_get_result($stmt);

// Debug information
error_log("History Result: " . print_r($history_result, true));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Water Usage Dashboard</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: Arial, sans-serif; background: #eaf4f4; }
    .header { background: #006d77; color: white; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; }
    .sidebar { width: 200px; background: #83c5be; color: white; height: 100vh; position: fixed; padding-top: 2rem; }
    .sidebar ul { list-style: none; }
    .sidebar ul li { padding: 1rem; cursor: pointer; }
    .sidebar ul li:hover { background: #5eaaa8; }
    .main { margin-left: 200px; padding: 2rem; }
    .card { background: white; padding: 1.5rem; margin-bottom: 1rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .card h2 { margin-bottom: 1rem; }
    .stats { display: flex; gap: 1rem; flex-wrap: wrap; }
    .stat { flex: 1; min-width: 200px; background: #ffffff; padding: 1rem; border-radius: 8px; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
    .water-bar { height: 20px; background: #caf0f8; border-radius: 10px; overflow: hidden; margin-top: 10px; }
    .water-bar-fill { height: 100%; background: #0077b6; }
    ul.usage-breakdown { margin-top: 1rem; padding-left: 1.5rem; }
    ul.usage-breakdown li { margin-bottom: 0.5rem; }
    .message { font-weight: bold; font-size: 16px; margin-top: 1rem; }
    .conserve { color: green; }
    .waste { color: red; }
    a.logout { color: white; text-decoration: underline; }
    .calculate-btn {
      background: #006d77;
      color: white;
      padding: 1rem;
      margin: 1rem;
      border-radius: 8px;
      text-align: center;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }
    .calculate-btn:hover {
      background: #004d52;
    }
    .calculate-btn a {
      color: white;
      text-decoration: none;
      display: block;
    }
  </style>
</head>
<body>
  <div class="header">
    <div><strong>Water Tracker Dashboard</strong></div>
    <div>Welcome, <strong><?php echo htmlspecialchars($username); ?></strong> | <a href="logout.php" class="logout">Logout</a></div>
  </div>

  <div class="sidebar">
    <ul>
      <li>Dashboard</li>
      <li>History</li>
      <div class="calculate-btn">
        <a href="Calculator.html">Calculate Usage</a>
      </div>
    </ul>
  </div>

  <div class="main">
    <?php if ($latest_usage): ?>
    <div class="card">
      <h2>Today's Water Usage</h2>
      <p>Goal: <?php echo $latest_usage['goal']; ?> Liters</p>
      <p>Used: <?php echo $latest_usage['total_usage']; ?> Liters</p>
      <div class="water-bar">
        <div class="water-bar-fill" style="width: <?php echo min(($latest_usage['total_usage'] / $latest_usage['goal']) * 100, 100); ?>%;"></div>
      </div>
      <ul class="usage-breakdown">
        <li><strong>Bathing:</strong> <?php echo $latest_usage['bathing']; ?> Liters</li>
        <li><strong>Washing Clothes:</strong> <?php echo $latest_usage['washing_clothes']; ?> Liters</li>
        <li><strong>Washing Utensils:</strong> <?php echo $latest_usage['washing_utensils']; ?> Liters</li>
        <li><strong>Cooking:</strong> <?php echo $latest_usage['cooking']; ?> Liters</li>
        <li><strong>Drinking:</strong> <?php echo $latest_usage['drinking']; ?> Liters</li>
        <li><strong>Cleaning:</strong> <?php echo $latest_usage['cleaning']; ?> Liters</li>
      </ul>
      <p class="message <?php echo $latest_usage['total_usage'] <= $latest_usage['goal'] ? 'conserve' : 'waste'; ?>">
        <strong><?php echo $latest_usage['total_usage'] <= $latest_usage['goal'] ? 'You\'re conserving water!' : 'You\'re wasting water!'; ?></strong>
        <?php echo $latest_usage['total_usage'] <= $latest_usage['goal'] ? 'Great job!' : 'Try to reduce your usage.'; ?>
      </p>
    </div>

    <div class="stats">
      <div class="stat">
        <h3>Morning</h3>
        <p><?php echo $latest_usage['morning_usage']; ?> Liters</p>
      </div>
      <div class="stat">
        <h3>Afternoon</h3>
        <p><?php echo $latest_usage['afternoon_usage']; ?> Liters</p>
      </div>
      <div class="stat">
        <h3>Evening</h3>
        <p><?php echo $latest_usage['evening_usage']; ?> Liters</p>
      </div>
    </div>

    <div class="card">
      <h2>Usage History (Last 3 Days)</h2>
      <ul>
        <?php while ($history = mysqli_fetch_assoc($history_result)): ?>
          <li><?php echo date('F j', strtotime($history['usage_date'])); ?>: <?php echo $history['total_usage']; ?> Liters</li>
          <br>
        <?php endwhile; ?>
      </ul>
    </div>
    <?php else: ?>
    <div class="card">
      <h2>No Usage Data Available</h2>
      <p>Start tracking your water usage by clicking the "Calculate Usage" button in the sidebar.</p>
    </div>
    <?php endif; ?>
  </div>
</body>
</html>
