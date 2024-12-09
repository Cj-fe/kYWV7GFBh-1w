<?php
error_reporting(0); // Suppress errors for testing

// Log the received upload type
file_put_contents('upload_log.txt', "Upload type: " . $_POST['upload_type'] . "\n", FILE_APPEND);

// Determine the target directory based on the upload type
$uploadType = isset($_POST['upload_type']) ? $_POST['upload_type'] : 'profile';
$target_dir = $uploadType === 'cover' ? "uploads/cover_photos/" : "uploads/";

$file_extension = strtolower(pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION));
$new_filename = uniqid() . '.' . $file_extension;
$target_file = $target_dir . $new_filename;

// Check if image file is a actual image or fake image
$check = getimagesize($_FILES["file"]["tmp_name"]);
if($check === false) {
    echo json_encode(["success" => false, "message" => "File is not an image."]);
    exit;
}

// Check file size (5MB limit)
if ($_FILES["file"]["size"] > 5000000) {
    echo json_encode(["success" => false, "message" => "Sorry, your file is too large. Maximum file size is 5MB."]);
    exit;
}

// Allow certain file formats
$allowed_extensions = ["jpg", "jpeg", "png", "gif"];
if (!in_array($file_extension, $allowed_extensions)) {
    echo json_encode(["success" => false, "message" => "Sorry, only JPG, JPEG, PNG & GIF files are allowed."]);
    exit;
}

// Try to upload file
if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
    echo json_encode(["success" => true, "message" => "The file has been uploaded.", "filename" => $new_filename]);
} else {
    echo json_encode(["success" => false, "message" => "Sorry, there was an error uploading your file."]);
}
?>