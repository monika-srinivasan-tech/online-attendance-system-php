<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
include 'db_connect.php';

// Fetch classes from DB
$class_sql = "SELECT * FROM classes";
$class_result = $conn->query($class_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard | Attendance System</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <!-- Common CSS -->
<link rel="stylesheet" href="assets/css/common.css">
   
</head>
<body>

<!-- HEADER -->
<div class="header">
    <h1>Class Attendance Dashboard</h1>
    <button class="logout-btn" onclick="location.href='logout.php'">Logout</button>
</div>

<!-- MAIN CONTENT -->
<div class="container">

    <div class="welcome">
        <h2>Welcome, <?php echo $_SESSION['username']; ?> 👋</h2>
        <p>Select a class to mark attendance or view summary</p>
    </div>

    <table>
        <tr>
            <th>Class Name</th>
            <th>Mark Attendance</th>
            <th>View Summary</th>
        </tr>

        <?php
        if ($class_result->num_rows > 0) {
            while ($row = $class_result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['class_name']}</td>";
                echo "<td><a class='btn' href='attendance.php?class_id={$row['id']}'>Mark</a></td>";
                echo "<td><a class='btn' href='summary.php?class_id={$row['id']}'>Summary</a></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='3' class='empty'>No classes found</td></tr>";
        }
        ?>
    </table>

</div>

</body>
</html>
