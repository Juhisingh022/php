<?php

header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Database connection det ails
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cityguide";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve JSON data from the request body
$data = json_decode(file_get_contents('php://input'), true);

// Check if data is received
if ($data) {
    // Retrieve form data
    $username = $data['username'];
    $contactNumber = $data['contactNumber'];
    $email = $data['email'];
    $password =$data['password'];

    // Validate input
    if (empty($username) || empty($contactNumber) || empty($email) || empty($password)) {
        echo "Please fill in all fields";
        return;
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format";
        return;
    }

    // Validate password format
    if (!preg_match('/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
        echo "Password must contain at least 8 characters, 1 number, 1 symbol, and 1 letter";
        return;
    }

    // Check if contact number already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE contact_number = ?");
    $stmt->bind_param("s", $contactNumber);
    $stmt->execute();
    $result = $stmt->get_result();

    // Hash the password before storing it in the database for security
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert user data into the database
    $sql = "INSERT INTO users (username, contact_number, email, password) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $username, $contactNumber, $email, $hashedPassword);

    if ($stmt->execute()) {
        echo "Signup successful";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Invalid request method";
}

$conn->close();
?>
