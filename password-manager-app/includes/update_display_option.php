<?php
require_once 'conn.php'; // Include the database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newOption = isset($_POST['option']) ? (int)$_POST['option'] : null;

    if ($newOption !== null && ($newOption === 0 || $newOption === 1)) {
        // Update the display option in the database
        $sql = "UPDATE `display_option` SET `option` = :option WHERE `id` = 'd451fd0d04c62ea543de70d48fc436c83e998995c6d85fb2ccedc6b8a0febce4/27160c038fd64bcb2e4533f224b55c3933ceb2d9d244f8536585355e993d66b1'";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':option', $newOption, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Display option updated successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update display option.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid option value.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>