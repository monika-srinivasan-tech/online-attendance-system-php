<?php
session_start();
include 'db_connect.php';

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Secure query (basic)
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $_SESSION['username'] = $row['username'];
        $_SESSION['role'] = $row['role'];
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid username or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | Online Attendance</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

   

<!-- Common CSS -->
<link rel="stylesheet" href="assets/css/common.css">

  

</head>

<body>

<div class="login-wrapper">

    <!-- LEFT -->
    <div class="left-panel">
        <h1>Welcome Back 👋</h1>
        <p>
            Login to manage and track attendance easily.
            Access records securely anytime, anywhere with a
            clean and simple interface.
        </p>
    </div>

    <!-- RIGHT -->
    <div class="right-panel">
        <h2>Login</h2>
        <p>Enter your account details</p>

        <?php if (isset($error)) { echo "<div class='error'>$error</div>"; } ?>

        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">Login</button>
        </form>
    </div>

</div>

</body>
</html>
