<?php

try {
    // Prepare and execute the query to fetch folder IDs and names
    $stmt = $conn->prepare("SELECT folder_id, folder_name FROM tbl_folder");
    $stmt->execute();

    // Fetch all results
    $folders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Generate HTML options
    foreach ($folders as $folder) {
        echo '<option value="' . htmlspecialchars($folder['folder_id']) . '">' . htmlspecialchars($folder['folder_name']) . '</option>';
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>