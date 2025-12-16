<?php
header('Content-Type: application/json');

// Database connection
$host = "localhost";
$username = "root";
$password = ""; // default XAMPP password is empty
$dbname = "user_auth";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]);
    exit();
}

// Registration
if (isset($_POST['fname'], $_POST['lname'], $_POST['email'], $_POST['password'])) {
    $fname = $conn->real_escape_string($_POST['fname']);
    $lname = $conn->real_escape_string($_POST['lname']);
    $email = $conn->real_escape_string($_POST['email']);
    $pass = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Check if email exists
    $check = $conn->query("SELECT * FROM users WHERE email = '$email'");
    if ($check->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "Email already registered."]);
    } else {
        $sql = "INSERT INTO users (first_name, last_name, email, password)
                VALUES ('$fname', '$lname', '$email', '$pass')";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(["success" => true, "message" => "Registration successful!"]);
        } else {
            echo json_encode(["success" => false, "message" => "Error: " . $conn->error]);
        }
    }
    exit();
}

// Login
if (isset($_POST['login_email'], $_POST['login_password'])) {
    $email = $conn->real_escape_string($_POST['login_email']);
    $password = $_POST['login_password'];

    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            echo json_encode(["success" => true, "message" => "Login successful."]);
        } else {
            echo json_encode(["success" => false, "message" => "Incorrect password."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "User not found."]);
    }
    exit();
}

echo json_encode(["success" => false, "message" => "Invalid request."]);
?>
