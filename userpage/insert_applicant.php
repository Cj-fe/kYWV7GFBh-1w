<?php
require_once '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uniqueId = $_POST['unique_id'];
    $idNumber = $_POST['id_number'];
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $password = $_POST['password']; // Ensure this is hashed
    $admission = $_POST['admission'];
    $isVerified = $_POST['is_verified'];
    $alumniId = $_POST['alumni_id'];
    $contact = $_POST['contact'];
    $dob = $_POST['dob'];
    $yearGraduated = $_POST['year_graduated'];
    $programGraduated = $_POST['program_graduated'];
    $address = $_POST['address'];
    $sex = $_POST['sex'];

    $conn = getMySQLConnection();
    if ($conn) {
        $stmt = $conn->prepare("INSERT INTO applicant (unique_id, id_number, fullname, email, password, admission, is_verified, alumni_id, contact, dob, year_graduated, program_graduated, address, sex) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssisssssss", $uniqueId, $idNumber, $fullname, $email, $password, $admission, $isVerified, $alumniId, $contact, $dob, $yearGraduated, $programGraduated, $address, $sex);
        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Data inserted successfully"]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to insert data"]);
        }
        $stmt->close();
        $conn->close();
    } else {
        echo json_encode(["success" => false, "message" => "Database connection failed"]);
    }
}
?>