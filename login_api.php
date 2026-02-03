<?php
header("Content-Type: application/json");
include 'db_connect.php';

$response = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Post la username, password vangarom
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username != "" && $password != "") {
        $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
        $result = $conn->query($sql);

        if ($result && $result->num_rows == 1) {
            $row = $result->fetch_assoc();

            $response['status'] = "success";
            $response['message'] = "Login successful";
            $response['user'] = array(
                "username" => $row['username'],
                "role" => $row['role']
            );
        } else {
            $response['status'] = "error";
            $response['message'] = "Invalid Username or Password";
        }
    } else {
        $response['status'] = "error";
        $response['message'] = "Username and Password required";
    }
} else {
    $response['status'] = "error";
    $response['message'] = "Invalid Request Method";
}

echo json_encode($response);
?>
