<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type");

include 'db.php';

$data = json_decode(file_get_contents("php://input"));

if (isset($data->contact_number) && isset($data->password)) {
    $contact_number = $data->contact_number;
    $password = $data->password;

    $stmt = $conn->prepare("SELECT * FROM users WHERE contact_number = ?");
    $stmt->bind_param("s", $contact_number);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        echo json_encode(["message" => "Login successful", "user" => $user]);
    } else {
        echo json_encode(["message" => "Invalid contact number or password"]);
    }

    $stmt->close();
} else {
    echo json_encode(["message" => "Contact number and password required"]);
}

$conn->close();
?>
